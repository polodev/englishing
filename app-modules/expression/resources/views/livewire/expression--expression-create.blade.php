<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Modules\Expression\Models\Expression;

new class extends Component {
    public bool $showModal = false;
    public bool $showSuccessMessage = false;
    public ?int $createdExpressionId = null;
    public ?string $createdExpressionName = null;

    // Form fields
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

    // Debug function to check if component is working
    public function mount()
    {
        // Initialize component
    }

    // Validation rules
    protected function rules()
    {
        return [
            'expression' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:expressions,slug',
            'part_of_speech' => 'nullable|string|max:255',
            'pronunciation.bn' => 'nullable|string|max:255',
            'pronunciation.hi' => 'nullable|string|max:255',
            'pronunciation.es' => 'nullable|string|max:255',
        ];
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
        
        $existingExpression = Expression::where('slug', $this->slug)->first();
        
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
        // Add detailed logging
        logger('ExpressionCreate::openModal called from Volt component');
        logger('Current state before reset: ' . json_encode([
            'showModal' => $this->showModal,
            'expression' => $this->expression,
        ]));
        
        $this->reset(['expression', 'slug', 'part_of_speech', 'pronunciation', 'showSuccessMessage', 'createdExpressionId', 'createdExpressionName', 'slugExists', 'existingExpressionId', 'existingExpressionName']);
        $this->showModal = true;
        
        // For debugging
        session()->flash('message', 'Modal opened');
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Create a new expression
    public function createExpression()
    {
        // Generate slug from expression if not already set
        if (empty($this->slug) && !empty($this->expression)) {
            $this->slug = Str::slug(Str::limit($this->expression, 100));
        }
        
        // Check if slug exists before validation
        if (!$this->checkSlugExists()) {
            // If slug exists, don't proceed with validation
            return;
        }
        
        $this->validate();

        // Filter out empty pronunciation values
        $pronunciationData = array_filter($this->pronunciation, fn($value) => !empty($value));

        // Create the expression
        $expression = Expression::create([
            'expression' => $this->expression,
            'slug' => $this->slug,
            'part_of_speech' => $this->part_of_speech,
            'source' => 'manual',
        ]);

        // Set pronunciations using Spatie's translatable
        foreach ($pronunciationData as $locale => $value) {
            $expression->setTranslation('pronunciation', $locale, $value);
        }
        $expression->save();

        // Show success message
        $this->createdExpressionId = $expression->id;
        $this->createdExpressionName = $expression->expression;
        $this->showSuccessMessage = true;
    }

    // Reset form for adding another expression
    public function addAnotherExpression()
    {
        $this->reset(['expression', 'slug', 'part_of_speech', 'pronunciation', 'showSuccessMessage', 'slugExists', 'existingExpressionId', 'existingExpressionName']);
    }
};
?>

<div>
    @if(session()->has('message'))
        <div class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-2 mb-2" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <!-- Add Expression Button -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add New Expression
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
                                    Expression "{{ $createdExpressionName }}" has been created successfully!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('backend.expression.show', $createdExpressionId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            View Expression
                        </a>

                        <button wire:click="addAnotherExpression" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Add Another Expression
                        </button>
                    </div>
                </div>
            @else
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add New Expression</h3>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="createExpression" class="p-6 dark:bg-gray-800">
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

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="button" wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2">
                            Cancel
                        </button>
                        
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Create Expression
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>