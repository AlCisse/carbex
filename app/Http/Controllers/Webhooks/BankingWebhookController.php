<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\SyncBankTransactions;
use App\Models\BankConnection;
use App\Notifications\BankConnectionExpired;
use App\Services\Banking\BridgeService;
use App\Services\Banking\FinapiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BankingWebhookController extends Controller
{
    /**
     * Handle Bridge.io webhooks.
     */
    public function bridge(Request $request, BridgeService $bridgeService): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Bridge-Signature', '');

        // Verify signature
        if (! $bridgeService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('BankingWebhook: Invalid Bridge signature');

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();
        $eventType = $data['type'] ?? null;

        Log::info('BankingWebhook: Bridge event received', [
            'type' => $eventType,
            'item_id' => $data['content']['item_id'] ?? null,
        ]);

        return match ($eventType) {
            'item.created' => $this->handleItemCreated($data),
            'item.refreshed' => $this->handleItemRefreshed($data),
            'item.status_changed' => $this->handleItemStatusChanged($data),
            'account.created' => $this->handleAccountCreated($data),
            'transaction.created' => $this->handleTransactionsCreated($data),
            'item.deleted' => $this->handleItemDeleted($data),
            default => response()->json(['status' => 'ignored']),
        };
    }

    /**
     * Handle Finapi webhooks.
     *
     * SECURITY: Verifies webhook signature before processing.
     */
    public function finapi(Request $request, FinapiService $finapiService): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Finapi-Signature', '');

        // SECURITY: Verify webhook signature to prevent unauthorized data injection
        if (! $finapiService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('BankingWebhook: Invalid Finapi signature', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();
        $eventType = $data['callbackType'] ?? null;

        Log::info('BankingWebhook: Finapi event received', [
            'type' => $eventType,
        ]);

        return match ($eventType) {
            'NEW_TRANSACTIONS' => $this->handleFinapiNewTransactions($data),
            'BANK_CONNECTION_UPDATE' => $this->handleFinapiBankConnectionUpdate($data),
            default => response()->json(['status' => 'ignored']),
        };
    }

    /**
     * Handle item created (new bank connection).
     */
    private function handleItemCreated(array $data): JsonResponse
    {
        $itemId = $data['content']['item_id'] ?? null;

        if (! $itemId) {
            return response()->json(['error' => 'Missing item_id'], 400);
        }

        $connection = BankConnection::where('provider', 'bridge')
            ->where('provider_item_id', (string) $itemId)
            ->first();

        if ($connection) {
            // Trigger initial sync
            SyncBankTransactions::dispatch($connection);
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle item refreshed (new data available).
     */
    private function handleItemRefreshed(array $data): JsonResponse
    {
        $itemId = $data['content']['item_id'] ?? null;

        if (! $itemId) {
            return response()->json(['error' => 'Missing item_id'], 400);
        }

        $connection = BankConnection::where('provider', 'bridge')
            ->where('provider_item_id', (string) $itemId)
            ->first();

        if ($connection) {
            SyncBankTransactions::dispatch($connection);
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle item status changed.
     */
    private function handleItemStatusChanged(array $data): JsonResponse
    {
        $itemId = $data['content']['item_id'] ?? null;
        $status = $data['content']['status'] ?? null;

        if (! $itemId) {
            return response()->json(['error' => 'Missing item_id'], 400);
        }

        $connection = BankConnection::where('provider', 'bridge')
            ->where('provider_item_id', (string) $itemId)
            ->first();

        if (! $connection) {
            return response()->json(['status' => 'connection_not_found']);
        }

        // Map Bridge status to our status
        $newStatus = match ($status) {
            0 => 'active',      // OK
            402 => 'inactive',  // Pro subscription required
            429 => 'error',     // Too many requests
            430 => 'error',     // SCA required
            1000 => 'error',    // Login failed
            1010 => 'error',    // Account not activated
            default => 'error',
        };

        $connection->update([
            'status' => $newStatus,
            'error_message' => $status !== 0 ? "Status code: {$status}" : null,
        ]);

        // Notify user if connection expired/failed
        if (in_array($newStatus, ['inactive', 'error'])) {
            $user = $connection->organization?->owner;
            if ($user) {
                $user->notify(new BankConnectionExpired($connection));
            }
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle account created.
     */
    private function handleAccountCreated(array $data): JsonResponse
    {
        // Usually handled automatically during sync
        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle transactions created.
     */
    private function handleTransactionsCreated(array $data): JsonResponse
    {
        $itemId = $data['content']['item_id'] ?? null;

        if (! $itemId) {
            return response()->json(['error' => 'Missing item_id'], 400);
        }

        $connection = BankConnection::where('provider', 'bridge')
            ->where('provider_item_id', (string) $itemId)
            ->first();

        if ($connection) {
            // Sync only new transactions
            SyncBankTransactions::dispatch($connection)
                ->delay(now()->addSeconds(5));
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle item deleted.
     */
    private function handleItemDeleted(array $data): JsonResponse
    {
        $itemId = $data['content']['item_id'] ?? null;

        if (! $itemId) {
            return response()->json(['error' => 'Missing item_id'], 400);
        }

        $connection = BankConnection::where('provider', 'bridge')
            ->where('provider_item_id', (string) $itemId)
            ->first();

        if ($connection) {
            $connection->update(['status' => 'disconnected']);
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle Finapi new transactions.
     */
    private function handleFinapiNewTransactions(array $data): JsonResponse
    {
        $bankConnectionIds = $data['bankConnectionIds'] ?? [];

        foreach ($bankConnectionIds as $connectionId) {
            $connection = BankConnection::where('provider', 'finapi')
                ->where('provider_item_id', (string) $connectionId)
                ->first();

            if ($connection) {
                SyncBankTransactions::dispatch($connection);
            }
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * Handle Finapi bank connection update.
     */
    private function handleFinapiBankConnectionUpdate(array $data): JsonResponse
    {
        $connectionId = $data['bankConnectionId'] ?? null;
        $status = $data['status'] ?? null;

        if (! $connectionId) {
            return response()->json(['error' => 'Missing connectionId'], 400);
        }

        $connection = BankConnection::where('provider', 'finapi')
            ->where('provider_item_id', (string) $connectionId)
            ->first();

        if ($connection) {
            $newStatus = match (strtoupper($status ?? '')) {
                'COMPLETED', 'UPDATED' => 'active',
                'DISABLED' => 'inactive',
                default => 'error',
            };

            $connection->update(['status' => $newStatus]);
        }

        return response()->json(['status' => 'processed']);
    }
}
