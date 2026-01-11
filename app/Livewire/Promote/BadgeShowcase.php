<?php

namespace App\Livewire\Promote;

use App\Models\Badge;
use App\Services\Gamification\BadgeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * BadgeShowcase Livewire Component
 *
 * Displays earned badges with sharing and download options.
 * Part of the "Promote" pillar (TrackZero-inspired).
 *
 * @see specs/001-carbex-mvp-platform/tasks.md T169-T172
 */
#[Layout('layouts.app')]
#[Title('Vitrine durabilité - Carbex')]
class BadgeShowcase extends Component
{
    public array $earnedBadges = [];

    public array $organizationInfo = [];

    public ?string $selectedBadgeId = null;

    public bool $showShareModal = false;

    public bool $showEmbedModal = false;

    public ?string $shareUrl = null;

    public ?string $embedCode = null;

    protected BadgeService $badgeService;

    public function boot(BadgeService $badgeService): void
    {
        $this->badgeService = $badgeService;
    }

    public function mount(): void
    {
        $this->loadEarnedBadges();
        $this->loadOrganizationInfo();
    }

    public function loadEarnedBadges(): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $badgesStatus = $this->badgeService->getOrganizationBadgesStatus($organization);

        $this->earnedBadges = $badgesStatus
            ->filter(fn ($item) => $item['earned'])
            ->map(function ($item) {
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
                    'earned_at' => $item['earned_at']?->format('d/m/Y'),
                    'share_token' => $item['share_token'],
                ];
            })
            ->values()
            ->toArray();
    }

    public function loadOrganizationInfo(): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $this->organizationInfo = [
            'name' => $organization->name,
            'country' => $organization->country,
            'sector' => $organization->sector,
            'total_points' => $this->badgeService->calculateOrganizationScore($organization)['total_points'] ?? 0,
            'badges_count' => count($this->earnedBadges),
        ];
    }

    public function openShareModal(string $badgeId): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $badge = Badge::find($badgeId);

        if (! $badge) {
            return;
        }

        $this->selectedBadgeId = $badgeId;
        $this->shareUrl = $this->badgeService->getShareUrl($organization, $badge);
        $this->showShareModal = true;
    }

    public function closeShareModal(): void
    {
        $this->showShareModal = false;
        $this->shareUrl = null;
        $this->selectedBadgeId = null;
    }

    public function openEmbedModal(string $badgeId): void
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return;
        }

        $badge = Badge::find($badgeId);

        if (! $badge) {
            return;
        }

        $this->selectedBadgeId = $badgeId;
        $shareUrl = $this->badgeService->getShareUrl($organization, $badge);

        // Generate embed code for website
        $this->embedCode = sprintf(
            '<a href="%s" target="_blank" rel="noopener" title="%s - Carbex">
  <img src="%s" alt="%s" style="max-width: 200px;" />
</a>',
            $shareUrl,
            e($badge->translated_name),
            url("/api/badges/{$badge->code}/image"),
            e($badge->translated_name . ' - ' . $organization->name)
        );

        $this->showEmbedModal = true;
    }

    public function closeEmbedModal(): void
    {
        $this->showEmbedModal = false;
        $this->embedCode = null;
        $this->selectedBadgeId = null;
    }

    public function getLinkedInShareUrl(string $badgeId): string
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return '#';
        }

        $badge = Badge::find($badgeId);

        if (! $badge) {
            return '#';
        }

        $shareUrl = $this->badgeService->getShareUrl($organization, $badge);
        $text = sprintf(
            __('carbex.promote.linkedin_share_text'),
            $badge->translated_name,
            $organization->name
        );

        return 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareUrl);
    }

    public function getTwitterShareUrl(string $badgeId): string
    {
        $organization = Auth::user()?->organization;

        if (! $organization) {
            return '#';
        }

        $badge = Badge::find($badgeId);

        if (! $badge) {
            return '#';
        }

        $shareUrl = $this->badgeService->getShareUrl($organization, $badge);
        $text = sprintf(
            __('carbex.promote.twitter_share_text'),
            $badge->translated_name,
            $organization->name
        );

        return 'https://twitter.com/intent/tweet?text=' . urlencode($text) . '&url=' . urlencode($shareUrl);
    }

    public function downloadBadge(string $badgeId, string $format = 'png'): void
    {
        // This would trigger a download - for now, redirect to API endpoint
        $badge = Badge::find($badgeId);

        if ($badge) {
            $this->dispatch('download-badge', [
                'url' => url("/api/badges/{$badge->code}/download?format={$format}"),
                'filename' => "carbex-badge-{$badge->code}.{$format}",
            ]);
        }
    }

    public function getSelectedBadge(): ?array
    {
        if (! $this->selectedBadgeId) {
            return null;
        }

        return collect($this->earnedBadges)->firstWhere('id', $this->selectedBadgeId);
    }

    public function render()
    {
        return view('livewire.promote.badge-showcase');
    }
}
