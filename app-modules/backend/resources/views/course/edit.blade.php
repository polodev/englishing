<x-ui-backend::layout>

<div class="container mx-auto px-4 py-6">
    <div class="mb-5 flex justify-between">
        <a href="{{ route('backend::courses.show', $course) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Course
        </a>
        <a href="{{ route('backend::courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            All Courses
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-4 mb-4" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-6 py-4 rounded-t-lg">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Edit Course: {{ $course->title }}</h1>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
                <span>Created: {{ $course->created_at->format('Y-m-d H:i:s') }} | Updated: {{ $course->updated_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>

        <div class="px-6 py-5 dark:bg-gray-800">
            <form action="{{ route('backend::courses.update', $course) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Course Information Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Course Information</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <!-- Title Input -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $course->title) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            @error('title')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Slug Input -->
                        <div class="mb-4">
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                            <input type="text" id="slug" name="slug" value="{{ old('slug', $course->slug) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            @error('slug')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Content Input -->
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                            <x-markdown-editor 
                                id="content"
                                name="content"
                                :value="old('content', $course->content)"
                                placeholder="Write your course content here..."
                            />
                            @error('content')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Title Translations Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Title Translations</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bengali Title Translation -->
                            <div>
                                <label for="title_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bengali</label>
                                <input type="text" id="title_translation_bn" name="title_translation[bn]" value="{{ old('title_translation.bn', $course->getTranslation('title_translation', 'bn', false)) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @error('title_translation.bn')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <!-- Hindi Title Translation -->
                            <div>
                                <label for="title_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hindi</label>
                                <input type="text" id="title_translation_hi" name="title_translation[hi]" value="{{ old('title_translation.hi', $course->getTranslation('title_translation', 'hi', false)) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @error('title_translation.hi')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Translations Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Content Translations</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bengali Content Translation -->
                            <div>
                                <label for="content_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bengali</label>
                                <x-markdown-editor 
                                    id="content_translation_bn"
                                    name="content_translation[bn]"
                                    :value="old('content_translation.bn', $course->getTranslation('content_translation', 'bn', false))"
                                    placeholder="Write your course content in Bengali here..."
                                />
                                @error('content_translation.bn')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <!-- Hindi Content Translation -->
                            <div>
                                <label for="content_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hindi</label>
                                <x-markdown-editor 
                                    id="content_translation_hi"
                                    name="content_translation[hi]"
                                    :value="old('content_translation.hi', $course->getTranslation('content_translation', 'hi', false))"
                                    placeholder="Write your course content in Hindi here..."
                                />
                                @error('content_translation.hi')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                        Update Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-generate slug when title changes
        $('#title').on('input', function() {
            let title = $(this).val();
            let slug = title.toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
            $('#slug').val(slug);
        });
    });
</script>
@endpush

</x-ui-backend::layout>