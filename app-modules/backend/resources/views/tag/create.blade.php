<x-ui-backend::layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('backend::tags.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Create New Tag</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <form action="{{ route('backend::tags.store') }}" method="POST">
                    @csrf

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Success!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title (English) -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (English) <span class="text-red-600">*</span></label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">The English display name of the tag.</p>
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug <span class="text-red-600">*</span></label>
                                <input type="text" id="slug" name="slug" value="{{ old('slug') }}" required 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">URL-friendly version of the title. Used in URLs.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Translations -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">Translations</h2>
                        
                        <div class="space-y-4">
                            <!-- Bangla Translation -->
                            <div>
                                <label for="title_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (Bangla)</label>
                                <input type="text" id="title_translation_bn" name="title_translation[bn]" value="{{ old('title_translation.bn') }}" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                            
                            <!-- Hindi Translation -->
                            <div>
                                <label for="title_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (Hindi)</label>
                                <input type="text" id="title_translation_hi" name="title_translation[hi]" value="{{ old('title_translation.hi') }}" 
                                       class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('backend::tags.index') }}" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                            Create Tag
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');
            
            titleInput.addEventListener('input', function() {
                // Generate slug from title
                slugInput.value = this.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove special characters
                    .replace(/\s+/g, '-') // Replace spaces with hyphens
                    .replace(/--+/g, '-') // Replace multiple hyphens with a single hyphen
                    .trim();
            });
        });
    </script>
    @endpush
</x-ui-backend::layout>
