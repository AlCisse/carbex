<?php

namespace App\Livewire\Promote;

use App\Models\Badge;
use App\Models\Organization;
use App\Services\Gamification\BadgeService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * BadgeShowcase Livewire Component
 *
 * Affichage promotionnel des badges pour communication externe.
 * Permet le partage sur les réseaux sociaux et le téléchargement d'assets.
 *
 * Tasks T169-T172 - Phase 10 (TrackZero Features)
 */
#[Layout('components.layouts.app')]
#[Title('Badge Showcase')]
class BadgeShowcase extends Component
{
    public ?string $selectedBadgeId = null;

    public bool $showShareModal = false;

    public bool $showEmbedModal = false;

    public bool $showDownloadModal = false;

    public string $embedSize = 'medium';

    public string $downloadFormat = 'png';

    protected BadgeService $badgeService;

    public function boot(BadgeService $badgeService): void
    {
        $this->badgeService = $badgeService;
    }

    #[Computed]
    public function organization(): ?Organization
    {
        return Auth::user()?->organization;
    }

    #[Computed]
    public function earnedBadges(): Collection
    {
        if (! $this->organization) {
            return collect();
        }

        return $this->organization->badges()
            ->orderByPivot('earned_at', 'desc')
            ->get()
            ->map(fn ($badge) => [
                'id' => $badge->id,
                'code' => $badge->code,
                'name' => $badge->translated_name,
                'description' => $badge->translated_description,
                'icon' => $badge->icon,
                'color' => $badge->color,
                'color_class' => $badge->color_class,
                'category' => $badge->category,
                'points' => $badge->points,
                'earned_at' => $badge->pivot->earned_at,
                'share_token' => $badge->pivot->share_token,
            ]);
    }

    #[Computed]
    public function selectedBadge(): ?array
    {
        if (! $this->selectedBadgeId) {
            return $this->earnedBadges->first();
        }

        return $this->earnedBadges->firstWhere('id', $this->selectedBadgeId);
    }

    #[Computed]
    public function totalPoints(): int
    {
        return $this->earnedBadges->sum('points');
    }

    public function selectBadge(string $badgeId): void
    {
        $this->selectedBadgeId = $badgeId;
    }

    // ==================== Share Methods ====================

    public function openShareModal(): void
    {
        $this->showShareModal = true;
    }

    public function closeShareModal(): void
    {
        $this->showShareModal = false;
    }

    public function getShareUrl(): ?string
    {
        if (! $this->selectedBadge) {
            return null;
        }

        return route('badge.public', ['token' => $this->selectedBadge['share_token']]);
    }

    public function getLinkedInShareUrl(): string
    {
        $url = urlencode($this->getShareUrl() ?? '');
        $title = urlencode($this->selectedBadge['name'] ?? 'Carbon Badge');
        $summary = urlencode(__('linscarbon.promote.linkedin_summary', [
            'badge' => $this->selectedBadge['name'] ?? '',
            'company' => $this->organization?->name ?? '',
        ]));

        return "https://www.linkedin.com/sharing/share-offsite/?url={$url}";
    }

    public function getTwitterShareUrl(): string
    {
        $url = urlencode($this->getShareUrl() ?? '');
        $text = urlencode(__('linscarbon.promote.twitter_text', [
            'badge' => $this->selectedBadge['name'] ?? '',
        ]));

        return "https://twitter.com/intent/tweet?url={$url}&text={$text}";
    }

    // ==================== Embed Methods ====================

    public function openEmbedModal(): void
    {
        $this->showEmbedModal = true;
    }

    public function closeEmbedModal(): void
    {
        $this->showEmbedModal = false;
    }

    public function setEmbedSize(string $size): void
    {
        $this->embedSize = $size;
    }

