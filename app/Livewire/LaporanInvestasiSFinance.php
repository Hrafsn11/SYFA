<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PengajuanInvestasi;

class LaporanInvestasiSFinance extends Component
{
    public $year;
    public $perPage = 10;
    public $search = '';
    public $globalSearch = '';

    // Edit modal properties
    public $showEditModal = false;
    public $editId = null;
    public $editField = null;
    public $editFieldLabel = null;
    public $editValue = null;
    public $editFieldType = 'text'; // text, number, date, select

    protected $queryString = [
        'year' => ['except' => ''],
    ];

    protected $listeners = ['openEditModal'];

    // Field configurations
    protected $editableFields = [
        'tanggal_investasi' => ['label' => 'Tanggal Investasi', 'type' => 'date'],
        'deposito' => ['label' => 'Deposito', 'type' => 'text'],
        'nama_investor' => ['label' => 'Nama Investor', 'type' => 'text'],
        'jumlah_investasi' => ['label' => 'Jumlah Investasi', 'type' => 'number'],
        'lama_investasi' => ['label' => 'Lama Investasi (Bulan)', 'type' => 'number'],
        'bunga_pertahun' => ['label' => 'Bunga (%PA)', 'type' => 'number'],
        'nominal_bunga_yang_didapatkan' => ['label' => 'Bunga Nominal', 'type' => 'number'],
        'status' => ['label' => 'Status', 'type' => 'select'],
        'sisa_pokok' => ['label' => 'Sisa Pokok', 'type' => 'number'],
        'sisa_bunga' => ['label' => 'Sisa Bunga', 'type' => 'number'],
    ];

    public function mount()
    {
        $this->year = $this->year ?: date('Y');
    }

    public function applyFilter()
    {
        $this->dispatch('yearChanged', $this->year);
    }

    public function updatedGlobalSearch($value)
    {
        $this->dispatch('globalSearchChanged', $value);
    }

    public function updatedYear($value)
    {
        $this->dispatch('yearChanged', $value);
    }

    public function openEditModal($id, $field)
    {
        $investasi = PengajuanInvestasi::find($id);

        if (!$investasi || !isset($this->editableFields[$field])) {
            return;
        }

        $this->editId = $id;
        $this->editField = $field;
        $this->editFieldLabel = $this->editableFields[$field]['label'];
        $this->editFieldType = $this->editableFields[$field]['type'];
        $this->editValue = $investasi->{$field};
        $this->showEditModal = true;
    }

    public function saveEdit()
    {
        $investasi = PengajuanInvestasi::find($this->editId);

        if (!$investasi || !$this->editField) {
            $this->showEditModal = false;
            return;
        }

        // Validate based on field type
        $rules = ['editValue' => 'required'];

        if ($this->editFieldType === 'number') {
            $rules['editValue'] = 'required|numeric|min:0';
        } elseif ($this->editFieldType === 'date') {
            $rules['editValue'] = 'required|date';
        }

        $this->validate($rules);

        // Update the field
        $investasi->{$this->editField} = $this->editValue;
        $investasi->save();

        // Close modal and refresh tables
        $this->showEditModal = false;
        $this->editId = null;
        $this->editField = null;
        $this->editValue = null;

        // Dispatch event to refresh all tables
        $this->dispatch('refreshLaporanInvestasiTable');

        // Show success message
        session()->flash('message', 'Data berhasil diupdate!');
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editId = null;
        $this->editField = null;
        $this->editValue = null;
    }

    public function render()
    {
        return view('livewire.laporan-investasi-sfinance.index')
            ->layout('layouts.app', ['title' => 'Laporan Investasi SFinance']);
    }
}
