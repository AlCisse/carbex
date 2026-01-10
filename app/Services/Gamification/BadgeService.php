<?php

namespace App\Services\Gamification;

use App\Models\Action;
use App\Models\Assessment;
use App\Models\Badge;
use App\Models\Organization;
use App\Models\ReductionTarget;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * BadgeService
 *
 * Service de gamification pour gérer l'attribution et le suivi des badges.
 * Évalue automatiquement les critères d'obtention et attribue les badges.
 *
 * Constitution Carbex v3.0 - Section 9.9 (Gamification)
 */
class BadgeService
{
    /**
     * Évalue et attribue tous les badges éligibles pour une organisation.
     *
     * @return Collection<int, Badge> Badges nouvellement attribués
     */
    public function evaluateOrganizationBadges(Organization $organization): Collection
    {
        $newBadges = collect();

        $evaluators = [
            Badge::FIRST_ASSESSMENT => fn () => $this->checkFirstAssessment($organization),
            Badge::FIVE_ASSESSMENTS => fn () => $this->checkFiveAssessments($organization),
            Badge::CARBON_REDUCER_10 => fn () => $this->checkCarbonReducer($organization, 10),
            Badge::CARBON_REDUCER_25 => fn () => $this->checkCarbonReducer($organization, 25),
            Badge::SCOPE3_CHAMPION => fn () => $this->checkScope3Champion($organization),
            Badge::DATA_QUALITY => fn () => $this->checkDataQuality($organization),
            Badge::SBTI_ALIGNED => fn () => $this->checkSbtiAligned($organization),
            Badge::SUPPLIER_ENGAGED => fn () => $this->checkSupplierEngaged($organization),
        ];

        foreach ($evaluators as $badgeCode => $evaluator) {
            if ($this->organizationHasBadge($organization, $badgeCode)) {
                continue;
            }

            if ($evaluator()) {
                $badge = $this->awardBadgeToOrganization($organization, $badgeCode);
                if ($badge) {
                    $newBadges->push($badge);
                }
            }
        }

        return $newBadges;
    }

    /**
     * Attribue un badge à une organisation.
     */
    public function awardBadgeToOrganization(
        Organization $organization,
        string $badgeCode,
        array $metadata = []
    ): ?Badge {
        $badge = Badge::where('code', $badgeCode)->where('is_active', true)->first();

        if (! $badge) {
            return null;
        }

        if ($this->organizationHasBadge($organization, $badgeCode)) {
            return null;
        }

        $organization->badges()->attach($badge->id, [
            'id' => Str::uuid(),
            'earned_at' => now(),
            'share_token' => Str::random(32),
            'metadata' => json_encode($metadata),
        ]);

        return $badge;
    }

    /**
     * Attribue un badge à un utilisateur.
     */
    public function awardBadgeToUser(User $user, string $badgeCode, array $metadata = []): ?Badge
    {
        $badge = Badge::where('code', $badgeCode)->where('is_active', true)->first();

        if (! $badge) {
            return null;
        }

        if ($this->userHasBadge($user, $badgeCode)) {
            return null;
        }

        $user->badges()->attach($badge->id, [
            'id' => Str::uuid(),
            'earned_at' => now(),
            'metadata' => json_encode($metadata),
        ]);

        return $badge;
    }

    /**
     * Vérifie si une organisation possède un badge.
     */
    public function organizationHasBadge(Organization $organization, string $badgeCode): bool
    {
        return $organization->badges()->where('code', $badgeCode)->exists();
    }

    /**
     * Vérifie si un utilisateur possède un badge.
     */
    public function userHasBadge(User $user, string $badgeCode): bool
    {
        return $user->badges()->where('code', $badgeCode)->exists();
    }

    /**
     * Obtient tous les badges d'une organisation avec leur statut.
     *
     * @return Collection<int, array>
     */
    public function getOrganizationBadgesStatus(Organization $organization): Collection
    {
        $allBadges = Badge::active()->orderBy('sort_order')->get();
        $earnedBadges = $organization->badges()->pluck('badges.id')->toArray();

        return $allBadges->map(function ($badge) use ($earnedBadges, $organization) {
            $isEarned = in_array($badge->id, $earnedBadges);
            $pivot = $isEarned
                ? $organization->badges()->where('badges.id', $badge->id)->first()?->pivot
                : null;

            return [
                'badge' => $badge,
                'earned' => $isEarned,
                'earned_at' => $pivot?->earned_at,
                'share_token' => $pivot?->share_token,
                'progress' => $isEarned ? 100 : $this->calculateProgress($organization, $badge->code),
            ];
        });
    }

