<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Modules\Word\Models\Word;

new class extends Component {
    public bool $showModal = false;
    public bool $showSuccessMessage = false;
    public ?int $createdWordId = null;
    public ?string $createdWordName = null;

    // Form fields
    public string $word = '';
    public string $slug = '';
    public string $phonetic = '';
    public ?string $part_of_speech = null;
    public array $pronunciation = [
        'bn' => '',
        'hi' => ''
    ];
    
    // For slug validation
    public bool $slugExists = false;
    public ?int $existingWordId = null;
    public ?string $existingWordName = null;

    // Debug function to check if component is working
    public function mount()
    {
        // Initialize component
    }

    // Validation rules
    protected function rules()
    {
        return [
            'word' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:words,slug',
            'phonetic' => 'nullable|string|max:255',
            'part_of_speech' => 'nullable|string|max:255',
            'pronunciation.bn' => 'nullable|string|max:255',
            'pronunciation.hi' => 'nullable|string|max:255',
        ];
    }

    // Get parts of speech from the Word model
    public function getPartsOfSpeech()
    {
        return Word::getPartsOfSpeech();
    }

    // Auto-generate slug when word changes
    public function updatedWord($value)
    {
        $this->slug = Str::slug($value);
    }
    
    // Check if the slug already exists in the database
    public function checkSlugExists()
    {
        if (empty($this->slug)) {
            $this->slugExists = false;
            $this->existingWordId = null;
            $this->existingWordName = null;
            return;
        }
        
        $existingWord = Word::where('slug', $this->slug)->first();
        
        if ($existingWord) {
            $this->slugExists = true;
            $this->existingWordId = $existingWord->id;
            $this->existingWordName = $existingWord->word;
            return false;
        } else {
            $this->slugExists = false;
            $this->existingWordId = null;
            $this->existingWordName = null;
            return true;
        }
    }

    // Open the modal
    public function openModal()
    {
        // Add detailed logging instead of dd() which breaks the flow
        logger('WordCreate::openModal called from Volt component');
        logger('Current state before reset: ' . json_encode([
            'showModal' => $this->showModal,
            'word' => $this->word,
        ]));
        
        $this->reset(['word', 'slug', 'phonetic', 'part_of_speech', 'pronunciation', 'showSuccessMessage', 'createdWordId', 'createdWordName', 'slugExists', 'existingWordId', 'existingWordName']);
        $this->showModal = true;
        
        // For debugging
        session()->flash('message', 'Modal opened');
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Create a new word
    public function createWord()
    {
        // Generate slug from word if not already set
        if (empty($this->slug) && !empty($this->word)) {
            $this->slug = Str::slug($this->word);
        }
        
        // Check if slug exists before validation
        if (!$this->checkSlugExists()) {
            // If slug exists, don't proceed with validation
            return;
        }
        
        $this->validate();

        // Filter out empty pronunciation values
        $pronunciationData = array_filter($this->pronunciation, fn($value) => !empty($value));

        // Create the word
        $word = Word::create([
            'word' => $this->word,
            'slug' => $this->slug,
            'phonetic' => $this->phonetic,
            'part_of_speech' => $this->part_of_speech,
        ]);

        // Set pronunciations using Spatie's translatable
        foreach ($pronunciationData as $locale => $value) {
            $word->setTranslation('pronunciation', $locale, $value);
        }
        $word->save();

        // Show success message
        $this->createdWordId = $word->id;
        $this->createdWordName = $word->word;
        $this->showSuccessMessage = true;
    }

    // Reset form for adding another word
    public function addAnotherWord()
    {
        $this->reset(['word', 'slug', 'phonetic', 'part_of_speech', 'pronunciation', 'showSuccessMessage', 'slugExists', 'existingWordId', 'existingWordName']);
    }
};
?>

<div>
    @if(session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-2 mb-2" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <!-- Add Word Button -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add New Word
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
        <div class="fixed inset-0 transform transition-all" wire:click="closeModal">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg mx-auto">
            <!-- Success Message -->
            @if ($showSuccessMessage)
                <div class="p-6">
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm leading-5 text-green-700">
                                    Word "{{ $createdWordName }}" has been created successfully!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('backend::words.show', $createdWordId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            View Word
                        </a>

                        <div>
                            <button wire:click="addAnotherWord" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                                Add Another Word
                            </button>

                            <button wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition ml-2">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gray-100 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Add New Word</h3>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="createWord" class="p-6">
                    <!-- Word Input -->
                    <div class="mb-4">
                        <label for="word" class="block text-sm font-medium text-gray-700 mb-1">Word</label>
                        <input type="text" id="word" wire:model.live="word" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter word">
                        @error('word') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug Input (Read-only) -->
                    <div class="mb-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <div class="flex items-center">
                            <input type="text" id="slug" wire:model="slug" readonly class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" value="{{ $slug }}">
                            @if($slugExists)
                                <a href="{{ route('backend::words.show', $existingWordId) }}" target="_blank" class="ml-2 text-blue-600 hover:text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                        @if($slugExists)
                            <div class="mt-1 text-amber-600 text-sm">
                                This slug is already used by "{{ $existingWordName }}". Please modify the word to generate a unique slug.
                            </div>
                        @endif
                        @error('slug') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Phonetic Input -->
                    <div class="mb-4">
                        <label for="phonetic" class="block text-sm font-medium text-gray-700 mb-1">Phonetic</label>
                        <input type="text" id="phonetic" wire:model="phonetic" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter phonetic pronunciation">
                        @error('phonetic') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Part of Speech Dropdown -->
                    <div class="mb-4">
                        <label for="part_of_speech" class="block text-sm font-medium text-gray-700 mb-1">Part of Speech</label>
                        <select id="part_of_speech" wire:model="part_of_speech" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Select Part of Speech</option>
                            @foreach($this->getPartsOfSpeech() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('part_of_speech') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pronunciation Fields -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pronunciation</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bengali Pronunciation -->
                            <div>
                                <label for="pronunciation_bn" class="block text-sm font-medium text-gray-600 mb-1">Bengali</label>
                                <input type="text" id="pronunciation_bn" wire:model="pronunciation.bn" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Bengali pronunciation">
                                @error('pronunciation.bn') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Hindi Pronunciation -->
                            <div>
                                <label for="pronunciation_hi" class="block text-sm font-medium text-gray-600 mb-1">Hindi</label>
                                <input type="text" id="pronunciation_hi" wire:model="pronunciation.hi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Hindi pronunciation">
                                @error('pronunciation.hi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-200 disabled:opacity-25 transition mr-2">
                            Cancel
                        </button>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                            Create Word
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>