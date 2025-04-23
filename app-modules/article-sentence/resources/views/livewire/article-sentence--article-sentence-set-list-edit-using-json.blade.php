<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\ArticleSentence\Models\ArticleSentenceSetList;
use Modules\ArticleSentence\Models\ArticleSentenceTranslation;

new class extends Component {
    public $articleSentenceSet;
    public $articleSentenceSetId;
    public $jsonData = '';
    public $showModal = false;
    public $processing = false;
    public $errors = [];
    public $updateSuccess = false;
    public $debugInfo = [];


    // locales supported for translations
    protected $supportedLocales = ['bn', 'hi'];

    public function mount($articleSentenceSet)
    {
        $this->articleSentenceSet      = $articleSentenceSet;
        $this->articleSentenceSetId    = $articleSentenceSet->id;
        $this->generateJsonData();
    }

    private function generateJsonData()
    {
        // Initialize the root data structure with sentence set data
        $sentenceSet = $this->articleSentenceSet;
        
        // Build the root structure with sentence set properties
        $data = [
            'id' => $sentenceSet->id,
            'article_id' => $sentenceSet->article_id,
            'title' => $sentenceSet->title,
            'content' => $sentenceSet->content,
            'display_order' => $sentenceSet->display_order,
            'title_translation' => [
                'bn' => $sentenceSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $sentenceSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $sentenceSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $sentenceSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($sentenceSet->column_order) ? 
                json_decode($sentenceSet->column_order, true) : 
                ($sentenceSet->column_order ?? ['sentence']),
            'sentence_set_lists' => []
        ];

        $sentenceLists = ArticleSentenceSetList::where('article_sentence_set_id', $this->articleSentenceSetId)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($sentenceLists->isNotEmpty()) {
            foreach ($sentenceLists as $sentenceList) {
                $translations = $sentenceList->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($this->supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'sentence_translation' => $translation->translation ?? '',
                        'sentence_transliteration' => $translation->transliteration ?? '',
                    ];
                }

                // Get pronunciation data using correct methods for translatable fields
                $pronunciation = [
                    'bn' => $sentenceList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $sentenceList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                $data['sentence_set_lists'][] = [
                    'id' => $sentenceList->id,
                    'sentence' => $sentenceList->sentence,
                    'slug' => $sentenceList->slug,
                    'pronunciation' => $pronunciation,
                    'display_order' => $sentenceList->display_order,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['sentence_set_lists'] = [
                [
                    'sentence' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'display_order' => 1,
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'sentence_translation' => '',
                            'sentence_transliteration' => '',
                        ],
                        [
                            'locale' => 'hi',
                            'sentence_translation' => '',
                            'sentence_transliteration' => '',
                        ]
                    ]
                ]
            ];
        }

        $this->jsonData = json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->reset(['errors','updateSuccess','debugInfo']);
        $this->generateJsonData();
    }

    public function closeModal() { $this->showModal = false; }

    public function processJson()
    {
        $this->reset(['errors','updateSuccess','debugInfo']);
        $this->processing = true;
        $this->debugInfo[] = "Started processing at " . now()->format('Y-m-d H:i:s');

        $data = json_decode($this->jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            $this->errors[] = 'Invalid JSON: '.json_last_error_msg();
            $this->processing = false;
            return;
        }

        // Verify the data has the expected structure
        if (!isset($data['sentence_set_lists']) || !is_array($data['sentence_set_lists'])) {
            $this->errors[] = "Missing or invalid 'sentence_set_lists' array in JSON data";
            $this->processing = false;
            return;
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            // Initialize arrays to track processed IDs
            $processedIds = [];
            $savedTranslationIds = [];
            
            // 1. Update the Article Sentence Set (parent) data
            if (isset($data['title'])) {
                // Process column_order - ensure it's stored as a JSON string in the database
                $columnOrder = null;
                if (isset($data['column_order'])) {
                    // Ensure column_order is saved as a JSON string, not an array
                    $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
                }
                
                // Prepare sentence set data, excluding id and article_id as requested
                $sentenceSetData = [
                    'title' => $data['title'] ?? $this->articleSentenceSet->title,
                    'content' => $data['content'] ?? $this->articleSentenceSet->content,
                    'display_order' => $data['display_order'] ?? $this->articleSentenceSet->display_order,
                    'column_order' => $columnOrder,
                    // Don't update id or article_id as requested
                ];
                
                // Update the sentence set
                $this->articleSentenceSet->update($sentenceSetData);
                $this->debugInfo[] = "Updated ArticleSentenceSet ID: {$this->articleSentenceSet->id}";
                
                // Handle title_translation directly for bn and hi
                if (isset($data['title_translation'])) {
                    // Set Bengali title translation
                    if (isset($data['title_translation']['bn'])) {
                        $this->articleSentenceSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                        $this->debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                    
                    // Set Hindi title translation
                    if (isset($data['title_translation']['hi'])) {
                        $this->articleSentenceSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                        $this->debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
                
                // Handle content_translation directly for bn and hi
                if (isset($data['content_translation'])) {
                    // Set Bengali content translation
                    if (isset($data['content_translation']['bn'])) {
                        $this->articleSentenceSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                        $this->debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                    
                    // Set Hindi content translation
                    if (isset($data['content_translation']['hi'])) {
                        $this->articleSentenceSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                        $this->debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
                
                // Save the translations
                $this->articleSentenceSet->save();
            }
            
            // 2. Process each sentence in the array
            $processedIds = [];
            $displayOrder = 1;
            
            // Filter out items with empty sentences
            $validItems = [];
            foreach ($data['sentence_set_lists'] as $index => $item) {
                if (!empty($item['sentence'])) {
                    $validItems[] = $item;
                } else {
                    $this->debugInfo[] = "Skipped item #" . ($index + 1) . " with empty sentence";
                }
            }
            
            // Process only valid items
            foreach ($validItems as $index => $item) {
                // Create or update sentence set list using slug as the primary lookup key
                $slug = $item['slug'] ?? Str::slug($item['sentence']);
                
                // Always try to find by slug first
                $sentenceList = ArticleSentenceSetList::where('slug', $slug)
                    ->where('article_sentence_set_id', $this->articleSentenceSetId)
                    ->first();
                    
                if (!$sentenceList) {
                    // Create new record if not found by slug
                    $sentenceList = new ArticleSentenceSetList();
                    $sentenceList->article_sentence_set_id = $this->articleSentenceSetId;
                    $this->debugInfo[] = "Creating new sentence list item with slug: {$slug}";
                } else {
                    $this->debugInfo[] = "Updating existing sentence list item with slug: {$slug}";
                }
                
                $sentenceList->sentence = $item['sentence'];
                $sentenceList->display_order = $displayOrder++;
                
                // Generate a unique slug to avoid duplicates
                $baseSlug = Str::slug($item['sentence']) ?: Str::uuid();
                $slug = $baseSlug;
                $counter = 1;
                
                // Check if this is a new record or we're changing the slug
                if (!$sentenceList->exists || $sentenceList->slug !== $baseSlug) {
                    // Check for duplicate slugs and make unique if needed
                    while (ArticleSentenceSetList::where('article_sentence_set_id', $this->articleSentenceSetId)
                        ->where('slug', $slug)
                        ->where('id', '!=', $sentenceList->id ?? 0)
                        ->exists()) {
                        $slug = $baseSlug . '-' . $counter++;
                    }
                }

                $sentenceList->slug = $slug;
                
                // Handle pronunciation using Spatie's translatable functionality
                if (isset($item['pronunciation']) && is_array($item['pronunciation'])) {
                    // Handle Bengali pronunciation
                    if (isset($item['pronunciation']['bn'])) {
                        $sentenceList->setTranslation('pronunciation', 'bn', $item['pronunciation']['bn']);
                        $this->debugInfo[] = "Set Bengali pronunciation for sentence: {$item['pronunciation']['bn']}";
                    }
                    
                    // Handle Hindi pronunciation
                    if (isset($item['pronunciation']['hi'])) {
                        $sentenceList->setTranslation('pronunciation', 'hi', $item['pronunciation']['hi']);
                        $this->debugInfo[] = "Set Hindi pronunciation for sentence: {$item['pronunciation']['hi']}";
                    }
                } else {
                    // Initialize empty pronunciations if not provided
                    $sentenceList->setTranslation('pronunciation', 'bn', '');
                    $sentenceList->setTranslation('pronunciation', 'hi', '');
                }
                
                $sentenceList->save();

                $processedIds[] = $sentenceList->id;

                // Process translations - only handle sequential format
                if (isset($item['translations']) && is_array($item['translations'])) {
                    $this->debugInfo[] = "Processing translations for sentence: {$item['sentence']}";
                    
                    // Process translations in sequential array format (array of objects with locale key)
                    foreach ($item['translations'] as $trData) {
                        if (!isset($trData['locale']) || !in_array($trData['locale'], $this->supportedLocales)) {
                            $this->debugInfo[] = "Skipping translation with invalid locale: " . ($trData['locale'] ?? 'unknown');
                            continue;
                        }
                        
                        $locale = $trData['locale'];
                        $tr = ArticleSentenceTranslation::where('article_sentence_set_list_id', $sentenceList->id)
                            ->where('locale', $locale)
                            ->first();
                            
                        if (!$tr) {
                            $tr = new ArticleSentenceTranslation();
                            $tr->article_sentence_set_list_id = $sentenceList->id;
                            $tr->locale = $locale;
                            $this->debugInfo[] = "Created new translation for locale: {$locale}";
                        } else {
                            $this->debugInfo[] = "Updating existing translation for locale: {$locale}";
                        }
                        
                        $tr->translation = $trData['sentence_translation'] ?? '';
                        $tr->transliteration = $trData['sentence_transliteration'] ?? '';
                        $tr->save();
                        $savedTranslationIds[] = $tr->id;
                    }
                }
            }

            // Delete missing translations
            ArticleSentenceTranslation::whereIn('article_sentence_set_list_id', $processedIds)
                ->whereNotIn('id', $savedTranslationIds)
                ->delete();
            $this->debugInfo[] = "Deleted translations that are no longer present in the input";

            // Delete records not in the processed list
            $deletedCount = ArticleSentenceSetList::where('article_sentence_set_id', $this->articleSentenceSetId)
                ->whereNotIn('id', $processedIds)
                ->delete();
            $this->debugInfo[] = "Deleted {$deletedCount} sentence list items that are no longer present in the input";

            // If no errors, commit the transaction
            if (count($this->errors) === 0) {
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Sentence Set & Sentence List using JSON</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Set: {{ $articleSentenceSet->title }}</p>
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
                    <p>Sentence set list updated successfully!</p>
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
                    <a href="/sample-data/article-sentence/article-sentence-set-list-sample.json" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            View Sample JSON Format
                        </span>
                    </a>
                </div>
            </div>

            <div wire:ignore>
                <x-json-editor
                    label=""
                    wire:model.live="jsonData"
                    :content="$jsonData"
                    placeholder='{"title": "Example Set", "sentence_set_lists": [{"sentence": "example", "translations": [...]}]}'
                    model-name="jsonData"
                />
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
                        Update Sentence Set List
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>