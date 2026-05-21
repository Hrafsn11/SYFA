<?php

namespace App\Livewire\PengajuanPinjaman;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Enums\JenisPembiayaanEnum;
use App\Models\PengajuanPeminjaman;
use App\Attributes\ParameterIDRoute;
use App\Livewire\Traits\HasValidate;
use App\Models\MasterDebiturDanInvestor;
use App\Models\MasterSumberPendanaanEksternal;
use App\Http\Requests\PengajuanPinjamanRequest;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Livewire\PengajuanPinjaman\Event\HandleCreateEvents;
use App\Livewire\PengajuanPinjaman\FieldsInput\FieldInputCreate;
use App\Livewire\PengajuanPinjaman\Dispatch\HandleCreateDispatch;

class Create extends Component
{
    use HasUniversalFormAction, 
        HasValidate, 
        WithFileUploads, 
        HandleCreateEvents, // event handling
        HandleCreateDispatch, // dispatching
        FieldInputCreate; // list input fields
    private string $validateClass = PengajuanPinjamanRequest::class;

    #[ParameterIDRoute]
    public $id = null;

    public $nama_perusahaan;
    public $nama_bank;
    public $no_rekening;
    public $lampiran_sid_current;
    // besides installment
    public $total_pinjaman;
    public $total_bunga;
    public $pembayaran_total;
    // installment
    public $nominal_pinjaman;
    public $pps_debit;
    public $pps_percentage;
    public $s_finance;
    public $total_pembayaran_installment;
    public $bayar_per_bulan;
    public $title;

    public $pengajuan = null;
    public $sumber_eksternal;
    public $invoice_financing_data = [];
    public $installment_data = [];
    public $list_tenor_pembayaran;

    public function mount($id = null)
    {
        $this->id = $id;

        $this->setUrlSaveData('store', 'peminjaman.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update', 'peminjaman.update', ["id" => "id_placeholder", "callback" => "afterAction"]);

        $this->nama_perusahaan = auth()->user()->name;
        $this->sumber_eksternal = MasterSumberPendanaanEksternal::orderBy('nama_instansi')->get();
        $this->title = 'Menu Pengajuan Peminjaman - Draft';
        $this->list_tenor_pembayaran = [
            ['value' => '3', 'label' => '3 Bulan'],
            ['value' => '6', 'label' => '6 Bulan'],
            ['value' => '9', 'label' => '9 Bulan'],
            ['value' => '12', 'label' => '12 Bulan']
        ];

        $masterDebiturDanInvestor = MasterDebiturDanInvestor::where('email', auth()->user()->email)
            ->where('flagging', 'tidak')
            ->where('status', 'active')
            ->first();

        if (!$masterDebiturDanInvestor) {
            abort(403, 'Data debitur tidak ditemukan untuk akun ini.');
        }

        $this->nama_bank = $masterDebiturDanInvestor->nama_bank;
        $this->no_rekening = $masterDebiturDanInvestor->no_rek;

        if ($this->id !== null) $this->edit();
    }

    public function render()
    {
        return view('livewire.pengajuan-pinjaman.create');
    }

    public function setterFormData()
    {
        foreach ($this->getUniversalFieldInputs() as $key => $value) {
            if ($key == 'sumber_pembiayaan') {
                $this->form_data[$value] = strtolower($this->{$value});
            } else {
                $this->form_data[$value] = $this->{$value};
            }
        }

        // Sertakan id_debitur saat mode edit agar lolos validasi di controller update()
        if ($this->id !== null && $this->pengajuan !== null) {
            $this->form_data['id_debitur'] = $this->pengajuan->id_debitur;
        }

        if ($this->jenis_pembiayaan != JenisPembiayaanEnum::INSTALLMENT) {
            unset($this->form_data['tenor_pembayaran']);
        } else {
            unset($this->form_data['harapan_tanggal_pencairan']);
            unset($this->form_data['rencana_tgl_pembayaran']);
        }
    }

    public function editInvoice($idx)
    {
        $data = $this->form_data_invoice[$idx];
        $data['_edit_index'] = $idx;
        $this->dispatch('edit-invoice', $data);
    }

    protected function setAdditionalValidationData(): array
    {
        return [
            'jenis_pembiayaan' => $this->jenis_pembiayaan ?? null,
            'form_data_invoice' => $this->form_data_invoice ?? [],
        ];
    }

    private function edit()
    {
        $this->pengajuan = PengajuanPeminjaman::with(['debitur', 'instansi', 'buktiPeminjaman'])->findOrFail($this->id);
        
        // Ambil hanya field yang relevan dari bukti peminjaman
        $this->form_data_invoice = $this->pengajuan->buktiPeminjaman->map(function ($item) {
            return collect($item->toArray())->only([
                'no_invoice', 'no_kontrak', 'nama_client',
                'nilai_invoice', 'nilai_pinjaman', 'nilai_bunga',
                'invoice_date', 'due_date', 'kontrak_date',
                'dokumen_invoice', 'dokumen_kontrak', 'dokumen_so',
                'dokumen_bast', 'dokumen_lainnya', 'nama_barang',
            ])->all();
        })->values()->toArray();

        // Set lampiran_sid_current agar view tahu file sudah ada
        $this->lampiran_sid_current = $this->pengajuan->lampiran_sid;

        foreach ($this->pengajuan->toArray() as $key => $value) {
            if ($value === null) continue;
            
            if ($key === 'sumber_pembiayaan') {
                $data = ucfirst($value);
            } elseif (in_array($key, ['harapan_tanggal_pencairan', 'rencana_tgl_pembayaran'])) {
                $parsed = parseCarbonDate($value);
                $data = $parsed ? $parsed->format('d/m/Y') : $value;
            } else {
                $data = $value;
            }

            if (property_exists($this, $key)) {
                $this->{$key} = $data;
            }
        }

        $this->title = 'Edit Pengajuan Peminjaman';
    }

    /**
     * Hapus invoice dari list berdasarkan index.
     */
    public function deleteInvoice(int $index): void
    {
        if (isset($this->form_data_invoice[$index])) {
            unset($this->form_data_invoice[$index]);
            $this->form_data_invoice = array_values($this->form_data_invoice);

            // Dispatch ke InvoiceForm agar sinkron
            $this->dispatch(
                'invoiceTotalsUpdated',
                totalPinjaman: collect($this->form_data_invoice)->sum(fn($item) => (double) ($item['nilai_pinjaman'] ?? 0)),
                totalBagiHasil: collect($this->form_data_invoice)->sum(fn($item) => (double) ($item['nilai_pinjaman'] ?? 0) * 0.02),
                formDataInvoice: $this->form_data_invoice
            )->self();

            // Recalculate totals
            $this->handleInvoiceTotalsUpdated(
                collect($this->form_data_invoice)->sum(fn($item) => (double) ($item['nilai_pinjaman'] ?? $item['nilai_invoice'] ?? 0)),
                collect($this->form_data_invoice)->sum(fn($item) => (double) ($item['nilai_pinjaman'] ?? $item['nilai_invoice'] ?? 0) * 0.02),
                $this->form_data_invoice
            );
        }
    }

    public function afterSave($payload)
    {
        if ($payload->error === false) {
            $this->redirect(route('peminjaman.index'), navigate: true);
        }
    }
}
