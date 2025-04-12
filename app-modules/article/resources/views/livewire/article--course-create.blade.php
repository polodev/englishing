<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Modules\Article\Models\Course;

new class extends Component {
    public bool $showModal = false;
    public bool $showSuccessMessage = false;
    public ?int $createdCourseId = null;
    public ?string $createdCourseTitle = null;

    // Form fields
    public string $title = '';
    public string $slug = '';
    public ?string $content = null;
    public array $title_translation = [
        'bn' => '',
        'hi' => ''
    ];
    public array $content_translation = [
        'bn' => '',
        'hi' => ''
    ];
    
    // For slug validation
    public bool $slugExists = false;
    public ?int $existingCourseId = null;
    public ?string $existingCourseTitle = null;

    // Initialize component
    public function mount()
    {
        // Initialize component
    }

    // Validation rules
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:courses,slug',
            'content' => 'nullable|string',
            'title_translation.bn' => 'nullable|string|max:255',
            'title_translation.hi' => 'nullable|string|max:255',
            'content_translation.bn' => 'nullable|string',
            'content_translation.hi' => 'nullable|string',
        ];
    }

    // Auto-generate slug when title changes
    public function updatedTitle($value)
    {
        if (!empty($value)) {
            $this->slug = Str::slug($value);
            $this->slugExists = false;
            $this->existingCourseId = null;
            $this->existingCourseTitle = null;
        }
    }
    
    // Check if the slug already exists in the database - only called on form submission
    public function validateSlug()
    {
        if (empty($this->slug)) {
            return false;
        }
        
        $existingCourse = Course::where('slug', $this->slug)->first();
        
        if ($existingCourse) {
            $this->slugExists = true;
            $this->existingCourseId = $existingCourse->id;
            $this->existingCourseTitle = $existingCourse->title;
            return false;
        }
        
        return true;
    }

    // Open the modal
    public function openModal()
    {
        logger('CourseCreate::openModal called from Volt component');
        
        $this->reset(['title', 'slug', 'content', 'title_translation', 'content_translation', 'showSuccessMessage', 'createdCourseId', 'createdCourseTitle', 'slugExists', 'existingCourseId', 'existingCourseTitle']);
        $this->showModal = true;
        
        // For debugging
        session()->flash('message', 'Modal opened');
    }

    // Close the modal
    public function closeModal()
    {
        $this->showModal = false;
    }

    // Create a new course
    public function createCourse()
    {
        // Generate slug from title if not already set
        if (empty($this->slug) && !empty($this->title)) {
            $this->slug = Str::slug($this->title);
        }
        
        // Check if slug exists before validation
        if (!$this->validateSlug()) {
            // If slug exists, don't proceed with validation
            return;
        }
        
        $this->validate();

        // Filter out empty translation values
        $titleTranslationData = array_filter($this->title_translation, fn($value) => !empty($value));
        $contentTranslationData = array_filter($this->content_translation, fn($value) => !empty($value));

        // Create the course
        $course = Course::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'user_id' => auth()->id(),
        ]);

        // Set translations using Spatie's translatable
        foreach ($titleTranslationData as $locale => $value) {
            $course->setTranslation('title_translation', $locale, $value);
        }
        
        foreach ($contentTranslationData as $locale => $value) {
            $course->setTranslation('content_translation', $locale, $value);
        }
        
        $course->save();

        // Show success message
        $this->createdCourseId = $course->id;
        $this->createdCourseTitle = $course->title;
        $this->showSuccessMessage = true;
    }

    // Reset form for adding another course
    public function addAnotherCourse()
    {
        $this->reset(['title', 'slug', 'content', 'title_translation', 'content_translation', 'showSuccessMessage', 'slugExists', 'existingCourseId', 'existingCourseTitle']);
    }
};
?>

<div>
    @if(session()->has('message'))
        <div class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-2 mb-2" role="alert">
            {{ session('message') }}
        </div>
    @endif

    <!-- Add Course Button -->
    <button
        wire:click="openModal"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add New Course
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
                                    Course "{{ $createdCourseTitle }}" has been created successfully!
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('backend::courses.show', $createdCourseId) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                            View Course
                        </a>

                        <button wire:click="addAnotherCourse" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500 active:bg-gray-900 dark:active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            Add Another Course
                        </button>

                        <button wire:click="closeModal" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                            Close
                        </button>
                    </div>
                </div>
            @else
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add New Course</h3>
                </div>

                <form wire:submit.prevent="createCourse" class="p-6 dark:bg-gray-800">
                    <!-- Title Input -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input type="text" id="title" wire:model.live.debounce.300ms="title" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter course title">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug Input -->
                    <div class="mb-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                        <div class="flex items-center">
                            <input type="text" id="slug" wire:model="slug" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter slug">
                        </div>
                        @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        
                        @if($slugExists)
                            <div class="mt-2 text-red-500 text-xs">
                                This slug is already in use by course "{{ $existingCourseTitle }}".
                                <a href="{{ route('backend::courses.show', $existingCourseId) }}" class="text-blue-500 hover:underline" target="_blank">View Course</a>
                            </div>
                        @endif
                    </div>

                    <!-- Content Input -->
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <div wire:ignore>
                            <x-markdown-editor 
                                id="content-create"
                                :wire-model="'content'"
                                :value="$content"
                                placeholder="Write your course content here..."
                            />
                        </div>
                        @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title Translations</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bengali Title Translation -->
                            <div>
                                <label for="title_translation_bn" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Bengali</label>
                                <input type="text" id="title_translation_bn" wire:model="title_translation.bn" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Bengali title translation">
                                @error('title_translation.bn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Hindi Title Translation -->
                            <div>
                                <label for="title_translation_hi" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Hindi</label>
                                <input type="text" id="title_translation_hi" wire:model="title_translation.hi" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Hindi title translation">
                                @error('title_translation.hi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

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
                            Create Course
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>