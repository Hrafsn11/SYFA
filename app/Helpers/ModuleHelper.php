<?php

namespace App\Helpers;

class ModuleHelper
{
    const MODULE_SFINANCE = 'sfinance';
    const MODULE_SFINLOG = 'sfinlog';
    const MODULE_MASTER_DATA = 'master-data';
    const MODULE_PORTOFOLIO = 'portofolio';

    /**
     * Get current active module from session or route
     */
    public static function getCurrentModule(): ?string
    {
        // Check session first
        if (session()->has('active_module')) {
            return session('active_module');
        }

        // Check route prefix
        $route = request()->route();
        if ($route) {
            $uri = $route->uri();
            
            if (str_starts_with($uri, 'sfinance')) {
                return self::MODULE_SFINANCE;
            } elseif (str_starts_with($uri, 'sfinlog')) {
                return self::MODULE_SFINLOG;
            } elseif (str_starts_with($uri, 'master-data') || str_starts_with($uri, 'config-')) {
                return self::MODULE_MASTER_DATA;
            } elseif (str_starts_with($uri, 'portofolio')) {
                return self::MODULE_PORTOFOLIO;
            }
        }

        return null;
    }

    /**
     * Set active module in session
     */
    public static function setActiveModule(string $module): void
    {
        if (in_array($module, [self::MODULE_SFINANCE, self::MODULE_SFINLOG, self::MODULE_MASTER_DATA, self::MODULE_PORTOFOLIO])) {
            session(['active_module' => $module]);
        }
    }

    /**
     * Check if current module is SFinance
     */
    public static function isSFinance(): bool
    {
        return self::getCurrentModule() === self::MODULE_SFINANCE;
    }

    /**
     * Check if current module is SFinlog
     */
    public static function isSFinlog(): bool
    {
        return self::getCurrentModule() === self::MODULE_SFINLOG;
    }

    /**
     * Check if current module is Master Data
     */
    public static function isMasterData(): bool
    {
        return self::getCurrentModule() === self::MODULE_MASTER_DATA;
    }

    /**
     * Check if current module is Portofolio
     */
    public static function isPortofolio(): bool
    {
        return self::getCurrentModule() === self::MODULE_PORTOFOLIO;
    }

    /**
     * Get module name for display
     */
    public static function getModuleName(?string $module = null): string
    {
        $module = $module ?? self::getCurrentModule();
        
        return match($module) {
            self::MODULE_SFINANCE => 'SFinance',
            self::MODULE_SFINLOG => 'SFinlog',
            self::MODULE_MASTER_DATA => 'Master Data & Configuration',
            self::MODULE_PORTOFOLIO => 'Portofolio',
            default => 'SYFA'
        };
    }

    /**
     * Get route prefix for module
     */
    public static function getRoutePrefix(?string $module = null): string
    {
        $module = $module ?? self::getCurrentModule();
        
        return match($module) {
            self::MODULE_SFINANCE => 'sfinance',
            self::MODULE_SFINLOG => 'sfinlog',
            self::MODULE_MASTER_DATA => 'master-data',
            self::MODULE_PORTOFOLIO => 'portofolio',
            default => ''
        };
    }
}

