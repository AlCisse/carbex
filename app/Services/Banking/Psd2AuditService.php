<?php

declare(strict_types=1);

namespace App\Services\Banking;

use App\Models\BankConnection;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * PSD2 Audit Service for German BaFin Compliance
 *
 * Implements audit logging requirements:
 * - PSD2 Article 97: Strong Customer Authentication
 * - PSD2 Article 98: Exemptions from SCA
 * - BaFin requirements for Account Information Services (AIS)
 *
 * German regulatory references:
 * - § 55 ZAG (Zahlungsdiensteaufsichtsgesetz)
 * - BaFin Rundschreiben zu PSD2
 */
class Psd2AuditService
{
    /**
     * Log consent granted event
     */
    public function logConsentGranted(
        BankConnection $connection,
        User $user,
        string $consentId,
        array $dataCategories = ['accounts', 'transactions', 'balances']
    ): void {
        $this->log($connection->organization, $user, [
            'event_type' => 'consent_granted',
            'status' => 'success',
            'bank_connection_id' => $connection->id,
            'aspsp_id' => $connection->bank_id,
            'consent_id' => $consentId,
            'data_categories' => $dataCategories,
            'metadata' => [
                'provider' => $connection->provider,
                'consent_valid_until' => $connection->consent_expires_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Log consent revoked event
     */
    public function logConsentRevoked(
        BankConnection $connection,
        User $user,
        string $reason = 'user_request'
    ): void {
        $this->log($connection->organization, $user, [
            'event_type' => 'consent_revoked',
            'status' => 'success',
            'bank_connection_id' => $connection->id,
            'aspsp_id' => $connection->bank_id,
            'consent_id' => $connection->consent_id,
            'metadata' => [
                'reason' => $reason,
            ],
        ]);
    }

    /**
     * Log data access event
     */
    public function logDataAccess(
        BankConnection $connection,
        User $user,
        string $dataType,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null
    ): void {
        $this->log($connection->organization, $user, [
            'event_type' => 'data_access',
            'event_subtype' => $dataType, // accounts, transactions, balances
            'status' => 'success',
            'bank_connection_id' => $connection->id,
            'aspsp_id' => $connection->bank_id,
            'consent_id' => $connection->consent_id,
            'data_categories' => [$dataType],
            'data_from' => $from?->format('Y-m-d H:i:s'),
            'data_to' => $to?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Log SCA challenge event
     */
    public function logScaChallenge(
        Organization $org,
        User $user,
        string $scaMethod,
        string $status,
        ?string $bankConnectionId = null,
        ?string $errorMessage = null
    ): void {
        $this->log($org, $user, [
            'event_type' => 'sca_challenge',
            'event_subtype' => $scaMethod, // redirect, decoupled, embedded
            'status' => $status,
            'bank_connection_id' => $bankConnectionId,
            'sca_required' => true,
            'sca_method' => $scaMethod,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Log token refresh event
     */
    public function logTokenRefresh(
        BankConnection $connection,
        string $status,
        ?string $errorMessage = null
    ): void {
        $this->log($connection->organization, null, [
            'event_type' => 'token_refresh',
            'status' => $status,
            'bank_connection_id' => $connection->id,
            'aspsp_id' => $connection->bank_id,
            'consent_id' => $connection->consent_id,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Log transaction sync event
     */
    public function logTransactionSync(
        BankConnection $connection,
        int $transactionCount,
        string $status,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null
    ): void {
        $this->log($connection->organization, null, [
            'event_type' => 'transaction_sync',
            'status' => $status,
            'bank_connection_id' => $connection->id,
            'aspsp_id' => $connection->bank_id,
            'consent_id' => $connection->consent_id,
            'data_categories' => ['transactions'],
            'data_from' => $from?->format('Y-m-d H:i:s'),
            'data_to' => $to?->format('Y-m-d H:i:s'),
            'metadata' => [
                'transaction_count' => $transactionCount,
            ],
        ]);
    }

    /**
     * Log API error
     */
    public function logApiError(
        Organization $org,
        ?User $user,
        string $eventType,
        string $errorMessage,
        ?BankConnection $connection = null
    ): void {
        $this->log($org, $user, [
            'event_type' => $eventType,
            'status' => 'failure',
            'bank_connection_id' => $connection?->id,
            'aspsp_id' => $connection?->bank_id,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Core logging method
     */
    protected function log(Organization $org, ?User $user, array $data): void
    {
        DB::table('psd2_audit_logs')->insert([
            'id' => Str::uuid()->toString(),
            'organization_id' => $org->id,
            'user_id' => $user?->id,
            'bank_connection_id' => $data['bank_connection_id'] ?? null,
            'event_type' => $data['event_type'],
            'event_subtype' => $data['event_subtype'] ?? null,
            'status' => $data['status'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'request_id' => request()->header('X-Request-ID', Str::uuid()->toString()),
            'aspsp_id' => $data['aspsp_id'] ?? null,
            'consent_id' => $data['consent_id'] ?? null,
            'sca_required' => $data['sca_required'] ?? false,
            'sca_method' => $data['sca_method'] ?? null,
            'data_categories' => isset($data['data_categories']) ? json_encode($data['data_categories']) : null,
            'data_from' => $data['data_from'] ?? null,
            'data_to' => $data['data_to'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'metadata' => isset($data['metadata']) ? json_encode($data['metadata']) : null,
            'created_at' => now(),
        ]);
    }

    /**
     * Get audit log for organization
     */
    public function getAuditLog(
        Organization $org,
        ?\DateTimeInterface $from = null,
        ?\DateTimeInterface $to = null,
        ?string $eventType = null,
        int $limit = 100
    ): \Illuminate\Support\Collection {
        $query = DB::table('psd2_audit_logs')
            ->where('organization_id', $org->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        if ($eventType) {
            $query->where('event_type', $eventType);
        }

        return $query->get();
    }

    /**
     * Generate compliance report for BaFin
     */
    public function generateBafinReport(Organization $org, int $year): array
    {
        $startDate = "{$year}-01-01 00:00:00";
        $endDate = "{$year}-12-31 23:59:59";

        $logs = DB::table('psd2_audit_logs')
            ->where('organization_id', $org->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $consentEvents = $logs->whereIn('event_type', ['consent_granted', 'consent_revoked']);
        $scaEvents = $logs->where('event_type', 'sca_challenge');
        $dataAccessEvents = $logs->where('event_type', 'data_access');
        $failedEvents = $logs->where('status', 'failure');

        return [
            'report_metadata' => [
                'organization_id' => $org->id,
                'organization_name' => $org->name,
                'reporting_period' => [
                    'from' => $startDate,
                    'to' => $endDate,
                ],
                'generated_at' => now()->toIso8601String(),
                'regulatory_framework' => [
                    'psd2' => 'EU Directive 2015/2366',
                    'zag' => '§ 55 ZAG (Zahlungsdiensteaufsichtsgesetz)',
                    'bafin' => 'BaFin PSD2 Rundschreiben',
                ],
            ],
            'consent_management' => [
                'total_consents_granted' => $consentEvents->where('event_type', 'consent_granted')->count(),
                'total_consents_revoked' => $consentEvents->where('event_type', 'consent_revoked')->count(),
                'active_consents' => DB::table('bank_connections')
                    ->where('organization_id', $org->id)
                    ->where('status', 'active')
                    ->whereNotNull('consent_expires_at')
                    ->where('consent_expires_at', '>', now())
                    ->count(),
            ],
            'sca_statistics' => [
                'total_challenges' => $scaEvents->count(),
                'successful_challenges' => $scaEvents->where('status', 'success')->count(),
                'failed_challenges' => $scaEvents->where('status', 'failure')->count(),
                'by_method' => $scaEvents->groupBy('sca_method')
                    ->map(fn ($group) => $group->count())
                    ->toArray(),
            ],
            'data_access_statistics' => [
                'total_access_events' => $dataAccessEvents->count(),
                'by_data_category' => $dataAccessEvents->groupBy('event_subtype')
                    ->map(fn ($group) => $group->count())
                    ->toArray(),
            ],
            'security_incidents' => [
                'total_failures' => $failedEvents->count(),
                'by_event_type' => $failedEvents->groupBy('event_type')
                    ->map(fn ($group) => $group->count())
                    ->toArray(),
            ],
        ];
    }
}
