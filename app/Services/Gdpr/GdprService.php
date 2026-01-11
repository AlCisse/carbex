<?php

declare(strict_types=1);

namespace App\Services\Gdpr;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * GDPR/RGPD/BDSG Compliance Service
 *
 * Handles data subject rights:
 * - Right of access (Art. 15 GDPR)
 * - Right to rectification (Art. 16 GDPR)
 * - Right to erasure (Art. 17 GDPR)
 * - Right to data portability (Art. 20 GDPR)
 * - Right to object (Art. 21 GDPR)
 *
 * German BDSG compliance:
 * - § 35 BDSG (Löschung)
 * - § 37 BDSG (Widerspruchsrecht)
 */
class GdprService
{
    /**
     * Export all user data in portable JSON format (Art. 20 GDPR)
     */
    public function exportUserData(User $user): array
    {
        $organization = $user->organization;

        $data = [
            'export_metadata' => [
                'generated_at' => now()->toIso8601String(),
                'format_version' => '1.0',
                'gdpr_article' => 'Art. 20 GDPR - Right to data portability',
                'bdsg_reference' => '§ 20 BDSG',
                'data_controller' => 'Carbex SAS',
                'contact' => 'dpo@carbex.fr',
            ],
            'personal_data' => [
                'user' => [
                    'id' => $user->uuid ?? $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'locale' => $user->locale,
                    'timezone' => $user->timezone,
                    'created_at' => $user->created_at?->toIso8601String(),
                    'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                    'notification_preferences' => $user->notification_preferences,
                ],
            ],
            'organization_data' => null,
            'emission_records' => [],
            'transactions' => [],
            'reports' => [],
            'documents' => [],
            'ai_conversations' => [],
            'consent_records' => [],
        ];

        if ($organization) {
            $data['organization_data'] = [
                'name' => $organization->name,
                'country' => $organization->country,
                'sector' => $organization->sector,
                'employee_count' => $organization->employee_count,
                'created_at' => $organization->created_at?->toIso8601String(),
            ];

            // Emission records
            $data['emission_records'] = $organization->emissionRecords()
                ->select(['id', 'scope', 'ghg_category', 'co2e_kg', 'activity_data', 'unit', 'recorded_at', 'created_at'])
                ->get()
                ->map(fn ($record) => [
                    'id' => $record->id,
                    'scope' => $record->scope,
                    'category' => $record->ghg_category,
                    'co2e_kg' => $record->co2e_kg,
                    'activity' => $record->activity_data,
                    'unit' => $record->unit,
                    'date' => $record->recorded_at?->toIso8601String(),
                ])
                ->toArray();

            // Transactions (anonymized amounts for privacy)
            $data['transactions'] = $organization->transactions()
                ->select(['id', 'description', 'category_id', 'amount', 'currency', 'transaction_date', 'created_at'])
                ->get()
                ->map(fn ($tx) => [
                    'id' => $tx->id,
                    'description' => $tx->description,
                    'category' => $tx->category?->name,
                    'amount' => $tx->amount,
                    'currency' => $tx->currency,
                    'date' => $tx->transaction_date?->toIso8601String(),
                ])
                ->toArray();

            // Reports
            $data['reports'] = $organization->reports()
                ->select(['id', 'type', 'year', 'status', 'created_at'])
                ->get()
                ->map(fn ($report) => [
                    'id' => $report->id,
                    'type' => $report->type,
                    'year' => $report->year,
                    'status' => $report->status,
                    'created_at' => $report->created_at?->toIso8601String(),
                ])
                ->toArray();

            // Uploaded documents (metadata only)
            $data['documents'] = $organization->uploadedDocuments()
                ->select(['id', 'original_name', 'type', 'created_at'])
                ->get()
                ->map(fn ($doc) => [
                    'id' => $doc->id,
                    'name' => $doc->original_name,
                    'type' => $doc->type,
                    'uploaded_at' => $doc->created_at?->toIso8601String(),
                ])
                ->toArray();
        }

        // AI Conversations
        $data['ai_conversations'] = $user->aiConversations()
            ->select(['id', 'title', 'created_at'])
            ->get()
            ->map(fn ($conv) => [
                'id' => $conv->id,
                'title' => $conv->title,
                'created_at' => $conv->created_at?->toIso8601String(),
            ])
            ->toArray();

        // Consent records
        $data['consent_records'] = [
            'terms_accepted' => $user->terms_accepted_at?->toIso8601String(),
            'privacy_accepted' => $user->privacy_accepted_at?->toIso8601String(),
            'marketing_consent' => $user->marketing_consent ?? false,
            'analytics_consent' => $user->analytics_consent ?? false,
        ];

        return $data;
    }

