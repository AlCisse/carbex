<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Report $report,
        protected string $errorMessage
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
        return (new MailMessage)
            ->subject("Report generation failed: {$this->report->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Unfortunately, we encountered an issue while generating your report.")
            ->line("**Report:** {$this->report->name}")
            ->line("**Error:** {$this->errorMessage}")
            ->line("Please try generating the report again, or contact support if the issue persists.")
            ->action('Go to Reports', url('/reports'))
            ->line('Our team has been notified of this issue.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'report_failed',
            'report_id' => $this->report->id,
            'report_name' => $this->report->name,
            'error_message' => $this->errorMessage,
            'action_url' => '/reports',
            'action_text' => 'View Reports',
        ];
    }
}
