<?php

namespace App\Livewire\MasterDataKol;

use Livewire\Component;

class MasterDataKolIndex extends Component
{
    public $data;

    public function mount()
    {
        $this->data = [
            [
                'id' => 1,
                'kol' => '1',
                'persentase_keterlambatan' => '0-30%',
                'tanggal_tenggat' => '0 Hari',
            ],
            [
                'id' => 2,
                'kol' => '2',
                'persentase_keterlambatan' => '31-60%',
                'tanggal_tenggat' => '1-29 Hari',
            ],
            [
                'id' => 3,
                'kol' => '3',
                'persentase_keterlambatan' => '61-90%',
                'tanggal_tenggat' => '30–59 Hari',
            ],
            [
                'id' => 4,
                'kol' => '4',
                'persentase_keterlambatan' => '>90%',
                'tanggal_tenggat' => '60–179 Hari',
            ],
            [
                'id' => 5,
                'kol' => '5',
                'persentase_keterlambatan' => '>180%',
                'tanggal_tenggat' => '≥180 Hari',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.master-data-kol.index')
            ->layout('layouts.app');
    }
}
