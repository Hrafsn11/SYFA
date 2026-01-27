<?php

namespace App\Livewire\ProgramRestrukturisasi;

use App\Models\ProgramRestrukturisasi;
use App\Models\JadwalAngsuran;
use App\Models\RiwayatPembayaranRestrukturisasi;
use App\Helpers\ListNotifSFinance;
use App\Models\PengajuanRestrukturisasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Edit extends Create
{
    // ==========================================
    // CONSTANTS
    // ==========================================

    private const STATUS_BELUM_JATUH_TEMPO = 'Belum Jatuh Tempo';
    private const STATUS_JATUH_TEMPO = 'Jatuh Tempo';
    private const STATUS_TERTUNDA = 'Tertunda';
    private const STATUS_DIBAYAR_SEBAGIAN = 'Dibayar Sebagian';
    private const STATUS_LUNAS = 'Lunas';
    private const STATUS_PROGRAM_BERJALAN = 'Berjalan';

    // ==========================================
    // PROPERTIES - Core
    // ==========================================

    public bool $isEdit = true;
    public ProgramRestrukturisasi $program;
    public PengajuanRestrukturisasi $pengajuanRestrukturisasi;

    public string $pageTitle = 'Aksi Program Restrukturisasi';
    public string $pageSubtitle = 'Perbarui parameter program restrukturisasi';
    public string $submitLabel = 'Perbarui Restrukturisasi';


    // ==========================================
    // PROPERTIES - Modal Upload
    // ==========================================

    public $buktiPembayaranFiles = [];
    public $showUploadModal = false;
    public $selectedAngsuranIndex = null;
    public $selectedAngsuranNo = null;
    public $uploadFile = null;
    public $uploadNominalBayar = 0;
    public $isEditingFile = false;
    public $maxNominalBayar = 0; // Maksimal nominal yang bisa dibayar (sisa pembayaran)
    public $viewOnlyMode = false; // True = hanya lihat riwayat, False = bisa upload

    // ==========================================
    // PROPERTIES - Modal Konfirmasi
    // ==========================================

    public $showKonfirmasiModal = false;
    public $selectedKonfirmasiIndex = null;
    public $selectedKonfirmasiNo = null;
    public $catatanKonfirmasi = '';
    public $showTolakModal = false;

    // ==========================================
    // PROPERTIES - Riwayat Pembayaran
    // ==========================================

    public $riwayatPembayaran = []; // Riwayat pembayaran per angsuran yang dipilih
    public $selectedRiwayatId = null; // ID riwayat yang akan dikonfirmasi
    public $selectedRiwayat = null; // Data riwayat yang akan dikonfirmasi


    // ==========================================
    // LIFECYCLE METHODS
    // ==========================================

    public function mount(?string $id = null): void
    {
        abort_unless(auth()->user()->can('program_restrukturisasi.edit'), 403, 'Unauthorized');

        $this->tanggal_mulai_cicilan = date('Y-m-d');

        abort_if($id === null, 404);

        $this->program = ProgramRestrukturisasi::with([
            'pengajuanRestrukturisasi.debitur',
            'jadwalAngsuran' => fn($query) => $query->orderBy('no'),
        ])->findOrFail($id);

        $this->pengajuanRestrukturisasi = $this->program->pengajuanRestrukturisasi;
        if (in_array('Pengurangan tunggakan pokok/margin', $this->pengajuanRestrukturisasi->jenis_restrukturisasi)) {
            $this->specialCase = false;
        }

        // Authorization check: Debitur hanya bisa edit data miliknya
        $this->authorizeDebiturAccess();
        $this->initializeFromProgram();
        $this->jadwal_angsuran = $this->mapJadwalAngsuran($this->program->jadwalAngsuran);
        $this->show_jadwal = true;
        $this->loadApprovedRestrukturisasi($this->id_pengajuan_restrukturisasi);
    }

    public function render()
    {
        return view('livewire.program-restrukturisasi.edit')->layout('layouts.app');
    }

    // ==========================================
    // DATA INITIALIZATION
    // ==========================================

    private function authorizeDebiturAccess(): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasRole(['super-admin', 'admin', 'sfinance', 'Finance SKI']);

        if ($isAdmin)
            return;

        $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();
        $pengajuanDebiturId = $this->program->pengajuanRestrukturisasi->id_debitur ?? null;

        abort_if(!$debitur || $debitur->id_debitur !== $pengajuanDebiturId, 403, 'Anda tidak memiliki akses untuk mengedit data ini.');
    }

    private function initializeFromProgram(): void
    {
        $pengajuan = $this->program->pengajuanRestrukturisasi;

        $this->id_pengajuan_restrukturisasi = $this->program->id_pengajuan_restrukturisasi;
        $this->nama_debitur = $pengajuan?->debitur?->nama ?? $pengajuan?->nama_perusahaan ?? '-';
        $this->nomor_kontrak = $pengajuan?->nomor_kontrak_pembiayaan ?? '-';
        $this->metode_perhitungan = $this->program->metode_perhitungan;

        $this->plafon_pembiayaan = (float) $this->program->plafon_pembiayaan;
        $this->suku_bunga_per_tahun = (float) $this->program->suku_bunga_per_tahun;
        $this->jangka_waktu_total = (int) $this->program->jangka_waktu_total;
        $this->nominal_yg_disetujui = (int) $this->program->nominal_yg_disetujui;
        $this->masa_tenggang = (int) $this->program->masa_tenggang;
        $this->tanggal_mulai_cicilan = optional($this->program->tanggal_mulai_cicilan)->format('Y-m-d');

        $this->total_pokok = (float) $this->program->total_pokok;
        $this->total_margin = (float) $this->program->total_margin;
        $this->total_cicilan = (float) $this->program->total_cicilan;
    }

    protected function loadData(): void
    {
        $this->program->refresh();
        $this->program->load(['jadwalAngsuran' => fn($query) => $query->orderBy('no')]);
        $this->jadwal_angsuran = $this->mapJadwalAngsuran($this->program->jadwalAngsuran);
    }

    private function mapJadwalAngsuran($jadwalCollection): array
    {
        $sisaPokok = $this->plafon_pembiayaan;

        return $jadwalCollection->map(function ($item) use (&$sisaPokok) {
            // Hitung total pembayaran tertunda (belum dikonfirmasi)
            $totalTertunda = $item->riwayatPembayaran()
                ->where('status', RiwayatPembayaranRestrukturisasi::STATUS_TERTUNDA)
                ->sum('nominal_bayar');

            // Hitung jumlah riwayat pembayaran tertunda
            $jumlahTertunda = $item->riwayatPembayaran()
                ->where('status', RiwayatPembayaranRestrukturisasi::STATUS_TERTUNDA)
                ->count();

            $data = [
                'id' => $item->id_jadwal_angsuran,
                'no' => $item->no,
                'tanggal_jatuh_tempo' => optional($item->tanggal_jatuh_tempo)->format('d F Y') ?? '-',
                'tanggal_jatuh_tempo_raw' => optional($item->tanggal_jatuh_tempo)->format('Y-m-d'),
                'pokok' => (float) $item->pokok,
                'margin' => (float) $item->margin,
                'total_cicilan' => (float) $item->total_cicilan,
                'catatan' => $item->catatan,
                'is_grace_period' => (bool) $item->is_grace_period,
                'status' => $item->status,
                'bukti_pembayaran' => $item->bukti_pembayaran,
                'tanggal_bayar' => $item->tanggal_bayar ? optional($item->tanggal_bayar)->format('Y-m-d') : null,
                'nominal_bayar' => (float) $item->nominal_bayar,
                'total_terbayar' => (float) $item->total_terbayar,
                'sisa_pembayaran' => max(0, (float) $item->total_cicilan - (float) $item->total_terbayar),
                'total_tertunda' => (float) $totalTertunda,
                'jumlah_tertunda' => $jumlahTertunda,
            ];

            if ($data['sisa_pembayaran'] <= 1 && $data['status'] !== self::STATUS_LUNAS) {
                $data['status'] = self::STATUS_LUNAS;
                $item->update(['status' => self::STATUS_LUNAS]);
            }

            if ($this->metode_perhitungan === 'Efektif (Anuitas)') {
                $data['sisa_pinjaman'] = $sisaPokok;
                if (!$item->is_grace_period) {
                    $sisaPokok = max(0, $sisaPokok - (float) $item->pokok);
                }
            }

            return $data;
        })->toArray();
    }

    // ==========================================
    // JADWAL ANGSURAN
    // ==========================================

    public function hitungJadwalAngsuran(): void
    {
        $preservedData = $this->preserveJadwalData();
        parent::hitungJadwalAngsuran();
        $this->restoreJadwalData($preservedData);
    }

    private function preserveJadwalData(): array
    {
        $data = [];
        foreach ($this->jadwal_angsuran as $item) {
            $data[$item['no']] = [
                'id' => $item['id'] ?? null,
                'status' => $item['status'] ?? self::STATUS_BELUM_JATUH_TEMPO,
                'bukti_pembayaran' => $item['bukti_pembayaran'] ?? null,
                'tanggal_bayar' => $item['tanggal_bayar'] ?? null,
                'nominal_bayar' => $item['nominal_bayar'] ?? 0,
            ];
        }
        return $data;
    }

    private function restoreJadwalData(array $preservedData): void
    {
        foreach ($this->jadwal_angsuran as $index => $item) {
            $no = $item['no'];
            if (isset($preservedData[$no])) {
                $this->jadwal_angsuran[$index] = array_merge($this->jadwal_angsuran[$index], $preservedData[$no]);
            } else {
                $this->jadwal_angsuran[$index]['id'] = null;
                $this->jadwal_angsuran[$index]['status'] = self::STATUS_BELUM_JATUH_TEMPO;
                $this->jadwal_angsuran[$index]['bukti_pembayaran'] = null;
                $this->jadwal_angsuran[$index]['tanggal_bayar'] = null;
                $this->jadwal_angsuran[$index]['nominal_bayar'] = 0;
            }
        }
    }

    // ==========================================
    // SIMPAN
    // ==========================================

    public function simpan(): void
    {
        if (empty($this->jadwal_angsuran) || !$this->show_jadwal) {
            $this->showError('Belum Dihitung', 'Silakan klik tombol "Hitung Jadwal Angsuran" terlebih dahulu.');
            return;
        }

        $khususPenguranganTunggakanPokok = in_array('Pengurangan tunggakan pokok/margin', $this->pengajuanRestrukturisasi->jenis_restrukturisasi);

        try {
            $this->validateInput();
        } catch (ValidationException $e) {
            $this->showError('Gagal Menyimpan', implode("\n", collect($e->errors())->flatten()->toArray()));
            throw $e;
        }

        try {
            DB::beginTransaction();
            $this->updateProgram();
            $this->recreateJadwalAngsuran();
            DB::commit();

            $this->showSuccess('Berhasil', 'Program restrukturisasi berhasil diperbarui!', route('program-restrukturisasi.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating: ' . $e->getMessage());
            $this->showError('System Error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    private function validateInput(): void
    {
        $khususPenguranganTunggakanPokok = in_array('Pengurangan tunggakan pokok/margin', $this->pengajuanRestrukturisasi->jenis_restrukturisasi);

        if ($khususPenguranganTunggakanPokok) {
            $validate = [
                'id_pengajuan_restrukturisasi' => 'required|exists:pengajuan_restrukturisasi,id_pengajuan_restrukturisasi',
                'plafon_pembiayaan' => 'required|numeric|min:0',
                'jangka_waktu_total' => 'required|integer|min:1',
                'nominal_yg_disetujui' => 'required|integer|min:1',
                'jadwal_angsuran' => 'required|array|min:1',
            ];
        } else {
            $validate = [
                'id_pengajuan_restrukturisasi' => 'required|exists:pengajuan_restrukturisasi,id_pengajuan_restrukturisasi',
                'metode_perhitungan' => 'required|in:Flat,Efektif (Anuitas)',
                'plafon_pembiayaan' => 'required|numeric|min:0',
                'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
                'jangka_waktu_total' => 'required|integer|min:1',
                'masa_tenggang' => 'required|integer|min:0',
                'tanggal_mulai_cicilan' => 'required|date',
                'jadwal_angsuran' => 'required|array|min:1',
            ];
        }

        $this->validate($validate, [
            'id_pengajuan_restrukturisasi.required' => 'Silakan pilih pengajuan restrukturisasi.',
            'suku_bunga_per_tahun.max' => 'Suku bunga tidak boleh lebih dari 100%.',
            'jadwal_angsuran.required' => 'Mohon hitung jadwal angsuran sebelum menyimpan.',
        ]);
    }

    private function updateProgram(): void
    {
        $khususPenguranganTunggakanPokok = in_array('Pengurangan tunggakan pokok/margin', $this->pengajuanRestrukturisasi->jenis_restrukturisasi);
        $metodeValid = null;

        if (!$khususPenguranganTunggakanPokok) {
            $metodeValid = trim($this->metode_perhitungan);
            if (!in_array($metodeValid, ['Flat', 'Efektif (Anuitas)'])) {
                throw new \Exception('Metode perhitungan tidak valid: ' . $metodeValid);
            }

            $fill = [
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
                'updated_by' => Auth::id(),
            ];
        } else {
            // Kasus khusus: Pengurangan tunggakan pokok/margin
            $fill = [
                'id_pengajuan_restrukturisasi' => $this->id_pengajuan_restrukturisasi,
                'metode_perhitungan' => $metodeValid,
                'plafon_pembiayaan' => (float) $this->plafon_pembiayaan,
                'jangka_waktu_total' => (int) $this->jangka_waktu_total,
                'nominal_yg_disetujui' => (double) $this->nominal_yg_disetujui,
                'total_pokok' => (float) $this->total_pokok,
                'total_margin' => (float) $this->total_margin,
                'total_cicilan' => (float) $this->total_cicilan,
                'updated_by' => Auth::id(),
            ];
        }

        $this->program->update($fill);
    }

    private function recreateJadwalAngsuran(): void
    {
        $khususPenguranganTunggakanPokok = in_array('Pengurangan tunggakan pokok/margin', $this->pengajuanRestrukturisasi->jenis_restrukturisasi);

        $buktiMapping = $this->program->jadwalAngsuran->keyBy('no')->map(fn($item) => [
            'bukti_pembayaran' => $item->bukti_pembayaran,
            'tanggal_bayar' => $item->tanggal_bayar,
            'nominal_bayar' => $item->nominal_bayar,
            'status' => $item->status,
        ]);

        $this->program->jadwalAngsuran()->delete();

        if (!$khususPenguranganTunggakanPokok) {
            foreach ($this->jadwal_angsuran as $item) {
                $existingData = $buktiMapping->get($item['no']);

                $this->program->jadwalAngsuran()->create([
                    'no' => $item['no'],
                    'tanggal_jatuh_tempo' => \Carbon\Carbon::parse($item['tanggal_jatuh_tempo_raw']),
                    'pokok' => $item['pokok'],
                    'margin' => $item['margin'],
                    'total_cicilan' => $item['total_cicilan'],
                    'catatan' => $item['catatan'],
                    'is_grace_period' => $item['is_grace_period'] ?? false,
                    'status' => $existingData['status'] ?? ($item['status'] ?? self::STATUS_BELUM_JATUH_TEMPO),
                    'bukti_pembayaran' => $existingData['bukti_pembayaran'] ?? null,
                    'tanggal_bayar' => $existingData['tanggal_bayar'] ?? null,
                    'nominal_bayar' => $existingData['nominal_bayar'] ?? null,
                ]);
            }
        } else {
            foreach ($this->jadwal_angsuran as $item) {
                $no = $item['no'];
                $existingData = $buktiMapping->get($no);

                $this->program->jadwalAngsuran()->create([
                    'no' => $no,
                    'pokok' => $item['pokok'],
                    'margin' => 0,
                    'total_cicilan' => $item['pokok'],
                    'status' => $existingData ? $existingData['status'] : ($item['status'] ?? self::STATUS_BELUM_JATUH_TEMPO),
                    // Preserve bukti pembayaran jika ada
                    'bukti_pembayaran' => $existingData ? $existingData['bukti_pembayaran'] : null,
                    'tanggal_bayar' => $existingData ? $existingData['tanggal_bayar'] : null,
                    'nominal_bayar' => $existingData ? $existingData['nominal_bayar'] : null,
                ]);
            }
        }
    }

    // ==========================================
    // MODAL UPLOAD BUKTI
    // ==========================================

    public function openUploadModal(int $index, bool $viewOnly = false): void
    {
        if (!$this->validateAngsuranExists($index))
            return;

        $angsuran = $this->jadwal_angsuran[$index];

        // Hitung sisa pembayaran yang bisa dibayar
        $sisaPembayaran = $angsuran['sisa_pembayaran'] ?? ($angsuran['total_cicilan'] - ($angsuran['total_terbayar'] ?? 0));

        // Set view only mode
        $this->viewOnlyMode = $viewOnly;

        // Jika mode upload (bukan view only) dan belum lunas, validasi pembayaran berurutan
        if (!$viewOnly && $sisaPembayaran > 0) {
            if (!$this->validatePembayaranBerurutan($angsuran, $index))
                return;
        }

        $this->isEditingFile = false; // Selalu false karena sekarang pakai riwayat
        $this->selectedAngsuranIndex = $index;
        $this->selectedAngsuranNo = $angsuran['no'];
        $this->uploadFile = null;
        $this->maxNominalBayar = (int) round($sisaPembayaran);
        $this->uploadNominalBayar = $this->maxNominalBayar; // Default ke sisa pembayaran
        $this->showUploadModal = true;

        // Load riwayat pembayaran untuk angsuran ini
        $this->loadRiwayatPembayaran($angsuran['id']);

        $this->dispatch('open-upload-modal');
    }

    /**
     * Load riwayat pembayaran untuk angsuran yang dipilih
     */
    private function loadRiwayatPembayaran(string $idJadwalAngsuran): void
    {
        $this->riwayatPembayaran = RiwayatPembayaranRestrukturisasi::where('id_jadwal_angsuran', $idJadwalAngsuran)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id_riwayat_pembayaran,
                    'nominal_bayar' => (float) $item->nominal_bayar,
                    'bukti_pembayaran' => $item->bukti_pembayaran,
                    'tanggal_bayar' => optional($item->tanggal_bayar)->format('d/m/Y'),
                    'status' => $item->status,
                    'catatan' => $item->catatan,
                    'created_at' => optional($item->created_at)->format('d/m/Y H:i'),
                ];
            })
            ->toArray();
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->selectedAngsuranIndex = null;
        $this->selectedAngsuranNo = null;
        $this->uploadFile = null;
        $this->uploadNominalBayar = 0;
        $this->maxNominalBayar = 0;
        $this->isEditingFile = false;
        $this->viewOnlyMode = false;
        $this->riwayatPembayaran = [];
        $this->resetValidation();
    }

    public function submitUploadBukti(): void
    {
        if (!$this->validateUploadPrerequisites())
            return;

        try {
            $this->validateUploadFile();

            $file = $this->uploadFile;
            if (!($file instanceof \Illuminate\Http\UploadedFile)) {
                $this->showWarning('File Belum Siap', 'File sedang diunggah ke server. Silakan tunggu sampai muncul pesan "File siap" (hijau), lalu klik Upload lagi.');
                return;
            }

            $index = $this->selectedAngsuranIndex;
            $angsuran = $this->jadwal_angsuran[$index];
            $nominalBayar = (float) $this->uploadNominalBayar;

            // Validasi nominal tidak melebihi sisa pembayaran
            $sisaPembayaran = $angsuran['sisa_pembayaran'] ?? ($angsuran['total_cicilan'] - ($angsuran['total_terbayar'] ?? 0));
            if ($nominalBayar > $sisaPembayaran) {
                $this->showError('Nominal Melebihi Sisa', 'Nominal pembayaran (Rp ' . number_format($nominalBayar, 0, ',', '.') . ') tidak boleh melebihi sisa pembayaran (Rp ' . number_format($sisaPembayaran, 0, ',', '.') . ').');
                return;
            }

            if ($nominalBayar <= 0) {
                $this->showError('Nominal Tidak Valid', 'Nominal pembayaran harus lebih dari 0.');
                return;
            }

            DB::beginTransaction();

            $jadwalAngsuran = $this->findJadwalAngsuran($angsuran['id']);
            if (!$jadwalAngsuran) {
                DB::rollBack();
                return;
            }

            // Upload bukti pembayaran
            $path = $this->uploadBuktiPembayaran($file, $angsuran['no']);

            // Simpan ke tabel riwayat pembayaran
            $riwayat = RiwayatPembayaranRestrukturisasi::create([
                'id_jadwal_angsuran' => $angsuran['id'],
                'nominal_bayar' => $nominalBayar,
                'bukti_pembayaran' => $path,
                'tanggal_bayar' => now(),
                'status' => RiwayatPembayaranRestrukturisasi::STATUS_TERTUNDA,
            ]);

            // Update status jadwal angsuran jika belum ada status pembayaran
            if (
                $jadwalAngsuran->status === self::STATUS_BELUM_JATUH_TEMPO ||
                $jadwalAngsuran->status === self::STATUS_JATUH_TEMPO
            ) {
                $jadwalAngsuran->update([
                    'status' => self::STATUS_TERTUNDA,
                ]);
            }

            $jadwalAngsuran->refresh();
            $jadwalAngsuran->load('programRestrukturisasi.pengajuanRestrukturisasi.debitur');

            DB::commit();

            ListNotifSFinance::pembayaranRestrukturisasi($jadwalAngsuran);

            $this->loadData();
            $this->closeUploadModal();
            $this->showSuccess('Berhasil', 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi dari SKI.');
        } catch (ValidationException $e) {
            DB::rollBack();
            $this->showError('Validasi Gagal', collect($e->errors())->flatten()->join(', '));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading bukti pembayaran: ' . $e->getMessage());
            $this->showError('System Error', 'Gagal mengupload bukti pembayaran: ' . $e->getMessage());
        }
    }

    private function validateUploadPrerequisites(): bool
    {
        if ($this->selectedAngsuranIndex === null) {
            $this->showError('Error', 'Data angsuran tidak ditemukan.');
            return false;
        }

        if (!$this->uploadFile) {
            $this->showError('Error', 'Silakan pilih file bukti pembayaran terlebih dahulu.');
            return false;
        }

        if (!isset($this->jadwal_angsuran[$this->selectedAngsuranIndex])) {
            $this->showError('Error', 'Data angsuran tidak ditemukan.');
            return false;
        }

        return true;
    }

    private function validateUploadFile(): void
    {
        try {
            $this->validate([
                'uploadFile' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
                'uploadNominalBayar' => 'required|numeric|min:1',
            ], [
                'uploadFile.required' => 'File bukti pembayaran wajib diisi.',
                'uploadFile.mimes' => 'File harus berupa gambar (jpg, jpeg, png) atau PDF.',
                'uploadFile.max' => 'Ukuran file maksimal 2MB.',
                'uploadNominalBayar.required' => 'Nominal pembayaran wajib diisi.',
                'uploadNominalBayar.numeric' => 'Nominal pembayaran harus berupa angka.',
                'uploadNominalBayar.min' => 'Nominal pembayaran minimal Rp 1.',
            ]);
        } catch (ValidationException $e) {
            if (str_contains(implode(' ', $e->errors()['uploadFile'] ?? []), 'required')) {
                $this->showWarning('Tunggu Sebentar', 'File sedang diunggah ke server. Silakan tunggu sampai loading selesai, lalu klik Upload lagi.');
                return;
            }
            throw $e;
        }
    }

    private function uploadBuktiPembayaran($file, int $noAngsuran): string
    {
        $filename = sprintf(
            'bukti_pembayaran_%s_%s_%s.%s',
            $this->program->id_program_restrukturisasi,
            $noAngsuran,
            time(),
            $file->getClientOriginalExtension()
        );

        return $file->storeAs('restrukturisasi/bukti_pembayaran', $filename, 'public');
    }

    private function deleteOldBukti(?string $oldPath): void
    {
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    // ==========================================
    // MODAL KONFIRMASI
    // ==========================================

    public function openKonfirmasiModal(int $index): void
    {
        if (!auth()->user()->can('program_restrukturisasi.konfirmasi')) {
            $this->showError('Akses Ditolak', 'Anda tidak memiliki izin untuk mengkonfirmasi pembayaran.');
            return;
        }

        if (!$this->validateAngsuranExists($index))
            return;

        $angsuran = $this->jadwal_angsuran[$index];

        // Cek apakah ada pembayaran yang perlu dikonfirmasi
        if ($angsuran['jumlah_tertunda'] <= 0) {
            $this->showWarning('Tidak Ada Pembayaran Tertunda', 'Tidak ada pembayaran yang menunggu konfirmasi untuk angsuran ini.');
            return;
        }

        $this->selectedKonfirmasiIndex = $index;
        $this->selectedKonfirmasiNo = $angsuran['no'];
        $this->showKonfirmasiModal = true;

        // Load riwayat pembayaran tertunda untuk angsuran ini
        $this->loadRiwayatPembayaranTertunda($angsuran['id']);

        $this->dispatch('open-konfirmasi-modal');
    }

    /**
     * Load riwayat pembayaran yang masih tertunda untuk angsuran tertentu
     */
    private function loadRiwayatPembayaranTertunda(string $idJadwalAngsuran): void
    {
        $this->riwayatPembayaran = RiwayatPembayaranRestrukturisasi::where('id_jadwal_angsuran', $idJadwalAngsuran)
            ->where('status', RiwayatPembayaranRestrukturisasi::STATUS_TERTUNDA)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id_riwayat_pembayaran,
                    'nominal_bayar' => (float) $item->nominal_bayar,
                    'bukti_pembayaran' => $item->bukti_pembayaran,
                    'tanggal_bayar' => optional($item->tanggal_bayar)->format('d/m/Y'),
                    'status' => $item->status,
                    'catatan' => $item->catatan,
                    'created_at' => optional($item->created_at)->format('d/m/Y H:i'),
                ];
            })
            ->toArray();

        // Set riwayat pertama sebagai selected by default
        if (!empty($this->riwayatPembayaran)) {
            $this->selectedRiwayatId = $this->riwayatPembayaran[0]['id'];
            $this->selectedRiwayat = $this->riwayatPembayaran[0];
        }
    }

    /**
     * Select riwayat pembayaran tertentu untuk konfirmasi
     */
    public function selectRiwayat(string $riwayatId): void
    {
        foreach ($this->riwayatPembayaran as $riwayat) {
            if ($riwayat['id'] === $riwayatId) {
                $this->selectedRiwayatId = $riwayatId;
                $this->selectedRiwayat = $riwayat;
                break;
            }
        }
    }

    public function closeKonfirmasiModal(): void
    {
        $this->showKonfirmasiModal = false;
        $this->selectedKonfirmasiIndex = null;
        $this->selectedKonfirmasiNo = null;
        $this->catatanKonfirmasi = '';
        $this->showTolakModal = false;
        $this->riwayatPembayaran = [];
        $this->selectedRiwayatId = null;
        $this->selectedRiwayat = null;
    }

    public function openTolakModal(): void
    {
        $this->showKonfirmasiModal = false;
        $this->showTolakModal = true;
        $this->dispatch('switch-to-tolak-modal');
    }

    public function closeTolakModal(): void
    {
        $this->showTolakModal = false;
    }

    /**
     * Konfirmasi pembayaran - otomatis menentukan status Lunas atau Dibayar Sebagian
     */
    public function submitKonfirmasi(): void
    {
        if ($this->selectedKonfirmasiIndex === null || $this->selectedRiwayatId === null) {
            $this->showError('Error', 'Data pembayaran tidak ditemukan.');
            return;
        }

        $angsuran = $this->jadwal_angsuran[$this->selectedKonfirmasiIndex];

        try {
            DB::beginTransaction();

            $jadwalAngsuran = $this->findJadwalAngsuran($angsuran['id']);
            if (!$jadwalAngsuran)
                throw new \Exception('Data jadwal angsuran tidak ditemukan di database.');

            $riwayat = RiwayatPembayaranRestrukturisasi::find($this->selectedRiwayatId);
            if (!$riwayat)
                throw new \Exception('Data riwayat pembayaran tidak ditemukan di database.');

            // Update status riwayat pembayaran
            $riwayat->update([
                'status' => RiwayatPembayaranRestrukturisasi::STATUS_DIKONFIRMASI,
                'catatan' => $this->catatanKonfirmasi ?: 'Pembayaran dikonfirmasi.',
                'dikonfirmasi_oleh' => Auth::id(),
                'dikonfirmasi_at' => now(),
            ]);

            // Hitung total pembayaran yang sudah dikonfirmasi
            $totalDikonfirmasi = (float) $jadwalAngsuran->total_terbayar + (float) $riwayat->nominal_bayar;

            $sisaPembayaran = (float) $jadwalAngsuran->total_cicilan - $totalDikonfirmasi;
            $isLunas = $sisaPembayaran <= 1; // Toleransi 1 rupiah
            $statusBaru = $isLunas ? self::STATUS_LUNAS : self::STATUS_DIBAYAR_SEBAGIAN;

            if ($isLunas) {
                $totalDikonfirmasi = (float) $jadwalAngsuran->total_cicilan;
            }

            // Update jadwal angsuran
            $catatanBaru = $this->appendCatatan(
                $jadwalAngsuran->catatan,
                $isLunas ? '[Dikonfirmasi Lunas]' : '[Dikonfirmasi Sebagian]',
                'Rp ' . number_format((float) $riwayat->nominal_bayar, 0, ',', '.') . ($this->catatanKonfirmasi ? ' - ' . $this->catatanKonfirmasi : '')
            );

            $jadwalAngsuran->update([
                'total_terbayar' => $totalDikonfirmasi,
                'status' => $statusBaru,
                'catatan' => $catatanBaru,
            ]);

            // Update total terbayar program
            $this->updateProgramTotalTerbayar();

            DB::commit();

            $this->dispatch('close-konfirmasi-modal');
            $this->closeKonfirmasiModal();
            $this->loadData();

            $message = $isLunas
                ? 'Pembayaran angsuran bulan ' . $angsuran['no'] . ' telah dikonfirmasi. Status: LUNAS.'
                : 'Pembayaran Rp ' . number_format((float) $riwayat->nominal_bayar, 0, ',', '.') . ' untuk angsuran bulan ' . $angsuran['no'] . ' telah dikonfirmasi. Status: Dibayar Sebagian.';

            $this->showSuccess('Berhasil', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error konfirmasi pembayaran: ' . $e->getMessage());
            $this->showError('System Error', 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
        }
    }


    /**
     * Tolak pembayaran dari riwayat
     */
    public function tolakPembayaran(): void
    {
        if ($this->selectedRiwayatId === null) {
            $this->showError('Error', 'Data pembayaran tidak ditemukan.');
            return;
        }

        try {
            DB::beginTransaction();

            $riwayat = RiwayatPembayaranRestrukturisasi::find($this->selectedRiwayatId);
            if (!$riwayat)
                throw new \Exception('Data riwayat pembayaran tidak ditemukan di database.');

            // Hapus file bukti pembayaran
            if (!empty($riwayat->bukti_pembayaran)) {
                Storage::disk('public')->delete($riwayat->bukti_pembayaran);
            }

            $alasanPenolakan = !empty($this->catatanKonfirmasi) ? $this->catatanKonfirmasi : 'Pembayaran ditolak oleh finance.';

            // Update status riwayat menjadi ditolak
            $riwayat->update([
                'status' => RiwayatPembayaranRestrukturisasi::STATUS_DITOLAK,
                'catatan' => $alasanPenolakan,
                'dikonfirmasi_oleh' => Auth::id(),
                'dikonfirmasi_at' => now(),
            ]);

            // Cek apakah masih ada pembayaran tertunda lainnya
            $jadwalAngsuran = $riwayat->jadwalAngsuran;
            $masihAdaTertunda = $jadwalAngsuran->riwayatPembayaran()
                ->where('status', RiwayatPembayaranRestrukturisasi::STATUS_TERTUNDA)
                ->exists();

            // Jika tidak ada pembayaran tertunda lagi dan belum ada yang dikonfirmasi, kembalikan status
            if (!$masihAdaTertunda && $jadwalAngsuran->total_terbayar <= 0) {
                $tanggalJatuhTempo = \Carbon\Carbon::parse($jadwalAngsuran->tanggal_jatuh_tempo);
                $statusBaru = $tanggalJatuhTempo->isPast() ? self::STATUS_JATUH_TEMPO : self::STATUS_BELUM_JATUH_TEMPO;

                $jadwalAngsuran->update([
                    'status' => $statusBaru,
                    'catatan' => $this->appendCatatan($jadwalAngsuran->catatan, '[Ditolak]', $alasanPenolakan),
                ]);
            }

            DB::commit();

            $this->dispatch('close-tolak-modal');
            $this->closeKonfirmasiModal();
            $this->loadData();

            $this->showWarning('Pembayaran Ditolak', 'Pembayaran Rp ' . number_format((float) $riwayat->nominal_bayar, 0, ',', '.') . ' telah ditolak. User harus mengupload ulang bukti pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error tolak pembayaran: ' . $e->getMessage());
            $this->showError('System Error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Update total terbayar program restrukturisasi
     */
    private function updateProgramTotalTerbayar(): void
    {
        // Hitung total dari semua jadwal angsuran
        $totalTerbayar = JadwalAngsuran::where('id_program_restrukturisasi', $this->program->id_program_restrukturisasi)
            ->sum('total_terbayar');

        $statusProgram = $totalTerbayar >= $this->total_cicilan ? self::STATUS_LUNAS : self::STATUS_PROGRAM_BERJALAN;

        $this->program->update([
            'total_terbayar' => $totalTerbayar,
            'status' => $statusProgram,
        ]);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function validateAngsuranExists(int $index): bool
    {
        if (!isset($this->jadwal_angsuran[$index])) {
            $this->showError('Error', 'Data angsuran tidak ditemukan.');
            return false;
        }
        return true;
    }

    private function validatePembayaranBerurutan(array $angsuran, int $index): bool
    {
        $noAngsuran = $angsuran['no'];

        if ($noAngsuran <= 1)
            return true;

        $previousIndex = $index - 1;
        if (!isset($this->jadwal_angsuran[$previousIndex]))
            return true;

        $previous = $this->jadwal_angsuran[$previousIndex];
        $previousStatus = $previous['status'] ?? 'Belum Jatuh Tempo';

        // Angsuran sebelumnya harus Lunas atau Dibayar Sebagian (ada progress pembayaran)
        if (!in_array($previousStatus, [self::STATUS_LUNAS, self::STATUS_DIBAYAR_SEBAGIAN])) {
            $this->showError(
                'Pembayaran Berurutan',
                'Angsuran bulan ' . $previous['no'] . ' belum dibayar. Status: ' . $previousStatus . '. ' .
                'Silakan bayar angsuran bulan ' . $previous['no'] . ' terlebih dahulu.'
            );
            return false;
        }

        return true;
    }

    private function findJadwalAngsuran($id): ?JadwalAngsuran
    {
        if (!$id) {
            $this->showError('Error', 'ID angsuran tidak ditemukan. Silakan refresh halaman dan coba lagi.');
            return null;
        }

        $jadwal = JadwalAngsuran::find($id);

        if (!$jadwal) {
            $this->showError('Error', 'Data jadwal angsuran tidak ditemukan di database.');
            return null;
        }

        return $jadwal;
    }

    private function appendCatatan(?string $existingCatatan, string $prefix, string $newCatatan): string
    {
        $catatan = $existingCatatan ?? '';
        return trim($catatan . "\n" . $prefix . ' ' . $newCatatan);
    }

    // ==========================================
    // NOTIFICATION HELPERS
    // ==========================================

    private function showSuccess(string $title, string $text, ?string $redirectUrl = null): void
    {
        $data = ['type' => 'success', 'title' => $title, 'text' => $text];
        if ($redirectUrl)
            $data['redirect_url'] = $redirectUrl;
        $this->dispatch('swal:modal', $data);
    }

    private function showError(string $title, string $text): void
    {
        $this->dispatch('swal:modal', ['type' => 'error', 'title' => $title, 'text' => $text]);
    }

    private function showWarning(string $title, string $text): void
    {
        $this->dispatch('swal:modal', ['type' => 'warning', 'title' => $title, 'text' => $text]);
    }
}
