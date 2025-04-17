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
        $wordSetLists = ArticleWordSetList::where('article_word_set_id', $this->articleWordSetId)
            ->with('translations')
            ->orderBy('display_order', 'asc')
            ->get();

        if ($wordSetLists->isNotEmpty()) {
            $data = [];

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

                // Get pronunciation data
                $pronunciation = [];
                if ($wordSetList->pronunciation) {
                    try {
                        $pronunciation = json_decode($wordSetList->pronunciation, true);
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
                    'translations' => $formattedTranslations,
                ];
            }
        } else {
            // Generate stub data if no existing data
            $data = [
                [
                    'word' => '',
                    'phonetic' => '',
                    'pronunciation' => [
                        'bn_pronunciation' => '',
                        'hi_pronunciation' => ''
                    ],
                    'parts_of_speech' => '',
                    'static_content_1' => '',
                    'static_content_2' => '',
                    'meaning' => '',
                    'example_sentence' => '',
                    'example_expression' => '',
                    'example_expression_meaning' => '',
                    'display_order' => 1,
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

        // Debug info - safer than dd() as it doesn't halt execution
        // $this->debugInfo[] = "JSON Data Length: " . strlen($this->jsonData);
        // $this->debugInfo[] = "JSON Data Preview: " . substr($this->jsonData, 0, 100) . '...';

        try {
            // Decode JSON data
            $data = json_decode($this->jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errors[] = "Invalid JSON: " . json_last_error_msg();
                throw new \Exception("Invalid JSON format");
            }

            // Debug info
            $this->debugInfo[] = "Using article_word_set_id: {$this->articleWordSetId}";
            $this->debugInfo[] = "Decoded JSON data: " . print_r($data, true);

            // Validate the JSON structure
            if (!is_array($data)) {
                throw new \Exception("JSON must be an array of word items");
            }

            // Verify article_word_set_id is valid
            if (empty($this->articleWordSetId)) {
                throw new \Exception("Invalid article_word_set_id: Cannot save words without a parent set");
            }

            DB::beginTransaction();
            $this->debugInfo[] = "Started database transaction";

            // Process each word in the array
            foreach ($data as $index => $wordData) {
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
                    'pronunciation' => isset($wordData['pronunciation']) ? json_encode($wordData['pronunciation']) : null,
                    'parts_of_speech' => $wordData['parts_of_speech'] ?? null,
                    'static_content_1' => $wordData['static_content_1'] ?? null,
                    'static_content_2' => $wordData['static_content_2'] ?? null,
                    'meaning' => $wordData['meaning'] ?? null,
                    'example_sentence' => $wordData['example_sentence'] ?? null,
                    'example_expression' => $wordData['example_expression'] ?? null,
                    'example_expression_meaning' => $wordData['example_expression_meaning'] ?? null,
                    'display_order' => $wordData['display_order'] ?? ($index + 1) * 10,
                    'article_word_set_id' => $this->articleWordSetId // Ensure this is explicitly set
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
                        Edit Word Set List JSON
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

                    <!-- JSON Input -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">JSON Data</label>
                        <textarea
                            wire:model.live="jsonData"
                            rows="20"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder='[{"word": "example", "translations": [...]}]'
                        >{{ $jsonData }}</textarea>
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