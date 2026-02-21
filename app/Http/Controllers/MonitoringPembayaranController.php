<?php

namespace App\Http\Controllers;

use App\Services\ArPerformanceService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MonitoringPembayaranController extends Controller
{
    protected $monitoringService;

    public function __construct(ArPerformanceService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $monitoringData = $this->monitoringService->getArPerformanceData($tahun);

        // Expecting view rename later
        return view('livewire.monitoring-pembayaran.index', [
            'monitoringData' => $monitoringData,
            'tahun' => $tahun ?? date('Y'),
        ]);
    }

    public function getTransactions(Request $request)
    {
        $debiturId = $request->input('debitur_id');
        $category = $request->input('category');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $transactions = $this->monitoringService->getTransactionsByCategory($debiturId, $category, $tahun, $bulan);

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'category_label' => $this->monitoringService->getCategoryLabel($category),
        ]);
    }

    public function clearCache(Request $request)
    {
        $tahun = $request->input('tahun');
        $this->monitoringService->clearCache($tahun);

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
        $monitoringData = $this->monitoringService->getArPerformanceData($tahun, $bulan, false);

        // Prepare bulan name
        $bulanNama = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        // Build title
        $title = 'Monitoring Pembayaran';
        if ($bulan) {
            $title .= ' - ' . ($bulanNama[$bulan] ?? $bulan);
        }
        $title .= ' Tahun ' . $tahun;

        // Generate filename
        $filename = 'Monitoring_Pembayaran';
        if ($bulan) {
            $filename .= '_' . ($bulanNama[$bulan] ?? $bulan);
        }
        $filename .= '_' . $tahun . '_' .'.pdf';

        // Generate PDF
        // Expecting view rename
        $pdf = Pdf::loadView('livewire.monitoring-pembayaran.export-pdf', [
            'monitoringData' => $monitoringData,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'bulanNama' => $bulanNama,
            'title' => $title
        ]);

        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }
}
