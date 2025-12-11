<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ModuleHelper;
use Symfony\Component\HttpFoundation\Response;

class SetActiveModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = $request->route()?->uri() ?? $request->path();
        
        // Set module based on route prefix
        if (str_starts_with($uri, 'sfinance')) {
            ModuleHelper::setActiveModule(ModuleHelper::MODULE_SFINANCE);
        } elseif (str_starts_with($uri, 'sfinlog')) {
            ModuleHelper::setActiveModule(ModuleHelper::MODULE_SFINLOG);
        } elseif (str_starts_with($uri, 'master-data') || str_starts_with($uri, 'config-')) {
            ModuleHelper::setActiveModule(ModuleHelper::MODULE_MASTER_DATA);
        } elseif (str_starts_with($uri, 'portofolio')) {
            ModuleHelper::setActiveModule(ModuleHelper::MODULE_PORTOFOLIO);
        }

        return $next($request);
    }
}

