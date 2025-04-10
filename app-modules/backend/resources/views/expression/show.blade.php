<x-ui-backend::layout>

<div class="container mx-auto px-4 py-6">
    <div class="mb-5">
        <a href="{{ route('backend::expressions.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Expression List
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white">
                    Expression Details
                </h3>
                <div class="flex space-x-2">
                    <button id="delete-expression" data-id="{{ $expression->id }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Expression Info -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Expression Information</h4>
                    
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</div>
                        <div class="text-base text-gray-800 dark:text-gray-200">{{ $expression->id }}</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Expression</div>
                        <div class="text-base text-gray-800 dark:text-gray-200">{{ $expression->expression }}</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</div>
                        <div class="text-base text-gray-800 dark:text-gray-200">{{ $expression->type ?? 'N/A' }}</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</div>
                        <div class="text-base text-gray-800 dark:text-gray-200">{{ $expression->slug ?? 'N/A' }}</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</div>
                        <div class="text-base text-gray-800 dark:text-gray-200">{{ $expression->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At</div>
                        <div class="text-base text-gray-800 dark:text-gray-200">{{ $expression->updated_at->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
                
                <!-- Pronunciation -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Pronunciation</h4>
                    
                    @php
                        $pronunciations = [];
                        try {
                            $pronunciations = $expression->getTranslations('pronunciation');
                        } catch (\Exception $e) {
                            // Handle the case when pronunciation is not set
                        }
                    @endphp
                    
                    @if(!empty($pronunciations))
                        @foreach($pronunciations as $locale => $pronunciation)
                            <div class="mb-3">
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ strtoupper($locale) }}</div>
                                <div class="text-base text-gray-800 dark:text-gray-200">{{ $pronunciation }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-gray-500 dark:text-gray-400 italic">No pronunciation available</div>
                    @endif
                </div>
            </div>
            
            <!-- Meanings with their translations -->
            <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Meanings with Translations</h4>
                
                @if($expression->meanings->isEmpty())
                    <div class="text-gray-500 dark:text-gray-400 italic">No meanings available</div>
                @else
                    <div class="space-y-6">
                        @foreach($expression->meanings as $meaning)
                            <div class="border-b border-gray-200 dark:border-gray-600 pb-4 last:border-0 last:pb-0">
                                <div class="mb-2">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Meaning</div>
                                    <div class="text-base text-gray-800 dark:text-gray-200">{{ $meaning->meaning }}</div>
                                </div>
                                
                                @if($meaning->slug)
                                <div class="mb-2">
                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</div>
                                    <div class="text-base text-gray-800 dark:text-gray-200">{{ $meaning->slug }}</div>
                                </div>
                                @endif
                                
                                @if($meaning->translations->isNotEmpty())
                                    <div class="mt-3">
                                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Meaning-specific Translations</div>
                                        <div class="ml-4 space-y-2">
                                            @foreach($meaning->translations as $translation)
                                                <div>
                                                    <span class="font-bold text-blue-600 dark:text-blue-400">{{ strtoupper($translation->locale) }}:</span>
                                                    <span class="text-gray-800 dark:text-gray-200 ml-2">{{ $translation->translation }}</span>
                                                    
                                                    @if(!empty($translation->transliteration))
                                                        <span class="ml-2 italic text-gray-500 dark:text-gray-400">({{ $translation->transliteration }})</span>
                                                    @endif
                                                    
                                                    @if($translation->slug)
                                                    <div class="ml-8 mt-1">
                                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug:</span>
                                                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $translation->slug }}</span>
                                                    </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Standalone Translations (not tied to specific meanings) -->
            <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Direct Expression Translations</h4>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    These translations apply to the expression as a whole, not to specific meanings.
                </div>
                
                @if($expression->translations->isEmpty())
                    <div class="text-gray-500 dark:text-gray-400 italic">No direct translations available</div>
                @else
                    <div class="space-y-3">
                        @foreach($expression->translations as $translation)
                            <div>
                                <span class="font-bold text-blue-600 dark:text-blue-400">{{ strtoupper($translation->locale) }}:</span>
                                <span class="text-gray-800 dark:text-gray-200 ml-2">{{ $translation->translation }}</span>
                                
                                @if(!empty($translation->transliteration))
                                    <span class="ml-2 italic text-gray-500 dark:text-gray-400">({{ $translation->transliteration }})</span>
                                @endif
                                
                                @if($translation->slug)
                                <div class="ml-8 mt-1">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug:</span>
                                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $translation->slug }}</span>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Connections -->
            <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Connections</h4>
                
                @if($expression->connections->isEmpty() && $expression->connectionsInverse->isEmpty())
                    <div class="text-gray-500 dark:text-gray-400 italic">No connections available</div>
                @else
                    <div class="space-y-6">
                        <!-- Synonyms -->
                        <div>
                            <h5 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Synonyms</h5>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $synonyms = $expression->connections->where('pivot.type', 'synonym')
                                        ->merge($expression->connectionsInverse->where('pivot.type', 'synonym'));
                                @endphp
                                
                                @if($synonyms->isEmpty())
                                    <div class="text-gray-500 dark:text-gray-400 italic">No synonyms available</div>
                                @else
                                    @foreach($synonyms as $synonym)
                                        <a href="{{ route('backend::expressions.show', $synonym->id) }}" class="px-3 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 rounded-full text-sm">
                                            {{ $synonym->expression }}
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        <!-- Antonyms -->
                        <div>
                            <h5 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Antonyms</h5>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $antonyms = $expression->connections->where('pivot.type', 'antonym')
                                        ->merge($expression->connectionsInverse->where('pivot.type', 'antonym'));
                                @endphp
                                
                                @if($antonyms->isEmpty())
                                    <div class="text-gray-500 dark:text-gray-400 italic">No antonyms available</div>
                                @else
                                    @foreach($antonyms as $antonym)
                                        <a href="{{ route('backend::expressions.show', $antonym->id) }}" class="px-3 py-1 bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 rounded-full text-sm">
                                            {{ $antonym->expression }}
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        <!-- Other connections -->
                        <div>
                            <h5 class="text-md font-medium text-gray-700 dark:text-gray-300 mb-2">Other Connections</h5>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $others = $expression->connections->whereNotIn('pivot.type', ['synonym', 'antonym'])
                                        ->merge($expression->connectionsInverse->whereNotIn('pivot.type', ['synonym', 'antonym']));
                                @endphp
                                
                                @if($others->isEmpty())
                                    <div class="text-gray-500 dark:text-gray-400 italic">No other connections available</div>
                                @else
                                    @foreach($others as $other)
                                        <div class="px-3 py-1 bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-full text-sm">
                                            <span class="font-medium">{{ $other->pivot->type }}:</span>
                                            <a href="{{ route('backend::expressions.show', $other->id) }}" class="ml-1 underline">
                                                {{ $other->expression }}
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
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
