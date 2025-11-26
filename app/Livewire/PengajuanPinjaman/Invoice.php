<?php

namespace App\Livewire\PengajuanPinjaman;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Traits\HasModal;
use App\Attributes\ParameterIDRoute;
use App\Livewire\Traits\HasValidate;
use App\Livewire\PengajuanPinjaman\Create;
use App\Http\Requests\InvoicePengajuanPinjamanRequest;
use App\Livewire\PengajuanPinjaman\Event\HandleInvoiceEvents;
use App\Livewire\PengajuanPinjaman\FieldsInput\FieldInputInvoice;

class Invoice extends Component
{
    use HasValidate, 
        HasModal, 
        WithFileUploads, 
        HandleInvoiceEvents, 
        FieldInputInvoice;
    private string $validateClass = InvoicePengajuanPinjamanRequest::class;

    #[ParameterIDRoute]
    public $index_data_invoice;
    
    public $jenis_pembiayaan, $pengajuan, $sumber_pembiayaan, $id_instansi;

    public $form_data_invoice,
        $invoice_financing_data, 
        $po_financing_data, 
        $installment_data, 
        $factoring_data, 
        $modal_title = 'Tambah Invoice', 
        $nilai_bagi_hasil,
        $persentase_bagi_hasil = 0;


    public function mount($jenis_pembiayaan = null, $pengajuan = null, $sumber_pembiayaan = null, $id_instansi = null)
    {
        $this->jenis_pembiayaan = $jenis_pembiayaan;
        $this->pengajuan = $pengajuan;
        $this->sumber_pembiayaan = $sumber_pembiayaan;
        $this->id_instansi = $id_instansi;

        $this->form_data_invoice = [];
    }
    
    public function render()
    {
        $this->prepareFormData();
        $this->prepareFormInvoice();

        if ($this->pengajuan !== null) $this->edit();
        return view('livewire.pengajuan-pinjaman.components.table_create');
    }

    public function saveDataInvoice()
    {
        $this->validate();
        
        $formData = $this->prepareFormDataByJenisPembiayaan();
        if ($this->index_data_invoice !== null) {
            $this->updateInvoiceData($formData);
        } else {
            $this->form_data_invoice[] = $formData;
        }
        $this->dispatch('invoice-saved', []);
        $this->emitTotalsUpdated();
    }

    private function updateInvoiceData($formData)
    {
        if ($this->index_data_invoice !== null) {
            foreach ([
                'dokumen_invoice_file', 
                'dokumen_kontrak_file', 
                'dokumen_so_file', 
                'dokumen_bast_file', 
                'dokumen_lainnnya_file'
            ] as $dokumen) {
                if (array_key_exists($dokumen, $formData) && is_null($formData[$dokumen])) {
                    $formData[$dokumen] = $this->form_data_invoice[$this->index_data_invoice][$dokumen];
                }
            }

            $this->form_data_invoice[$this->index_data_invoice] = $formData;
            $this->index_data_invoice = null;
        }
    }

    /**
     * Mengirim total pinjaman ke parent component.
     */
    private function emitTotalsUpdated(): void
    {
        $data = collect($this->form_data_invoice ?? []);

        $totalPinjaman = $data->sum(fn ($item) => (float) ($item['nilai_pinjaman'] ?? 0));
        $totalBagiHasil = $data->sum(function ($item) {
            $persentase = (float) ($this->persentase_bagi_hasil ?? 0);
            $nilaiPinjaman = (float) ($item['nilai_pinjaman'] ?? 0);

            return $nilaiPinjaman * $persentase;
        });

        $this->dispatch(
            'invoiceTotalsUpdated',
            totalPinjaman: $totalPinjaman,
            totalBagiHasil: $totalBagiHasil,
            formDataInvoice: $this->form_data_invoice ?? []
        )->to(Create::class);
    }

    public function editInvoice($idx)
    {
        $data = [];

        $this->index_data_invoice = $idx;
        foreach ($this->form_data_invoice[$idx] as $key => $value) {
            if (in_array($key, [
                'dokumen_invoice_file', 
                'dokumen_kontrak_file', 
                'dokumen_so_file', 
                'dokumen_bast_file', 
                'dokumen_lainnnya_file'
            ])) continue;

            if (in_array($key, ['nilai_invoice', 'nilai_pinjaman', 'nilai_bagi_hasil'])) {
                $this->{$key} = rupiahFormatter($value);
            } else if (in_array($key, ['invoice_date', 'due_date'])) {
                $this->{$key} = parseCarbonDate($value)->format('d/m/Y');
                $data[$key] = $this->{$key};
            } else {
                $this->{$key} = $value;
            }

        }
        $this->dispatch('edit-invoice', $data);
    }

    
}
