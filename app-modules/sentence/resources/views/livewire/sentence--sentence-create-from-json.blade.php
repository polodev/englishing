<?php

namespace Modules\Sentence\Http\Livewire;

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Sentence\Models\Sentence;
use Modules\Sentence\Models\SentenceTranslation;

new class extends Component {
    public bool $showModal = false;
    public string $jsonData = '';
    public array $createdSentences = [];
    public array $errors = [];
    public bool $showSampleLink = true;
    public bool $processing = false;

    // Open the modal
    public function openModal()
    {
        $this->reset(['jsonData', 'createdSentences', 'errors', 'processing']);
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

    // Process JSON data and create sentences
    public function processJson()
    {
        $this->reset(['createdSentences', 'errors']);
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
            if (!isset($data['sentences']) || !is_array($data['sentences'])) {
                $this->errors[] = "Invalid JSON structure. Expected a 'sentences' array.";
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Invalid JSON structure. Expected a 'sentences' array."
                ]);
                $this->processing = false;
                return;
            }

            // Process each sentence
            $totalProcessed = 0;
            $totalCreated = 0;
            $totalUpdated = 0;
            $totalErrors = 0;

            DB::beginTransaction();
            try {
                foreach ($data['sentences'] as $sentenceData) {
                    $result = $this->createSentenceFromData($sentenceData);
                    if ($result) {
                        $this->createdSentences[] = $result;
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
                        'message' => "Processed $totalProcessed sentences: $totalCreated created, $totalUpdated updated"
                    ]);
                } else {
                    // If there were errors, rollback and show error toast
                    DB::rollBack();
                    $this->dispatch('toast', [
                        'type' => 'error',
                        'message' => "Encountered " . count($this->errors) . " errors while processing sentences."
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Error processing sentences: " . $e->getMessage();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Error processing sentences: " . $e->getMessage()
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

    // Create a sentence from the provided data
    private function createSentenceFromData($sentenceData)
    {
        try {
            // Validate required fields
            if (!isset($sentenceData['sentence']) || empty($sentenceData['sentence'])) {
                $this->errors[] = "Sentence is required.";
                return null;
            }

            // Generate slug if not provided
            if (!isset($sentenceData['slug']) || empty($sentenceData['slug'])) {
                $sentenceData['slug'] = Str::slug(Str::limit($sentenceData['sentence'], 100));
            }

            // Create or find the sentence
            $sentence = Sentence::firstOrCreate(
                ['slug' => $sentenceData['slug']],
                [
                    'sentence' => $sentenceData['sentence'],
                    'source' => $sentenceData['source'] ?? 'json',
                ]
            );

            $result = $sentence->wasRecentlyCreated ? 'created' : 'updated';

            // For existing sentences, update fields if provided
            if (!$sentence->wasRecentlyCreated) {
                $needsUpdate = false;

                if (isset($sentenceData['source']) && $sentence->source !== $sentenceData['source']) {
                    $sentence->source = $sentenceData['source'];
                    $needsUpdate = true;
                } else if ($sentence->source !== 'json') {
                    $sentence->source = 'json';
                    $needsUpdate = true;
                }

                if ($needsUpdate) {
                    $sentence->save();
                }
            }

            // Set pronunciations if provided - always replace with new values
            if (isset($sentenceData['pronunciation'])) {
                if (isset($sentenceData['pronunciation']['bn'])) {
                    $sentence->setTranslation('pronunciation', 'bn', $sentenceData['pronunciation']['bn']);
                }
                if (isset($sentenceData['pronunciation']['hi'])) {
                    $sentence->setTranslation('pronunciation', 'hi', $sentenceData['pronunciation']['hi']);
                }
                if (isset($sentenceData['pronunciation']['es'])) {
                    $sentence->setTranslation('pronunciation', 'es', $sentenceData['pronunciation']['es']);
                }
                $sentence->save();
            }

            // Process translations
            if (isset($sentenceData['translations']) && is_array($sentenceData['translations'])) {
                foreach ($sentenceData['translations'] as $translationData) {
                    $this->createTranslationFromData($sentence, $translationData);
                }
            }

            return [
                'id' => $sentence->id,
                'sentence' => $sentence->sentence,
                'slug' => $sentence->slug,
                'status' => $result
            ];
        } catch (\Exception $e) {
            $this->errors[] = "Error creating sentence '{$sentenceData['sentence']}': " . $e->getMessage();
            return null;
        }
    }

    // Create a translation for a sentence
    private function createTranslationFromData($sentence, $translationData)
    {
        try {
            // Validate required fields
            if (!isset($translationData['locale']) || empty($translationData['locale'])) {
                $this->errors[] = "Translation locale is required.";
                return null;
            }

            if (!isset($translationData['translation']) || empty($translationData['translation'])) {
                $this->errors[] = "Translation text is required.";
                return null;
            }

            // Create or update the translation
            $translation = SentenceTranslation::updateOrCreate(
                [
                    'sentence_id' => $sentence->id,
                    'locale' => $translationData['locale']
                ],
                [
                    'translation' => $translationData['translation'],
                    'transliteration' => $translationData['transliteration'] ?? null,
                    'slug' => isset($translationData['slug']) ? $translationData['slug'] : Str::slug(Str::limit($translationData['translation'], 100))
                ]
            );

            return $translation;
        } catch (\Exception $e) {
            $this->errors[] = "Error creating translation for '{$sentence->sentence}' in locale '{$translationData['locale']}': " . $e->getMessage();
            return null;
        }
    }

    // Load sample JSON data
    public function loadSampleData()
    {
        $samplePath = public_path('sample-data/words/sentence-sample.json');
        if (file_exists($samplePath)) {
            $this->jsonData = file_get_contents($samplePath);
        } else {
            $this->errors[] = "Sample data file not found.";
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
        Import Sentences from JSON
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Import Sentences from JSON</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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
                        href="/sample-data/words/sentence-sample.json"
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

                <!-- Created Sentences -->
                @if(count($createdSentences) > 0)
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200">
                    <h4 class="font-bold mb-2">Created/Updated Sentences:</h4>
                    <ul class="list-disc pl-5">
                        @foreach($createdSentences as $sentence)
                        <li>
                            <a href="{{ route('backend::sentences.show', $sentence['id']) }}" class="text-blue-500 dark:text-blue-400 hover:underline" target="_blank">
                                {{ Str::limit($sentence['sentence'], 50) }}
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
                            placeholder='{"sentences": [...]}'
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