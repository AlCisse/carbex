<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Import Complete Notification
 *
 * Notifies user when data import is complete:
 * - Import statistics
 * - Success/failure status
 * - Link to view imported data
 */
class ImportComplete extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $stats,
        public string $importType,
        public bool $failed = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeLabel = match ($this->importType) {
            'transactions' => __('Bank Transactions'),
            'activities' => __('Activities'),
            'fec' => __('FEC Accounting Data'),
            default => __('Data'),
        };

        if ($this->failed) {
            return (new MailMessage)
                ->subject(__('Import Failed: :type', ['type' => $typeLabel]))
                ->error()
                ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
                ->line(__('Your :type import has failed.', ['type' => $typeLabel]))
                ->line(__('Error: :message', ['message' => $this->stats['error_message'] ?? __('Unknown error')]))
                ->line(__('Please check your file format and try again.'))
                ->action(__('Try Again'), url('/transactions/import'))
                ->salutation(__('Best regards,') . "\n" . config('app.name'));
        }

        $message = (new MailMessage)
            ->subject(__('Import Complete: :type', ['type' => $typeLabel]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Your :type import has been completed.', ['type' => $typeLabel]));

        // Add statistics
        $message->line('**' . __('Import Summary') . ':**')
            ->line(__('- Total rows processed: :count', ['count' => $this->stats['total'] ?? 0]))
            ->line(__('- Successfully imported: :count', ['count' => $this->stats['imported'] ?? 0]))
            ->line(__('- Skipped: :count', ['count' => $this->stats['skipped'] ?? 0]));

        if (($this->stats['errors'] ?? 0) > 0) {
            $message->line(__('- Errors: :count', ['count' => $this->stats['errors']]));
        }

        if (($this->stats['emissions_calculated'] ?? 0) > 0) {
            $message->line(__('- Emissions calculated: :count', ['count' => $this->stats['emissions_calculated']]));
        }

        $actionUrl = $this->importType === 'activities'
            ? url('/emissions/activities')
            : url('/transactions');

        return $message
            ->action(__('View Imported Data'), $actionUrl)
            ->salutation(__('Best regards,') . "\n" . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'import_complete',
            'import_type' => $this->importType,
            'failed' => $this->failed,
            'stats' => $this->stats,
            'title' => $this->failed
                ? __('Import Failed')
                : __('Import Complete'),
            'message' => $this->failed
                ? __('Your data import has failed.')
                : __(':count records imported successfully.', [
                    'count' => $this->stats['imported'] ?? 0,
                ]),
        ];
    }
}
