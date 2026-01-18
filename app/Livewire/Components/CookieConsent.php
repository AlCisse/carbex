<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Cookie;

/**
 * Cookie Consent Banner - GDPR/RGPD/BDSG Compliant
 *
 * EU ePrivacy Directive + GDPR Art. 7
 * German TTDSG (Telekommunikation-Telemedien-Datenschutz-Gesetz)
 */
class CookieConsent extends Component
{
    public bool $showBanner = true;
    public bool $showDetails = false;

    public bool $essential = true; // Always required
    public bool $analytics = false;
    public bool $marketing = false;
    public bool $functional = true;

    public function mount(): void
    {
        // Check if consent already given
        $consent = request()->cookie('linscarbon_cookie_consent');

        if ($consent) {
            $this->showBanner = false;
            $preferences = json_decode($consent, true);

            if ($preferences) {
                $this->analytics = $preferences['analytics'] ?? false;
                $this->marketing = $preferences['marketing'] ?? false;
                $this->functional = $preferences['functional'] ?? true;
            }
        }
    }

    /**
     * Accept all cookies
     */
    public function acceptAll(): void
    {
        $this->analytics = true;
        $this->marketing = true;
        $this->functional = true;

        $this->saveConsent();
    }

    /**
     * Accept only essential cookies
     */
    public function acceptEssential(): void
    {
        $this->analytics = false;
        $this->marketing = false;
        $this->functional = true;

        $this->saveConsent();
    }

    /**
     * Save custom preferences
     */
    public function savePreferences(): void
    {
        $this->saveConsent();
    }

    /**
     * Toggle details panel
     */
    public function toggleDetails(): void
    {
        $this->showDetails = !$this->showDetails;
    }

    /**
     * Save consent to cookie (1 year validity per GDPR guidelines)
     */
    protected function saveConsent(): void
    {
        $consent = [
            'essential' => true,
            'analytics' => $this->analytics,
            'marketing' => $this->marketing,
            'functional' => $this->functional,
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0',
        ];

        // 365 days cookie duration
        Cookie::queue('linscarbon_cookie_consent', json_encode($consent), 60 * 24 * 365);

        // Update user preferences if logged in
        if (auth()->check()) {
            auth()->user()->update([
                'analytics_consent' => $this->analytics,
                'marketing_consent' => $this->marketing,
                'analytics_consent_at' => $this->analytics ? now() : null,
                'marketing_consent_at' => $this->marketing ? now() : null,
            ]);
        }

        $this->showBanner = false;

        // Emit event for JavaScript to handle (e.g., load/unload scripts)
        $this->dispatch('cookie-consent-updated', consent: $consent);
    }

    public function render()
    {
        return view('livewire.components.cookie-consent');
    }
}
