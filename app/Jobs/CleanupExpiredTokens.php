<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupExpiredTokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        // Clean expired personal access tokens (Sanctum)
        $deletedTokens = DB::table('personal_access_tokens')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        // Clean expired password reset tokens
        $deletedResets = DB::table('password_reset_tokens')
            ->where('created_at', '<', now()->subHours(24))
            ->delete();

        // Clean expired organization invitations
        $deletedInvitations = DB::table('organization_invitations')
            ->where('expires_at', '<', now())
            ->delete();

        Log::info('CleanupExpiredTokens: Cleanup completed', [
            'tokens' => $deletedTokens,
            'password_resets' => $deletedResets,
            'invitations' => $deletedInvitations,
        ]);
    }
}
