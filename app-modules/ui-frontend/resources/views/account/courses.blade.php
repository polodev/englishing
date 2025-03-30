<x-ui-frontend::account-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('My Courses') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Manage your enrolled courses and track your progress.') }}</p>
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" x-data="{ activeTab: 'all' }">
                <button @click="activeTab = 'all'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'all', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'all' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('All Courses') }}
                </button>
                <button @click="activeTab = 'in-progress'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'in-progress', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'in-progress' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('In Progress') }}
                </button>
                <button @click="activeTab = 'completed'" :class="{ 'border-indigo-500 text-indigo-600 dark:text-indigo-400': activeTab === 'completed', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600': activeTab !== 'completed' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ __('Completed') }}
                </button>
            </nav>
        </div>
        
        <!-- Course Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- In Progress Course 1 -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden" x-data="{ activeTab: 'all' }" x-show="activeTab === 'all' || activeTab === 'in-progress'">
                <div class="relative h-48 bg-gray-200 dark:bg-gray-600">
                    <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Course Image" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200">
                            {{ __('In Progress') }}
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Everyday Conversations') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Master everyday English conversations for various situations.') }}</p>
                    
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Progress') }}</span>
                            <span class="font-medium text-indigo-600 dark:text-indigo-400">58%</span>
                        </div>
                        <div class="mt-1 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: 58%"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>7/12 {{ __('lessons') }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>{{ __('Intermediate') }}</span>
                        </div>
                    </div>
                    
                    <a href="#" class="mt-4 block text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Continue Course') }}
                    </a>
                </div>
            </div>
            
            <!-- In Progress Course 2 -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden" x-data="{ activeTab: 'all' }" x-show="activeTab === 'all' || activeTab === 'in-progress'">
                <div class="relative h-48 bg-gray-200 dark:bg-gray-600">
                    <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Course Image" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200">
                            {{ __('In Progress') }}
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Academic Writing') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Develop skills for academic essays, research papers, and more.') }}</p>
                    
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Progress') }}</span>
                            <span class="font-medium text-indigo-600 dark:text-indigo-400">20%</span>
                        </div>
                        <div class="mt-1 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: 20%"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>2/10 {{ __('lessons') }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>{{ __('Advanced') }}</span>
                        </div>
                    </div>
                    
                    <a href="#" class="mt-4 block text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Continue Course') }}
                    </a>
                </div>
            </div>
            
            <!-- Completed Course -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden" x-data="{ activeTab: 'all' }" x-show="activeTab === 'all' || activeTab === 'completed'">
                <div class="relative h-48 bg-gray-200 dark:bg-gray-600">
                    <img src="https://images.unsplash.com/photo-1546410531-bb4caa6b424d?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Course Image" class="w-full h-full object-cover">
                    <div class="absolute top-2 right-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                            {{ __('Completed') }}
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('English for Beginners') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('A comprehensive introduction to English language basics.') }}</p>
                    
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('Progress') }}</span>
                            <span class="font-medium text-green-600 dark:text-green-400">100%</span>
                        </div>
                        <div class="mt-1 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ __('Completed on May 12, 2023') }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>{{ __('Beginner') }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex space-x-2">
                        <a href="#" class="flex-1 text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            {{ __('View Certificate') }}
                        </a>
                        <a href="#" class="flex-1 text-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Review') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Recommended Course -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600">
                <div class="relative h-48 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                    <div class="text-center p-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Recommended for You') }}</h3>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Business English') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Learn professional vocabulary and communication skills for the workplace.') }}</p>
                    
                    <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>15 {{ __('lessons') }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="mr-1.5 h-4 w-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>{{ __('Intermediate') }}</span>
                        </div>
                    </div>
                    
                    <a href="#" class="mt-4 block text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Enroll Now') }}
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Browse More Courses Section -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6 mt-8">
            <div class="text-center">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Looking for more courses?') }}</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Explore our catalog of English language courses for all levels.') }}</p>
                <a href="#" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Browse Course Catalog') }}
                </a>
            </div>
        </div>
    </div>
</x-ui-frontend::account-layout>
