<x-ui-frontend::account-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Account Settings') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Manage your account preferences and settings.') }}</p>
        </div>
        
        <!-- Settings Sections -->
        <div class="space-y-8">
            <!-- Notification Settings -->
            <div class="bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Notification Settings') }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ __('Control how and when you receive notifications.') }}</p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="email_notifications" name="email_notifications" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="email_notifications" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Email Notifications') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Receive email notifications about course updates, new lessons, and other important information.') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="browser_notifications" name="browser_notifications" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="browser_notifications" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Browser Notifications') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Receive browser notifications when you are on the site.') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="weekly_digest" name="weekly_digest" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="weekly_digest" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Weekly Learning Digest') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Receive a weekly summary of your learning progress and recommended content.') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="marketing_emails" name="marketing_emails" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="marketing_emails" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Marketing Emails') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Receive emails about new courses, special offers, and other promotional content.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Privacy Settings -->
            <div class="bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Privacy Settings') }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ __('Control your privacy and data sharing preferences.') }}</p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="profile_visibility" name="profile_visibility" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="profile_visibility" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Public Profile') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Make your profile visible to other users on the platform.') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="learning_progress" name="learning_progress" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="learning_progress" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Learning Progress') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Show your learning progress and achievements on your public profile.') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="data_collection" name="data_collection" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="data_collection" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Data Collection') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Allow us to collect data about your learning habits to improve your experience.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Display Settings -->
            <div class="bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Display Settings') }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ __('Customize how the platform looks and behaves.') }}</p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                        <!-- Theme Selector -->
                        <div>
                            <label for="theme" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Theme') }}</label>
                            <select id="theme" name="theme" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="light">{{ __('Light') }}</option>
                                <option value="dark">{{ __('Dark') }}</option>
                                <option value="system">{{ __('System Default') }}</option>
                            </select>
                        </div>
                        
                        <!-- Font Size -->
                        <div>
                            <label for="font_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Font Size') }}</label>
                            <select id="font_size" name="font_size" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="small">{{ __('Small') }}</option>
                                <option value="medium">{{ __('Medium') }}</option>
                                <option value="large">{{ __('Large') }}</option>
                            </select>
                        </div>
                        
                        <!-- Reduced Motion -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="reduced_motion" name="reduced_motion" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="reduced_motion" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Reduced Motion') }}</label>
                                <p class="text-gray-500 dark:text-gray-400">{{ __('Minimize animations and transitions for improved accessibility.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Management -->
            <div class="bg-white dark:bg-gray-700 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Account Management') }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">{{ __('Manage your account data and subscription.') }}</p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                        <!-- Subscription Status -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Current Subscription') }}</h4>
                            <div class="mt-2 flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                                    {{ __('Premium Plan') }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Renews on October 15, 2023') }}</span>
                            </div>
                            <div class="mt-4">
                                <a href="#" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">{{ __('Manage Subscription') }}</a>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                            <div class="flex justify-between">
                                <a href="#" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">{{ __('Download Personal Data') }}</a>
                                <a href="#" class="text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-500">{{ __('Delete Account') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Save Settings') }}
            </button>
        </div>
    </div>
</x-ui-frontend::account-layout>
