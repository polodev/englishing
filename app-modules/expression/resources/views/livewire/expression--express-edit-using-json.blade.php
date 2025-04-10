<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Modules\Expression\Models\Expression;

new class extends Component {
    public bool $showModal = false;
    public bool $showSuccessMessage = false;
    public ?int $expressionId = null;
    public ?string $expressionName = null;

    // Form fields
    public string $jsonInput = '';
    public array $parsedData = [];
    public string $expression = '';
    public string $slug = '';
    public ?string $part_of_speech = null;
    public array $pronunciation = [
        'bn' => '',
        'hi' => '',
        'es' => ''
    ];
    
    // For slug validation
    public bool $slugExists = false;
    public ?int $existingExpressionId = null;
    public ?string $existingExpressionName = null;
    
    // Original slug for comparison
    public string $originalSlug = '';

    // Mount function to initialize component
    public function mount($expressionId = null)
    {
        $this->expressionId = $expressionId;
        
        if ($this->expressionId) {
            $this->loadExpression();
        }
    }

    // Load expression data
    public function loadExpression()
    {
        $expression = Expression::with(['meanings.translations'])->find($this->expressionId);
        
        if (!$expression) {
            session()->flash('error', 'Expression not found');
            return;
        }
        
        $this->expression = $expression->expression;
        $this->expressionName = $expression->expression;
        $this->slug = $expression->slug;
        $this->originalSlug = $expression->slug;
        $this->part_of_speech = $expression->part_of_speech;
        
        // Load pronunciations
        $this->pronunciation = [
            'bn' => $expression->getTranslation('pronunciation', 'bn', false) ?: '',
            'hi' => $expression->getTranslation('pronunciation', 'hi', false) ?: '',
            'es' => $expression->getTranslation('pronunciation', 'es', false) ?: '',
        ];
        
        // Generate JSON representation
        $jsonData = [
            'expression' => $expression->expression,
            'slug' => $expression->slug,
            'part_of_speech' => $expression->part_of_speech,
            'pronunciation' => $this->pronunciation,
            'meanings' => []
        ];
        
        // Add meanings and translations
        foreach ($expression->meanings as $meaning) {
            $meaningData = [
                'id' => $meaning->id,
                'meaning' => $meaning->meaning,
                'translations' => []
            ];
            
            // Add translations if they exist
            if ($meaning->translations->isNotEmpty()) {
                $translation = $meaning->translations->first();
                
                if ($translation) {
                    // Get translations for each locale
                    $meaningData['translations'] = [
                        'bn' => $translation->getTranslation('translations', 'bn', false) ?: null,
                        'hi' => $translation->getTranslation('translations', 'hi', false) ?: null,
                        'es' => $translation->getTranslation('translations', 'es', false) ?: null,
                    ];
                }
            }
            
            $jsonData['meanings'][] = $meaningData;
        }
        
        $this->jsonInput = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->parsedData = $jsonData;
    }

    // Validation rules
    protected function rules()
    {
        $rules = [
            'expression' => 'required|string|max:255',
            'part_of_speech' => 'nullable|string|max:255',
            'pronunciation.bn' => 'nullable|string|max:255',
            'pronunciation.hi' => 'nullable|string|max:255',
            'pronunciation.es' => 'nullable|string|max:255',
        ];
        
        // Only validate slug uniqueness if it has changed
        if ($this->slug !== $this->originalSlug) {
            $rules['slug'] = 'required|string|max:255|unique:expressions,slug';
        } else {
            $rules['slug'] = 'required|string|max:255';
        }
        
        return $rules;
    }

    // Get parts of speech from the Expression model
    public function getPartsOfSpeech()
    {
        return [
            'noun',
            'verb',
            'adjective',
            'adverb',
            'preposition',
            'conjunction',
            'interjection',
            'idiom',
            'phrase',
            'proverb'
        ];
    }

    // Parse JSON input
    public function parseJson()
    {
        try {
            $this->parsedData = json_decode($this->jsonInput, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                session()->flash('error', 'Invalid JSON format: ' . json_last_error_msg());
                return false;
            }
            
            // Extract data from JSON
            $this->expression = $this->parsedData['expression'] ?? '';
            $this->slug = $this->parsedData['slug'] ?? '';
            $this->part_of_speech = $this->parsedData['part_of_speech'] ?? null;
            
            // Handle pronunciations
            if (isset($this->parsedData['pronunciation'])) {
                $this->pronunciation = [
                    'bn' => $this->parsedData['pronunciation']['bn'] ?? '',
                    'hi' => $this->parsedData['pronunciation']['hi'] ?? '',
                    'es' => $this->parsedData['pronunciation']['es'] ?? '',
                ];
            }
            
            // Check if slug has changed
            if ($this->slug !== $this->originalSlug) {
                $this->checkSlugExists();
            }
            
            session()->flash('message', 'JSON parsed successfully!');
            return true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error parsing JSON: ' . $e->getMessage());
            return false;
        }
    }

    // Auto-generate slug when expression changes
    public function updatedExpression($value)
    {
        $this->slug = Str::slug(Str::limit($value, 100));
        $this->checkSlugExists();
    }
    
    // Check if the slug already exists in the database
    public function checkSlugExists()
    {
        if (empty($this->slug)) {
            $this->slugExists = false;
            $this->existingExpressionId = null;
            $this->existingExpressionName = null;
            return;
        }
        
        // If slug hasn't changed, no need to check
        if ($this->slug === $this->originalSlug) {
            $this->slugExists = false;
            $this->existingExpressionId = null;
            $this->existingExpressionName = null;
            return true;
        }
        
        $existingExpression = Expression::where('slug', $this->slug)
            ->where('id', '!=', $this->expressionId)
            ->first();
        
        if ($existingExpression) {
            $this->slugExists = true;
            $this->existingExpressionId = $existingExpression->id;
            $this->existingExpressionName = $existingExpression->expression;
            return false;
        } else {
            $this->slugExists = false;
            $this->existingExpressionId = null;
            $this->existingExpressionName = null;
            return true;
        }
    }

    // Open the modal
    public function openModal()
    {
        $this->loadExpression();
        $this->showModal = true;
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Update the expression
    public function updateExpression()
    {
        // Check if slug exists before validation (only if it changed)
        if ($this->slug !== $this->originalSlug && !$this->checkSlugExists()) {
            // If slug exists, don't proceed with validation
            return;
        }
        
        $this->validate();

        $expression = Expression::find($this->expressionId);
        
        if (!$expression) {
            session()->flash('error', 'Expression not found');
            return;
        }

        // Update basic expression data
        $expression->expression = $this->expression;
        $expression->slug = $this->slug;
        $expression->part_of_speech = $this->part_of_speech;
        
        // Filter out empty pronunciation values
        $pronunciationData = array_filter($this->pronunciation, fn($value) => !empty($value));

        // Set pronunciations using Spatie's translatable
        foreach ($pronunciationData as $locale => $value) {
            $expression->setTranslation('pronunciation', $locale, $value);
        }
        $expression->save();

        // Update meanings if they exist in the JSON
        if (isset($this->parsedData['meanings']) && is_array($this->parsedData['meanings'])) {
            // Get existing meaning IDs
            $existingMeaningIds = $expression->meanings->pluck('id')->toArray();
            $updatedMeaningIds = [];
            
            foreach ($this->parsedData['meanings'] as $meaningData) {
                if (isset($meaningData['meaning']) && !empty($meaningData['meaning'])) {
                    // Check if this is an existing meaning or a new one
                    $meaningId = $meaningData['id'] ?? null;
                    
                    if ($meaningId && in_array($meaningId, $existingMeaningIds)) {
                        // Update existing meaning
                        $meaning = $expression->meanings()->find($meaningId);
                        $meaning->update([
                            'meaning' => $meaningData['meaning'],
                        ]);
                        $updatedMeaningIds[] = $meaningId;
                        
                        // Update translations if they exist
                        if (isset($meaningData['translations']) && is_array($meaningData['translations'])) {
                            $translationData = [];
                            
                            // Extract translations for different locales
                            foreach (['bn', 'hi', 'es'] as $locale) {
                                if (isset($meaningData['translations'][$locale]) && !empty($meaningData['translations'][$locale])) {
                                    $translationData[$locale] = $meaningData['translations'][$locale];
                                }
                            }
                            
                            if (!empty($translationData)) {
                                // Get or create translation
                                $translation = $meaning->translations->first();
                                
                                if ($translation) {
                                    // Update existing translation
                                    foreach ($translationData as $locale => $value) {
                                        $translation->setTranslation('translations', $locale, $value);
                                    }
                                    $translation->save();
                                } else {
                                    // Create new translation
                                    $meaning->translations()->create([
                                        'translations' => $translationData,
                                    ]);
                                }
                            }
                        }
                    } else {
                        // Create new meaning
                        $meaning = $expression->meanings()->create([
                            'meaning' => $meaningData['meaning'],
                        ]);
                        
                        // Create translations if they exist
                        if (isset($meaningData['translations']) && is_array($meaningData['translations'])) {
                            $translationData = [];
                            
                            // Extract translations for different locales
                            foreach (['bn', 'hi', 'es'] as $locale) {
                                if (isset($meaningData['translations'][$locale]) && !empty($meaningData['translations'][$locale])) {
                                    $translationData[$locale] = $meaningData['translations'][$locale];
                                }
                            }
                            
                            if (!empty($translationData)) {
                                $meaning->translations()->create([
                                    'translations' => $translationData,
                                ]);
                            }
                        }
                        
                        $updatedMeaningIds[] = $meaning->id;
                    }
                }
            }
            
            // Delete meanings that are not in the updated data
            $meaningsToDelete = array_diff($existingMeaningIds, $updatedMeaningIds);
            if (!empty($meaningsToDelete)) {
                $expression->meanings()->whereIn('id', $meaningsToDelete)->delete();
            }
        }

        // Show success message
        $this->showSuccessMessage = true;
    }
};
?>

<div>
    @if(session()->has('message'))
        <div class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-2 mb-2" role="alert">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-100 dark:bg-red-800 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-2 mb-2" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Edit Expression Button -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
        Edit Expression Using JSON
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
        <div class="fixed inset-0 transform transition-all" wire:click="closeModal">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-800 opacity-75"></div>
        </div>

        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg mx-auto">
            <!-- Success Message -->
            @if ($showSuccessMessage)
                <div class="p-6">
                    <div class="bg-green-50 dark:bg-green-900 border-l-4 border-green-500 p-4 mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm leading-5 text-green-700 dark:text-green-200">
                                    Expression "{{ $expressionName }}" has been updated successfully!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('backend.expression.show', $expressionId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            View Expression
                        </a>

                        <button wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Close
                        </button>
                    </div>
                </div>
            @else
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Expression Using JSON</h3>
                </div>

                <!-- Modal Body -->
                <div class="p-6 dark:bg-gray-800">
                    <!-- JSON Input Section -->
                    <div class="mb-4">
                        <label for="jsonInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">JSON Input</label>
                        <textarea id="jsonInput" wire:model="jsonInput" rows="10" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Paste your JSON here"></textarea>
                        <div class="mt-2">
                            <button type="button" wire:click="parseJson" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                                Parse JSON
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="updateExpression">
                        <!-- Expression Input -->
                        <div class="mb-4">
                            <label for="expression" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expression</label>
                            <input type="text" id="expression" wire:model.live="expression" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter expression">
                            @error('expression') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Slug Input -->
                        <div class="mb-4">
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input type="text" id="slug" wire:model.live="slug" wire:blur="checkSlugExists" class="block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter slug">
                            </div>
                            @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            
                            @if($slugExists)
                            <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 text-xs rounded">
                                <p>This slug is already in use by another expression: 
                                    <a href="{{ route('backend.expression.show', $existingExpressionId) }}" class="underline font-semibold" target="_blank">
                                        {{ $existingExpressionName }}
                                    </a>
                                </p>
                            </div>
                            @endif
                        </div>

                        <!-- Part of Speech -->
                        <div class="mb-4">
                            <label for="part_of_speech" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Part of Speech</label>
                            <select id="part_of_speech" wire:model="part_of_speech" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Select part of speech</option>
                                @foreach($this->getPartsOfSpeech() as $pos)
                                    <option value="{{ $pos }}">{{ ucfirst($pos) }}</option>
                                @endforeach
                            </select>
                            @error('part_of_speech') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Pronunciation -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pronunciation</label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Bengali Pronunciation -->
                                <div>
                                    <label for="pronunciation_bn" class="block text-xs text-gray-500 dark:text-gray-400">Bengali</label>
                                    <input type="text" id="pronunciation_bn" wire:model="pronunciation.bn" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Bengali pronunciation">
                                    @error('pronunciation.bn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Hindi Pronunciation -->
                                <div>
                                    <label for="pronunciation_hi" class="block text-xs text-gray-500 dark:text-gray-400">Hindi</label>
                                    <input type="text" id="pronunciation_hi" wire:model="pronunciation.hi" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Hindi pronunciation">
                                    @error('pronunciation.hi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Spanish Pronunciation -->
                                <div>
                                    <label for="pronunciation_es" class="block text-xs text-gray-500 dark:text-gray-400">Spanish</label>
                                    <input type="text" id="pronunciation_es" wire:model="pronunciation.es" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Spanish pronunciation">
                                    @error('pronunciation.es') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Parsed Meanings Preview (Read-only) -->
                        @if(isset($parsedData['meanings']) && count($parsedData['meanings']) > 0)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meanings from JSON (will be updated)</label>
                                
                                <div class="border border-gray-200 dark:border-gray-600 rounded-md p-3 bg-gray-50 dark:bg-gray-700">
                                    @foreach($parsedData['meanings'] as $index => $meaning)
                                        <div class="mb-2 pb-2 {{ $index > 0 ? 'border-t border-gray-200 dark:border-gray-600 pt-2' : '' }}">
                                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $meaning['meaning'] ?? 'N/A' }}</p>
                                            
                                            @if(isset($meaning['translations']))
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <p><span class="font-semibold">Bengali:</span> {{ $meaning['translations']['bn'] ?? 'N/A' }}</p>
                                                    <p><span class="font-semibold">Hindi:</span> {{ $meaning['translations']['hi'] ?? 'N/A' }}</p>
                                                    <p><span class="font-semibold">Spanish:</span> {{ $meaning['translations']['es'] ?? 'N/A' }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="button" wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2">
                                Cancel
                            </button>
                            
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Update Expression
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>