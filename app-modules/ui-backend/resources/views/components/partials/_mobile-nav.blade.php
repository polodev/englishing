<!-- Mobile Navigation -->
<div x-cloak x-show="mobileMenuOpen" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 lg:hidden"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-lg">
        <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
            <div class="flex items-center">
                @if(config('app.logo'))
                    <img src="{{ asset(config('app.logo')) }}" alt="Logo" class="w-8 h-8 mr-2">
                @else
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-2">E</div>
                @endif
                <span class="text-xl font-semibold dark:text-white">{{ config('app.name', 'Englishing.org') }}</span>
            </div>
            <button @click="mobileMenuOpen = false" class="p-2 text-gray-500 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('ui-backend::index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-home w-6"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                </li>
                @hasrole('admin')
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-users w-6"></i>
                        <span class="ml-3">Users</span>
                    </a>
                </li>
                @endhasrole
                <!-- Content Management Dropdown -->
                <li x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center w-full p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-book-open w-6"></i>
                        <span class="ml-3">Contents</span>
                        <i class="fas fa-chevron-down ml-auto" x-show="!open"></i>
                        <i class="fas fa-chevron-up ml-auto" x-show="open"></i>
                    </button>
                    <div x-show="open" x-transition class="pl-4 mt-1">
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('backend::courses.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <div class="w-5 h-5 text-gray-700 dark:text-gray-200">
                                        @include('backend::svg.course')
                                    </div>
                                    <span class="ml-3">Courses</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('backend::articles.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-newspaper w-5 h-5"></i>
                                    <span class="ml-3">Articles</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="{{ route('backend::words.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="w-5 h-5 text-gray-700 dark:text-gray-200">
                            @include('backend::svg.word')
                        </div>
                        <span class="ml-3">Words</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('backend::sentences.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="w-5 h-5 text-gray-700 dark:text-gray-200">
                            @include('backend::svg.sentence')
                        </div>
                        <span class="ml-3">Sentences</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('backend::expressions.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="w-5 h-5 text-gray-700 dark:text-gray-200">
                            @include('backend::svg.expression')
                        </div>
                        <span class="ml-3">Expressions</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-cog w-6"></i>
                        <span class="ml-3">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
