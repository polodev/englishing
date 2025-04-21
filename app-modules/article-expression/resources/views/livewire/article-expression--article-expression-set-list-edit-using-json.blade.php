<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\ArticleExpression\Models\ArticleExpressionSetList;
use Modules\ArticleExpression\Models\ArticleExpressionTranslation;

new class extends Component {
    public $articleExpressionSet;
    public $articleExpressionSetId;
    public $jsonData = '';
    public $showModal = false;
    public $processing = false;
    public $errors = [];
    public $updateSuccess = false;
    public $debugInfo = [];

    // Supported locales for translations
    protected $supportedLocales = ['bn', 'hi'];

    public function mount($articleExpressionSet)
    {
        $this->articleExpressionSet = $articleExpressionSet;
        $this->articleExpressionSetId = $articleExpressionSet->id;
        $this->generateJsonData();
    }

    // Generate JSON representation of the expression set list items
    private function generateJsonData()
    {
        $expressionSetLists = ArticleExpressionSetList::where('article_expression_set_id', $this->articleExpressionSetId)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($expressionSetLists->isNotEmpty()) {
            $data = [];

            foreach ($expressionSetLists as $expressionSetList) {
                $translations = $expressionSetList->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($this->supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'expression_translation' => $translation->expression_translation ?? '',
                        'expression_transliteration' => $translation->expression_transliteration ?? '',
                        'example_sentence_translation' => $translation->example_sentence_translation ?? '',
                        'example_sentence_transliteration' => $translation->example_sentence_transliteration ?? '',
                        'source' => $translation->source ?? 'oxford'
                    ];
                }

                // Get pronunciation data
                $pronunciation = [];
                if ($expressionSetList->pronunciation) {
                    try {
                        $pronunciation = json_decode($expressionSetList->pronunciation, true);
                        if (!is_array($pronunciation)) {
                            $pronunciation = ['bn_pronunciation' => '', 'hi_pronunciation' => ''];
                        }
                    } catch (\Exception $e) {
                        $pronunciation = ['bn_pronunciation' => '', 'hi_pronunciation' => ''];
                    }
                } else {
                    $pronunciation = ['bn_pronunciation' => '', 'hi_pronunciation' => ''];
                }

                $data[] = [
                    'id' => $expressionSetList->id,
                    'expression' => $expressionSetList->expression,
                    'type' => $expressionSetList->type ?? 'idiom',
                    'slug' => $expressionSetList->slug,
                    'pronunciation' => $pronunciation,
                    'meaning' => $expressionSetList->meaning,
                    'example_sentence' => $expressionSetList->example_sentence,
                    'display_order' => $expressionSetList->display_order,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data = [
                [
                    'expression' => '',
                    'type' => 'idiom',
                    'pronunciation' => [
                        'bn_pronunciation' => '',
                        'hi_pronunciation' => ''
                    ],
                    'meaning' => '',
                    'example_sentence' => '',
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'expression_translation' => '',
                            'expression_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                            'source' => 'oxford'
                        ],
                        [
                            'locale' => 'hi',
                            'expression_translation' => '',
                            'expression_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                            'source' => 'oxford'
                        ]
                    ]
                ]
            ];
        }

        $this->jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->debugInfo[] = 'Generated JSON data';
    }

    // Open the modal
    public function openModal()
    {
        $this->showModal = true;
        $this->reset(['errors', 'updateSuccess', 'debugInfo']);
        $this->generateJsonData(); // Refresh data when opening modal
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['errors', 'updateSuccess', 'debugInfo']);
    }

    // Process JSON data and update expression set list items
    public function processJson()
    {
        $this->reset(['errors', 'updateSuccess', 'debugInfo']);
        $this->processing = true;

        try {
            // Decode JSON data
            $data = json_decode($this->jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errors[] = "Invalid JSON: " . json_last_error_msg();
                $this->processing = false;
                return;
            }

            // Basic validation
            if (!is_array($data)) {
                $this->errors[] = "Data must be an array of expressions";
                $this->processing = false;
                return;
            }

            DB::beginTransaction();
            $this->debugInfo[] = "Started database transaction";

            // Process each expression in the array
            $processedIds = [];
            $displayOrder = 1;

            foreach ($data as $index => $item) {
                // Validate required fields
                if (empty($item['expression'])) {
                    $this->errors[] = "Item #" . ($index + 1) . " is missing required field 'expression'";
                    continue;
                }

                // Create or update expression set list
                $slug = $item['slug'] ?? Str::slug($item['expression']);
                $expressionSetListId = $item['id'] ?? null;

                if ($expressionSetListId) {
                    // Update existing record
                    $expressionSetList = ArticleExpressionSetList::where('id', $expressionSetListId)
                        ->where('article_expression_set_id', $this->articleExpressionSetId)
                        ->first();

                    if (!$expressionSetList) {
                        $this->errors[] = "Item #" . ($index + 1) . " with ID " . $expressionSetListId . " not found or doesn't belong to this set";
                        continue;
                    }
                } else {
                    // Create new record
                    $expressionSetList = new ArticleExpressionSetList();
                    $expressionSetList->article_expression_set_id = $this->articleExpressionSetId;
                }

                // Update fields
                $expressionSetList->expression = $item['expression'];
                $expressionSetList->type = $item['type'] ?? 'idiom';
                $expressionSetList->slug = $slug;
                $expressionSetList->meaning = $item['meaning'] ?? null;
                $expressionSetList->example_sentence = $item['example_sentence'] ?? null;
                $expressionSetList->display_order = $item['display_order'] ?? $displayOrder;
                
                // Handle pronunciation as JSON
                if (isset($item['pronunciation']) && is_array($item['pronunciation'])) {
                    $expressionSetList->pronunciation = json_encode($item['pronunciation']);
                }

                $expressionSetList->save();
                $processedIds[] = $expressionSetList->id;
                $displayOrder++;

                // Process translations
                if (isset($item['translations']) && is_array($item['translations'])) {
                    foreach ($item['translations'] as $translationData) {
                        $locale = $translationData['locale'] ?? null;
                        $translationId = $translationData['id'] ?? null;

                        if (!$locale || !in_array($locale, $this->supportedLocales)) {
                            $this->errors[] = "Item #" . ($index + 1) . " has an invalid locale: " . ($locale ?: 'undefined');
                            continue;
                        }

                        if ($translationId) {
                            // Update existing translation
                            $translation = ArticleExpressionTranslation::where('id', $translationId)
                                ->where('article_expression_set_list_id', $expressionSetList->id)
                                ->where('locale', $locale)
                                ->first();

                            if (!$translation) {
                                $translation = ArticleExpressionTranslation::where('article_expression_set_list_id', $expressionSetList->id)
                                    ->where('locale', $locale)
                                    ->first();
                                    
                                if (!$translation) {
                                    $translation = new ArticleExpressionTranslation();
                                    $translation->article_expression_set_list_id = $expressionSetList->id;
                                    $translation->locale = $locale;
                                }
                            }
                        } else {
                            // Create new translation or find existing one by locale
                            $translation = ArticleExpressionTranslation::where('article_expression_set_list_id', $expressionSetList->id)
                                ->where('locale', $locale)
                                ->first();
                                
                            if (!$translation) {
                                $translation = new ArticleExpressionTranslation();
                                $translation->article_expression_set_list_id = $expressionSetList->id;
                                $translation->locale = $locale;
                            }
                        }

                        // Update translation fields
                        $translation->expression_translation = $translationData['expression_translation'] ?? null;
                        $translation->expression_transliteration = $translationData['expression_transliteration'] ?? null;
                        $translation->example_sentence_translation = $translationData['example_sentence_translation'] ?? null;
                        $translation->example_sentence_transliteration = $translationData['example_sentence_transliteration'] ?? null;
                        $translation->source = $translationData['source'] ?? 'oxford';
                        $translation->save();
                    }
                }
            }

            // Delete any expression items that weren't processed (if they exist in DB but not in JSON)
            $expressionItemsToDelete = ArticleExpressionSetList::where('article_expression_set_id', $this->articleExpressionSetId)
                ->whereNotIn('id', $processedIds)
                ->get();
                
            foreach ($expressionItemsToDelete as $item) {
                $this->debugInfo[] = "Deleting expression item: " . $item->expression;
                $item->delete();
            }

            if (empty($this->errors)) {
                DB::commit();
                $this->updateSuccess = true;
                $this->debugInfo[] = "Committed database transaction";
            } else {
                DB::rollBack();
                $this->debugInfo[] = "Rolled back database transaction due to errors";
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = "Error: " . $e->getMessage();
            $this->debugInfo[] = "Exception caught: " . $e->getMessage();
        }

        $this->processing = false;
    }
};
?>

