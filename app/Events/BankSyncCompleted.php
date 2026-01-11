<?php

namespace App\Events;

use App\Models\BankConnection;
use App\Models\Organization;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BankSyncCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public BankConnection $connection,
        public Organization $organization,
        public int $newTransactions,
        public int $updatedTransactions = 0,
        public array $accountsSynced = []
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('organization.' . $this->organization->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'bank.sync_completed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'connection_id' => $this->connection->id,
            'bank_name' => $this->connection->bank_name,
            'new_transactions' => $this->newTransactions,
            'updated_transactions' => $this->updatedTransactions,
            'accounts_synced' => count($this->accountsSynced),
            'synced_at' => now()->toISOString(),
        ];
    }

    /**
     * Get webhook payload for external integrations.
     */
    public function toWebhook(): array
    {
        return [
            'event' => 'bank.sync_completed',
            'timestamp' => now()->toISOString(),
            'data' => [
                'connection' => [
                    'id' => $this->connection->id,
                    'bank_name' => $this->connection->bank_name,
                    'provider' => $this->connection->provider,
                ],
                'sync_results' => [
                    'new_transactions' => $this->newTransactions,
                    'updated_transactions' => $this->updatedTransactions,
                    'accounts_synced' => $this->accountsSynced,
                ],
                'organization_id' => $this->organization->id,
            ],
        ];
    }
}
