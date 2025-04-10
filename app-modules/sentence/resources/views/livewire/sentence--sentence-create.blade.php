<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Modules\Sentence\Models\Sentence;

new class extends Component {
    public bool $showModal = false;
    public bool $showSuccessMessage = false;
    public ?int $createdSentenceId = null;
    public ?string $createdSentenceText = null;

    // Form fields
    public string $sentence = '';
    public string $slug = '';
    public array $pronunciation = [
        'bn' => '',
        'hi' => '',
    ];
    
    // For slug validation
    public bool $slugExists = false;
    public ?int $existingSentenceId = null;
    public ?string $existingSentenceText = null;

    // Debug function to check if component is working
    public function mount()
    {
        // Initialize component
    }

    // Validation rules
    protected function rules()
    {
        return [
            'sentence' => 'required|string|max:1000',
            'slug' => 'required|string|max:255|unique:sentences,slug',
            'pronunciation.bn' => 'nullable|string|max:1000',
            'pronunciation.hi' => 'nullable|string|max:1000',
            'pronunciation.es' => 'nullable|string|max:1000',
        ];
    }

    // Auto-generate slug when sentence changes
    public function updatedSentence($value)
    {
        $this->slug = Str::slug(Str::limit($value, 100));
        $this->checkSlugExists();
    }
    
    // Check if the slug already exists in the database
    public function checkSlugExists()
    {
        if (empty($this->slug)) {
            $this->slugExists = false;
            $this->existingSentenceId = null;
            $this->existingSentenceText = null;
            return;
        }
        
        $existingSentence = Sentence::where('slug', $this->slug)->first();
        
        if ($existingSentence) {
            $this->slugExists = true;
            $this->existingSentenceId = $existingSentence->id;
            $this->existingSentenceText = $existingSentence->sentence;
            return false;
        } else {
            $this->slugExists = false;
            $this->existingSentenceId = null;
            $this->existingSentenceText = null;
            return true;
        }
    }

    // Open the modal
    public function openModal()
    {
        // Add detailed logging
        logger('SentenceCreate::openModal called from Volt component');
        logger('Current state before reset: ' . json_encode([
            'showModal' => $this->showModal,
            'sentence' => $this->sentence,
        ]));
        
        $this->reset(['sentence', 'slug', 'pronunciation', 'showSuccessMessage', 'createdSentenceId', 'createdSentenceText', 'slugExists', 'existingSentenceId', 'existingSentenceText']);
        $this->showModal = true;
        
        // For debugging
        session()->flash('message', 'Modal opened');
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Create a new sentence
    public function createSentence()
    {
        // Generate slug from sentence if not already set
        if (empty($this->slug) && !empty($this->sentence)) {
            $this->slug = Str::slug(Str::limit($this->sentence, 100));
        }
        
        // Check if slug exists before validation
        if (!$this->checkSlugExists()) {
            // If slug exists, don't proceed with validation
            return;
        }
        
        $this->validate();

        // Filter out empty pronunciation values
        $pronunciationData = array_filter($this->pronunciation, fn($value) => !empty($value));

        // Create the sentence
        $sentence = Sentence::create([
            'sentence' => $this->sentence,
            'slug' => $this->slug,
        ]);

        // Set pronunciations using Spatie's translatable
        foreach ($pronunciationData as $locale => $value) {
            $sentence->setTranslation('pronunciation', $locale, $value);
        }
        $sentence->save();

        // Show success message
        $this->createdSentenceId = $sentence->id;
        $this->createdSentenceText = $sentence->sentence;
        $this->showSuccessMessage = true;
    }

    // Reset form for adding another sentence
    public function addAnotherSentence()
    {
        $this->reset(['sentence', 'slug', 'pronunciation', 'showSuccessMessage', 'slugExists', 'existingSentenceId', 'existingSentenceText']);
    }
};
?>

<div>
    @if(session()->has('message'))
        <div class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-2 mb-2" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <!-- Add Sentence Button -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add New Sentence
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
                                    Sentence has been created successfully!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('backend::sentences.show', $createdSentenceId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            View Sentence
                        </a>

                        <button wire:click="addAnotherSentence" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Add Another Sentence
                        </button>
                    </div>
                </div>
            @else
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add New Sentence</h3>
                </div>

                <form wire:submit.prevent="createSentence" class="p-6 dark:bg-gray-800">
                    <!-- Sentence Input -->
                    <div class="mb-4">
                        <label for="sentence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sentence</label>
                        <textarea id="sentence" wire:model.live="sentence" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter sentence"></textarea>
                        @error('sentence') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug Input -->
                    <div class="mb-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                        <div class="flex items-center">
                            <input type="text" id="slug" wire:model="slug" readonly class="mt-1 block w-full bg-gray-100 dark:bg-gray-600 border-gray-300 dark:border-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ $slug }}">
                            
                            @if($slugExists)
                                <a href="{{ route('backend::sentences.show', $existingSentenceId) }}" target="_blank" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        
                        @if($slugExists)
                            <div class="mt-2 text-amber-600 dark:text-amber-400 text-sm">
                                This slug is already used by "<a href="{{ route('backend::sentences.show', $existingSentenceId) }}" class="underline" target="_blank">{{ Str::limit($existingSentenceText, 50) }}</a>". Please modify the sentence to generate a unique slug.
                            </div>
                        @endif
                    </div>

                    <!-- Pronunciation Section -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pronunciation</label>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Bengali Pronunciation -->
                            <div>
                                <label for="pronunciation_bn" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Bengali (bn)</label>
                                <textarea id="pronunciation_bn" wire:model="pronunciation.bn" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Bengali pronunciation"></textarea>
                                @error('pronunciation.bn') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Hindi Pronunciation -->
                            <div>
                                <label for="pronunciation_hi" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hindi (hi)</label>
                                <textarea id="pronunciation_hi" wire:model="pronunciation.hi" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Hindi pronunciation"></textarea>
                                @error('pronunciation.hi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-6">
                        <button type="button" wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-white uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition mr-2">
                            Cancel
                        </button>
                        
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Create Sentence
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>