<div>
    <!-- Button to open modal -->
    <button wire:click="openModal" class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
        </svg>
        Edit Using JSON
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
        <div class="fixed inset-0 transform transition-all" wire:click="closeModal">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-800 opacity-75"></div>
        </div>

        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-4xl mx-auto">
            <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Expression Set List using JSON</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Set: {{ $articleExpressionSet->title }}</p>
            </div>

            <div class="p-6 dark:bg-gray-800">
                <!-- Error Messages -->
                @if(count($errors) > 0)
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-200">
                    <h4 class="font-bold mb-2">Error(s):</h4>
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
                    <p>Expression set list updated successfully!</p>
                </div>
                @endif

                <!-- Debug Info -->
                @if(count($debugInfo) > 0)
                <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-900 border-l-4 border-gray-500 text-gray-700 dark:text-gray-200">
                    <h4 class="font-bold mb-2">Debug Info:</h4>
                    <ul class="list-disc pl-5">
                        @foreach($debugInfo as $info)
                        <li>{{ $info }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Sample JSON Link -->
                <div class="mb-4">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">JSON Data</label>
                        <a href="/sample-data/article-expression/article-expression-set-list-sample.json" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                View Sample JSON Format
                            </span>
                        </a>
                    </div>
                </div>

                <!-- JSON Editor -->
                <div class="mb-4">
                    <div wire:ignore>
                        <x-json-editor
                            label=""
                            wire:model.live="jsonData"
                            :content="$jsonData"
                            placeholder='[{"expression": "example", "translations": [...]}]'
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
                        Update Expression Set List
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>