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

// Import Ace Editor
import ace from 'ace-builds/src-noconflict/ace';
import 'ace-builds/src-noconflict/mode-json';
import 'ace-builds/src-noconflict/theme-dracula';
import 'ace-builds/src-noconflict/keybinding-vim';
import 'ace-builds/src-noconflict/ext-language_tools';

// Make Ace globally available
window.ace = ace;
// Livewire.start();

// Configure Ace
ace.require('ace/ext/language_tools');

// Listen for the json-editor-update custom event
document.addEventListener('livewire:initialized', () => {
    window.addEventListener('json-editor-update', (event) => {
        const { editorId, content } = event.detail;
        const editor = ace.edit(editorId);
        if (editor) {
            editor.setValue(content);
            editor.clearSelection();
        }
    });

    // Listen for toast events from Livewire
    Livewire.on('toast', (data) => {
        window.dispatchEvent(new CustomEvent('new-toast', {
            detail: data
        }));
    });
});
