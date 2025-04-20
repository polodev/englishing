<!-- Desktop Sidebar -->
<aside :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}"
       class="fixed inset-y-0 left-0 z-40 bg-white dark:bg-gray-800 shadow-lg sidebar-transition hidden lg:block">
    <div class="flex flex-col h-full">
        <div class="flex items-center p-4 border-b dark:border-gray-700" :class="{'justify-center': !sidebarOpen}">
            <div class="flex items-center" :class="{'justify-center w-full': !sidebarOpen}">
                @if(auth()->check() && auth()->user()->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Logo" class="w-8 h-8 rounded-full" :class="{'mr-2': sidebarOpen}">
                @else
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold" :class="{'mr-2': sidebarOpen}">
                        {{ config('app.name')[0] ?? 'E' }}
                    </div>
                @endif
                <span class="text-xl font-semibold dark:text-white" x-show="sidebarOpen">{{ config('app.name', 'Englishing.org') }}</span>
            </div>
        </div>
        <nav class="flex-1 p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('ui-backend::index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <i class="fas fa-home" :class="{'w-6': sidebarOpen}"></i>
                        <span class="ml-3" x-show="sidebarOpen">Dashboard</span>
                    </a>
                </li>
                @hasrole('admin')
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <i class="fas fa-users" :class="{'w-6': sidebarOpen}"></i>
                        <span class="ml-3" x-show="sidebarOpen">Users</span>
                    </a>
                </li>
                @endhasrole
                <!-- Content Management Dropdown -->
                <li x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center w-full p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <i class="fas fa-book-open" :class="{'w-6': sidebarOpen}"></i>
                        <span class="ml-3" x-show="sidebarOpen">Contents</span>
                        <i class="fas fa-chevron-down ml-auto" x-show="sidebarOpen && !open"></i>
                        <i class="fas fa-chevron-up ml-auto" x-show="sidebarOpen && open"></i>
                    </button>
                    <div x-show="open || !sidebarOpen" x-transition class="mt-1" :class="{'pl-4': sidebarOpen}">
                        <ul class="space-y-1" x-show="sidebarOpen">
                            <li>
                                <a href="{{ route('backend::tags.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-tags w-5 h-5"></i>
                                    <span class="ml-3">Tags</span>
                                </a>
                            </li>
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
                            <li>
                                <a href="{{ route('backend::article-word-sets.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-list-alt w-5 h-5"></i>
                                    <span class="ml-3">Article Word Sets</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('backend::article-expression-sets.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-language w-5 h-5"></i>
                                    <span class="ml-3">Article Expression Sets</span>
                                </a>
                            </li>
                        </ul>
                        <div x-show="!sidebarOpen" class="py-1">
                            <a href="{{ route('backend::tags.index') }}" class="block py-2 text-center text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" title="Tags">
                                <i class="fas fa-tags mx-auto"></i>
                            </a>
                            <a href="{{ route('backend::courses.index') }}" class="block py-2 text-center text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" title="Courses">
                                <div class="w-5 h-5 text-gray-700 dark:text-gray-200 mx-auto">
                                    @include('backend::svg.course')
                                </div>
                            </a>
                            <a href="{{ route('backend::articles.index') }}" class="block p-2 text-center text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" title="Articles">
                                <i class="fas fa-newspaper mx-auto"></i>
                            </a>
                            <a href="{{ route('backend::article-word-sets.index') }}" class="block p-2 text-center text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" title="Article Word Sets">
                                <i class="fas fa-list-alt mx-auto"></i>
                            </a>
                            <a href="{{ route('backend::article-expression-sets.index') }}" class="block p-2 text-center text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" title="Article Expression Sets">
                                <i class="fas fa-language mx-auto"></i>
                            </a>
                        </div>
                    </div>
                </li>

                <li>
                    <a href="{{ route('backend::words.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <div class="w-5 h-5 text-gray-700 dark:text-gray-200" :class="{'w-6': sidebarOpen}">
                            @include('backend::svg.word')
                        </div>
                        <span class="ml-3" x-show="sidebarOpen">Words</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('backend::sentences.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <div class="w-5 h-5 text-gray-700 dark:text-gray-200" :class="{'w-6': sidebarOpen}">
                            @include('backend::svg.sentence')
                        </div>
                        <span class="ml-3" x-show="sidebarOpen">Sentences</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('backend::expressions.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <div class="w-5 h-5 text-gray-700 dark:text-gray-200" :class="{'w-6': sidebarOpen}">
                            @include('backend::svg.expression')
                        </div>
                        <span class="ml-3" x-show="sidebarOpen">Expressions</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <i class="fas fa-cog" :class="{'w-6': sidebarOpen}"></i>
                        <span class="ml-3" x-show="sidebarOpen">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-4 border-t dark:border-gray-700" x-show="sidebarOpen">
            <div class="flex items-center">
                @if(auth()->check())
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="User" class="w-8 h-8 rounded-full mr-2">
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mr-2">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-medium dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Role:{{ auth()->user()->role }}</p>
                    </div>
                @else
                    <p class="text-sm font-medium dark:text-white">Guest User</p>
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="flex items-center space-x-2 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-red-500 w-full">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
        <div class="p-4 border-t dark:border-gray-700 text-center" x-show="!sidebarOpen">
            @if(auth()->check())
                @if(auth()->user()->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="User" class="w-8 h-8 rounded-full mx-auto">
                @else
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold mx-auto">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            @else
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold mx-auto">
                    G
                </div>
            @endif
        </div>
    </div>
</aside>
