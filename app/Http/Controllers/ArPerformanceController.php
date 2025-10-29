<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArPerformanceController extends Controller
{
    public function index()
    {
        return view('livewire.ar-performance.index');
    }

    public function getTransactions(Request $request)
    {
        $debitur = $request->input('debitur');
        $category = $request->input('category');
        
        $transactions = $this->getDummyTransactions($category);
        
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
    
    private function getDummyTransactions($category)
    {
        $data = [
            'Belum Jatuh Tempo' => [
                ['nomor_kontrak' => 'KTR-2024-001', 'no_invoice' => 'INV-001', 'nilai_invoice' => 2000000],
                ['nomor_kontrak' => 'KTR-2024-002', 'no_invoice' => 'INV-002', 'nilai_invoice' => 2000000],
                ['nomor_kontrak' => 'KTR-2024-003', 'no_invoice' => 'INV-003', 'nilai_invoice' => 2000000],
                ['nomor_kontrak' => 'KTR-2024-004', 'no_invoice' => 'INV-004', 'nilai_invoice' => 2000000],
                ['nomor_kontrak' => 'KTR-2024-005', 'no_invoice' => 'INV-005', 'nilai_invoice' => 2000000],
            ],
            'DEL (1 - 30)' => [
                ['nomor_kontrak' => 'KTR-2024-006', 'no_invoice' => 'INV-006', 'nilai_invoice' => 2000000],
            ],
            'DEL (31 - 60)' => [
                ['nomor_kontrak' => 'KTR-2024-007', 'no_invoice' => 'INV-007', 'nilai_invoice' => 1500000],
            ],
            'DEL (61 - 90)' => [
                ['nomor_kontrak' => 'KTR-2024-008', 'no_invoice' => 'INV-008', 'nilai_invoice' => 1000000],
            ],
            'NPL (91 - 179)' => [
                ['nomor_kontrak' => 'KTR-2024-009', 'no_invoice' => 'INV-009', 'nilai_invoice' => 500000],
            ],
            'WriteOff (>180)' => [
                ['nomor_kontrak' => 'KTR-2024-010', 'no_invoice' => 'INV-010', 'nilai_invoice' => 200000],
            ],
        ];
        
        return $data[$category] ?? [];
    }

    public function show()
    {

    }
}
