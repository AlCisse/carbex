<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class UploadedDocument extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'assessment_id',
        'uploaded_by',
        'original_filename',
        'storage_path',
        'mime_type',
        'file_size',
        'file_hash',
        'document_type',
        'processing_status',
        'extracted_data',
        'extraction_metadata',
        'ai_confidence',
        'ai_model_used',
        'processing_time_ms',
        'is_validated',
        'validated_by',
        'validated_at',
        'validation_corrections',
        'emission_record_id',
        'emission_created',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'extraction_metadata' => 'array',
        'validation_corrections' => 'array',
        'ai_confidence' => 'decimal:2',
        'is_validated' => 'boolean',
        'emission_created' => 'boolean',
        'validated_at' => 'datetime',
        'file_size' => 'integer',
        'processing_time_ms' => 'integer',
        'retry_count' => 'integer',
    ];

    /**
     * Processing status constants.
     */
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_NEEDS_REVIEW = 'needs_review';

    /**
     * Document type constants.
     */
    public const TYPE_INVOICE = 'invoice';

    public const TYPE_ENERGY_BILL = 'energy_bill';

    public const TYPE_FUEL_RECEIPT = 'fuel_receipt';

    public const TYPE_TRANSPORT_INVOICE = 'transport_invoice';

    public const TYPE_PURCHASE_ORDER = 'purchase_order';

    public const TYPE_BANK_STATEMENT = 'bank_statement';

    public const TYPE_EXPENSE_REPORT = 'expense_report';

    public const TYPE_OTHER = 'other';

    /**
     * Maximum file size in bytes (10MB).
     */
    public const MAX_FILE_SIZE = 10 * 1024 * 1024;

    /**
     * Allowed MIME types.
     */
    public const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/heic',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel
        'application/vnd.ms-excel',
        'text/csv',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function emissionRecord(): BelongsTo
    {
        return $this->belongsTo(EmissionRecord::class);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeForOrganization($query, string $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopePending($query)
    {
        return $query->where('processing_status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('processing_status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('processing_status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('processing_status', self::STATUS_FAILED);
    }

    public function scopeNeedsReview($query)
    {
        return $query->where('processing_status', self::STATUS_NEEDS_REVIEW);
    }

    public function scopeValidated($query)
    {
        return $query->where('is_validated', true);
    }

    public function scopeUnvalidated($query)
    {
        return $query->where('is_validated', false);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeWithEmission($query)
    {
        return $query->where('emission_created', true);
    }

    public function scopeWithoutEmission($query)
    {
        return $query->where('emission_created', false);
    }

    // =========================================================================
    // Status Methods
    // =========================================================================

    public function isPending(): bool
    {
        return $this->processing_status === self::STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->processing_status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->processing_status === self::STATUS_COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->processing_status === self::STATUS_FAILED;
    }

    public function needsReview(): bool
    {
        return $this->processing_status === self::STATUS_NEEDS_REVIEW;
    }

    public function canBeReprocessed(): bool
    {
        return in_array($this->processing_status, [
            self::STATUS_FAILED,
            self::STATUS_NEEDS_REVIEW,
        ]) && $this->retry_count < 3;
    }

    // =========================================================================
    // Status Transitions
    // =========================================================================

    public function markAsProcessing(): void
    {
        $this->update([
            'processing_status' => self::STATUS_PROCESSING,
            'error_message' => null,
        ]);
    }

    public function markAsCompleted(array $extractedData, float $confidence, array $metadata = []): void
    {
        $this->update([
            'processing_status' => $confidence >= 0.7
                ? self::STATUS_COMPLETED
                : self::STATUS_NEEDS_REVIEW,
            'extracted_data' => $extractedData,
            'extraction_metadata' => $metadata,
            'ai_confidence' => $confidence,
            'error_message' => null,
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'processing_status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    public function validate(int $userId, ?array $corrections = null): void
    {
        $this->update([
            'is_validated' => true,
            'validated_by' => $userId,
            'validated_at' => now(),
            'validation_corrections' => $corrections,
            'processing_status' => self::STATUS_COMPLETED,
        ]);
    }

    public function linkEmissionRecord(string $emissionRecordId): void
    {
        $this->update([
            'emission_record_id' => $emissionRecordId,
            'emission_created' => true,
        ]);
    }

    // =========================================================================
    // File Methods
    // =========================================================================

    public function getFileUrl(): ?string
    {
        if (!$this->storage_path) {
            return null;
        }

        return Storage::disk('local')->url($this->storage_path);
    }

    public function getFilePath(): ?string
    {
        if (!$this->storage_path) {
            return null;
        }

        return Storage::disk('local')->path($this->storage_path);
    }

    public function getFileContents(): ?string
    {
        if (!$this->storage_path || !Storage::disk('local')->exists($this->storage_path)) {
            return null;
        }

        return Storage::disk('local')->get($this->storage_path);
    }

    public function getFileBase64(): ?string
    {
        $contents = $this->getFileContents();

        if (!$contents) {
            return null;
        }

        return base64_encode($contents);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public function isExcel(): bool
    {
        return in_array($this->mime_type, [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
        ]);
    }

    public function isCsv(): bool
    {
        return $this->mime_type === 'text/csv';
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    // =========================================================================
    // Extraction Data Helpers
    // =========================================================================

    public function getExtractedValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->extracted_data, $key, $default);
    }

    public function getExtractedEmissions(): ?array
    {
        return $this->getExtractedValue('emissions');
    }

    public function getExtractedTotal(): ?float
    {
        return $this->getExtractedValue('total_amount');
    }

    public function getExtractedDate(): ?string
    {
        return $this->getExtractedValue('date');
    }

    public function getExtractedSupplier(): ?string
    {
        return $this->getExtractedValue('supplier_name');
    }

    public function getSuggestedCategory(): ?string
    {
        return $this->getExtractedValue('suggested_category');
    }

    public function getSuggestedFactor(): ?array
    {
        return $this->getExtractedValue('suggested_factor');
    }

    // =========================================================================
    // Attributes
    // =========================================================================

    public function getStatusLabelAttribute(): string
    {
        return match ($this->processing_status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_PROCESSING => 'En cours',
            self::STATUS_COMPLETED => 'Terminé',
            self::STATUS_FAILED => 'Échec',
            self::STATUS_NEEDS_REVIEW => 'À réviser',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->processing_status) {
            self::STATUS_PENDING => 'gray',
            self::STATUS_PROCESSING => 'blue',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_NEEDS_REVIEW => 'yellow',
            default => 'gray',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->document_type) {
            self::TYPE_INVOICE => 'Facture',
            self::TYPE_ENERGY_BILL => 'Facture énergie',
            self::TYPE_FUEL_RECEIPT => 'Ticket carburant',
            self::TYPE_TRANSPORT_INVOICE => 'Facture transport',
            self::TYPE_PURCHASE_ORDER => 'Bon de commande',
            self::TYPE_BANK_STATEMENT => 'Relevé bancaire',
            self::TYPE_EXPENSE_REPORT => 'Note de frais',
            self::TYPE_OTHER => 'Autre',
            default => 'Inconnu',
        };
    }

    public function getConfidencePercentAttribute(): ?int
    {
        if ($this->ai_confidence === null) {
            return null;
        }

        return (int) ($this->ai_confidence * 100);
    }

    // =========================================================================
    // Static Helpers
    // =========================================================================

    public static function getDocumentTypes(): array
    {
        return [
            self::TYPE_INVOICE => __('linscarbon.documents.types.invoice'),
            self::TYPE_ENERGY_BILL => __('linscarbon.documents.types.energy_bill'),
            self::TYPE_FUEL_RECEIPT => __('linscarbon.documents.types.fuel_receipt'),
            self::TYPE_TRANSPORT_INVOICE => __('linscarbon.documents.types.transport_invoice'),
            self::TYPE_PURCHASE_ORDER => __('linscarbon.documents.types.purchase_order'),
            self::TYPE_BANK_STATEMENT => __('linscarbon.documents.types.bank_statement'),
            self::TYPE_EXPENSE_REPORT => __('linscarbon.documents.types.expense_report'),
            self::TYPE_OTHER => __('linscarbon.documents.types.other'),
        ];
    }

    public static function getProcessingStatuses(): array
    {
        return [
            self::STATUS_PENDING => __('linscarbon.documents.statuses.pending'),
            self::STATUS_PROCESSING => __('linscarbon.documents.statuses.processing'),
            self::STATUS_COMPLETED => __('linscarbon.documents.statuses.completed'),
            self::STATUS_FAILED => __('linscarbon.documents.statuses.failed'),
            self::STATUS_NEEDS_REVIEW => __('linscarbon.documents.statuses.needs_review'),
        ];
    }
}
