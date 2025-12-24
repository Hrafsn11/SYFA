<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PengajuanRestrukturisasi;
use App\Models\ProgramRestrukturisasi;
use App\Models\JadwalAngsuran;
use App\Http\Requests\ProgramRestrukturisasiRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProgramRestrukturisasiCreate extends Component
{
    use WithFileUploads;

    public bool $isEdit = false;
    public string $pageTitle = 'Form Tambah Jenis Program Restrukturisasi';
    public string $pageSubtitle = 'Buat program restrukturisasi berdasarkan pengajuan yang telah disetujui';
    public string $submitLabel = 'Simpan Program Restrukturisasi';

    // Form fields
    public $id_pengajuan_restrukturisasi = '';
    public $nama_debitur = '';
    public $nomor_kontrak = '';
    public $metode_perhitungan = 'Flat';
    public $plafon_pembiayaan = 0;
    public $suku_bunga_per_tahun = 12;
    public $jangka_waktu_total = 12;
    public $masa_tenggang = 2;
    public $tanggal_mulai_cicilan;

    // Calculated data
    public $jadwal_angsuran = [];
    public $total_pokok = 0;
    public $total_margin = 0;
    public $total_cicilan = 0;
    public $show_jadwal = false;

    // Options
    public $approvedRestrukturisasi = [];

    public function mount(?string $id = null): void
    {
        $this->tanggal_mulai_cicilan = $this->tanggal_mulai_cicilan ?? date('Y-m-d');
        $this->show_jadwal = false;
        $this->jadwal_angsuran = [];
        $this->loadApprovedRestrukturisasi($id);
    }

    protected function approvalStatuses(): array
    {
        return ['Selesai', 'Disetujui CEO SKI', 'Disetujui Direktur SKI'];
    }

    protected function loadApprovedRestrukturisasi(?string $forceIncludeId = null): void
    {
        $query = PengajuanRestrukturisasi::with('debitur')
            ->where(function ($q) {
                $q->whereIn('status', $this->approvalStatuses());
            })
            ->whereNotNull('sisa_pokok_belum_dibayar')
            ->where('sisa_pokok_belum_dibayar', '>', 0);

        if ($forceIncludeId) {
            $query->orWhere('id_pengajuan_restrukturisasi', $forceIncludeId);
        }

        $this->approvedRestrukturisasi = $query->orderBy('created_at', 'desc')->get();
    }


    public function updatedIdPengajuanRestrukturisasi()
    {
        $this->loadPengajuanData();
    }

    /**
     * Load data pengajuan restrukturisasi
     * Called when pengajuan selection changes
     */
    public function loadPengajuanData()
    {
        if ($this->id_pengajuan_restrukturisasi) {
            $restrukturisasi = PengajuanRestrukturisasi::with('debitur')
                ->find($this->id_pengajuan_restrukturisasi);

            if ($restrukturisasi) {
                $this->nama_debitur = $restrukturisasi->debitur
                    ? $restrukturisasi->debitur->nama
                    : $restrukturisasi->nama_perusahaan;
                $this->nomor_kontrak = $restrukturisasi->nomor_kontrak_pembiayaan;
                $this->plafon_pembiayaan = (float) $restrukturisasi->sisa_pokok_belum_dibayar;
            }
        } else {
            $this->nama_debitur = '';
            $this->nomor_kontrak = '';
            $this->plafon_pembiayaan = 0;
        }

        $this->show_jadwal = false;
        $this->jadwal_angsuran = [];
    }


    public function updated($propertyName)
    {
        $calculationFields = [
            'plafon_pembiayaan',
            'suku_bunga_per_tahun',
            'jangka_waktu_total',
            'masa_tenggang',
            'tanggal_mulai_cicilan',
            'metode_perhitungan'
        ];

        if (in_array($propertyName, $calculationFields)) {
            $this->show_jadwal = false;
            $this->jadwal_angsuran = [];
            $this->total_pokok = 0;
            $this->total_margin = 0;
            $this->total_cicilan = 0;
        }

        if ($propertyName === 'id_pengajuan_restrukturisasi') {
            $this->updatedIdPengajuanRestrukturisasi();
        }
    }

    /**
     * Hitung jadwal angsuran berdasarkan metode perhitungan
     * 
     * @return void
     */
    public function hitungJadwalAngsuran()
    {
        // VALIDATION GUARD: Pengajuan harus dipilih terlebih dahulu
        if (empty($this->id_pengajuan_restrukturisasi)) {
            $this->addError('id_pengajuan_restrukturisasi', 'Silakan pilih Pengajuan Restrukturisasi terlebih dahulu.');
            return;
        }

        // Sanitasi Input
        $this->plafon_pembiayaan = (float) $this->plafon_pembiayaan;
        $this->suku_bunga_per_tahun = (float) $this->suku_bunga_per_tahun;
        $this->jangka_waktu_total = (int) $this->jangka_waktu_total;
        $this->masa_tenggang = (int) $this->masa_tenggang;

        // Validate calculation parameters
        try {
            $this->validateCalculationParameters();
        } catch (ValidationException $e) {
            // Validation errors already added by validate()
            return;
        }

        // Validasi Logika Masa Tenggang
        if ($this->masa_tenggang >= $this->jangka_waktu_total) {
            $this->addError('masa_tenggang', 'Masa tenggang tidak boleh melebihi atau sama dengan jangka waktu total!');
            $this->show_jadwal = false;
            return;
        }

        // [LOGIC PERHITUNGAN DISINI]
        if ($this->metode_perhitungan === 'Flat') {
            $this->calculateFlatMethod();
        } elseif ($this->metode_perhitungan === 'Efektif (Anuitas)') {
            $this->calculateEfektifAnuitasMethod();
        }

        $this->show_jadwal = true;

        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'Jadwal angsuran berhasil dihitung! Silakan simpan.',
        ]);
    }

    // [UPDATE] Metode Flat dengan Sisa Pinjaman
    // [KEMBALI KE VERSI LAMA - BERSIH]
    private function calculateFlatMethod()
    {
        $marginPerBulan = ($this->plafon_pembiayaan * ($this->suku_bunga_per_tahun / 100)) / 12;
        $bulanEfektifAnuitas = $this->jangka_waktu_total - $this->masa_tenggang;
        $pokokPerBulan = $bulanEfektifAnuitas > 0 ? $this->plafon_pembiayaan / $bulanEfektifAnuitas : 0;

        $jadwal = [];
        $totalPokok = 0;
        $totalMargin = 0;
        $totalCicilan = 0;
        $startDate = \Carbon\Carbon::parse($this->tanggal_mulai_cicilan);

        for ($i = 1; $i <= $this->jangka_waktu_total; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            $pokok = 0;
            $margin = $marginPerBulan;
            $catatan = '';

            if ($i <= $this->masa_tenggang) {
                $pokok = 0;
                $catatan = 'Masa Tenggang - Hanya Bayar Margin';
            } else {
                $pokok = $pokokPerBulan;
            }

            $total = $pokok + $margin;
            $totalPokok += $pokok;
            $totalMargin += $margin;
            $totalCicilan += $total;

            $jadwal[] = [
                'no' => $i,
                'tanggal_jatuh_tempo' => $dueDate->format('d F Y'),
                'tanggal_jatuh_tempo_raw' => $dueDate->format('Y-m-d'),
                // TIDAK ADA KEY 'sisa_pinjaman' DISINI
                'pokok' => $pokok,
                'margin' => $margin,
                'total_cicilan' => $total,
                'catatan' => $catatan,
                'is_grace_period' => $i <= $this->masa_tenggang,
            ];
        }

        $this->jadwal_angsuran = $jadwal;
        $this->total_pokok = $totalPokok;
        $this->total_margin = $totalMargin;
        $this->total_cicilan = $totalCicilan;
    }

    // [METODE Efektif (Anuitas) - ADA SISA PINJAMAN]
    private function calculateEfektifAnuitasMethod()
    {
        $bungaPerBulan = ($this->suku_bunga_per_tahun / 100) / 12;
        $sisaPokok = $this->plafon_pembiayaan;
        $bulanEfektifAnuitas = $this->jangka_waktu_total - $this->masa_tenggang;

        $cicilanEfektifAnuitasTetap = 0;
        if ($bulanEfektifAnuitas > 0 && $bungaPerBulan > 0) {
            $penyebut = 1 - pow(1 + $bungaPerBulan, -$bulanEfektifAnuitas);
            $cicilanEfektifAnuitasTetap = $sisaPokok * ($bungaPerBulan / $penyebut);
        }

        $jadwal = [];
        $totalPokok = 0;
        $totalMargin = 0;
        $totalCicilan = 0;
        $startDate = \Carbon\Carbon::parse($this->tanggal_mulai_cicilan);

        for ($i = 1; $i <= $this->jangka_waktu_total; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            $isGracePeriod = $i <= $this->masa_tenggang;

            // Simpan Sisa Pinjaman Awal Bulan untuk Ditampilkan
            $sisaPinjamanDisplay = $sisaPokok;

            $pokok = 0;
            $margin = 0;
            $total = 0;
            $catatan = '';

            if ($isGracePeriod) {
                $pokok = 0;
                $margin = $sisaPokok * $bungaPerBulan;
                $total = $margin;
                $catatan = 'Masa Tenggang - Hanya Bayar Margin';
            } else {
                $margin = $sisaPokok * $bungaPerBulan;

                if ($i == $this->jangka_waktu_total) {
                    $pokok = $sisaPokok;
                    $total = $pokok + $margin;
                } else {
                    $total = $cicilanEfektifAnuitasTetap;
                    $pokok = $total - $margin;
                }

                $sisaPokok -= $pokok;
                if ($sisaPokok < 0) $sisaPokok = 0;
            }

            $totalPokok += $pokok;
            $totalMargin += $margin;
            $totalCicilan += $total;

            $jadwal[] = [
                'no' => $i,
                'tanggal_jatuh_tempo' => $dueDate->format('d F Y'),
                'tanggal_jatuh_tempo_raw' => $dueDate->format('Y-m-d'),
                'sisa_pinjaman' => $sisaPinjamanDisplay, // <--- HANYA ADA DI Efektif (Anuitas)
                'pokok' => $pokok,
                'margin' => $margin,
                'total_cicilan' => $total,
                'catatan' => $catatan,
                'is_grace_period' => $isGracePeriod,
            ];
        }

        $this->jadwal_angsuran = $jadwal;
        $this->total_pokok = $totalPokok;
        $this->total_margin = $totalMargin;
        $this->total_cicilan = $totalCicilan;
    }

    /**
     * Simpan program restrukturisasi beserta jadwal angsuran
     * 
     * @return void
     */
    public function simpan()
    {
        // VALIDATION GUARD: Jadwal harus sudah dihitung
        if (empty($this->jadwal_angsuran) || !$this->show_jadwal) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Belum Dihitung',
                'text' => 'Silakan klik tombol "Hitung Jadwal Angsuran" terlebih dahulu.',
            ]);
            return;
        }

        // Validate using FormRequest rules directly
        try {
            $request = new ProgramRestrukturisasiRequest();
            $this->validate(
                $request->rules(),
                $request->messages(),
                $request->attributes()
            );

            // Additional business logic validation (since passedValidation doesn't work with Livewire)
            if ($this->masa_tenggang >= $this->jangka_waktu_total) {
                $this->addError('masa_tenggang', 'Masa tenggang tidak boleh lebih dari atau sama dengan jangka waktu total.');
                throw ValidationException::withMessages([
                    'masa_tenggang' => 'Masa tenggang tidak boleh lebih dari atau sama dengan jangka waktu total.',
                ]);
            }

            // Verify totals match
            $sumPokok = array_sum(array_column($this->jadwal_angsuran, 'pokok'));
            $sumMargin = array_sum(array_column($this->jadwal_angsuran, 'margin'));
            $tolerance = 1;

            if (abs($sumPokok - $this->total_pokok) > $tolerance) {
                throw ValidationException::withMessages([
                    'total_pokok' => 'Total pokok tidak sesuai dengan jumlah angsuran. Silakan hitung ulang.',
                ]);
            }

            if (abs($sumMargin - $this->total_margin) > $tolerance) {
                throw ValidationException::withMessages([
                    'total_margin' => 'Total margin tidak sesuai dengan jumlah angsuran. Silakan hitung ulang.',
                ]);
            }
        } catch (ValidationException $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Gagal Menyimpan',
                'text' => implode("\n", collect($e->errors())->flatten()->toArray()),
            ]);
            return; // Don't throw, just return
        }

        try {
            DB::beginTransaction();

            // Sanitasi metode_perhitungan
            $metodeValid = trim($this->metode_perhitungan);
            if (!in_array($metodeValid, ['Flat', 'Efektif (Anuitas)'])) {
                throw new \Exception('Metode perhitungan tidak valid: ' . $metodeValid);
            }

            $program = ProgramRestrukturisasi::create([
                'id_pengajuan_restrukturisasi' => $this->id_pengajuan_restrukturisasi,
                'metode_perhitungan' => $metodeValid,
                'plafon_pembiayaan' => (float) $this->plafon_pembiayaan,
                'suku_bunga_per_tahun' => (float) $this->suku_bunga_per_tahun,
                'jangka_waktu_total' => (int) $this->jangka_waktu_total,
                'masa_tenggang' => (int) $this->masa_tenggang,
                'tanggal_mulai_cicilan' => $this->tanggal_mulai_cicilan,
                'total_pokok' => (float) $this->total_pokok,
                'total_margin' => (float) $this->total_margin,
                'total_cicilan' => (float) $this->total_cicilan,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            foreach ($this->jadwal_angsuran as $item) {
                JadwalAngsuran::create([
                    'id_program_restrukturisasi' => $program->id_program_restrukturisasi,
                    'no' => $item['no'],
                    'tanggal_jatuh_tempo' => \Carbon\Carbon::parse($item['tanggal_jatuh_tempo_raw']),
                    'pokok' => $item['pokok'],
                    'margin' => $item['margin'],
                    'total_cicilan' => $item['total_cicilan'],
                    'catatan' => $item['catatan'],
                    'is_grace_period' => $item['is_grace_period'],
                    'status' => 'Belum Jatuh Tempo',
                ]);
            }

            DB::commit();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Program restrukturisasi berhasil disimpan!',
                'redirect_url' => route('program-restrukturisasi.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving: ' . $e->getMessage());
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'System Error',
                'text' => 'Gagal menyimpan: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Validate calculation parameters
     * 
     * @return void
     * @throws ValidationException
     */
    protected function validateCalculationParameters(): void
    {
        $this->validate([
            'plafon_pembiayaan' => 'required|numeric|min:1',
            'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
            'jangka_waktu_total' => 'required|integer|min:1|max:360',
            'masa_tenggang' => 'required|integer|min:0|max:36',
            'tanggal_mulai_cicilan' => 'required|date|after_or_equal:today',
        ], [
            'plafon_pembiayaan.required' => 'Plafon pembiayaan wajib diisi.',
            'plafon_pembiayaan.min' => 'Plafon pembiayaan harus lebih dari 0.',
            'suku_bunga_per_tahun.max' => 'Suku bunga tidak boleh lebih dari 100%.',
            'jangka_waktu_total.min' => 'Jangka waktu minimal 1 bulan.',
            'jangka_waktu_total.max' => 'Jangka waktu maksimal 360 bulan (30 tahun).',
            'masa_tenggang.max' => 'Masa tenggang maksimal 36 bulan (3 tahun).',
            'tanggal_mulai_cicilan.after_or_equal' => 'Tanggal mulai cicilan tidak boleh kurang dari hari ini.',
        ]);

        // Additional business logic validation
        if ($this->masa_tenggang >= $this->jangka_waktu_total) {
            throw ValidationException::withMessages([
                'masa_tenggang' => 'Masa tenggang tidak boleh lebih dari atau sama dengan jangka waktu total.',
            ]);
        }
    }

    /**
     * Check if program can be calculated
     * 
     * @return bool
     */
    public function getCanCalculateProperty(): bool
    {
        return !empty($this->id_pengajuan_restrukturisasi)
            && $this->plafon_pembiayaan > 0
            && $this->suku_bunga_per_tahun >= 0
            && $this->jangka_waktu_total > 0
            && !empty($this->tanggal_mulai_cicilan);
    }

    /**
     * Check if program can be saved
     * 
     * @return bool
     */
    public function getCanSaveProperty(): bool
    {
        return !empty($this->jadwal_angsuran)
            && $this->show_jadwal
            && !empty($this->id_pengajuan_restrukturisasi);
    }

    public function render()
    {
        return view('livewire.program-restrukturisasi')->layout('layouts.app');
    }
}
