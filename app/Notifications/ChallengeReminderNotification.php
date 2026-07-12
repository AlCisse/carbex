<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Reminder notification for active challenges nearing their end date.
 *
 * Part of Phase 10: Employee engagement module (T182).
 *
 * @see specs/001-linscarbon-mvp-platform/tasks.md T182
 */
class ChallengeReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected array $challenge,
        protected int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? config('app.locale', 'fr');
        app()->setLocale($locale);

        $challengeTitle = $this->getChallengeTitle($locale);
        $points = $this->challenge['points'] ?? 0;

        $mail = (new MailMessage)
            ->subject(__('linscarbon.notifications.challenge_reminder.subject', ['challenge' => $challengeTitle]))
            ->greeting(__('linscarbon.notifications.challenge_reminder.greeting', ['name' => $notifiable->name]));

        if ($this->daysRemaining <= 1) {
            $mail->line(__('linscarbon.notifications.challenge_reminder.last_day', ['challenge' => $challengeTitle]));
        } else {
            $mail->line(__('linscarbon.notifications.challenge_reminder.days_left', [
                'challenge' => $challengeTitle,
                'days' => $this->daysRemaining,
            ]));
        }

        $mail->line(__('linscarbon.notifications.challenge_reminder.points_reminder', ['points' => $points]))
            ->action(__('linscarbon.notifications.challenge_reminder.cta'), route('engage.employees'))
            ->line(__('linscarbon.notifications.challenge_reminder.encouragement'));

        return $mail->salutation(__('linscarbon.notifications.challenge_reminder.salutation'));
    }

    protected function getChallengeTitle(string $locale): string
    {
        $key = 'linscarbon.engage.challenges.' . $this->challenge['id'];

        return __($key);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'challenge_reminder',
            'challenge_id' => $this->challenge['id'],
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
