<?php

namespace App\Livewire\Dashboard;

use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * TrainingSection - Educational videos and resources
 *
 * Constitution LinsCarbon v3.0 - Section 3.2, T056
 *
 * Displays training videos:
 * - How to define your carbon footprint
 * - Setting up your account
 * - Defining reduction targets
 */
class TrainingSection extends Component
{
    public bool $expanded = false;

    public function toggleExpanded(): void
    {
        $this->expanded = ! $this->expanded;
    }

    public function getVideosProperty(): array
    {
        return [
            [
                'id' => 'carbon-basics',
                'title' => __('linscarbon.training.video1_title'),
                'description' => __('linscarbon.training.video1_desc'),
                'youtube_id' => 'dQw4w9WgXcQ', // Placeholder - replace with actual video
                'duration' => '5:30',
                'category' => 'basics',
            ],
            [
                'id' => 'account-setup',
                'title' => __('linscarbon.training.video2_title'),
                'description' => __('linscarbon.training.video2_desc'),
                'youtube_id' => 'dQw4w9WgXcQ', // Placeholder - replace with actual video
                'duration' => '3:45',
                'category' => 'setup',
            ],
            [
                'id' => 'reduction-targets',
                'title' => __('linscarbon.training.video3_title'),
                'description' => __('linscarbon.training.video3_desc'),
                'youtube_id' => 'dQw4w9WgXcQ', // Placeholder - replace with actual video
                'duration' => '7:15',
                'category' => 'advanced',
            ],
        ];
    }

    public function render(): View
    {
        return view('livewire.dashboard.training-section', [
            'videos' => $this->videos,
        ]);
    }
}
