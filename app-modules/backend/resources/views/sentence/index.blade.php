<x-ui-backend::layout>
<style>
    .translations-container {
        @apply max-w-md;
    }
    .translation-item {
        @apply mb-0.5;
    }
    .language-label {
        @apply font-bold text-blue-600 dark:text-blue-400 inline-block w-10;
    }
    .transliteration-block {
        @apply ml-2.5 italic text-gray-500 dark:text-gray-400;
    }
    .pronunciations-container {
        @apply max-w-md;
    }
    .pronunciation-item {
        @apply mb-0.5;
    }
    .no-data {
        @apply text-gray-500 dark:text-gray-400 italic text-sm;
    }
</style>
<div class="container mx-auto px-4 py-6">

    <div class="flex flex-wrap -mx-3">
        <div class="w-full px-3">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Sentences</h3>
                        <div class="flex space-x-2">
                            <!-- Add sentence creation buttons here when available -->
                        </div>
                    </div>
                </div>
                <div class="p-6 dark:bg-gray-800">
                    <!-- Datatable -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="sentences-table">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sentence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Translations</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pronunciation</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Source</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Updated At</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with Tailwind styling
        let sentencesTable = $('#sentences-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("backend::sentences.index_json") }}',
                type: 'POST',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                }
            },
            // Custom classes for DataTables elements
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex-1"f><"flex-shrink-0"l>>rt<"flex flex-col md:flex-row justify-between items-center"<"flex-1"i><"flex-shrink-0"p>>',
            language: {
                paginate: {
                    previous: '<span class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">Previous</span>',
                    next: '<span class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">Next</span>'
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'sentence', name: 'sentence' },
                { data: 'translations_html', name: 'translations_html', searchable: false },
                { data: 'pronunciation_text', name: 'pronunciation_text', searchable: false },
                { data: 'source', name: 'source', searchable: true },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'updated_at_formatted', name: 'updated_at' }
            ],
            order: [[0, 'desc']],
            // Add dark mode support for DataTable cells
            initComplete: function() {
                // Apply dark mode classes to DataTable elements
                $('#sentences-table_wrapper').addClass('dark:text-gray-200');

                // Add MutationObserver to handle dynamically added rows
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.addedNodes.length) {
                            // Add dark mode classes to table body and rows
                            $('#sentences-table tbody').addClass('dark:bg-gray-800 dark:text-gray-200');
                            $('#sentences-table tbody tr').addClass('dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700');
                            $('#sentences-table tbody tr:hover').addClass('dark:bg-gray-700');
                            $('#sentences-table tbody td').addClass('dark:border-gray-700');

                            // Add dark mode classes to pagination and info elements
                            $('.dataTables_info').addClass('dark:text-gray-300');
                            $('.dataTables_length select').addClass('dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600');
                            $('.dataTables_filter input').addClass('dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600');
                        }
                    });
                });

                // Observe the table for changes
                observer.observe(document.querySelector('#sentences-table'), {
                    childList: true,
                    subtree: true
                });
            }
        });
    });
</script>
@endpush

</x-ui-backend::layout>
