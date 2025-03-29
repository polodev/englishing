@props(['title' => 'Admin Dashboard'])

@php
    use App\Helpers\Helpers;
@endphp

<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $title }}</h2>
    </div>
    
    {{-- This content will only be visible to admins --}}
    @role('admin')
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Admin Controls</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="bg-indigo-50 dark:bg-indigo-900 p-4 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-800 transition">
                    <div class="flex items-center">
                        <span class="bg-indigo-500 text-white p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">User Management</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage user accounts and roles</p>
                        </div>
                    </div>
                </a>
                
                <a href="#" class="bg-green-50 dark:bg-green-900 p-4 rounded-lg hover:bg-green-100 dark:hover:bg-green-800 transition">
                    <div class="flex items-center">
                        <span class="bg-green-500 text-white p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
                                <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Content Management</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage website content</p>
                        </div>
                    </div>
                </a>
                
                <a href="#" class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-800 transition">
                    <div class="flex items-center">
                        <span class="bg-purple-500 text-white p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Site Settings</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Configure website settings</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endrole
    
    {{-- This content will be visible to both admins and editors --}}
    @hasanyrole(['admin', 'editor'])
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Content Tools</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="#" class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-800 transition">
                    <div class="flex items-center">
                        <span class="bg-blue-500 text-white p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Edit Content</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Create and edit website content</p>
                        </div>
                    </div>
                </a>
                
                <a href="#" class="bg-amber-50 dark:bg-amber-900 p-4 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-800 transition">
                    <div class="flex items-center">
                        <span class="bg-amber-500 text-white p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Media Library</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage images and media files</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endhasanyrole
    
    {{-- This content will be visible to teachers --}}
    @hasrole('teacher')
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Teacher Tools</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="#" class="bg-teal-50 dark:bg-teal-900 p-4 rounded-lg hover:bg-teal-100 dark:hover:bg-teal-800 transition">
                    <div class="flex items-center">
                        <span class="bg-teal-500 text-white p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Manage Courses</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Create and manage your courses</p>
                        </div>
                    </div>
                </a>
                
                <a href="#" class="bg-rose-50 dark:bg-rose-900 p-4 rounded-lg hover:bg-rose-100 dark:hover:bg-rose-800 transition">
                    <div class="flex items-center">
                        <span class="bg-rose-500 text-white p-2 rounded-full mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-white">Student Assessments</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage student grades and assessments</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endhasrole
    
    {{-- This section is visible to all authenticated users --}}
    <div>
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">User Profile</h3>
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <div class="flex items-center">
                <span class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 p-2 rounded-full mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                    </svg>
                </span>
                <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white">Your Account</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Update your profile and preferences</p>
                </div>
            </div>
        </div>
    </div>
</div>
