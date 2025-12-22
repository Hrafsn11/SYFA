<?php

namespace App\Http\Controllers;

use App\Models\ReportPengembalian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportPengembalianController extends Controller
{
    public function exportPdf(Request $request)
    {
        // Ambil semua data report pengembalian (sesuai yang tampil di tabel)
        $items = ReportPengembalian::query()
            ->orderByDesc('id_report_pengembalian')
            ->get();

        $html = view('exports.report-pengembalian-sfinance-pdf', [
            'items' => $items,
        ])->render();

        $fileName = 'Report_Pengembalian_' . now()->format('Ymd') . '.pdf';

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }
}