    #[Computed]
    public function embedCode(): string
    {
        if (! $this->selectedBadge) {
            return '';
        }

        $url = $this->getShareUrl();
        $dimensions = match ($this->embedSize) {
            'small' => ['width' => 200, 'height' => 250],
            'large' => ['width' => 400, 'height' => 500],
            default => ['width' => 300, 'height' => 375],
        };

        return sprintf(
            '<iframe src="%s?embed=1" width="%d" height="%d" frameborder="0" style="border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);"></iframe>',
            $url,
            $dimensions['width'],
            $dimensions['height']
        );
    }

    // ==================== Download Methods ====================

    public function openDownloadModal(): void
    {
        $this->showDownloadModal = true;
    }

    public function closeDownloadModal(): void
    {
        $this->showDownloadModal = false;
    }

    public function setDownloadFormat(string $format): void
    {
        $this->downloadFormat = $format;
    }

    public function downloadBadge(): void
    {
        if (! $this->selectedBadge) {
            return;
        }

        $badge = Badge::find($this->selectedBadge['id']);

        if (! $badge) {
            return;
        }

        // Generate badge asset
        $filename = $this->generateBadgeAsset($badge);

        if ($filename) {
            $this->dispatch('download-file', [
                'url' => Storage::url($filename),
                'filename' => "linscarbon-badge-{$badge->code}.{$this->downloadFormat}",
            ]);
        }
    }

    public function downloadEmailSignature(): void
    {
        if (! $this->selectedBadge || ! $this->organization) {
            return;
        }

        $html = view('badges.email-signature', [
            'badge' => $this->selectedBadge,
            'organization' => $this->organization,
            'shareUrl' => $this->getShareUrl(),
        ])->render();

        $this->dispatch('download-html', [
            'content' => $html,
            'filename' => 'linscarbon-email-signature.html',
        ]);
    }

    public function downloadSocialKit(): void
    {
        if (! $this->earnedBadges->count()) {
            return;
        }

        // This would generate a ZIP with multiple assets
        // For now, dispatch a notification
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => __('linscarbon.promote.social_kit_preparing'),
        ]);
    }

    /**
     * Generate badge asset file.
     */
    protected function generateBadgeAsset(Badge $badge): ?string
    {
        // In production, this would use Intervention Image or similar
        // For now, return the existing icon path or generate SVG
        $svgContent = $this->generateBadgeSvg($badge);

        $filename = "badges/{$this->organization->id}/{$badge->code}.svg";
        Storage::put($filename, $svgContent);

        return $filename;
    }

    /**
     * Generate SVG badge.
     */
    protected function generateBadgeSvg(Badge $badge): string
    {
        $color = match ($badge->color) {
            'emerald' => '#10b981',
            'blue' => '#3b82f6',
            'purple' => '#8b5cf6',
            'yellow' => '#f59e0b',
            'orange' => '#f97316',
            default => '#6b7280',
        };

        $icon = $badge->icon ?? 'award';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 250" width="200" height="250">
  <defs>
    <linearGradient id="badgeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$color};stop-opacity:1" />
      <stop offset="100%" style="stop-color:{$color};stop-opacity:0.8" />
    </linearGradient>
    <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
      <feDropShadow dx="0" dy="4" stdDeviation="4" flood-opacity="0.25"/>
    </filter>
  </defs>

  <!-- Badge shape -->
  <path d="M100 10 L180 50 L180 130 L100 240 L20 130 L20 50 Z"
        fill="url(#badgeGradient)" filter="url(#shadow)"/>

  <!-- Inner border -->
  <path d="M100 25 L165 58 L165 125 L100 220 L35 125 L35 58 Z"
        fill="none" stroke="white" stroke-width="2" opacity="0.5"/>

  <!-- Badge icon placeholder -->
  <circle cx="100" cy="90" r="35" fill="white" opacity="0.9"/>

  <!-- LinsCarbon logo text -->
  <text x="100" y="180" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="14" font-weight="bold">
    LINSCARBON
  </text>

  <!-- Badge name -->
  <text x="100" y="200" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="10" opacity="0.9">
    {$badge->translated_name}
  </text>
</svg>
SVG;
    }

    public function render()
    {
        return view('livewire.promote.badge-showcase');
    }
}