    /**
     * Calcule le score de gamification d'une organisation.
     */
    public function calculateOrganizationScore(Organization $organization): array
    {
        $badges = $organization->badges;
        $totalPoints = $badges->sum('points');
        $badgeCount = $badges->count();

        $level = $this->calculateLevel($totalPoints);

        return [
            'total_points' => $totalPoints,
            'badge_count' => $badgeCount,
            'level' => $level,
            'level_name' => $this->getLevelName($level),
            'next_level_points' => $this->getNextLevelPoints($level),
            'progress_to_next' => $this->getProgressToNextLevel($totalPoints, $level),
        ];
    }

    /**
     * Obtient le classement des organisations.
     *
     * @return Collection<int, array>
     */
    public function getLeaderboard(int $limit = 10): Collection
    {
        return Organization::select('organizations.id', 'organizations.name')
            ->leftJoin('organization_badges', 'organizations.id', '=', 'organization_badges.organization_id')
            ->leftJoin('badges', 'organization_badges.badge_id', '=', 'badges.id')
            ->groupBy('organizations.id', 'organizations.name')
            ->selectRaw('COALESCE(SUM(badges.points), 0) as total_points')
            ->selectRaw('COUNT(DISTINCT organization_badges.badge_id) as badge_count')
            ->orderByDesc('total_points')
            ->limit($limit)
            ->get()
            ->map(fn ($org, $index) => [
                'rank' => $index + 1,
                'organization_id' => $org->id,
                'organization_name' => $org->name,
                'total_points' => (int) $org->total_points,
                'badge_count' => (int) $org->badge_count,
                'level' => $this->calculateLevel((int) $org->total_points),
            ]);
    }

    /**
     * Génère un lien de partage pour un badge.
     */
    public function getShareUrl(Organization $organization, Badge $badge): ?string
    {
        $pivot = $organization->badges()->where('badges.id', $badge->id)->first()?->pivot;

        if (! $pivot || ! $pivot->share_token) {
            return null;
        }

        return route('badges.share', ['token' => $pivot->share_token]);
    }

    // ========================================
    // Méthodes de vérification des critères
    // ========================================

