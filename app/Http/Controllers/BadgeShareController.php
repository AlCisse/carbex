<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * BadgeShareController
 *
 * Gère l'affichage public des badges partagés.
 *
 * Constitution Carbex v3.0 - Section 9.9 (Gamification)
 */
class BadgeShareController extends Controller
{
    /**
     * Affiche un badge partagé publiquement.
     */
    public function show(string $token)
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

        return view('gamification.share', [
            'badge' => $badge,
            'organization' => $organization,
            'earned_at' => $pivot->earned_at,
        ]);
    }
}
