<?php

namespace App\Livewire;

use Livewire\Component;

class ConfigMatrixPinjaman extends Component
{
    public $data;

    public function mount()
    {
        $this->data = [
            [
                'id' => 1,
                'nominal' => '10.000.000',
                'approve_oleh' => 'Manager',
            ],
            [
                'id' => 2,
                'nominal' => '20.000.000',
                'approve_oleh' => 'CEO',
            ],
            [
                'id' => 3,
                'nominal' => '50.000.000',
                'approve_oleh' => 'Direktur',
            ],
            [
                'id' => 4,
                'nominal' => '100.000.000',
                'approve_oleh' => 'HR',
            ],
            [
                'id' => 5,
                'nominal' => '200.000.000',
                'approve_oleh' => 'Board',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.config-matrix-pinjaman.index')
        ->layout('layouts.app');
    }
}
