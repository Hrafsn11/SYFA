<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelper
{
    /**
     * Generate route name with module prefix
     */
    public static function route(string $name, array $parameters = [], bool $absolute = true): string
    {
        $module = ModuleHelper::getCurrentModule();
        $prefix = ModuleHelper::getRoutePrefix($module);
        
        // If no module or module is master-data/portofolio, use original route
        if (empty($prefix) || $module === ModuleHelper::MODULE_MASTER_DATA || $module === ModuleHelper::MODULE_PORTOFOLIO) {
            try {
                return route($name, $parameters, $absolute);
            } catch (\Exception $e) {
                // If route doesn't exist, try with prefix
                $prefixedName = $prefix . '.' . $name;
                if (Route::has($prefixedName)) {
                    return route($prefixedName, $parameters, $absolute);
                }
                throw $e;
            }
        }
        
        // Try to find route with module prefix first
        $prefixedName = $prefix . '.' . $name;
        
        if (Route::has($prefixedName)) {
            return route($prefixedName, $parameters, $absolute);
        }
        
        // Fallback to original route if prefixed route doesn't exist
        try {
            return route($name, $parameters, $absolute);
        } catch (\Exception $e) {
            // If both don't exist, throw the exception
            throw $e;
        }
    }

    /**
     * Check if route matches with module prefix
     */
    public static function routeIs(string $pattern): bool
    {
        $module = ModuleHelper::getCurrentModule();
        $prefix = ModuleHelper::getRoutePrefix($module);
        
        // If no module or module is master-data/portofolio, use original check
        if (empty($prefix) || $module === ModuleHelper::MODULE_MASTER_DATA || $module === ModuleHelper::MODULE_PORTOFOLIO) {
            return request()->routeIs($pattern);
        }
        
        // Check both original and prefixed routes
        $prefixedPattern = $prefix . '.' . $pattern;
        return request()->routeIs($pattern) || request()->routeIs($prefixedPattern);
    }

    /**
     * Check if current path matches with module prefix
     */
    public static function is(string $pattern): bool
    {
        $module = ModuleHelper::getCurrentModule();
        $prefix = ModuleHelper::getRoutePrefix($module);
        
        // If no module or module is master-data/portofolio, use original check
        if (empty($prefix) || $module === ModuleHelper::MODULE_MASTER_DATA || $module === ModuleHelper::MODULE_PORTOFOLIO) {
            return request()->is($pattern);
        }
        
        // Check both original and prefixed paths
        $prefixedPattern = $prefix . '/' . ltrim($pattern, '/');
        return request()->is($pattern) || request()->is($prefixedPattern);
    }
}

