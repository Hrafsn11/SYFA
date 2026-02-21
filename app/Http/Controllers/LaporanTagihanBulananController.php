<?php

namespace App\Http\Controllers;

use App\Livewire\LaporanTagihanBulanan;
use App\Services\ArPerbulanService;
use App\Helpers\Response;
use Illuminate\Http\Request;

class LaporanTagihanBulananController extends Controller
{
    public function index()
    {
        return LaporanTagihanBulanan::class;
    }

    /**
     * Manual update Laporan Tagihan Bulanan untuk debitur tertentu
     */
    public function updateLaporanTagihan(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_debitur' => 'required|exists:master_debitur_dan_investor,id_debitur',
                'bulan' => 'required|date_format:Y-m',
            ]);

            // Assuming service method name remains same for now, or update if service is refactored
            $laporan = app(ArPerbulanService::class)->updateOrCreateAR(
                $validated['id_debitur'],
                $validated['bulan']
            );

            if (!$laporan) {
                return Response::error('Gagal update Laporan Tagihan Bulanan', 400);
            }

            return Response::success($laporan, 'Laporan Tagihan Bulanan berhasil diupdate');

        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }
}
