@props([
    'type' => 'success', // success, error, warning, info
    'message' => '',
    'duration' => 5000,
    'position' => 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
])

@php
    $typeClasses = [
        'success' => 'bg-green-500 text-white',
        'error' => 'bg-red-500 text-white',
        'warning' => 'bg-yellow-500 text-white',
        'info' => 'bg-blue-500 text-white',
    ];
    
    $positionClasses = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4',
        'top-center' => 'top-4 left-1/2 transform -translate-x-1/2',
        'bottom-center' => 'bottom-4 left-1/2 transform -translate-x-1/2',
    ];
    
    $bgClass = $typeClasses[$type] ?? $typeClasses['info'];
    $positionClass = $positionClasses[$position] ?? $positionClasses['top-right'];
@endphp

<div
    x-data="{ show: false, message: '{{ $message }}', type: '{{ $type }}' }"
    x-init="
        $nextTick(() => { show = true });
        setTimeout(() => { show = false }, {{ $duration }});
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    @new-toast.window="
        show = false;
        setTimeout(() => {
            message = $event.detail.message;
            type = $event.detail.type || 'info';
            show = true;
            setTimeout(() => { show = false }, {{ $duration }});
        }, 300);
    "
    class="fixed {{ $positionClass }} z-50 p-4 rounded-lg shadow-lg {{ $bgClass }} flex items-center space-x-3 min-w-[300px] max-w-md"
    style="display: none;"
>
    <div class="flex-shrink-0">
        <template x-if="type === 'success'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </template>
        <template x-if="type === 'error'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </template>
        <template x-if="type === 'warning'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </template>
        <template x-if="type === 'info'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </template>
    </div>
    <div class="flex-1">
        <p x-text="message" class="text-sm font-medium"></p>
    </div>
    <button @click="show = false" class="flex-shrink-0 text-white hover:text-gray-200 focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>
