<?php

namespace App\Events;

use App\Models\Report;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Report $report,
        public Organization $organization,
        public string $errorMessage,
        public ?string $errorCode = null,
        public ?User $generatedBy = null
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('organization.' . $this->organization->id),
        ];

        if ($this->generatedBy) {
            $channels[] = new PrivateChannel('user.' . $this->generatedBy->id);
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'report.failed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'report_id' => $this->report->id,
            'type' => $this->report->type,
            'name' => $this->report->name,
            'error_message' => $this->errorMessage,
            'error_code' => $this->errorCode,
            'failed_at' => now()->toISOString(),
        ];
    }

    /**
     * Get webhook payload for external integrations.
     */
    public function toWebhook(): array
    {
        return [
            'event' => 'report.failed',
            'timestamp' => now()->toISOString(),
            'data' => [
                'report' => [
                    'id' => $this->report->id,
                    'type' => $this->report->type,
                    'name' => $this->report->name,
                ],
                'error' => [
                    'code' => $this->errorCode,
                    'message' => $this->errorMessage,
                ],
                'organization_id' => $this->organization->id,
                'generated_by' => $this->generatedBy?->id,
            ],
        ];
    }
}
