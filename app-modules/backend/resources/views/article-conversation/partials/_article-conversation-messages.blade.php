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
                            <div class="ml-auto">
                                <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" 
                                        onclick="deleteMessage('{{ $message->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
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

<script>
    function deleteMessage(id) {
        if (confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
            // Send a DELETE request to remove the message
            fetch(`/dashboard/article-conversation-message/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to reflect the changes
                    window.location.reload();
                } else {
                    alert('Failed to delete the message.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the message.');
            });
        }
    }
</script>
