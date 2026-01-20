<?php

namespace App\Livewire\ProgramRestrukturisasi;

use App\Models\ProgramRestrukturisasi;
use App\Models\JadwalAngsuran;
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

    // ==========================================
    // PROPERTIES - Modal Konfirmasi
    // ==========================================

    public $showKonfirmasiModal = false;
    public $selectedKonfirmasiIndex = null;
    public $selectedKonfirmasiNo = null;
    public $catatanKonfirmasi = '';
    public $showTolakModal = false;

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

        if ($isAdmin) return;

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
            ];

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
                $fill = [
                    'id_pengajuan_restrukturisasi' => $this->id_pengajuan_restrukturisasi,
                    'metode_perhitungan' => $metodeValid,
                    'plafon_pembiayaan' => (float) $this->plafon_pembiayaan,
                    'jangka_waktu_total' => (int) $this->jangka_waktu_total,
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
                    'total_cicilan' =>  $item['pokok'],
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

    public function openUploadModal(int $index): void
    {
        if (!$this->validateAngsuranExists($index)) return;

        $angsuran = $this->jadwal_angsuran[$index];

        if (!$this->validatePembayaranBerurutan($angsuran, $index)) return;

        $this->isEditingFile = !empty($angsuran['bukti_pembayaran']);
        $this->selectedAngsuranIndex = $index;
        $this->selectedAngsuranNo = $angsuran['no'];
        $this->uploadFile = null;
        $this->uploadNominalBayar = (int) round($this->isEditingFile
            ? ($angsuran['nominal_bayar'] ?? $angsuran['total_cicilan'])
            : $angsuran['total_cicilan']);
        $this->showUploadModal = true;

        $this->dispatch('open-upload-modal');
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->selectedAngsuranIndex = null;
        $this->selectedAngsuranNo = null;
        $this->uploadFile = null;
        $this->uploadNominalBayar = 0;
        $this->isEditingFile = false;
        $this->resetValidation();
    }

    public function submitUploadBukti(): void
    {
        if (!$this->validateUploadPrerequisites()) return;

        try {
            $this->validateUploadFile();

            $file = $this->uploadFile;
            if (!($file instanceof \Illuminate\Http\UploadedFile)) {
                $this->showWarning('File Belum Siap', 'File sedang diunggah ke server. Silakan tunggu sampai muncul pesan "File siap" (hijau), lalu klik Upload lagi.');
                return;
            }

            $index = $this->selectedAngsuranIndex;
            $angsuran = $this->jadwal_angsuran[$index];

            DB::beginTransaction();

            $path = $this->uploadBuktiPembayaran($file, $angsuran['no']);
            $this->deleteOldBukti($angsuran['bukti_pembayaran'] ?? null);

            $jadwalAngsuran = $this->findJadwalAngsuran($angsuran['id']);
            if (!$jadwalAngsuran) {
                DB::rollBack();
                return;
            }

            $jadwalAngsuran->update([
                'bukti_pembayaran' => $path,
                'status' => self::STATUS_TERTUNDA,
                'tanggal_bayar' => now(),
                'nominal_bayar' => (float) $this->uploadNominalBayar,
            ]);

            $jadwalAngsuran->refresh();
            $jadwalAngsuran->load('programRestrukturisasi.pengajuanRestrukturisasi.debitur');

            DB::commit();

            ListNotifSFinance::pembayaranRestrukturisasi($jadwalAngsuran);

            $this->loadData();
            $this->closeUploadModal();
            $this->showSuccess('Berhasil', 'Bukti pembayaran berhasil diupload dan status angsuran telah diperbarui menjadi Tertunda.');
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

        if (!$this->validateAngsuranExists($index)) return;

        $angsuran = $this->jadwal_angsuran[$index];

        if ($angsuran['status'] !== self::STATUS_TERTUNDA) {
            $this->showWarning('Tidak Dapat Dikonfirmasi', 'Hanya pembayaran dengan status Tertunda yang dapat dikonfirmasi.');
            return;
        }

        $this->selectedKonfirmasiIndex = $index;
        $this->selectedKonfirmasiNo = $angsuran['no'];
        $this->showKonfirmasiModal = true;

        $this->dispatch('open-konfirmasi-modal');
    }

    public function closeKonfirmasiModal(): void
    {
        $this->showKonfirmasiModal = false;
        $this->selectedKonfirmasiIndex = null;
        $this->selectedKonfirmasiNo = null;
        $this->catatanKonfirmasi = '';
        $this->showTolakModal = false;
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

    public function submitKonfirmasi(): void
    {
        if ($this->selectedKonfirmasiIndex === null) {
            $this->showError('Error', 'Data angsuran tidak ditemukan.');
            return;
        }

        $angsuran = $this->jadwal_angsuran[$this->selectedKonfirmasiIndex];

        if (empty($angsuran['bukti_pembayaran'])) {
            $this->showWarning('Tidak Ada Bukti', 'Tidak ada bukti pembayaran yang diupload.');
            return;
        }

        try {
            DB::beginTransaction();

            $jadwalAngsuran = $this->findJadwalAngsuran($angsuran['id']);
            if (!$jadwalAngsuran) throw new \Exception('Data jadwal angsuran tidak ditemukan di database.');

            $catatanBaru = $this->appendCatatan($jadwalAngsuran->catatan, '[Dikonfirmasi]', $this->catatanKonfirmasi);

            $jadwalAngsuran->update([
                'status' => self::STATUS_LUNAS,
                'catatan' => $catatanBaru,
            ]);

            $this->updateTotalTerbayar();

            DB::commit();

            $this->dispatch('close-konfirmasi-modal');
            $this->closeKonfirmasiModal();
            $this->loadData();

            $this->showSuccess('Berhasil', 'Pembayaran angsuran bulan ' . $angsuran['no'] . ' telah dikonfirmasi dan status diubah menjadi Lunas.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error konfirmasi pembayaran: ' . $e->getMessage());
            $this->showError('System Error', 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage());
        }
    }

    public function tolakPembayaran(): void
    {
        if ($this->selectedKonfirmasiIndex === null) {
            $this->showError('Error', 'Data angsuran tidak ditemukan.');
            return;
        }

        $angsuran = $this->jadwal_angsuran[$this->selectedKonfirmasiIndex];

        try {
            DB::beginTransaction();

            $jadwalAngsuran = $this->findJadwalAngsuran($angsuran['id']);
            if (!$jadwalAngsuran) throw new \Exception('Data jadwal angsuran tidak ditemukan di database.');

            if (!empty($jadwalAngsuran->bukti_pembayaran)) {
                Storage::delete($jadwalAngsuran->bukti_pembayaran);
            }

            $tanggalJatuhTempo = \Carbon\Carbon::parse($jadwalAngsuran->tanggal_jatuh_tempo);
            $statusBaru = $tanggalJatuhTempo->isPast() ? self::STATUS_JATUH_TEMPO : self::STATUS_BELUM_JATUH_TEMPO;

            $alasanPenolakan = !empty($this->catatanKonfirmasi) ? $this->catatanKonfirmasi : 'Pembayaran ditolak oleh finance.';
            $catatanBaru = $this->appendCatatan($jadwalAngsuran->catatan, '[Ditolak]', $alasanPenolakan);

            $jadwalAngsuran->update([
                'status' => $statusBaru,
                'bukti_pembayaran' => null,
                'tanggal_bayar' => null,
                'nominal_bayar' => null,
                'catatan' => $catatanBaru,
            ]);

            DB::commit();

            $this->dispatch('close-tolak-modal');
            $this->closeKonfirmasiModal();
            $this->loadData();

            $this->showWarning('Pembayaran Ditolak', 'Pembayaran angsuran bulan ' . $angsuran['no'] . ' telah ditolak. User harus mengupload ulang bukti pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error tolak pembayaran: ' . $e->getMessage());
            $this->showError('System Error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    private function updateTotalTerbayar(): void
    {
        $totalTerbayar = JadwalAngsuran::where('id_program_restrukturisasi', $this->program->id_program_restrukturisasi)
            ->where('status', self::STATUS_LUNAS)
            ->whereNotNull('nominal_bayar')
            ->sum('nominal_bayar');

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

        if ($noAngsuran <= 1) return true;

        $previousIndex = $index - 1;
        if (!isset($this->jadwal_angsuran[$previousIndex])) return true;

        $previous = $this->jadwal_angsuran[$previousIndex];

        if ($previous['status'] !== self::STATUS_LUNAS || empty($previous['bukti_pembayaran'])) {
            $this->showError(
                'Pembayaran Berurutan',
                'Anda harus melunasi angsuran bulan ' . $previous['no'] .
                    ' terlebih dahulu sebelum dapat mengupload bukti pembayaran untuk bulan ' . $noAngsuran . '.'
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
        if ($redirectUrl) $data['redirect_url'] = $redirectUrl;
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
