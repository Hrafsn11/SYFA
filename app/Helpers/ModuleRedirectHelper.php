<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class ModuleRedirectHelper
{
    /**
     * Redirect to the first accessible route in the module
     *
     * @param string $module Module name ('sfinance' or 'sfinlog')
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirectToFirstAccessible(string $module = 'sfinance')
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $routes = [
            'sfinance' => [
                ['route' => 'sfinance.dashboard.index', 'permission' => 'sfinance.menu.dashboard_pembiayaan'],
                ['route' => 'sfinance.dashboard.pembiayaan', 'permission' => 'sfinance.menu.dashboard_pembiayaan'],
                ['route' => 'sfinance.dashboard.investasi-deposito', 'permission' => 'sfinance.menu.dashboard_pembiayaan_investasi'],
                ['route' => 'sfinance.peminjaman', 'permission' => 'sfinance.menu.pengajuan_peminjaman'],
                ['route' => 'sfinance.pengajuan-investasi.index', 'permission' => 'sfinance.menu.pengajuan_investasi'],
                ['route' => 'sfinance.penyaluran-dana-investasi.index', 'permission' => 'sfinance.menu.penyaluran_deposito'],
                ['route' => 'sfinance.pengembalian.index', 'permission' => 'sfinance.menu.pengembalian_dana'],
                ['route' => 'sfinance.pengembalian-investasi.index', 'permission' => 'sfinance.menu.pengembalian_investasi'],
                ['route' => 'sfinance.laporan-investasi-sfinance.index', 'permission' => 'sfinance.menu.kertas_kerja_investor'],
            ],
            'sfinlog' => [
                ['route' => 'sfinlog.dashboard.index', 'permission' => 'sfinlog.menu.dashboard_sfinlog'],
                ['route' => 'sfinlog.peminjaman.index', 'permission' => 'sfinlog.menu.pengajuan_peminjaman'],
                ['route' => 'sfinlog.pengajuan-investasi.index', 'permission' => 'sfinlog.menu.pengajuan_investasi'],
                ['route' => 'sfinlog.penyaluran-deposito-sfinlog.index', 'permission' => 'sfinlog.menu.penyaluran_deposito'],
                ['route' => 'sfinlog.pengembalian-pinjaman.index', 'permission' => 'sfinlog.menu.pengembalian_dana'],
                ['route' => 'sfinlog.pengembalian-investasi-sfinlog.index', 'permission' => 'sfinlog.menu.pengembalian_investasi'],
            ],
        ];

        $moduleRoutes = $routes[$module] ?? $routes['sfinance'];

        foreach ($moduleRoutes as $item) {
            // Check if user has permission or is super-admin
            if ($user->can($item['permission']) || $user->hasRole('super-admin')) {
                return redirect()->route($item['route']);
            }
        }

        // If no specific module permission, go to main dashboard
        return redirect()->route('dashboard.index');
    }
}
