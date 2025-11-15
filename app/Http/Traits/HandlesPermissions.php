<?php

namespace App\Http\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

trait HandlesPermissions
{
    /**
     * Check if user has permission and throw exception if not
     *
     * @param string $permission
     * @param string|null $customMessage
     * @throws AuthorizationException
     */
    protected function checkPermission(string $permission, ?string $customMessage = null): void
    {
        if (!auth()->user()->can($permission)) {
            $message = $customMessage ?? "You don't have permission to {$permission}.";
            throw new AuthorizationException($message);
        }
    }

    /**
     * Check if user has any of the given permissions
     *
     * @param array $permissions
     * @param string|null $customMessage
     * @throws AuthorizationException
     */
    protected function checkAnyPermission(array $permissions, ?string $customMessage = null): void
    {
        if (!auth()->user()->hasAnyPermission($permissions)) {
            $message = $customMessage ?? "You don't have the required permissions to access this resource.";
            throw new AuthorizationException($message);
        }
    }

    /**
     * Check if user has all of the given permissions
     *
     * @param array $permissions
     * @param string|null $customMessage
     * @throws AuthorizationException
     */
    protected function checkAllPermissions(array $permissions, ?string $customMessage = null): void
    {
        if (!auth()->user()->hasAllPermissions($permissions)) {
            $message = $customMessage ?? "You don't have all the required permissions to access this resource.";
            throw new AuthorizationException($message);
        }
    }

    /**
     * Check if user has permission with custom response
     *
     * @param string $permission
     * @param Request $request
     * @param string|null $customMessage
     * @return bool
     */
    protected function hasPermissionOrFail(string $permission, Request $request, ?string $customMessage = null): bool
    {
        if (!auth()->user()->can($permission)) {
            $message = $customMessage ?? "You don't have permission to {$permission}.";
            
            if ($request->expectsJson()) {
                throw new AuthorizationException($message);
            }
            
            // For web requests, you can customize the response here
            throw new AuthorizationException($message);
        }
        
        return true;
    }

    /**
     * Check if user has role
     *
     * @param string $role
     * @param string|null $customMessage
     * @throws AuthorizationException
     */
    protected function checkRole(string $role, ?string $customMessage = null): void
    {
        if (!auth()->user()->hasRole($role)) {
            $message = $customMessage ?? "You must have the {$role} role to access this resource.";
            throw new AuthorizationException($message);
        }
    }
}