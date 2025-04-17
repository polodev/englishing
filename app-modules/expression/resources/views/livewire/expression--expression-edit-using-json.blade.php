<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Expression\Models\Expression;
use Modules\Expression\Models\ExpressionMeaning;
use Modules\Expression\Models\ExpressionTranslation;

new class extends Component {
    public Expression $expression;
    public $jsonData = '';
    public $showModal = false;
    public $processing = false;
    public $errors = [];
    public $updateSuccess = false;

    public function mount(Expression $expression)
    {
        $this->expression = $expression;
        $this->jsonData = $this->generateJsonFromExpression($this->expression);
    }

    // Generate JSON representation of the expression
    private function generateJsonFromExpression(Expression $expression)
    {
        $data = [
            'id' => $expression->id,
            'expression' => $expression->expression,
            'slug' => $expression->slug,
            'type' => $expression->type,
            'source' => $expression->source,
            'meanings' => [],
            'synonyms' => [],
            'antonyms' => [],
            'pronunciation' => [
                'bn' => '',
                'hi' => '',
                'es' => ''
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
                ],
                [
                    'locale' => 'es',
                    'translation' => '',
                    'transliteration' => ''
                ]
            ]
        ];

        // Add pronunciations for non-English locales
        $pronunciations = [];
        try {
            $pronunciations = $expression->getTranslations('pronunciation');
        } catch (\Exception $e) {
            // Handle the case when pronunciation is not set
        }

        if (!empty($pronunciations)) {
            // Merge with default empty structure to ensure all keys exist
            $data['pronunciation'] = array_merge([
                'bn' => '',
                'hi' => '',
                'es' => ''
            ], $pronunciations);
        }

        // Add at least one empty meaning if none exist
        if (count($expression->meanings) === 0) {
            $data['meanings'][] = [
                'meaning' => '',
                'slug' => '',
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
                    ],
                    [
                        'locale' => 'es',
                        'translation' => '',
                        'transliteration' => ''
                    ]
                ]
            ];
        }

        // Add meanings with translations
        foreach ($expression->meanings as $meaning) {
            $meaningData = [
                'id' => $meaning->id,
                'meaning' => $meaning->meaning,
                'slug' => $meaning->slug,
                'source' => $meaning->source,
                'translations' => []
            ];

            // Add translations for this meaning
            foreach ($meaning->translations as $translation) {
                $meaningData['translations'][] = [
                    'id' => $translation->id,
                    'locale' => $translation->locale,
                    'translation' => $translation->translation,
                    'transliteration' => $translation->transliteration,
                    'slug' => $translation->slug
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
                    ],
                    [
                        'locale' => 'es',
                        'translation' => '',
                        'transliteration' => ''
                    ]
                ];
            }

            $data['meanings'][] = $meaningData;
        }

        // Add standalone translations
        $standaloneTranslations = $expression->translations()->whereNull('expression_meaning_id')->get();
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
                ],
                [
                    'locale' => 'es',
                    'translation' => '',
                    'transliteration' => ''
                ]
            ];
        }

        // Add synonyms
        $synonyms = $expression->connections->where('pivot.type', 'synonyms')
            ->merge($expression->connectionsInverse->where('pivot.type', 'synonyms'));
        if ($synonyms->count() > 0) {
            $data['synonyms'] = [];
            foreach ($synonyms as $synonym) {
                $data['synonyms'][] = $synonym->expression;
            }
        }

        // Add antonyms
        $antonyms = $expression->connections->where('pivot.type', 'antonyms')
            ->merge($expression->connectionsInverse->where('pivot.type', 'antonyms'));
        if ($antonyms->count() > 0) {
            $data['antonyms'] = [];
            foreach ($antonyms as $antonym) {
                $data['antonyms'][] = $antonym->expression;
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

    // Process JSON data and update expression
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

            // Start transaction
            DB::beginTransaction();

            // Update basic expression data
            $this->updateExpressionData($data);

            // Update meanings and their translations
            $this->updateMeanings($data);

            // Update standalone translations
            $this->updateTranslations($data);

            // Update synonyms
            $this->updateSynonyms($data);

            // Update antonyms
            $this->updateAntonyms($data);

            // Commit transaction
            DB::commit();

            $this->updateSuccess = true;
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "Expression updated successfully"
            ]);

            // Refresh the JSON data to reflect the updated state
            $this->expression = Expression::with(['meanings.translations', 'translations'])->find($this->expression->id);
            $this->jsonData = $this->generateJsonFromExpression($this->expression);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = "Error updating expression: " . $e->getMessage();
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Error updating expression: " . $e->getMessage()
            ]);
        }

        $this->processing = false;
    }

    // Update basic expression data
    private function updateExpressionData($data)
    {
        // Update basic expression fields
        $this->expression->expression = $data['expression'] ?? $this->expression->expression;
        $this->expression->slug = $data['slug'] ?? $this->expression->slug;
        $this->expression->type = $data['type'] ?? $this->expression->type;
        $this->expression->source = $data['source'] ?? $this->expression->source;

        // Update pronunciation if provided
        if (isset($data['pronunciation']) && is_array($data['pronunciation'])) {
            $this->expression->pronunciation = $data['pronunciation'];
        }

        $this->expression->save();
    }

    // Update meanings and their translations
    private function updateMeanings($data)
    {
        if (!isset($data['meanings']) || !is_array($data['meanings'])) {
            return;
        }

        // Get existing meaning IDs
        $existingMeaningIds = $this->expression->meanings->pluck('id')->toArray();
        $updatedMeaningIds = [];

        foreach ($data['meanings'] as $meaningData) {
            if (empty($meaningData['meaning'])) {
                continue; // Skip empty meanings
            }

            // If ID is provided and exists, update the meaning
            if (isset($meaningData['id']) && in_array($meaningData['id'], $existingMeaningIds)) {
                $meaning = ExpressionMeaning::find($meaningData['id']);
                if ($meaning) {
                    $meaning->meaning = $meaningData['meaning'];
                    $meaning->slug = $meaningData['slug'] ?? Str::slug($meaningData['meaning']);
                    $meaning->source = $meaningData['source'] ?? 'json';
                    $meaning->save();
                    $updatedMeaningIds[] = $meaning->id;
                }
            } else {
                // Create new meaning
                $meaning = new ExpressionMeaning([
                    'meaning' => $meaningData['meaning'],
                    'slug' => $meaningData['slug'] ?? Str::slug($meaningData['meaning']),
                    'source' => $meaningData['source'] ?? 'json'
                ]);
                $this->expression->meanings()->save($meaning);
                $updatedMeaningIds[] = $meaning->id;
            }

            // Update translations for this meaning
            $this->updateMeaningTranslations($meaning, $meaningData);
        }

        // Delete meanings that weren't updated
        $meaningsToDelete = array_diff($existingMeaningIds, $updatedMeaningIds);
        if (!empty($meaningsToDelete)) {
            ExpressionMeaning::whereIn('id', $meaningsToDelete)->delete();
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

        foreach ($meaningData['translations'] as $translationData) {
            if (empty($translationData['locale']) || empty($translationData['translation'])) {
                continue; // Skip empty translations
            }

            // If ID is provided and exists, update the translation
            if (isset($translationData['id']) && in_array($translationData['id'], $existingTranslationIds)) {
                $translation = ExpressionTranslation::find($translationData['id']);
                if ($translation) {
                    $translation->locale = $translationData['locale'];
                    $translation->translation = $translationData['translation'];
                    $translation->transliteration = $translationData['transliteration'] ?? null;
                    $translation->slug = $translationData['slug'] ?? Str::slug($translationData['translation']);
                    $translation->save();
                    $updatedTranslationIds[] = $translation->id;
                }
            } else {
                // Create new translation
                $translation = new ExpressionTranslation([
                    'expression_id' => $this->expression->id,
                    'expression_meaning_id' => $meaning->id,
                    'locale' => $translationData['locale'],
                    'translation' => $translationData['translation'],
                    'transliteration' => $translationData['transliteration'] ?? null,
                    'slug' => $translationData['slug'] ?? Str::slug($translationData['translation']),
                    'source' => $translationData['source'] ?? 'json'
                ]);
                $translation->save();
                $updatedTranslationIds[] = $translation->id;
            }
        }

        // Delete translations that weren't updated
        $translationsToDelete = array_diff($existingTranslationIds, $updatedTranslationIds);
        if (!empty($translationsToDelete)) {
            ExpressionTranslation::whereIn('id', $translationsToDelete)->delete();
        }
    }

    // Update standalone translations (not tied to meanings)
    private function updateTranslations($data)
    {
        if (!isset($data['translations']) || !is_array($data['translations'])) {
            // Remove all standalone translations if not in JSON
            ExpressionTranslation::where('expression_id', $this->expression->id)
                ->whereNull('expression_meaning_id')
                ->delete();
            return;
        }

        // Get existing standalone translation IDs
        $existingTranslationIds = $this->expression->translations()
            ->whereNull('expression_meaning_id')
            ->pluck('id')
            ->toArray();
        $updatedTranslationIds = [];

        foreach ($data['translations'] as $translationData) {
            if (empty($translationData['locale']) || empty($translationData['translation'])) {
                continue; // Skip empty translations
            }

            // If ID is provided and exists, update the translation
            if (isset($translationData['id']) && in_array($translationData['id'], $existingTranslationIds)) {
                $translation = ExpressionTranslation::find($translationData['id']);
                if ($translation) {
                    $translation->locale = $translationData['locale'];
                    $translation->translation = $translationData['translation'];
                    $translation->transliteration = $translationData['transliteration'] ?? null;
                    $translation->slug = $translationData['slug'] ?? Str::slug($translationData['translation']);
                    $translation->save();
                    $updatedTranslationIds[] = $translation->id;
                }
            } else {
                // Create new translation
                $translation = new ExpressionTranslation([
                    'expression_id' => $this->expression->id,
                    'expression_meaning_id' => null,
                    'locale' => $translationData['locale'],
                    'translation' => $translationData['translation'],
                    'transliteration' => $translationData['transliteration'] ?? null,
                    'slug' => $translationData['slug'] ?? Str::slug($translationData['translation']),
                    'source' => $translationData['source'] ?? 'json'
                ]);
                $translation->save();
                $updatedTranslationIds[] = $translation->id;
            }
        }

        // Delete translations that weren't updated
        $translationsToDelete = array_diff($existingTranslationIds, $updatedTranslationIds);
        if (!empty($translationsToDelete)) {
            ExpressionTranslation::whereIn('id', $translationsToDelete)->delete();
        }
    }

    // Update synonyms
    private function updateSynonyms($data)
    {
        // Skip if no synonyms data
        if (!isset($data['synonyms']) || !is_array($data['synonyms'])) {
            return;
        }

        // Remove existing synonym connections
        DB::table('expression_connections')
            ->where(function ($query) {
                $query->where('expression_id_1', $this->expression->id)
                    ->where('type', 'synonyms');
            })
            ->orWhere(function ($query) {
                $query->where('expression_id_2', $this->expression->id)
                    ->where('type', 'synonyms');
            })
            ->delete();

        // Add new synonym connections
        foreach ($data['synonyms'] as $synonymText) {
            // Find or create the synonym expression
            $synonym = Expression::firstOrCreate(
                ['slug' => Str::slug($synonymText)],
                [
                    'expression' => $synonymText,
                    'source' => 'json'
                ]
            );

            // Skip self-reference
            if ($synonym->id === $this->expression->id) {
                continue;
            }

            // Insert new connection
            DB::table('expression_connections')->insert([
                'expression_id_1' => $this->expression->id,
                'expression_id_2' => $synonym->id,
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

        // Remove existing antonym connections
        DB::table('expression_connections')
            ->where(function ($query) {
                $query->where('expression_id_1', $this->expression->id)
                    ->where('type', 'antonyms');
            })
            ->orWhere(function ($query) {
                $query->where('expression_id_2', $this->expression->id)
                    ->where('type', 'antonyms');
            })
            ->delete();

        // Add new antonym connections
        foreach ($data['antonyms'] as $antonymText) {
            // Find or create the antonym expression
            $antonym = Expression::firstOrCreate(
                ['slug' => Str::slug($antonymText)],
                [
                    'expression' => $antonymText,
                    'source' => 'json'
                ]
            );

            // Skip self-reference
            if ($antonym->id === $this->expression->id) {
                continue;
            }

            // Insert new connection
            DB::table('expression_connections')->insert([
                'expression_id_1' => $this->expression->id,
                'expression_id_2' => $antonym->id,
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Expression Using JSON</h3>
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
                    <p>Expression updated successfully!</p>
                </div>
                @endif

                <!-- JSON Input -->
                <div class="mb-4">
                    <div wire:ignore>
                        <x-json-editor
                            label="JSON Data"
                            wire:model.live="jsonData"
                            :content="$jsonData"
                            placeholder='{"expression": "example", "meanings": [...]}'
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
                        Update Expression
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