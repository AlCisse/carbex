<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * BadgeShareController
 *
 * Gère l'affichage public des badges partagés.
 *
 * Constitution LinsCarbon v3.0 - Section 9.9 (Gamification)
 * Tasks T171 - Phase 10 (TrackZero Features)
 */
class BadgeShareController extends Controller
{
    /**
     * Affiche un badge partagé publiquement.
     */
    public function show(Request $request, string $token)
    {
        $pivot = DB::table('organization_badges')
            ->where('share_token', $token)
            ->first();

        if (! $pivot) {
            abort(404);
        }

        $badge = DB::table('badges')->where('id', $pivot->badge_id)->first();
        $organization = DB::table('organizations')->where('id', $pivot->organization_id)->first();

        if (! $badge || ! $organization) {
            abort(404);
        }

        // Get translated name based on locale
        $locale = app()->getLocale();
        $badgeName = match ($locale) {
            'en' => $badge->name_en ?? $badge->name,
            'de' => $badge->name_de ?? $badge->name,
            default => $badge->name,
        };
        $badgeDescription = match ($locale) {
            'en' => $badge->description_en ?? $badge->description,
            'de' => $badge->description_de ?? $badge->description,
            default => $badge->description,
        };

        $viewData = [
            'badge' => $badge,
            'badgeName' => $badgeName,
            'badgeDescription' => $badgeDescription,
            'organization' => $organization,
            'earned_at' => Carbon::parse($pivot->earned_at),
            'isEmbed' => $request->has('embed'),
            'share_token' => $token,
        ];

        // Return embed view for iframe usage
        if ($request->has('embed')) {
            return view('badges.embed', $viewData);
        }

        return view('badges.public', $viewData);
    }

    /**
     * Verify badge authenticity (API endpoint).
     */
    public function verify(string $token)
    {
        $pivot = DB::table('organization_badges')
            ->where('share_token', $token)
            ->first();

        if (! $pivot) {
            return response()->json([
                'valid' => false,
                'message' => 'Badge not found',
            ], 404);
        }

        $badge = DB::table('badges')->where('id', $pivot->badge_id)->first();
        $organization = DB::table('organizations')
            ->select('name', 'country')
            ->where('id', $pivot->organization_id)
            ->first();

        return response()->json([
            'valid' => true,
            'badge' => [
                'code' => $badge->code,
                'name' => $badge->name,
                'category' => $badge->category,
            ],
            'organization' => [
                'name' => $organization->name,
                'country' => $organization->country,
            ],
            'earned_at' => $pivot->earned_at,
            'verified_by' => 'LinsCarbon',
        ]);
    }
}
