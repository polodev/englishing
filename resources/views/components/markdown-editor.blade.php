@props([
    'id' => 'markdown-editor',
    'name' => null,
    'value' => '',
    'placeholder' => 'Write your content here...',
    'required' => false,
    'wireModel' => null
])

<div class="markdown-editor-wrapper">
    <textarea
        id="{{ $id }}"
        name="{{ $name ?? $id }}"
        {{ $required ? 'required' : '' }}
        @if($wireModel)
            wire:model.defer="{{ $wireModel }}"
        @endif
        {{ $attributes->merge(['class' => 'hidden']) }}
    >{{ $value }}</textarea>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Give a small delay to ensure DOM is ready
        setTimeout(function() {
            const textarea = document.getElementById('{{ $id }}');
            if (!textarea) {
                console.error('Textarea not found:', '{{ $id }}');
                return;
            }

            try {
                // Create a new instance of EasyMDE
                const editor = new EasyMDE({
                    element: textarea,
                    spellChecker: false,
                    autosave: {
                        enabled: false,
                        uniqueId: '{{ $id }}_autosave',
                        delay: 1000,
                    },
                    placeholder: '{{ $placeholder }}',
                    toolbar: [
                        'bold', 'italic', 'heading', '|',
                        'quote', 'unordered-list', 'ordered-list', '|',
                        'link', 'image', '|',
                        'preview', 'side-by-side', 'fullscreen', '|',
                        'guide'
                    ],
                    status: ['lines', 'words', 'cursor'],
                    tabSize: 2,
                    lineWrapping: true,
                    initialValue: textarea.value // Explicitly set initial value
                });

                // Ensure the editor's content is always synced with the textarea
                editor.codemirror.on('change', function() {
                    textarea.value = editor.value();
                    
                    // For Livewire integration
                    @if($wireModel)
                        const livewireComponent = window.Livewire.find(
                            textarea.closest('[wire\\:id]')?.getAttribute('wire:id')
                        );
                        if (livewireComponent) {
                            livewireComponent.set('{{ $wireModel }}', editor.value());
                        }
                    @endif
                });

                // For regular form submission
                if (textarea.form) {
                    textarea.form.addEventListener('submit', function() {
                        textarea.value = editor.value();
                    });
                }

                // Add a custom command to clear the editor content
                editor.toolbar.push({
                    name: "clear",
                    action: function() {
                        editor.codemirror.setValue("");
                    },
                    className: "fa fa-eraser",
                    title: "Clear content"
                });

                console.log('EasyMDE initialized successfully for', '{{ $id }}');
            } catch (error) {
                console.error('Error initializing EasyMDE:', error);
            }
        }, 100);
    });
</script>