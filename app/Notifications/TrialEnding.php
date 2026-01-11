<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Trial Ending Notification
 *
 * Notifies user 3 days before trial ends:
 * - Email with upgrade CTA
 * - Summary of features they'll lose
 */
class TrialEnding extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysRemaining = $this->subscription->trialDaysRemaining();

        return (new MailMessage)
            ->subject(__('Your Carbex Trial Ends in :days Days', ['days' => $daysRemaining]))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Your free trial will end on :date.', ['date' => $this->subscription->trial_ends_at->format('d/m/Y')]))
            ->line(__('To continue tracking your carbon footprint and generating reports, upgrade to a paid plan.'))
            ->line(__('**What you get with a paid plan:**'))
            ->line('• ' . __('Unlimited bank synchronization'))
            ->line('• ' . __('Advanced emission reports'))
            ->line('• ' . __('Multi-site management'))
            ->line('• ' . __('Team collaboration features'))
            ->action(__('Upgrade Now'), url('/settings/billing'))
            ->line(__('Questions? Reply to this email and our team will be happy to help.'))
            ->salutation(__('Best regards,') . "\n" . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'trial_ending',
            'subscription_id' => $this->subscription->id,
            'days_remaining' => $this->subscription->trialDaysRemaining(),
            'ends_at' => $this->subscription->trial_ends_at->toIso8601String(),
            'message' => __('Your free trial ends in :days days. Upgrade to continue using Carbex.', [
                'days' => $this->subscription->trialDaysRemaining(),
            ]),
            'action_url' => '/settings/billing',
        ];
    }
}
