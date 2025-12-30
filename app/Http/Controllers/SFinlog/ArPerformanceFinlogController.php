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

    public function exportPDF(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan', null);

        // Normalize bulan: convert empty string to null
        if ($bulan === '' || $bulan === '0') {
            $bulan = null;
        }

        // Get fresh data based on filters using SFinlog service
        $arData = $this->arService->getArPerformanceData($tahun, $bulan, false);

        // Prepare bulan name
        $bulanNama = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Build title
        $title = 'Daftar AR Performance - SFinlog';
        if ($bulan) {
            $title .= ' - ' . ($bulanNama[$bulan] ?? $bulan);
        }
        $title .= ' Tahun ' . $tahun;

        // Generate filename
        $filename = 'AR_Performance_SFinlog';
        if ($bulan) {
            $filename .= '_' . ($bulanNama[$bulan] ?? $bulan);
        }
        $filename .= '_' . $tahun . '.pdf';

        // Generate PDF
        $pdf = Pdf::loadView('livewire.sfinlog.ar-performance.export-pdf', [
            'arData' => $arData,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'bulanNama' => $bulanNama,
            'title' => $title
        ]);

        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }
}
