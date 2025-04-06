<x-ui-backend::layout>
<style>
    .meanings-container {
        max-width: 400px;
    }
    .meaning-text {
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }
    .translations {
        background-color: #f8f9fa;
        padding: 8px;
        border-radius: 4px;
        margin-bottom: 5px;
    }
    .translation-item {
        margin-bottom: 3px;
    }
    .language-label {
        font-weight: bold;
        color: #007bff;
        display: inline-block;
        width: 40px;
    }
    .transliteration-block {
        margin-left: 10px;
        font-style: italic;
        color: #6c757d;
    }
    .meaning-separator {
        margin: 10px 0;
        border-top: 1px dashed #dee2e6;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Words</h3>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="created-from">Created From</label>
                                <input type="text" class="form-control flatpickr-date" id="created-from" placeholder="From date">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="created-to">Created To</label>
                                <input type="text" class="form-control flatpickr-date" id="created-to" placeholder="To date">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group">
                                <button id="filter-button" class="btn btn-primary mr-2">Apply</button>
                                <button id="reset-button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>


                    <!-- Datatable -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="words-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Word</th>
                                    <th>Meanings</th>
                                    <th>Synonyms</th>
                                    <th>Antonyms</th>
                                    <th>Pronunciation</th>
                                    <th>Translations</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
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
        // Initialize Flatpickr for date inputs
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });

        // Initialize DataTable
        let wordsTable = $('#words-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("backend::words.index_json") }}',
                type: 'POST',
                data: function(d) {
                    d.created_from = $('#created-from').val();
                    d.created_to = $('#created-to').val();
                    d._token = '{{ csrf_token() }}';
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'word', name: 'word', searchable: true },
                { data: 'meanings_list', name: 'meanings_list', searchable: false },
                { data: 'synonyms', name: 'synonyms', searchable: false },
                { data: 'antonyms', name: 'antonyms', searchable: false },
                { data: 'pronunciation_text', name: 'pronunciation_text', searchable: false },
                { data: 'translations', name: 'translations', searchable: false },
                { data: 'created_at_formatted', name: 'created_at', searchable: false },
                { data: 'updated_at_formatted', name: 'updated_at', searchable: false },
            ],
            order: [[0, 'desc']]
        });

        // Apply filters
        $('#filter-button').on('click', function() {
            wordsTable.draw();
        });

        // Reset filters
        $('#reset-button').on('click', function() {
            $('#created-from').val('');
            $('#created-to').val('');
            wordsTable.draw();
        });
    });
</script>
@endpush

</x-ui-backend::layout>
