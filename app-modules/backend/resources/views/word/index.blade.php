<x-ui-backend::layout>
<style>
    .meanings-container {
        @apply max-w-md;
    }
    .meaning-text {
        @apply font-bold text-gray-800 mb-1;
    }
    .translations {
        @apply bg-gray-50 p-2 rounded mb-1;
    }
    .translation-item {
        @apply mb-0.5;
    }
    .language-label {
        @apply font-bold text-blue-600 inline-block w-10;
    }
    .transliterations {
        @apply bg-gray-100 p-2 rounded mt-1;
    }
    .transliteration-title {
        @apply italic mb-1 text-gray-500;
    }
    .transliteration-item {
        @apply mb-0.5;
    }
    .transliteration-block {
        @apply ml-2.5 italic text-gray-500;
    }
    .meaning-separator {
        @apply my-2.5 border-t border-dashed border-gray-200;
    }
    .no-data {
        @apply text-gray-500 italic text-sm;
    }
</style>
<div class="container mx-auto px-4 py-6">

    <div class="flex flex-wrap -mx-3">
        <div class="w-full px-3">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800">Words</h3>
                        <livewire:word--word-create/>
                    </div>
                </div>
                <div class="p-6">

                    <!-- Datatable -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="words-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Word</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phonetic</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meanings & Translations</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Standalone Translations</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Synonyms</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Antonyms</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pronunciation</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated At</th>
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
        let wordsTable = $('#words-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("backend::words.index_json") }}',
                type: 'POST',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                }
            },
            // Custom classes for DataTables elements
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4"<"flex-1"f><"flex-shrink-0"l>>rt<"flex flex-col md:flex-row justify-between items-center"<"flex-1"i><"flex-shrink-0"p>>',
            language: {
                paginate: {
                    previous: '<span class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100">Previous</span>',
                    next: '<span class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100">Next</span>'
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'word', name: 'word' },
                { data: 'phonetic', name: 'phonetic', searchable: false },
                { data: 'meanings_with_translations', name: 'meanings_with_translations', searchable: false },
                { data: 'standalone_translations', name: 'standalone_translations', searchable: false },
                { data: 'synonyms', name: 'synonyms', searchable: false },
                { data: 'antonyms', name: 'antonyms', searchable: false },
                { data: 'pronunciation_text', name: 'pronunciation_text', searchable: false },
                { data: 'created_at_formatted', name: 'created_at' },
                { data: 'updated_at_formatted', name: 'updated_at' }
            ],
            order: [[0, 'desc']]
        });

    });
</script>
@endpush

</x-ui-backend::layout>
