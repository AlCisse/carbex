<?php

namespace App\Events;

use App\Models\BankConnection;
use App\Models\Organization;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BankSyncFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public BankConnection $connection,
        public Organization $organization,
        public string $errorMessage,
        public ?string $errorCode = null,
        public bool $requiresReauth = false
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
        return 'bank.sync_failed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'connection_id' => $this->connection->id,
            'bank_name' => $this->connection->bank_name,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'requires_reauth' => $this->requiresReauth,
            'failed_at' => now()->toISOString(),
        ];
    }

    /**
     * Get webhook payload for external integrations.
     */
    public function toWebhook(): array
    {
        return [
            'event' => 'bank.sync_failed',
            'timestamp' => now()->toISOString(),
            'data' => [
                'connection' => [
                    'id' => $this->connection->id,
                    'bank_name' => $this->connection->bank_name,
                    'provider' => $this->connection->provider,
                ],
                'error' => [
                    'code' => $this->errorCode,
                    'message' => $this->errorMessage,
                    'requires_reauth' => $this->requiresReauth,
                ],
                'organization_id' => $this->organization->id,
            ],
        ];
    }
}
