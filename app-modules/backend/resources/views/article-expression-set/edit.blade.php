<x-ui-backend::layout>
    <x-slot:title>Edit Article Expression Set</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <!-- Header with actions -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Edit Article Expression Set</h1>
            <div class="flex space-x-2">
                <a href="{{ route('backend::article-expression-sets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
                <a href="{{ route('backend::article-expression-sets.show', $articleExpressionSet) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-eye mr-2"></i> View Details
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <form action="{{ route('backend::article-expression-sets.update', $articleExpressionSet) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

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
                                @if($articleExpressionSet->article)
                                    <option value="{{ $articleExpressionSet->article->id }}" selected>{{ $articleExpressionSet->article->title }}</option>
                                @endif
                            </select>
                            @error('article_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Display Order -->
                        <div>
                            <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
                            <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $articleExpressionSet->display_order) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            @error('display_order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>


                <div class="mb-6">
                    <!-- Column Order and Visibility -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Drag columns between buckets to set display order and visibility. Only columns in the "Active Columns" bucket will be displayed in the frontend.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Available Columns Bucket -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Available Columns</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Drag columns from here to add them</p>

                                <div id="available-columns" class="min-h-[450px] space-y-2 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                    @php
                                        $allColumns = \Modules\ArticleExpression\Models\ArticleExpressionSet::getColumnsForColumnOrder();
                                        $savedOrder = old('column_order', $articleExpressionSet->column_order ?? null);

                                        if ($savedOrder) {
                                            // If it's a string (from old input or database), decode it
                                            if (is_string($savedOrder)) {
                                                $savedOrder = json_decode($savedOrder, true);
                                            }
                                        } else {
                                            $savedOrder = [];
                                        }

                                        // Active columns are those in the saved order
                                        $activeColumns = !empty($savedOrder) ? $savedOrder : [];

                                        // Available columns are those not in active columns
                                        $availableColumns = array_diff($allColumns, $activeColumns);
                                    @endphp

                                    @foreach($availableColumns as $column)
                                        <div class="column-item flex items-center bg-gray-50 dark:bg-gray-700 p-3 rounded border border-gray-200 dark:border-gray-600 cursor-move" data-column="{{ $column }}">
                                            <div class="mr-3 text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-gray-700 dark:text-gray-300">{{ ucwords(str_replace('_', ' ', $column)) }}</span>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if(count($availableColumns) === 0)
                                        <div class="text-center py-4 text-gray-500 dark:text-gray-400 italic">All columns are active</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Active Columns Bucket -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Active Columns</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Drag to reorder or remove</p>

                                <div id="active-columns" class="min-h-[450px] space-y-2 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                    @foreach($activeColumns as $index => $column)
                                        <div class="column-item flex items-center bg-blue-50 dark:bg-blue-900 p-3 rounded border border-blue-200 dark:border-blue-700 cursor-move" data-column="{{ $column }}">
                                            <div class="mr-3 text-gray-400 dark:text-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-gray-700 dark:text-gray-300">{{ ucwords(str_replace('_', ' ', $column)) }}</span>
                                            </div>
                                            <div class="column-number bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-200 rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                                {{ $index + 1 }}
                                            </div>
                                        </div>
                                    @endforeach

                                    @if(count($activeColumns) === 0)
                                        <div class="text-center py-4 text-gray-500 dark:text-gray-400 italic">Drag columns here to activate</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <input type="hidden" id="column_order" name="column_order" value="{{ old('column_order', is_array($articleExpressionSet->column_order) ? json_encode($articleExpressionSet->column_order) : '[]') }}">
                    </div>


                </div>

                <!-- Title and Content Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Expression Set Details</h2>

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (English)</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $articleExpressionSet->title) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content (English)</label>
                        <x-markdown-editor-ace-editor
                            id="content"
                            name="content"
                            :value="old('content', $articleExpressionSet->content)"
                            placeholder="Write your content here..."
                        />
                        @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Translations -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Translations</h3>

                        <!-- Bengali Translations -->
                        <div class="mb-4">
                            <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Bengali Translations</h4>

                            <div class="mb-3">
                                <label for="title_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (Bengali)</label>
                                <input type="text" id="title_translation_bn" name="title_translation[bn]" value="{{ old('title_translation.bn', $articleExpressionSet->getTranslation('title_translation', 'bn', false)) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="content_translation_bn" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content (Bengali)</label>
                                <x-markdown-editor-ace-editor
                                    id="content_translation_bn"
                                    name="content_translation[bn]"
                                    :value="old('content_translation.bn', $articleExpressionSet->getTranslation('content_translation', 'bn', false))"
                                    placeholder="Write your Bengali content here..."
                                />
                            </div>
                        </div>

                        <!-- Hindi Translations -->
                        <div>
                            <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Hindi Translations</h4>

                            <div class="mb-3">
                                <label for="title_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (Hindi)</label>
                                <input type="text" id="title_translation_hi" name="title_translation[hi]" value="{{ old('title_translation.hi', $articleExpressionSet->getTranslation('title_translation', 'hi', false)) }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="content_translation_hi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content (Hindi)</label>
                                <x-markdown-editor-ace-editor
                                    id="content_translation_hi"
                                    name="content_translation[hi]"
                                    :value="old('content_translation.hi', $articleExpressionSet->getTranslation('content_translation', 'hi', false))"
                                    placeholder="Write your Hindi content here..."
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save mr-2"></i> Update Expression Set
                    </button>
                </div>
            </form>
        </div>

        <!-- Expression List Editor -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mt-6 p-6">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Expression List</h2>

            <div class="mb-4">
                <p class="text-gray-600 dark:text-gray-400">Edit the expressions associated with this set using the JSON editor below:</p>
            </div>

            <livewire:article-expression--article-expression-set-list-edit-using-json :articleExpressionSet="$articleExpressionSet" />
        </div>
    </div>

    @push('styles')
    <style>
        /* Dark mode styles for Select2 */
        .dark .select2-container--default .select2-selection--single {
            background-color: #374151 !important; /* bg-gray-700 */
            border-color: #4B5563 !important; /* border-gray-600 */
            color: #E5E7EB !important; /* text-gray-200 */
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #E5E7EB !important; /* text-gray-200 */
        }

        .dark .select2-dropdown {
            background-color: #374151 !important; /* bg-gray-700 */
            border-color: #4B5563 !important; /* border-gray-600 */
        }

        .dark .select2-container--default .select2-results__option {
            color: #E5E7EB !important; /* text-gray-200 */
        }

        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2563EB !important; /* bg-blue-600 */
        }

        .dark .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #1D4ED8 !important; /* bg-blue-700 */
        }

        .dark .select2-search--dropdown .select2-search__field {
            background-color: #1F2937 !important; /* bg-gray-800 */
            border-color: #4B5563 !important; /* border-gray-600 */
            color: #E5E7EB !important; /* text-gray-200 */
        }
    </style>
    @endpush

