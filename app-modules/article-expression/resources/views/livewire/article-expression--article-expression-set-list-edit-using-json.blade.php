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

    // Generate JSON representation of the expression set and its list items
    private function generateJsonData()
    {
        // Initialize the root data structure with expression set data
        $expressionSet = $this->articleExpressionSet;

        // Build the root structure with expression set properties
        $data = [
            'id' => $expressionSet->id,
            'article_id' => $expressionSet->article_id,
            'title' => $expressionSet->title,
            'content' => $expressionSet->content,
            'display_order' => $expressionSet->display_order,
            'title_translation' => [
                'bn' => $expressionSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $expressionSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $expressionSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $expressionSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($expressionSet->column_order) ?
                json_decode($expressionSet->column_order, true) :
                ($expressionSet->column_order ?? ['expression', 'type', 'meaning', 'example_sentence']),
            'expression_set_lists' => []
        ];

        $expressionSetLists = ArticleExpressionSetList::where('article_expression_set_id', $this->articleExpressionSetId)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($expressionSetLists->isNotEmpty()) {
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

                // Get pronunciation data using Spatie's getTranslation method directly
                $pronunciation = [
                    'bn' => $expressionSetList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $expressionSetList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                $data['expression_set_lists'][] = [
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
            $data['expression_set_lists'] = [
                [
                    'expression' => '',
                    'type' => 'idiom',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
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
        $this->debugInfo[] = 'Generated JSON data with expression set and lists';
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

    // Process JSON data and update expression set and list items
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
                $this->errors[] = "Data must be an object with expression set properties";
                $this->processing = false;
                return;
            }

            // Start a database transaction to ensure all changes are committed or rolled back together
            DB::beginTransaction();
            $this->debugInfo[] = "Started database transaction";

            // 1. Update the Article Expression Set (parent) data
            if (isset($data['title'])) {
                // Process column_order - ensure it's stored as a JSON string in the database
                $columnOrder = null;
                if (isset($data['column_order'])) {
                    // Ensure column_order is saved as a JSON string, not an array
                    $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
                }

                // Prepare expression set data, excluding id and article_id as requested
                $expressionSetData = [
                    'title' => $data['title'] ?? $this->articleExpressionSet->title,
                    'content' => $data['content'] ?? $this->articleExpressionSet->content,
                    'display_order' => $data['display_order'] ?? $this->articleExpressionSet->display_order,
                    'column_order' => $columnOrder,
                    // Don't update id or article_id as requested
                ];

                // Update the expression set
                $this->articleExpressionSet->update($expressionSetData);
                $this->debugInfo[] = "Updated ArticleExpressionSet ID: {$this->articleExpressionSet->id}";

                // Handle title_translation directly for bn and hi
                if (isset($data['title_translation'])) {
                    // Set Bengali title translation
                    if (isset($data['title_translation']['bn'])) {
                        $this->articleExpressionSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                        $this->debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }

                    // Set Hindi title translation
                    if (isset($data['title_translation']['hi'])) {
                        $this->articleExpressionSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                        $this->debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }

                // Handle content_translation directly for bn and hi
                if (isset($data['content_translation'])) {
                    // Set Bengali content translation
                    if (isset($data['content_translation']['bn'])) {
                        $this->articleExpressionSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                        $this->debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }

                    // Set Hindi content translation
                    if (isset($data['content_translation']['hi'])) {
                        $this->articleExpressionSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                        $this->debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }

                // Save the translations
                $this->articleExpressionSet->save();
            }

            // 2. Update the Article Expression Set List items
            // Process each expression in the array
            $processedIds = [];
            $displayOrder = 1;

            if (!isset($data['expression_set_lists']) || !is_array($data['expression_set_lists'])) {
                $this->errors[] = "Missing or invalid 'expression_set_lists' array in JSON data";
                DB::rollBack();
                $this->processing = false;
                return;
            }

            // Filter out items with empty expressions instead of treating them as errors
            $validItems = [];
            foreach ($data['expression_set_lists'] as $index => $item) {
                if (!empty($item['expression'])) {
                    $validItems[] = $item;
                } else {
                    $this->debugInfo[] = "Skipped item #" . ($index + 1) . " with empty expression";
                }
            }

            // Process only valid items
            foreach ($validItems as $index => $item) {

                // Create or update expression set list using slug as the primary lookup key
                $slug = $item['slug'] ?? Str::slug($item['expression']);

                // Always try to find by slug first
                $expressionSetList = ArticleExpressionSetList::where('slug', $slug)
                    ->where('article_expression_set_id', $this->articleExpressionSetId)
                    ->first();

                if (!$expressionSetList) {
                    // Create new record if not found by slug
                    $expressionSetList = new ArticleExpressionSetList();
                    $expressionSetList->article_expression_set_id = $this->articleExpressionSetId;
                    $this->debugInfo[] = "Creating new expression list item with slug: {$slug}";
                } else {
                    $this->debugInfo[] = "Updating existing expression list item with slug: {$slug}";
                }

                // Update fields
                $expressionSetList->expression = $item['expression'];
                $expressionSetList->type = $item['type'] ?? 'idiom';
                $expressionSetList->slug = $slug;
                $expressionSetList->meaning = $item['meaning'] ?? null;
                $expressionSetList->example_sentence = $item['example_sentence'] ?? null;
                $expressionSetList->display_order = $item['display_order'] ?? $displayOrder;

                // Handle pronunciation as JSON
                // Handle pronunciation using Spatie's translatable functionality
                if (isset($item['pronunciation']) && is_array($item['pronunciation'])) {
                    // Handle Bengali pronunciation - support both 'bn' and 'bn_pronunciation' keys
                    $bnValue = $item['pronunciation']['bn'] ?? $item['pronunciation']['bn_pronunciation'] ?? null;
                    if ($bnValue !== null) {
                        $expressionSetList->setTranslation('pronunciation', 'bn', $bnValue);
                        $this->debugInfo[] = "Set Bengali pronunciation for expression '{$item['expression']}': {$bnValue}";
                    }

                    // Handle Hindi pronunciation - support both 'hi' and 'hi_pronunciation' keys
                    $hiValue = $item['pronunciation']['hi'] ?? $item['pronunciation']['hi_pronunciation'] ?? null;
                    if ($hiValue !== null) {
                        $expressionSetList->setTranslation('pronunciation', 'hi', $hiValue);
                        $this->debugInfo[] = "Set Hindi pronunciation for expression '{$item['expression']}': {$hiValue}";
                    }
                } else {
                    // Initialize empty pronunciations if not provided
                    $expressionSetList->setTranslation('pronunciation', 'bn', '');
                    $expressionSetList->setTranslation('pronunciation', 'hi', '');
                    $this->debugInfo[] = "Initialized empty pronunciations for expression '{$item['expression']}'";
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
            // Ensure transaction is rolled back
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            // Provide more detailed error information
            $this->errors[] = "Error: " . $e->getMessage();
            $this->debugInfo[] = "Exception caught: " . $e->getMessage();
            $this->debugInfo[] = "Error code: " . $e->getCode();

            // For SQL errors, try to extract more detailed information
            if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
                $this->debugInfo[] = "SQL error detected - check for duplicate entries or constraint violations";
            }
        }

        $this->processing = false;

        // Refresh the JSON data with the updated state
        if ($this->updateSuccess) {
            $this->generateJsonData();
        }
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Expression Set & Expression List using JSON</h3>
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
                            placeholder='{"title": "Example Set", "expression_set_lists": [{"expression": "example", "translations": [...]}]}'
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