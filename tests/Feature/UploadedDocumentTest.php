<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Organization;
use App\Models\UploadedDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for UploadedDocument model - T137-T143
 */
class UploadedDocumentTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_can_create_uploaded_document(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create([
                'original_filename' => 'facture-edf.pdf',
                'document_type' => UploadedDocument::TYPE_ENERGY_BILL,
            ]);

        $this->assertDatabaseHas('uploaded_documents', [
            'id' => $document->id,
            'organization_id' => $this->organization->id,
            'original_filename' => 'facture-edf.pdf',
            'document_type' => 'energy_bill',
        ]);
    }

    public function test_document_starts_as_pending(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create();

        $this->assertEquals(UploadedDocument::STATUS_PENDING, $document->processing_status);
        $this->assertTrue($document->isPending());
    }

    public function test_can_mark_as_processing(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->pending()
            ->create();

        $document->markAsProcessing();

        $this->assertEquals(UploadedDocument::STATUS_PROCESSING, $document->processing_status);
        $this->assertTrue($document->isProcessing());
        $this->assertNull($document->error_message);
    }

    public function test_can_mark_as_completed_with_high_confidence(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->processing()
            ->create();

        $extractedData = [
            'supplier_name' => 'EDF',
            'total_amount' => 150.00,
            'date' => '2024-01-15',
        ];

        $document->markAsCompleted($extractedData, 0.85, ['model' => 'claude']);

        $this->assertEquals(UploadedDocument::STATUS_COMPLETED, $document->processing_status);
        $this->assertTrue($document->isCompleted());
        $this->assertEquals(0.85, $document->ai_confidence);
        $this->assertEquals('EDF', $document->getExtractedSupplier());
    }

    public function test_low_confidence_sets_needs_review(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->processing()
            ->create();

        $document->markAsCompleted(['data' => 'test'], 0.5);

        $this->assertEquals(UploadedDocument::STATUS_NEEDS_REVIEW, $document->processing_status);
        $this->assertTrue($document->needsReview());
    }

    public function test_can_mark_as_failed(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->processing()
            ->create(['retry_count' => 0]);

        $document->markAsFailed('API error: timeout');

        $this->assertEquals(UploadedDocument::STATUS_FAILED, $document->processing_status);
        $this->assertTrue($document->isFailed());
        $this->assertEquals('API error: timeout', $document->error_message);
        $this->assertEquals(1, $document->retry_count);
    }

    public function test_can_be_reprocessed_when_failed(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->failed()
            ->create(['retry_count' => 2]);

        $this->assertTrue($document->canBeReprocessed());
    }

    public function test_cannot_be_reprocessed_after_max_retries(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->failed()
            ->create(['retry_count' => 3]);

        $this->assertFalse($document->canBeReprocessed());
    }

    public function test_can_validate_document(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create();

        $corrections = ['supplier_name' => ['original' => 'EF', 'corrected' => 'EDF']];
        $document->validate($this->user->id, $corrections);

        $this->assertTrue($document->is_validated);
        $this->assertEquals($this->user->id, $document->validated_by);
        $this->assertNotNull($document->validated_at);
        $this->assertEquals($corrections, $document->validation_corrections);
    }

    public function test_can_link_emission_record(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->validated()
            ->create();

        // Test the method logic without actually saving (to avoid FK constraint)
        // The linkEmissionRecord method sets these attributes
        $fakeEmissionId = \Illuminate\Support\Str::uuid()->toString();

        // Temporarily disable FK checks for this test
        \Illuminate\Support\Facades\DB::statement('SET session_replication_role = replica;');

        try {
            $document->linkEmissionRecord($fakeEmissionId);

            $this->assertEquals($fakeEmissionId, $document->emission_record_id);
            $this->assertTrue($document->emission_created);
        } finally {
            \Illuminate\Support\Facades\DB::statement('SET session_replication_role = DEFAULT;');
        }
    }

    public function test_extracted_value_helpers(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create([
                'extracted_data' => [
                    'supplier_name' => 'TotalEnergies',
                    'date' => '2024-03-15',
                    'total_amount' => 245.50,
                    'suggested_category' => '1.2',
                    'emissions' => [
                        ['type' => 'diesel', 'quantity' => 50],
                    ],
                ],
            ]);

        $this->assertEquals('TotalEnergies', $document->getExtractedSupplier());
        $this->assertEquals('2024-03-15', $document->getExtractedDate());
        $this->assertEquals(245.50, $document->getExtractedTotal());
        $this->assertEquals('1.2', $document->getSuggestedCategory());
        $this->assertIsArray($document->getExtractedEmissions());
    }

    public function test_file_type_detection(): void
    {
        $pdfDocument = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create(['mime_type' => 'application/pdf']);

        $imageDocument = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->image()
            ->create();

        $excelDocument = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->excel()
            ->create();

        $this->assertTrue($pdfDocument->isPdf());
        $this->assertFalse($pdfDocument->isImage());

        $this->assertTrue($imageDocument->isImage());
        $this->assertFalse($imageDocument->isPdf());

        $this->assertTrue($excelDocument->isExcel());
        $this->assertFalse($excelDocument->isPdf());
    }

    public function test_file_size_formatted(): void
    {
        $smallDocument = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create(['file_size' => 500]);

        $mediumDocument = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create(['file_size' => 2048]);

        $largeDocument = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create(['file_size' => 5242880]);

        $this->assertEquals('500 bytes', $smallDocument->getFileSizeFormatted());
        $this->assertEquals('2.00 KB', $mediumDocument->getFileSizeFormatted());
        $this->assertEquals('5.00 MB', $largeDocument->getFileSizeFormatted());
    }

    public function test_status_labels(): void
    {
        $pending = UploadedDocument::factory()->pending()->create();
        $processing = UploadedDocument::factory()->processing()->create();
        $completed = UploadedDocument::factory()->completed()->create();
        $failed = UploadedDocument::factory()->failed()->create();

        $this->assertEquals('En attente', $pending->status_label);
        $this->assertEquals('En cours', $processing->status_label);
        $this->assertEquals('Terminé', $completed->status_label);
        $this->assertEquals('Échec', $failed->status_label);
    }

    public function test_status_colors(): void
    {
        $pending = UploadedDocument::factory()->pending()->create();
        $completed = UploadedDocument::factory()->completed()->create();
        $failed = UploadedDocument::factory()->failed()->create();

        $this->assertEquals('gray', $pending->status_color);
        $this->assertEquals('green', $completed->status_color);
        $this->assertEquals('red', $failed->status_color);
    }

    public function test_type_labels(): void
    {
        $invoice = UploadedDocument::factory()->invoice()->create();
        $energyBill = UploadedDocument::factory()->energyBill()->create();
        $fuelReceipt = UploadedDocument::factory()->fuelReceipt()->create();

        $this->assertEquals('Facture', $invoice->type_label);
        $this->assertEquals('Facture énergie', $energyBill->type_label);
        $this->assertEquals('Ticket carburant', $fuelReceipt->type_label);
    }

    public function test_confidence_percent(): void
    {
        $highConfidence = UploadedDocument::factory()
            ->completed()
            ->create(['ai_confidence' => 0.95]);

        $lowConfidence = UploadedDocument::factory()
            ->needsReview()
            ->create(['ai_confidence' => 0.45]);

        $noConfidence = UploadedDocument::factory()
            ->pending()
            ->create(['ai_confidence' => null]);

        $this->assertEquals(95, $highConfidence->confidence_percent);
        $this->assertEquals(45, $lowConfidence->confidence_percent);
        $this->assertNull($noConfidence->confidence_percent);
    }

    public function test_scope_for_organization(): void
    {
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        UploadedDocument::factory()->forOrganization($org1)->count(3)->create();
        UploadedDocument::factory()->forOrganization($org2)->count(2)->create();

        $this->assertCount(3, UploadedDocument::forOrganization($org1->id)->get());
        $this->assertCount(2, UploadedDocument::forOrganization($org2->id)->get());
    }

    public function test_scope_by_status(): void
    {
        UploadedDocument::factory()->pending()->count(2)->create();
        UploadedDocument::factory()->processing()->count(1)->create();
        UploadedDocument::factory()->completed()->count(3)->create();
        UploadedDocument::factory()->failed()->count(1)->create();

        $this->assertCount(2, UploadedDocument::pending()->get());
        $this->assertCount(1, UploadedDocument::processing()->get());
        $this->assertCount(3, UploadedDocument::completed()->get());
        $this->assertCount(1, UploadedDocument::failed()->get());
    }

    public function test_scope_validated_unvalidated(): void
    {
        UploadedDocument::factory()->validated()->count(2)->create();
        UploadedDocument::factory()->completed()->count(3)->create();

        $this->assertCount(2, UploadedDocument::validated()->get());
        $this->assertCount(3, UploadedDocument::unvalidated()->get());
    }

    public function test_scope_with_emission(): void
    {
        UploadedDocument::factory()->withEmission()->count(2)->create();
        UploadedDocument::factory()->validated()->count(3)->create();

        $this->assertCount(2, UploadedDocument::withEmission()->get());
        $this->assertCount(3, UploadedDocument::withoutEmission()->get());
    }

    public function test_scope_of_type(): void
    {
        UploadedDocument::factory()->invoice()->count(2)->create();
        UploadedDocument::factory()->energyBill()->count(3)->create();
        UploadedDocument::factory()->fuelReceipt()->count(1)->create();

        $this->assertCount(2, UploadedDocument::ofType(UploadedDocument::TYPE_INVOICE)->get());
        $this->assertCount(3, UploadedDocument::ofType(UploadedDocument::TYPE_ENERGY_BILL)->get());
        $this->assertCount(1, UploadedDocument::ofType(UploadedDocument::TYPE_FUEL_RECEIPT)->get());
    }

    public function test_belongs_to_organization(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create();

        $this->assertNotNull($document->organization);
        $this->assertEquals($this->organization->id, $document->organization->id);
    }

    public function test_belongs_to_uploader(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create(['uploaded_by' => $this->user->id]);

        $this->assertNotNull($document->uploader);
        $this->assertEquals($this->user->id, $document->uploader->id);
    }

    public function test_belongs_to_assessment(): void
    {
        $assessment = Assessment::factory()
            ->forOrganization($this->organization)
            ->create();

        $document = UploadedDocument::factory()
            ->forAssessment($assessment)
            ->create();

        $this->assertNotNull($document->assessment);
        $this->assertEquals($assessment->id, $document->assessment->id);
    }

    public function test_static_helpers(): void
    {
        $documentTypes = UploadedDocument::getDocumentTypes();
        $processingStatuses = UploadedDocument::getProcessingStatuses();

        $this->assertIsArray($documentTypes);
        $this->assertArrayHasKey('invoice', $documentTypes);
        $this->assertArrayHasKey('energy_bill', $documentTypes);

        $this->assertIsArray($processingStatuses);
        $this->assertArrayHasKey('pending', $processingStatuses);
        $this->assertArrayHasKey('completed', $processingStatuses);
    }

    public function test_soft_delete(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create();

        $documentId = $document->id;
        $document->delete();

        $this->assertSoftDeleted('uploaded_documents', ['id' => $documentId]);
        $this->assertNull(UploadedDocument::find($documentId));
        $this->assertNotNull(UploadedDocument::withTrashed()->find($documentId));
    }
}
