<x-ui-backend::layout>
    <div class="container mx-auto px-4 py-6">
        <div class="mb-5 flex justify-between">
            <a href="{{ route('backend::articles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Articles List
            </a>
            <a href="{{ route('backend::articles.show', $article) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Article
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-6 py-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Article: {{ $article->title }}</h1>
            </div>

            <form action="{{ route('backend::articles.update', $article) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Validation Error</p>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Basic Information -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug <span class="text-red-500">*</span></label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $article->slug) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                        </div>

                        <!-- Course -->
                        <div>
                            <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Course</label>
                            <select name="course_id" id="course_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">No Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ (old('course_id', $article->course_id) == $course->id) ? 'selected' : '' }}>{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Display Order -->
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $article->display_order) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Order in which this article appears in its course</p>
                        </div>
                    </div>

                    <!-- Premium Content Toggle -->
                    <div class="mt-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_premium" value="1" {{ old('is_premium', $article->is_premium) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Premium Content (requires subscription)</span>
                        </label>
                    </div>
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">Content</h2>
                    
                    <!-- Main Content -->
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <x-markdown-editor-ace-editor 
                            id="content"
                            name="content"
                            :value="old('content', $article->content)"
                            placeholder="Write your article content here..."
                        />
                        @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Supports Markdown formatting</p>
                    </div>

                    <!-- Excerpt -->
                    <div class="mb-4">
                        <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Excerpt</label>
                        <textarea name="excerpt" id="excerpt" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('excerpt', $article->excerpt) }}</textarea>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">A short summary of the article</p>
                    </div>
                </div>

                <!-- Title Translations -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">Title Translations</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bengali Title Translation -->
                        <div>
                            <label for="title_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bengali</label>
                            <input type="text" name="title_translation[bn]" id="title_translation_bn" value="{{ old('title_translation.bn', $article->getTranslation('title_translation', 'bn', false)) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>

                        <!-- Hindi Title Translation -->
                        <div>
                            <label for="title_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hindi</label>
                            <input type="text" name="title_translation[hi]" id="title_translation_hi" value="{{ old('title_translation.hi', $article->getTranslation('title_translation', 'hi', false)) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>

                <!-- Content Translations -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">Content Translations</h2>
                    
                    <!-- Bengali Content Translation -->
                    <div class="mb-4">
                        <label for="content_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bengali</label>
                        <x-markdown-editor-ace-editor 
                            id="content_translation_bn"
                            name="content_translation[bn]"
                            :value="old('content_translation.bn', $article->getTranslation('content_translation', 'bn', false))"
                            placeholder="Bengali content translation..."
                        />
                        @error('content_translation.bn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Hindi Content Translation -->
                    <div class="mb-4">
                        <label for="content_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hindi</label>
                        <x-markdown-editor-ace-editor 
                            id="content_translation_hi"
                            name="content_translation[hi]"
                            :value="old('content_translation.hi', $article->getTranslation('content_translation', 'hi', false))"
                            placeholder="Hindi content translation..."
                        />
                        @error('content_translation.hi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Excerpt Translations -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600 pb-2 mb-4">Excerpt Translations</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bengali Excerpt Translation -->
                        <div>
                            <label for="excerpt_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bengali</label>
                            <textarea name="excerpt_translation[bn]" id="excerpt_translation_bn" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('excerpt_translation.bn', $article->getTranslation('excerpt_translation', 'bn', false)) }}</textarea>
                        </div>

                        <!-- Hindi Excerpt Translation -->
                        <div>
                            <label for="excerpt_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hindi</label>
                            <textarea name="excerpt_translation[hi]" id="excerpt_translation_hi" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('excerpt_translation.hi', $article->getTranslation('excerpt_translation', 'hi', false)) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end">
                    <a href="{{ route('backend::articles.show', $article) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition mr-2">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                        Update Article
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Auto-generate slug from title if slug is empty
            $('#title').on('blur', function() {
                if ($('#slug').val() === '') {
                    const title = $(this).val();
                    const slug = title.toLowerCase()
                        .replace(/[^\w\s-]/g, '')  // Remove special characters
                        .replace(/\s+/g, '-')      // Replace spaces with hyphens
                        .replace(/-+/g, '-')       // Replace multiple hyphens with single hyphen
                        .trim();                   // Trim leading/trailing spaces
                    
                    $('#slug').val(slug);
                }
            });
        });
    </script>
    @endpush
</x-ui-backend::layout>
