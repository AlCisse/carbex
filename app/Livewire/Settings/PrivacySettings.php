<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Services\Gdpr\GdprService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

/**
 * Privacy Settings - GDPR/DSGVO/BDSG Compliant
 *
 * Implements all data subject rights:
 * - Art. 15 DSGVO: Right of access
 * - Art. 16 DSGVO: Right to rectification
 * - Art. 17 DSGVO: Right to erasure (§ 35 BDSG)
 * - Art. 20 DSGVO: Right to data portability
 * - Art. 21 DSGVO: Right to object (§ 37 BDSG)
 */
class PrivacySettings extends Component
{
    public bool $marketingConsent = false;
    public bool $analyticsConsent = false;
    public bool $aiConsent = true;

    public bool $showDeleteModal = false;
    public string $deleteConfirmation = '';

    public bool $isExporting = false;
    public ?string $exportPath = null;

    protected GdprService $gdprService;

    public function boot(GdprService $gdprService): void
    {
        $this->gdprService = $gdprService;
    }

    public function mount(): void
    {
        $user = auth()->user();

        $this->marketingConsent = $user->marketing_consent ?? false;
        $this->analyticsConsent = $user->analytics_consent ?? false;
        $this->aiConsent = $user->ai_consent ?? true;
    }

    /**
     * Update consent preferences (Art. 7 DSGVO)
     */
    public function updateConsent(string $type): void
    {
        $user = auth()->user();

        match ($type) {
            'marketing' => $user->update([
                'marketing_consent' => $this->marketingConsent,
                'marketing_consent_at' => $this->marketingConsent ? now() : null,
            ]),
            'analytics' => $user->update([
                'analytics_consent' => $this->analyticsConsent,
                'analytics_consent_at' => $this->analyticsConsent ? now() : null,
            ]),
            'ai' => $user->update([
                'ai_consent' => $this->aiConsent,
                'ai_consent_at' => $this->aiConsent ? now() : null,
            ]),
            default => null,
        };

        activity()
            ->causedBy($user)
            ->withProperties([
                'action' => 'consent_updated',
                'type' => $type,
                'value' => match ($type) {
                    'marketing' => $this->marketingConsent,
                    'analytics' => $this->analyticsConsent,
                    'ai' => $this->aiConsent,
                    default => null,
                },
                'gdpr_article' => 'Art. 7 DSGVO',
            ])
            ->log('Consent preference updated');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('linscarbon.gdpr.consent_updated'),
        ]);
    }

    /**
     * Export user data (Art. 20 DSGVO - Right to data portability)
     */
    public function exportData(): void
    {
        $this->isExporting = true;

        try {
            $path = $this->gdprService->generateExportFile(auth()->user());
            $this->exportPath = $path;

            activity()
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'data_export',
                    'gdpr_article' => 'Art. 20 DSGVO',
                ])
                ->log('User data exported');

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('linscarbon.gdpr.export_ready'),
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('linscarbon.gdpr.export_error'),
            ]);
        }

        $this->isExporting = false;
    }

    /**
     * Download exported data
     */
    public function downloadExport(): ?\Symfony\Component\HttpFoundation\StreamedResponse
    {
        if ($this->exportPath && Storage::exists($this->exportPath)) {
            return Storage::download($this->exportPath);
        }

        return null;
    }

    /**
     * Open delete account modal
     */
    public function confirmDelete(): void
    {
        $this->showDeleteModal = true;
        $this->deleteConfirmation = '';
    }

    /**
     * Close delete modal
     */
    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteConfirmation = '';
    }

    /**
     * Delete account (Art. 17 DSGVO - Right to erasure, § 35 BDSG)
     */
    public function deleteAccount(): void
    {
        $confirmWord = __('linscarbon.gdpr.delete_confirm_word');

        if (strtoupper($this->deleteConfirmation) !== strtoupper($confirmWord)) {
            $this->addError('deleteConfirmation', __('linscarbon.gdpr.delete_confirm_error'));
            return;
        }

        $user = auth()->user();

        activity()
            ->causedBy($user)
            ->withProperties([
                'action' => 'account_deletion_requested',
                'gdpr_article' => 'Art. 17 DSGVO',
                'bdsg_reference' => '§ 35 BDSG',
            ])
            ->log('Account deletion requested');

        // Anonymize user data
        $this->gdprService->anonymizeUser($user);

        // Logout user
        auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        // Redirect to home with message
        return redirect()->route('home')->with('success', __('linscarbon.gdpr.account_deleted'));
    }

    public function render()
    {
        return view('livewire.settings.privacy-settings', [
            'consentStatus' => $this->gdprService->getConsentStatus(auth()->user()),
        ]);
    }
}
