<?php

namespace App\Http\Controllers;

use App\Services\ArPerformanceService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MonitoringPembayaranController extends Controller
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

        return view('livewire.monitoring-pembayaran.index', [
            'arData' => $arData,
            'tahun' => $tahun ?? date('Y'),
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
        $this->arService->clearCache($tahun);

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }

    public function exportPDF(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');

        // Get fresh data based on filters
        $arData = $this->arService->getArPerformanceData($tahun, $bulan, false);

        // Prepare bulan name
        $bulanNama = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Build title
        $title = 'Daftar AR Performance';
        if ($bulan) {
            $title .= ' - ' . ($bulanNama[$bulan] ?? $bulan);
        }
        $title .= ' Tahun ' . $tahun;

        // Generate filename
        $filename = 'AR_Performance';
        if ($bulan) {
            $filename .= '_' . ($bulanNama[$bulan] ?? $bulan);
        }
        $filename .= '_' . $tahun . '_' .'.pdf';

        // Generate PDF
        $pdf = Pdf::loadView('livewire.monitoring-pembayaran.export-pdf', [
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
