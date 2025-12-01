<?php

namespace App\Http\Controllers;

use App\Services\DebiturPiutangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebiturPiutangController extends Controller
{
    public function __construct(
        private readonly DebiturPiutangService $service
    ) {}

    public function getHistoriPembayaran(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_pengembalian' => 'required|string',
            'period' => 'nullable|string|date_format:Y-m',
        ]);

        $histori = $this->service->getHistoriPembayaran(
            $validated['id_pengembalian'],
            $validated['period'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $histori,
        ]);
    }

    public function getSummaryData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_pengembalian' => 'required|string',
        ]);

        $summary = $this->service->getSummaryData($validated['id_pengembalian']);

        if (!$summary) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
