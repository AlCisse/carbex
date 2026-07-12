<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected Report $report
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
            ->subject("Your report \"{$this->report->name}\" is ready")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your carbon footprint report has been generated successfully.")
            ->line("**Report:** {$this->report->name}")
            ->line("**Type:** " . ucfirst(str_replace('_', ' ', $this->report->type)))
            ->line("**Period:** {$this->report->period_start?->format('M d, Y')} - {$this->report->period_end?->format('M d, Y')}")
            ->action('Download Report', url("/reports/{$this->report->id}/download"))
            ->line('You can also view this report in your dashboard.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'report_ready',
            'report_id' => $this->report->id,
            'report_name' => $this->report->name,
            'report_type' => $this->report->type,
            'action_url' => "/reports/{$this->report->id}",
            'action_text' => 'View Report',
        ];
    }
}
