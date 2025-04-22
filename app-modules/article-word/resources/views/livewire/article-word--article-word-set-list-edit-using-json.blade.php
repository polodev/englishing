<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\ArticleWord\Models\ArticleWordSetList;
use Modules\ArticleWord\Models\ArticleWordTranslation;

new class extends Component {
    public $articleWordSet;
    public $articleWordSetId;
    public $jsonData = '';
    public $showModal = false;
    public $processing = false;
    public $errors = [];
    public $updateSuccess = false;
    public $debugInfo = [];

    // Supported locales for translations
    protected $supportedLocales = ['bn', 'hi'];

    public function mount($articleWordSet)
    {
        $this->articleWordSet = $articleWordSet;
        $this->articleWordSetId = $articleWordSet->id;
        $this->generateJsonData();
    }

    // Generate JSON representation of the word set list items
    private function generateJsonData()
    {
        // Get the article word set data
        $wordSet = $this->articleWordSet;
        $wordSetLists = ArticleWordSetList::where('article_word_set_id', $this->articleWordSetId)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        // Initialize the data structure with word set information
        $data = [
            'id' => $wordSet->id,
            'article_id' => $wordSet->article_id,
            'title' => $wordSet->title,
            'content' => $wordSet->content,
            'display_order' => $wordSet->display_order,
            'static_content_1' => $wordSet->static_content_1 ?? '',
            'static_content_2' => $wordSet->static_content_2 ?? '',
            'title_translation' => [
                'bn' => $wordSet->getTranslation('title_translation', 'bn', false) ?: '',
                'hi' => $wordSet->getTranslation('title_translation', 'hi', false) ?: ''
            ],
            'content_translation' => [
                'bn' => $wordSet->getTranslation('content_translation', 'bn', false) ?: '',
                'hi' => $wordSet->getTranslation('content_translation', 'hi', false) ?: ''
            ],
            'column_order' => is_string($wordSet->column_order) ? json_decode($wordSet->column_order, true) : ($wordSet->column_order ?? ['word', 'phonetic', 'meaning', 'example_sentence']),
            'word_set_lists' => []
        ];

        if ($wordSetLists->isNotEmpty()) {
            foreach ($wordSetLists as $wordSetList) {
                $translations = $wordSetList->translations->keyBy('locale');
                $formattedTranslations = [];

                // Ensure all supported locales are present
                foreach ($this->supportedLocales as $locale) {
                    $translation = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $translation->id ?? null,
                        'locale' => $locale,
                        'word_translation' => $translation->word_translation ?? '',
                        'word_transliteration' => $translation->word_transliteration ?? '',
                        'example_sentence_translation' => $translation->example_sentence_translation ?? '',
                        'example_sentence_transliteration' => $translation->example_sentence_transliteration ?? '',
                        'example_expression_translation' => $translation->example_expression_translation ?? '',
                        'example_expression_transliteration' => $translation->example_expression_transliteration ?? '',
                        'source' => $translation->source ?? 'oxford'
                    ];
                }

                // Get pronunciation data using Spatie's getTranslation method
                $pronunciation = [
                    'bn' => $wordSetList->getTranslation('pronunciation', 'bn', false) ?: '',
                    'hi' => $wordSetList->getTranslation('pronunciation', 'hi', false) ?: ''
                ];

                // Format synonyms and antonyms (could be string or array in JSON)
                $synonyms = $wordSetList->synonyms;
                $antonyms = $wordSetList->antonyms;

                $data['word_set_lists'][] = [
                    'id' => $wordSetList->id,
                    'word' => $wordSetList->word,
                    'slug' => $wordSetList->slug,
                    'phonetic' => $wordSetList->phonetic,
                    'pronunciation' => $pronunciation,
                    'parts_of_speech' => $wordSetList->parts_of_speech,
                    'static_content_1' => $wordSetList->static_content_1,
                    'static_content_2' => $wordSetList->static_content_2,
                    'meaning' => $wordSetList->meaning,
                    'example_sentence' => $wordSetList->example_sentence,
                    'example_expression' => $wordSetList->example_expression,
                    'example_expression_meaning' => $wordSetList->example_expression_meaning,
                    'display_order' => $wordSetList->display_order,
                    'synonyms' => $synonyms,
                    'antonyms' => $antonyms,
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data['word_set_lists'] = [
                [
                    'word' => '',
                    'phonetic' => '',
                    'pronunciation' => [
                        'bn' => '',
                        'hi' => ''
                    ],
                    'parts_of_speech' => '',
                    'static_content_1' => '',
                    'static_content_2' => '',
                    'meaning' => '',
                    'example_sentence' => '',
                    'example_expression' => '',
                    'example_expression_meaning' => '',
                    'display_order' => 1,
                    'synonyms' => '',
                    'antonyms' => '',
                    'translations' => [
                        [
                            'locale' => 'bn',
                            'word_translation' => '',
                            'word_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                            'example_expression_translation' => '',
                            'example_expression_transliteration' => '',
                            'source' => 'oxford'
                        ],
                        [
                            'locale' => 'hi',
                            'word_translation' => '',
                            'word_transliteration' => '',
                            'example_sentence_translation' => '',
                            'example_sentence_transliteration' => '',
                            'example_expression_translation' => '',
                            'example_expression_transliteration' => '',
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

    // Process JSON data and update word set list items
    public function processJson()
    {
        $this->reset(['errors', 'updateSuccess', 'debugInfo']);
        $this->processing = true;

        try {
            // Decode JSON data
            $data = json_decode($this->jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errors[] = "Invalid JSON: " . json_last_error_msg();
                throw new \Exception("Invalid JSON format");
            }

            // Debug info
            $this->debugInfo[] = "Using article_word_set_id: {$this->articleWordSetId}";
            
            // Validate the JSON structure
            if (!is_array($data)) {
                throw new \Exception("JSON must be an object with word set data");
            }

            // Verify article_word_set_id is valid
            if (empty($this->articleWordSetId)) {
                throw new \Exception("Invalid article_word_set_id: Cannot save words without a parent set");
            }

            DB::beginTransaction();
            $this->debugInfo[] = "Started database transaction";
            
            // 1. Update the Article Word Set (parent) data
            if (isset($data['title'])) {
                // Process column_order - ensure it's stored as a JSON string in the database
                $columnOrder = null;
                if (isset($data['column_order'])) {
                    // Ensure column_order is saved as a JSON string, not an array
                    $columnOrder = is_array($data['column_order']) ? json_encode($data['column_order']) : $data['column_order'];
                }
                
                $wordSetData = [
                    'title' => $data['title'] ?? $this->articleWordSet->title,
                    'content' => $data['content'] ?? $this->articleWordSet->content,
                    'display_order' => $data['display_order'] ?? $this->articleWordSet->display_order,
                    'static_content_1' => $data['static_content_1'] ?? null,
                    'static_content_2' => $data['static_content_2'] ?? null,
                    'column_order' => $columnOrder,
                    // Don't update id or article_id as requested
                ];
                
                // Update the word set
                $this->articleWordSet->update($wordSetData);
                $this->debugInfo[] = "Updated ArticleWordSet ID: {$this->articleWordSet->id}";
                
                // Handle title_translation directly for bn and hi
                if (isset($data['title_translation'])) {
                    // Set Bengali title translation
                    if (isset($data['title_translation']['bn'])) {
                        $this->articleWordSet->setTranslation('title_translation', 'bn', $data['title_translation']['bn']);
                        $this->debugInfo[] = "Set Bengali title translation: {$data['title_translation']['bn']}";
                    }
                    
                    // Set Hindi title translation
                    if (isset($data['title_translation']['hi'])) {
                        $this->articleWordSet->setTranslation('title_translation', 'hi', $data['title_translation']['hi']);
                        $this->debugInfo[] = "Set Hindi title translation: {$data['title_translation']['hi']}";
                    }
                }
                
                // Handle content_translation directly for bn and hi
                if (isset($data['content_translation'])) {
                    // Set Bengali content translation
                    if (isset($data['content_translation']['bn'])) {
                        $this->articleWordSet->setTranslation('content_translation', 'bn', $data['content_translation']['bn']);
                        $this->debugInfo[] = "Set Bengali content translation: {$data['content_translation']['bn']}";
                    }
                    
                    // Set Hindi content translation
                    if (isset($data['content_translation']['hi'])) {
                        $this->articleWordSet->setTranslation('content_translation', 'hi', $data['content_translation']['hi']);
                        $this->debugInfo[] = "Set Hindi content translation: {$data['content_translation']['hi']}";
                    }
                }
                
                // Save translations
                $this->articleWordSet->save();
            }

            // 2. Process the word set list items
            if (isset($data['word_set_lists']) && is_array($data['word_set_lists'])) {
                $wordSetListData = $data['word_set_lists'];
                
                foreach ($wordSetListData as $index => $wordData) {
                    // Skip if word is empty
                    if (empty($wordData['word'])) {
                        $this->debugInfo[] = "Skipping item at index $index: 'word' field is empty";
                        continue;
                    }

                    $word = $wordData['word'];
                    // Always generate the slug from the word, ignoring any provided slug
                    $slug = Str::slug($word);

                    $this->debugInfo[] = "Processing word: $word (Slug: $slug)";

                    // Prepare ArticleWordSetList data
                    $listData = [
                        'word' => $word,
                        'slug' => $slug, // Generated slug
                        'phonetic' => $wordData['phonetic'] ?? null,
                        // We'll manually handle pronunciation after model creation
                        'parts_of_speech' => $wordData['parts_of_speech'] ?? null,
                        'static_content_1' => $wordData['static_content_1'] ?? null,
                        'static_content_2' => $wordData['static_content_2'] ?? null,
                        'meaning' => $wordData['meaning'] ?? null,
                        'example_sentence' => $wordData['example_sentence'] ?? null,
                        'example_expression' => $wordData['example_expression'] ?? null,
                        'example_expression_meaning' => $wordData['example_expression_meaning'] ?? null,
                        'display_order' => $wordData['display_order'] ?? ($index + 1) * 10,
                        'article_word_set_id' => $this->articleWordSetId, // Ensure this is explicitly set
                        // Add synonyms and antonyms handling
                        'synonyms' => is_array($wordData['synonyms']) ? implode(', ', $wordData['synonyms']) : ($wordData['synonyms'] ?? null),
                        'antonyms' => is_array($wordData['antonyms']) ? implode(', ', $wordData['antonyms']) : ($wordData['antonyms'] ?? null)
                    ];

                    // Update or create word set list
                    $wordSetList = ArticleWordSetList::updateOrCreate(
                        [
                            'article_word_set_id' => $this->articleWordSetId,
                            'slug' => $slug
                        ],
                        $listData
                    );

                    $this->debugInfo[] = "Updated/Created ArticleWordSetList ID: {$wordSetList->id} with article_word_set_id: {$wordSetList->article_word_set_id}";
                    
                    // Set pronunciation data using Spatie's setTranslation method
                    if (isset($wordData['pronunciation']) && is_array($wordData['pronunciation'])) {
                        // Set Bengali pronunciation
                        if (isset($wordData['pronunciation']['bn'])) {
                            $wordSetList->setTranslation('pronunciation', 'bn', $wordData['pronunciation']['bn']);
                            $this->debugInfo[] = "Set Bengali pronunciation: {$wordData['pronunciation']['bn']}";
                        }
                        
                        // Set Hindi pronunciation
                        if (isset($wordData['pronunciation']['hi'])) {
                            $wordSetList->setTranslation('pronunciation', 'hi', $wordData['pronunciation']['hi']);
                            $this->debugInfo[] = "Set Hindi pronunciation: {$wordData['pronunciation']['hi']}";
                        }
                        
                        // Save the model with the updated translations
                        $wordSetList->save();
                        
                        // Verify the saved data
                        $savedBn = $wordSetList->getTranslation('pronunciation', 'bn', false);
                        $savedHi = $wordSetList->getTranslation('pronunciation', 'hi', false);
                        $this->debugInfo[] = "Saved Bengali pronunciation: {$savedBn}";
                        $this->debugInfo[] = "Saved Hindi pronunciation: {$savedHi}";
                    }
                    
                    // Process translations
                    if (isset($wordData['translations']) && is_array($wordData['translations'])) {
                        foreach ($wordData['translations'] as $translationData) {
                            $locale = $translationData['locale'] ?? null;
                            if (!$locale || !in_array($locale, $this->supportedLocales)) {
                                $this->debugInfo[] = "Skipping translation: Invalid or unsupported locale '$locale'";
                                continue;
                            }

                            // Prepare translation data
                            $wordTranslation = $translationData['word_translation'] ?? '';
                            $sentenceTranslation = $translationData['example_sentence_translation'] ?? '';
                            $expressionTranslation = $translationData['example_expression_translation'] ?? '';

                            // Only save if at least one of the translation fields has content
                            if (!empty($wordTranslation) || !empty($sentenceTranslation) || !empty($expressionTranslation)) {
                                $translationSaveData = [
                                    'word_translation' => $wordTranslation,
                                    'word_transliteration' => $translationData['word_transliteration'] ?? null,
                                    'example_sentence_translation' => $sentenceTranslation,
                                    'example_sentence_transliteration' => $translationData['example_sentence_transliteration'] ?? null,
                                    'example_expression_translation' => $expressionTranslation,
                                    'example_expression_transliteration' => $translationData['example_expression_transliteration'] ?? null,
                                    'source' => $translationData['source'] ?? 'oxford',
                                    'article_word_set_list_id' => $wordSetList->id // Ensure this is explicitly set
                                ];

                                $translation = ArticleWordTranslation::updateOrCreate(
                                    [
                                        'article_word_set_list_id' => $wordSetList->id,
                                        'locale' => $locale
                                    ],
                                    $translationSaveData
                                );

                                $this->debugInfo[] = "Updated/Created Translation ID: {$translation->id} for word_set_list_id: {$translation->article_word_set_list_id}, locale: $locale";
                            } else {
                                $this->debugInfo[] = "Skipping empty translation for locale: $locale";
                            }
                        }
                    }
                }
            } else {
                $this->debugInfo[] = "No word_set_lists data found in JSON";
            }

            DB::commit();
            $this->debugInfo[] = "Database transaction committed successfully";
            $this->updateSuccess = true;

            // Refresh the JSON data to reflect the updated state
            $this->generateJsonData();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->debugInfo[] = "ERROR: " . $e->getMessage() . " at line " . $e->getLine();
            $this->errors[] = "Error updating word set list: " . $e->getMessage();
        }

        $this->processing = false;
    }
}; ?>

<div>
    <!-- Edit Button -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 active:bg-blue-700 dark:active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
    >
        Edit JSON
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <!-- Modal Header -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                        Edit Word Set & Word Set List JSON
                    </h3>
                    <button
                        wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                        <p>Word set list updated successfully!</p>
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
                            <a href="/sample-data/article-word/article-word-set-list-sample.json" target="_blank" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
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
                                placeholder='[{"word": "example", "translations": [...]}]'
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
                            Update Word Set List
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif


</div>