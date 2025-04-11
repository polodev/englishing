<x-ui-backend::layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Courses</h1>
            <div>
                <livewire:article--course-create />
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <!-- Courses Table -->
                <div class="overflow-x-auto">
                    <table id="courses-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Articles</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Translations</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
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
                    "dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                           "<'row'<'col-sm-12'tr>>" +
                           "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    "language": {
                        "paginate": {
                            "next": "<span class='text-gray-300'>Next</span>",
                            "previous": "<span class='text-gray-300'>Previous</span>"
                        },
                        "info": "<span class='text-gray-300'>Showing _START_ to _END_ of _TOTAL_ entries</span>",
                        "lengthMenu": "<span class='text-gray-300'>Show _MENU_ entries</span>",
                        "search": "<span class='text-gray-300'>Search:</span>",
                        "emptyTable": "<span class='text-gray-300'>No data available in table</span>",
                        "zeroRecords": "<span class='text-gray-300'>No matching records found</span>"
                    }
                });
            }

            let table = $('#courses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("backend::courses.index_json") }}'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    { data: 'article_count', name: 'article_count', searchable: false },
                    { data: 'user_name', name: 'user_name', searchable: false },
                    { data: 'title_translation_text', name: 'title_translation_text', searchable: false },
                    { data: 'created_at_formatted', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                responsive: true,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                drawCallback: function() {
                    // Apply dark mode styles to the table after each draw
                    if (document.documentElement.classList.contains('dark')) {
                        $('#courses-table_wrapper').addClass('dark:bg-gray-800');
                        $('#courses-table_wrapper .dataTables_length, #courses-table_wrapper .dataTables_filter, #courses-table_wrapper .dataTables_info, #courses-table_wrapper .dataTables_paginate')
                            .addClass('dark:text-gray-300');
                        $('#courses-table_wrapper input, #courses-table_wrapper select')
                            .addClass('dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600');
                        $('#courses-table_wrapper .paginate_button')
                            .addClass('dark:text-gray-300 dark:hover:bg-gray-700');
                        $('#courses-table_wrapper .dataTables_length select')
                            .addClass('dark:bg-gray-700');
                        $('#courses-table tbody tr').addClass('dark:bg-gray-800 dark:text-gray-300');
                        $('#courses-table tbody tr:hover').addClass('dark:bg-gray-700');
                    }
                }
            });

            // Delete course
            $('#courses-table').on('click', '.delete-course', function() {
                let courseId = $(this).data('id');

                if (confirm('Are you sure you want to delete this course?')) {
                    $.ajax({
                        url: `/dashboard/courses/destroy/${courseId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                table.draw();
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

    <style>
        /* Dark mode styles for DataTables */
        .dark .dataTables_wrapper .dataTables_length,
        .dark .dataTables_wrapper .dataTables_filter,
        .dark .dataTables_wrapper .dataTables_info,
        .dark .dataTables_wrapper .dataTables_processing,
        .dark .dataTables_wrapper .dataTables_paginate {
            color: #d1d5db !important; /* text-gray-300 */
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #d1d5db !important; /* text-gray-300 */
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #374151 !important; /* bg-gray-700 */
            color: white !important;
            border-color: #4b5563 !important; /* border-gray-600 */
        }

        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #374151 !important; /* bg-gray-700 */
            color: white !important;
            border-color: #4b5563 !important; /* border-gray-600 */
        }

        .dark .dataTables_wrapper .dataTables_length select,
        .dark .dataTables_wrapper .dataTables_filter input {
            background-color: #374151 !important; /* bg-gray-700 */
            color: #d1d5db !important; /* text-gray-300 */
            border-color: #4b5563 !important; /* border-gray-600 */
        }

        .dark table.dataTable tbody tr {
            background-color: #1f2937 !important; /* bg-gray-800 */
            color: #d1d5db !important; /* text-gray-300 */
        }

        .dark table.dataTable tbody tr:hover {
            background-color: #374151 !important; /* bg-gray-700 */
        }

        .dark table.dataTable.stripe tbody tr.odd,
        .dark table.dataTable.display tbody tr.odd {
            background-color: #1f2937 !important; /* bg-gray-800 */
        }

        .dark table.dataTable.stripe tbody tr.even,
        .dark table.dataTable.display tbody tr.even {
            background-color: #111827 !important; /* bg-gray-900 */
        }

        .dark table.dataTable.hover tbody tr:hover,
        .dark table.dataTable.display tbody tr:hover {
            background-color: #374151 !important; /* bg-gray-700 */
        }

        .dark .dataTables_wrapper .dataTables_processing {
            background-color: rgba(31, 41, 55, 0.7) !important; /* bg-gray-800 with opacity */
            color: #d1d5db !important; /* text-gray-300 */
        }

        .dark .dt-buttons button.dt-button {
            background-color: #374151 !important; /* bg-gray-700 */
            color: #d1d5db !important; /* text-gray-300 */
            border-color: #4b5563 !important; /* border-gray-600 */
        }

        .dark .dt-buttons button.dt-button:hover {
            background-color: #4b5563 !important; /* bg-gray-600 */
        }
    </style>
    @endpush
</x-ui-backend::layout>
