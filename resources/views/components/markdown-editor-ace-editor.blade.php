@props([
    'id' => 'markdown-editor',
    'name' => null,
    'value' => '',
    'placeholder' => 'Write your content here...',
    'required' => false,
    'wireModel' => null
])

<div class="markdown-editor-container">
    <div class="border border-gray-300 dark:border-gray-700 rounded-md overflow-hidden">
        <!-- Editor Toolbar -->
        <div id="toolbar-{{ $id }}" class="flex flex-wrap items-center justify-between p-2 bg-gray-100 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
            <div class="flex flex-wrap items-center space-x-1 mb-1 md:mb-0">
                <button type="button" onclick="insertMarkdown_{{ $id }}('bold')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Bold">
                    <strong>B</strong>
                </button>
                <button type="button" onclick="insertMarkdown_{{ $id }}('italic')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Italic">
                    <em>I</em>
                </button>
                <button type="button" onclick="insertMarkdown_{{ $id }}('heading')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Heading">
                    H
                </button>
                <span class="mx-1 text-gray-400">|</span>
                <button type="button" onclick="insertMarkdown_{{ $id }}('link')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Link">
                    🔗
                </button>
                <button type="button" onclick="insertMarkdown_{{ $id }}('image')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Image">
                    🖼️
                </button>
                <button type="button" onclick="insertMarkdown_{{ $id }}('code')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Code">
                    &lt;/&gt;
                </button>
                <span class="mx-1 text-gray-400">|</span>
                <button type="button" onclick="insertMarkdown_{{ $id }}('quote')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Quote">
                    💬
                </button>
                <button type="button" onclick="insertMarkdown_{{ $id }}('ul')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Unordered List">
                    •
                </button>
                <button type="button" onclick="insertMarkdown_{{ $id }}('ol')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Ordered List">
                    1.
                </button>
                <button type="button" onclick="insertMarkdown_{{ $id }}('hr')" class="p-1 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded" title="Horizontal Rule">
                    —
                </button>
            </div>
            <div class="flex items-center space-x-1">
                <button
                    type="button"
                    onclick="toggleVimMode_{{ $id }}()"
                    id="vim-toggle-{{ $id }}"
                    class="px-2 py-1 text-xs font-medium text-white bg-gray-600 rounded hover:bg-gray-700 focus:outline-none"
                >
                    Vim Mode
                </button>
                <button
                    type="button"
                    onclick="togglePreview_{{ $id }}()"
                    id="preview-toggle-{{ $id }}"
                    class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none"
                >
                    Show Preview
                </button>
                <button
                    type="button"
                    onclick="toggleFullscreen_{{ $id }}()"
                    id="fullscreen-toggle-{{ $id }}"
                    class="px-2 py-1 text-xs font-medium text-white bg-gray-600 rounded hover:bg-gray-700 focus:outline-none"
                >
                    Fullscreen
                </button>
                <button
                    type="button"
                    onclick="toggleTextarea_{{ $id }}()"
                    id="textarea-toggle-{{ $id }}"
                    class="px-2 py-1 text-xs font-medium text-white bg-purple-600 rounded hover:bg-purple-700 focus:outline-none"
                >
                    Use Textarea
                </button>
            </div>
        </div>

        <div id="editor-container-{{ $id }}" class="editor-preview-container-{{ $id }} flex flex-col md:flex-row">
            <!-- Ace Editor Container -->
            <div 
                id="ace-editor-{{ $id }}" 
                class="w-full"
                style="height: 450px; min-height: 450px; position: relative;"
            >{{ $value }}</div>

            <!-- Textarea Container (hidden by default) -->
            <textarea
                id="textarea-editor-{{ $id }}"
                class="w-full p-4 font-mono text-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hidden"
                style="height: 450px; min-height: 450px; resize: none;"
            >{{ $value }}</textarea>

            <!-- Preview Pane -->
            <div
                id="preview-{{ $id }}"
                class="w-full md:w-1/2 p-4 overflow-auto border-t md:border-t-0 md:border-l border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 prose dark:prose-invert max-w-none hidden"
                style="height: 450px; min-height: 450px;"
            ></div>
        </div>

        <!-- Hidden Input for Form Submission -->
        <input
            type="hidden"
            id="{{ $id }}"
            name="{{ $name ?? $id }}"
            {{ $required ? 'required' : '' }}
            @if($wireModel)
                wire:model{{ Str::startsWith($wireModel, ['defer:', 'live:', 'lazy:']) ? '' : '.defer' }}="{{ $wireModel }}"
            @endif
            value="{{ $value }}"
        >
    </div>
