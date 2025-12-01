<?php

namespace App\Http\Controllers;

use App\Services\ArPerformanceService;
use Illuminate\Http\Request;

class ArPerformanceController extends Controller
{
    protected $arService;

    public function __construct(ArPerformanceService $arService)
    {
        $this->arService = $arService;
    }

    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $arData = $this->arService->getArPerformanceData($tahun);

        return view('livewire.ar-performance.index', [
            'arData' => $arData,
            'tahun' => $tahun ?? date('Y'),
        ]);
    }

    public function getTransactions(Request $request)
    {
        $debiturId = $request->input('debitur_id');
        $category = $request->input('category');
        $tahun = $request->input('tahun');

        $transactions = $this->arService->getTransactionsByCategory($debiturId, $category, $tahun);

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'category_label' => $this->arService->getCategoryLabel($category),
        ]);
    }

    public function clearCache(Request $request)
    {
        $tahun = $request->input('tahun');
        $this->arService->clearCache($tahun);

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }
}
