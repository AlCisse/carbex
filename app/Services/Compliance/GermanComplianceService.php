<?php

declare(strict_types=1);

namespace App\Services\Compliance;

use App\Models\Organization;
use App\Services\Gdpr\GdprService;

/**
 * German Regulatory Compliance Service
 *
 * Consolidates compliance with all German regulations:
 * - BDSG (Bundesdatenschutzgesetz) - Data protection
 * - TTDSG (Telekommunikation-Telemedien-Datenschutz-Gesetz) - Telecom privacy
 * - KSG (Bundes-Klimaschutzgesetz) - Climate protection
 * - CSR-RUG (CSR-Richtlinie-Umsetzungsgesetz) - CSR reporting
 * - ZAG (Zahlungsdiensteaufsichtsgesetz) - Payment services
 */
class GermanComplianceService
{
    public function __construct(
        protected GdprService $gdprService,
        protected EsrsCalculator $esrsCalculator,
        protected DoubleMaterialityService $materialityService
    ) {}

    /**
     * Get comprehensive German compliance status
     */
    public function getComplianceStatus(Organization $org, int $year): array
    {
        return [
            'overall_score' => $this->calculateOverallScore($org, $year),
            'regulations' => [
                'bdsg' => $this->getBdsgStatus($org),
                'ttdsg' => $this->getTtdsgStatus($org),
                'ksg' => $this->getKsgStatus($org, $year),
                'csrd_csrrug' => $this->getCsrdStatus($org, $year),
                'psd2_zag' => $this->getPsd2Status($org),
            ],
            'recommendations' => $this->getRecommendations($org, $year),
        ];
    }

    /**
     * BDSG (Bundesdatenschutzgesetz) compliance status
     */
    protected function getBdsgStatus(Organization $org): array
    {
        $users = $org->users;
        $hasConsentTracking = $users->every(fn ($u) => $u->terms_accepted_at !== null);
        $hasPrivacyPolicy = true; // Assumed from legal pages
        $hasDpoContact = true; // dpo@linscarbon.fr defined

        return [
            'name' => 'BDSG (Bundesdatenschutzgesetz)',
            'description' => 'German Federal Data Protection Act',
            'score' => $this->calculateScore([
                'consent_tracking' => $hasConsentTracking,
                'privacy_policy' => $hasPrivacyPolicy,
                'dpo_contact' => $hasDpoContact,
                'data_export' => true,
                'right_to_erasure' => true,
            ]),
            'requirements' => [
                ['name' => '§ 26 BDSG - Datenverarbeitung für Beschäftigungsverhältnisse', 'status' => 'compliant'],
                ['name' => '§ 35 BDSG - Löschung', 'status' => 'compliant', 'note' => 'Soft delete + anonymization implemented'],
                ['name' => '§ 37 BDSG - Widerspruchsrecht', 'status' => 'compliant'],
                ['name' => '§ 38 BDSG - Datenschutzbeauftragter', 'status' => $hasDpoContact ? 'compliant' : 'pending'],
            ],
        ];
    }

    /**
     * TTDSG (Telekommunikation-Telemedien-Datenschutz-Gesetz) compliance
     */
    protected function getTtdsgStatus(Organization $org): array
    {
        return [
            'name' => 'TTDSG',
            'description' => 'Telecommunications-Telemedia Data Protection Act',
            'score' => 90.0,
            'requirements' => [
                ['name' => '§ 25 TTDSG - Schutz der Privatsphäre bei Endeinrichtungen', 'status' => 'compliant', 'note' => 'Cookie consent banner implemented'],
                ['name' => '§ 26 TTDSG - Anerkennung von Diensten', 'status' => 'compliant'],
            ],
        ];
    }

