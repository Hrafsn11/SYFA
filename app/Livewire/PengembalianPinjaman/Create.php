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
use App\Models\MasterDebiturDanInvestor;
use App\Livewire\Traits\HandleComponentEvent;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\PengembalianPinjamanRequest;

class Create extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads, HandleComponentEvent;
    
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

    public $pengembalian_invoices = [];
    
    // Temporary property for modal input
    public $nominal_yang_dibayarkan;

    // Additional properties untuk UI
    public $pengajuanPeminjaman;
    public $namaPerusahaan;
    public $debitur;
    public $availableInvoices = [];
    public $availableBulanPembayaran = [];
    public $jenisPembiayaan;
    public $tenorPembayaran;
    public $yangHarusDibayarkanPerBulan;
    public $tanggalPencairanReal;

    // File upload for modal
    public $modalFile;

    protected $listeners = ['refreshData' => '$refresh'];
    
    // Real-time validation for file upload
    protected function rules()
    {
        return [
            'modalFile' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:2048',
        ];
    }
    
    protected $messages = [
        'modalFile.mimes' => 'File harus berformat PDF, PNG, JPG, atau JPEG.',
        'modalFile.max' => 'Ukuran file maksimal 2MB.',
    ];

    public function mount()
    {
        $this->setUrlSaveData('store', 'pengembalian.store', ['callback' => 'afterSave']);
        
        $this->debitur = MasterDebiturDanInvestor::where('user_id', auth()->user()->id)->first();
        $this->nama_perusahaan = $this->debitur->nama ?? auth()->user()->name;
        $this->namaPerusahaan = $this->nama_perusahaan;
        
        $this->pengajuanPeminjaman = isset($this->debitur)
            ? PengajuanPeminjaman::where('id_debitur', $this->debitur->id_debitur)
                ->where('status', 'Dana Sudah Dicairkan')
                ->select('id_pengajuan_peminjaman', 'nomor_peminjaman', 'jenis_pembiayaan', 'total_pinjaman', 'total_bagi_hasil', 'harapan_tanggal_pencairan', 'tenor_pembayaran', 'yang_harus_dibayarkan')
                ->get()
            : collect([]);

        // Initialize empty invoice
        $this->pengembalian_invoices = [];
        
        // Initialize sisa values
        $this->sisa_utang = 0;
        $this->sisa_bagi_hasil = 0;
    }

    public function loadPeminjamanData($value)
    {
        $this->updatedKodePeminjaman($value);
    }

    public function updatedKodePeminjaman($value)
    {
        if (!$value) {
            $this->resetFormData();
            return;
        }

        $pengajuan = PengajuanPeminjaman::with(['buktiPeminjaman' => function($query) {
            $query->select('id_bukti_peminjaman', 'id_pengajuan_peminjaman', 'no_invoice', 'no_kontrak', 'nilai_invoice', 'nilai_pinjaman', 'nilai_bagi_hasil', 'due_date')
                ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
                ->orderBy('due_date', 'asc');
        }])
        ->find($value);

        if (!$pengajuan) {
            $this->resetFormData();
            return;
        }

        // Get data pengembalian terakhir untuk cek sisa
        $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $value)
            ->orderBy('created_at', 'desc')
            ->first();

        $totalPinjaman = $pengembalianTerakhir ? $pengembalianTerakhir->sisa_bayar_pokok : $pengajuan->total_pinjaman;
        $totalBagiHasil = $pengembalianTerakhir ? $pengembalianTerakhir->sisa_bagi_hasil : $pengajuan->total_bagi_hasil;

        $this->total_pinjaman = $totalPinjaman;
        $this->total_bagi_hasil = $totalBagiHasil;
        $this->jenisPembiayaan = $pengajuan->jenis_pembiayaan;
        $this->tenorPembayaran = $pengajuan->tenor_pembayaran;
        $this->yangHarusDibayarkanPerBulan = $pengajuan->yang_harus_dibayarkan;

        // Set tanggal pencairan
        if ($pengajuan->jenis_pembiayaan === 'Installment') {
            // Untuk Installment, ambil dari history status
            $historyPencairan = $pengajuan->historyStatus()
                ->where('status', 'Dana Sudah Dicairkan')
                ->orderBy('created_at', 'desc')
                ->first();
            
            $this->tanggal_pencairan = $historyPencairan 
                ? Carbon::parse($historyPencairan->created_at)->format('d-m-Y')
                : Carbon::parse($pengajuan->harapan_tanggal_pencairan)->format('d-m-Y');
            
            $this->tanggalPencairanReal = $historyPencairan 
                ? $historyPencairan->created_at 
                : $pengajuan->harapan_tanggal_pencairan;
        } else {
            $this->tanggal_pencairan = Carbon::parse($pengajuan->harapan_tanggal_pencairan)->format('d-m-Y');
            $this->tanggalPencairanReal = $pengajuan->harapan_tanggal_pencairan;
        }

        // Populate available invoices/kontrak
        $this->availableInvoices = $pengajuan->buktiPeminjaman->map(function($bukti) use ($pengajuan) {
            $label = '';
            $nilai = 0;

            if ($pengajuan->jenis_pembiayaan === 'Invoice Financing') {
                $label = $bukti->no_invoice;
                $nilai = $bukti->nilai_invoice;
            } elseif (in_array($pengajuan->jenis_pembiayaan, ['PO Financing', 'Factoring'])) {
                $label = $bukti->no_kontrak;
                $nilai = $bukti->nilai_pinjaman;
            }

            return [
                'value' => $label,
                'label' => $label,
                'nilai' => $nilai,
                'id' => $bukti->id_bukti_peminjaman,
                'due_date' => $bukti->due_date
            ];
        })->filter()->values()->toArray();

        // Populate bulan pembayaran untuk Installment
        if ($pengajuan->jenis_pembiayaan === 'Installment' && $pengajuan->tenor_pembayaran > 0) {
            $this->availableBulanPembayaran = collect(range(1, $pengajuan->tenor_pembayaran))
                ->map(fn($i) => "Bulan ke-{$i}")
                ->toArray();
        } else {
            $this->availableBulanPembayaran = [];
        }

        $this->calculateLamaPemakaian();
        $this->calculateSisa(); // Initialize sisa values
    }

    public function updatedInvoiceDibayarkan($value)
    {
        if (!$value) {
            $this->nominal_invoice = null;
            return;
        }

        $invoice = collect($this->availableInvoices)->firstWhere('label', $value);
        
        if ($invoice) {
            $this->nominal_invoice = $invoice['nilai'];
        }
    }

    public function updatedBulanPembayaran($value)
    {
        if (!$value || $this->jenisPembiayaan !== 'Installment') {
            $this->yang_harus_dibayarkan = null;
            return;
        }

        $bulanKe = (int) str_replace('Bulan ke-', '', $value);
        $nominalBulanIni = $this->yangHarusDibayarkanPerBulan;

        // Bulan pertama include bagi hasil
        if ($bulanKe === 1) {
            $nominalBulanIni += $this->total_bagi_hasil;
        }

        $this->yang_harus_dibayarkan = round($nominalBulanIni);
        $this->nominal_invoice = $nominalBulanIni;
        $this->invoice_dibayarkan = $value;
    }

    public function calculateLamaPemakaian()
    {
        if (!$this->tanggalPencairanReal) {
            $this->lama_pemakaian = 0;
            return;
        }

        if ($this->jenisPembiayaan === 'Installment') {
            // Untuk Installment, lama pemakaian = tenor dalam hari (approx)
            $this->lama_pemakaian = $this->tenorPembayaran * 30;
        } else {
            // Hitung dari tanggal pencairan sampai hari ini
            $pencairan = Carbon::parse($this->tanggalPencairanReal);
            $this->lama_pemakaian = $pencairan->diffInDays(now());
        }
    }

    public function calculateSisa()
    {
        $totalBayar = collect($this->pengembalian_invoices)->sum('nominal');
        
        $this->sisa_utang = max(0, $this->total_pinjaman - $totalBayar);
        $this->sisa_bagi_hasil = max(0, $this->total_bagi_hasil);
    }

    public function editInvoice($index)
    {
        // Return invoice data for JavaScript to populate modal
        return $this->pengembalian_invoices[$index];
    }

    public function saveInvoice($data)
    {
        $nominal = $data['nominal'] ?? 0;
        $editingIndex = $data['editingIndex'] ?? null;

        if ($nominal < 1) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Nominal harus lebih dari 0'
            ]);
            return;
        }

        // Validate file upload (required for new, optional for edit)
        if ($editingIndex === null && !$this->modalFile) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Bukti pembayaran harus diupload'
            ]);
            return;
        }

        $invoiceData = [
            'nominal' => $nominal,
        ];

        // Handle file upload from Livewire upload
        if ($this->modalFile) {
            $invoiceData['file'] = $this->modalFile;
            $invoiceData['file_name'] = $this->modalFile->getClientOriginalName();
        } elseif ($editingIndex !== null && isset($this->pengembalian_invoices[$editingIndex]['file'])) {
            // Keep existing file when editing without new upload
            $invoiceData['file'] = $this->pengembalian_invoices[$editingIndex]['file'];
            $invoiceData['file_name'] = $this->pengembalian_invoices[$editingIndex]['file_name'];
        }

        if ($editingIndex !== null) {
            $this->pengembalian_invoices[$editingIndex] = $invoiceData;
        } else {
            $this->pengembalian_invoices[] = $invoiceData;
        }

        $this->calculateSisa();
        
        // Reset modal file
        $this->modalFile = null;
        
        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Data pengembalian invoice berhasil ' . ($editingIndex !== null ? 'diupdate' : 'ditambahkan')
        ]);
        
        // Close modal
        $this->dispatch('closeInvoiceModal');
    }

    public function deleteInvoice($index)
    {
        unset($this->pengembalian_invoices[$index]);
        $this->pengembalian_invoices = array_values($this->pengembalian_invoices);
        $this->calculateSisa();
        
        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Data pengembalian invoice berhasil dihapus'
        ]);
    }

    public function save()
    {
        $this->calculateSisa();

        $validatedData = $this->validate();

        try {
            DB::beginTransaction();

            $pengajuan = PengajuanPeminjaman::findOrFail($this->kode_peminjaman);

            $status = 'Belum Lunas';
            if ($this->sisa_utang == 0 && $this->sisa_bagi_hasil == 0) {
                $status = 'Lunas';
            }

            // Convert tanggal pencairan
            $tanggalPencairan = $this->tanggal_pencairan;
            try {
                $tanggalPencairan = Carbon::createFromFormat('d-m-Y', $this->tanggal_pencairan)->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    $tanggalPencairan = Carbon::createFromFormat('d/m/Y', $this->tanggal_pencairan)->format('Y-m-d');
                } catch (\Exception $e2) {
                    $tanggalPencairan = $this->tanggal_pencairan;
                }
            }

            $pengembalian = PengembalianPinjaman::create([
                'id_pengajuan_peminjaman' => $this->kode_peminjaman,
                'nama_perusahaan' => $this->nama_perusahaan,
                'nomor_peminjaman' => $pengajuan->nomor_peminjaman,
                'total_pinjaman' => $this->total_pinjaman,
                'total_bagi_hasil' => $this->total_bagi_hasil,
                'tanggal_pencairan' => $tanggalPencairan,
                'lama_pemakaian' => $this->lama_pemakaian,
                'nominal_invoice' => $this->nominal_invoice,
                'invoice_dibayarkan' => $this->invoice_dibayarkan,
                'bulan_pembayaran' => $this->bulan_pembayaran,
                'yang_harus_dibayarkan' => $this->yang_harus_dibayarkan,
                'sisa_bayar_pokok' => $this->sisa_utang,
                'sisa_bagi_hasil' => $this->sisa_bagi_hasil,
                'catatan' => $this->catatan,
                'status' => $status,
            ]);

            // Get due_date based on jenis pembiayaan
            $jenisPembiayaan = $pengajuan->jenis_pembiayaan;
            $dueDate = null;
            
            if ($jenisPembiayaan === 'Installment') {
                $dueDate = null;
            } else {
                $buktiPeminjamanQuery = BuktiPeminjaman::where('id_pengajuan_peminjaman', $this->kode_peminjaman);
                
                if ($jenisPembiayaan === 'Invoice Financing') {
                    $buktiPeminjamanQuery->where('no_invoice', $this->invoice_dibayarkan);
                } elseif (in_array($jenisPembiayaan, ['PO Financing', 'Factoring'])) {
                    $buktiPeminjamanQuery->where('no_kontrak', $this->invoice_dibayarkan);
                }
                
                $buktiPeminjaman = $buktiPeminjamanQuery->first();
                $dueDate = $buktiPeminjaman->due_date ?? null;
            }

            // Save pengembalian invoices
            foreach ($this->pengembalian_invoices as $index => $item) {
                $filePath = null;

                if (isset($item['file']) && $item['file']) {
                    $file = $item['file'];
                    $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('pengembalian_invoices', $fileName, 'public');
                }

                \App\Models\PengembalianInvoice::create([
                    'id_pengembalian' => $pengembalian->ulid,
                    'nominal_yg_dibayarkan' => $item['nominal'],
                    'bukti_pembayaran' => $filePath,
                ]);

                // Create report pengembalian (mengikuti pattern di Controller)
                \App\Models\ReportPengembalian::create([
                    'id_pengembalian' => $pengembalian->ulid,
                    'nomor_peminjaman' => $pengajuan->nomor_peminjaman,
                    'nomor_invoice' => $this->invoice_dibayarkan,
                    'due_date' => $dueDate,
                    'tanggal_pembayaran' => now(),
                    'hari_keterlambatan' => $this->calculateHariKeterlambatan($dueDate),
                    'total_bulan_pemakaian' => $this->convertHariToBulan($this->lama_pemakaian),
                    'nilai_total_pengembalian' => $item['nominal'],
                ]);
            }

            DB::commit();

            // Update AR Perbulan
            app(ArPerbulanService::class)->updateAROnPengembalian(
                $this->kode_peminjaman,
                now()
            );

            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Data pengembalian berhasil disimpan'
            ]);

            return redirect()->route('pengembalian.index');

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function afterSave($response)
    {
        if (isset($response['success']) && $response['success']) {
            return redirect()->route('pengembalian.index');
        }
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
        $this->availableInvoices = [];
        $this->availableBulanPembayaran = [];
        $this->jenisPembiayaan = null;
        $this->pengembalian_invoices = [];
    }

    private function calculateHariKeterlambatan($dueDate)
    {
        if (!$dueDate) {
            return '0 Hari';
        }

        $dueDateCarbon = Carbon::parse($dueDate);
        $now = Carbon::now();

        if ($now->lte($dueDateCarbon)) {
            return '0 Hari';
        }

        $diff = $now->diff($dueDateCarbon);

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = $diff->y . ' Tahun';
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m . ' Bulan';
        }
        if ($diff->d > 0) {
            $parts[] = $diff->d . ' Hari';
        }

        return !empty($parts) ? implode(' ', $parts) : '0 Hari';
    }

    private function convertHariToBulan($hari)
    {
        if ($hari <= 0) {
            return '0 Bulan';
        }

        $tahun = floor($hari / 365);
        $sisaHari = $hari % 365;
        $bulan = floor($sisaHari / 30);
        $hari = $sisaHari % 30;

        $parts = [];
        if ($tahun > 0) {
            $parts[] = $tahun . ' Tahun';
        }
        if ($bulan > 0) {
            $parts[] = $bulan . ' Bulan';
        }
        if ($hari > 0) {
            $parts[] = $hari . ' Hari';
        }

        return !empty($parts) ? implode(' ', $parts) : '0 Hari';
    }

    public function render()
    {
        return view('livewire.pengembalian-pinjaman.create')
            ->layout('layouts.app', [
                'title' => 'Tambah Pengembalian Pinjaman'
            ]);
    }
}