</div>

<!-- Load Ace Editor from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/ace.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/mode-markdown.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.23.4/keybinding-vim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/marked@4.3.0/marked.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize editor after a short delay to ensure DOM is ready
    setTimeout(function() {
        initAceEditor_{{ $id }}();
    }, 500); // Increased delay for better initialization
});

// Global variables
var editor_{{ $id }} = null;
var isVimMode_{{ $id }} = false;
var isFullscreen_{{ $id }} = false;
var isTextareaMode_{{ $id }} = false;
var originalParent_{{ $id }} = null;
var fullscreenOverlay_{{ $id }} = null;

// Initialize Ace Editor
function initAceEditor_{{ $id }}() {
    try {
        // Get editor container
        var editorContainer = document.getElementById('ace-editor-{{ $id }}');
        if (!editorContainer) {
            console.error('Editor container not found');
            return;
        }
        
        // Make sure Ace is loaded
        if (typeof ace === 'undefined') {
            console.error('Ace Editor not loaded');
            setTimeout(initAceEditor_{{ $id }}, 500); // Try again after a delay
            return;
        }
        
        // Initialize Ace Editor
        editor_{{ $id }} = ace.edit(editorContainer, {
            theme: 'ace/theme/github',
            mode: 'ace/mode/markdown',
            enableBasicAutocompletion: true,
            enableSnippets: true,
            enableLiveAutocompletion: true,
            fontSize: '14px',
            wrap: true,
            showPrintMargin: false
        });
        
        // Get initial content from the hidden input
        var hiddenInput = document.getElementById('{{ $id }}');
        var initialContent = hiddenInput ? hiddenInput.value : '';
        
        // Set initial content
        editor_{{ $id }}.setValue(initialContent, -1);
        
        // Update hidden input when content changes
        editor_{{ $id }}.session.on('change', function() {
            updateContent_{{ $id }}();
        });
        
        // Initialize textarea
        var textareaEditor = document.getElementById('textarea-editor-{{ $id }}');
        if (textareaEditor) {
            textareaEditor.value = initialContent;
            textareaEditor.addEventListener('input', function() {
                updateContentFromTextarea_{{ $id }}();
            });
        }
        
        console.log('Ace Editor initialized successfully');
    } catch (error) {
        console.error('Error initializing Ace Editor:', error);
    }
}

// Update content from Ace Editor
function updateContent_{{ $id }}() {
    if (!editor_{{ $id }}) return;
    
    var content = editor_{{ $id }}.getValue();
    var hiddenInput = document.getElementById('{{ $id }}');
    var textareaEditor = document.getElementById('textarea-editor-{{ $id }}');
    
    // Update textarea if in textarea mode
    if (textareaEditor) {
        textareaEditor.value = content;
    }
    
    if (hiddenInput) {
        hiddenInput.value = content;
        
        // Update Livewire if applicable
        if (window.Livewire) {
            var wireModel = hiddenInput.getAttribute('wire:model') || 
                           hiddenInput.getAttribute('wire:model.defer') || 
                           hiddenInput.getAttribute('wire:model.live') || 
                           hiddenInput.getAttribute('wire:model.lazy');
            
            if (wireModel) {
                var wireId = hiddenInput.closest('[wire\\:id]')?.getAttribute('wire:id');
                if (wireId) {
                    var component = window.Livewire.find(wireId);
                    if (component) {
                        component.set(wireModel, content);
                    }
                }
            }
        }
    }
    
    // Update preview if visible
    var previewElement = document.getElementById('preview-{{ $id }}');
    if (previewElement && !previewElement.classList.contains('hidden')) {
        updatePreview_{{ $id }}();
    }
}

