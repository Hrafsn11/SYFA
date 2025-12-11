<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardPembiayaanSfinance extends Component
{
    // Filter properties
    public $bulan;
    public $tahun;
    public $bulan1; // untuk comparison chart
    public $bulan2; // untuk comparison chart

    public function mount()
    {
        $this->tahun = date('Y');
        $this->bulan = date('m');
        $this->bulan1 = date('m');
        $this->bulan2 = date('m', strtotime('-2 months'));
    }

    public function render()
    {
        // Dummy data untuk frontend (nanti akan diganti dengan data real dari backend)
        $summaryData = [
            'total_disbursement' => 126500,
            'total_pembayaran_masuk' => 126500,
            'total_sisa_belum_terbayar' => 126500,
            'total_outstanding_piutang' => 126500,
        ];

        $chartData = [
            'disbursement' => $this->getDisbursementData(),
            'pembayaran' => $this->getPembayaranData(),
            'sisa_belum_terbayar' => $this->getSisaBelumTerbayarData(),
            'pembayaran_piutang_tahun' => $this->getPembayaranPiutangTahunData(),
            'comparison' => $this->getComparisonData(),
        ];

        $arTableData = $this->getArTableData();

        return view('livewire.dashboard-pembiayaan-sfinance', [
            'summaryData' => $summaryData,
            'chartData' => $chartData,
            'arTableData' => $arTableData,
        ])->layout('layouts.app', [
            'title' => 'Dashboard Pembiayaan Sfinance'
        ]);
    }

    // Dummy data methods - akan diganti dengan data real dari service/model
    private function getDisbursementData()
    {
        return [
            'categories' => ['Tahun', 'Proses', 'Mudaha', 'Hukum', 'Kredit'],
            'pokok' => [188000000, 150000000, 120000000, 100000000, 80000000],
            'bagi_hasil' => [150000000, 120000000, 100000000, 110000000, 70000000],
        ];
    }

    private function getPembayaranData()
    {
        return [
            'categories' => ['Tahun', 'Proses', 'Mudaha', 'Hukum', 'Kredit'],
            'pokok' => [180000000, 145000000, 115000000, 95000000, 75000000],
            'bagi_hasil' => [145000000, 115000000, 95000000, 105000000, 65000000],
        ];
    }

    private function getSisaBelumTerbayarData()
    {
        return [
            'categories' => ['Tahun', 'Proses', 'Mudaha', 'Hukum', 'Kredit'],
            'pokok' => [80000000, 50000000, 50000000, 50000000, 50000000],
            'bagi_hasil' => [50000000, 50000000, 50000000, 50000000, 50000000],
        ];
    }

    private function getPembayaranPiutangTahunData()
    {
        return [
            'categories' => ['Tahun', 'Proses', 'Mudaha', 'Hukum', 'Kredit'],
            'pokok' => [190000000, 155000000, 125000000, 105000000, 85000000],
        ];
    }

    private function getComparisonData()
    {
        return [
            'categories' => ['Tahun', 'Proses', 'Mudaha', 'Hukum', 'Kredit'],
            'bulan1' => [190000000, 155000000, 125000000, 105000000, 85000000],
            'bulan2' => [150000000, 120000000, 100000000, 95000000, 75000000],
            'selisih' => [40000000, 35000000, 25000000, 10000000, 10000000],
        ];
    }

    private function getArTableData()
    {
        return [
            [
                'debitur' => 'Ada wong sugih',
                'del_1_30' => 55000000,
                'del_31_60' => 0,
                'del_61_90' => 0,
                'npl_91_179' => 0,
                'write_off' => 0,
            ],
            [
                'debitur' => 'Super Admin',
                'del_1_30' => 10000000,
                'del_31_60' => 0,
                'del_61_90' => 0,
                'npl_91_179' => 0,
                'write_off' => 0,
            ],
        ];
    }
}