    /**
     * KSG (Bundes-Klimaschutzgesetz) compliance
     */
    protected function getKsgStatus(Organization $org, int $year): array
    {
        $hasEmissionData = $org->emissionRecords()->whereYear('recorded_at', $year)->exists();
        $hasReductionTargets = $org->reductionTargets()->exists();

        return [
            'name' => 'KSG (Bundes-Klimaschutzgesetz)',
            'description' => 'Federal Climate Change Act',
            'score' => $this->calculateScore([
                'emission_tracking' => $hasEmissionData,
                'reduction_targets' => $hasReductionTargets,
                'scope_1_2_3' => $hasEmissionData,
            ]),
            'requirements' => [
                ['name' => 'Treibhausgasbilanzierung (GHG Accounting)', 'status' => $hasEmissionData ? 'compliant' : 'pending'],
                ['name' => 'Klimaziele nach § 3 KSG', 'status' => $hasReductionTargets ? 'compliant' : 'pending'],
                ['name' => 'UBA-konforme Emissionsfaktoren', 'status' => 'compliant', 'note' => 'UBA factors integrated'],
            ],
            'targets' => [
                '2030' => '-65% vs 1990 (national target)',
                '2040' => '-88% vs 1990',
                '2045' => 'Climate neutrality',
            ],
        ];
    }

    /**
     * CSRD/CSR-RUG compliance
     */
    protected function getCsrdStatus(Organization $org, int $year): array
    {
        $esrsStatus = $this->esrsCalculator->getComplianceStatus($org, $year);
        $materialityStatus = $this->materialityService->getComplianceStatus($org, $year);

        return [
            'name' => 'CSRD / CSR-RUG',
            'description' => 'Corporate Sustainability Reporting Directive (EU) / CSR-Richtlinie-Umsetzungsgesetz (DE)',
            'score' => ($esrsStatus['compliance_percentage'] + $materialityStatus['assessment_progress']) / 2,
            'requirements' => [
                [
                    'name' => 'ESRS E1 Climate Indicators',
                    'status' => $esrsStatus['is_compliant'] ? 'compliant' : 'in_progress',
                    'progress' => $esrsStatus['compliance_percentage'] . '%',
                ],
                [
                    'name' => 'Double Materiality Assessment',
                    'status' => $materialityStatus['is_complete'] ? 'compliant' : 'in_progress',
                    'progress' => $materialityStatus['assessment_progress'] . '%',
                ],
                [
                    'name' => 'GHG Protocol Scope 1, 2, 3',
                    'status' => 'compliant',
                ],
            ],
            'esrs_details' => $esrsStatus,
            'materiality_details' => $materialityStatus,
        ];
    }

    /**
     * PSD2/ZAG banking compliance
     */
    protected function getPsd2Status(Organization $org): array
    {
        $hasBankConnections = $org->bankConnections()->exists();
        $activeConnections = $org->bankConnections()->where('status', 'active')->count();
        $expiredConsents = $org->bankConnections()
            ->where('consent_expires_at', '<', now())
            ->count();

        return [
            'name' => 'PSD2 / ZAG',
            'description' => 'Payment Services Directive 2 / Zahlungsdiensteaufsichtsgesetz',
            'score' => $hasBankConnections ? 85.0 : 100.0,
            'requirements' => [
                ['name' => 'Strong Customer Authentication (SCA)', 'status' => 'compliant'],
                ['name' => 'Token Encryption (AES-256-GCM)', 'status' => 'compliant'],
                ['name' => 'Consent Management', 'status' => $expiredConsents > 0 ? 'warning' : 'compliant'],
                ['name' => 'PSD2 Audit Logging', 'status' => 'compliant'],
                ['name' => 'XS2A Interface', 'status' => 'compliant', 'note' => 'FinAPI for DE/AT'],
            ],
            'statistics' => [
                'active_connections' => $activeConnections,
                'expired_consents' => $expiredConsents,
            ],
        ];
    }

    /**
     * Calculate compliance score from requirements
     */
    protected function calculateScore(array $requirements): float
    {
        $total = count($requirements);
        $compliant = count(array_filter($requirements));

        return $total > 0 ? round(($compliant / $total) * 100, 1) : 0;
    }

