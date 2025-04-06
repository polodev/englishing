<div>
    <!-- Add Word Button -->
    <button 
        wire:click="openModal"
        type="button"
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
                    
                    <!-- Slug Input -->
                    <div class="mb-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" id="slug" wire:model="slug" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter slug">
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
                            <option value="">Select part of speech</option>
                            @foreach($this->getPartsOfSpeech() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('part_of_speech') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Pronunciation Fields -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Pronunciation (Non-English Locales)</h4>
                        
                        <!-- Bangla Pronunciation -->
                        <div class="mb-2">
                            <label for="pronunciation_bn" class="block text-sm font-medium text-gray-700 mb-1">Bangla Pronunciation</label>
                            <input type="text" id="pronunciation_bn" wire:model="pronunciation.bn" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter Bangla pronunciation">
                            @error('pronunciation.bn') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Hindi Pronunciation -->
                        <div>
                            <label for="pronunciation_hi" class="block text-sm font-medium text-gray-700 mb-1">Hindi Pronunciation</label>
                            <input type="text" id="pronunciation_hi" wire:model="pronunciation.hi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter Hindi pronunciation">
                            @error('pronunciation.hi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-200 disabled:opacity-25 transition mr-2">
                            Cancel
                        </button>
                        
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            Create Word
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>
