<x-ui-backend::layout>
    <x-slot:title>Edit Article Double Word Set</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Edit Article Double Word Set #{{ $articleDoubleWordSet->id }}</h1>
            <div class="flex space-x-2">
                <a href="{{ route('backend::article-double-word-sets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
                <a href="{{ route('backend::article-double-word-sets.show', $articleDoubleWordSet->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-eye mr-2"></i> View Details
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <form action="{{ route('backend::article-double-word-sets.update', $articleDoubleWordSet) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Form fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Article Selection -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="article_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Article</label>
                        <select id="article_id" name="article_id" class="article-select mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Select an Article</option>
                        </select>
                        @error('article_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-600">*</span></label>
                        <input type="text" id="title" name="title" required value="{{ old('title', $articleDoubleWordSet->title) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <textarea id="content" name="content" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('content', $articleDoubleWordSet->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Order -->
                    <div class="col-span-1">
                        <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                        <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $articleDoubleWordSet->display_order) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        @error('display_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Translations -->
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Translations</h2>
                    <div class="border dark:border-gray-600 rounded-lg p-4">
                        <div class="mb-4">
                            <h3 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Title Translations</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="title_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bangla</label>
                                    <input type="text" id="title_translation_bn" name="title_translation[bn]" value="{{ old('title_translation.bn', $articleDoubleWordSet->getTranslation('title_translation', 'bn', false)) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="title_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hindi</label>
                                    <input type="text" id="title_translation_hi" name="title_translation[hi]" value="{{ old('title_translation.hi', $articleDoubleWordSet->getTranslation('title_translation', 'hi', false)) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Content Translations</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="content_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bangla</label>
                                    <textarea id="content_translation_bn" name="content_translation[bn]" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('content_translation.bn', $articleDoubleWordSet->getTranslation('content_translation', 'bn', false)) }}</textarea>
                                </div>
                                <div>
                                    <label for="content_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hindi</label>
                                    <textarea id="content_translation_hi" name="content_translation[hi]" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('content_translation.hi', $articleDoubleWordSet->getTranslation('content_translation', 'hi', false)) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save mr-2"></i> Update Double Word Set
                    </button>
                </div>
            </form>
        </div>

        <!-- Double Word Lists -->
        <livewire:article-double-word--create-article-double-word-set :articleDoubleWordSet="$articleDoubleWordSet" />
    </div>
    
    @push('styles')
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border-color: rgb(209 213 219);
        }
        .dark .select2-container--default .select2-selection--single {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
            color: white;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white;
        }
        .dark .select2-dropdown {
            background-color: rgb(55 65 81);
            border-color: rgb(75 85 99);
            color: white;
        }
        .dark .select2-search__field {
            background-color: rgb(55 65 81);
            color: white;
        }
        .dark .select2-results__option {
            color: white;
        }
        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgb(75 85 99);
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for Article Selection
            $('.article-select').select2({
                ajax: {
                    url: '{{ route("backend::api.articles.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data, params) {
                        return {
                            results: data.items,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Search for an article...',
                minimumInputLength: 1,
                templateResult: formatArticle,
                templateSelection: formatArticleSelection
            });

            // Function to format articles in dropdown
            function formatArticle(article) {
                if (article.loading) {
                    return article.text;
                }
                return $('<div class="select2-result-article">' +
                    '<div class="select2-result-article__title">' + article.title + '</div>' +
                    '</div>');
            }

            // Function to format the selected article
            function formatArticleSelection(article) {
                return article.title || article.text;
            }

            // Load the current article or any old selected article
            @if($articleDoubleWordSet->article_id || old('article_id'))
            $.ajax({
                url: '{{ route("backend::api.articles.search") }}',
                data: {
                    q: '{{ old('article_id', $articleDoubleWordSet->article_id) }}',
                    exact_id: true
                },
                dataType: 'json'
            }).then(function(data) {
                if (data.items.length > 0) {
                    var article = data.items[0];
                    var option = new Option(article.title, article.id, true, true);
                    $('.article-select').append(option).trigger('change');
                }
            });
            @endif
        });
    </script>
    @endpush
</x-ui-backend::layout>
