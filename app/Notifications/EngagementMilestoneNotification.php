<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Celebration notification for engagement milestones (points, badges, quiz completion).
 *
 * Part of Phase 10: Employee engagement module (T182).
 *
 * @see specs/001-carbex-mvp-platform/tasks.md T182
 */
class EngagementMilestoneNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const TYPE_QUIZ_COMPLETED = 'quiz_completed';

    public const TYPE_CHALLENGE_COMPLETED = 'challenge_completed';

    public const TYPE_POINTS_MILESTONE = 'points_milestone';

    public const TYPE_LEADERBOARD_TOP3 = 'leaderboard_top3';

    public function __construct(
        protected string $milestoneType,
        protected array $data = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? config('app.locale', 'fr');
        app()->setLocale($locale);

        $mail = (new MailMessage);

        match ($this->milestoneType) {
            self::TYPE_QUIZ_COMPLETED => $this->buildQuizCompletedMail($mail, $notifiable),
            self::TYPE_CHALLENGE_COMPLETED => $this->buildChallengeCompletedMail($mail, $notifiable),
            self::TYPE_POINTS_MILESTONE => $this->buildPointsMilestoneMail($mail, $notifiable),
            self::TYPE_LEADERBOARD_TOP3 => $this->buildLeaderboardMail($mail, $notifiable),
            default => $this->buildGenericMail($mail, $notifiable),
        };

        $mail->action(__('carbex.notifications.milestone.cta'), route('engage.employees'))
            ->salutation(__('carbex.notifications.milestone.salutation'));

        return $mail;
    }

    protected function buildQuizCompletedMail(MailMessage $mail, object $notifiable): void
    {
        $score = $this->data['score'] ?? 0;

        $mail->subject(__('carbex.notifications.milestone.quiz_subject'))
            ->greeting(__('carbex.notifications.milestone.quiz_greeting', ['name' => $notifiable->name]))
            ->line(__('carbex.notifications.milestone.quiz_score', ['score' => $score]));

        if ($score >= 80) {
            $mail->line(__('carbex.notifications.milestone.quiz_excellent'));
        } elseif ($score >= 60) {
            $mail->line(__('carbex.notifications.milestone.quiz_good'));
        } else {
            $mail->line(__('carbex.notifications.milestone.quiz_improve'));
        }

        $mail->line(__('carbex.notifications.milestone.quiz_points', ['points' => 25]));
    }

    protected function buildChallengeCompletedMail(MailMessage $mail, object $notifiable): void
    {
        $challengeTitle = $this->data['challenge_title'] ?? 'Challenge';
        $points = $this->data['points'] ?? 0;

        $mail->subject(__('carbex.notifications.milestone.challenge_subject', ['challenge' => $challengeTitle]))
            ->greeting(__('carbex.notifications.milestone.challenge_greeting', ['name' => $notifiable->name]))
            ->line(__('carbex.notifications.milestone.challenge_completed', ['challenge' => $challengeTitle]))
            ->line(__('carbex.notifications.milestone.challenge_points', ['points' => $points]))
            ->line(__('carbex.notifications.milestone.challenge_impact'));
    }

    protected function buildPointsMilestoneMail(MailMessage $mail, object $notifiable): void
    {
        $points = $this->data['points'] ?? 0;
        $milestone = $this->data['milestone'] ?? $points;

        $mail->subject(__('carbex.notifications.milestone.points_subject', ['milestone' => $milestone]))
            ->greeting(__('carbex.notifications.milestone.points_greeting', ['name' => $notifiable->name]))
            ->line(__('carbex.notifications.milestone.points_reached', ['milestone' => $milestone]))
            ->line(__('carbex.notifications.milestone.points_total', ['points' => $points]))
            ->line(__('carbex.notifications.milestone.points_keep_going'));
    }

    protected function buildLeaderboardMail(MailMessage $mail, object $notifiable): void
    {
        $rank = $this->data['rank'] ?? 1;

        $mail->subject(__('carbex.notifications.milestone.leaderboard_subject', ['rank' => $rank]))
            ->greeting(__('carbex.notifications.milestone.leaderboard_greeting', ['name' => $notifiable->name]))
            ->line(__('carbex.notifications.milestone.leaderboard_rank', ['rank' => $rank]))
            ->line(__('carbex.notifications.milestone.leaderboard_message'));
    }

    protected function buildGenericMail(MailMessage $mail, object $notifiable): void
    {
        $mail->subject(__('carbex.notifications.milestone.generic_subject'))
            ->greeting(__('carbex.notifications.milestone.generic_greeting', ['name' => $notifiable->name]))
            ->line(__('carbex.notifications.milestone.generic_message'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'engagement_milestone',
            'milestone_type' => $this->milestoneType,
            'data' => $this->data,
        ];
    }
}
