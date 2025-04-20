<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Modules\Article\Models\Article;
use Modules\ArticleExpression\Models\ArticleExpressionSet;

new class extends Component {
    public bool $showModal = false;
    public bool $showSuccessMessage = false;
    public ?int $createdSetId = null;
    public ?string $createdSetTitle = null;
    
    // Article this set belongs to
    public ?int $articleId = null;
    public ?Article $article = null;
    
    // Form fields
    public string $title = '';
    public ?string $content = null;
    public int $displayOrder = 0;
    public array $title_translation = [
        'bn' => '',
        'hi' => ''
    ];
    public array $content_translation = [
        'bn' => '',
        'hi' => ''
    ];
    
    // Initialize component
    public function mount($articleId = null)
    {
        $this->articleId = $articleId;
        if ($this->articleId) {
            $this->article = Article::find($this->articleId);
            
            // Set default display order to be after the last set
            $lastSet = ArticleExpressionSet::where('article_id', $this->articleId)
                ->orderBy('display_order', 'desc')
                ->first();
                
            $this->displayOrder = $lastSet ? $lastSet->display_order + 1 : 1;
        }
    }

    // Validation rules
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'displayOrder' => 'required|integer|min:0',
            'title_translation.bn' => 'nullable|string|max:255',
            'title_translation.hi' => 'nullable|string|max:255',
            'content_translation.bn' => 'nullable|string',
            'content_translation.hi' => 'nullable|string',
        ];
    }

    // Open the modal
    public function openModal()
    {
        if (!$this->articleId) {
            session()->flash('error', 'No article selected. Please select an article first.');
            return;
        }
        
        $this->reset([
            'title', 'content', 'title_translation', 'content_translation', 
            'showSuccessMessage', 'createdSetId', 'createdSetTitle'
        ]);
        $this->showModal = true;
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Create a new article expression set
    public function createArticleExpressionSet()
    {
        if (!$this->articleId) {
            session()->flash('error', 'No article selected. Please select an article first.');
            return;
        }
        
        $this->validate();

        // Filter out empty translation values
        $titleTranslationData = array_filter($this->title_translation, fn($value) => !empty($value));
        $contentTranslationData = array_filter($this->content_translation, fn($value) => !empty($value));

        // Create the article expression set
        $set = ArticleExpressionSet::create([
            'article_id' => $this->articleId,
            'title' => $this->title,
            'content' => $this->content,
            'display_order' => $this->displayOrder,
            'user_id' => auth()->id(),
        ]);

        // Set translations using Spatie's translatable
        foreach ($titleTranslationData as $locale => $value) {
            $set->setTranslation('title_translation', $locale, $value);
        }
        
        foreach ($contentTranslationData as $locale => $value) {
            $set->setTranslation('content_translation', $locale, $value);
        }
        
        $set->save();

        // Show success message
        $this->createdSetId = $set->id;
        $this->createdSetTitle = $set->title;
        $this->showSuccessMessage = true;
        
        // Emit event for parent components to refresh
        $this->dispatch('article-expression-set-created', ['articleId' => $this->articleId]);
    }

    // Reset form for adding another set
    public function addAnotherSet()
    {
        $this->reset([
            'title', 'content', 'title_translation', 'content_translation', 
            'showSuccessMessage', 'createdSetId', 'createdSetTitle'
        ]);
        
        // Increment display order for the next set
        $this->displayOrder++;
    }
};
?>

<div>
    @if(session()->has('error'))
        <div class="bg-red-100 dark:bg-red-800 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-2 mb-2" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Button to open modal -->
    <button wire:click="openModal" class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-0.5 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Expression Set
    </button>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50">
        <div class="fixed inset-0 transform transition-all" wire:click="closeModal">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-800 opacity-75"></div>
        </div>

        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-4xl mx-auto">
            <!-- Success Message -->
            @if ($showSuccessMessage)
                <div class="p-6">
                    <div class="bg-green-50 dark:bg-green-900 border-l-4 border-green-500 p-4 mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700 dark:text-green-200">
                                    Expression Set "{{ $createdSetTitle }}" has been created successfully!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" wire:click="addAnotherSet" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                            Add Another Expression Set
                        </button>
                        <button type="button" wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                            Close
                        </button>
                    </div>
                </div>
            @else
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add New Expression Set to Article</h3>
                    @if($article)
                        <p class="text-sm text-gray-600 dark:text-gray-400">Article: {{ $article->title }}</p>
                    @endif
                </div>

                <form wire:submit.prevent="createArticleExpressionSet" class="p-6 dark:bg-gray-800">
                    <!-- Title Input -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input id="title" type="text" wire:model="title" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter title...">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Display Order -->
                    <div class="mb-4">
                        <label for="displayOrder" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                        <input id="displayOrder" type="number" wire:model="displayOrder" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" min="0">
                        @error('displayOrder') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Title Translations -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title Translations</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bengali Title Translation -->
                            <div>
                                <label for="title_translation_bn" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Bengali</label>
                                <input id="title_translation_bn" type="text" wire:model="title_translation.bn" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Bengali title translation">
                                @error('title_translation.bn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Hindi Title Translation -->
                            <div>
                                <label for="title_translation_hi" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hindi</label>
                                <input id="title_translation_hi" type="text" wire:model="title_translation.hi" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Hindi title translation">
                                @error('title_translation.hi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Content Input -->
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <textarea id="content" wire:model="content" rows="5" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Write your expression set content here..."></textarea>
                        @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content Translations -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content Translations</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bengali Content Translation -->
                            <div>
                                <label for="content_translation_bn" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Bengali</label>
                                <textarea id="content_translation_bn" wire:model="content_translation.bn" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Bengali content translation"></textarea>
                                @error('content_translation.bn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Hindi Content Translation -->
                            <div>
                                <label for="content_translation_hi" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hindi</label>
                                <textarea id="content_translation_hi" wire:model="content_translation.hi" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Hindi content translation"></textarea>
                                @error('content_translation.hi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                            Create Expression Set
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>