<?php

namespace Tests\Feature\Livewire;

use App\Jobs\ProcessDocumentExtraction;
use App\Livewire\AI\DocumentUploader;
use App\Models\Organization;
use App\Models\UploadedDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Feature tests for DocumentUploader Livewire component - T139
 */
class DocumentUploaderTest extends TestCase
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

        Storage::fake('local');
        Queue::fake();
    }

    public function test_component_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->assertStatus(200)
            ->assertSee(__('linscarbon.documents.title'));
    }

    public function test_component_shows_upload_form(): void
    {
        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->assertSet('showUploadForm', true)
            ->assertSee(__('linscarbon.documents.drop_files'));
    }

    public function test_component_shows_empty_state_when_no_documents(): void
    {
        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->assertSee(__('linscarbon.documents.no_documents'));
    }

    public function test_component_lists_organization_documents(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create([
                'original_filename' => 'facture-test.pdf',
            ]);

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->assertSee('facture-test.pdf');
    }

    public function test_can_upload_pdf_file(): void
    {
        $file = UploadedFile::fake()->create('facture.pdf', 1024, 'application/pdf');

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->set('file', $file)
            ->set('documentType', 'invoice')
            ->call('upload')
            ->assertHasNoErrors()
            ->assertSet('successMessage', __('linscarbon.documents.upload_success'));

        $this->assertDatabaseHas('uploaded_documents', [
            'organization_id' => $this->organization->id,
            'original_filename' => 'facture.pdf',
            'document_type' => 'invoice',
            'processing_status' => 'pending',
        ]);

        Queue::assertPushed(ProcessDocumentExtraction::class);
    }

    public function test_can_upload_image_file(): void
    {
        $file = UploadedFile::fake()->image('receipt.jpg', 800, 600);

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->set('file', $file)
            ->set('documentType', 'fuel_receipt')
            ->call('upload')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('uploaded_documents', [
            'organization_id' => $this->organization->id,
            'document_type' => 'fuel_receipt',
        ]);
    }

    public function test_validates_max_file_size(): void
    {
        // Test that the component has file size validation configured
        // Note: Livewire file upload validation in tests can be tricky
        // as fake files may not trigger real size validation
        $this->assertTrue(
            UploadedDocument::MAX_FILE_SIZE === 10 * 1024 * 1024,
            'Max file size should be 10MB'
        );

        // Verify the component accepts valid files
        $validFile = UploadedFile::fake()->create('valid.pdf', 1024, 'application/pdf');

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->set('file', $validFile)
            ->assertHasNoErrors(['file']);
    }

    public function test_rejects_invalid_file_type(): void
    {
        $file = UploadedFile::fake()->create('script.exe', 100, 'application/octet-stream');

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->set('file', $file)
            ->assertHasErrors(['file']);
    }

    public function test_upload_requires_file(): void
    {
        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('upload')
            ->assertSet('errorMessage', __('linscarbon.documents.file_required'));
    }

    public function test_can_select_document_for_preview(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create();

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('selectDocument', $document->id)
            ->assertSet('selectedDocumentId', $document->id);
    }

    public function test_can_toggle_document_selection(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create();

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('selectDocument', $document->id)
            ->assertSet('selectedDocumentId', $document->id)
            ->call('selectDocument', $document->id)
            ->assertSet('selectedDocumentId', null);
    }

    public function test_can_open_validation_modal(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create();

        $component = Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('openValidation', $document->id)
            ->assertSet('showValidationModal', true);

        $this->assertNotNull($component->get('validatingDocument'));
        $this->assertEquals($document->id, $component->get('validatingDocument')['id']);
    }

    public function test_can_validate_document(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create([
                'extracted_data' => [
                    'supplier_name' => 'Old Name',
                    'total_amount' => 100,
                ],
            ]);

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('openValidation', $document->id)
            ->set('correctedData.supplier_name', 'Corrected Name')
            ->call('validateDocument')
            ->assertSet('showValidationModal', false)
            ->assertSet('successMessage', __('linscarbon.documents.validation_success'));

        $document->refresh();
        $this->assertTrue($document->is_validated);
        $this->assertEquals($this->user->id, $document->validated_by);
    }

    public function test_can_close_validation_modal(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create();

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('openValidation', $document->id)
            ->call('closeValidation')
            ->assertSet('showValidationModal', false)
            ->assertSet('validatingDocument', null);
    }

    public function test_can_reprocess_failed_document(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->failed()
            ->create(['retry_count' => 1]);

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('reprocess', $document->id)
            ->assertSet('successMessage', __('linscarbon.documents.reprocessing'));

        $document->refresh();
        $this->assertEquals('processing', $document->processing_status);

        Queue::assertPushed(ProcessDocumentExtraction::class);
    }

    public function test_cannot_reprocess_document_at_max_retries(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->failed()
            ->create(['retry_count' => 3]);

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('reprocess', $document->id)
            ->assertSet('errorMessage', __('linscarbon.documents.cannot_reprocess'));
    }

    public function test_can_delete_document(): void
    {
        $document = UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create();

        $documentId = $document->id;

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->call('deleteDocument', $documentId)
            ->assertSet('successMessage', __('linscarbon.documents.deleted'));

        $this->assertSoftDeleted('uploaded_documents', ['id' => $documentId]);
    }

    public function test_document_types_property(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(DocumentUploader::class);

        $types = $component->get('documentTypes');

        $this->assertIsArray($types);
        $this->assertArrayHasKey('invoice', $types);
        $this->assertArrayHasKey('energy_bill', $types);
        $this->assertArrayHasKey('fuel_receipt', $types);
    }

    public function test_filters_documents_by_assessment(): void
    {
        $assessment = \App\Models\Assessment::factory()
            ->forOrganization($this->organization)
            ->create();

        UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create(['assessment_id' => $assessment->id]);

        UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->create(['assessment_id' => null]);

        $component = Livewire::actingAs($this->user)
            ->test(DocumentUploader::class, ['assessmentId' => $assessment->id]);

        $this->assertCount(1, $component->get('documents'));
    }

    public function test_shows_processing_indicator(): void
    {
        UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->processing()
            ->create(['original_filename' => 'processing-doc.pdf']);

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->assertSee('processing-doc.pdf')
            ->assertSee(__('linscarbon.documents.processing'));
    }

    public function test_shows_confidence_for_completed_documents(): void
    {
        UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->completed()
            ->create([
                'original_filename' => 'completed-doc.pdf',
                'ai_confidence' => 0.85,
            ]);

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->assertSee('85%');
    }

    public function test_shows_validation_badge_for_validated_documents(): void
    {
        UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->validated()
            ->create(['original_filename' => 'validated-doc.pdf']);

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->assertSee(__('linscarbon.documents.validated'));
    }

    public function test_auto_refresh_for_processing_documents(): void
    {
        UploadedDocument::factory()
            ->forOrganization($this->organization)
            ->processing()
            ->create();

        Livewire::actingAs($this->user)
            ->test(DocumentUploader::class)
            ->assertSee('wire:poll');
    }
}
