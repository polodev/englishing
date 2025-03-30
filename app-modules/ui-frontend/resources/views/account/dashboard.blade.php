<x-ui-frontend::account-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Dashboard') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Welcome to your account dashboard.') }}</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Learning Progress Card -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow p-4 text-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ __('Learning Progress') }}</h3>
                    <svg class="h-8 w-8 opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium">{{ __('Overall Progress') }}</span>
                        <span class="text-sm font-medium">65%</span>
                    </div>
                    <div class="w-full bg-white bg-opacity-30 rounded-full h-2">
                        <div class="bg-white h-2 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
                <div class="mt-4 flex justify-between text-sm">
                    <span>{{ __('Courses Completed') }}: 3/5</span>
                    <span>{{ __('Streak') }}: 7 {{ __('days') }}</span>
                </div>
            </div>
            
            <!-- Recent Activity Card -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recent Activity') }}</h3>
                <div class="mt-4 space-y-3">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-green-100 dark:bg-green-800 rounded-full p-1">
                            <svg class="h-4 w-4 text-green-600 dark:text-green-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Completed lesson') }}: {{ __('Basic Greetings') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('2 hours ago') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-800 rounded-full p-1">
                            <svg class="h-4 w-4 text-blue-600 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Bookmarked article') }}: {{ __('10 Common English Mistakes') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Yesterday') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-800 rounded-full p-1">
                            <svg class="h-4 w-4 text-purple-600 dark:text-purple-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Viewed course') }}: {{ __('Advanced Conversation') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('3 days ago') }}</p>
                        </div>
                    </div>
                </div>
                <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                    {{ __('View all activity') }}
                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            
            <!-- Recommended Card -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Recommended For You') }}</h3>
                <div class="mt-4 space-y-3">
                    <a href="#" class="block p-3 bg-gray-50 dark:bg-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-500 transition">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Business English') }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Learn professional vocabulary and email writing') }}</p>
                        <div class="mt-2 flex items-center">
                            <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ __('Intermediate') }}</span>
                            <span class="mx-2 text-gray-300 dark:text-gray-500">•</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">12 {{ __('lessons') }}</span>
                        </div>
                    </a>
                    <a href="#" class="block p-3 bg-gray-50 dark:bg-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-500 transition">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Pronunciation Practice') }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Master difficult sounds and intonation patterns') }}</p>
                        <div class="mt-2 flex items-center">
                            <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">{{ __('All Levels') }}</span>
                            <span class="mx-2 text-gray-300 dark:text-gray-500">•</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">8 {{ __('lessons') }}</span>
                        </div>
                    </a>
                </div>
                <a href="#" class="mt-4 inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                    {{ __('View all recommendations') }}
                    <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Current Courses -->
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Continue Learning') }}</h2>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow overflow-hidden">
                    <div class="h-40 bg-gray-200 dark:bg-gray-600 relative">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Course Image" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                            <span class="text-white text-sm font-medium">{{ __('Intermediate') }}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Everyday Conversations') }}</h3>
                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <svg class="mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('Progress') }}: 7/12 {{ __('lessons') }}</span>
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: 58%"></div>
                            </div>
                        </div>
                        <a href="#" class="mt-4 block text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Continue Course') }}
                        </a>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow overflow-hidden">
                    <div class="h-40 bg-gray-200 dark:bg-gray-600 relative">
                        <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Course Image" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                            <span class="text-white text-sm font-medium">{{ __('Advanced') }}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Academic Writing') }}</h3>
                        <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <svg class="mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('Progress') }}: 2/10 {{ __('lessons') }}</span>
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: 20%"></div>
                            </div>
                        </div>
                        <a href="#" class="mt-4 block text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Continue Course') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-ui-frontend::account-layout>