// Update content from textarea
function updateContentFromTextarea_{{ $id }}() {
    var textareaEditor = document.getElementById('textarea-editor-{{ $id }}');
    if (!textareaEditor) return;
    
    var content = textareaEditor.value;
    var hiddenInput = document.getElementById('{{ $id }}');
    
    // Update Ace Editor if it exists
    if (editor_{{ $id }}) {
        editor_{{ $id }}.setValue(content, -1);
    }
    
    if (hiddenInput) {
        hiddenInput.value = content;
        
        // Update Livewire if applicable
        if (window.Livewire) {
            var wireModel = hiddenInput.getAttribute('wire:model') || 
                           hiddenInput.getAttribute('wire:model.defer') || 
                           hiddenInput.getAttribute('wire:model.live') || 
                           hiddenInput.getAttribute('wire:model.lazy');
            
            if (wireModel) {
                var wireId = hiddenInput.closest('[wire\\:id]')?.getAttribute('wire:id');
                if (wireId) {
                    var component = window.Livewire.find(wireId);
                    if (component) {
                        component.set(wireModel, content);
                    }
                }
            }
        }
    }
    
    // Update preview if visible
    var previewElement = document.getElementById('preview-{{ $id }}');
    if (previewElement && !previewElement.classList.contains('hidden')) {
        updatePreview_{{ $id }}();
    }
}

// Toggle between Ace Editor and Textarea
function toggleTextarea_{{ $id }}() {
    var aceEditor = document.getElementById('ace-editor-{{ $id }}');
    var textareaEditor = document.getElementById('textarea-editor-{{ $id }}');
    var textareaToggle = document.getElementById('textarea-toggle-{{ $id }}');
    
    if (!aceEditor || !textareaEditor || !textareaToggle) return;
    
    if (isTextareaMode_{{ $id }}) {
        // Switch to Ace Editor
        textareaEditor.classList.add('hidden');
        aceEditor.classList.remove('hidden');
        textareaToggle.textContent = 'Use Textarea';
        
        // Update Ace Editor content
        if (editor_{{ $id }}) {
            editor_{{ $id }}.setValue(textareaEditor.value, -1);
            editor_{{ $id }}.focus();
        }
        
        isTextareaMode_{{ $id }} = false;
    } else {
        // Switch to Textarea
        aceEditor.classList.add('hidden');
        textareaEditor.classList.remove('hidden');
        textareaToggle.textContent = 'Use Ace Editor';
        
        // Update textarea content
        if (editor_{{ $id }}) {
            textareaEditor.value = editor_{{ $id }}.getValue();
        }
        
        textareaEditor.focus();
        isTextareaMode_{{ $id }} = true;
    }
    
    // Update preview if visible
    var previewElement = document.getElementById('preview-{{ $id }}');
    if (previewElement && !previewElement.classList.contains('hidden')) {
        updatePreview_{{ $id }}();
    }
}

// Toggle Vim mode
function toggleVimMode_{{ $id }}() {
    if (!editor_{{ $id }}) return;
    
    var vimToggle = document.getElementById('vim-toggle-{{ $id }}');
    
    try {
        if (isVimMode_{{ $id }}) {
            editor_{{ $id }}.setKeyboardHandler(null);
            vimToggle.textContent = 'Vim Mode';
            isVimMode_{{ $id }} = false;
        } else {
            editor_{{ $id }}.setKeyboardHandler('ace/keyboard/vim');
            vimToggle.textContent = 'Normal Mode';
            isVimMode_{{ $id }} = true;
        }
    } catch (error) {
        console.error('Error toggling Vim mode:', error);
        alert('Vim mode could not be enabled. Make sure Ace Editor is fully loaded.');
    }
}

// Toggle preview
function togglePreview_{{ $id }}() {
    var previewElement = document.getElementById('preview-{{ $id }}');
    var editorElement = isTextareaMode_{{ $id }} ? 
                        document.getElementById('textarea-editor-{{ $id }}') : 
                        document.getElementById('ace-editor-{{ $id }}');
    var previewToggle = document.getElementById('preview-toggle-{{ $id }}');
    
    if (!previewElement || !editorElement || !previewToggle) return;
    
    if (previewElement.classList.contains('hidden')) {
        // Show preview
        previewElement.classList.remove('hidden');
        editorElement.classList.remove('w-full');
        editorElement.classList.add('w-1/2');
        previewToggle.textContent = 'Hide Preview';
        updatePreview_{{ $id }}();
    } else {
        // Hide preview
        previewElement.classList.add('hidden');
        editorElement.classList.remove('w-1/2');
        editorElement.classList.add('w-full');
        previewToggle.textContent = 'Show Preview';
    }
    
    if (editor_{{ $id }} && !isTextareaMode_{{ $id }}) {
        editor_{{ $id }}.resize();
    }
}