    /**
     * Vérifie le badge "Premier bilan carbone".
     */
    protected function checkFirstAssessment(Organization $organization): bool
    {
        return Assessment::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Vérifie le badge "5 bilans réalisés".
     */
    protected function checkFiveAssessments(Organization $organization): bool
    {
        return Assessment::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->count() >= 5;
    }

    /**
     * Vérifie le badge "Réduction carbone X%".
     */
    protected function checkCarbonReducer(Organization $organization, int $targetPercent): bool
    {
        $assessments = Assessment::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->orderBy('year')
            ->take(2)
            ->get();

        if ($assessments->count() < 2) {
            return false;
        }

        $oldest = $assessments->first();
        $newest = $assessments->last();

        if ($oldest->total_emissions <= 0) {
            return false;
        }

        $reduction = (($oldest->total_emissions - $newest->total_emissions) / $oldest->total_emissions) * 100;

        return $reduction >= $targetPercent;
    }

    /**
     * Vérifie le badge "Champion Scope 3".
     */
    protected function checkScope3Champion(Organization $organization): bool
    {
        // Doit avoir au moins 80% du Scope 3 documenté
        $latestAssessment = Assessment::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->orderByDesc('year')
            ->first();

        if (! $latestAssessment) {
            return false;
        }

        $scope3Categories = ['3.1', '3.2', '3.3', '3.5', '4.1', '4.2', '4.3', '4.4', '4.5'];
        $documentedCategories = $latestAssessment->emissionRecords()
            ->whereIn('category_code', $scope3Categories)
            ->where('status', 'completed')
            ->distinct('category_code')
            ->count();

        return $documentedCategories >= 7; // Au moins 7 sur 9 catégories
    }

    /**
     * Vérifie le badge "Qualité des données".
     */
    protected function checkDataQuality(Organization $organization): bool
    {
        $latestAssessment = Assessment::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->orderByDesc('year')
            ->first();

        if (! $latestAssessment) {
            return false;
        }

        // Au moins 80% des données de haute qualité (primary data)
        $totalRecords = $latestAssessment->emissionRecords()->where('status', 'completed')->count();
        $primaryDataRecords = $latestAssessment->emissionRecords()
            ->where('status', 'completed')
            ->where('data_quality', 'primary')
            ->count();

        if ($totalRecords === 0) {
            return false;
        }

        return ($primaryDataRecords / $totalRecords) >= 0.8;
    }

    /**
     * Vérifie le badge "Aligné SBTi".
     */
    protected function checkSbtiAligned(Organization $organization): bool
    {
        return ReductionTarget::where('organization_id', $organization->id)
            ->where('is_sbti_aligned', true)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Vérifie le badge "Fournisseurs engagés".
     */
    protected function checkSupplierEngaged(Organization $organization): bool
    {
        // Au moins 5 fournisseurs avec données carbone
        return Supplier::where('organization_id', $organization->id)
            ->withEmissionData()
            ->count() >= 5;
    }

    /**
     * Calcule la progression vers un badge.
     */
    protected function calculateProgress(Organization $organization, string $badgeCode): int
    {
        return match ($badgeCode) {
            Badge::FIRST_ASSESSMENT => Assessment::where('organization_id', $organization->id)
                ->where('status', 'completed')->exists() ? 100 : 0,

            Badge::FIVE_ASSESSMENTS => min(100, (Assessment::where('organization_id', $organization->id)
                ->where('status', 'completed')->count() / 5) * 100),

            Badge::CARBON_REDUCER_10, Badge::CARBON_REDUCER_25 => $this->calculateReductionProgress($organization, $badgeCode),

            Badge::SCOPE3_CHAMPION => $this->calculateScope3Progress($organization),

            Badge::DATA_QUALITY => $this->calculateDataQualityProgress($organization),

            Badge::SBTI_ALIGNED => ReductionTarget::where('organization_id', $organization->id)
                ->where('is_sbti_aligned', true)->exists() ? 100 : 0,

            Badge::SUPPLIER_ENGAGED => min(100, (Supplier::where('organization_id', $organization->id)
                ->withEmissionData()->count() / 5) * 100),

            default => 0,
        };
    }

    protected function calculateReductionProgress(Organization $organization, string $badgeCode): int
    {
        $targetPercent = $badgeCode === Badge::CARBON_REDUCER_10 ? 10 : 25;

        $assessments = Assessment::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->orderBy('year')
            ->take(2)
            ->get();

        if ($assessments->count() < 2) {
            return $assessments->count() * 25; // 25% pour avoir commencé
        }

        $oldest = $assessments->first();
        $newest = $assessments->last();

        if ($oldest->total_emissions <= 0) {
            return 50;
        }

        $reduction = (($oldest->total_emissions - $newest->total_emissions) / $oldest->total_emissions) * 100;

        return min(100, (int) (($reduction / $targetPercent) * 100));
    }

    protected function calculateScope3Progress(Organization $organization): int
    {
        $latestAssessment = Assessment::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->orderByDesc('year')
            ->first();

        if (! $latestAssessment) {
            return 0;
        }

        $scope3Categories = ['3.1', '3.2', '3.3', '3.5', '4.1', '4.2', '4.3', '4.4', '4.5'];
        $documentedCategories = $latestAssessment->emissionRecords()
            ->whereIn('category_code', $scope3Categories)
            ->where('status', 'completed')
            ->distinct('category_code')
            ->count();

        return min(100, (int) (($documentedCategories / 7) * 100));
    }

    protected function calculateDataQualityProgress(Organization $organization): int
    {
        $latestAssessment = Assessment::where('organization_id', $organization->id)
            ->where('status', 'completed')
            ->orderByDesc('year')
            ->first();

        if (! $latestAssessment) {
            return 0;
        }

        $totalRecords = $latestAssessment->emissionRecords()->where('status', 'completed')->count();
        $primaryDataRecords = $latestAssessment->emissionRecords()
            ->where('status', 'completed')
            ->where('data_quality', 'primary')
            ->count();

        if ($totalRecords === 0) {
            return 0;
        }

        $ratio = $primaryDataRecords / $totalRecords;

        return min(100, (int) (($ratio / 0.8) * 100));
    }

    /**
     * Calcule le niveau basé sur les points.
     */
    protected function calculateLevel(int $points): int
    {
        return match (true) {
            $points >= 1000 => 5,
            $points >= 500 => 4,
            $points >= 250 => 3,
            $points >= 100 => 2,
            $points >= 25 => 1,
            default => 0,
        };
    }

    /**
     * Obtient le nom du niveau.
     */
    protected function getLevelName(int $level): string
    {
        return match ($level) {
            5 => __('carbex.gamification.level.champion'),
            4 => __('carbex.gamification.level.expert'),
            3 => __('carbex.gamification.level.advanced'),
            2 => __('carbex.gamification.level.intermediate'),
            1 => __('carbex.gamification.level.beginner'),
            default => __('carbex.gamification.level.starter'),
        };
    }

    /**
     * Obtient les points requis pour le niveau suivant.
     */
    protected function getNextLevelPoints(int $currentLevel): int
    {
        return match ($currentLevel) {
            0 => 25,
            1 => 100,
            2 => 250,
            3 => 500,
            4 => 1000,
            default => 0,
        };
    }

    /**
     * Calcule la progression vers le niveau suivant.
     */
    protected function getProgressToNextLevel(int $points, int $currentLevel): int
    {
        $nextLevelPoints = $this->getNextLevelPoints($currentLevel);

        if ($nextLevelPoints === 0) {
            return 100;
        }

        $previousLevelPoints = match ($currentLevel) {
            1 => 25,
            2 => 100,
            3 => 250,
            4 => 500,
            default => 0,
        };

        $pointsInLevel = $points - $previousLevelPoints;
        $pointsNeeded = $nextLevelPoints - $previousLevelPoints;

        return min(100, (int) (($pointsInLevel / $pointsNeeded) * 100));
    }
}
