<?php

namespace App\Jobs;

use App\Models\UploadedDocument;
use App\Services\AI\DocumentExtractor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessDocumentExtraction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public UploadedDocument $document
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DocumentExtractor $extractor): void
    {
        Log::info('Starting document extraction', [
            'document_id' => $this->document->id,
            'filename' => $this->document->original_filename,
            'type' => $this->document->document_type,
        ]);

        // Mark as processing
        $this->document->markAsProcessing();

        try {
            // Extract data
            $result = $extractor->extract($this->document);

            if ($result['success']) {
                // Validate extraction
                $validationErrors = $extractor->validateExtraction($result['data']);

                if (!empty($validationErrors)) {
                    Log::warning('Extraction validation warnings', [
                        'document_id' => $this->document->id,
                        'errors' => $validationErrors,
                    ]);
                }

                // Mark as completed
                $this->document->markAsCompleted(
                    extractedData: $result['data'],
                    confidence: $result['confidence'],
                    metadata: [
                        'processing_time_ms' => $result['processing_time_ms'],
                        'validation_warnings' => $validationErrors,
                        'provider' => 'ai',
                    ]
                );

                // Update processing time
                $this->document->update([
                    'processing_time_ms' => $result['processing_time_ms'],
                    'ai_model_used' => config('ai.default_provider', 'anthropic'),
                ]);

                Log::info('Document extraction completed', [
                    'document_id' => $this->document->id,
                    'confidence' => $result['confidence'],
                    'processing_time_ms' => $result['processing_time_ms'],
                ]);

                // Broadcast event for Livewire refresh
                broadcast(new \App\Events\DocumentProcessed($this->document->id));

            } else {
                // Mark as failed
                $this->document->markAsFailed($result['error'] ?? 'Unknown error');

                Log::error('Document extraction failed', [
                    'document_id' => $this->document->id,
                    'error' => $result['error'],
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Document extraction exception', [
                'document_id' => $this->document->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->document->markAsFailed($e->getMessage());

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Document extraction job failed permanently', [
            'document_id' => $this->document->id,
            'error' => $exception->getMessage(),
        ]);

        $this->document->markAsFailed(
            'Échec permanent: ' . $exception->getMessage()
        );
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'document-extraction',
            'document:' . $this->document->id,
            'organization:' . $this->document->organization_id,
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function retryAfter(): int
    {
        // Exponential backoff: 60s, 120s, 240s
        return $this->backoff * pow(2, $this->attempts() - 1);
    }
}
