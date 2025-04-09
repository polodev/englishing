@props([
    'label' => null,
    'name' => 'json_editor',
    'content' => '',
    'placeholder' => '{}',
    'modelName' => null,
])

<div
    x-data="{
        editor: null,
        isVimMode: false,
        isFullscreen: false,
        hasError: false,
        errorMessage: '',
        content: {{ json_encode($content) }},
        originalParent: null,
        originalHeight: null,
        livewireId: null,
        modelName: {{ json_encode($modelName) }},
        isSimpleMode: false,

        init() {
            this.findLivewireComponent();
            this.waitForAce().then(() => {
                this.initializeEditor();
            }).catch(error => {
                console.error('Error initializing Ace Editor:', error);
            });
        },

        findLivewireComponent() {
            const el = $el.closest('[wire\\:id]');
            if (el) {
                this.livewireId = el.getAttribute('wire:id');
                if (!this.modelName) {
                    const input = $refs.hiddenInput;
                    const modelAttr = input.getAttribute('wire:model') || 
                                     input.getAttribute('wire:model.live') || 
                                     input.getAttribute('wire:model.defer');
                    if (modelAttr) {
                        this.modelName = modelAttr;
                    }
                }
            }
        },

        waitForAce() {
            return new Promise((resolve, reject) => {
                if (window.ace) {
                    resolve();
                } else {
                    const checkInterval = setInterval(() => {
                        if (window.ace) {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 100);
                    setTimeout(() => {
                        clearInterval(checkInterval);
                        reject('Ace Editor is not loaded after 5 seconds');
                    }, 5000);
                }
            });
        },

        /**
         * Initialize the Ace Editor
         */
        initializeEditor() {
            try {
                // Initialize Ace Editor
                this.editor = window.ace.edit($refs.editor);

                // Enable language tools for auto-completion
                this.editor.setOptions({
                    enableBasicAutocompletion: true,
                    enableSnippets: true,
                    enableLiveAutocompletion: true
                });

                // Set ACE Editor to JSON Mode
                this.editor.session.setMode('ace/mode/json');
                this.editor.setTheme('ace/theme/dracula');
                this.editor.session.setUseWrapMode(true);
                this.editor.setOption('tabSize', 2);
                this.editor.setShowPrintMargin(false);
                
                // Set the initial content
                this.editor.setValue(this.unescapeHtml(this.content), -1);

                // Listen for changes
                this.editor.session.on('change', () => {
                    this.updateContent();
                });

                // Add keyboard shortcut for formatting JSON
                this.editor.commands.addCommand({
                    name: 'saveContent',
                    bindKey: {win: 'Ctrl-S', mac: 'Command-S'},
                    exec: () => {
                        this.formatJson();
                        return true;
                    }
                });

                // Validate initial JSON
                this.validateJson();
            } catch (error) {
                console.error('Error initializing Ace Editor:', error);
            }
        },

        /**
         * Update the content and validate JSON
         */
        updateContent() {
            if (this.isSimpleMode) {
                const content = $refs.simpleEditor.value;
                this.content = content;
                $refs.hiddenInput.value = content;
                this.validateJson(content);
            } else if (this.editor) {
                const content = this.editor.getValue();
                this.content = content;
                $refs.hiddenInput.value = content;
                this.validateJson(content);
            }

            // Update Livewire model directly if we have the component ID and model name
            if (this.livewireId && this.modelName) {
                // Use Livewire's JavaScript API to update the model
                if (window.Livewire) {
                    try {
                        const component = window.Livewire.find(this.livewireId);
                        if (component) {
                            // Use the set method to update the property
                            component.set(this.modelName, this.content);

                            // Also dispatch a custom event as a backup method
                            window.dispatchEvent(new CustomEvent('json-editor-update', {
                                detail: {
                                    id: this.livewireId,
                                    model: this.modelName,
                                    value: this.content
                                }
                            }));
                        }
                    } catch (error) {
                        // Silently handle errors
                    }
                }
            }
        },

        /**
         * Validate JSON and show error if invalid
         */
        validateJson(contentToValidate) {
            const content = contentToValidate || (this.isSimpleMode ? $refs.simpleEditor.value : (this.editor ? this.editor.getValue() : '')).trim();
            
            if (!content) {
                this.hasError = false;
                this.errorMessage = '';
                return;
            }

            try {
                JSON.parse(content);
                this.hasError = false;
                this.errorMessage = '';
            } catch (error) {
                this.hasError = true;
                this.errorMessage = error.message;
            }
        },

        /**
         * Toggle fullscreen mode for the editor.
         */
        toggleFullscreen() {
            if (this.isSimpleMode) return;
            
            this.isFullscreen = !this.isFullscreen;

            if (this.isFullscreen) {
                // Save the original parent to restore later
                this.originalParent = $refs.editorContainer.parentNode;
                this.originalHeight = $refs.editor.style.height;

                // Create a fullscreen overlay
                const overlay = document.createElement('div');
                overlay.id = 'ace-editor-fullscreen-overlay';
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100vw';
                overlay.style.height = '100vh';
                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.9)';
                overlay.style.zIndex = '9999';
                overlay.style.display = 'flex';
                overlay.style.flexDirection = 'column';
                overlay.style.padding = '20px';

                // Add a close button
                const closeButton = document.createElement('button');
                closeButton.textContent = 'Exit Fullscreen';
                closeButton.style.alignSelf = 'flex-end';
                closeButton.style.marginBottom = '10px';
                closeButton.style.padding = '5px 10px';
                closeButton.style.backgroundColor = '#4a5568';
                closeButton.style.color = 'white';
                closeButton.style.border = 'none';
                closeButton.style.borderRadius = '4px';
                closeButton.style.cursor = 'pointer';
                closeButton.addEventListener('click', () => this.toggleFullscreen());

                // Add the editor container to the overlay
                document.body.appendChild(overlay);
                overlay.appendChild(closeButton);
                overlay.appendChild($refs.editorContainer);

                // Adjust editor size
                $refs.editor.style.height = 'calc(100vh - 100px)';
                $refs.editor.style.width = '100%';

                // Prevent body scrolling
                document.body.style.overflow = 'hidden';

                // Resize the editor to fit the new container
                this.editor.resize();
                this.editor.focus();
            } else {
                // Get the overlay
                const overlay = document.getElementById('ace-editor-fullscreen-overlay');
                if (overlay) {
                    // Restore the editor to its original parent
                    if (this.originalParent) {
                        this.originalParent.appendChild($refs.editorContainer);
                    } else {
                        // Fallback if original parent is not available
                        document.body.appendChild($refs.editorContainer);
                    }

                    // Remove the overlay
                    document.body.removeChild(overlay);

                    // Restore original height
                    $refs.editor.style.height = this.originalHeight || '450px';

                    // Allow body scrolling again
                    document.body.style.overflow = '';

                    // Resize the editor to fit the original container
                    this.editor.resize();
                }
            }
        },

        /**
         * Toggle Vim mode
         */
        toggleVimMode() {
            if (this.isSimpleMode) return;
            
            this.isVimMode = !this.isVimMode;
            if (this.isVimMode) {
                this.editor.setKeyboardHandler('ace/keyboard/vim');
            } else {
                this.editor.setKeyboardHandler(null);
            }
        },

        /**
         * Format JSON
         */
        formatJson() {
            if (this.isSimpleMode) {
                const content = $refs.simpleEditor.value.trim();
                if (!content) return;
                
                try {
                    const parsed = JSON.parse(content);
                    const formatted = JSON.stringify(parsed, null, 2);
                    $refs.simpleEditor.value = formatted;
                    this.updateContent();
                } catch (error) {
                    console.error('Cannot format invalid JSON:', error);
                }
            } else if (this.editor) {
                const content = this.editor.getValue().trim();
                if (!content) return;
                
                try {
                    const parsed = JSON.parse(content);
                    const formatted = JSON.stringify(parsed, null, 2);
                    this.editor.setValue(formatted, -1);
                    this.validateJson();
                } catch (error) {
                    // If JSON is invalid, don't format
                    console.error('Cannot format invalid JSON:', error);
                }
            }
        },

        /**
         * Toggle between Ace editor and simple textarea
         */
        toggleSimpleMode() {
            if (!this.isSimpleMode && this.editor) {
                // Switching to simple mode
                const content = this.editor.getValue();
                this.isSimpleMode = true;
                // Wait for the textarea to be visible before setting value
                this.$nextTick(() => {
                    $refs.simpleEditor.value = content;
                    $refs.simpleEditor.style.height = $refs.editor.style.height || '450px';
                });
            } else {
                // Switching to Ace editor
                const content = $refs.simpleEditor.value;
                this.isSimpleMode = false;
                // Wait for the editor to be visible before setting value
                this.$nextTick(() => {
                    if (this.editor) {
                        this.editor.setValue(content, -1);
                        this.editor.resize();
                    }
                });
            }
        },

        /**
         * Unescape HTML entities
         */
        unescapeHtml(html) {
            if (!html) return '';
            const textarea = document.createElement('textarea');
            textarea.innerHTML = html;
            return textarea.value;
        }
    }"
    x-init="init()"
    class="relative"
