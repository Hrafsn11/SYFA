<?php

namespace App\Livewire;

use App\Exports\ArPerbulanExport;
use App\Exports\ArPerbulanPdfExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use ZipArchive;

class ArPerbulan extends Component
{
    public $selectedMonth = '';

    protected $listeners = ['filterByMonth'];

    public function filterByMonth($month)
    {
        $this->selectedMonth = $month;
    }

    public function exportToExcel()
    {
        $fileName = 'AR_Perbulan_' . ($this->selectedMonth ?: 'All') . '_' . now()->format('Y-m-d_His') . '.xlsx';
        
        return Excel::download(
            new ArPerbulanExport($this->selectedMonth),
            $fileName
        );
    }

    public function exportToPdf()
    {
        $pdfExport = new ArPerbulanPdfExport($this->selectedMonth);
        $data = $pdfExport->getData();
        $fileName = $pdfExport->getFileName();
        
        // Render view to HTML
        $html = view('exports.ar-perbulan-pdf', [
            'data' => $data,
            'selectedMonth' => $this->selectedMonth,
        ])->render();
        
        // Generate PDF with mPDF
        $mpdf = new Mpdf([
            'tempDir' => storage_path('logs'),
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);
        
        $mpdf->WriteHTML($html);
        
        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '.pdf"',
        ]);
    }

    public function exportToZip()
    {
        $pdfExport = new ArPerbulanPdfExport($this->selectedMonth);
        $data = $pdfExport->getData();
        $baseName = $pdfExport->getFileName();
        
        // Create temporary directory
        $tempDir = storage_path('app/exports/' . uniqid());
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        try {
            // Generate Excel file
            $excelFile = $tempDir . '/' . $baseName . '.xlsx';
            $excelContent = Excel::raw(
                new ArPerbulanExport($this->selectedMonth),
                \Maatwebsite\Excel\Excel::XLSX
            );
            file_put_contents($excelFile, $excelContent);

            // Generate PDF file
            $pdfFile = $tempDir . '/' . $baseName . '.pdf';
            $html = view('exports.ar-perbulan-pdf', [
                'data' => $data,
                'selectedMonth' => $this->selectedMonth,
            ])->render();

            $mpdf = new Mpdf([
                'tempDir' => storage_path('logs'),
                'mode' => 'utf-8',
                'format' => 'A4-L',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
            ]);
            $mpdf->WriteHTML($html);
            file_put_contents($pdfFile, $mpdf->Output('', 'S'));

            // Create ZIP file
            $zipPath = storage_path('app/exports/' . $baseName . '.zip');
            $zip = new ZipArchive();
            
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $zip->addFile($excelFile, basename($excelFile));
                $zip->addFile($pdfFile, basename($pdfFile));
                $zip->close();

                // Cleanup temporary files
                @unlink($excelFile);
                @unlink($pdfFile);
                @rmdir($tempDir);

                // Download ZIP
                return response()->download($zipPath, $baseName . '.zip', [
                    'Content-Type' => 'application/zip',
                ])->deleteFileAfterSend();
            }
        } catch (\Exception $e) {
            // Cleanup on error
            @array_map('unlink', glob($tempDir . '/*'));
            @rmdir($tempDir);
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.ar-perbulan.index');
    }
}
