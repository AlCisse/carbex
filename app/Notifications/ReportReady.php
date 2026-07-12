<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Report Ready Notification
 *
 * Notifies user when report generation is complete:
 * - Email with download link
 * - In-app notification
 */
class ReportReady extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Report $report
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your Carbon Report is Ready'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Your carbon footprint report has been generated and is ready for download.'))
            ->line('**' . $this->report->title . '**')
            ->line(__('Period: :start to :end', [
                'start' => $this->report->period_start->format('d/m/Y'),
                'end' => $this->report->period_end->format('d/m/Y'),
            ]))
            ->action(__('Download Report'), url("/reports/{$this->report->id}/download"))
            ->line(__('This report will be available for 30 days.'))
            ->salutation(__('Best regards,') . "\n" . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'report_ready',
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'message' => __('Your carbon footprint report is ready for download.'),
            'download_url' => "/reports/{$this->report->id}/download",
        ];
    }
}
