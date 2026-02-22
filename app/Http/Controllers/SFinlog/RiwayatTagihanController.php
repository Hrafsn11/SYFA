<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiwayatTagihanController extends Controller
{
    /**
     * Get histori pembayaran for SFinlog
     */
    public function getHistoriPembayaran(Request $request): JsonResponse
    {
        // TODO: Implementasi logika histori pembayaran khusus SFinlog
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }

    /**
     * Get summary data for SFinlog
     */
    public function getSummaryData(Request $request): JsonResponse
    {
        // TODO: Implementasi logika summary data khusus SFinlog
        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}

