<?php

namespace App\Http\Controllers\SFinlog;

use App\Http\Controllers\Controller;
use App\Services\ArPerformanceFinlogService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ArPerformanceFinlogController extends Controller
{
    protected $arService;

    public function __construct(ArPerformanceFinlogService $arService)
    {
        $this->arService = $arService;
    }

    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $arData = $this->arService->getArPerformanceData($tahun, $bulan);

        return view('livewire.sfinlog.ar-performance.index', [
            'arData' => $arData,
            'tahun' => $tahun ?? date('Y'),
            'bulan' => $bulan,
        ]);
    }

    public function getTransactions(Request $request)
    {
        $debiturId = $request->input('debitur_id');
        $category = $request->input('category');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $transactions = $this->arService->getTransactionsByCategory($debiturId, $category, $tahun, $bulan);

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'category_label' => $this->arService->getCategoryLabel($category),
        ]);
    }

    public function clearCache(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $this->arService->clearCache($tahun, $bulan);

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }
}