// Update preview
function updatePreview_{{ $id }}() {
    var previewElement = document.getElementById('preview-{{ $id }}');
    if (!previewElement) return;
    
    var content = '';
    
    if (isTextareaMode_{{ $id }}) {
        var textareaEditor = document.getElementById('textarea-editor-{{ $id }}');
        if (textareaEditor) {
            content = textareaEditor.value;
        }
    } else if (editor_{{ $id }}) {
        content = editor_{{ $id }}.getValue();
    }
    
    if (typeof marked !== 'undefined') {
        try {
            previewElement.innerHTML = marked.parse(content);
        } catch (error) {
            console.error('Error parsing markdown:', error);
            previewElement.innerHTML = '<div class="text-red-500">Error parsing markdown</div>';
        }
    } else {
        previewElement.innerHTML = '<div class="text-yellow-500">Loading preview...</div>';
    }
}

// Toggle fullscreen
function toggleFullscreen_{{ $id }}() {
    var editorContainer = document.getElementById('editor-container-{{ $id }}');
    var toolbar = document.getElementById('toolbar-{{ $id }}');
    var fullscreenToggle = document.getElementById('fullscreen-toggle-{{ $id }}');
    
    if (!editorContainer || !toolbar || !fullscreenToggle) return;
    
    if (!isFullscreen_{{ $id }}) {
        // Enter fullscreen
        originalParent_{{ $id }} = editorContainer.parentNode;
        
        // Create fullscreen overlay
        fullscreenOverlay_{{ $id }} = document.createElement('div');
        fullscreenOverlay_{{ $id }}.id = 'fullscreen-overlay-{{ $id }}';
        fullscreenOverlay_{{ $id }}.style.position = 'fixed';
        fullscreenOverlay_{{ $id }}.style.top = '0';
        fullscreenOverlay_{{ $id }}.style.left = '0';
        fullscreenOverlay_{{ $id }}.style.width = '100vw';
        fullscreenOverlay_{{ $id }}.style.height = '100vh';
        fullscreenOverlay_{{ $id }}.style.backgroundColor = '#fff';
        fullscreenOverlay_{{ $id }}.style.zIndex = '9999';
        fullscreenOverlay_{{ $id }}.style.display = 'flex';
        fullscreenOverlay_{{ $id }}.style.flexDirection = 'column';
        
        // Add toolbar to fullscreen overlay
        var clonedToolbar = toolbar.cloneNode(true);
        clonedToolbar.id = 'fullscreen-toolbar-{{ $id }}';
        fullscreenOverlay_{{ $id }}.appendChild(clonedToolbar);
        
        // Add editor container to fullscreen overlay
        fullscreenOverlay_{{ $id }}.appendChild(editorContainer);
        
        // Add fullscreen overlay to body
        document.body.appendChild(fullscreenOverlay_{{ $id }});
        
        // Adjust editor height
        document.getElementById('ace-editor-{{ $id }}').style.height = 'calc(100vh - 50px)';
        document.getElementById('textarea-editor-{{ $id }}').style.height = 'calc(100vh - 50px)';
        if (!document.getElementById('preview-{{ $id }}').classList.contains('hidden')) {
            document.getElementById('preview-{{ $id }}').style.height = 'calc(100vh - 50px)';
        }
        
        // Update button text
        fullscreenToggle.textContent = 'Exit Fullscreen';
        isFullscreen_{{ $id }} = true;
        
        // Rebind event handlers for the cloned toolbar buttons
        var buttons = clonedToolbar.querySelectorAll('button');
        buttons.forEach(function(button) {
            var onclick = button.getAttribute('onclick');
            if (onclick) {
                // Create a new function from the onclick attribute
                var fnName = onclick.split('(')[0];
                if (fnName === 'toggleFullscreen_' + '{{ $id }}') {
                    button.onclick = function() {
                        toggleFullscreen_{{ $id }}();
                    };
                } else {
                    button.onclick = new Function('return ' + onclick);
                }
            }
        });
        
        // Resize editor
        if (editor_{{ $id }} && !isTextareaMode_{{ $id }}) {
            editor_{{ $id }}.resize();
        }
    } else {
        // Exit fullscreen
        if (originalParent_{{ $id }} && fullscreenOverlay_{{ $id }}) {
            // Move editor container back to original parent
            originalParent_{{ $id }}.insertBefore(editorContainer, originalParent_{{ $id }}.firstChild);
            
            // Remove fullscreen overlay
            document.body.removeChild(fullscreenOverlay_{{ $id }});
            fullscreenOverlay_{{ $id }} = null;
            
            // Reset editor height
            document.getElementById('ace-editor-{{ $id }}').style.height = '450px';
            document.getElementById('textarea-editor-{{ $id }}').style.height = '450px';
            if (!document.getElementById('preview-{{ $id }}').classList.contains('hidden')) {
                document.getElementById('preview-{{ $id }}').style.height = '450px';
            }
            
            // Update button text
            fullscreenToggle.textContent = 'Fullscreen';
            isFullscreen_{{ $id }} = false;
            
            // Resize editor
            if (editor_{{ $id }} && !isTextareaMode_{{ $id }}) {
                editor_{{ $id }}.resize();
            }
        }
    }
}

