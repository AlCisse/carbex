<?php

namespace App\Events;

use App\Models\Emission;
use App\Models\Organization;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmissionCalculated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Emission $emission,
        public Organization $organization,
        public array $metadata = []
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
        return 'emission.calculated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'emission_id' => $this->emission->id,
            'scope' => $this->emission->scope,
            'category' => $this->emission->category,
            'co2_kg' => $this->emission->co2_kg,
            'date' => $this->emission->date->toISOString(),
            'site_id' => $this->emission->site_id,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Determine if this event should broadcast.
     */
    public function broadcastWhen(): bool
    {
        // Only broadcast for significant emissions (> 1kg CO2)
        return $this->emission->co2_kg > 1;
    }

    /**
     * Get webhook payload for external integrations.
     */
    public function toWebhook(): array
    {
        return [
            'event' => 'emission.calculated',
            'timestamp' => now()->toISOString(),
            'data' => [
                'emission' => [
                    'id' => $this->emission->id,
                    'scope' => $this->emission->scope,
                    'category' => $this->emission->category,
                    'subcategory' => $this->emission->subcategory,
                    'co2_kg' => round($this->emission->co2_kg, 4),
                    'co2_tonnes' => round($this->emission->co2_kg / 1000, 6),
                    'date' => $this->emission->date->format('Y-m-d'),
                    'source' => $this->emission->source,
                ],
                'organization_id' => $this->organization->id,
                'site_id' => $this->emission->site_id,
            ],
            'metadata' => $this->metadata,
        ];
    }
}
