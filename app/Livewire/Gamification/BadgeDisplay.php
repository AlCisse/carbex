<?php

namespace App\Livewire\Gamification;

use App\Models\Badge;
use App\Services\Gamification\BadgeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * BadgeDisplay Livewire Component
 *
 * Affiche les badges et le score de gamification d'une organisation.
 *
 * Constitution Carbex v3.0 - Section 9.9 (Gamification)
 */
class BadgeDisplay extends Component
{
    public array $badges = [];

    public array $score = [];

    public array $leaderboard = [];

    public bool $showLeaderboard = false;

    public ?string $selectedBadgeId = null;

    public bool $showShareModal = false;

    public ?string $shareUrl = null;

    protected BadgeService $badgeService;

    public function boot(BadgeService $badgeService): void
    {
        $this->badgeService = $badgeService;
    }

    public function mount(): void
    {
        $this->loadBadges();
        $this->loadScore();
    }

    public function loadBadges(): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $badgesStatus = $this->badgeService->getOrganizationBadgesStatus($organization);

        $this->badges = $badgesStatus->map(function ($item) {
            return [
                'id' => $item['badge']->id,
                'code' => $item['badge']->code,
                'name' => $item['badge']->translated_name,
                'description' => $item['badge']->translated_description,
                'icon' => $item['badge']->icon,
                'color' => $item['badge']->color,
                'color_class' => $item['badge']->color_class,
                'category' => $item['badge']->category,
                'points' => $item['badge']->points,
                'earned' => $item['earned'],
                'earned_at' => $item['earned_at']?->format('d/m/Y'),
                'progress' => $item['progress'],
                'share_token' => $item['share_token'],
            ];
        })->toArray();
    }

    public function loadScore(): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $this->score = $this->badgeService->calculateOrganizationScore($organization);
    }

    public function loadLeaderboard(): void
    {
        $this->leaderboard = $this->badgeService->getLeaderboard(10)->toArray();
        $this->showLeaderboard = true;
    }

    public function hideLeaderboard(): void
    {
        $this->showLeaderboard = false;
    }

    public function checkNewBadges(): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $newBadges = $this->badgeService->evaluateOrganizationBadges($organization);

        if ($newBadges->isNotEmpty()) {
            foreach ($newBadges as $badge) {
                $this->dispatch('badge-earned', [
                    'name' => $badge->translated_name,
                    'points' => $badge->points,
                ]);
            }

            $this->loadBadges();
            $this->loadScore();
        }
    }

    public function selectBadge(string $badgeId): void
    {
        $this->selectedBadgeId = $badgeId;
    }

    public function shareBadge(string $badgeId): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $badge = Badge::find($badgeId);

        if (! $badge) {
            return;
        }

        $this->shareUrl = $this->badgeService->getShareUrl($organization, $badge);
        $this->showShareModal = true;
    }

    public function closeShareModal(): void
    {
        $this->showShareModal = false;
        $this->shareUrl = null;
    }

    public function render()
    {
        return view('livewire.gamification.badge-display');
    }
}
