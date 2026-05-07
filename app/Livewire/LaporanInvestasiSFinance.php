<?php

namespace App\Livewire;

use App\Exports\LaporanInvestasiSFinanceExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class LaporanInvestasiSFinance extends Component
{
    public $year;
    public $globalSearch = '';
    public $filterStatus = '';

    protected $queryString = [
        'year'         => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount()
    {
        $this->year = $this->year ?: '';
    }

    public function updatedGlobalSearch($value)
    {
        $this->dispatch('globalSearchChanged', $value);
    }

    public function updatedYear($value)
    {
        $this->dispatch('yearChanged', $value);
    }

    public function updatedFilterStatus($value)
    {
        $this->dispatch('statusFilterChanged', $value);
    }

    public function clearFilters()
    {
        $this->globalSearch  = '';
        $this->year          = '';
        $this->filterStatus  = '';
        $this->dispatch('globalSearchChanged', '');
        $this->dispatch('yearChanged', '');
        $this->dispatch('statusFilterChanged', '');
    }

    public function exportExcel()
    {
        $yearLabel = $this->year ?: 'SemuaTahun';
        $statusLabel = $this->filterStatus ? str_replace(' ', '_', $this->filterStatus) : 'SemuaStatus';
        $fileName = 'Laporan_Investasi_SFinance_' . $yearLabel . '_' . $statusLabel . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new LaporanInvestasiSFinanceExport(
                (string) $this->year,
                (string) $this->globalSearch,
                (string) $this->filterStatus
            ),
            $fileName
        );
    }

    public function exportPdf()
    {
        $yearLabel = $this->year ?: 'SemuaTahun';
        $statusLabel = $this->filterStatus ? str_replace(' ', '_', $this->filterStatus) : 'SemuaStatus';
        $fileName = 'Laporan_Investasi_SFinance_' . $yearLabel . '_' . $statusLabel . '_' . now()->format('Ymd_His') . '.pdf';

        $export = new LaporanInvestasiSFinanceExport(
            (string) $this->year,
            (string) $this->globalSearch,
            (string) $this->filterStatus
        );

        $rows = $export->collection();
        $headings = $export->headings();
        $mappedRows = $rows->map(fn($row) => $export->map($row))->all();

        $html = view('exports.laporan-investasi-sfinance-pdf', [
            'headings' => $headings,
            'rows' => $mappedRows,
            'year' => $this->year,
            'filterStatus' => $this->filterStatus,
            'globalSearch' => $this->globalSearch,
        ])->render();

        $mpdf = new Mpdf([
            'tempDir' => storage_path('app/temp'),
            'mode' => 'utf-8',
            'format' => 'A3-L',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->SetTitle('Laporan Investasi SFinance');
        $mpdf->WriteHTML($html);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $tempPath = storage_path('app/temp/' . $fileName);
        $mpdf->Output($tempPath, 'F');

        return response()->download($tempPath, $fileName, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend();
    }

    public function render()
    {
        return view('livewire.laporan-investasi-sfinance.index')
            ->layout('layouts.app', ['title' => 'Laporan Investasi SFinance']);
    }
}
