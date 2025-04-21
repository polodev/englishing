<x-ui-backend::layout>
    <x-slot:title>Article Sentence Sets</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Article Sentence Sets</h1>
            <a href="{{ route('backend::article-sentence-sets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
                <table id="article-sentence-sets-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title Translations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Content Translations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Article</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Updated At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    <style>
        /* Dark mode styles for DataTables */
        .dark .dataTables_wrapper .dataTables_length,
        .dark .dataTables_wrapper .dataTables_filter,
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #a0aec0;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #4299e1;
            color: #fff;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background-color: #2d3748;
            color: #a0aec0;
        }

        .dark .dataTables_wrapper .dataTables_filter input {
            background-color: #2d3748;
            color: #a0aec0;
            border-color: #4a5568;
        }

        .language-label {
            font-weight: bold;
            margin-right: 5px;
        }
    </style>
    @endpush

    @push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#article-sentence-sets-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('backend::article-sentence-sets.index_json') }}',
                    data: function(d) {
                        d.article_id = $('#article_id').val();
                        d.created_from = $('#created_from').val();
                        d.created_to = $('#created_to').val();
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'title_translation_text', name: 'title_translation' },
                    { data: 'content_translation_text', name: 'content_translation' },
                    { data: 'article_title', name: 'article.title' },
                    { data: 'created_at_formatted', name: 'created_at' },
                    { data: 'updated_at_formatted', name: 'updated_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Processing...'
                }
            });

            // Filter form handling
            $('#apply-filters').click(function() {
                table.draw();
            });

            $('#reset-filters').click(function() {
                $('#filters-form')[0].reset();
                table.draw();
            });

            // Delete confirmation
            $(document).on('click', '.delete-sentence-set', function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                
                if (confirm('Are you sure you want to delete the article sentence set: "' + title + '"?')) {
                    $.ajax({
                        url: '{{ route('backend::article-sentence-sets.destroy', ':id') }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                table.row($(this).parents('tr')).remove().draw();
                                toastr.success('Article sentence set deleted successfully');
                            }
                        },
                        error: function() {
                            toastr.error('Error deleting article sentence set');
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-ui-backend::layout>
