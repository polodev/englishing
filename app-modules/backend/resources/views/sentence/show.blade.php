<x-ui-backend::layout>

<div class="container mx-auto px-4 py-6">
    <div class="mb-5">
        <a href="{{ route('backend::sentences.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Sentence List
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-6 py-4 rounded-t-lg">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">{{ $sentence->sentence }}</h1>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
                <span>Created: {{ $sentence->created_at->format('Y-m-d H:i:s') }} | Updated: {{ $sentence->updated_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>

        <div class="px-6 py-5 dark:bg-gray-800">
            <!-- Source Section -->
            @if($sentence->source)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Source</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="text-lg dark:text-gray-200">{{ $sentence->source }}</div>
                </div>
            </div>
            @endif

            <!-- Pronunciation Section (non-English locales) -->
            @if($sentence->getTranslations('pronunciation'))
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Pronunciation</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    @foreach($sentence->getTranslations('pronunciation') as $locale => $pronunciation)
                        <div class="mb-1"><span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($locale) }}:</span> <span class="dark:text-gray-200">{{ $pronunciation }}</span></div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Translations Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Translations</h2>
                @if($sentence->translations->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        @foreach($sentence->translations as $translation)
                            <div class="mb-4">
                                <div class="flex items-start mb-2">
                                    <span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($translation->locale) }}:</span>
                                    <div class="flex-1 dark:text-gray-200">
                                        {{ $translation->translation }}
                                    </div>
                                </div>
                                
                                @if(!empty($translation->transliteration))
                                <div class="ml-10 bg-gray-100 dark:bg-gray-600 p-3 rounded-md">
                                    <div class="italic text-gray-600 dark:text-gray-400 mb-1">Transliteration:</div>
                                    <div class="dark:text-gray-300">{{ $translation->transliteration }}</div>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <div class="text-gray-500 dark:text-gray-400 italic">No translations available</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg ps-4 pb-4">
            <!-- Add edit functionality here when available -->
            <div class="flex justify-between items-center px-2 py-3">
                <div>
                    <button id="delete-sentence" data-id="{{ $sentence->id }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors">
                        Delete Sentence
                    </button>
                </div>
                <div>
                    <!-- Add edit button here when available -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#delete-sentence').on('click', function() {
            if (confirm('Are you sure you want to delete this sentence?')) {
                const sentenceId = $(this).data('id');
                $.ajax({
                    url: '{{ route("backend::sentences.destroy", $sentence->id) }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route("backend::sentences.index") }}';
                        }
                    },
                    error: function(xhr) {
                        console.error('Error deleting sentence:', xhr);
                        alert('There was an error deleting the sentence. Please try again.');
                    }
                });
            }
        });
    });
</script>
@endpush
</x-ui-backend::layout>
