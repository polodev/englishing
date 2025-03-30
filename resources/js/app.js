import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';

// Create a custom navigation plugin for Alpine.js
Alpine.plugin(persist);

// Add navigate function for Livewire compatibility
Alpine.navigate = (url) => {
    window.location.href = url;
};

window.Alpine = Alpine;

// Initialize Alpine.js
Alpine.start();

// Import navigation functionality
import './navigation.js';

// Alpine.js debug helper removed
