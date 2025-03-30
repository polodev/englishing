<x-ui-frontend::account-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Profile') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Manage your account information and settings.') }}</p>
        </div>
        
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1 bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Personal Information') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Update your account\'s profile information.') }}</p>
                </div>
                <div class="md:col-span-2 px-4 py-5 sm:p-6">
                    <form action="#" method="POST">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <img class="h-16 w-16 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url ?? asset('img/default-avatar.png') }}" alt="{{ auth()->user()->name ?? 'User' }}">
                                </div>
                                <div class="ml-4">
                                    <div class="relative bg-gray-100 dark:bg-gray-600 py-2 px-3 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm">
                                        <input type="file" id="profile_photo" name="profile_photo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Change Photo') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" value="{{ auth()->user()->name ?? 'John Doe' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Email') }}</label>
                                <input type="email" name="email" id="email" value="{{ auth()->user()->email ?? 'john@example.com' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Bio') }}</label>
                                <textarea id="bio" name="bio" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ auth()->user()->bio ?? 'I am learning English to improve my communication skills.' }}</textarea>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Brief description for your profile.') }}</p>
                            </div>
                            
                            <div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1 bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Password') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Ensure your account is using a secure password.') }}</p>
                </div>
                <div class="md:col-span-2 px-4 py-5 sm:p-6">
                    <form action="#" method="POST">
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Current Password') }}</label>
                                <input type="password" name="current_password" id="current_password" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('New Password') }}</label>
                                <input type="password" name="password" id="password" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            
                            <div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Update Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1 bg-gray-50 dark:bg-gray-800 px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Language Preferences') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Set your preferred languages for learning.') }}</p>
                </div>
                <div class="md:col-span-2 px-4 py-5 sm:p-6">
                    <form action="#" method="POST">
                        <div class="space-y-4">
                            <div>
                                <label for="native_language" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Native Language') }}</label>
                                <select id="native_language" name="native_language" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="bn">{{ __('Bengali') }}</option>
                                    <option value="en">{{ __('English') }}</option>
                                    <option value="hi">{{ __('Hindi') }}</option>
                                    <option value="es">{{ __('Spanish') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="learning_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('English Level') }}</label>
                                <select id="learning_level" name="learning_level" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="beginner">{{ __('Beginner') }}</option>
                                    <option value="intermediate" selected>{{ __('Intermediate') }}</option>
                                    <option value="advanced">{{ __('Advanced') }}</option>
                                    <option value="fluent">{{ __('Fluent') }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Learning Goals') }}</label>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="goals_conversation" name="goals[]" type="checkbox" value="conversation" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="goals_conversation" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Conversation') }}</label>
                                            <p class="text-gray-500 dark:text-gray-400">{{ __('Improve speaking and listening skills') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="goals_business" name="goals[]" type="checkbox" value="business" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="goals_business" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Business English') }}</label>
                                            <p class="text-gray-500 dark:text-gray-400">{{ __('Professional vocabulary and communication') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="goals_academic" name="goals[]" type="checkbox" value="academic" checked class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="goals_academic" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Academic') }}</label>
                                            <p class="text-gray-500 dark:text-gray-400">{{ __('Reading and writing for academic purposes') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Save Preferences') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-ui-frontend::account-layout>
