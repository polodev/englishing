<x-ui-backend::layout>
    <x-slot:title>Article Conversation Sets</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Article Conversation Sets</h1>
            <a href="{{ route('backend::article-conversation-sets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i> Create New
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Filters</h2>
            <form id="filters-form" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="article_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Article</label>
                    <select id="article_id" name="article_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">All Articles</option>
                        @foreach(\Modules\Article\Models\Article::orderBy('title')->get() as $article)
                            <option value="{{ $article->id }}">{{ $article->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="created_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created From</label>
                    <input type="date" id="created_from" name="created_from" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="created_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created To</label>
                    <input type="date" id="created_to" name="created_to" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button type="button" id="reset-filters" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                        Reset
                    </button>
                    <button type="button" id="apply-filters" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- DataTable -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-4">
                <table id="article-conversation-sets-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Article</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title Translations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Content Translations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"></tbody>
                </table>
            </div>
        </div>
    </div>
    @push('styles')
    <style>
        /* Dark mode styles for DataTables */
        .dark .dataTables_wrapper .dataTables_length,
        .dark .dataTables_wrapper .dataTables_filter,
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_processing,
        .dark .dataTables_wrapper .dataTables_paginate {
            color: #e2e8f0 !important; /* text-gray-200 */
        }
        
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #e2e8f0 !important; /* text-gray-200 */
        }
        
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #4b5563 !important; /* bg-gray-600 */
            color: #ffffff !important;
            border-color: #4b5563 !important;
        }
        
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #374151 !important; /* bg-gray-700 */
            color: #ffffff !important;
            border-color: #4b5563 !important;
        }
        
        .dark .dataTables_wrapper .dataTables_length select,
        .dark .dataTables_wrapper .dataTables_filter input {
            background-color: #374151 !important; /* bg-gray-700 */
            color: #e2e8f0 !important; /* text-gray-200 */
            border-color: #4b5563 !important; /* border-gray-600 */
        }
    </style>
    @endpush
    
    @push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#article-conversation-sets-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("backend::article-conversation-sets.index_json") }}',
                    data: function (d) {
                        d.article_id = $('#article_id').val();
                        d.created_from = $('#created_from').val();
                        d.created_to = $('#created_to').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'article_title', name: 'article.title'},
                    {data: 'title_translation_text', name: 'title_translation'},
                    {data: 'content_translation_text', name: 'content_translation'},
                    {data: 'created_at_formatted', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true,
                dom: '<"flex justify-between items-center mb-4"<"flex-1"l><"flex"f>>t<"flex justify-between items-center mt-4"<"flex-1"i><"flex"p>>',
                drawCallback: function() {
                    // Apply dark mode styling to table rows after each draw
                    if (document.documentElement.classList.contains('dark')) {
                        $(this).find('tbody tr').addClass('dark:bg-gray-800 dark:text-gray-200');
                        $(this).find('tbody td').addClass('dark:text-gray-200');
                    }
                }
            });

            // Apply filters
            $('#apply-filters').click(function() {
                table.draw();
            });

            // Reset filters
            $('#reset-filters').click(function() {
                $('#filters-form')[0].reset();
                table.draw();
            });
        });
    </script>
    @endpush
</x-ui-backend::layout>