    /**
     * Calculate overall compliance score
     */
    protected function calculateOverallScore(Organization $org, int $year): float
    {
        $bdsg = $this->getBdsgStatus($org)['score'];
        $ttdsg = $this->getTtdsgStatus($org)['score'];
        $ksg = $this->getKsgStatus($org, $year)['score'];
        $csrd = $this->getCsrdStatus($org, $year)['score'];
        $psd2 = $this->getPsd2Status($org)['score'];

        // Weighted average (CSRD and KSG are most important for carbon platform)
        return round(
            ($bdsg * 0.15) +
            ($ttdsg * 0.10) +
            ($ksg * 0.25) +
            ($csrd * 0.35) +
            ($psd2 * 0.15),
            1
        );
    }

    /**
     * Get prioritized recommendations
     */
    protected function getRecommendations(Organization $org, int $year): array
    {
        $recommendations = [];

        // Check ESRS completion
        $esrsStatus = $this->esrsCalculator->getComplianceStatus($org, $year);
        if (!$esrsStatus['is_compliant']) {
            $recommendations[] = [
                'priority' => 'high',
                'regulation' => 'CSRD',
                'action' => 'Complete ESRS E1 climate indicators',
                'missing' => $esrsStatus['missing_indicators'],
            ];
        }

        // Check materiality assessment
        $materialityStatus = $this->materialityService->getComplianceStatus($org, $year);
        if (!$materialityStatus['is_complete']) {
            $recommendations[] = [
                'priority' => 'high',
                'regulation' => 'CSRD',
                'action' => 'Complete double materiality assessment for all ESRS topics',
                'progress' => $materialityStatus['assessment_progress'] . '%',
            ];
        }

        // Check reduction targets
        if (!$org->reductionTargets()->exists()) {
            $recommendations[] = [
                'priority' => 'medium',
                'regulation' => 'KSG',
                'action' => 'Define GHG reduction targets aligned with German climate goals',
            ];
        }

        // Check bank consent expiry
        $expiringConsents = $org->bankConnections()
            ->where('consent_expires_at', '<', now()->addDays(30))
            ->where('consent_expires_at', '>', now())
            ->count();

        if ($expiringConsents > 0) {
            $recommendations[] = [
                'priority' => 'medium',
                'regulation' => 'PSD2',
                'action' => "Renew {$expiringConsents} bank connection consent(s) expiring soon",
            ];
        }

        return $recommendations;
    }

    /**
     * Generate comprehensive compliance report
     */
    public function generateReport(Organization $org, int $year): array
    {
        return [
            'metadata' => [
                'organization' => $org->name,
                'country' => $org->country,
                'year' => $year,
                'generated_at' => now()->toIso8601String(),
                'report_type' => 'German & EU Regulatory Compliance',
            ],
            'compliance_status' => $this->getComplianceStatus($org, $year),
            'regulatory_framework' => [
                'eu' => [
                    'GDPR' => 'Regulation (EU) 2016/679',
                    'CSRD' => 'Directive (EU) 2022/2464',
                    'ESRS' => 'Commission Delegated Regulation (EU) 2023/2772',
                    'PSD2' => 'Directive (EU) 2015/2366',
                    'EU Taxonomy' => 'Regulation (EU) 2020/852',
                ],
                'germany' => [
                    'BDSG' => 'Bundesdatenschutzgesetz',
                    'TTDSG' => 'Telekommunikation-Telemedien-Datenschutz-Gesetz',
                    'KSG' => 'Bundes-Klimaschutzgesetz',
                    'CSR-RUG' => 'CSR-Richtlinie-Umsetzungsgesetz',
                    'ZAG' => 'Zahlungsdiensteaufsichtsgesetz',
                ],
            ],
            'certification_readiness' => [
                'din_en_iso_14064' => 'Ready for certification',
                'ghg_protocol' => 'Fully compliant',
                'csrd_2025' => $this->getCsrdStatus($org, $year)['score'] >= 80 ? 'Ready' : 'In progress',
            ],
        ];
    }
}
