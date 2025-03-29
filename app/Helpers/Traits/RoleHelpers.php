<?php

namespace App\Helpers\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Role-related helper methods
 */
trait RoleHelpers
{
    /**
     * Check if the current authenticated user has a specific role
     *
     * @param string $role
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        /** @var User $user */
        $user = Auth::user();
        return $user->role === $role;
    }

    /**
     * Check if the current authenticated user has any of the given roles
     *
     * @param array|string $roles
     * @return bool
     */
    public static function hasAnyRole(array|string $roles): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        /** @var User $user */
        $user = Auth::user();
        
        if (is_string($roles)) {
            return $user->role === $roles;
        }
        
        return in_array($user->role, $roles);
    }

    /**
     * Check if the current authenticated user has all of the given roles
     *
     * @param array $roles
     * @return bool
     */
    public static function hasAllRoles(array $roles): bool
    {
        // Since our implementation only supports a single role per user,
        // this will only return true if there's exactly one role in the array
        // and the user has that role
        return count($roles) === 1 && self::hasRole($roles[0]);
    }

    /**
     * Check if the current authenticated user is an admin
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }

    /**
     * Get all available roles in the system
     *
     * @return array
     */
    public static function getAllRoles(): array
    {
        return [
            'admin' => 'Administrator',
            'editor' => 'Content Editor',
            'teacher' => 'Teacher',
            'student' => 'Student',
            'user' => 'Regular User',
        ];
    }

    /**
     * Get role display name
     *
     * @param string $role
     * @return string
     */
    public static function getRoleDisplayName(string $role): string
    {
        $roles = self::getAllRoles();
        return $roles[$role] ?? ucfirst($role);
    }
}
