<?php

namespace App\Http\Controllers;

use App\Models\LaporanPengembalian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanPengembalianController extends Controller
{
    public function exportPdf(Request $request)
    {
        // Ambil semua data laporan pengembalian (sesuai yang tampil di tabel)
        $items = LaporanPengembalian::query()
            ->orderByDesc('id_report_pengembalian')
            ->get();

        $html = view('exports.laporan-pengembalian-sfinance-pdf', [
            'items' => $items,
        ])->render();

        $fileName = 'Laporan_Pengembalian_' . now()->format('Ymd') . '.pdf';

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');

        return $pdf->download($fileName);
    }
}
