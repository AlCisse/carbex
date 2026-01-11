<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Payment Failed Notification
 *
 * Notifies user when a subscription payment fails:
 * - Email with retry link
 * - Instructions to update payment method
 */
class PaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public object $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Action Required: Payment Failed'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('We were unable to process your subscription payment.'))
            ->line('**' . __('Amount') . '**: ' . number_format($this->invoice->amount_due / 100, 2, ',', ' ') . ' €')
            ->line(__('Please update your payment method to maintain access to your Carbex account.'))
            ->action(__('Update Payment Method'), url('/settings/billing'))
            ->line(__('If you need assistance, please contact our support team.'))
            ->salutation(__('Best regards,') . "\n" . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_failed',
            'subscription_id' => $this->subscription->id,
            'amount' => $this->invoice->amount_due,
            'message' => __('Your subscription payment failed. Please update your payment method.'),
            'action_url' => '/settings/billing',
        ];
    }
}
