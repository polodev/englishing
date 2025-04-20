<x-ui-backend::layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('backend::tags.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tag: {{ $tag->title }}</h1>
            <div class="ml-auto flex space-x-2">
                <a href="{{ route('backend::tags.edit', $tag) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <button type="button" class="delete-tag inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors" 
                       data-id="{{ $tag->id }}" data-title="{{ $tag->title }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Tag Details -->
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Tag Details</h2>
                    </div>
                    <div class="p-6">
                        <dl>
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tag->id }}</dd>
                            </div>
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Title (English)</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tag->title }}</dd>
                            </div>
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tag->slug }}</dd>
                            </div>
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Articles</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tag->articles->count() }}</dd>
                            </div>
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tag->created_at->format('Y-m-d H:i:s') }}</dd>
                            </div>
                            <div class="mb-4">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $tag->updated_at->format('Y-m-d H:i:s') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Tag Translations -->
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Translations</h2>
                    </div>
                    <div class="p-6">
                        @php
                            $locales = ['bn', 'hi'];
                        @endphp
                        
                        <dl>
                            @foreach($locales as $locale)
                                @php
                                    $translation = $tag->getTranslation('title_translation', $locale, false);
                                @endphp
                                
                                @if($translation)
                                    <div class="mb-4">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ strtoupper($locale) }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $translation }}</dd>
                                    </div>
                                @endif
                            @endforeach
                        </dl>
                        
                        @if(count($locales) == 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400">No translations available.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Articles -->
            <div class="md:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Related Articles</h2>
                    </div>
                    <div class="p-6">
                        @if($tag->articles->isNotEmpty())
                            <ul class="space-y-2">
                                @foreach($tag->articles as $article)
                                    <li>
                                        <a href="{{ route('backend::articles.show', $article) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            {{ $article->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No related articles found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete tag functionality
            document.querySelector('.delete-tag').addEventListener('click', function() {
                var tagId = this.getAttribute('data-id');
                var tagTitle = this.getAttribute('data-title');
                
                if (confirm('Are you sure you want to delete the tag "' + tagTitle + '"?')) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ url("dashboard/tags/destroy") }}/' + tagId;
                    form.style.display = 'none';
                    
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    var methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    
                    form.submit();
                }
            });
        });
    </script>
    @endpush
</x-ui-backend::layout>