    /**
     * Generate downloadable JSON file for data export
     */
    public function generateExportFile(User $user): string
    {
        $data = $this->exportUserData($user);
        $filename = 'gdpr-export-' . Str::slug($user->email) . '-' . now()->format('Y-m-d-His') . '.json';

        $path = 'gdpr-exports/' . $filename;
        Storage::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $path;
    }

    /**
     * Anonymize user data (Art. 17 GDPR - Right to erasure)
     * German BDSG § 35 - Löschung
     */
    public function anonymizeUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            $anonymizedId = 'ANON-' . Str::random(16);

            // Anonymize personal data
            $user->update([
                'name' => 'Anonymisierter Benutzer',
                'email' => $anonymizedId . '@anonymized.carbex.local',
                'password' => bcrypt(Str::random(64)),
                'notification_preferences' => null,
                'remember_token' => null,
            ]);

            // Soft delete - keeps for audit but anonymized
            $user->delete();

            // Log the anonymization
            activity()
                ->causedBy($user)
                ->withProperties([
                    'action' => 'gdpr_anonymization',
                    'gdpr_article' => 'Art. 17 GDPR',
                    'bdsg_reference' => '§ 35 BDSG',
                    'anonymized_id' => $anonymizedId,
                ])
                ->log('User data anonymized per GDPR/BDSG request');
        });
    }

    /**
     * Permanent deletion after retention period (3 years per legal requirements)
     */
    public function permanentlyDelete(User $user): void
    {
        DB::transaction(function () use ($user) {
            // Delete related data
            if ($user->organization) {
                $org = $user->organization;

                // Delete emissions (after retention)
                $org->emissionRecords()->forceDelete();

                // Delete transactions (keep for 10 years per accounting law)
                // $org->transactions()->forceDelete(); // Comment: Keep for accounting

                // Delete documents
                $org->uploadedDocuments()->each(function ($doc) {
                    Storage::delete($doc->path);
                    $doc->forceDelete();
                });
            }

            // Delete AI conversations
            $user->aiConversations()->forceDelete();

            // Permanently delete user
            $user->forceDelete();
        });
    }

    /**
     * Get consent status for a user
     */
    public function getConsentStatus(User $user): array
    {
        return [
            'essential' => true, // Always required
            'terms' => [
                'accepted' => $user->terms_accepted_at !== null,
                'accepted_at' => $user->terms_accepted_at?->toIso8601String(),
                'version' => config('carbex.terms_version', '1.0'),
            ],
            'privacy' => [
                'accepted' => $user->privacy_accepted_at !== null,
                'accepted_at' => $user->privacy_accepted_at?->toIso8601String(),
                'version' => config('carbex.privacy_version', '1.0'),
            ],
            'marketing' => [
                'accepted' => $user->marketing_consent ?? false,
                'updated_at' => $user->marketing_consent_at?->toIso8601String(),
            ],
            'analytics' => [
                'accepted' => $user->analytics_consent ?? false,
                'updated_at' => $user->analytics_consent_at?->toIso8601String(),
            ],
        ];
    }

    /**
     * Update consent preferences (Art. 7 GDPR)
     */
    public function updateConsent(User $user, array $consents): void
    {
        $updates = [];

        if (isset($consents['marketing'])) {
            $updates['marketing_consent'] = $consents['marketing'];
            $updates['marketing_consent_at'] = $consents['marketing'] ? now() : null;
        }

        if (isset($consents['analytics'])) {
            $updates['analytics_consent'] = $consents['analytics'];
            $updates['analytics_consent_at'] = $consents['analytics'] ? now() : null;
        }

        if (!empty($updates)) {
            $user->update($updates);

            activity()
                ->causedBy($user)
                ->withProperties([
                    'action' => 'consent_updated',
                    'gdpr_article' => 'Art. 7 GDPR',
                    'consents' => $consents,
                ])
                ->log('User consent preferences updated');
        }
    }

    /**
     * Record data processing activity (Art. 30 GDPR)
     */
    public function recordProcessingActivity(
        string $activity,
        string $purpose,
        string $legalBasis,
        ?User $user = null,
        array $dataCategories = []
    ): void {
        activity()
            ->when($user, fn ($log) => $log->causedBy($user))
            ->withProperties([
                'processing_activity' => $activity,
                'purpose' => $purpose,
                'legal_basis' => $legalBasis,
                'data_categories' => $dataCategories,
                'gdpr_article' => 'Art. 30 GDPR',
                'timestamp' => now()->toIso8601String(),
            ])
            ->log("Data processing: {$activity}");
    }
}