// Insert markdown
function insertMarkdown_{{ $id }}(type) {
    var cursor, session, selection, selectedText, insertText;
    
    if (isTextareaMode_{{ $id }}) {
        // Insert markdown in textarea
        var textareaEditor = document.getElementById('textarea-editor-{{ $id }}');
        if (!textareaEditor) return;
        
        var start = textareaEditor.selectionStart;
        var end = textareaEditor.selectionEnd;
        selectedText = textareaEditor.value.substring(start, end);
        
        insertText = getMarkdownText(type, selectedText);
        
        // Insert the text
        textareaEditor.focus();
        document.execCommand('insertText', false, insertText);
        
        // If that doesn't work, fall back to the old way
        if (textareaEditor.value.indexOf(insertText) === -1) {
            var beforeText = textareaEditor.value.substring(0, start);
            var afterText = textareaEditor.value.substring(end);
            textareaEditor.value = beforeText + insertText + afterText;
            
            // Update the content
            updateContentFromTextarea_{{ $id }}();
        }
    } else if (editor_{{ $id }}) {
        // Insert markdown in Ace Editor
        cursor = editor_{{ $id }}.getCursorPosition();
        session = editor_{{ $id }}.session;
        selection = editor_{{ $id }}.getSelection();
        selectedText = selection.isEmpty() ? '' : editor_{{ $id }}.getSelectedText();
        
        insertText = getMarkdownText(type, selectedText);
        
        if (selection.isEmpty()) {
            session.insert(cursor, insertText);
        } else {
            var range = selection.getRange();
            session.replace(range, insertText);
        }
        
        editor_{{ $id }}.focus();
    }
}

// Get markdown text based on type and selected text
function getMarkdownText(type, selectedText) {
    var insertText = '';
    
    switch(type) {
        case 'bold':
            insertText = selectedText ? '**' + selectedText + '**' : '**bold text**';
            break;
        case 'italic':
            insertText = selectedText ? '*' + selectedText + '*' : '*italic text*';
            break;
        case 'heading':
            insertText = selectedText ? '\n# ' + selectedText + '\n' : '\n# Heading\n';
            break;
        case 'link':
            insertText = selectedText ? '[' + selectedText + '](https://)' : '[link_text](https://)';
            break;
        case 'image':
            insertText = '![alt_text](https://)';
            break;
        case 'code':
            insertText = selectedText ? '```\n' + selectedText + '\n```' : '```\ncode block\n```';
            break;
        case 'quote':
            if (selectedText) {
                var lines = selectedText.split('\n');
                insertText = '';
                for (var i = 0; i < lines.length; i++) {
                    insertText += '> ' + lines[i] + '\n';
                }
            } else {
                insertText = '> blockquote';
            }
            break;
        case 'ul':
            if (selectedText) {
                var lines = selectedText.split('\n');
                insertText = '';
                for (var i = 0; i < lines.length; i++) {
                    insertText += '- ' + lines[i] + '\n';
                }
            } else {
                insertText = '- list item';
            }
            break;
        case 'ol':
            if (selectedText) {
                var lines = selectedText.split('\n');
                insertText = '';
                for (var i = 0; i < lines.length; i++) {
                    insertText += (i+1) + '. ' + lines[i] + '\n';
                }
            } else {
                insertText = '1. list item';
            }
            break;
        case 'hr':
            insertText = '\n---\n';
            break;
    }
    
    return insertText;
}
</script>
