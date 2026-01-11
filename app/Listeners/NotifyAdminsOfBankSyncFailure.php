<?php

namespace App\Listeners;

use App\Events\BankSyncFailed;
use App\Models\User;
use App\Notifications\BankSyncFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminsOfBankSyncFailure implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = 'notifications';

    /**
     * Handle the event.
     */
    public function handle(BankSyncFailed $event): void
    {
        // Get organization admins
        $admins = User::where('organization_id', $event->organization->id)
            ->where(function ($query) {
                $query->where('role', 'owner')
                    ->orWhere('role', 'admin');
            })
            ->get();

        // Notify each admin
        foreach ($admins as $admin) {
            $admin->notify(new BankSyncFailedNotification(
                $event->connection,
                $event->errorMessage,
                $event->requiresReauth
            ));
        }
    }
}
