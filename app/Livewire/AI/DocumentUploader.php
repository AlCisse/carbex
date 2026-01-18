<?php

namespace App\Livewire\AI;

use App\Jobs\ProcessDocumentExtraction;
use App\Models\UploadedDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentUploader extends Component
{
    use WithFileUploads;

    /**
     * The uploaded file.
     */
    public $file;

    /**
     * Upload progress percentage.
     */
    public int $progress = 0;

    /**
     * Selected document type.
     */
    public string $documentType = 'other';

    /**
     * Current assessment ID.
     */
    public ?string $assessmentId = null;

    /**
     * List of uploaded documents.
     */
    public array $documents = [];

    /**
     * Currently selected document for preview.
     */
    public ?string $selectedDocumentId = null;

    /**
     * Show validation modal.
     */
    public bool $showValidationModal = false;

    /**
     * Document being validated.
     */
    public ?array $validatingDocument = null;

    /**
     * Corrected data during validation.
     */
    public array $correctedData = [];

    /**
     * Error message.
     */
    public ?string $errorMessage = null;

    /**
     * Success message.
     */
    public ?string $successMessage = null;

    /**
     * Show upload form.
     */
    public bool $showUploadForm = true;

    public function mount(?string $assessmentId = null): void
    {
        $this->assessmentId = $assessmentId;
        $this->loadDocuments();
    }

    public function loadDocuments(): void
    {
        $user = Auth::user();

        if (!$user || !$user->organization_id) {
            $this->documents = [];
            return;
        }

        $organizationId = $user->organization_id;

        $query = UploadedDocument::forOrganization($organizationId)
            ->with(['uploader', 'validator'])
            ->latest();

        if ($this->assessmentId) {
            $query->where('assessment_id', $this->assessmentId);
        }

        $this->documents = $query->get()->map(function ($doc) {
            return [
                'id' => $doc->id,
                'filename' => $doc->original_filename,
                'type' => $doc->document_type,
                'type_label' => $doc->type_label,
                'status' => $doc->processing_status,
                'status_label' => $doc->status_label,
                'status_color' => $doc->status_color,
                'file_size' => $doc->getFileSizeFormatted(),
                'mime_type' => $doc->mime_type,
                'confidence' => $doc->confidence_percent,
                'is_validated' => $doc->is_validated,
                'emission_created' => $doc->emission_created,
                'extracted_data' => $doc->extracted_data,
                'created_at' => $doc->created_at->format('d/m/Y H:i'),
                'uploader' => $doc->uploader?->name,
                'can_reprocess' => $doc->canBeReprocessed(),
            ];
        })->toArray();
    }

    public function updatedFile(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        // Validate file
        $this->validate([
            'file' => [
                'required',
                'file',
                'max:' . (UploadedDocument::MAX_FILE_SIZE / 1024), // KB
                'mimes:pdf,jpeg,jpg,png,webp,heic,xlsx,xls,csv',
            ],
        ], [
            'file.required' => __('linscarbon.documents.file_required'),
            'file.max' => __('linscarbon.documents.file_too_large'),
            'file.mimes' => __('linscarbon.documents.invalid_type'),
        ]);
    }

    public function upload(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        if (!$this->file) {
            $this->errorMessage = __('linscarbon.documents.file_required');
            return;
        }

        try {
            $user = Auth::user();
            $organizationId = $user->organization_id;

            // Generate storage path
            $filename = $this->file->getClientOriginalName();
            $extension = $this->file->getClientOriginalExtension();
            $storagePath = "documents/{$organizationId}/" . now()->format('Y/m') . '/' . uniqid() . '.' . $extension;

            // Store file
            Storage::disk('local')->put($storagePath, $this->file->get());

            // Create document record
            $document = UploadedDocument::create([
                'organization_id' => $organizationId,
                'assessment_id' => $this->assessmentId,
                'uploaded_by' => $user->id,
                'original_filename' => $filename,
                'storage_path' => $storagePath,
                'mime_type' => $this->file->getMimeType(),
                'file_size' => $this->file->getSize(),
                'file_hash' => hash_file('sha256', $this->file->getRealPath()),
                'document_type' => $this->documentType,
                'processing_status' => UploadedDocument::STATUS_PENDING,
            ]);

            // Dispatch extraction job
            ProcessDocumentExtraction::dispatch($document);

            // Reset form
            $this->reset(['file', 'documentType', 'progress']);
            $this->documentType = 'other';

            // Reload documents
            $this->loadDocuments();

            $this->successMessage = __('linscarbon.documents.upload_success');

        } catch (\Exception $e) {
            $this->errorMessage = __('linscarbon.documents.upload_error') . ': ' . $e->getMessage();
        }
    }

    public function selectDocument(string $documentId): void
    {
        $this->selectedDocumentId = $documentId === $this->selectedDocumentId ? null : $documentId;
    }

    public function openValidation(string $documentId): void
    {
        $document = UploadedDocument::find($documentId);

        if (!$document) {
            return;
        }

        $this->validatingDocument = [
            'id' => $document->id,
            'filename' => $document->original_filename,
            'extracted_data' => $document->extracted_data ?? [],
            'confidence' => $document->confidence_percent,
        ];

        $this->correctedData = $document->extracted_data ?? [];
        $this->showValidationModal = true;
    }

    public function validateDocument(): void
    {
        if (!$this->validatingDocument) {
            return;
        }

        $document = UploadedDocument::find($this->validatingDocument['id']);

        if (!$document) {
            return;
        }

        // Calculate corrections
        $corrections = [];
        $originalData = $document->extracted_data ?? [];

        foreach ($this->correctedData as $key => $value) {
            if (($originalData[$key] ?? null) !== $value) {
                $corrections[$key] = [
                    'original' => $originalData[$key] ?? null,
                    'corrected' => $value,
                ];
            }
        }

        // Update document with corrections
        $document->validate(Auth::id(), $corrections ?: null);

        // Update extracted data if corrected
        if (!empty($corrections)) {
            $document->update([
                'extracted_data' => $this->correctedData,
            ]);
        }

        $this->closeValidation();
        $this->loadDocuments();

        $this->successMessage = __('linscarbon.documents.validation_success');
    }

    public function closeValidation(): void
    {
        $this->showValidationModal = false;
        $this->validatingDocument = null;
        $this->correctedData = [];
    }

    public function reprocess(string $documentId): void
    {
        $document = UploadedDocument::find($documentId);

        if (!$document || !$document->canBeReprocessed()) {
            $this->errorMessage = __('linscarbon.documents.cannot_reprocess');
            return;
        }

        $document->markAsProcessing();
        ProcessDocumentExtraction::dispatch($document);

        $this->loadDocuments();
        $this->successMessage = __('linscarbon.documents.reprocessing');
    }

    public function createEmission(string $documentId): void
    {
        $document = UploadedDocument::find($documentId);

        if (!$document || !$document->isCompleted() || $document->emission_created) {
            return;
        }

        // Emit event for emission creation
        $this->dispatch('create-emission-from-document', documentId: $documentId);
    }

    public function deleteDocument(string $documentId): void
    {
        $document = UploadedDocument::find($documentId);

        if (!$document) {
            return;
        }

        // Delete file from storage
        if ($document->storage_path) {
            Storage::disk('local')->delete($document->storage_path);
        }

        // Soft delete document
        $document->delete();

        $this->loadDocuments();
        $this->successMessage = __('linscarbon.documents.deleted');
    }

    #[On('document-processed')]
    public function onDocumentProcessed(string $documentId): void
    {
        $this->loadDocuments();
    }

    public function getDocumentTypesProperty(): array
    {
        return UploadedDocument::getDocumentTypes();
    }

    public function getSelectedDocumentProperty(): ?array
    {
        if (!$this->selectedDocumentId) {
            return null;
        }

        return collect($this->documents)->firstWhere('id', $this->selectedDocumentId);
    }

    public function render()
    {
        return view('livewire.ai.document-uploader');
    }
}
