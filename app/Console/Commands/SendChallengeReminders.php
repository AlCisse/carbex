<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ChallengeReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Send reminders for active challenges nearing their end date.
 *
 * Part of Phase 10: Employee engagement module (T182).
 *
 * @see specs/001-carbex-mvp-platform/tasks.md T182
 */
class SendChallengeReminders extends Command
{
    protected $signature = 'carbex:send-challenge-reminders
                            {--dry-run : Preview without sending}';

    protected $description = 'Send reminders for active challenges nearing completion';

    protected array $challenges = [
        'no_car_week' => [
            'id' => 'no_car_week',
            'title' => 'carbex.engage.challenges.no_car_week',
            'points' => 100,
            'duration_days' => 7,
        ],
        'meatless_monday' => [
            'id' => 'meatless_monday',
            'title' => 'carbex.engage.challenges.meatless_monday',
            'points' => 50,
            'duration_days' => 28,
        ],
        'zero_waste_lunch' => [
            'id' => 'zero_waste_lunch',
            'title' => 'carbex.engage.challenges.zero_waste_lunch',
            'points' => 75,
            'duration_days' => 14,
        ],
        'energy_saver' => [
            'id' => 'energy_saver',
            'title' => 'carbex.engage.challenges.energy_saver',
            'points' => 150,
            'duration_days' => 30,
        ],
        'digital_detox' => [
            'id' => 'digital_detox',
            'title' => 'carbex.engage.challenges.digital_detox',
            'points' => 60,
            'duration_days' => 7,
        ],
    ];

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $totalSent = 0;

        // Get all users with active challenges
        $users = User::whereNotNull('settings->active_challenges')->get();

        foreach ($users as $user) {
            $settings = $user->settings ?? [];
            $activeChallenges = $settings['active_challenges'] ?? [];

            foreach ($activeChallenges as $challengeId => $challengeData) {
                // Skip completed challenges
                if (($challengeData['status'] ?? '') !== 'active') {
                    continue;
                }

                // Skip if user has disabled engagement emails
                if (isset($settings['engagement_emails']) && $settings['engagement_emails'] === false) {
                    continue;
                }

                $challenge = $this->challenges[$challengeId] ?? null;
                if (! $challenge) {
                    continue;
                }

                // Calculate days remaining
                $joinedAt = Carbon::parse($challengeData['joined_at']);
                $endDate = $joinedAt->addDays($challenge['duration_days']);
                $daysRemaining = now()->diffInDays($endDate, false);

                // Send reminder if 1-3 days remaining
                if ($daysRemaining >= 1 && $daysRemaining <= 3) {
                    if ($dryRun) {
                        $this->line("[DRY-RUN] Would remind {$user->email} about {$challengeId} ({$daysRemaining} days left)");
                    } else {
                        try {
                            $user->notify(new ChallengeReminderNotification($challenge, $daysRemaining));
                            $this->line("Sent reminder to {$user->email} for {$challengeId}");
                            $totalSent++;
                        } catch (\Exception $e) {
                            $this->error("Failed to send to {$user->email}: {$e->getMessage()}");
                            Log::error('Failed to send challenge reminder', [
                                'user_id' => $user->id,
                                'challenge_id' => $challengeId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }
        }

        $this->info($dryRun ? 'Dry run completed.' : "Reminders sent to {$totalSent} users.");

        return self::SUCCESS;
    }
}
