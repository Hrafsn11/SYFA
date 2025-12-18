<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Attributes\FieldInput;
use App\Livewire\Traits\HasModal;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Models\PeminjamanFinlog;
use App\Models\PengembalianPinjamanFinlog;
use App\Models\MasterDebiturDanInvestor;
use App\Http\Requests\SFinlog\PengembalianPinjamanFinlogRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PengembalianPinjamanFinlogCreate extends Component
{
    use WithFileUploads, HasModal, HasValidate, HasUniversalFormAction;

    public $validateClass = PengembalianPinjamanFinlogRequest::class;

    // Form fields
    #[FieldInput]
    public string $id_pinjaman_finlog = '';

    #[Locked]
    public string $nama_perusahaan = '';

    #[FieldInput] public string $cells_bisnis = '';
    #[FieldInput] public string $nama_project = '';
    #[FieldInput] public string $tanggal_pencairan = '';
    #[FieldInput] public string $top = '';
    #[FieldInput] public string $jatuh_tempo = '';
    #[FieldInput] public float $nilai_pinjaman = 0;
    #[FieldInput] public float $nilai_bagi_hasil = 0;
    #[FieldInput] public float $total_pinjaman = 0;
    #[FieldInput] public float $sisa_utang = 0;
    #[FieldInput] public float $sisa_bagi_hasil = 0;
    #[FieldInput] public string $catatan = '';

    public array $pengembalian_list = [];


    // Modal fields
    public float $nominal_yang_dibayarkan = 0;
    public $bukti_pembayaran_invoice;

    // Configuration
    protected int $maxUploadSize = 2048; // 2MB

    // State
    public string $value = '';
    public ?PeminjamanFinlog $selectedPeminjaman = null;
    public string $id_cells_project = '';
    public string $id_project = '';

    // User State
    #[Locked] public $currentUserId;
    #[Locked] public $currentDebitur;

    public bool $hasNoData = false;
    public bool $isSubmitting = false;

    // Constants
    private const STATUS_LUNAS = 'Lunas';
    private const STATUS_BELUM_LUNAS = 'Belum Lunas';
    private const STATUS_TERLAMBAT = 'Terlambat';

    public function mount()
    {
        $this->currentUserId = auth()->id();
        $this->currentDebitur = MasterDebiturDanInvestor::where('user_id', $this->currentUserId)->first();
        $this->nama_perusahaan = $this->currentDebitur->nama ?? auth()->user()->name;

        if (!$this->currentDebitur) {
            $this->hasNoData = true;
            $this->showToast('warning', 'Anda belum terdaftar sebagai debitur.');
        }

        // Setup URL Action via Trait
        $this->setUrlSaveData('url_simpan', 'sfinlog.pengembalian-pinjaman.store');
    }

    #[On('select2-changed')]
    public function onSelect2Changed($value, $modelName)
    {
        if ($modelName === 'id_pinjaman_finlog') {
            $this->id_pinjaman_finlog = $value;
            $this->value = $value;
            $this->loadPeminjamanData($value);
        }
    }

    public function updatedIdPinjamanFinlog($value)
    {
        $this->value = $value;
        $this->loadPeminjamanData($value);
    }

    public function addPengembalian()
    {
        try {
            $this->nominal_yang_dibayarkan = $this->sanitizeCurrency($this->nominal_yang_dibayarkan);


            $this->validate([
                'nominal_yang_dibayarkan' => 'required|numeric|min:1',
                'bukti_pembayaran_invoice' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ], [
                'nominal_yang_dibayarkan.required' => 'Nominal wajib diisi.',
                'nominal_yang_dibayarkan.min' => 'Nominal minimal Rp 1.',
                'bukti_pembayaran_invoice.required' => 'Bukti pembayaran wajib diunggah.',
                'bukti_pembayaran_invoice.max' => 'Ukuran file maksimal 2MB.',
            ]);

            $this->pengembalian_list[] = [
                'nominal' => $this->nominal_yang_dibayarkan,
                'bukti_file' => $this->bukti_pembayaran_invoice,
            ];

            $this->calculateRemainingBalance();
            $this->resetModalFields();

            $this->dispatch('close-pengembalian-modal');
            $this->showToast('success', 'Pengembalian invoice berhasil ditambahkan!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error adding pengembalian', ['message' => $e->getMessage()]);
            $this->showToast('error', 'Gagal menambahkan pengembalian: ' . $e->getMessage());
        }
    }

    public function removePengembalian(int $index)
    {
        array_splice($this->pengembalian_list, $index, 1);
        $this->calculateRemainingBalance();
        $this->showToast('success', 'Pengembalian invoice berhasil dihapus!');
    }

    public function store()
    {
        if (empty($this->id_pinjaman_finlog)) {
            if (!empty($this->value)) {
                $this->id_pinjaman_finlog = $this->value;
            } elseif ($this->selectedPeminjaman) {
                $this->id_pinjaman_finlog = $this->selectedPeminjaman->id_peminjaman_finlog;
            }
        }

        $this->saveData('sfinlog.pengembalian-pinjaman.store');
    }

    public function setterFormData()
    {
        if ($this->id_pinjaman_finlog) {
            $peminjaman = PeminjamanFinlog::find($this->id_pinjaman_finlog);
            if ($peminjaman) {
                $this->nilai_pinjaman = $peminjaman->nilai_pinjaman ?? 0;
                $this->nilai_bagi_hasil = $peminjaman->nilai_bagi_hasil ?? 0;
                $this->selectedPeminjaman = $peminjaman;
            }
        }

        $payloadList = [];
        $totalPaidSoFar = PengembalianPinjamanFinlog::where('id_pinjaman_finlog', $this->id_pinjaman_finlog)
            ->sum('jumlah_pengembalian');

        foreach ($this->pengembalian_list as $payment) {
            $nominal = $payment['nominal'];
            $totalPaidSoFar += $nominal;

            // Calculate state AFTER this specific payment
            $initialBagiHasil = $this->nilai_bagi_hasil;
            $initialUtang = $this->nilai_pinjaman;

            $paidToBagiHasil = min($totalPaidSoFar, $initialBagiHasil);
            $remainderForUtang = max(0, $totalPaidSoFar - $initialBagiHasil);
            $paidToUtang = min($remainderForUtang, $initialUtang);

            $sisaBagiHasil = max(0, $initialBagiHasil - $paidToBagiHasil);
            $sisaUtang = max(0, $initialUtang - $paidToUtang);
            $totalSisa = $sisaBagiHasil + $sisaUtang;

            $status = $this->determineStatus($totalSisa);

            $payloadList[] = [
                'nominal' => $nominal,
                'bukti_file' => $payment['bukti_file'],
                'id_cells_project' => $this->id_cells_project,
                'id_project' => $this->id_project,
                'sisa_pinjaman' => $sisaUtang,
                'sisa_bagi_hasil' => $sisaBagiHasil,
                'total_sisa_pinjaman' => $totalSisa,
                'jatuh_tempo' => $this->selectedPeminjaman->rencana_tgl_pengembalian,
                'catatan' => $this->catatan,
                'status' => $status,
            ];
        }

        $this->form_data = [
            'id_pinjaman_finlog' => $this->id_pinjaman_finlog,
            'pengembalian_list' => $payloadList
        ];
    }

    public function afterSave($payload)
    {
        if ($payload && isset($payload->error) && $payload->error === false) {
            $data = $payload->data ?? [];
            $data = is_array($data) ? $data : (array) $data;

            session()->flash('success', $payload->message ?? 'Berhasil disimpan');

            $redirectUrl = $data['redirect'] ?? route('sfinlog.pengembalian-pinjaman.index');
            return redirect()->to($redirectUrl);
        }
    }

    public function render()
    {
        return view('livewire.sfinlog.pengembalian-pinjaman.create', [
            'peminjamanList' => $this->getPeminjamanList(),
        ]);
    }

    public function loadPeminjamanData($id)
    {
        if (empty($id)) {
            $this->resetPeminjamanData();
            return;
        }

        $peminjaman = PeminjamanFinlog::with(['debitur', 'cellsProject.projects'])
            ->where('id_peminjaman_finlog', $id)
            ->where('status', 'Selesai')
            ->first();

        if (!$peminjaman) {
            $this->resetPeminjamanData();
            $this->showToast('error', 'Data peminjaman tidak valid.');
            return;
        }

        $this->selectedPeminjaman = $peminjaman;
        $this->populateFormFields($peminjaman);
        $this->calculateRemainingBalance();
    }

    private function populateFormFields(PeminjamanFinlog $peminjaman)
    {
        $this->cells_bisnis = $peminjaman->cellsProject?->nama_cells_bisnis ?? '-';
        $this->id_cells_project = $peminjaman->id_cells_project ?? '';
        $this->id_project = $peminjaman->nama_project ?? '';
        $this->nama_project = $this->resolveProjectName($peminjaman);
        $this->tanggal_pencairan = $peminjaman->harapan_tanggal_pencairan?->format('d/m/Y') ?? '-';
        $this->jatuh_tempo = $peminjaman->rencana_tgl_pengembalian?->format('d/m/Y') ?? '-';
        $this->top = $peminjaman->top ?? '0';
        $this->nilai_pinjaman = $peminjaman->nilai_pinjaman ?? 0;
        $this->nilai_bagi_hasil = $peminjaman->nilai_bagi_hasil ?? 0;
        $this->total_pinjaman = $peminjaman->total_pinjaman ?? 0;
    }

    private function resolveProjectName(PeminjamanFinlog $peminjaman): string
    {
        if (!$peminjaman->cellsProject || empty($peminjaman->nama_project)) return '-';

        $projects = $peminjaman->cellsProject->projects ?? collect();
        $project = $projects->firstWhere('id_project', $peminjaman->nama_project)
            ?? $projects->firstWhere('nama_project', $peminjaman->nama_project)
            ?? \App\Models\Project::where('id_project', $peminjaman->nama_project)->orWhere('nama_project', $peminjaman->nama_project)->first();

        if ($project) {
            $this->id_project = $project->id_project;
            return $project->nama_project;
        }

        return '-';
    }

    private function getPeminjamanList(): array
    {
        if (!$this->currentDebitur) return [];

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

    private function calculateRemainingBalance()
    {
        if (empty($this->id_pinjaman_finlog)) {
            $this->resetBalanceFields();
            return;
        }

        $dbPayments = PengembalianPinjamanFinlog::where('id_pinjaman_finlog', $this->id_pinjaman_finlog)
            ->sum('jumlah_pengembalian');

        $newPayments = array_sum(array_column($this->pengembalian_list, 'nominal'));
        $totalPaid = $dbPayments + $newPayments;

        $initialBagiHasil = $this->nilai_bagi_hasil;
        $initialUtang = $this->nilai_pinjaman;

        $paidToBagiHasil = min($totalPaid, $initialBagiHasil);
        $remainderForUtang = max(0, $totalPaid - $initialBagiHasil);
        $paidToUtang = min($remainderForUtang, $initialUtang);

        $this->sisa_bagi_hasil = max(0, $initialBagiHasil - $paidToBagiHasil);
        $this->sisa_utang = max(0, $initialUtang - $paidToUtang);
    }

    private function determineStatus($totalSisa)
    {
        if ($totalSisa <= 0) return self::STATUS_LUNAS;

        $jatuhTempo = $this->selectedPeminjaman->rencana_tgl_pengembalian;
        if ($jatuhTempo && now()->gt($jatuhTempo)) return self::STATUS_TERLAMBAT;

        return self::STATUS_BELUM_LUNAS;
    }

    private function resetPeminjamanData()
    {
        $this->reset([
            'selectedPeminjaman',
            'cells_bisnis',
            'nama_project',
            'tanggal_pencairan',
            'top',
            'jatuh_tempo',
            'nilai_pinjaman',
            'nilai_bagi_hasil',
            'total_pinjaman',
            'id_cells_project',
            'id_project'
        ]);
        $this->resetBalanceFields();
    }

    private function resetBalanceFields()
    {
        $this->sisa_utang = 0;
        $this->sisa_bagi_hasil = 0;
    }

    private function resetModalFields()
    {
        $this->reset(['nominal_yang_dibayarkan', 'bukti_pembayaran_invoice']);
    }

    private function sanitizeCurrency($value)
    {
        if (is_numeric($value)) return (float) $value;
        return (float) preg_replace('/[^0-9]/', '', $value);
    }

    private function showToast($type, $message)
    {
        $this->dispatch('alert', ['icon' => $type, 'html' => $message]);
    }
}
