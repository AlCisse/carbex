<?php

namespace App\Notifications;

use App\Models\BankConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BankSyncFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected BankConnection $connection,
        protected string $errorMessage,
        protected bool $requiresReauth = false
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
        $message = (new MailMessage)
            ->subject("Bank sync failed: {$this->connection->bank_name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("We encountered an issue while syncing your bank connection.")
            ->line("**Bank:** {$this->connection->bank_name}")
            ->line("**Error:** {$this->errorMessage}");

        if ($this->requiresReauth) {
            $message->line('**Action Required:** Your bank connection needs to be re-authenticated.')
                ->action('Reconnect Bank', url('/settings/banking'))
                ->line('Please reconnect your bank account to resume automatic transaction syncing.');
        } else {
            $message->action('Check Connection', url('/settings/banking'))
                ->line("We'll automatically retry the sync. If the issue persists, please check your connection settings.");
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'bank_sync_failed',
            'connection_id' => $this->connection->id,
            'bank_name' => $this->connection->bank_name,
            'error_message' => $this->errorMessage,
            'requires_reauth' => $this->requiresReauth,
            'action_url' => '/settings/banking',
            'action_text' => $this->requiresReauth ? 'Reconnect Bank' : 'Check Connection',
        ];
    }
}
