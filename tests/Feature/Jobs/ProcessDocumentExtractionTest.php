<?php

namespace Tests\Feature\Jobs;

use App\Events\DocumentProcessed;
use App\Jobs\ProcessDocumentExtraction;
use App\Models\Organization;
use App\Models\UploadedDocument;
use App\Services\AI\DocumentExtractor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

/**
 * Feature tests for ProcessDocumentExtraction job - T142
 */
class ProcessDocumentExtractionTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();

        Event::fake([DocumentProcessed::class]);
        Log::spy();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_job_can_be_dispatched(): void
    {
        Queue::fake();

        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create();

        ProcessDocumentExtraction::dispatch($document);

        Queue::assertPushed(ProcessDocumentExtraction::class, function ($job) use ($document) {
            return $job->document->id === $document->id;
        });
    }

    public function test_job_processes_document_successfully(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create();

        $extractor = Mockery::mock(DocumentExtractor::class);
        $extractor->shouldReceive('extract')
            ->once()
            ->with(Mockery::type(UploadedDocument::class))
            ->andReturn([
                'success' => true,
                'data' => [
                    'document_type' => 'invoice',
                    'supplier_name' => 'Test Company',
                    'date' => '2024-01-15',
                    'total_amount' => 500.00,
                ],
                'confidence' => 0.85,
                'processing_time_ms' => 1500,
                'error' => null,
            ]);

        $extractor->shouldReceive('validateExtraction')
            ->once()
            ->andReturn([]);

        $this->app->instance(DocumentExtractor::class, $extractor);

        $job = new ProcessDocumentExtraction($document);
        $job->handle($extractor);

        $document->refresh();

        $this->assertEquals(UploadedDocument::STATUS_COMPLETED, $document->processing_status);
        $this->assertEquals(0.85, $document->ai_confidence);
        $this->assertEquals('Test Company', $document->getExtractedSupplier());
        $this->assertEquals(1500, $document->processing_time_ms);

        Event::assertDispatched(DocumentProcessed::class);
    }

    public function test_job_marks_document_as_processing_first(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create();

        $extractor = Mockery::mock(DocumentExtractor::class);
        $extractor->shouldReceive('extract')
            ->once()
            ->andReturnUsing(function () use ($document) {
                // Verify document is marked as processing at this point
                $document->refresh();
                $this->assertEquals(UploadedDocument::STATUS_PROCESSING, $document->processing_status);

                return [
                    'success' => true,
                    'data' => ['document_type' => 'invoice'],
                    'confidence' => 0.9,
                    'processing_time_ms' => 1000,
                    'error' => null,
                ];
            });

        $extractor->shouldReceive('validateExtraction')
            ->andReturn([]);

        $job = new ProcessDocumentExtraction($document);
        $job->handle($extractor);
    }

    public function test_job_handles_extraction_failure(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create(['retry_count' => 0]);

        $extractor = Mockery::mock(DocumentExtractor::class);
        $extractor->shouldReceive('extract')
            ->once()
            ->andReturn([
                'success' => false,
                'data' => null,
                'confidence' => 0,
                'processing_time_ms' => 500,
                'error' => 'API unavailable',
            ]);

        $job = new ProcessDocumentExtraction($document);
        $job->handle($extractor);

        $document->refresh();

        $this->assertEquals(UploadedDocument::STATUS_FAILED, $document->processing_status);
        $this->assertEquals('API unavailable', $document->error_message);
        $this->assertEquals(1, $document->retry_count);

        Event::assertNotDispatched(DocumentProcessed::class);
    }

    public function test_job_handles_exception(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create(['retry_count' => 0]);

        $extractor = Mockery::mock(DocumentExtractor::class);
        $extractor->shouldReceive('extract')
            ->once()
            ->andThrow(new \Exception('Network error'));

        $job = new ProcessDocumentExtraction($document);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network error');

        try {
            $job->handle($extractor);
        } finally {
            $document->refresh();
            $this->assertEquals(UploadedDocument::STATUS_FAILED, $document->processing_status);
            $this->assertStringContainsString('Network error', $document->error_message);
        }
    }

    public function test_job_logs_validation_warnings(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create();

        $extractor = Mockery::mock(DocumentExtractor::class);
        $extractor->shouldReceive('extract')
            ->once()
            ->andReturn([
                'success' => true,
                'data' => [
                    'document_type' => 'invoice',
                    'date' => '2024-01-15',
                ],
                'confidence' => 0.75,
                'processing_time_ms' => 1200,
                'error' => null,
            ]);

        $extractor->shouldReceive('validateExtraction')
            ->once()
            ->andReturn(['Montant total non trouvé ou invalide']);

        $job = new ProcessDocumentExtraction($document);
        $job->handle($extractor);

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Extraction validation warnings'
                    && in_array('Montant total non trouvé ou invalide', $context['errors']);
            });
    }

    public function test_job_sets_low_confidence_as_needs_review(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create();

        $extractor = Mockery::mock(DocumentExtractor::class);
        $extractor->shouldReceive('extract')
            ->once()
            ->andReturn([
                'success' => true,
                'data' => ['document_type' => 'other'],
                'confidence' => 0.45,
                'processing_time_ms' => 800,
                'error' => null,
            ]);

        $extractor->shouldReceive('validateExtraction')
            ->once()
            ->andReturn([]);

        $job = new ProcessDocumentExtraction($document);
        $job->handle($extractor);

        $document->refresh();

        $this->assertEquals(UploadedDocument::STATUS_NEEDS_REVIEW, $document->processing_status);
        $this->assertTrue($document->needsReview());
    }

    public function test_failed_method_marks_document_as_failed(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->processing()
            ->create(['retry_count' => 2]);

        $job = new ProcessDocumentExtraction($document);
        $job->failed(new \Exception('Final failure'));

        $document->refresh();

        $this->assertEquals(UploadedDocument::STATUS_FAILED, $document->processing_status);
        $this->assertStringContainsString('Échec permanent', $document->error_message);
    }

    public function test_job_has_correct_tags(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create();

        $job = new ProcessDocumentExtraction($document);
        $tags = $job->tags();

        $this->assertContains('document-extraction', $tags);
        $this->assertContains('document:' . $document->id, $tags);
        $this->assertContains('organization:' . $this->organization->id, $tags);
    }

    public function test_job_has_correct_configuration(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create();

        $job = new ProcessDocumentExtraction($document);

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(60, $job->backoff);
        $this->assertEquals(2, $job->maxExceptions);
        $this->assertEquals(120, $job->timeout);
    }

    public function test_job_stores_extraction_metadata(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create();

        $extractor = Mockery::mock(DocumentExtractor::class);
        $extractor->shouldReceive('extract')
            ->once()
            ->andReturn([
                'success' => true,
                'data' => [
                    'document_type' => 'energy_bill',
                    'supplier_name' => 'EDF',
                    'date' => '2024-03-15',
                    'total_amount' => 150.00,
                ],
                'confidence' => 0.92,
                'processing_time_ms' => 2000,
                'error' => null,
            ]);

        $extractor->shouldReceive('validateExtraction')
            ->once()
            ->andReturn([]);

        $job = new ProcessDocumentExtraction($document);
        $job->handle($extractor);

        $document->refresh();

        $this->assertNotNull($document->extraction_metadata);
        $this->assertEquals(2000, $document->processing_time_ms);
        $this->assertNotNull($document->ai_model_used);
    }

    public function test_job_broadcasts_event_on_success(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create();

        $extractor = Mockery::mock(DocumentExtractor::class);
        $extractor->shouldReceive('extract')
            ->once()
            ->andReturn([
                'success' => true,
                'data' => ['document_type' => 'invoice'],
                'confidence' => 0.88,
                'processing_time_ms' => 1000,
                'error' => null,
            ]);

        $extractor->shouldReceive('validateExtraction')
            ->andReturn([]);

        $job = new ProcessDocumentExtraction($document);
        $job->handle($extractor);

        Event::assertDispatched(DocumentProcessed::class, function ($event) use ($document) {
            return $event->documentId === $document->id;
        });
    }
}
