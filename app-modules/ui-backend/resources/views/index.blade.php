<x-ui-backend::layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <h1 class="text-2xl font-bold">{{ __('Dashboard') }}</h1>
        </div>
    </x-slot>
    
    <div class="bg-white rounded-lg shadow-sm p-6">
        <p class="text-gray-700 mb-4">This is dashboard content area.</p>
        <p class="text-gray-500">Placeholder text for the admin dashboard. Real content will be added later.</p>
    </div>
</x-ui-backend::layout>
