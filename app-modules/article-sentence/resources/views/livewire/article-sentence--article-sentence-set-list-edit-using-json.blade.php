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
                        'bn' => [
                            'sentence_translation'      => '',
                            'sentence_transliteration'  => '',
                        ],
                        'hi' => [
                            'sentence_translation'      => '',
                            'sentence_transliteration'  => '',
                        ],
                    ],
                ],
            ];
        } else {
            $data = [];
            foreach ($sentenceLists as $list) {
                // fetch pronunciation array (cast attribute)
                $pronunciation = $list->pronunciation ?: [
                    'bn_pronunciation' => '',
                    'hi_pronunciation' => '',
                ];

                // collect translations keyed by locale
                $translations = [];
                foreach ($this->supportedLocales as $locale) {
                    $tr = $list->getTranslation($locale);
                    $translations[$locale] = [
                        'sentence_translation'     => optional($tr)->sentence_translation ?? '',
                        'sentence_transliteration' => optional($tr)->sentence_transliteration ?? '',
                    ];
                }

                $data[] = [
                    'id'            => $list->id,
                    'sentence'      => $list->sentence,
                    'display_order' => $list->display_order,
                    'pronunciation' => $pronunciation,
                    'translations'  => $translations,
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
                $list->slug = Str::slug($item['sentence']) ?: Str::uuid();
                $list->pronunciation = $item['pronunciation'] ?? [];
                $list->save();

                $processedIds[] = $list->id;

                // translations
                if (isset($item['translations']) && is_array($item['translations'])) {
                    foreach ($item['translations'] as $locale => $trData) {
                        if (!in_array($locale, $this->supportedLocales)) continue;
                        $tr = $list->translations()->firstOrNew(['locale'=>$locale]);
                        $tr->sentence_translation = $trData['sentence_translation'] ?? '';
                        $tr->sentence_transliteration = $trData['sentence_transliteration'] ?? '';
                        $tr->source = $tr->source ?? 'oxford';
                        $tr->save();
                    }
                }
            }

            // delete removed
            ArticleSentenceSetList::where('article_sentence_set_id', $this->articleSentenceSetId)
                ->whereNotIn('id',$processedIds)->delete();

            DB::commit();
            $this->updateSuccess = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = $e->getMessage();
        }
        $this->processing = false;
    }
};
?>

<div>
    <button wire:click="openModal" class="px-4 py-2 bg-blue-600 text-white rounded">Edit Sentence Set List (JSON)</button>

    @if($showModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 w-full max-w-4xl mx-4 rounded shadow-lg overflow-y-auto max-h-[90vh] p-6" wire:key="modal">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">Edit Sentence Set List (JSON)</h2>

            @if($errors)
            <div class="mb-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 p-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach($errors as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($updateSuccess)
            <div class="mb-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 p-3 rounded">
                Updated successfully!
            </div>
            @endif

            <div wire:ignore>
                <x-json-editor wire:model.live="jsonData" :content="$jsonData" />
            </div>

            <div class="mt-4 flex justify-end space-x-2">
                <button wire:click="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded">Close</button>
                <button wire:click="processJson" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </div>
    </div>
    @endif
</div>