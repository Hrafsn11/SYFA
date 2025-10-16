<?php

namespace App\Livewire\MasterDataDebiturInvestor;

use Livewire\Component;

class DebiturInvestorIndex extends Component
{
    public $kol;
    public $data;

    public $nama_perusahaan, $flagging = 'tidak', $nama_ceo, $alamat_perusahaan, $email, $kol_perusahaan, $nama_bank, $no_rek;

    // Properti untuk kontrol modal dan mode
    public $showModal = false;
    public $isEditMode = false;
    public $editDataId;

    public function mount()
    {
        $this->data = [
            [
                'id' => 1,
                'nama_perusahaan' => 'Techno Infinity',
                'Flagging' => 'Investor',
                'nama_ceo' => 'Cahyo',
                'alamat_perusahaan' => 'Bandung',
                'email' => 'Techno@gmail.com',
                'kol_perusahaan' => 1,
                'nama_bank' => 'BCA',
                'no_rek' => 12345678,
            ],
            [
                'id' => 2,
                'nama_perusahaan' => 'Malaka',
                'Flagging' => '-',
                'nama_ceo' => 'Budi',
                'alamat_perusahaan' => 'Jakarta',
                'email' => 'Malaka@gmail.com',
                'kol_perusahaan' => 2,
                'nama_bank' => 'BRI',
                'no_rek' => 12345678,
            ],
        ];

        $this->kol = [
            ['id' => 1, 'kol' => '1'],
            ['id' => 2, 'kol' => '2'],
            ['id' => 3, 'kol' => '3'],
            ['id' => 4, 'kol' => '4'],
            ['id' => 5, 'kol' => '5'],
        ];
    }

    private function resetInputFields()
    {
        $this->nama_perusahaan = '';
        $this->flagging = 'investor';
        $this->nama_ceo = '';
        $this->alamat_perusahaan = '';
        $this->email = '';
        $this->kol_perusahaan = '';
        $this->nama_bank = '';
        $this->no_rek = '';
        $this->editDataId = null;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->showModal = true;
        $this->dispatch('init-select2');
    }

    public function edit($id)
    {
        $this->editDataId = $id;
        // Cari data berdasarkan ID. Karena kita pakai array, kita gunakan firstWhere.
        $item = collect($this->data)->firstWhere('id', $id);

        if ($item) {
            $this->nama_perusahaan = $item['nama_perusahaan'];
            $this->flagging = strtolower($item['Flagging']) == 'investor' ? 'ya' : 'tidak';
            $this->nama_ceo = $item['nama_ceo'];
            $this->alamat_perusahaan = $item['alamat_perusahaan'];
            $this->email = $item['email'];
            $this->kol_perusahaan = $item['kol_perusahaan'];
            $this->nama_bank = $item['nama_bank'];
            $this->no_rek = $item['no_rek'];

            $this->isEditMode = true;
            $this->showModal = true;
            $this->dispatch('init-select2');
        }
    }

    public function save()
    {
        $this->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'flagging' => 'required|string',
            'nama_ceo' => 'required|string|max:255',
            'alamat_perusahaan' => 'required|string',
            'email' => 'required|email',
            'kol_perusahaan' => 'required',
            'nama_bank' => 'required|string|max:255',
            'no_rek' => 'required|numeric',
        ]);

        $newData = [
            'nama_perusahaan' => $this->nama_perusahaan,
            'Flagging' => $this->flagging === 'ya' ? 'Investor' : '-',
            'nama_ceo' => $this->nama_ceo,
            'alamat_perusahaan' => $this->alamat_perusahaan,
            'email' => $this->email,
            'kol_perusahaan' => $this->kol_perusahaan,
            'nama_bank' => $this->nama_bank,
            'no_rek' => $this->no_rek,
        ];

        if ($this->isEditMode) {
            $index = collect($this->data)->search(function ($item) {
                return $item['id'] == $this->editDataId;
            });

            if ($index !== false) {
                $this->data[$index] = array_merge(['id' => $this->editDataId], $newData);
            }
        } else {
            $newId = count($this->data) > 0 ? max(array_column($this->data, 'id')) + 1 : 1;
            $this->data[] = array_merge(['id' => $newId], $newData);
        }

        $this->closeModal();
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.debitur-investor.index')
            ->layout('layouts.app');
    }
}