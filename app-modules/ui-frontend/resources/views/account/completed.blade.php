<x-ui-frontend::account-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Completed Content') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Track your learning progress and achievements.') }}</p>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" x-data="{ activeTab: 'all' }">
                <button @click="activeTab = 'all'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'all', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'all' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('All') }}
                </button>
                <button @click="activeTab = 'courses'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'courses', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'courses' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('Courses') }}
                </button>
                <button @click="activeTab = 'lessons'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'lessons', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'lessons' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('Lessons') }}
                </button>
                <button @click="activeTab = 'quizzes'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'quizzes', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'quizzes' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('Quizzes') }}
                </button>
            </nav>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-md bg-indigo-100 dark:bg-indigo-800">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Courses') }}</h3>
                        <p class="text-2xl font-semibold text-indigo-600 dark:text-indigo-400">3</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-md bg-green-100 dark:bg-green-800">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Lessons') }}</h3>
                        <p class="text-2xl font-semibold text-green-600 dark:text-green-400">27</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-md bg-purple-100 dark:bg-purple-800">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Quizzes') }}</h3>
                        <p class="text-2xl font-semibold text-purple-600 dark:text-purple-400">15</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 rounded-md bg-yellow-100 dark:bg-yellow-800">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Hours') }}</h3>
                        <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">42</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Completed Items List -->
        <div class="space-y-4">
            <!-- Course Item -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden" x-data="{ activeTab: 'all' }" x-show="activeTab === 'all' || activeTab === 'courses'">
                <div class="md:flex">
                    <div class="md:flex-shrink-0">
                        <img class="h-48 w-full object-cover md:w-48" src="https://images.unsplash.com/photo-1546410531-bb4caa6b424d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Course image">
                    </div>
                    <div class="p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200">
                            {{ __('Course') }}
                        </span>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">{{ __('English for Beginners') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('A comprehensive introduction to English language basics.') }}</p>
                        
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center text-green-600 dark:text-green-400">
                                    <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Completed on May 12, 2023') }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1 text-gray-500 dark:text-gray-400">5.0</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 flex space-x-3">
                            <a href="#" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('View Certificate') }}
                            </a>
                            <a href="#" class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Review Course') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lesson Item -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4" x-data="{ activeTab: 'all' }" x-show="activeTab === 'all' || activeTab === 'lessons'">
                <div class="flex justify-between">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                            {{ __('Lesson') }}
                        </span>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">{{ __('Present Perfect Tense') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Learn how to form and use the present perfect tense in English.') }}</p>
                        <div class="mt-2 flex items-center text-sm text-green-600 dark:text-green-400">
                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('Completed on June 3, 2023') }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex flex-col items-end">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">95%</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Quiz Score') }}</span>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Review Lesson') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quiz Item -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4" x-data="{ activeTab: 'all' }" x-show="activeTab === 'all' || activeTab === 'quizzes'">
                <div class="flex justify-between">
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-200">
                            {{ __('Quiz') }}
                        </span>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">{{ __('Vocabulary Assessment: Intermediate Level') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Test your knowledge of intermediate English vocabulary.') }}</p>
                        <div class="mt-2 flex items-center text-sm text-green-600 dark:text-green-400">
                            <svg class="flex-shrink-0 mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('Completed on July 15, 2023') }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex flex-col items-end">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">42/50</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Score') }}</span>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('View Results') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    {{ __('Previous') }}
                </a>
                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    {{ __('Next') }}
                </a>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        {{ __('Showing') }} <span class="font-medium">1</span> {{ __('to') }} <span class="font-medium">3</span> {{ __('of') }} <span class="font-medium">45</span> {{ __('results') }}
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <span class="sr-only">{{ __('Previous') }}</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <a href="#" aria-current="page" class="z-10 bg-indigo-50 dark:bg-indigo-900 border-indigo-500 dark:border-indigo-500 text-indigo-600 dark:text-indigo-200 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            1
                        </a>
                        <a href="#" class="bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            2
                        </a>
                        <a href="#" class="bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            3
                        </a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <span class="sr-only">{{ __('Next') }}</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</x-ui-frontend::account-layout>
