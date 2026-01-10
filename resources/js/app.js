import './bootstrap';

import ApexCharts from 'apexcharts';
import collapse from '@alpinejs/collapse';

// Make ApexCharts available globally for Alpine.js components
window.ApexCharts = ApexCharts;

// Register Alpine plugins via Livewire's Alpine instance
// Livewire 3 manages Alpine.js, so we don't start it manually
document.addEventListener('livewire:init', () => {
    if (window.Alpine) {
        window.Alpine.plugin(collapse);
    }
});

// For pages without Livewire, check if Alpine is available after a delay
document.addEventListener('DOMContentLoaded', () => {
    // If Livewire hasn't initialized Alpine, do it manually
    setTimeout(() => {
        if (!window.Alpine) {
            import('alpinejs').then((Alpine) => {
                window.Alpine = Alpine.default;
                Alpine.default.plugin(collapse);
                Alpine.default.start();
            });
        }
    }, 100);
});
