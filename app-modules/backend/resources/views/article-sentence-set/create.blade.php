<x-ui-backend::layout>
    <x-slot:title>Create Article Sentence Set</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <!-- Header with actions -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Create Article Sentence Set</h1>
            <a href="{{ route('backend::article-sentence-sets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <form action="{{ route('backend::article-sentence-sets.store') }}" method="POST" class="p-6">
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

                <!-- Column Order Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Column Order</h2>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-4">
                        <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded-md border border-blue-200 dark:border-blue-800 mb-4">
                            <h3 class="font-medium text-blue-800 dark:text-blue-200 mb-1">Note</h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300">You only need to select and order a few columns (typically 2-3) that will be displayed in the frontend. Most columns can remain in the Available section.</p>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">Drag columns between buckets to set display order and visibility. <strong>Only columns in the "Active Columns" bucket will be displayed in the frontend.</strong></p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Available Columns Bucket -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Available Columns</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Drag columns from here to add them</p>

                                <div id="available-columns" class="min-h-[450px] space-y-2 bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-600">
                                    @php
                                        $allColumns = \Modules\ArticleSentence\Models\ArticleSentenceSet::getColumnsForColumnOrder();
                                        $savedOrder = old('column_order') ? json_decode(old('column_order'), true) : [];

                                        // If we have saved order, use it, otherwise use default columns
                                        $activeColumns = !empty($savedOrder) ? $savedOrder : ['sentence'];

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

                        <input type="hidden" id="column_order" name="column_order" value="{{ old('column_order', json_encode($activeColumns)) }}">
                    </div>
                </div>

                <!-- Title and Content Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Title and Content</h2>

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content</label>
                        <x-markdown-editor-ace-editor
                            id="content"
                            name="content"
                            :value="old('content')"
                            placeholder="Write your content here..."
                        />
                        @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Translations Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Translations</h2>

                    <!-- Title Translations -->
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Title Translations</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bengali Title Translation -->
                            <div>
                                <label for="title_translation_bn" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded text-xs font-bold uppercase mr-2">BN</span>
                                    Bengali Title
                                </label>
                                <input type="text" id="title_translation_bn" name="title_translation[bn]" value="{{ old('title_translation.bn') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @error('title_translation.bn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Hindi Title Translation -->
                            <div>
                                <label for="title_translation_hi" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    <span class="inline-block bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-2 py-1 rounded text-xs font-bold uppercase mr-2">HI</span>
                                    Hindi Title
                                </label>
                                <input type="text" id="title_translation_hi" name="title_translation[hi]" value="{{ old('title_translation.hi') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @error('title_translation.hi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Content Translations -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Content Translations</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Bengali Content Translation -->
                            <div>
                                <label for="content_translation_bn" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded text-xs font-bold uppercase mr-2">BN</span>
                                    Bengali Content
                                </label>
                                <x-markdown-editor-ace-editor
                                    id="content_translation_bn"
                                    name="content_translation[bn]"
                                    :value="old('content_translation.bn')"
                                    placeholder="Write your Bengali content here..."
                                />
                                @error('content_translation.bn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- Hindi Content Translation -->
                            <div>
                                <label for="content_translation_hi" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    <span class="inline-block bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-2 py-1 rounded text-xs font-bold uppercase mr-2">HI</span>
                                    Hindi Content
                                </label>
                                <x-markdown-editor-ace-editor
                                    id="content_translation_hi"
                                    name="content_translation[hi]"
                                    :value="old('content_translation.hi')"
                                    placeholder="Write your Hindi content here..."
                                />
                                @error('content_translation.hi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('backend::article-sentence-sets.index') }}" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-white rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Save</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            // Initialize Select2 for article selection
            document.addEventListener('DOMContentLoaded', function() {
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
                        }
                    },
                    templateResult: function(article) {
                        if (article.loading) {
                            return article.text;
                        }

                        return $('<div class="py-1"><strong>#' + article.id + '</strong> - ' + article.text + '</div>');
                    },
                    templateSelection: function(article) {
                        return article.text || 'Search for an article...';
                    }
                });

                // Initialize Sortable for column ordering
                var availableColumns = document.getElementById('available-columns');
                var activeColumns = document.getElementById('active-columns');
                var columnOrderInput = document.getElementById('column_order');

                // Function to update column numbers and order input
                function updateColumnNumbers() {
                    // Update column numbers
                    $('#active-columns .column-item').each(function(index) {
                        $(this).find('.column-number').text(index + 1);
                    });

                    // Update column order hidden input
                    var order = [];
                    $('#active-columns .column-item').each(function() {
                        order.push($(this).data('column'));
                    });
                    columnOrderInput.value = JSON.stringify(order);
                }

                // Initialize Sortable for Available Columns
                new Sortable(availableColumns, {
                    group: {
                        name: 'columns',
                        pull: 'clone',
                        put: true,
                    },
                    animation: 150,
                    sort: false, // No sorting within available columns
                    ghostClass: 'sortable-ghost-available',
                    onEnd: function(evt) {
                        updateColumnNumbers();
                    }
                });

                // Initialize Sortable for Active Columns
                new Sortable(activeColumns, {
                    group: 'columns',
                    animation: 150,
                    ghostClass: 'sortable-ghost-active',
                    onEnd: function(evt) {
                        updateColumnNumbers();
                    }
                });

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
