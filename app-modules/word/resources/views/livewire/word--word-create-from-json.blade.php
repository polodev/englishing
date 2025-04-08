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

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // View sample JSON
    public function viewSample()
    {
        $this->errors = [];
        
        try {
            // Hardcoded sample JSON
            $this->jsonData = '{
    "words": [
        {
            "word": "happiness",
            "slug": "happiness",
            "phonetic": "ˈhæpinəs",
            "part_of_speech": "noun",
            "source": "oxford",
            "pronunciation": {
                "bn_pronunciation": "হ্যাপিনেস",
                "hi_pronunciation": "हैप्पीनेस",
                "es_pronunciation": "jápines"
            },
            "meanings": [
                {
                    "meaning": "the state of being happy",
                    "slug": "state-of-being-happy",
                    "display_order": 1,
                    "source": "oxford",
                    "translations": [
                        {
                            "locale": "bn",
                            "translation": "সুখ",
                            "transliteration": "shukh"
                        },
                        {
                            "locale": "hi",
                            "translation": "ख़ुशी",
                            "transliteration": "khushi"
                        },
                        {
                            "locale": "es",
                            "translation": "felicidad",
                            "transliteration": null
                        }
                    ]
                }
            ],
            "standalone_translations": [
                {
                    "locale": "bn",
                    "translation": "আনন্দ",
                    "transliteration": "anondo"
                }
            ],
            "synonyms": ["joy", "delight", "pleasure", "contentment"],
            "antonyms": ["sadness", "sorrow", "misery"]
        }
    ]
}';
            
        } catch (\Exception $e) {
            $this->errors[] = "Error loading sample: " . $e->getMessage();
        }
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
                $this->processing = false;
                return;
            }

            // Check if the JSON has the expected structure
            if (!isset($data['words']) || !is_array($data['words'])) {
                $this->errors[] = "JSON must contain a 'words' array.";
                $this->processing = false;
                return;
            }

            // Process each word
            foreach ($data['words'] as $wordData) {
                $this->createWordFromData($wordData);
            }

        } catch (\Exception $e) {
            $this->errors[] = "Error processing JSON: " . $e->getMessage();
        }

        $this->processing = false;
    }

    // Create a word from the provided data
    private function createWordFromData($wordData)
    {
        try {
            // Validate required fields
            if (!isset($wordData['word']) || empty($wordData['word'])) {
                $this->errors[] = "Word is required.";
                return;
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
                    'source' => $wordData['source'] ?? null,
                ]
            );

            // For existing words, update fields if provided
            if ($word->wasRecentlyCreated === false) {
                $needsUpdate = false;
                
                if (isset($wordData['phonetic']) && $word->phonetic !== $wordData['phonetic']) {
                    $word->phonetic = $wordData['phonetic'];
                    $needsUpdate = true;
                }
                
                if (isset($wordData['part_of_speech']) && $word->part_of_speech !== $wordData['part_of_speech']) {
                    $word->part_of_speech = $wordData['part_of_speech'];
                    $needsUpdate = true;
                }
                
                if (isset($wordData['source']) && $word->source !== $wordData['source']) {
                    $word->source = $wordData['source'];
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

            // Process standalone translations
            if (isset($wordData['standalone_translations']) && is_array($wordData['standalone_translations'])) {
                foreach ($wordData['standalone_translations'] as $translationData) {
                    $this->createStandaloneTranslation($word, $translationData);
                }
            }

            // Process synonyms
            if (isset($wordData['synonyms']) && is_array($wordData['synonyms'])) {
                $this->processWordConnections($word, $wordData['synonyms'], 'synonyms');
            } else if (isset($wordData['synonyms']) && is_string($wordData['synonyms'])) {
                $this->processWordConnections($word, explode(',', $wordData['synonyms']), 'synonyms');
            }

            // Process antonyms
            if (isset($wordData['antonyms']) && is_array($wordData['antonyms'])) {
                $this->processWordConnections($word, $wordData['antonyms'], 'antonyms');
            } else if (isset($wordData['antonyms']) && is_string($wordData['antonyms'])) {
                $this->processWordConnections($word, explode(',', $wordData['antonyms']), 'antonyms');
            }

            // Add to created words list
            $this->createdWords[] = [
                'id' => $word->id,
                'word' => $word->word,
                'slug' => $word->slug
            ];

        } catch (\Exception $e) {
            $this->errors[] = "Error creating word '{$wordData['word']}': " . $e->getMessage();
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
    private function createMeaningFromData($word, $meaningData, $displayOrder)
    {
        // Validate required fields
        if (!isset($meaningData['meaning']) || empty($meaningData['meaning'])) {
            $this->errors[] = "Meaning is required for word '{$word->word}'.";
            return;
        }

        // Generate slug if not provided
        if (!isset($meaningData['slug']) || empty($meaningData['slug'])) {
            $meaningData['slug'] = Str::slug($meaningData['meaning']);
        }

        // Create or find the meaning
        $meaning = WordMeaning::firstOrCreate(
            [
                'word_id' => $word->id,
                'slug' => $meaningData['slug']
            ],
            [
                'meaning' => $meaningData['meaning'],
                'source' => $meaningData['source'] ?? null,
                'display_order' => $meaningData['display_order'] ?? $displayOrder,
            ]
        );

        // For existing meanings, update fields if provided
        if ($meaning->wasRecentlyCreated === false) {
            $needsUpdate = false;
            
            if (isset($meaningData['meaning']) && $meaning->meaning !== $meaningData['meaning']) {
                $meaning->meaning = $meaningData['meaning'];
                $needsUpdate = true;
            }
            
            if (isset($meaningData['source']) && $meaning->source !== $meaningData['source']) {
                $meaning->source = $meaningData['source'];
                $needsUpdate = true;
            }
            
            if (isset($meaningData['display_order']) && $meaning->display_order !== $meaningData['display_order']) {
                $meaning->display_order = $meaningData['display_order'];
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $meaning->save();
            }
        }

        // Process translations
        if (isset($meaningData['translations']) && is_array($meaningData['translations'])) {
            foreach ($meaningData['translations'] as $translationData) {
                $this->createTranslationFromData($word, $meaning, $translationData);
            }
        }
    }

    // Create a translation from the provided data
    private function createTranslationFromData($word, $meaning, $translationData)
    {
        // Validate required fields
        if (!isset($translationData['locale']) || empty($translationData['locale'])) {
            $this->errors[] = "Locale is required for translation.";
            return;
        }

        if (!isset($translationData['translation']) || empty($translationData['translation'])) {
            $this->errors[] = "Translation text is required for locale '{$translationData['locale']}'.";
            return;
        }

        // Generate slug if not provided
        if (!isset($translationData['slug']) || empty($translationData['slug'])) {
            $translationData['slug'] = Str::slug($translationData['translation']);
            // If the language doesn't use Latin script, use transliteration for slug
            if (empty($translationData['slug']) && isset($translationData['transliteration'])) {
                $translationData['slug'] = Str::slug($translationData['transliteration']);
            }
            // If still empty, use a hash of the translation
            if (empty($translationData['slug'])) {
                $translationData['slug'] = Str::slug(substr(md5($translationData['translation']), 0, 10));
            }
        }

        // Check if translation already exists for this word, meaning, and locale
        $existingTranslation = WordTranslation::where('word_id', $word->id)
            ->where('meaning_id', $meaning->id)
            ->where('locale', $translationData['locale'])
            ->first();

        if ($existingTranslation) {
            // Update existing translation
            $existingTranslation->translation = $translationData['translation'];
            $existingTranslation->transliteration = $translationData['transliteration'] ?? null;
            $existingTranslation->source = $translationData['source'] ?? null;
            $existingTranslation->save();
        } else {
            // Create new translation
            WordTranslation::create([
                'word_id' => $word->id,
                'meaning_id' => $meaning->id,
                'locale' => $translationData['locale'],
                'translation' => $translationData['translation'],
                'transliteration' => $translationData['transliteration'] ?? null,
                'source' => $translationData['source'] ?? null,
                'slug' => $translationData['slug'],
            ]);
        }
    }

    // Create a standalone translation (not tied to a specific meaning)
    private function createStandaloneTranslation($word, $translationData)
    {
        // Validate required fields
        if (!isset($translationData['locale']) || empty($translationData['locale'])) {
            $this->errors[] = "Locale is required for standalone translation.";
            return;
        }

        if (!isset($translationData['translation']) || empty($translationData['translation'])) {
            $this->errors[] = "Translation text is required for locale '{$translationData['locale']}'.";
            return;
        }

        // Generate slug if not provided
        if (!isset($translationData['slug']) || empty($translationData['slug'])) {
            $translationData['slug'] = Str::slug($translationData['translation']);
            // If the language doesn't use Latin script, use transliteration for slug
            if (empty($translationData['slug']) && isset($translationData['transliteration'])) {
                $translationData['slug'] = Str::slug($translationData['transliteration']);
            }
            // If still empty, use a hash of the translation
            if (empty($translationData['slug'])) {
                $translationData['slug'] = Str::slug(substr(md5($translationData['translation']), 0, 10));
            }
        }

        // Check if standalone translation already exists for this word and locale
        $existingTranslation = WordTranslation::where('word_id', $word->id)
            ->whereNull('meaning_id')
            ->where('locale', $translationData['locale'])
            ->first();

        if ($existingTranslation) {
            // Update existing translation
            $existingTranslation->translation = $translationData['translation'];
            $existingTranslation->transliteration = $translationData['transliteration'] ?? null;
            $existingTranslation->source = $translationData['source'] ?? null;
            $existingTranslation->save();
        } else {
            // Create new translation
            WordTranslation::create([
                'word_id' => $word->id,
                'meaning_id' => null,
                'locale' => $translationData['locale'],
                'translation' => $translationData['translation'],
                'transliteration' => $translationData['transliteration'] ?? null,
                'source' => $translationData['source'] ?? null,
                'slug' => $translationData['slug'],
            ]);
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
    <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-600 active:bg-gray-900 dark:active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
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
                    <button 
                        type="button" 
                        wire:click.prevent="viewSample" 
                        class="inline-flex items-center px-3 py-1 bg-blue-600 dark:bg-blue-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                    >
                        View Sample JSON Format
                    </button>
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
                            <a href="{{ route('word.show', $word['slug']) }}" class="text-blue-500 dark:text-blue-400 hover:underline" target="_blank">
                                {{ $word['word'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- JSON Input -->
                <div class="mb-4">
                    <label for="jsonData" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        JSON Data
                    </label>
                    <textarea
                        id="jsonData"
                        wire:model="jsonData"
                        rows="15"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder='{"words": [...]}'
                    ></textarea>
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