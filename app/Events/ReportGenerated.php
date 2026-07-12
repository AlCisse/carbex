<?php

namespace App\Events;

use App\Models\Report;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Report $report,
        public Organization $organization,
        public ?User $generatedBy = null,
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
        $channels = [
            new PrivateChannel('organization.' . $this->organization->id),
        ];

        // Also broadcast to the specific user who generated the report
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
        return 'report.generated';
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
            'period_start' => $this->report->period_start?->toISOString(),
            'period_end' => $this->report->period_end?->toISOString(),
            'status' => $this->report->status,
            'file_path' => $this->report->file_path,
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get webhook payload for external integrations.
     */
    public function toWebhook(): array
    {
        return [
            'event' => 'report.generated',
            'timestamp' => now()->toISOString(),
            'data' => [
                'report' => [
                    'id' => $this->report->id,
                    'type' => $this->report->type,
                    'name' => $this->report->name,
                    'period_start' => $this->report->period_start?->format('Y-m-d'),
                    'period_end' => $this->report->period_end?->format('Y-m-d'),
                    'status' => $this->report->status,
                    'total_emissions_kg' => $this->report->total_emissions_kg ?? null,
                    'download_url' => $this->getDownloadUrl(),
                ],
                'organization_id' => $this->organization->id,
                'generated_by' => $this->generatedBy?->id,
            ],
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Get the report download URL.
     */
    protected function getDownloadUrl(): ?string
    {
        if (!$this->report->file_path) {
            return null;
        }

        return url("/api/v1/reports/{$this->report->id}/download");
    }
}
