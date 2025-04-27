<x-ui-backend::layout>
    <x-slot:title>View Article Conversation Set</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('backend::article-conversation-sets.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Article Conversation Set #{{ $articleConversationSet->id }}</h1>
            <div class="ml-auto flex space-x-2">
                <a href="{{ route('backend::article-conversation-sets.edit', $articleConversationSet->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <button type="button" onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
                <form id="delete-form" action="{{ route('backend::article-conversation-sets.destroy', $articleConversationSet->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Conversation Set Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleConversationSet->title }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Article</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">
                            @if($articleConversationSet->article)
                                <a href="{{ route('backend::articles.show', $articleConversationSet->article->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $articleConversationSet->article->title }}
                                </a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleConversationSet->user ? $articleConversationSet->user->name : 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Display Order</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleConversationSet->display_order }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleConversationSet->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated At</h3>
                        <p class="text-lg text-gray-800 dark:text-gray-200">{{ $articleConversationSet->updated_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
                @if($articleConversationSet->content)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Content</h3>
                        <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $articleConversationSet->content }}</p>
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
                                @if(count($articleConversationSet->getTranslations('title_translation')) > 0)
                                    <ul class="space-y-2">
                                        @foreach($articleConversationSet->getTranslations('title_translation') as $locale => $translation)
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
                                @if(count($articleConversationSet->getTranslations('content_translation')) > 0)
                                    <ul class="space-y-2">
                                        @foreach($articleConversationSet->getTranslations('content_translation') as $locale => $translation)
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

        <!-- Conversation Messages -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Conversation Messages</h2>
                
                @if($articleConversationSet->messages && $articleConversationSet->messages->count() > 0)
                    <div class="space-y-4">
                        @foreach($articleConversationSet->messages as $message)
                            <div class="p-4 rounded-lg {{ $message->is_user ? 'bg-blue-50 dark:bg-blue-900 ml-12' : 'bg-gray-50 dark:bg-gray-700 mr-12' }}">
                                <div class="flex items-center mb-2">
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $message->is_user ? 'User' : 'System' }}</span>
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ $message->created_at->format('Y-m-d H:i:s') }}</span>
                                </div>
                                <p class="text-gray-800 dark:text-gray-200">{{ $message->content }}</p>
                                
                                @if(count($message->getTranslations('translation')) > 0)
                                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Translations</h4>
                                        <ul class="space-y-1">
                                            @foreach($message->getTranslations('translation') as $locale => $translation)
                                                <li>
                                                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ strtoupper($locale) }}:</span> 
                                                    <span class="text-gray-800 dark:text-gray-200">{{ $translation }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 dark:text-gray-400 italic">No conversation messages have been added yet.</p>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this conversation set? This action cannot be undone.')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
    @endpush
</x-ui-backend::layout>
