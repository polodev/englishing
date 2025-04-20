<x-ui-backend::layout>
    <x-slot:title>View Article Expression Set</x-slot:title>
    <div class="container mx-auto px-4 py-6">
        <!-- Header with actions -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Article Expression Set Details</h1>
            <div class="flex space-x-2">
                <a href="{{ route('backend::article-expression-sets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
                <a href="{{ route('backend::article-expression-sets.edit', $articleExpressionSet) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Main Content -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <!-- Basic Info Section -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</p>
                        <p class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ $articleExpressionSet->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</p>
                        <p class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ $articleExpressionSet->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Related Article</p>
                        <p class="text-lg font-medium text-gray-800 dark:text-gray-200">
                            @if($articleExpressionSet->article)
                                <a href="{{ route('backend::articles.show', $articleExpressionSet->article->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $articleExpressionSet->article->title }}
                                </a>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic">No article associated</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Display Order</p>
                        <p class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ $articleExpressionSet->display_order }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</p>
                        <p class="text-lg font-medium text-gray-800 dark:text-gray-200">
                            {{ $articleExpressionSet->user ? $articleExpressionSet->user->name : 'Unknown' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</p>
                        <p class="text-lg font-medium text-gray-800 dark:text-gray-200">{{ $articleExpressionSet->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Column Order</p>
                        <p class="text-lg font-medium text-gray-800 dark:text-gray-200">
                            @if($articleExpressionSet->column_order && is_array($articleExpressionSet->column_order) && count($articleExpressionSet->column_order) > 0)
                                {{ implode(', ', $articleExpressionSet->column_order) }}
                            @else
                                <span class="text-gray-500 dark:text-gray-400 italic">No column order defined</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Content</h2>
                <div class="prose dark:prose-invert max-w-none dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-3 rounded-md font-mono text-sm whitespace-pre-wrap">
                    {{ $articleExpressionSet->content }}
                </div>
            </div>
            
            <!-- Expression Set Lists Section -->
            @include('backend::article-expression-set.partials._article-expression-set-list-and-its-translation')

            <!-- Title Translations Section -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Title Translations</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    @if(count($articleExpressionSet->getTranslations('title_translation')) > 0)
                        <div class="space-y-4">
                            @foreach($articleExpressionSet->getTranslations('title_translation') as $locale => $translation)
                                <div class="border-b border-gray-200 dark:border-gray-600 pb-4 last:border-0 last:pb-0">
                                    <span class="inline-block {{ $locale == 'bn' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }} px-2 py-1 rounded text-xs font-bold uppercase mb-2">{{ strtoupper($locale) }}</span>
                                    <div class="dark:text-gray-200">{{ $translation }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-500 dark:text-gray-400 italic">No translations available</div>
                    @endif
                </div>
            </div>

            <!-- Content Translations Section -->
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Content Translations</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    @if(count($articleExpressionSet->getTranslations('content_translation')) > 0)
                        <div class="space-y-4">
                            @foreach($articleExpressionSet->getTranslations('content_translation') as $locale => $translation)
                                <div class="border-b border-gray-200 dark:border-gray-600 pb-4 last:border-0 last:pb-0">
                                    <span class="inline-block {{ $locale == 'bn' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }} px-2 py-1 rounded text-xs font-bold uppercase mb-2">{{ strtoupper($locale) }}</span>
                                    <div class="prose dark:prose-invert max-w-none dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-3 rounded-md font-mono text-sm whitespace-pre-wrap">
                                        {{ $translation }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-500 dark:text-gray-400 italic">No translations available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-ui-backend::layout>