@push('styles')
    <style>
        /* Dark mode styles for Select2 */
        .dark .select2-container--default .select2-selection--single {
            background-color: #374151 !important; /* bg-gray-700 */
            border-color: #4B5563 !important; /* border-gray-600 */
            color: #E5E7EB !important; /* text-gray-200 */
        }

        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #E5E7EB !important; /* text-gray-200 */
        }

        .dark .select2-dropdown {
            background-color: #374151 !important; /* bg-gray-700 */
            border-color: #4B5563 !important; /* border-gray-600 */
        }

        .dark .select2-container--default .select2-results__option {
            color: #E5E7EB !important; /* text-gray-200 */
        }

        .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2563EB !important; /* bg-blue-600 */
        }

        .dark .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #1D4ED8 !important; /* bg-blue-700 */
        }

        .dark .select2-search--dropdown .select2-search__field {
            background-color: #1F2937 !important; /* bg-gray-800 */
            border-color: #4B5563 !important; /* border-gray-600 */
            color: #E5E7EB !important; /* text-gray-200 */
        }

        /* Sortable styles */
        .sortable-ghost-available {
            background-color: rgba(243, 244, 246, 0.7) !important;
            border-color: #E5E7EB !important;
            opacity: 0.8;
        }

        .dark .sortable-ghost-available {
            background-color: rgba(55, 65, 81, 0.7) !important;
            border-color: #4B5563 !important;
        }

        .sortable-ghost-active {
            background-color: rgba(219, 234, 254, 0.7) !important;
            border-color: #BFDBFE !important;
            opacity: 0.8;
        }

        .dark .sortable-ghost-active {
            background-color: rgba(30, 58, 138, 0.7) !important;
            border-color: #1E40AF !important;
        }
    </style>
    @endpush

@push('scripts')
    <!-- Sortable.js for drag-and-drop functionality -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize column order drag and drop functionality for both buckets
            const availableColumns = document.getElementById('available-columns');
            const activeColumns = document.getElementById('active-columns');

            if (availableColumns && activeColumns) {
                // Initialize Sortable for available columns
                new Sortable(availableColumns, {
                    group: 'columns',
                    animation: 150,
                    ghostClass: 'sortable-ghost-available',
                    onEnd: updateColumnOrder
                });

                // Initialize Sortable for active columns
                new Sortable(activeColumns, {
                    group: 'columns',
                    animation: 150,
                    ghostClass: 'sortable-ghost-active',
                    onEnd: updateColumnOrder
                });

                // Update column numbers and hidden input when order changes
                function updateColumnOrder() {
                    // Get all active columns
                    const activeItems = activeColumns.querySelectorAll('.column-item');
                    const columns = [];

                    // Update numbers and collect column names
                    activeItems.forEach((item, index) => {
                        // Update the number badge
                        const numberBadge = item.querySelector('.column-number');
                        if (numberBadge) {
                            numberBadge.textContent = index + 1;
                        }

                        // Add column to the ordered list
                        columns.push(item.dataset.column);
                    });

                    // Update the hidden input with the new order
                    document.getElementById('column_order').value = JSON.stringify(columns);
                }
            }

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
        });
    </script>
    @endpush
</x-ui-backend::layout>
