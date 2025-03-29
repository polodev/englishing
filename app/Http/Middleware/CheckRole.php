<?php

namespace App\Http\Middleware;

use App\Helpers\Helpers;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        // If no roles are specified, allow access
        if (empty($roles)) {
            return $next($request);
        }
        
        // Check if user has any of the specified roles
        foreach ($roles as $role) {
            if (Helpers::hasRole($role)) {
                return $next($request);
            }
        }
        
        // User doesn't have any of the required roles
        abort(403, 'Unauthorized action.');
    }
}
