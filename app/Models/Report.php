<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'generated_by',
        'type',
        'name',
        'description',
        'period_start',
        'period_end',
        'year',
        'quarter',
        'month',
        'file_format',
        'status',
        'file_path',
        'file_size',
        'download_count',
        'last_downloaded_at',
        'parameters',
        'summary',
        'error_message',
        'started_at',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'parameters' => 'array',
        'summary' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Check if report is ready for download.
     */
    public function isReady(): bool
    {
        return $this->status === 'completed' && $this->file_path;
    }

    /**
     * Check if report generation is in progress.
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if report generation failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if report has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the download URL for the report.
     */
    public function getDownloadUrlAttribute(): ?string
    {
        if (! $this->isReady()) {
            return null;
        }

        // Use local storage for development, S3 for production
        if (config('filesystems.default') === 's3') {
            return Storage::disk('s3')->temporaryUrl(
                $this->file_path,
                now()->addHours(1)
            );
        }

        return route('reports.download', $this->id);
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (! $this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Get the report type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'beges' => 'BEGES',
            'ghg_inventory' => __('carbex.reports.ghg_inventory'),
            'carbon_footprint' => __('carbex.reports.carbon_footprint'),
            'scope_breakdown' => __('carbex.reports.scope_breakdown'),
            'category_analysis' => __('carbex.reports.category_analysis'),
            'period_comparison' => __('carbex.reports.period_comparison'),
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    /**
     * Increment download counter.
     */
    public function recordDownload(): void
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }

    /**
     * Delete the file from storage.
     */
    public function deleteFile(): bool
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }

        return false;
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'completed')
            ->whereNotNull('file_path');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }
}
