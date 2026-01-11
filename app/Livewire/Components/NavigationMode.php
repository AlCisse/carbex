<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * NavigationMode Component
 *
 * Allows toggling between standard navigation and 5-pillar navigation.
 *
 * Tasks T166 - Phase 10 (TrackZero Features)
 */
class NavigationMode extends Component
{
    public string $mode = 'standard'; // standard or pillars

    public function mount(): void
    {
        $this->mode = session('navigation_mode', 'standard');
    }

    public function setMode(string $mode): void
    {
        if (in_array($mode, ['standard', 'pillars'])) {
            $this->mode = $mode;
            session(['navigation_mode' => $mode]);

            $this->dispatch('navigation-mode-changed', mode: $mode);
        }
    }

    public function render()
    {
        return view('livewire.components.navigation-mode');
    }
}
