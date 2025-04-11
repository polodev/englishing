<x-ui-backend::layout>

<div class="container mx-auto px-4 py-6">
    <div class="mb-5">
        <a href="{{ route('backend::expressions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Expressions List
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-6 py-4 rounded-t-lg">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">{{ $expression->expression }}</h1>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
                <span>Created: {{ $expression->created_at->format('Y-m-d H:i:s') }} | Updated: {{ $expression->updated_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>

        <div class="px-6 py-5 dark:bg-gray-800">
            <!-- Expression Info Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Expression Information</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-2">
                                <span class="font-bold text-gray-600 dark:text-gray-400">Expression:</span>
                                <span class="dark:text-gray-200">{{ $expression->expression }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-bold text-gray-600 dark:text-gray-400">Slug:</span>
                                <span class="dark:text-gray-200">{{ $expression->slug }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="font-bold text-gray-600 dark:text-gray-400">Type:</span>
                                <span class="dark:text-gray-200">{{ $expression->type ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pronunciation Section (non-English locales) -->
            @php
                $pronunciations = [];
                try {
                    $pronunciations = $expression->getTranslations('pronunciation');
                } catch (\Exception $e) {
                    // Handle the case when pronunciation is not set
                }
            @endphp
            
            @if(!empty($pronunciations))
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Pronunciation</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    @foreach($pronunciations as $locale => $pronunciation)
                        <div class="mb-1"><span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($locale) }}:</span> <span class="dark:text-gray-200">{{ $pronunciation }}</span></div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Meanings with Translations Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Meanings with Translations</h2>
                @forelse($expression->meanings as $index => $meaning)
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-4">
                        <div class="mb-2">
                            <span class="font-bold text-gray-600 dark:text-gray-400">Meaning:</span>
                            <span class="dark:text-gray-200">{{ $meaning->meaning }}</span>
                        </div>
                        @if($meaning->slug)
                        <div class="mb-2">
                            <span class="font-bold text-gray-600 dark:text-gray-400">Slug:</span>
                            <span class="dark:text-gray-200">{{ $meaning->slug }}</span>
                        </div>
                        @endif

                        @if($meaning->translations->count() > 0)
                            <div class="ml-4 p-3 bg-white dark:bg-gray-600 rounded border-l-4 border-blue-500">
                                <div class="font-medium mb-2 dark:text-gray-200">Translations:</div>
                                @foreach($meaning->translations as $translation)
                                    <div class="mb-1">
                                        <span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($translation->locale) }}:</span>
                                        <span class="dark:text-gray-200">{{ $translation->translation }}</span>
                                        @if($translation->transliteration)
                                            <span class="ml-2 italic text-gray-600 dark:text-gray-400">({{ $translation->transliteration }})</span>
                                        @endif
                                    </div>
                                    @if($translation->slug)
                                    <div class="mb-2 ml-10">
                                        <span class="font-bold text-gray-600 dark:text-gray-400">Slug:</span>
                                        <span class="dark:text-gray-200">{{ $translation->slug }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="ml-4 p-3 bg-white dark:bg-gray-600 rounded border-l-4 border-blue-500">
                                <div class="text-gray-500 dark:text-gray-400">No translations available</div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No meanings available for this expression.</div>
                @endforelse
            </div>

            <!-- Standalone Translations Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Standalone Translations</h2>
                @if($expression->translations->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        @foreach($expression->translations as $translation)
                            @if(!$translation->expression_meaning_id)
                                <div class="mb-2">
                                    <span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($translation->locale) }}:</span>
                                    <span class="dark:text-gray-200">{{ $translation->translation }}</span>
                                    @if($translation->transliteration)
                                        <span class="ml-2 italic text-gray-600 dark:text-gray-400">({{ $translation->transliteration }})</span>
                                    @endif
                                </div>
                                @if($translation->slug)
                                <div class="mb-2 ml-10">
                                    <span class="font-bold text-gray-600 dark:text-gray-400">Slug:</span>
                                    <span class="dark:text-gray-200">{{ $translation->slug }}</span>
                                </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No standalone translations available for this expression.</div>
                @endif
            </div>

            <!-- Synonyms Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Synonyms</h2>
                @php
                    $synonyms = $expression->connections->where('pivot.type', 'synonyms')
                        ->merge($expression->connectionsInverse->where('pivot.type', 'synonyms'));
                @endphp
                
                @if($synonyms->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($synonyms as $synonym)
                            <a href="{{ route('backend::expressions.show', $synonym->id) }}" class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white dark:text-gray-200 text-gray-700 rounded-full text-sm transition-colors">
                                {{ $synonym->expression }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No synonyms available for this expression.</div>
                @endif
            </div>

            <!-- Antonyms Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Antonyms</h2>
                @php
                    $antonyms = $expression->connections->where('pivot.type', 'antonyms')
                        ->merge($expression->connectionsInverse->where('pivot.type', 'antonyms'));
                @endphp
                
                @if($antonyms->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($antonyms as $antonym)
                            <a href="{{ route('backend::expressions.show', $antonym->id) }}" class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white dark:text-gray-200 text-gray-700 rounded-full text-sm transition-colors">
                                {{ $antonym->expression }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No antonyms available for this expression.</div>
                @endif
            </div>
            
            <!-- Other Connections Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Other Connections</h2>
                @php
                    $others = $expression->connections->whereNotIn('pivot.type', ['synonyms', 'antonyms'])
                        ->merge($expression->connectionsInverse->whereNotIn('pivot.type', ['synonyms', 'antonyms']));
                @endphp
                
                @if($others->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($others as $other)
                            <div class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-full text-sm">
                                <span class="font-medium">{{ $other->pivot->type }}:</span>
                                <a href="{{ route('backend::expressions.show', $other->id) }}" class="hover:text-blue-500 transition-colors">
                                    {{ $other->expression }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No other connections available for this expression.</div>
                @endif
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg ps-4 pb-4">
            <livewire:expression--expression-edit-using-json :expression="$expression" />
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#delete-expression').on('click', function() {
            if (confirm('Are you sure you want to delete this expression?')) {
                const expressionId = $(this).data('id');

                $.ajax({
                    url: '{{ route("backend::expressions.destroy", $expression->id) }}',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route("backend::expressions.index") }}';
                        } else {
                            alert('Failed to delete expression');
                        }
                    },
                    error: function() {
                        alert('An error occurred while deleting the expression');
                    }
                });
            }
        });
    });
</script>
@endpush
</x-ui-backend::layout>
