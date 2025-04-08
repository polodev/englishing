// Import Livewire with Alpine
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Add navigate function for Livewire compatibility
Alpine.navigate = (url) => {
    window.location.href = url;
};

// Make Alpine globally available
window.Alpine = Alpine;

// Import navigation functionality
import './navigation.js';

// Log initialization for debugging
console.log('Alpine initialized from Livewire');
