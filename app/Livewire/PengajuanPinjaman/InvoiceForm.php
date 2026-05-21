<?php

namespace App\Livewire\PengajuanPinjaman;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Traits\HasModal;
use App\Attributes\ParameterIDRoute;
use App\Livewire\Traits\HasValidate;
use App\Http\Requests\InvoicePengajuanPinjamanRequest;
use App\Livewire\PengajuanPinjaman\Event\HandleInvoiceEvents;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Livewire\PengajuanPinjaman\FieldsInput\FieldInputInvoice;

class InvoiceForm extends Component
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
        $modal_title = 'Tambah Invoice', 
        $dokumen_invoice_current = null,
        $dokumen_kontrak_current = null,
        $dokumen_so_current = null,
        $dokumen_bast_current = null,
        $dokumen_lainnya_current = null,
        $nilai_bunga,
        $persentase_bunga = 0;

    public function mount(): void
    {
        $this->prepareFormData();
        $this->prepareFormInvoice();

        if ($this->pengajuan !== null) {
            // Isi form_data_invoice dari data DB agar sinkron dengan parent
            $this->form_data_invoice = $this->pengajuan->buktiPeminjaman->map(function ($item) {
                return collect($item->toArray())->only([
                    'no_invoice', 'no_kontrak', 'nama_client',
                    'nilai_invoice', 'nilai_pinjaman', 'nilai_bunga',
                    'invoice_date', 'due_date', 'kontrak_date',
                    'dokumen_invoice', 'dokumen_kontrak', 'dokumen_so',
                    'dokumen_bast', 'dokumen_lainnya', 'nama_barang',
                ])->all();
            })->values()->toArray();
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pengajuan-pinjaman.components.modal_create');
    }

    protected function setAdditionalValidationData(): array
    {
        return [
            'jenis_pembiayaan'   => $this->jenis_pembiayaan,
            'form_data_invoice'  => $this->form_data_invoice ?? [],
            'index_data_invoice' => $this->index_data_invoice,
        ];
    }

    public function saveDataInvoice()
    {
        // Bangun rules dengan exclude IDs jika pengajuan sudah ada di DB
        $excludeIds = [];
        if ($this->pengajuan !== null) {
            $excludeIds = \App\Models\BuktiPeminjaman::where('id_pengajuan_peminjaman', $this->pengajuan->id_pengajuan_peminjaman)
                ->pluck('id_bukti_peminjaman')
                ->toArray();
        }

        $invoiceRequest = new \App\Http\Requests\InvoicePengajuanPinjamanRequest();
        $rules = $invoiceRequest->getRules($this->jenis_pembiayaan, $this->form_data_invoice ?? [], $excludeIds);
        $messages = $invoiceRequest->messages();

        $this->validate($rules, $messages);
        
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
            // Pertahankan dokumen lama jika tidak diupload ulang
            $existingItem = $this->form_data_invoice[$this->index_data_invoice] ?? [];
            
            foreach ([
                'dokumen_invoice', 
                'dokumen_kontrak', 
                'dokumen_so', 
                'dokumen_bast', 
                'dokumen_lainnya'
            ] as $dokumen) {
                if (array_key_exists($dokumen, $formData) && is_null($formData[$dokumen])) {
                    $formData[$dokumen] = $existingItem[$dokumen] ?? null;
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

        $totalPinjaman = $data->sum(fn ($item) => (double) ($item['nilai_pinjaman'] ?? 0));
        $totalBagiHasil = $data->sum(function ($item) {
            $persentase = (double) ($this->persentase_bunga ?? 0);
            $nilaiPinjaman = (double) ($item['nilai_pinjaman'] ?? 0);

            return $nilaiPinjaman * $persentase;
        });

        foreach ($this->form_data_invoice as $key => $value) {
            foreach ([
                'dokumen_invoice', 
                'dokumen_kontrak', 
                'dokumen_so', 
                'dokumen_bast', 
                'dokumen_lainnya'
            ] as $dokumen) {
                if (
                    array_key_exists($dokumen, $value) && 
                    $value[$dokumen] instanceof TemporaryUploadedFile
                ) {
                    $fileInfo = [
                        'real_path' => $value[$dokumen]->getRealPath(),
                        'client_original_name' => $value[$dokumen]->getClientOriginalName(),
                        'mime_type' => $value[$dokumen]->getMimeType(),
                    ];
    
                    $this->form_data_invoice[$key][$dokumen] = $fileInfo;
                }
            }   
        }

        $this->dispatch(
            'invoiceTotalsUpdated',
            totalPinjaman: $totalPinjaman,
            totalBagiHasil: $totalBagiHasil,
            formDataInvoice: $this->form_data_invoice ?? []
        )->to(Create::class);
    }
}
