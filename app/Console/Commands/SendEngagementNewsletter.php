<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\User;
use App\Notifications\EngagementNewsletterNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Send weekly engagement newsletter to all employees.
 *
 * Part of Phase 10: Employee engagement module (T182).
 *
 * @see specs/001-linscarbon-mvp-platform/tasks.md T182
 */
class SendEngagementNewsletter extends Command
{
    protected $signature = 'linscarbon:send-engagement-newsletter
                            {--org= : Send only to a specific organization ID}
                            {--dry-run : Preview without sending}';

    protected $description = 'Send weekly engagement newsletter to employees';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $orgId = $this->option('org');

        $query = Organization::query()
            ->whereHas('users', function ($q) {
                $q->whereJsonContains('settings->engagement_emails', true);
            });

        if ($orgId) {
            $query->where('id', $orgId);
        }

        $organizations = $query->get();

        if ($organizations->isEmpty()) {
            $this->info('No organizations with engagement email subscribers found.');

            return self::SUCCESS;
        }

        $totalSent = 0;

        foreach ($organizations as $organization) {
            $this->info("Processing organization: {$organization->name}");

            // Calculate team stats for the month
            $stats = $this->calculateTeamStats($organization);

            // Get users who opted into engagement emails
            $users = User::where('organization_id', $organization->id)
                ->where(function ($q) {
                    $q->whereJsonContains('settings->engagement_emails', true)
                        ->orWhereNull('settings->engagement_emails');
                })
                ->get();

            foreach ($users as $user) {
                if ($dryRun) {
                    $this->line("  [DRY-RUN] Would send to: {$user->email}");
                } else {
                    try {
                        $user->notify(new EngagementNewsletterNotification($organization, $stats));
                        $this->line("  Sent to: {$user->email}");
                        $totalSent++;
                    } catch (\Exception $e) {
                        $this->error("  Failed to send to {$user->email}: {$e->getMessage()}");
                        Log::error('Failed to send engagement newsletter', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        $this->info($dryRun ? 'Dry run completed.' : "Newsletter sent to {$totalSent} users.");

        return self::SUCCESS;
    }

    protected function calculateTeamStats(Organization $organization): array
    {
        $users = User::where('organization_id', $organization->id)->get();

        $challengesCompleted = 0;
        $totalPoints = 0;
        $topPerformer = null;
        $topPoints = 0;

        $startOfMonth = now()->startOfMonth();

        foreach ($users as $user) {
            $settings = $user->settings ?? [];

            // Count points
            $points = $settings['engagement_points'] ?? 0;
            $totalPoints += $points;

            // Find top performer
            if ($points > $topPoints && ($settings['participate_leaderboard'] ?? false)) {
                $topPoints = $points;
                $topPerformer = $user->name;
            }

            // Count challenges completed this month
            $activeChallenges = $settings['active_challenges'] ?? [];
            foreach ($activeChallenges as $challenge) {
                if (($challenge['status'] ?? '') === 'completed' &&
                    isset($challenge['completed_at']) &&
                    $challenge['completed_at'] >= $startOfMonth->toDateTimeString()) {
                    $challengesCompleted++;
                }
            }
        }

        return [
            'challenges_completed' => $challengesCompleted,
            'total_points' => $totalPoints,
            'top_performer' => $topPerformer,
            'user_count' => $users->count(),
        ];
    }
}
