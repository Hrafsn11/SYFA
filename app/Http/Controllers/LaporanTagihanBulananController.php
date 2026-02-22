<?php

namespace App\Http\Controllers;

use App\Livewire\ArPerbulan;
use App\Services\ArPerbulanService;
use App\Helpers\Response;
use Illuminate\Http\Request;

class LaporanTagihanBulananController extends Controller
{
    public function index()
    {
        return ArPerbulan::class;
    }

    /**
     * Manual update AR untuk debitur tertentu
     */
    public function updateAR(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_debitur' => 'required|exists:master_debitur_dan_investor,id_debitur',
                'bulan' => 'required|date_format:Y-m',
            ]);

            $arPerbulan = app(ArPerbulanService::class)->updateOrCreateAR(
                $validated['id_debitur'],
                $validated['bulan']
            );

            if (!$arPerbulan) {
                return Response::error('Gagal update AR Perbulan', 400);
            }

            return Response::success($arPerbulan, 'AR Perbulan berhasil diupdate');

        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }
}
