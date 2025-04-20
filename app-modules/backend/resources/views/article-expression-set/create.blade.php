<x-ui-backend::layout>
    <x-slot:title>Create Article Expression Set</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <!-- Header with actions -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Create Article Expression Set</h1>
            <a href="{{ route('backend::article-expression-sets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <form action="{{ route('backend::article-expression-sets.store') }}" method="POST" class="p-6">
                @csrf

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                        <p class="font-bold">Validation Error</p>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Basic Info Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Basic Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Article Selection with Search -->
                        <div>
                            <label for="article_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Related Article (Optional)</label>
                            <select id="article_id" name="article_id" class="article-select mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @if(old('article_id'))
                                    <option value="{{ old('article_id') }}" selected>Loading...</option>
                                @endif
                            </select>
                            @error('article_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Display Order -->
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                            <input type="number" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @error('display_order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Livewire Component for Creating Article Expression Set -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Expression Set Details</h2>
                    <livewire:article-expression--create-article-expression-set />
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save mr-2"></i> Save Expression Set
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for article selection with AJAX search
            $('.article-select').select2({
                placeholder: 'Search for an article...',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("backend::api.articles.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.items.map(function(article) {
                                return {
                                    id: article.id,
                                    text: article.title
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                templateResult: formatArticle,
                templateSelection: formatArticleSelection
            });

            // Format article in dropdown
            function formatArticle(article) {
                if (article.loading) {
                    return article.text;
                }

                return $('<div class="py-1"><strong>#' + article.id + '</strong> - ' + article.text + '</div>');
            }

            // Format selected article
            function formatArticleSelection(article) {
                return article.text || 'Search for an article...';
            }

            // If there's a selected article on page load, fetch its details
            @if(old('article_id'))
            $.ajax({
                url: '{{ route("backend::api.articles.search") }}',
                data: {
                    q: '{{ old('article_id') }}',
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
