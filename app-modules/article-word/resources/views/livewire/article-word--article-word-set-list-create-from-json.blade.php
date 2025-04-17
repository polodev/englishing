<?php

namespace Modules\ArticleWord\Http\Livewire;

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\ArticleWord\Models\ArticleWordSet;
use Modules\ArticleWord\Models\ArticleWordSetList;
use Modules\ArticleWord\Models\ArticleWordTranslation;

new class extends Component {
    public bool $showModal = false;
    public string $jsonData = '';
    public array $createdItems = [];
    public array $errors = [];
    public bool $showSampleLink = true;
    public bool $processing = false;
    public ?int $articleWordSetId = null;

    // Mount method to receive the articleWordSetId parameter
    public function mount($articleWordSetId = null)
    {
        $this->articleWordSetId = $articleWordSetId;
    }

    // Open the modal
    public function openModal($articleWordSetId = null)
    {
        $this->reset(['jsonData', 'createdItems', 'errors', 'processing']);
        $this->articleWordSetId = $articleWordSetId;
        $this->showModal = true;
        $this->showSampleLink = true;
    }

    // Listen for the openArticleWordSetListJsonImportModal event
    #[On('openArticleWordSetListJsonImportModal')]
    public function handleOpenModal($params = null)
    {
        // Extract articleWordSetId from parameters if provided
        $articleWordSetId = null;
        if (is_array($params) && isset($params['articleWordSetId'])) {
            $articleWordSetId = $params['articleWordSetId'];
        }
        $this->openModal($articleWordSetId);
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Process JSON data and create article word set lists
    public function processJson()
    {
        $this->reset(['createdItems', 'errors']);
        $this->processing = true;

        try {
            // Validate article word set ID
            if (!$this->articleWordSetId) {
                $this->errors[] = "Article Word Set ID is required.";
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Article Word Set ID is required."
                ]);
                $this->processing = false;
                return;
            }

            // Check if article word set exists
            $articleWordSet = ArticleWordSet::find($this->articleWordSetId);
            if (!$articleWordSet) {
                $this->errors[] = "Article Word Set not found.";
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Article Word Set not found."
                ]);
                $this->processing = false;
                return;
            }

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

            // Process each item in the JSON array
            $totalProcessed = 0;
            $totalCreated = 0;
            $totalUpdated = 0;
            $totalErrors = 0;

            DB::beginTransaction();
            try {
                foreach ($data as $itemData) {
                    $result = $this->createArticleWordSetListFromData($articleWordSet, $itemData);
                    if ($result) {
                        $this->createdItems[] = $result;
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
                        'message' => "Processed $totalProcessed items: $totalCreated created, $totalUpdated updated"
                    ]);
                } else {
                    // If there were errors, rollback and show error toast
                    DB::rollBack();
                    $this->dispatch('toast', [
                        'type' => 'error',
                        'message' => "Encountered " . count($this->errors) . " errors while processing items."
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Error processing items: " . $e->getMessage();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Error processing items: " . $e->getMessage()
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

    // Create an article word set list item from the provided data
    private function createArticleWordSetListFromData($articleWordSet, $itemData)
    {
        try {
            // Validate required fields
            if (!isset($itemData['word']) || empty($itemData['word'])) {
                $this->errors[] = "Word is required.";
                return null;
            }

            // Generate slug if not provided
            $slug = isset($itemData['slug']) && !empty($itemData['slug'])
                ? $itemData['slug']
                : Str::slug($itemData['word']);

            // Check if item already exists by slug
            $existingItem = ArticleWordSetList::where('article_word_set_id', $articleWordSet->id)
                ->where('slug', $slug)
                ->first();

            // Create or update the item
            $item = $existingItem ?: new ArticleWordSetList();

            if (!$existingItem) {
                $item->article_word_set_id = $articleWordSet->id;
                $item->slug = $slug;
            }

            // Set basic fields
            $item->word = $itemData['word'];
            $item->display_order = $itemData['display_order'] ?? 0;
            $item->phonetic = $itemData['phonetic'] ?? null;
            $item->parts_of_speech = $itemData['parts_of_speech'] ?? null;
            $item->static_content_1 = $itemData['static_content_1'] ?? null;
            $item->static_content_2 = $itemData['static_content_2'] ?? null;
            $item->meaning = $itemData['meaning'] ?? null;
            $item->example_sentence = $itemData['example_sentence'] ?? null;
            $item->example_expression = $itemData['example_expression'] ?? null;
            $item->example_expression_meaning = $itemData['example_expression_meaning'] ?? null;

            // Handle pronunciation as JSON
            if (isset($itemData['pronunciation'])) {
                $item->pronunciation = $itemData['pronunciation'];
            }

            $item->save();

            $result = $existingItem ? 'updated' : 'created';

            // Process translations if provided
            if (isset($itemData['translations']) && is_array($itemData['translations'])) {
                foreach ($itemData['translations'] as $translationData) {
                    $this->createTranslation($item, $translationData);
                }
            }

            // Add to created items list
            return [
                'id' => $item->id,
                'word' => $item->word,
                'slug' => $item->slug,
                'status' => $result
            ];
        } catch (\Exception $e) {
            $this->errors[] = "Error creating item '{$itemData['word']}': " . $e->getMessage();
            return null;
        }
    }

    // Create a translation for an article word set list item
    private function createTranslation($item, $translationData)
    {
        if (!isset($translationData['locale']) || empty($translationData['locale'])) {
            $this->errors[] = "Locale is required for translations.";
            return null;
        }

        // Check if translation already exists
        $translation = ArticleWordTranslation::where('article_word_set_list_id', $item->id)
            ->where('locale', $translationData['locale'])
            ->first();

        if (!$translation) {
            $translation = new ArticleWordTranslation();
            $translation->article_word_set_list_id = $item->id;
            $translation->locale = $translationData['locale'];
        }

        // Set translation fields
        $translation->word_translation = $translationData['word_translation'] ?? '';
        $translation->word_transliteration = $translationData['word_transliteration'] ?? null;
        $translation->example_sentence_translation = $translationData['example_sentence_translation'] ?? null;
        $translation->example_sentence_transliteration = $translationData['example_sentence_transliteration'] ?? null;
        $translation->example_expression_translation = $translationData['example_expression_translation'] ?? null;
        $translation->example_expression_transliteration = $translationData['example_expression_transliteration'] ?? null;
        $translation->source = $translationData['source'] ?? null;

        $translation->save();
        return $translation;
    }
};
?>

<div>
    <!-- Button to open modal -->
    <button
        wire:click="openModal({{ $articleWordSetId }})"
        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded"
    >
        <i class="fas fa-file-import mr-2"></i> Import from JSON
    </button>

    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <!-- Modal header -->
                <div class="flex items-center justify-between px-4 py-3 border-b dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Import Article Word Set List Items from JSON
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300" wire:click="closeModal">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 dark:bg-gray-800">
                    <!-- Sample Link -->
                    @if($showSampleLink)
                    <div class="mb-4">
                        <a
                            href="/sample-data/article-word/article-word-sample.json"
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

                    <!-- Created Items -->
                    @if(count($createdItems) > 0)
                    <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200">
                        <h4 class="font-bold mb-2">Created/Updated Items:</h4>
                        <ul class="list-disc pl-5">
                            @foreach($createdItems as $item)
                            <li>
                                {{ $item['word'] }} ({{ $item['status'] }})
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
                                placeholder='[{"word": "example", ...}]'
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
    </div>
    @endif
</div>