>
    @if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
        {{ $label }}
    </label>
    @endif

    <div
        x-ref="editorContainer"
        class="relative border border-gray-300 dark:border-gray-700 rounded-md overflow-hidden"
    >
        <!-- Editor Toolbar -->
        <div class="flex items-center justify-between p-2 bg-gray-100 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
            <div class="flex items-center space-x-2">
                <button
                    type="button"
                    x-on:click="formatJson"
                    class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Format JSON
                </button>
                <button
                    type="button"
                    x-on:click="toggleVimMode"
                    x-text="isVimMode ? 'Disable Vim' : 'Enable Vim'"
                    x-show="!isSimpleMode"
                    class="px-2 py-1 text-xs font-medium text-white bg-gray-600 rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    Enable Vim
                </button>
                <button
                    type="button"
                    x-on:click="toggleSimpleMode"
                    x-text="isSimpleMode ? 'Use Editor' : 'Use Textarea'"
                    class="px-2 py-1 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                    Use Textarea
                </button>
            </div>
            <button
                type="button"
                x-on:click="toggleFullscreen"
                x-show="!isSimpleMode"
                class="px-2 py-1 text-xs font-medium text-white bg-gray-600 rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
            >
                <span x-text="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'">Fullscreen</span>
            </button>
        </div>

        <!-- Ace Editor Container -->
        <div
            x-ref="editor"
            x-show="!isSimpleMode"
            class="w-full"
            style="height: 450px; min-height: 450px; position: relative;"
        ></div>

        <!-- Simple Textarea -->
        <textarea
            x-ref="simpleEditor"
            x-show="isSimpleMode"
            x-on:input="updateContent()"
            class="w-full p-4 font-mono text-sm bg-gray-800 text-white dark:bg-gray-900 dark:text-gray-100"
            style="height: 450px; min-height: 450px; resize: vertical; font-family: 'Noto Sans Bengali', monospace;"
            placeholder="{{ $placeholder }}"
        ></textarea>

        <!-- Error Message -->
        <div
            x-show="hasError"
            x-transition
            class="p-2 text-sm text-red-600 bg-red-100 dark:bg-red-900 dark:text-red-200 border-t border-red-200 dark:border-red-800"
        >
            <span x-text="errorMessage"></span>
        </div>

        <!-- Hidden Input for Form Submission -->
        <input
            type="hidden"
            name="{{ $name }}"
            x-ref="hiddenInput"
            {{ $attributes->whereStartsWith('wire:') }}
        >
    </div>
</div>
