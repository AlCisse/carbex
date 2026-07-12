<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Subscription Canceled Notification
 *
 * Notifies user when their subscription is canceled:
 * - Email with reactivation CTA
 * - Information about data retention
 */
class SubscriptionCanceled extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject(__('Your LinsCarbon Subscription Has Been Canceled'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('Your LinsCarbon subscription has been canceled.'))
            ->line(__('**Important information:**'))
            ->line('• ' . __('Your data will be retained for 30 days'))
            ->line('• ' . __('You can reactivate your subscription at any time'))
            ->line('• ' . __('Downloaded reports remain accessible'))
            ->line(__('We\'d love to have you back! If you change your mind, you can resubscribe at any time.'))
            ->action(__('Reactivate Subscription'), url('/settings/billing'))
            ->line(__('If you have any feedback about why you left, we\'d love to hear it. Just reply to this email.'))
            ->salutation(__('Best regards,') . "\n" . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_canceled',
            'subscription_id' => $this->subscription->id,
            'message' => __('Your subscription has been canceled. Your data will be retained for 30 days.'),
            'action_url' => '/settings/billing',
        ];
    }
}
