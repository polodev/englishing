<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Double Word Lists</h2>
        @if($articleDoubleWordSet->lists && $articleDoubleWordSet->lists->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">First Word</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Second Word</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Translations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($articleDoubleWordSet->lists as $list)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $list->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $list->first_word }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $list->second_word }}</td>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button type="button" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 mr-2" 
                                            onclick="deleteList('{{ $list->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400 italic">No double word lists have been added yet.</p>
        @endif
    </div>
</div>

<script>
    function deleteList(id) {
        if (confirm('Are you sure you want to delete this double word list? This action cannot be undone.')) {
            // Send a DELETE request to remove the list
            fetch(`/dashboard/article-double-word-list/${id}`, {
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
                    alert('Failed to delete the double word list.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the double word list.');
            });
        }
    }
</script>
