<?php

namespace App\Jobs;

use App\Models\SupplierRequest;
use App\Notifications\SupplierReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendSupplierReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        // Find pending supplier requests that haven't been reminded recently
        $pendingRequests = SupplierRequest::where('status', 'pending')
            ->where('created_at', '<', now()->subDays(7))
            ->where(function ($query) {
                $query->whereNull('last_reminder_at')
                    ->orWhere('last_reminder_at', '<', now()->subDays(7));
            })
            ->where('reminder_count', '<', 3)
            ->get();

        Log::info('SendSupplierReminders: Sending reminders', [
            'count' => $pendingRequests->count(),
        ]);

        foreach ($pendingRequests as $request) {
            try {
                Notification::route('mail', $request->supplier_email)
                    ->notify(new SupplierReminderNotification($request));

                $request->update([
                    'last_reminder_at' => now(),
                    'reminder_count' => $request->reminder_count + 1,
                ]);
            } catch (\Exception $e) {
                Log::error('SendSupplierReminders: Failed to send reminder', [
                    'request_id' => $request->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
