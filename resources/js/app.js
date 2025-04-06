// Import Alpine.js and plugins
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import { Livewire, Alpine as LivewireAlpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Configure Alpine.js
Alpine.plugin(persist);

// Add navigate function for Livewire compatibility
Alpine.navigate = (url) => {
    window.location.href = url;
};

// Make Alpine globally available
window.Alpine = Alpine;

// Initialize Livewire with Alpine.js
Livewire.start();

// Initialize Alpine.js after Livewire
Alpine.start();

// Import navigation functionality
import './navigation.js';

// Log initialization for debugging
console.log('Livewire and Alpine.js initialized');
