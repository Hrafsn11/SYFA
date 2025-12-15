<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasModal;
use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use App\Models\MasterDebiturDanInvestor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PengembalianPinjamanFinlogCreate extends Component
{
    use WithFileUploads, HasModal;

    #[FieldInput] public $id_peminjaman_finlog = '';
    #[FieldInput] public $nama_perusahaan = '';
    #[FieldInput] public $cells_bisnis = '';
    #[FieldInput] public $nama_project = '';
    #[FieldInput] public $tanggal_pencairan = '';
    #[FieldInput] public $top = '';
    #[FieldInput] public $jatuh_tempo = '';
    #[FieldInput] public $nilai_pinjaman = 0;
    #[FieldInput] public $nilai_bagi_hasil = 0;
    #[FieldInput] public $total_pinjaman = 0;
    #[FieldInput] public $sisa_utang = 0;
    #[FieldInput] public $sisa_bagi_hasil = 0;
    #[FieldInput] public $catatan = '';
    #[FieldInput] public $nominal_yang_dibayarkan = 0;
    #[FieldInput] public $bukti_pembayaran_invoice = null;

    public $selectedPeminjaman = null;
    public $pengembalianList = [];
    public $id_cells_project = '';
    public $id_project = '';
    public $currentUserId = null;
    public $currentDebitur = null;
    public $hasNoData = false;

    private const STATUS_LUNAS = 'Lunas';
    private const STATUS_BELUM_LUNAS = 'Belum Lunas';
    private const STATUS_TERLAMBAT = 'Terlambat';

    public function mount()
    {
        $this->currentUserId = auth()->id();
        $this->currentDebitur = MasterDebiturDanInvestor::where('user_id', $this->currentUserId)->first();
        
        if ($this->currentDebitur) {
            $this->nama_perusahaan = $this->currentDebitur->nama;
        } else {
            $this->nama_perusahaan = auth()->user()->name;
            $this->hasNoData = true;
            $this->showToast('warning', 'Anda belum terdaftar sebagai debitur. Silakan hubungi admin.');
        }
    }

    #[On('select2-changed')]
    public function onSelect2Changed($value, $modelName)
    {
        if ($modelName !== 'id_peminjaman_finlog') {
            return;
        }

        if (empty($value)) {
            $this->id_peminjaman_finlog = '';
            $this->resetPeminjamanData();
            return;
        }
        
        $this->id_peminjaman_finlog = $value;
        $this->loadPeminjamanData($value);
    }

    private function loadPeminjamanData($peminjamanId)
    {
        $peminjaman = PeminjamanFinlog::with(['debitur', 'cellsProject.projects'])
            ->where('id_peminjaman_finlog', $peminjamanId)
            ->where('status', 'Selesai')
            ->first();

        if (!$peminjaman) {
            $this->resetPeminjamanData();
            $this->showToast('error', 'Data peminjaman tidak ditemukan atau belum selesai!');
            return;
        }

        $this->selectedPeminjaman = $peminjaman;
        $this->populateFormFields($peminjaman);
        $this->calculateRemainingBalance();
    }

    private function populateFormFields($peminjaman)
    {
        $this->cells_bisnis = $peminjaman->cellsProject?->nama_cells_bisnis ?? '-';
        $this->id_cells_project = $peminjaman->id_cells_project ?? '';
        $this->id_project = $peminjaman->nama_project ?? '';
        
        $this->nama_project = $this->resolveProjectName($peminjaman);
        $this->tanggal_pencairan = $peminjaman->harapan_tanggal_pencairan?->format('d/m/Y') ?? '-';
        $this->jatuh_tempo = $peminjaman->rencana_tgl_pengembalian?->format('d/m/Y') ?? '-';
        $this->top = $peminjaman->top ?? 0;
        $this->nilai_pinjaman = $peminjaman->nilai_pinjaman ?? 0;
        $this->nilai_bagi_hasil = $peminjaman->nilai_bagi_hasil ?? 0;
        $this->total_pinjaman = $peminjaman->total_pinjaman ?? 0;
    }

    private function resolveProjectName($peminjaman)
    {
        // If there's no related cellsProject or no nama_project value, return placeholder
        if (!$peminjaman->cellsProject || empty($peminjaman->nama_project)) {
            return '-';
        }

        $projects = $peminjaman->cellsProject->projects ?? collect();

        // Try find by id_project first (common: nama_project stores ULID id)
        $project = $projects->firstWhere('id_project', $peminjaman->nama_project);

        // If not found, try matching by project name (in case nama_project stores the name)
        if (!$project) {
            $project = $projects->firstWhere('nama_project', $peminjaman->nama_project);
        }

        // As a last resort, try querying projects table directly (handles cases where relation wasn't eager-loaded)
        if (!$project) {
            $project = \App\Models\Project::where('id_project', $peminjaman->nama_project)
                ->orWhere('nama_project', $peminjaman->nama_project)
                ->first();
        }

        // If a project is found, ensure component's id_project is set so other logic can rely on it
        if ($project) {
            $this->id_project = $project->id_project ?? $this->id_project;
            return $project->nama_project ?? '-';
        }

        return '-';
    }

    private function resetPeminjamanData()
    {
        $this->reset([
            'selectedPeminjaman', 'cells_bisnis', 'nama_project', 'tanggal_pencairan',
            'top', 'jatuh_tempo', 'nilai_pinjaman', 'nilai_bagi_hasil', 'total_pinjaman',
            'sisa_utang', 'sisa_bagi_hasil', 'id_cells_project', 'id_project'
        ]);
    }

    private function calculateRemainingBalance()
    {
        if (empty($this->id_peminjaman_finlog)) {
            $this->sisa_bagi_hasil = 0;
            $this->sisa_utang = 0;
            return;
        }

        $totalPaid = PengembalianPinjamanFinlog::where('id_pinjaman_finlog', $this->id_peminjaman_finlog)
            ->sum('jumlah_pengembalian');
        
        $newPayments = array_sum(array_column($this->pengembalianList, 'nominal'));
        $totalPayment = $totalPaid + $newPayments;
        
        $this->allocatePayment($totalPayment);
    }

    private function allocatePayment($totalPayment)
    {
        $sisaBagiHasil = $this->nilai_bagi_hasil;
        $sisaPinjaman = $this->nilai_pinjaman;
        
        if ($totalPayment >= $sisaBagiHasil) {
            $remainingPayment = $totalPayment - $sisaBagiHasil;
            $sisaBagiHasil = 0;
            $sisaPinjaman = max(0, $sisaPinjaman - $remainingPayment);
        } else {
            $sisaBagiHasil -= $totalPayment;
        }
        
        $this->sisa_bagi_hasil = max(0, $sisaBagiHasil);
        $this->sisa_utang = max(0, $sisaPinjaman);
    }

    public function addPengembalian()
    {
        $this->nominal_yang_dibayarkan = $this->sanitizeCurrency($this->nominal_yang_dibayarkan);
        
        $this->validate([
            'nominal_yang_dibayarkan' => 'required|numeric|min:1',
            'bukti_pembayaran_invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'nominal_yang_dibayarkan.required' => 'Nominal wajib diisi.',
            'nominal_yang_dibayarkan.numeric' => 'Nominal harus berupa angka.',
            'nominal_yang_dibayarkan.min' => 'Nominal minimal Rp 1.',
            'bukti_pembayaran_invoice.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran_invoice.mimes' => 'File harus berupa PDF, JPG, JPEG, atau PNG.',
            'bukti_pembayaran_invoice.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $filePath = $this->storeTemporaryFile();

        $this->pengembalianList[] = [
            'nominal' => $this->nominal_yang_dibayarkan,
            'bukti_file' => $filePath,
        ];

        $this->reset(['nominal_yang_dibayarkan', 'bukti_pembayaran_invoice']);
        $this->calculateRemainingBalance();
        
        $this->dispatch('close-pengembalian-modal');
        $this->showToast('success', 'Pengembalian invoice berhasil ditambahkan!');
    }

    private function storeTemporaryFile()
    {
        $filename = 'temp_' . uniqid() . '.' . $this->bukti_pembayaran_invoice->extension();
        return $this->bukti_pembayaran_invoice->storeAs('temp', $filename, 'public');
    }

    public function removePengembalian($index)
    {
        if (isset($this->pengembalianList[$index]['bukti_file'])) {
            Storage::disk('public')->delete($this->pengembalianList[$index]['bukti_file']);
        }
        
        unset($this->pengembalianList[$index]);
        $this->pengembalianList = array_values($this->pengembalianList);
        $this->calculateRemainingBalance();
        
        $this->showToast('success', 'Pengembalian invoice berhasil dihapus!');
    }

    public function store()
    {
        if (!$this->validateBeforeStore()) {
            return;
        }

        try {
            DB::beginTransaction();
            
            $this->saveAllPayments();
            
            DB::commit();
            
            $this->showToast('success', 'Data pengembalian pinjaman berhasil disimpan!');
            return redirect()->route('sfinlog.pengembalian-pinjaman.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->cleanupTemporaryFiles();
            $this->showToast('error', 'Gagal menyimpan data: ' . $e->getMessage());
            Log::error('Pengembalian pinjaman store failed', ['error' => $e->getMessage()]);
        }
    }

    private function validateBeforeStore()
    {
        if (empty($this->id_peminjaman_finlog)) {
            $this->showToast('error', 'Silakan pilih kode peminjaman terlebih dahulu!');
            return false;
        }

        if (empty($this->pengembalianList)) {
            $this->showToast('error', 'Silakan tambahkan minimal 1 pengembalian invoice!');
            return false;
        }

        return true;
    }

    private function saveAllPayments()
    {
        $status = $this->determinePaymentStatus();
        $jatuhTempo = $this->selectedPeminjaman->rencana_tgl_pengembalian;
        
        foreach ($this->pengembalianList as $pengembalian) {
            $permanentPath = $this->moveToPermanentStorage($pengembalian['bukti_file']);
            
            PengembalianPinjamanFinlog::create([
                'id_pinjaman_finlog' => $this->id_peminjaman_finlog,
                'id_cells_project' => $this->id_cells_project,
                'id_project' => $this->id_project,
                'jumlah_pengembalian' => $pengembalian['nominal'],
                'sisa_pinjaman' => $this->sisa_utang,
                'sisa_bagi_hasil' => $this->sisa_bagi_hasil,
                'total_sisa_pinjaman' => $this->sisa_utang + $this->sisa_bagi_hasil,
                'tanggal_pengembalian' => now(),
                'bukti_pembayaran' => $permanentPath,
                'jatuh_tempo' => $jatuhTempo,
                'catatan' => $this->catatan,
                'status' => $status,
            ]);
        }
    }

    private function determinePaymentStatus()
    {
        $totalSisa = $this->sisa_utang + $this->sisa_bagi_hasil;
        
        if ($totalSisa <= 0) {
            return self::STATUS_LUNAS;
        }
        
        $jatuhTempo = $this->selectedPeminjaman->rencana_tgl_pengembalian;
        if ($jatuhTempo && now()->gt($jatuhTempo)) {
            return self::STATUS_TERLAMBAT;
        }
        
        return self::STATUS_BELUM_LUNAS;
    }

    private function moveToPermanentStorage($tempPath)
    {
        $filename = 'pengembalian_' . time() . '_' . uniqid() . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
        $newPath = 'pengembalian_finlog/' . $filename;
        Storage::disk('public')->move($tempPath, $newPath);
        return $newPath;
    }

    private function cleanupTemporaryFiles()
    {
        foreach ($this->pengembalianList as $pengembalian) {
            if (isset($pengembalian['bukti_file'])) {
                Storage::disk('public')->delete($pengembalian['bukti_file']);
            }
        }
    }

    private function sanitizeCurrency($value)
    {
        if (empty($value)) {
            return 0;
        }
        
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    private function showToast($type, $message)
    {
        $this->dispatch('show-toast', ['type' => $type, 'message' => $message]);
    }

    public function render()
    {
        return view('livewire.sfinlog.pengembalian-pinjaman.create', [
            'peminjamanList' => $this->getPeminjamanList(),
        ]);
    }

    private function getPeminjamanList()
    {
        if (!$this->currentDebitur) {
            return [];
        }

        return PeminjamanFinlog::query()
            ->with(['debitur', 'cellsProject'])
            ->where('id_debitur', $this->currentDebitur->id_debitur)
            ->where('status', 'Selesai')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($item) => (object)[
                'id' => $item->id_peminjaman_finlog,
                'text' => $item->nomor_peminjaman,
            ])
            ->all();
    }
}
