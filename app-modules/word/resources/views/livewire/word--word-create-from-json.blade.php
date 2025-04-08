<?php

namespace Modules\Word\Http\Livewire;

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Modules\Word\Models\Word;
use Modules\Word\Models\WordMeaning;
use Modules\Word\Models\WordTranslation;

new class extends Component {
    public bool $showModal = false;
    public string $jsonData = '';
    public array $createdWords = [];
    public array $errors = [];
    public bool $showSampleLink = true;
    public bool $processing = false;

    // Open the modal
    public function openModal()
    {
        $this->reset(['jsonData', 'createdWords', 'errors', 'processing']);
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

    // Process JSON data and create words
    public function processJson()
    {
        $this->reset(['createdWords', 'errors']);
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
            if (!isset($data['words']) || !is_array($data['words'])) {
                $this->errors[] = "Invalid JSON structure. Expected a 'words' array.";
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Invalid JSON structure. Expected a 'words' array."
                ]);
                $this->processing = false;
                return;
            }

            // Process each word
            $totalProcessed = 0;
            $totalCreated = 0;
            $totalUpdated = 0;
            $totalErrors = 0;

            DB::beginTransaction();
            try {
                foreach ($data['words'] as $wordData) {
                    $result = $this->createWordFromData($wordData);
                    if ($result) {
                        $this->createdWords[] = $result;
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
                        'message' => "Processed $totalProcessed words: $totalCreated created, $totalUpdated updated"
                    ]);
                } else {
                    // If there were errors, rollback and show error toast
                    DB::rollBack();
                    $this->dispatch('toast', [
                        'type' => 'error',
                        'message' => "Encountered " . count($this->errors) . " errors while processing words."
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Error processing words: " . $e->getMessage();
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => "Error processing words: " . $e->getMessage()
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

    // Create a word from the provided data
    private function createWordFromData($wordData)
    {
        try {
            // Validate required fields
            if (!isset($wordData['word']) || empty($wordData['word'])) {
                $this->errors[] = "Word is required.";
                return null;
            }

            // Generate slug if not provided
            if (!isset($wordData['slug']) || empty($wordData['slug'])) {
                $wordData['slug'] = Str::slug($wordData['word']);
            }

            // Create or find the word
            $word = Word::firstOrCreate(
                ['slug' => $wordData['slug']],
                [
                    'word' => $wordData['word'],
                    'phonetic' => $wordData['phonetic'] ?? null,
                    'part_of_speech' => $wordData['part_of_speech'] ?? null,
                    'source' => 'json',
                ]
            );

            $result = $word->wasRecentlyCreated ? 'created' : 'updated';

            // For existing words, update fields if provided
            if (!$word->wasRecentlyCreated) {
                $needsUpdate = false;

                if (isset($wordData['phonetic']) && $word->phonetic !== $wordData['phonetic']) {
                    $word->phonetic = $wordData['phonetic'];
                    $needsUpdate = true;
                }

                if (isset($wordData['part_of_speech']) && $word->part_of_speech !== $wordData['part_of_speech']) {
                    $word->part_of_speech = $wordData['part_of_speech'];
                    $needsUpdate = true;
                }

                if ($word->source !== 'json') {
                    $word->source = 'json';
                    $needsUpdate = true;
                }

                if ($needsUpdate) {
                    $word->save();
                }
            }

            // Set pronunciations if provided - always replace with new values
            if (isset($wordData['pronunciation'])) {
                if (isset($wordData['pronunciation']['bn_pronunciation'])) {
                    $word->setTranslation('pronunciation', 'bn', $wordData['pronunciation']['bn_pronunciation']);
                }
                if (isset($wordData['pronunciation']['hi_pronunciation'])) {
                    $word->setTranslation('pronunciation', 'hi', $wordData['pronunciation']['hi_pronunciation']);
                }
                if (isset($wordData['pronunciation']['es_pronunciation'])) {
                    $word->setTranslation('pronunciation', 'es', $wordData['pronunciation']['es_pronunciation']);
                }
                $word->save();
            }

            // Process meanings
            if (isset($wordData['meanings']) && is_array($wordData['meanings'])) {
                foreach ($wordData['meanings'] as $index => $meaningData) {
                    $this->createMeaningFromData($word, $meaningData, $index + 1);
                }
            }

            // Process standalone translations (translations at the word level, not nested under meanings)
            if (isset($wordData['translations']) && is_array($wordData['translations'])) {
                foreach ($wordData['translations'] as $translationData) {
                    $this->createStandaloneTranslation($word, $translationData);
                }
            }

            // Process synonyms
            if (isset($wordData['synonyms'])) {
                $synonyms = is_array($wordData['synonyms'])
                    ? $wordData['synonyms']
                    : array_map('trim', explode(',', $wordData['synonyms']));

                $this->processWordConnections($word, $synonyms, 'synonyms');
            }

            // Process antonyms
            if (isset($wordData['antonyms'])) {
                $antonyms = is_array($wordData['antonyms'])
                    ? $wordData['antonyms']
                    : array_map('trim', explode(',', $wordData['antonyms']));

                $this->processWordConnections($word, $antonyms, 'antonyms');
            }

            // Add to created words list
            return [
                'id' => $word->id,
                'word' => $word->word,
                'slug' => $word->slug,
                'status' => $result
            ];

        } catch (\Exception $e) {
            $this->errors[] = "Error creating word '{$wordData['word']}': " . $e->getMessage();
            return null;
        }
    }

    // Process word connections (synonyms or antonyms)
    private function processWordConnections($word, $connections, $type)
    {
        foreach ($connections as $connection) {
            $connection = trim($connection);
            if (empty($connection)) continue;

            // Create or find the related word
            $relatedWord = Word::firstOrCreate(
                ['slug' => Str::slug($connection)],
                [
                    'word' => $connection,
                ]
            );

            $this->createWordConnection($word, $relatedWord, $type);
        }
    }

    // Create a meaning from the provided data
    private function createMeaningFromData($word, $meaningData, $displayOrder = 1)
    {
        try {
            // Validate required fields
            if (!isset($meaningData['meaning']) || empty($meaningData['meaning'])) {
                $this->errors[] = "Meaning is required for word '{$word->word}'.";
                return null;
            }

            // Generate slug if not provided
            if (!isset($meaningData['slug']) || empty($meaningData['slug'])) {
                $meaningData['slug'] = Str::slug(substr($meaningData['meaning'], 0, 250));
            }

            // Create or find the meaning
            $meaning = WordMeaning::firstOrCreate(
                [
                    'word_id' => $word->id,
                    'slug' => $meaningData['slug']
                ],
                [
                    'meaning' => $meaningData['meaning'],
                    'display_order' => $meaningData['display_order'] ?? $displayOrder,
                    'source' => $meaningData['source'] ?? 'json',
                ]
            );

            // Process translations
            if (isset($meaningData['translations']) && is_array($meaningData['translations'])) {
                foreach ($meaningData['translations'] as $translationData) {
                    $this->createTranslationFromData($word, $meaning, $translationData);
                }
            }

            return $meaning;
        } catch (\Exception $e) {
            $this->errors[] = "Error creating meaning for word '{$word->word}': " . $e->getMessage();
            return null;
        }
    }

    // Create a translation from the provided data
    private function createTranslationFromData($word, $meaning, $translationData)
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

            // Check if translation already exists for this word, meaning, and locale
            $existingTranslation = WordTranslation::where('meaning_id', $meaning->id)
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
                WordTranslation::create([
                    'word_id' => $word->id,
                    'meaning_id' => $meaning->id,
                    'locale' => $translationData['locale'],
                    'translation' => $translationData['translation'],
                    'transliteration' => $translationData['transliteration'] ?? null,
                    'source' => $translationData['source'] ?? 'json',
                    'slug' => $translationData['slug'],
                ]);
            }
        } catch (\Exception $e) {
            $this->errors[] = "Error creating translation for word '{$word->word}', meaning '{$meaning->meaning}': " . $e->getMessage();
        }
    }

    // Create a standalone translation (not tied to a specific meaning)
    private function createStandaloneTranslation($word, $translationData)
    {
        try {
            // Validate required fields
            if (!isset($translationData['locale']) || empty($translationData['locale'])) {
                $this->errors[] = "Locale is required for standalone translation.";
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

            // Check if standalone translation already exists for this word and locale
            $existingTranslation = WordTranslation::where('word_id', $word->id)
                ->whereNull('meaning_id')
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
                // Create new standalone translation with explicit slug
                WordTranslation::create([
                    'word_id' => $word->id,
                    'meaning_id' => null,
                    'locale' => $translationData['locale'],
                    'translation' => $translationData['translation'],
                    'transliteration' => $translationData['transliteration'] ?? null,
                    'source' => $translationData['source'] ?? 'json',
                    'slug' => $translationData['slug'],
                ]);
            }
        } catch (\Exception $e) {
            $this->errors[] = "Error creating standalone translation for word '{$word->word}': " . $e->getMessage();
        }
    }

    // Create a word connection (synonym or antonym)
    private function createWordConnection($word, $relatedWord, $type)
    {
        // Skip if trying to connect a word to itself
        if ($word->id === $relatedWord->id) {
            return;
        }

        // Check if connection already exists (in either direction)
        $existingConnection1 = DB::table('word_connections')
            ->where('word_id_1', $word->id)
            ->where('word_id_2', $relatedWord->id)
            ->where('type', $type)
            ->exists();

        $existingConnection2 = DB::table('word_connections')
            ->where('word_id_1', $relatedWord->id)
            ->where('word_id_2', $word->id)
            ->where('type', $type)
            ->exists();

        // Create the connection if it doesn't exist
        if (!$existingConnection1 && !$existingConnection2) {
            DB::table('word_connections')->insert([
                'word_id_1' => $word->id,
                'word_id_2' => $relatedWord->id,
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};

?>

<div>
    <!-- Button to open modal -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
    >
        Import Words from JSON
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Import Words from JSON</h3>
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
                        href="/sample-data/words/words-sample.json"
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

                <!-- Created Words -->
                @if(count($createdWords) > 0)
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200">
                    <h4 class="font-bold mb-2">Created/Updated Words:</h4>
                    <ul class="list-disc pl-5">
                        @foreach($createdWords as $word)
                        <li>
                            <a href="{{ route('backend::words.show', $word['id']) }}" class="text-blue-500 dark:text-blue-400 hover:underline" target="_blank">
                                {{ $word['word'] }}
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
                            placeholder='{"words": [...]}'
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