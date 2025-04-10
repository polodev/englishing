<?php

use Livewire\Volt\Component;
use Modules\Sentence\Models\Sentence;
use Modules\Sentence\Models\SentenceTranslation;
use Modules\Sentence\Models\SentencePronunciation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public Sentence $sentence;
    public $jsonData = '';
    public $showModal = false;
    public $processing = false;
    public $errors = [];
    public $updateSuccess = false;

    public function mount(Sentence $sentence)
    {
        $this->sentence = $sentence;
        $this->jsonData = $this->generateJsonFromSentence($this->sentence);
    }

    // Generate JSON representation of the sentence
    private function generateJsonFromSentence(Sentence $sentence)
    {
        $data = [
            'id' => $sentence->id,
            'sentence' => $sentence->sentence,
            'slug' => $sentence->slug,
            'source' => $sentence->source,
            'pronunciations' => [
                'bn' => '',
                'hi' => '',
                'es' => ''
            ],
            'translations' => []
        ];

        // Add pronunciations for non-English locales
        $pronunciations = $sentence->getTranslations('pronunciation');
        if (!empty($pronunciations)) {
            // Merge with default empty structure to ensure all keys exist
            $data['pronunciations'] = array_merge([
                'bn' => '',
                'hi' => '',
                'es' => ''
            ], $pronunciations);
        }

        // Add translations
        $translations = $sentence->translations;
        if ($translations->count() > 0) {
            foreach ($translations as $translation) {
                $translationData = [
                    'id' => $translation->id,
                    'locale' => $translation->locale,
                    'translation' => $translation->translation,
                    'slug' => $translation->slug
                ];

                // Add transliteration if it exists
                if ($translation->transliteration) {
                    $translationData['transliteration'] = $translation->transliteration;
                } else {
                    $translationData['transliteration'] = '';
                }

                $data['translations'][] = $translationData;
            }
        } else {
            // If no translations exist, add empty ones for common locales
            $data['translations'] = [
                [
                    'locale' => 'bn',
                    'translation' => '',
                    'transliteration' => '',
                    'slug' => ''
                ],
                [
                    'locale' => 'hi',
                    'translation' => '',
                    'transliteration' => '',
                    'slug' => ''
                ],
                [
                    'locale' => 'es',
                    'translation' => '',
                    'transliteration' => '',
                    'slug' => ''
                ]
            ];
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // Open the modal
    public function openModal()
    {
        $this->showModal = true;
        $this->reset(['errors', 'updateSuccess']);
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['errors', 'updateSuccess']);
    }

    // Process JSON data and update sentence
    public function processJson()
    {
        $this->reset(['errors', 'updateSuccess']);
        $this->processing = true;

        try {
            // Decode JSON data
            $data = json_decode($this->jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errors[] = "Invalid JSON: " . json_last_error_msg();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Invalid JSON: " . json_last_error_msg()
                ]);
                $this->processing = false;
                return;
            }

            // Validate that we're updating the correct sentence
            if (!isset($data['id']) || $data['id'] != $this->sentence->id) {
                $this->errors[] = "Sentence ID in JSON does not match the sentence being edited.";
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Sentence ID in JSON does not match the sentence being edited."
                ]);
                $this->processing = false;
                return;
            }

            DB::beginTransaction();
            try {
                // Update sentence basic attributes (except slug and sentence itself)
                $this->sentence->source = $data['source'] ?? $this->sentence->source;
                $this->sentence->save();

                // Update pronunciations if provided
                if (isset($data['pronunciations']) && is_array($data['pronunciations'])) {
                    foreach ($data['pronunciations'] as $locale => $pronunciation) {
                        $this->sentence->setTranslation('pronunciation', $locale, $pronunciation);
                    }
                    $this->sentence->save();
                }

                // Update translations
                $this->updateTranslations($data);

                DB::commit();
                $this->updateSuccess = true;
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => "Sentence updated successfully!"
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Error updating sentence: " . $e->getMessage();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Error updating sentence: " . $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            $this->errors[] = "Error: " . $e->getMessage();
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Error: " . $e->getMessage()
            ]);
        }

        $this->processing = false;
    }

    // Update translations
    private function updateTranslations($data)
    {
        if (!isset($data['translations']) || !is_array($data['translations'])) {
            // Remove all translations if not in JSON
            SentenceTranslation::where('sentence_id', $this->sentence->id)->delete();
            return;
        }

        // Get existing translations
        $existingTranslations = SentenceTranslation::where('sentence_id', $this->sentence->id)->get()->keyBy('locale');
        
        // Track locales in the JSON to identify which ones to delete
        $jsonLocales = [];
        
        foreach ($data['translations'] as $translationData) {
            // Skip if no locale or translation
            if (!isset($translationData['locale']) || empty($translationData['locale']) || 
                !isset($translationData['translation'])) {
                continue;
            }
            
            $locale = $translationData['locale'];
            $jsonLocales[] = $locale;
            
            // Create or update translation
            $translation = $existingTranslations->get($locale);
            
            if (!$translation) {
                // Create new translation
                $translation = new SentenceTranslation();
                $translation->sentence_id = $this->sentence->id;
                $translation->locale = $locale;
            }
            
            $translation->translation = $translationData['translation'];
            $translation->transliteration = $translationData['transliteration'] ?? null;
            
            // Generate slug if not provided
            if (empty($translationData['slug']) && !empty($translationData['translation'])) {
                $translation->slug = Str::slug(Str::limit($translationData['translation'], 100));
            } else {
                $translation->slug = $translationData['slug'] ?? '';
            }
            
            $translation->save();
        }
        
        // Delete translations not in the JSON
        if (!empty($jsonLocales)) {
            SentenceTranslation::where('sentence_id', $this->sentence->id)
                ->whereNotIn('locale', $jsonLocales)
                ->delete();
        }
    }
};
?>

<div>
    <!-- Button to open modal -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent dark:border-gray-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
    >
        Edit Using JSON
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
        <div class="fixed inset-0 transform transition-all" wire:click="closeModal">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-800 opacity-75"></div>
        </div>

        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-4xl mx-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Sentence Using JSON</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 dark:bg-gray-800">
                <!-- Errors -->
                @if(count($errors) > 0)
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-200">
                    <h4 class="font-bold mb-2">Errors:</h4>
                    <ul class="list-disc pl-5">
                        @foreach($errors as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Success Message -->
                @if($updateSuccess)
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200">
                    <p>Sentence updated successfully!</p>
                </div>
                @endif

                <!-- JSON Input -->
                <div class="mb-4">
                    <div wire:ignore>
                        <x-json-editor
                            label="JSON Data"
                            wire:model.live="jsonData"
                            :content="$jsonData"
                            placeholder='{"sentence": "example", "translations": [...]}'
                            model-name="jsonData"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button
                        wire:click.prevent="processJson"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 active:bg-blue-700 dark:active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                    >
                        <span wire:loading wire:target="processJson" class="mr-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        Update Sentence
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Toast Notifications -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('toast', (event) => {
                const toast = document.createElement('div');
                toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded-md text-white ${event.type === 'success' ? 'bg-green-500' : 'bg-red-500'} transition-opacity duration-500 opacity-0`;
                toast.textContent = event.message;
                document.body.appendChild(toast);
                
                // Fade in
                setTimeout(() => {
                    toast.style.opacity = '1';
                }, 10);
                
                // Fade out and remove
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 500);
                }, 3000);
            });
        });
    </script>
</div>