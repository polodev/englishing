<x-ui-backend::layout>

<div class="container mx-auto px-4 py-6">
    <div class="mb-5 flex justify-between">
        <a href="{{ route('backend::courses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Courses List
        </a>
        <a href="{{ route('backend::courses.edit', $course) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Course
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-6 py-4 rounded-t-lg">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">{{ $course->title }}</h1>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
                <span>Created: {{ $course->created_at->format('Y-m-d H:i:s') }} | Updated: {{ $course->updated_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>

        <div class="px-6 py-5 dark:bg-gray-800">
            <!-- Course Info Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Course Information</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-2">
                                <span class="font-bold text-gray-600 dark:text-gray-400">Title:</span>
                                <span class="dark:text-gray-200">{{ $course->title }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-bold text-gray-600 dark:text-gray-400">Slug:</span>
                                <span class="dark:text-gray-200">{{ $course->slug }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-bold text-gray-600 dark:text-gray-400">Author:</span>
                                <span class="dark:text-gray-200">{{ $course->user ? $course->user->name : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Content Section -->
            @if($course->content)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Course Content</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="prose prose-sm max-w-none dark:prose-invert dark:text-gray-200">
                        {!! nl2br(e($course->content)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Title Translations Section -->
            @if($course->getTranslations('title_translation'))
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Title Translations</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    @foreach($course->getTranslations('title_translation') as $locale => $translation)
                        <div class="mb-1"><span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($locale) }}:</span> <span class="dark:text-gray-200">{{ $translation }}</span></div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Content Translations Section -->
            @if($course->getTranslations('content_translation'))
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Content Translations</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    @foreach($course->getTranslations('content_translation') as $locale => $translation)
                        <div class="mb-4">
                            <div class="font-bold text-blue-600 dark:text-blue-400 mb-1">{{ strtoupper($locale) }}:</div>
                            <div class="dark:text-gray-200 pl-4 border-l-4 border-blue-500">
                                {!! $translation !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Articles Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Articles ({{ $articles->count() }})</h2>
                
                @if($articles->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Slug</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Display Order</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Premium</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($articles as $article)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $article->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                        <a href="{{ route('backend::articles.show', $article->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $article->title }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $article->slug }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $article->display_order }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                        @if($article->is_premium)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                Premium
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Free
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $article->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No articles available for this course.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#delete-course').on('click', function() {
            if (confirm('Are you sure you want to delete this course?')) {
                const courseId = $(this).data('id');

                $.ajax({
                    url: '{{ route("backend::courses.destroy", $course->id) }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route("backend::courses.index") }}';
                        } else {
                            alert(response.message || 'Failed to delete course');
                        }
                    },
                    error: function() {
                        alert('An error occurred while deleting the course');
                    }
                });
            }
        });
    });
</script>
@endpush
</x-ui-backend::layout>
