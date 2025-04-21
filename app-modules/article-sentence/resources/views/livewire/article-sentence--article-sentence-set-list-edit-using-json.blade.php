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
        $sentenceLists = ArticleSentenceSetList::where('article_sentence_set_id', $this->articleSentenceSetId)
            ->with('translations')
            ->orderBy('display_order')
            ->get();

        if ($sentenceLists->isEmpty()) {
            $data = [
                [
                    'sentence'       => '',
                    'display_order'  => 1,
                    'pronunciation'  => [
                        'bn_pronunciation' => '',
                        'hi_pronunciation' => '',
                    ],
                    'translations'   => [
                        [
                            'locale' => 'bn',
                            'sentence_translation'      => '',
                            'sentence_transliteration'  => '',
                        ],
                        [
                            'locale' => 'hi',
                            'sentence_translation'      => '',
                            'sentence_transliteration'  => '',
                        ],
                    ],
                ],
            ];
        } else {
            $data = [];
            foreach ($sentenceLists as $list) {
                // Get pronunciation data properly decoded from JSON
                $pronunciation = [];
                if ($list->pronunciation) {
                    try {
                        $pronunciation = json_decode($list->pronunciation, true);
                        if (!is_array($pronunciation)) {
                            $pronunciation = ['bn_pronunciation' => '', 'hi_pronunciation' => ''];
                        }
                    } catch (\Exception $e) {
                        $pronunciation = ['bn_pronunciation' => '', 'hi_pronunciation' => ''];
                        $this->debugInfo[] = "Failed to decode pronunciation: " . $e->getMessage();
                    }
                } else {
                    $pronunciation = ['bn_pronunciation' => '', 'hi_pronunciation' => ''];
                }

                // Format translations as a sequential array like the expression component
                $translations = $list->translations->keyBy('locale');
                $formattedTranslations = [];
                foreach ($this->supportedLocales as $locale) {
                    $tr = $translations->get($locale);
                    $formattedTranslations[] = [
                        'id' => $tr->id ?? null,
                        'locale' => $locale,
                        'sentence_translation' => $tr->translation ?? '',
                        'sentence_transliteration' => $tr->transliteration ?? '',
                    ];
                }

                $data[] = [
                    'id'            => $list->id,
                    'sentence'      => $list->sentence,
                    'display_order' => $list->display_order,
                    'pronunciation' => $pronunciation,
                    'translations'  => $formattedTranslations,
                ];
            }
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

        DB::beginTransaction();
        try {
            $processedIds = [];
            $order = 1;
            foreach ($data as $item) {
                if (!isset($item['sentence']) || $item['sentence'] === '') {
                    $this->errors[] = 'Each item must have sentence';
                    continue;
                }
                $list = isset($item['id']) ? ArticleSentenceSetList::find($item['id']) : null;
                if(!$list) {
                    $list = new ArticleSentenceSetList();
                    $list->article_sentence_set_id = $this->articleSentenceSetId;
                }
                $list->sentence = $item['sentence'];
                $list->display_order = $order++;
                
                // Generate a unique slug to avoid duplicates
                $baseSlug = Str::slug($item['sentence']) ?: Str::uuid();
                $slug = $baseSlug;
                $counter = 1;
                
                // Check if this is a new record or we're changing the slug
                if (!$list->exists || $list->slug !== $baseSlug) {
                    // Check for duplicate slugs and make unique if needed
                    while (ArticleSentenceSetList::where('article_sentence_set_id', $this->articleSentenceSetId)
                        ->where('slug', $slug)
                        ->where('id', '!=', $list->id ?? 0)
                        ->exists()) {
                        $slug = $baseSlug . '-' . $counter++;
                    }
                }
                
                $list->slug = $slug;
                $list->pronunciation = $item['pronunciation'] ?? [];
                $list->save();

                $processedIds[] = $list->id;

                // Process translations - handle both formats
                if (isset($item['translations']) && is_array($item['translations'])) {
                    // Determine the format - if it's first item has a 'locale' key, it's sequential, otherwise associative
                    $isSequentialFormat = false;
                    $firstItem = reset($item['translations']);
                    if (is_array($firstItem) && isset($firstItem['locale'])) {
                        $isSequentialFormat = true;
                    }
                    
                    if ($isSequentialFormat) {
                        // Process as sequential array (new format)
                        $this->debugInfo[] = "Processing translations in sequential format";
                        foreach ($item['translations'] as $trData) {
                            if (!isset($trData['locale']) || !in_array($trData['locale'], $this->supportedLocales)) continue;
                            
                            $locale = $trData['locale'];
                            $tr = $list->translations()->firstOrNew(['locale' => $locale]);
                            $tr->translation = $trData['sentence_translation'] ?? '';
                            $tr->transliteration = $trData['sentence_transliteration'] ?? '';
                            $tr->save();
                            
                            $this->debugInfo[] = "Saved translation for locale: {$locale} (sequential format)";
                        }
                    } else {
                        // Process as associative array (original format)
                        $this->debugInfo[] = "Processing translations in associative format";
                        foreach ($this->supportedLocales as $locale) {
                            if (!isset($item['translations'][$locale])) continue;
                            
                            $trData = $item['translations'][$locale];
                            $tr = $list->translations()->firstOrNew(['locale' => $locale]);
                            $tr->translation = $trData['sentence_translation'] ?? '';
                            $tr->transliteration = $trData['sentence_transliteration'] ?? '';
                            $tr->save();
                            
                            $this->debugInfo[] = "Saved translation for locale: {$locale} (associative format)";
                        }
                    }
                }
            }

            // delete removed
            ArticleSentenceSetList::where('article_sentence_set_id', $this->articleSentenceSetId)
                ->whereNotIn('id',$processedIds)->delete();

            DB::commit();
            $this->updateSuccess = true;
            $this->debugInfo[] = "Processed " . count($processedIds) . " items";
            $this->debugInfo[] = "Completed at " . now()->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = $e->getMessage();
            $this->debugInfo[] = "Error: " . $e->getMessage();
            $this->debugInfo[] = "Failed at " . now()->format('Y-m-d H:i:s');
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Sentence Set List using JSON</h3>
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
                    placeholder='[{"sentence": "example", "translations": [...]}]'
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