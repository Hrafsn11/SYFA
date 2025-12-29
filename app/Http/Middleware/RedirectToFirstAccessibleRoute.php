<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ModuleHelper;
use Symfony\Component\HttpFoundation\Response;

class RedirectToFirstAccessibleRoute
{
    /**
     * Handle an incoming request.
     * 
     * Redirect user to first accessible route based on their permissions.
     * This is used for module entry points (e.g., /sfinance, /sfinlog)
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module = 'sfinance'): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }
        $routePriorities = $this->getRoutePriorities($module);

        foreach ($routePriorities as $route) {
            $permission = $route['permission'] ?? null;
            $routeName = $route['route'];

            if ($permission === null || $user->can($permission)) {
                return redirect()->route($routeName);
            }
        }

        return redirect()->route('home.services')->with('error', 'Anda tidak memiliki akses ke modul ini.');
    }

    private function getRoutePriorities(string $module): array
    {
        $routes = [
            'sfinance' => [
                // Dashboard routes first (for admin/finance roles)
                ['route' => 'sfinance.dashboard.pembiayaan', 'permission' => 'sfinance.menu.dashboard_pembiayaan'],
                ['route' => 'sfinance.dashboard.investasi-deposito', 'permission' => 'sfinance.menu.dashboard_pembiayaan_investasi'],
                // Then feature routes (for debitur/investor)
                ['route' => 'sfinance.peminjaman', 'permission' => 'sfinance.menu.pengajuan_peminjaman'],
                ['route' => 'sfinance.pengajuan-investasi.index', 'permission' => 'sfinance.menu.pengajuan_investasi'],
                ['route' => 'sfinance.penyaluran-deposito.index', 'permission' => 'sfinance.menu.penyaluran_dana'],
                ['route' => 'sfinance.pengembalian-investasi.index', 'permission' => 'sfinance.menu.pengembalian_dana'],
            ],
            'sfinlog' => [
                // Dashboard routes first (for admin/finance roles)
                ['route' => 'sfinlog.dashboard.pembiayaan', 'permission' => 'sfinlog.menu.dashboard_pembiayaan'],
                ['route' => 'sfinlog.dashboard.investasi-deposito', 'permission' => 'sfinlog.menu.dashboard_investasi_deposito'],
                // Then feature routes (for debitur/investor)
                ['route' => 'sfinlog.peminjaman.index', 'permission' => 'sfinlog.menu.pengajuan_peminjaman'],
                ['route' => 'sfinlog.pengajuan-investasi.index', 'permission' => 'sfinlog.menu.pengajuan_investasi'],
                ['route' => 'sfinlog.penyaluran-deposito-sfinlog.index', 'permission' => 'sfinlog.menu.penyaluran_dana'],
                ['route' => 'sfinlog.pengembalian-pinjaman.index', 'permission' => 'sfinlog.menu.pengembalian_dana'],
            ],
        ];

        return $routes[$module] ?? $routes['sfinance'];
    }
}
