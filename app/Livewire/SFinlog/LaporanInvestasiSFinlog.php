<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use App\Models\PengajuanInvestasiFinlog;

class LaporanInvestasiSFinlog extends Component
{
    public $year;

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

    // Field configurations for SFinlog
    protected $editableFields = [
        'tanggal_investasi' => ['label' => 'Tanggal Investasi', 'type' => 'date'],
        'nama_investor' => ['label' => 'Nama Investor', 'type' => 'text'],
        'nominal_investasi' => ['label' => 'Nominal Investasi', 'type' => 'number'],
        'lama_investasi' => ['label' => 'Lama Investasi (Bulan)', 'type' => 'number'],
        'persentase_bunga' => ['label' => 'Bunga (%PA)', 'type' => 'number'],
        'nominal_bunga_yang_didapat' => ['label' => 'Bunga Nominal', 'type' => 'number'],
        'status' => ['label' => 'Status', 'type' => 'select'],
        'sisa_pokok' => ['label' => 'Sisa Pokok', 'type' => 'number'],
        'sisa_bunga' => ['label' => 'Sisa Bunga', 'type' => 'number'],
    ];

    public function mount()
    {
        $this->year = $this->year ?: date('Y');
    }

    public function openEditModal($id, $field)
    {
        $investasi = PengajuanInvestasiFinlog::find($id);

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
        $investasi = PengajuanInvestasiFinlog::find($this->editId);

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

        // Auto-recalculate tanggal_berakhir_investasi when lama_investasi or tanggal_investasi changes
        if (in_array($this->editField, ['lama_investasi', 'tanggal_investasi'])) {
            $tanggalInvestasi = \Carbon\Carbon::parse($investasi->tanggal_investasi);
            $lamaInvestasi = (int) $investasi->lama_investasi;
            $investasi->tanggal_berakhir_investasi = $tanggalInvestasi->copy()->addMonths($lamaInvestasi);
        }

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
        return view('livewire.sfinlog.laporan-investasi-sfinlog.index')
            ->layout('layouts.app', ['title' => 'Laporan Investasi SFinlog']);
    }
}
