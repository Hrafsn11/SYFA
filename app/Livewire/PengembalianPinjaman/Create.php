<?php

namespace App\Livewire\PengembalianPinjaman;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Attributes\FieldInput;
use App\Models\BuktiPeminjaman;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanPeminjaman;
use App\Services\ArPerbulanService;
use App\Livewire\Traits\HasValidate;
use App\Models\PengembalianPinjaman;
use Illuminate\Support\Facades\Auth;
use App\Models\MasterDebiturDanInvestor;
use App\Livewire\Traits\HandleComponentEvent;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\PengembalianPinjamanRequest;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Attributes\On;

class Create extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads, HandleComponentEvent;

    // Override HandleComponentEvent khusus untuk komponen ini
    #[On('select2-updated')]
    #[On('select2-changed')]
    #[On('datepicker-updated')]
    #[On('currency-updated')]
    public function handleComponentEvent($value, $modelName)
    {
        if (property_exists($this, $modelName)) {
            $this->{$modelName} = $value;

            // Trigger hook updated secara manual
            $methodName = 'updated' . \Illuminate\Support\Str::studly($modelName);
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
        }
    }

    private string $validateClass = PengembalianPinjamanRequest::class;

    #[FieldInput]
    public
        $kode_peminjaman,
        $nama_perusahaan,
        $total_pinjaman,
        $total_bagi_hasil,
        $tanggal_pencairan,
        $lama_pemakaian,
        $nominal_invoice,
        $invoice_dibayarkan,
        $bulan_pembayaran,
        $yang_harus_dibayarkan,
        $sisa_utang,
        $sisa_bagi_hasil,
        $catatan;

    #[FieldInput]
    public $pengembalian_invoices = [];

    public $nominal_yang_dibayarkan;
    public $modalFile;

    public $pengajuanPeminjaman;
    public $namaPerusahaan;
    public $debitur;
    public $availableInvoices = [];
    public $availableBulanPembayaran = [];
    public $jenisPembiayaan;
    public $tenorPembayaran;
    public $yangHarusDibayarkanPerBulan;
    public $tanggalPencairanReal;

    // Late payment adjustment fields
    public $persentaseBagiHasil = 0;      // Persentase bagi hasil (e.g., 5 for 5%)
    public $bulanKeterlambatan = 0;        // Jumlah bulan keterlambatan
    public $bagiHasilTambahan = 0;         // Bagi hasil tambahan karena keterlambatan
    public $bagiHasilAwal = 0;             // Bagi hasil awal sebelum penyesuaian
    public $totalBagiHasilDisesuaikan = 0; // Total bagi hasil setelah penyesuaian
    public $selectedDueDate = null;        // Due Date tannggal keterlambaran
    public $isLate = false;                // Flag apakah terlambat
    public $tanggal_jatuh_tempo = null;    // Tanggal jatuh tempo dari pengajuan_peminjaman

    protected $listeners = ['refreshData' => '$refresh'];

    public function mount()
    {
        $this->setUrlSaveData('store', 'pengembalian.store', ['callback' => 'afterSave']);
        $this->loadInitialData();
    }

    private function loadInitialData()
    {
        $this->debitur = MasterDebiturDanInvestor::where('user_id', auth()->user()->id)->first();
        $this->nama_perusahaan = $this->debitur->nama ?? auth()->user()->name;
        $this->namaPerusahaan = $this->nama_perusahaan;

        $this->pengajuanPeminjaman = isset($this->debitur)
            ? PengajuanPeminjaman::where('id_debitur', $this->debitur->id_debitur)
            ->where('status', 'Dana Sudah Dicairkan')
            ->select('id_pengajuan_peminjaman', 'nomor_peminjaman', 'jenis_pembiayaan', 'total_pinjaman', 'total_bagi_hasil', 'harapan_tanggal_pencairan', 'tenor_pembayaran', 'yang_harus_dibayarkan')
            ->get()
            : collect([]);

        $this->pengembalian_invoices = [];
        $this->sisa_utang = 0;
        $this->sisa_bagi_hasil = 0;
    }

    public function setterFormData()
    {
        // 1. Map FieldInput properties ke form_data
        foreach ($this->getUniversalFieldInputs() as $field) {
            $this->form_data[$field] = $this->{$field};
        }

        // 2. Process File Uploads dalam Array Invoice
        $processedInvoices = [];
        foreach ($this->pengembalian_invoices as $item) {
            if (isset($item['file']) && $item['file'] instanceof TemporaryUploadedFile) {
                $fileName = time() . '_' . uniqid() . '_' . $item['file']->getClientOriginalName();
                $filePath = $item['file']->storeAs('bukti_pembayaran', $fileName, 'public');
                $item['file'] = $filePath;
            }
            $processedInvoices[] = $item;
        }

        $this->form_data['pengembalian_invoices'] = $processedInvoices;
    }

    public function updatedKodePeminjaman($value)
    {
        if (!$value) {
            $this->resetFormData();
            return;
        }

        $pengajuan = PengajuanPeminjaman::with(['buktiPeminjaman' => function ($query) {
            $query->select('id_bukti_peminjaman', 'id_pengajuan_peminjaman', 'no_invoice', 'no_kontrak', 'nilai_invoice', 'nilai_pinjaman', 'nilai_bagi_hasil', 'due_date')
                ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('due_date', 'asc');
        }])->find($value);

        if (!$pengajuan) {
            $this->resetFormData();
            return;
        }

        $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $value)
            ->orderBy('created_at', 'desc')
            ->first();

        $this->total_pinjaman = $pengembalianTerakhir ? $pengembalianTerakhir->sisa_bayar_pokok : $pengajuan->total_pinjaman;
        $this->bagiHasilAwal = $pengembalianTerakhir ? $pengembalianTerakhir->sisa_bagi_hasil : $pengajuan->total_bagi_hasil;
        $this->total_bagi_hasil = $this->bagiHasilAwal; // Will be adjusted if late
        $this->persentaseBagiHasil = $pengajuan->persentase_bagi_hasil ?? 0;
        $this->jenisPembiayaan = $pengajuan->jenis_pembiayaan;
        $this->tenorPembayaran = $pengajuan->tenor_pembayaran;
        $this->tanggal_jatuh_tempo = $pengajuan->tanggal_jatuh_tempo;

        // Hitung yangHarusDibayarkanPerBulan dengan fallback jika null
        if ($pengajuan->yang_harus_dibayarkan) {
            $this->yangHarusDibayarkanPerBulan = $pengajuan->yang_harus_dibayarkan;
        } elseif ($pengajuan->jenis_pembiayaan === 'Installment' && $pengajuan->tenor_pembayaran > 0) {
            // Fallback: hitung manual dari (total_pinjaman + total_bagi_hasil) / tenor
            $totalPinjaman = $pengajuan->total_pinjaman ?? 0;
            $totalBagiHasil = $pengajuan->total_bagi_hasil ?? 0;
            $pembayaranTotal = $totalPinjaman + $totalBagiHasil;
            $this->yangHarusDibayarkanPerBulan = $pembayaranTotal / $pengajuan->tenor_pembayaran;
        } else {
            $this->yangHarusDibayarkanPerBulan = 0;
        }

        // Reset late payment fields
        $this->resetLatePaymentFields();

        // Ambil tanggal pencairan dari history yang memiliki tanggal_pencairan (terbaru)
        $historyPencairan = $pengajuan->historyStatus()
            ->whereNotNull('tanggal_pencairan')
            ->orderBy('created_at', 'desc')
            ->first();

        $this->tanggalPencairanReal = $historyPencairan?->tanggal_pencairan ?? $pengajuan->harapan_tanggal_pencairan;

        // Untuk non-Installment: due date = tanggal pencairan + 30 hari
        if ($pengajuan->jenis_pembiayaan !== 'Installment' && $this->tanggalPencairanReal) {
            $this->selectedDueDate = Carbon::parse($this->tanggalPencairanReal)->addDays(30)->format('Y-m-d');
        }
        $this->tanggal_pencairan = Carbon::parse($this->tanggalPencairanReal)->format('d-m-Y');

        $this->availableInvoices = $pengajuan->buktiPeminjaman->map(function ($bukti) use ($pengajuan) {
            $label = ($pengajuan->jenis_pembiayaan === 'Invoice Financing') ? $bukti->no_invoice : (in_array($pengajuan->jenis_pembiayaan, ['PO Financing', 'Factoring']) ? $bukti->no_kontrak : '');

            $nilai = ($pengajuan->jenis_pembiayaan === 'Invoice Financing') ? $bukti->nilai_invoice : (in_array($pengajuan->jenis_pembiayaan, ['PO Financing', 'Factoring']) ? $bukti->nilai_pinjaman : 0);

            if (!$label) return null;

            return [
                'value' => $label,
                'label' => $label,
                'nilai' => $nilai,
                'id' => $bukti->id_bukti_peminjaman,
                'due_date' => $bukti->due_date
            ];
        })->filter()->values()->toArray();

        if ($pengajuan->jenis_pembiayaan === 'Installment' && $pengajuan->tenor_pembayaran > 0) {
            $this->availableBulanPembayaran = collect(range(1, $pengajuan->tenor_pembayaran))
                ->map(fn($i) => "Bulan ke-{$i}")
                ->toArray();
        } else {
            $this->availableBulanPembayaran = [];
        }

        $this->calculateLamaPemakaian();
        $this->calculateSisa();

        // Dispatch event untuk re-init Select2 di frontend
        $this->dispatch('init-select2-invoice');
    }

    public function updatedInvoiceDibayarkan($value)
    {
        $invoice = collect($this->availableInvoices)->firstWhere('label', $value);
        $this->nominal_invoice = $invoice ? $invoice['nilai'] : null;

        // Untuk non-Installment: due date sudah dihitung dari tanggal_pencairan + 30 hari
        // Tidak perlu mengambil dari invoice['due_date'] lagi
        // selectedDueDate sudah di-set di updatedKodePeminjaman

        $this->calculateLatePaymentAdjustment();
    }

    private function calculateLatePaymentAdjustment(): void
    {
        $this->resetLatePaymentFields();

        if (!$this->selectedDueDate || !$this->total_pinjaman || $this->persentaseBagiHasil <= 0) {
            return;
        }

        $dueDate = Carbon::parse($this->selectedDueDate);
        $today = Carbon::now();

        if ($today->gt($dueDate)) {
            $bulanKeterlambatanSekarang = (int) ceil($dueDate->diffInDays($today) / 30);

            $pengembalianTerakhirSetelahDueDate = PengembalianPinjaman::where('id_pengajuan_peminjaman', $this->kode_peminjaman)
                ->where('created_at', '>', $dueDate)
                ->orderBy('created_at', 'desc')
                ->first();

            $bulanKeterlambatanSebelumnya = 0;

            if ($pengembalianTerakhirSetelahDueDate) {
                $bulanKeterlambatanSebelumnya = (int) ceil($dueDate->diffInDays($pengembalianTerakhirSetelahDueDate->created_at) / 30);
            }

            $selisihBulan = max(0, $bulanKeterlambatanSekarang - $bulanKeterlambatanSebelumnya);

            if ($selisihBulan > 0) {
                $this->isLate = true;
                $this->bulanKeterlambatan = $selisihBulan;

                // sisa_pokok × (persentase / 100) × selisih_bulan
                $sisaPokok = (float) $this->total_pinjaman;
                $persentase = (float) $this->persentaseBagiHasil / 100;

                $this->bagiHasilTambahan = round($sisaPokok * $persentase * $selisihBulan);

                // Adjust total profit sharing
                $this->totalBagiHasilDisesuaikan = $this->bagiHasilAwal + $this->bagiHasilTambahan;
                $this->total_bagi_hasil = $this->totalBagiHasilDisesuaikan;
            }
        }

        // Recalculate sisa
        $this->calculateSisa();
    }

    private function resetLatePaymentFields(): void
    {
        $this->isLate = false;
        $this->bulanKeterlambatan = 0;
        $this->bagiHasilTambahan = 0;
        $this->totalBagiHasilDisesuaikan = $this->bagiHasilAwal;
        $this->total_bagi_hasil = $this->bagiHasilAwal;
    }

    public function updatedBulanPembayaran($value)
    {
        if (!$value || $this->jenisPembiayaan !== 'Installment') {
            $this->yang_harus_dibayarkan = null;
            $this->nominal_invoice = null;
            $this->invoice_dibayarkan = null;
            return;
        }

        $bulanKe = (int) str_replace('Bulan ke-', '', $value);
        
        // Pastikan yangHarusDibayarkanPerBulan ada
        $nominalPerBulan = $this->yangHarusDibayarkanPerBulan ?? 0;
        
        // Fallback calculation jika masih 0
        if ($nominalPerBulan <= 0 && $this->tenorPembayaran > 0) {
            $totalPinjaman = $this->total_pinjaman ?? 0;
            $totalBagiHasil = $this->bagiHasilAwal ?? 0;
            $pembayaranTotal = $totalPinjaman + $totalBagiHasil;
            $nominalPerBulan = $pembayaranTotal / $this->tenorPembayaran;
        }

        $nominalBulanIni = $nominalPerBulan;

        // Bulan ke-1: cicilan + seluruh bagi hasil
        if ($bulanKe === 1) {
            $nominalBulanIni = $nominalPerBulan + ($this->total_bagi_hasil ?? 0);
        }

        $this->yang_harus_dibayarkan = round($nominalBulanIni);
        $this->nominal_invoice = round($nominalBulanIni);
        $this->invoice_dibayarkan = $value;
    }

    public function saveInvoice($data)
    {
        $nominal = $data['nominal'] ?? 0;
        $editingIndex = $data['editingIndex'] ?? null;

        if ($nominal < 1) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Nominal harus lebih dari 0']);
            return;
        }

        if ($editingIndex === null && !$this->modalFile) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Bukti pembayaran harus diupload']);
            return;
        }

        $invoiceData = ['nominal' => $nominal];

        if ($this->modalFile) {
            $invoiceData['file'] = $this->modalFile;
            $invoiceData['file_name'] = $this->modalFile->getClientOriginalName();
        } elseif ($editingIndex !== null && isset($this->pengembalian_invoices[$editingIndex]['file'])) {
            $invoiceData['file'] = $this->pengembalian_invoices[$editingIndex]['file'];
            $invoiceData['file_name'] = $this->pengembalian_invoices[$editingIndex]['file_name'];
        }

        if ($editingIndex !== null) {
            $this->pengembalian_invoices[$editingIndex] = $invoiceData;
        } else {
            $this->pengembalian_invoices[] = $invoiceData;
        }

        $this->calculateSisa();
        $this->modalFile = null;
        $this->dispatch('closeInvoiceModal');
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Data invoice disimpan sementara']);
    }

    public function deleteInvoice($index)
    {
        unset($this->pengembalian_invoices[$index]);
        $this->pengembalian_invoices = array_values($this->pengembalian_invoices);
        $this->calculateSisa();
    }

    public function calculateLamaPemakaian()
    {
        if (!$this->tanggalPencairanReal) {
            $this->lama_pemakaian = 0;
            return;
        }

        if ($this->jenisPembiayaan === 'Installment') {
            $this->lama_pemakaian = $this->tenorPembayaran * 30;
        } else {
            $pencairan = Carbon::parse($this->tanggalPencairanReal);
            $this->lama_pemakaian = $pencairan->diffInDays(now());
        }
    }

    public function calculateSisa()
    {
        // Hitung total yang sudah dibayarkan dari list invoice
        $totalBayar = collect($this->pengembalian_invoices)->sum('nominal');

        $totalBagiHasil = $this->total_bagi_hasil;
        $totalPokok = $this->total_pinjaman;

        // Logic Prioritas: Bayar Bagi Hasil Dulu
        if ($totalBayar >= $totalBagiHasil) {
            // Jika pembayaran cukup untuk melunasi bagi hasil
            $this->sisa_bagi_hasil = 0;

            // Sisa uangnya dipakai untuk bayar pokok
            $sisaUntukPokok = $totalBayar - $totalBagiHasil;
            $this->sisa_utang = max(0, $totalPokok - $sisaUntukPokok);
        } else {
            // Jika pembayaran belum cukup melunasi bagi hasil
            $this->sisa_bagi_hasil = $totalBagiHasil - $totalBayar;
            $this->sisa_utang = $totalPokok; // Pokok utuh
        }
    }



    public function save()
    {
        $this->calculateSisa();
        $this->saveData('pengembalian.store');
    }

    public function afterSave($payload)
    {

        if ($payload->error) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => $payload->message ?? 'Terjadi kesalahan saat menyimpan data.'
            ]);
            return;
        }

        $data = $payload->data ?? [];

        $data = is_array($data) ? $data : (array) $data;

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => $payload->message ?? 'Data berhasil disimpan.'
        ]);

        if (isset($data['redirect'])) {
            return redirect($data['redirect']);
        }

        $this->resetFormData();
        $this->dispatch('refreshData');
    }

    private function resetFormData()
    {
        $this->total_pinjaman = null;
        $this->total_bagi_hasil = null;
        $this->tanggal_pencairan = null;
        $this->lama_pemakaian = 0;
        $this->nominal_invoice = null;
        $this->invoice_dibayarkan = null;
        $this->bulan_pembayaran = null;
        $this->yang_harus_dibayarkan = null;
        $this->sisa_utang = 0;
        $this->sisa_bagi_hasil = 0;
        $this->pengembalian_invoices = [];
        $this->availableInvoices = [];
        $this->availableBulanPembayaran = [];

        // Reset late payment fields
        $this->persentaseBagiHasil = 0;
        $this->bulanKeterlambatan = 0;
        $this->bagiHasilTambahan = 0;
        $this->bagiHasilAwal = 0;
        $this->totalBagiHasilDisesuaikan = 0;
        $this->selectedDueDate = null;
        $this->isLate = false;
        $this->tanggal_jatuh_tempo = null;
    }
}
