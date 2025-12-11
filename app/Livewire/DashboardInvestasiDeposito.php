<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardInvestasiDeposito extends Component
{
    public $selectedMonth = null;

    public function mount()
    {
        // Set default bulan ke bulan saat ini
        $this->selectedMonth = date('m');
    }

    public function updatedSelectedMonth()
    {
        // Method ini akan dipanggil ketika bulan berubah
        // Bisa digunakan untuk refresh data chart
    }

    public function render()
    {
        // Data untuk summary cards
        $summaryData = [
            'total_deposito_pokok' => 126500,
            'total_deposito_pokok_percent' => 34.7,
            'total_deposito_pokok_period' => 'Jan 2025',
            
            'total_cof' => 126500,
            'total_cof_percent' => 8,
            'total_cof_period' => 'Oct 2023',
            
            'total_pengembalian' => 126500,
            'total_pengembalian_percent' => 34.7,
            'total_pengembalian_period' => 'Oct 2023',
            
            'total_outstanding' => 126500,
            'total_outstanding_percent' => 5,
            'total_outstanding_period' => 'Oct 2023',
        ];

        // Data untuk chart Total Deposito Pokok yang masuk Per Bulan
        $chartDepositoPokok = [
            'categories' => ['0', 'Techno', 'Proxsis', 'Malaka', 'Hukum', 'Kredit'],
            'series' => [
                [
                    'name' => 'Pokok',
                    'data' => [0, 195000000, 188000000, 115000000, 85000000, 20000000]
                ]
            ]
        ];

        // Data untuk chart Total CoF per bulan
        $chartCoF = [
            'categories' => ['0', 'Techno', 'Proxsis', 'Malaka', 'Hukum', 'Kredit'],
            'series' => [
                [
                    'name' => 'Pokok',
                    'data' => [0, 195000000, 185000000, 115000000, 85000000, 20000000]
                ]
            ]
        ];

        // Data untuk chart Total Pengembalian Pokok dan Bagi Hasil Perbulan
        // NOTE: Chart row 2 TIDAK memiliki "0" di X-axis sesuai gambar
        $chartPengembalian = [
            'categories' => ['Techno', 'Proxsis', 'Malaka', 'Hukum', 'Kredit'],
            'series' => [
                [
                    'name' => 'Pokok',
                    'data' => [200000000, 175000000, 115000000, 95000000, 25000000]
                ],
                [
                    'name' => 'Bagi Hasil',
                    'data' => [150000000, 120000000, 150000000, 125000000, 95000000]
                ]
            ]
        ];

        // Data untuk chart Total Sisa Deposito Pokok dan CoF yang Belum Dikembalikan
        // NOTE: Chart row 2 TIDAK memiliki "0" di X-axis sesuai gambar
        $chartSisaDeposito = [
            'categories' => ['Techno', 'Proxsis', 'Malaka', 'Hukum', 'Kredit'],
            'series' => [
                [
                    'name' => 'Pokok',
                    'data' => [190000000, 180000000, 115000000, 90000000, 25000000]
                ],
                [
                    'name' => 'Bagi Hasil',
                    'data' => [145000000, 115000000, 145000000, 120000000, 90000000]
                ]
            ]
        ];

        return view('livewire.dashboard-investasi-deposito', compact(
            'summaryData',
            'chartDepositoPokok',
            'chartCoF',
            'chartPengembalian',
            'chartSisaDeposito'
        ))->layout('layouts.app');
    }
}

