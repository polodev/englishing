<x-ui-backend::layout>
    <x-slot:title>View Article Double Sentence Set</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('backend::article-double-sentence-sets.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Article Double Sentence Set #{{ $articleDoubleSentenceSet->id }}</h1>
            <div class="ml-auto flex space-x-2">
                <a href="{{ route('backend::article-double-sentence-sets.edit', $articleDoubleSentenceSet->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <button type="button" onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
                <form id="delete-form" action="{{ route('backend::article-double-sentence-sets.destroy', $articleDoubleSentenceSet->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Double Sentence Set Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleDoubleSentenceSet->title }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Article</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">
                            @if($articleDoubleSentenceSet->article)
                                <a href="{{ route('backend::articles.show', $articleDoubleSentenceSet->article->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $articleDoubleSentenceSet->article->title }}
                                </a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleDoubleSentenceSet->user ? $articleDoubleSentenceSet->user->name : 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Display Order</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleDoubleSentenceSet->display_order }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleDoubleSentenceSet->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleDoubleSentenceSet->updated_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
                @if($articleDoubleSentenceSet->content)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Content</h3>
                        <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $articleDoubleSentenceSet->content }}</p>
                        </div>
                    </div>
                @endif

                <!-- Translations -->
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Translations</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Title Translations</h3>
                            <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                @if(count($articleDoubleSentenceSet->getTranslations('title_translation')) > 0)
                                    <ul class="space-y-2">
                                        @foreach($articleDoubleSentenceSet->getTranslations('title_translation') as $locale => $translation)
                                            <li>
                                                <span class="font-semibold text-blue-600 dark:text-blue-400">{{ strtoupper($locale) }}:</span> 
                                                <span class="text-gray-800 dark:text-gray-200">{{ $translation }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-600 dark:text-gray-400 italic">No translations available</p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Content Translations</h3>
                            <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                @if(count($articleDoubleSentenceSet->getTranslations('content_translation')) > 0)
                                    <ul class="space-y-2">
                                        @foreach($articleDoubleSentenceSet->getTranslations('content_translation') as $locale => $translation)
                                            <li>
                                                <span class="font-semibold text-blue-600 dark:text-blue-400">{{ strtoupper($locale) }}:</span> 
                                                <span class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $translation }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-600 dark:text-gray-400 italic">No translations available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Double Sentence Lists -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Double Sentence Lists</h2>
                @if($articleDoubleSentenceSet->lists && $articleDoubleSentenceSet->lists->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">First Sentence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Second Sentence</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Translations</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($articleDoubleSentenceSet->lists as $list)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $list->id }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ $list->first_sentence }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">{{ $list->second_sentence }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                            @if(count($list->getTranslations('translation')) > 0)
                                                <ul class="space-y-1">
                                                    @foreach($list->getTranslations('translation') as $locale => $translation)
                                                        <li>
                                                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ strtoupper($locale) }}:</span> 
                                                            <span>{{ $translation }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-gray-500 dark:text-gray-400 italic">No translations</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $list->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400 italic">No double sentence lists have been added yet.</p>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this double sentence set? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
    @endpush
</x-ui-backend::layout>
