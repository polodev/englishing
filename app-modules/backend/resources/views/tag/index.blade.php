<x-ui-backend::layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Tags</h1>
            <div>
                <a href="{{ route('backend::tags.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Tag
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Filter Form -->
                <div class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Filters</h2>
                    <form id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="created_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created From</label>
                            <input type="date" id="created_from" name="created_from" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="created_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Created To</label>
                            <input type="date" id="created_to" name="created_to" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <!-- Empty div for grid alignment -->
                        </div>
                        <div class="md:col-span-3 flex justify-end">
                            <button type="button" id="reset-filter" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-white uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-25 transition mr-2">
                                Reset
                            </button>
                            <button type="button" id="apply-filter" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                                Apply Filters
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tags Table -->
                <div class="overflow-x-auto">
                    <table id="tags-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Translations</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Articles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            <!-- Table rows will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Apply dark mode styles to DataTables
            if (document.documentElement.classList.contains('dark')) {
                $.extend(true, $.fn.dataTable.defaults, {
                    "dom": '<"dark:text-gray-200"lif>rt<"dark:text-gray-200"p>',
                    "language": {
                        "info": "Showing _START_ to _END_ of _TOTAL_ tags",
                        "lengthMenu": "Show _MENU_ tags",
                        "search": "Search:",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        },
                        "emptyTable": "<span class='text-gray-300'>No data available in table</span>"
                    }
                });
            }

            // Initialize DataTable
            var table = $('#tags-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('backend::tags.index_json') }}",
                    data: function (d) {
                        d.created_from = $('#created_from').val();
                        d.created_to = $('#created_to').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'slug', name: 'slug'},
                    {data: 'title_translation_text', name: 'title_translation', orderable: false},
                    {data: 'article_count', name: 'article_count', searchable: false},
                    {data: 'created_at_formatted', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']]
            });

            // Apply filters
            $('#apply-filter').click(function() {
                table.draw();
            });

            // Reset filters
            $('#reset-filter').click(function() {
                $('#filter-form')[0].reset();
                table.draw();
            });

            // Delete tag functionality
            $(document).on('click', '.delete-tag', function() {
                var tagId = $(this).data('id');
                var tagTitle = $(this).data('title');
                
                if (confirm('Are you sure you want to delete the tag "' + tagTitle + '"?')) {
                    $.ajax({
                        url: "{{ url('dashboard/tags/destroy') }}" + '/' + tagId,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(result) {
                            table.draw(false);
                            alert('Tag deleted successfully');
                        },
                        error: function(xhr) {
                            alert('Error deleting tag. Please try again.');
                        }
                    });
                }
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        /* Dark mode styles for DataTables */
        .dark .dataTables_wrapper .dataTables_length,
        .dark .dataTables_wrapper .dataTables_filter,
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_processing,
        .dark .dataTables_wrapper .dataTables_paginate {
            color: #e5e7eb;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #e5e7eb !important;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            color: #111 !important;
            background: #e5e7eb;
            border-color: #4b5563;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            color: #111 !important;
            background: #9ca3af;
            border-color: #4b5563;
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
            color: #6b7280 !important;
        }

        .dark .dataTables_wrapper .dataTables_length select {
            background-color: #374151;
            color: #e5e7eb;
            border-color: #4b5563;
        }

        .dark .dataTables_wrapper .dataTables_filter input {
            background-color: #374151;
            color: #e5e7eb;
            border-color: #4b5563;
        }

        /* Fix for DataTable background in dark mode */
        .dark table.dataTable tbody tr {
            background-color: #1f2937 !important;
        }
        
        .dark table.dataTable tbody tr:hover {
            background-color: #374151 !important;
        }

        /* Language label styling */
        .language-label {
            display: inline-block;
            background-color: #e5e7eb;
            color: #111827;
            padding: 0.1rem 0.3rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.25rem;
        }

        .dark .language-label {
            background-color: #4b5563;
            color: #e5e7eb;
        }
    </style>
    @endpush
</x-ui-backend::layout>
