<?php

namespace Modules\Expression\Http\Livewire;

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Expression\Models\Expression;
use Modules\Expression\Models\ExpressionMeaning;
use Modules\Expression\Models\ExpressionTranslation;

new class extends Component {
    public bool $showModal = false;
    public string $jsonData = '';
    public array $createdExpressions = [];
    public array $errors = [];
    public bool $showSampleLink = true;
    public bool $processing = false;
    
    // For slug validation
    public bool $slugExists = false;
    public ?int $existingExpressionId = null;
    public ?string $existingExpressionName = null;

    // Open the modal
    public function openModal()
    {
        $this->reset(['jsonData', 'createdExpressions', 'errors', 'processing']);
        $this->showModal = true;
        $this->showSampleLink = true;
    }

    // Listen for the openJsonImportModal event
    #[On('openJsonImportModal')]
    public function handleOpenModal()
    {
        $this->openModal();
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Process JSON data and create expressions
    public function processJson()
    {
        $this->reset(['createdExpressions', 'errors']);
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

            // Validate structure
            if (!isset($data['expressions']) || !is_array($data['expressions'])) {
                $this->errors[] = "Invalid JSON structure. Expected an 'expressions' array.";
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Invalid JSON structure. Expected an 'expressions' array."
                ]);
                $this->processing = false;
                return;
            }

            // Process each expression
            $totalProcessed = 0;
            $totalCreated = 0;
            $totalUpdated = 0;
            $totalErrors = 0;

            DB::beginTransaction();
            try {
                foreach ($data['expressions'] as $expressionData) {
                    $result = $this->createExpressionFromData($expressionData);
                    if ($result) {
                        $this->createdExpressions[] = $result;
                        $totalProcessed++;
                        if ($result['status'] === 'created') {
                            $totalCreated++;
                        } else {
                            $totalUpdated++;
                        }
                    } else {
                        $totalErrors++;
                    }
                }
                
                // Only commit if there were no errors
                if (count($this->errors) === 0) {
                    DB::commit();
                    $this->dispatch('toast', [
                        'type' => 'success',
                        'message' => "Processed $totalProcessed expressions: $totalCreated created, $totalUpdated updated"
                    ]);
                } else {
                    // If there were errors, rollback and show error toast
                    DB::rollBack();
                    $this->dispatch('toast', [
                        'type' => 'error',
                        'message' => "Encountered " . count($this->errors) . " errors while processing expressions."
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Error processing expressions: " . $e->getMessage();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Error processing expressions: " . $e->getMessage()
                ]);
            }

            $this->processing = false;
            $this->showSampleLink = false;
        } catch (\Exception $e) {
            $this->errors[] = "Error: " . $e->getMessage();
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Error: " . $e->getMessage()
            ]);
            $this->processing = false;
        }
    }

    // Create an expression from the provided data
    private function createExpressionFromData($expressionData)
    {
        try {
            // Validate required fields
            if (!isset($expressionData['expression']) || empty($expressionData['expression'])) {
                $this->errors[] = "Expression is required.";
                return null;
            }

            // Generate slug if not provided
            if (!isset($expressionData['slug']) || empty($expressionData['slug'])) {
                $expressionData['slug'] = Str::slug($expressionData['expression']);
            }

            // Create or find the expression
            $expression = Expression::firstOrCreate(
                ['slug' => $expressionData['slug']],
                [
                    'expression' => $expressionData['expression'],
                    'type' => $expressionData['type'] ?? null,
                    'source' => 'json',
                ]
            );

            $result = $expression->wasRecentlyCreated ? 'created' : 'updated';

            // For existing expressions, update fields if provided
            if (!$expression->wasRecentlyCreated) {
                $needsUpdate = false;

                if (isset($expressionData['type']) && $expression->type !== $expressionData['type']) {
                    $expression->type = $expressionData['type'];
                    $needsUpdate = true;
                }

                if ($expression->source !== 'json') {
                    $expression->source = 'json';
                    $needsUpdate = true;
                }

                if ($needsUpdate) {
                    $expression->save();
                }
            }

            // Handle pronunciations
            if (isset($expressionData['pronunciation'])) {
                $pronunciationData = [];
                
                if (isset($expressionData['pronunciation']['bn_pronunciation'])) {
                    $pronunciationData['bn'] = $expressionData['pronunciation']['bn_pronunciation'];
                }
                if (isset($expressionData['pronunciation']['hi_pronunciation'])) {
                    $pronunciationData['hi'] = $expressionData['pronunciation']['hi_pronunciation'];
                }
                if (isset($expressionData['pronunciation']['es_pronunciation'])) {
                    $pronunciationData['es'] = $expressionData['pronunciation']['es_pronunciation'];
                }
                
                if (!empty($pronunciationData)) {
                    $expression->pronunciation = $pronunciationData;
                    $expression->save();
                }
            }

            // Process meanings
            if (isset($expressionData['meanings']) && is_array($expressionData['meanings'])) {
                foreach ($expressionData['meanings'] as $index => $meaningData) {
                    $this->createMeaningFromData($expression, $meaningData, $index + 1);
                }
            }

            // Process connections (synonyms and antonyms)
            if (isset($expressionData['synonyms'])) {
                $synonyms = is_array($expressionData['synonyms'])
                    ? $expressionData['synonyms']
                    : array_map('trim', explode(',', $expressionData['synonyms']));

                $this->processExpressionConnections($expression, $synonyms, 'synonyms');
            }

            if (isset($expressionData['antonyms'])) {
                $antonyms = is_array($expressionData['antonyms'])
                    ? $expressionData['antonyms']
                    : array_map('trim', explode(',', $expressionData['antonyms']));

                $this->processExpressionConnections($expression, $antonyms, 'antonyms');
            }

            // Add to created expressions list
            return [
                'id' => $expression->id,
                'expression' => $expression->expression,
                'slug' => $expression->slug,
                'status' => $result
            ];

        } catch (\Exception $e) {
            $this->errors[] = "Error creating expression '{$expressionData['expression']}': " . $e->getMessage();
            return null;
        }
    }

    // Process expression connections (synonyms or antonyms)
    private function processExpressionConnections($expression, $connections, $type)
    {
        foreach ($connections as $connection) {
            $connection = trim($connection);
            if (empty($connection)) continue;

            // Create or find the related expression
            $relatedExpression = Expression::firstOrCreate(
                ['slug' => Str::slug($connection)],
                [
                    'expression' => $connection,
                ]
            );

            $this->createExpressionConnection($expression, $relatedExpression, $type);
        }
    }

    // Create a meaning from the provided data
    private function createMeaningFromData($expression, $meaningData, $displayOrder = 1)
    {
        try {
            // Validate required fields
            if (!isset($meaningData['meaning']) || empty($meaningData['meaning'])) {
                $this->errors[] = "Meaning is required for expression '{$expression->expression}'.";
                return null;
            }

            // Generate slug if not provided
            if (!isset($meaningData['slug']) || empty($meaningData['slug'])) {
                $meaningData['slug'] = Str::slug(substr($meaningData['meaning'], 0, 250));
            }

            // Create or find the meaning
            $meaning = ExpressionMeaning::firstOrCreate(
                [
                    'expression_id' => $expression->id,
                    'slug' => $meaningData['slug']
                ],
                [
                    'meaning' => $meaningData['meaning'],
                    'source' => $meaningData['source'] ?? 'json',
                ]
            );

            // Process translations
            if (isset($meaningData['translations']) && is_array($meaningData['translations'])) {
                foreach ($meaningData['translations'] as $translationData) {
                    $this->createTranslationFromData($expression, $meaning, $translationData);
                }
            }

            return $meaning;
        } catch (\Exception $e) {
            $this->errors[] = "Error creating meaning for expression '{$expression->expression}': " . $e->getMessage();
            return null;
        }
    }

    // Create a translation from the provided data
    private function createTranslationFromData($expression, $meaning, $translationData)
    {
        try {
            // Validate required fields
            if (!isset($translationData['locale']) || empty($translationData['locale'])) {
                $this->errors[] = "Locale is required for translation.";
                return;
            }

            // Skip if translation is missing instead of showing an error
            if (!isset($translationData['translation']) || empty($translationData['translation'])) {
                return; // Skip this translation silently
            }

            // Generate slug if not provided
            if (!isset($translationData['slug']) || empty($translationData['slug'])) {
                $translationText = $translationData['translation'] ?? '';
                $slug = Str::slug(substr($translationText, 0, 250));
                
                // If we can't generate a valid slug, skip this translation
                if (empty($slug)) {
                    return; // Skip this translation silently
                }
                
                $translationData['slug'] = $slug;
            }

            // Check if translation already exists for this expression, meaning, and locale
            $existingTranslation = ExpressionTranslation::where('expression_meaning_id', $meaning->id)
                ->where('locale', $translationData['locale'])
                ->where('slug', $translationData['slug'])
                ->first();

            if ($existingTranslation) {
                // Update existing translation
                $existingTranslation->translation = $translationData['translation'];
                $existingTranslation->transliteration = $translationData['transliteration'] ?? null;
                $existingTranslation->source = $translationData['source'] ?? 'json';
                $existingTranslation->save();
            } else {
                // Create new translation with explicit slug
                ExpressionTranslation::create([
                    'expression_id' => $expression->id,
                    'expression_meaning_id' => $meaning->id,
                    'locale' => $translationData['locale'],
                    'translation' => $translationData['translation'],
                    'transliteration' => $translationData['transliteration'] ?? null,
                    'source' => $translationData['source'] ?? 'json',
                    'slug' => $translationData['slug'],
                ]);
            }
        } catch (\Exception $e) {
            $this->errors[] = "Error creating translation for expression '{$expression->expression}', meaning '{$meaning->meaning}': " . $e->getMessage();
        }
    }

    // Create an expression connection (synonym or antonym)
    private function createExpressionConnection($expression, $relatedExpression, $type)
    {
        // Skip if trying to connect an expression to itself
        if ($expression->id === $relatedExpression->id) {
            return;
        }

        // Check if connection already exists (in either direction)
        $existingConnection1 = DB::table('expression_connections')
            ->where('expression_id_1', $expression->id)
            ->where('expression_id_2', $relatedExpression->id)
            ->where('type', $type)
            ->exists();

        $existingConnection2 = DB::table('expression_connections')
            ->where('expression_id_1', $relatedExpression->id)
            ->where('expression_id_2', $expression->id)
            ->where('type', $type)
            ->exists();

        // Create the connection if it doesn't exist
        if (!$existingConnection1 && !$existingConnection2) {
            DB::table('expression_connections')->insert([
                'expression_id_1' => $expression->id,
                'expression_id_2' => $relatedExpression->id,
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // Check if the slug already exists in the database
    private function checkSlugExists()
    {
        if (empty($this->slug)) {
            return true;
        }

        $existingExpression = Expression::where('slug', $this->slug)->first();
        
        if ($existingExpression) {
            $this->slugExists = true;
            $this->existingExpressionId = $existingExpression->id;
            $this->existingExpressionName = $existingExpression->expression;
            return false;
        }
        
        $this->slugExists = false;
        $this->existingExpressionId = null;
        $this->existingExpressionName = null;
        return true;
    }
};

?>

<div>
    <!-- Button to open modal -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent dark:border-gray-500 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
    >
        Import Expressions from JSON
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Import Expressions from JSON</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 dark:bg-gray-800">
                <!-- Sample Link -->
                @if($showSampleLink)
                <div class="mb-4">
                    <a
                        href="/sample-data/expression/expression-sample.json"
                        target="_blank"
                        class="inline-flex items-center px-3 py-1 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                    >
                        View Sample JSON Format
                    </a>
                </div>
                @endif

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

                <!-- Created Expressions -->
                @if(count($createdExpressions) > 0)
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200">
                    <h4 class="font-bold mb-2">Created/Updated Expressions:</h4>
                    <ul class="list-disc pl-5">
                        @foreach($createdExpressions as $expression)
                        <li>
                            <a href="{{ route('backend::expressions.show', $expression['id']) }}" class="text-blue-500 dark:text-blue-400 hover:underline" target="_blank">
                                {{ $expression['expression'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- JSON Input -->
                <div class="mb-4">
                    <div wire:ignore>
                        <x-json-editor
                            label="JSON Data"
                            wire:model.live="jsonData"
                            :content="$jsonData"
                            placeholder='{"expressions": [...]}'
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
                        Process JSON
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>