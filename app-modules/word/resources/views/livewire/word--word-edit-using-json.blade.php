<?php

use Livewire\Volt\Component;
use Modules\Word\Models\Word;
use Modules\Word\Models\WordMeaning;
use Modules\Word\Models\WordTranslation;
use Modules\Word\Models\WordConnection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public Word $word;
    public $jsonData = '';
    public $showModal = false;
    public $processing = false;
    public $errors = [];
    public $updateSuccess = false;

    public function mount(Word $word)
    {
        $this->word = $word;
        $this->jsonData = $this->generateJsonFromWord($this->word);
    }

    // Generate JSON representation of the word
    private function generateJsonFromWord(Word $word)
    {
        $data = [
            'id' => $word->id,
            'word' => $word->word,
            'slug' => $word->slug,
            'phonetic' => $word->phonetic,
            'part_of_speech' => $word->part_of_speech,
            'source' => $word->source,
            'meanings' => [],
            'synonyms' => [],
            'antonyms' => [],
            'pronunciations' => [
                'bn' => '',
                'hi' => ''
            ],
            'translations' => [
                [
                    'locale' => 'bn',
                    'translation' => '',
                    'transliteration' => ''
                ],
                [
                    'locale' => 'hi',
                    'translation' => '',
                    'transliteration' => ''
                ]
            ]
        ];

        // Add pronunciations for non-English locales
        $pronunciations = $word->getTranslations('pronunciation');
        if (!empty($pronunciations)) {
            $data['pronunciations'] = $pronunciations;
        }

        // Add at least one empty meaning if none exist
        if (count($word->meanings) === 0) {
            $data['meanings'][] = [
                'meaning' => '',
                'slug' => '',
                'display_order' => 1,
                'source' => '',
                'translations' => [
                    [
                        'locale' => 'bn',
                        'translation' => '',
                        'transliteration' => ''
                    ],
                    [
                        'locale' => 'hi',
                        'translation' => '',
                        'transliteration' => ''
                    ]
                ]
            ];
        }

        // Add meanings with translations
        foreach ($word->meanings as $meaning) {
            $meaningData = [
                'id' => $meaning->id,
                'meaning' => $meaning->meaning,
                'slug' => $meaning->slug,
                'display_order' => $meaning->display_order,
                'source' => $meaning->source,
                'translations' => []
            ];

            // Add translations for this meaning
            foreach ($meaning->translations as $translation) {
                $meaningData['translations'][] = [
                    'id' => $translation->id,
                    'locale' => $translation->locale,
                    'translation' => $translation->translation,
                    'transliteration' => $translation->transliteration
                ];
            }

            // If no translations exist for this meaning, add empty ones
            if (empty($meaningData['translations'])) {
                $meaningData['translations'] = [
                    [
                        'locale' => 'bn',
                        'translation' => '',
                        'transliteration' => ''
                    ],
                    [
                        'locale' => 'hi',
                        'translation' => '',
                        'transliteration' => ''
                    ]
                ];
            }

            $data['meanings'][] = $meaningData;
        }

        // Add standalone translations
        $standaloneTranslations = $word->translations()->whereNull('meaning_id')->get();
        if ($standaloneTranslations->count() > 0) {
            $data['translations'] = [];
            foreach ($standaloneTranslations as $translation) {
                $data['translations'][] = [
                    'id' => $translation->id,
                    'locale' => $translation->locale,
                    'translation' => $translation->translation,
                    'transliteration' => $translation->transliteration,
                    'slug' => $translation->slug
                ];
            }
        } else {
            // If no standalone translations exist, add empty ones
            $data['translations'] = [
                [
                    'locale' => 'bn',
                    'translation' => '',
                    'transliteration' => ''
                ],
                [
                    'locale' => 'hi',
                    'translation' => '',
                    'transliteration' => ''
                ]
            ];
        }

        // Add synonyms
        $synonyms = $word->synonyms();
        if ($synonyms->count() > 0) {
            $data['synonyms'] = [];
            foreach ($synonyms as $synonym) {
                $data['synonyms'][] = $synonym->word;
            }
        }

        // Add antonyms
        $antonyms = $word->antonyms();
        if ($antonyms->count() > 0) {
            $data['antonyms'] = [];
            foreach ($antonyms as $antonym) {
                $data['antonyms'][] = $antonym->word;
            }
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

    // Process JSON data and update word
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

            // Validate that we're updating the correct word
            if (!isset($data['id']) || $data['id'] != $this->word->id) {
                $this->errors[] = "Word ID in JSON does not match the word being edited.";
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Word ID in JSON does not match the word being edited."
                ]);
                $this->processing = false;
                return;
            }

            DB::beginTransaction();
            try {
                // Update word basic attributes (except slug and word itself)
                $this->word->phonetic = $data['phonetic'] ?? $this->word->phonetic;
                $this->word->part_of_speech = $data['part_of_speech'] ?? $this->word->part_of_speech;
                $this->word->source = $data['source'] ?? $this->word->source;
                $this->word->save();

                // Update pronunciations if provided
                if (isset($data['pronunciations']) && is_array($data['pronunciations'])) {
                    foreach ($data['pronunciations'] as $locale => $pronunciation) {
                        $this->word->setTranslation('pronunciation', $locale, $pronunciation);
                    }
                    $this->word->save();
                }

                // Update meanings and their translations
                $this->updateMeanings($data);

                // Update standalone translations
                $this->updateTranslations($data);

                // Update synonyms and antonyms
                $this->updateSynonyms($data);
                $this->updateAntonyms($data);

                DB::commit();
                $this->updateSuccess = true;
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => "Word updated successfully!"
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Error updating word: " . $e->getMessage();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Error updating word: " . $e->getMessage()
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

    // Update meanings and their translations
    private function updateMeanings($data)
    {
        if (!isset($data['meanings']) || !is_array($data['meanings'])) {
            return;
        }

        // Get existing meaning IDs
        $existingMeaningIds = $this->word->meanings->pluck('id')->toArray();
        $updatedMeaningIds = [];

        // Process each meaning in the JSON
        foreach ($data['meanings'] as $meaningData) {
            // If ID is provided, update existing meaning
            if (isset($meaningData['id']) && !empty($meaningData['id'])) {
                $meaning = WordMeaning::find($meaningData['id']);

                if ($meaning && $meaning->word_id == $this->word->id) {
                    $meaning->meaning = $meaningData['meaning'] ?? $meaning->meaning;
                    // Always update slug when meaning changes
                    $meaning->slug = Str::slug(substr($meaning->meaning, 0, 250));
                    $meaning->display_order = $meaningData['display_order'] ?? $meaning->display_order;
                    $meaning->source = $meaningData['source'] ?? $meaning->source;
                    $meaning->save();

                    $updatedMeaningIds[] = $meaning->id;

                    // Update translations for this meaning
                    $this->updateMeaningTranslations($meaning, $meaningData);
                }
            } else {
                // Create new meaning
                $meaningText = $meaningData['meaning'];
                // Generate slug from meaning text
                $slug = Str::slug(substr($meaningText, 0, 250));
                $meaning = WordMeaning::create([
                    'word_id' => $this->word->id,
                    'meaning' => $meaningText,
                    'slug' => $slug,
                    'display_order' => $meaningData['display_order'] ?? 0,
                    'source' => $meaningData['source'] ?? 'json',
                ]);

                $updatedMeaningIds[] = $meaning->id;

                // Create translations for this meaning
                $this->updateMeaningTranslations($meaning, $meaningData);
            }
        }

        // Remove meanings that are not in the JSON
        $meaningsToDelete = array_diff($existingMeaningIds, $updatedMeaningIds);
        if (!empty($meaningsToDelete)) {
            WordMeaning::whereIn('id', $meaningsToDelete)->delete();
        }
    }

    // Update translations for a meaning
    private function updateMeaningTranslations($meaning, $meaningData)
    {
        if (!isset($meaningData['translations']) || !is_array($meaningData['translations'])) {
            return;
        }

        // Get existing translation IDs for this meaning
        $existingTranslationIds = $meaning->translations->pluck('id')->toArray();
        $updatedTranslationIds = [];

        // Process each translation in the JSON
        foreach ($meaningData['translations'] as $translationData) {
            // If ID is provided, update existing translation
            if (isset($translationData['id']) && !empty($translationData['id'])) {
                $translation = WordTranslation::find($translationData['id']);

                if ($translation && $translation->meaning_id == $meaning->id) {
                    $translation->translation = $translationData['translation'] ?? $translation->translation;
                    // Always update slug when translation changes
                    $translation->slug = Str::slug(substr($translation->translation, 0, 250));
                    $translation->transliteration = $translationData['transliteration'] ?? $translation->transliteration;
                    $translation->save();

                    $updatedTranslationIds[] = $translation->id;
                }
            } else {
                // Skip if translation text is missing
                if (!isset($translationData['translation']) || empty($translationData['translation'])) {
                    continue;
                }

                // Try to generate a slug
                $translationText = $translationData['translation'];
                $slug = Str::slug(substr($translationText, 0, 250));

                // If we can't generate a valid slug, skip this translation
                if (empty($slug)) {
                    continue;
                }

                // Check if translation already exists
                $existingTranslation = WordTranslation::where('meaning_id', $meaning->id)
                    ->where('locale', $translationData['locale'])
                    ->first();

                if ($existingTranslation) {
                    $existingTranslation->translation = $translationData['translation'];
                    // Always update slug when translation changes
                    $existingTranslation->slug = Str::slug(substr($translationData['translation'], 0, 250));
                    $existingTranslation->transliteration = $translationData['transliteration'] ?? null;
                    $existingTranslation->save();

                    $updatedTranslationIds[] = $existingTranslation->id;
                } else {
                    // Create new translation
                    $translation = WordTranslation::create([
                        'word_id' => $this->word->id,
                        'meaning_id' => $meaning->id,
                        'locale' => $translationData['locale'],
                        'translation' => $translationData['translation'],
                        'transliteration' => $translationData['transliteration'] ?? null,
                        'source' => 'json',
                        'slug' => $slug,
                    ]);

                    $updatedTranslationIds[] = $translation->id;
                }
            }
        }

        // Remove translations that are not in the JSON
        $translationsToDelete = array_diff($existingTranslationIds, $updatedTranslationIds);
        if (!empty($translationsToDelete)) {
            WordTranslation::whereIn('id', $translationsToDelete)->delete();
        }
    }

    // Update standalone translations
    private function updateTranslations($data)
    {
        if (!isset($data['translations']) || !is_array($data['translations'])) {
            // Remove all standalone translations if not in JSON
            WordTranslation::where('word_id', $this->word->id)
                ->whereNull('meaning_id')
                ->delete();
            return;
        }

        // Get existing standalone translation IDs
        $existingTranslationIds = $this->word->translations()
            ->whereNull('meaning_id')
            ->pluck('id')
            ->toArray();
        $updatedTranslationIds = [];

        // Process each translation in the JSON
        foreach ($data['translations'] as $translationData) {
            // If ID is provided, update existing translation
            if (isset($translationData['id']) && !empty($translationData['id'])) {
                $translation = WordTranslation::find($translationData['id']);

                if ($translation && $translation->word_id == $this->word->id && $translation->meaning_id === null) {
                    $translation->translation = $translationData['translation'] ?? $translation->translation;
                    // Always update slug when translation changes
                    $translation->slug = Str::slug(substr($translation->translation, 0, 250));
                    $translation->transliteration = $translationData['transliteration'] ?? $translation->transliteration;
                    $translation->save();

                    $updatedTranslationIds[] = $translation->id;
                }
            } else {
                // Skip if translation text is missing
                if (!isset($translationData['translation']) || empty($translationData['translation'])) {
                    continue;
                }

                // Try to generate a slug
                $translationText = $translationData['translation'];
                $slug = Str::slug(substr($translationText, 0, 250));

                // If we can't generate a valid slug, skip this translation
                if (empty($slug)) {
                    continue;
                }

                // Check if translation already exists
                $existingTranslation = WordTranslation::where('word_id', $this->word->id)
                    ->whereNull('meaning_id')
                    ->where('locale', $translationData['locale'])
                    ->first();

                if ($existingTranslation) {
                    $existingTranslation->translation = $translationData['translation'];
                    // Always update slug when translation changes
                    $existingTranslation->slug = Str::slug(substr($translationData['translation'], 0, 250));
                    $existingTranslation->transliteration = $translationData['transliteration'] ?? null;
                    $existingTranslation->save();

                    $updatedTranslationIds[] = $existingTranslation->id;
                } else {
                    // Create new translation
                    $translation = WordTranslation::create([
                        'word_id' => $this->word->id,
                        'meaning_id' => null,
                        'locale' => $translationData['locale'],
                        'translation' => $translationData['translation'],
                        'transliteration' => $translationData['transliteration'] ?? null,
                        'source' => 'json',
                        'slug' => $slug,
                    ]);

                    $updatedTranslationIds[] = $translation->id;
                }
            }
        }

        // Remove translations that are not in the JSON
        $translationsToDelete = array_diff($existingTranslationIds, $updatedTranslationIds);
        if (!empty($translationsToDelete)) {
            WordTranslation::whereIn('id', $translationsToDelete)->delete();
        }
    }

    // Update synonyms
    private function updateSynonyms($data)
    {
        // Skip if no synonyms data
        if (!isset($data['synonyms']) || !is_array($data['synonyms'])) {
            return;
        }

        // Remove all existing synonym connections
        DB::table('word_connections')
            ->where(function($query) {
                $query->where('word_id_1', $this->word->id)
                    ->where('type', 'synonyms');
            })
            ->orWhere(function($query) {
                $query->where('word_id_2', $this->word->id)
                    ->where('type', 'synonyms');
            })
            ->delete();

        // Add new synonym connections
        foreach ($data['synonyms'] as $synonym) {
            // Skip if empty
            if (empty($synonym)) {
                continue;
            }

            // Handle both ID and word string cases
            if (is_numeric($synonym)) {
                $synonymId = $synonym;
            } else {
                // Find or create the word
                $synonymWord = Word::firstOrCreate(
                    ['slug' => Str::slug($synonym)],
                    [
                        'word' => $synonym,
                        'slug' => Str::slug($synonym),
                        'source' => 'json'
                    ]
                );
                $synonymId = $synonymWord->id;
            }

            // Skip if trying to connect to self
            if ($synonymId == $this->word->id) {
                continue;
            }

            // Insert new connection
            DB::table('word_connections')->insert([
                'word_id_1' => $this->word->id,
                'word_id_2' => $synonymId,
                'type' => 'synonyms',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Update antonyms
    private function updateAntonyms($data)
    {
        // Skip if no antonyms data
        if (!isset($data['antonyms']) || !is_array($data['antonyms'])) {
            return;
        }

        // Remove all existing antonym connections
        DB::table('word_connections')
            ->where(function($query) {
                $query->where('word_id_1', $this->word->id)
                    ->where('type', 'antonyms');
            })
            ->orWhere(function($query) {
                $query->where('word_id_2', $this->word->id)
                    ->where('type', 'antonyms');
            })
            ->delete();

        // Add new antonym connections
        foreach ($data['antonyms'] as $antonym) {
            // Skip if empty
            if (empty($antonym)) {
                continue;
            }

            // Handle both ID and word string cases
            if (is_numeric($antonym)) {
                $antonymId = $antonym;
            } else {
                // Find or create the word
                $antonymWord = Word::firstOrCreate(
                    ['slug' => Str::slug($antonym)],
                    [
                        'word' => $antonym,
                        'slug' => Str::slug($antonym),
                        'source' => 'json'
                    ]
                );
                $antonymId = $antonymWord->id;
            }

            // Skip if trying to connect to self
            if ($antonymId == $this->word->id) {
                continue;
            }

            // Insert new connection
            DB::table('word_connections')->insert([
                'word_id_1' => $this->word->id,
                'word_id_2' => $antonymId,
                'type' => 'antonyms',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Word Using JSON</h3>
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
                    <p>Word updated successfully!</p>
                </div>
                @endif

                <!-- JSON Input -->
                <div class="mb-4">
                    <div wire:ignore>
                        <x-json-editor
                            label="JSON Data"
                            wire:model.live="jsonData"
                            :content="$jsonData"
                            placeholder='{"word": "example", "meanings": [...]}'
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
                        Update Word
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