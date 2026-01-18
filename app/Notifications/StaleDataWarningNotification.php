<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class StaleDataWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Organization $organization,
        protected array $staleData
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysSinceSync = $this->staleData['days_since_sync'] ?? 0;
        $staleConnections = $this->staleData['stale_connections'] ?? [];

        $message = (new MailMessage)
            ->subject("Action Required: Your emission data hasn't been updated in {$daysSinceSync} days")
            ->greeting("Hello {$notifiable->name},")
            ->line("We noticed that your organization's emission data in LinsCarbon hasn't been updated recently.")
            ->line("**Last sync:** {$daysSinceSync} days ago");

        if (!empty($staleConnections)) {
            $message->line('**Affected bank connections:**');
            foreach ($staleConnections as $connection) {
                $message->line("- {$connection['bank_name']}: Last synced {$connection['days_ago']} days ago");
            }
        }

        if (!empty($this->staleData['pending_transactions'])) {
            $message->line("**{$this->staleData['pending_transactions']} transactions** are waiting to be categorized.");
        }

        $message->line('Keeping your data up-to-date ensures accurate carbon footprint tracking and reporting.')
            ->action('Update Your Data', url('/dashboard'))
            ->line('If you need assistance, please contact our support team.');

        if ($this->hasConnectionIssues()) {
            $message->line('---')
                ->line('**Note:** Some of your bank connections may need to be re-authenticated. Please check your connection status in Settings.');
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'stale_data_warning',
            'organization_id' => $this->organization->id,
            'organization_name' => $this->organization->name,
            'days_since_sync' => $this->staleData['days_since_sync'] ?? 0,
            'stale_connections_count' => count($this->staleData['stale_connections'] ?? []),
            'pending_transactions' => $this->staleData['pending_transactions'] ?? 0,
            'has_connection_issues' => $this->hasConnectionIssues(),
            'last_sync_at' => $this->staleData['last_sync_at'] ?? null,
            'severity' => $this->getSeverity(),
            'action_url' => '/dashboard',
            'action_text' => 'Update Your Data',
        ];
    }

    /**
     * Check if there are connection issues.
     */
    protected function hasConnectionIssues(): bool
    {
        $staleConnections = $this->staleData['stale_connections'] ?? [];

        foreach ($staleConnections as $connection) {
            if (($connection['status'] ?? '') === 'error') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get notification severity based on staleness.
     */
    protected function getSeverity(): string
    {
        $daysSinceSync = $this->staleData['days_since_sync'] ?? 0;

        if ($daysSinceSync >= 30) {
            return 'critical';
        }

        if ($daysSinceSync >= 14) {
            return 'high';
        }

        if ($daysSinceSync >= 7) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Determine which queues should be used for each notification channel.
     */
    public function viaQueues(): array
    {
        return [
            'mail' => 'notifications',
            'database' => 'default',
        ];
    }
}
