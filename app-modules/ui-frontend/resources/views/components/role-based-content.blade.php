@props(['showForRoles' => null])

@php
    use App\Helpers\Helpers;
    
    $userHasAccess = false;
    
    if ($showForRoles === null) {
        // If no roles specified, show content to everyone
        $userHasAccess = true;
    } elseif (auth()->check()) {
        if (is_array($showForRoles)) {
            $userHasAccess = Helpers::hasAnyRole($showForRoles);
        } else {
            $userHasAccess = Helpers::hasRole($showForRoles);
        }
    }
@endphp

@if ($userHasAccess)
    {{ $slot }}
@endif
