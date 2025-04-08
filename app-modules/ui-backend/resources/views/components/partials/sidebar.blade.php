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
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <i class="fas fa-newspaper" :class="{'w-6': sidebarOpen}"></i>
                        <span class="ml-3" x-show="sidebarOpen">Articles</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <i class="fas fa-comment" :class="{'w-6': sidebarOpen}"></i>
                        <span class="ml-3" x-show="sidebarOpen">Sentences</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('backend::words.index') }}" class="flex items-center p-2 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" :class="{'justify-center': !sidebarOpen}">
                        <i class="fas fa-book" :class="{'w-6': sidebarOpen}"></i>
                        <span class="ml-3" x-show="sidebarOpen">Words</span>
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
