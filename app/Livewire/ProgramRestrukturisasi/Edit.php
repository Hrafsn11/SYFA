<?php

namespace App\Livewire\ProgramRestrukturisasi;

use App\Models\ProgramRestrukturisasi;
use App\Models\JadwalAngsuran;
use App\Helpers\ListNotifSFinance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Edit extends Create
{
    public bool $isEdit = true;
    public ProgramRestrukturisasi $program;

    // Property untuk file upload per angsuran
    public $buktiPembayaranFiles = [];

    // Property untuk modal upload
    public $showUploadModal = false;
    public $selectedAngsuranIndex = null;
    public $selectedAngsuranNo = null;
    public $uploadFile = null;

    public string $pageTitle = 'Aksi Program Restrukturisasi';
    public string $pageSubtitle = 'Perbarui parameter program restrukturisasi';
    public string $submitLabel = 'Perbarui Restrukturisasi';

    public function mount(?string $id = null): void
    {
        abort_unless(auth()->user()->can('program_restrukturisasi.edit'), 403, 'Unauthorized');

        $this->tanggal_mulai_cicilan = date('Y-m-d');

        if ($id === null) {
            abort(404);
        }

        $this->program = ProgramRestrukturisasi::with([
            'pengajuanRestrukturisasi.debitur',
            'jadwalAngsuran' => fn($query) => $query->orderBy('no'),
        ])->findOrFail($id);

        // Authorization check: Debitur hanya bisa edit data miliknya
        $user = Auth::user();
        $isAdmin = $user && $user->hasRole(['super-admin', 'admin', 'sfinance', 'Finance SKI']);
        
        if (!$isAdmin) {
            $debitur = \App\Models\MasterDebiturDanInvestor::where('user_id', Auth::id())->first();
            $pengajuanDebiturId = $this->program->pengajuanRestrukturisasi->id_debitur ?? null;
            
            if (!$debitur || $debitur->id_debitur !== $pengajuanDebiturId) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
            }
        }

        $pengajuan = $this->program->pengajuanRestrukturisasi;

        $this->id_pengajuan_restrukturisasi = $this->program->id_pengajuan_restrukturisasi;
        $this->nama_debitur = $pengajuan?->debitur?->nama ?? $pengajuan?->nama_perusahaan ?? '-';
        $this->nomor_kontrak = $pengajuan?->nomor_kontrak_pembiayaan ?? '-';
        $this->metode_perhitungan = $this->program->metode_perhitungan;

        // [FIX] Float cast
        $this->plafon_pembiayaan = (float) $this->program->plafon_pembiayaan;
        $this->suku_bunga_per_tahun = (float) $this->program->suku_bunga_per_tahun;

        $this->jangka_waktu_total = (int) $this->program->jangka_waktu_total;
        $this->masa_tenggang = (int) $this->program->masa_tenggang;
        $this->tanggal_mulai_cicilan = optional($this->program->tanggal_mulai_cicilan)->format('Y-m-d');

        $this->total_pokok = (float) $this->program->total_pokok;
        $this->total_margin = (float) $this->program->total_margin;
        $this->total_cicilan = (float) $this->program->total_cicilan;

        // Hitung sisa pinjaman untuk metode Efektif (Anuitas)
        $sisaPokok = $this->plafon_pembiayaan;

        $this->jadwal_angsuran = $this->program->jadwalAngsuran->map(function ($item, $index) use (&$sisaPokok) {
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

            // Tambah sisa_pinjaman untuk metode Efektif (Anuitas)
            if ($this->metode_perhitungan === 'Efektif (Anuitas)') {
                $data['sisa_pinjaman'] = $sisaPokok;
                if (!$item->is_grace_period) {
                    $sisaPokok -= (float) $item->pokok;
                    if ($sisaPokok < 0) $sisaPokok = 0;
                }
            }

            return $data;
        })->toArray();

        $this->show_jadwal = true;
        $this->loadApprovedRestrukturisasi($this->id_pengajuan_restrukturisasi);
    }

    /**
     * Reload data jadwal angsuran dari database
     */
    protected function loadData()
    {
        $this->program->refresh();
        $this->program->load(['jadwalAngsuran' => fn($query) => $query->orderBy('no')]);

        // Hitung sisa pinjaman untuk metode Efektif (Anuitas)
        $sisaPokok = $this->plafon_pembiayaan;

        $this->jadwal_angsuran = $this->program->jadwalAngsuran->map(function ($item, $index) use (&$sisaPokok) {
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

            // Tambah sisa_pinjaman untuk metode Efektif (Anuitas)
            if ($this->metode_perhitungan === 'Efektif (Anuitas)') {
                $data['sisa_pinjaman'] = $sisaPokok;
                if (!$item->is_grace_period) {
                    $sisaPokok -= (float) $item->pokok;
                    if ($sisaPokok < 0) $sisaPokok = 0;
                }
            }

            return $data;
        })->toArray();
    }

    /**
     * Override hitungJadwalAngsuran to preserve existing data
     * When user recalculates in edit mode, we need to keep status & bukti_pembayaran
     */
    public function hitungJadwalAngsuran()
    {
        // Save current jadwal data (status, bukti_pembayaran, etc)
        $oldJadwalData = [];
        foreach ($this->jadwal_angsuran as $index => $item) {
            $oldJadwalData[$item['no']] = [
                'id' => $item['id'] ?? null,
                'status' => $item['status'] ?? 'Belum Jatuh Tempo',
                'bukti_pembayaran' => $item['bukti_pembayaran'] ?? null,
                'tanggal_bayar' => $item['tanggal_bayar'] ?? null,
                'nominal_bayar' => $item['nominal_bayar'] ?? 0,
            ];
        }

        // Call parent to recalculate
        parent::hitungJadwalAngsuran();

        // Merge back the preserved data
        foreach ($this->jadwal_angsuran as $index => $item) {
            $no = $item['no'];
            if (isset($oldJadwalData[$no])) {
                $this->jadwal_angsuran[$index]['id'] = $oldJadwalData[$no]['id'];
                $this->jadwal_angsuran[$index]['status'] = $oldJadwalData[$no]['status'];
                $this->jadwal_angsuran[$index]['bukti_pembayaran'] = $oldJadwalData[$no]['bukti_pembayaran'];
                $this->jadwal_angsuran[$index]['tanggal_bayar'] = $oldJadwalData[$no]['tanggal_bayar'];
                $this->jadwal_angsuran[$index]['nominal_bayar'] = $oldJadwalData[$no]['nominal_bayar'];
            } else {
                // New installment (if jangka_waktu increased)
                $this->jadwal_angsuran[$index]['id'] = null;
                $this->jadwal_angsuran[$index]['status'] = 'Belum Jatuh Tempo';
                $this->jadwal_angsuran[$index]['bukti_pembayaran'] = null;
                $this->jadwal_angsuran[$index]['tanggal_bayar'] = null;
                $this->jadwal_angsuran[$index]['nominal_bayar'] = 0;
            }
        }
    }

    public function simpan()
    {
        if (empty($this->jadwal_angsuran) || !$this->show_jadwal) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Belum Dihitung',
                'text' => 'Silakan klik tombol "Hitung Jadwal Angsuran" terlebih dahulu.',
            ]);
            return;
        }

        try {
            $this->validate([
                'id_pengajuan_restrukturisasi' => 'required|exists:pengajuan_restrukturisasi,id_pengajuan_restrukturisasi',
                'metode_perhitungan' => 'required|in:Flat,Efektif (Anuitas)',
                'plafon_pembiayaan' => 'required|numeric|min:0',
                'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
                'jangka_waktu_total' => 'required|integer|min:1',
                'masa_tenggang' => 'required|integer|min:0',
                'tanggal_mulai_cicilan' => 'required|date',
                'jadwal_angsuran' => 'required|array|min:1',
            ], [
                'id_pengajuan_restrukturisasi.required' => 'Silakan pilih pengajuan restrukturisasi.',
                'suku_bunga_per_tahun.max' => 'Suku bunga tidak boleh lebih dari 100%.',
                'jadwal_angsuran.required' => 'Mohon hitung jadwal angsuran sebelum menyimpan.',
            ]);
        } catch (ValidationException $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Gagal Menyimpan',
                'text' => implode("\n", collect($e->errors())->flatten()->toArray()),
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Sanitasi metode_perhitungan
            $metodeValid = trim($this->metode_perhitungan);
            if (!in_array($metodeValid, ['Flat', 'Efektif (Anuitas)'])) {
                throw new \Exception('Metode perhitungan tidak valid: ' . $metodeValid);
            }

            $this->program->update([
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
            ]);

            // Simpan mapping bukti pembayaran berdasarkan nomor angsuran
            $buktiMapping = $this->program->jadwalAngsuran->keyBy('no')->map(function ($item) {
                return [
                    'bukti_pembayaran' => $item->bukti_pembayaran,
                    'tanggal_bayar' => $item->tanggal_bayar,
                    'nominal_bayar' => $item->nominal_bayar,
                    'status' => $item->status,
                ];
            });

            // Delete dan recreate jadwal angsuran
            $this->program->jadwalAngsuran()->delete();

            foreach ($this->jadwal_angsuran as $item) {
                $no = $item['no'];
                $existingData = $buktiMapping->get($no);

                $this->program->jadwalAngsuran()->create([
                    'no' => $no,
                    'tanggal_jatuh_tempo' => \Carbon\Carbon::parse($item['tanggal_jatuh_tempo_raw']),
                    'pokok' => $item['pokok'],
                    'margin' => $item['margin'],
                    'total_cicilan' => $item['total_cicilan'],
                    'catatan' => $item['catatan'],
                    'is_grace_period' => $item['is_grace_period'] ?? false,
                    'status' => $existingData ? $existingData['status'] : ($item['status'] ?? 'Belum Jatuh Tempo'),
                    // Preserve bukti pembayaran jika ada
                    'bukti_pembayaran' => $existingData ? $existingData['bukti_pembayaran'] : null,
                    'tanggal_bayar' => $existingData ? $existingData['tanggal_bayar'] : null,
                    'nominal_bayar' => $existingData ? $existingData['nominal_bayar'] : null,
                ]);
            }

            DB::commit();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Program restrukturisasi berhasil diperbarui!',
                'redirect_url' => route('program-restrukturisasi.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating: ' . $e->getMessage());
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'System Error',
                'text' => 'Gagal memperbarui: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Buka modal upload bukti pembayaran
     */
    public function openUploadModal($index)
    {
        // Cek apakah jadwal angsuran ada
        if (!isset($this->jadwal_angsuran[$index])) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'Data angsuran tidak ditemukan.',
            ]);
            return;
        }

        $angsuran = $this->jadwal_angsuran[$index];
        $noAngsuran = $angsuran['no'];

        // Validasi pembayaran berurutan
        if ($noAngsuran > 1) {
            // Cari angsuran sebelumnya
            $previousIndex = $index - 1;
            if (isset($this->jadwal_angsuran[$previousIndex])) {
                $previousAngsuran = $this->jadwal_angsuran[$previousIndex];

                // Cek apakah angsuran sebelumnya sudah lunas
                if ($previousAngsuran['status'] !== 'Lunas' || empty($previousAngsuran['bukti_pembayaran'])) {
                    $this->dispatch('swal:modal', [
                        'type' => 'error',
                        'title' => 'Pembayaran Berurutan',
                        'text' => 'Anda harus melunasi angsuran bulan ' . $previousAngsuran['no'] . ' terlebih dahulu sebelum dapat mengupload bukti pembayaran untuk bulan ' . $noAngsuran . '.',
                    ]);
                    return;
                }
            }
        }

        // Cek apakah sudah ada bukti pembayaran
        if (!empty($angsuran['bukti_pembayaran'])) {
            $this->dispatch('swal:modal', [
                'type' => 'info',
                'title' => 'Info',
                'text' => 'Bukti pembayaran untuk angsuran ini sudah ada. Silakan hapus terlebih dahulu jika ingin mengganti.',
            ]);
            return;
        }

        $this->selectedAngsuranIndex = $index;
        $this->selectedAngsuranNo = $noAngsuran;
        $this->uploadFile = null;
        $this->showUploadModal = true;

        // Dispatch event untuk trigger JavaScript buka modal
        $this->dispatch('open-upload-modal');
    }

    /**
     * Tutup modal upload
     */
    public function closeUploadModal()
    {
        $this->showUploadModal = false;
        $this->selectedAngsuranIndex = null;
        $this->selectedAngsuranNo = null;
        $this->uploadFile = null;
        $this->resetValidation();
    }

    /**
     * Upload bukti pembayaran untuk angsuran tertentu
     */
    public function submitUploadBukti()
    {
        \Log::info('submitUploadBukti called', [
            'selected_index' => $this->selectedAngsuranIndex,
            'has_file' => $this->uploadFile ? 'yes' : 'no',
            'file_type' => $this->uploadFile ? get_class($this->uploadFile) : 'null'
        ]);

        try {
            // Validasi
            if ($this->selectedAngsuranIndex === null) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'Data angsuran tidak ditemukan.',
                ]);
                return;
            }

            // Cek apakah file sudah dipilih
            if (!$this->uploadFile) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'Silakan pilih file bukti pembayaran terlebih dahulu.',
                ]);
                return;
            }

            // Validasi file - Livewire sudah handle upload, jadi langsung validate
            try {
                $this->validate([
                    'uploadFile' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
                ], [
                    'uploadFile.required' => 'File bukti pembayaran wajib diisi.',
                    'uploadFile.mimes' => 'File harus berupa gambar (jpg, jpeg, png) atau PDF.',
                    'uploadFile.max' => 'Ukuran file maksimal 2MB.',
                ]);
            } catch (ValidationException $e) {
                // Jika validasi gagal karena file belum ter-upload, tunggu sebentar
                if (str_contains(implode(' ', $e->errors()['uploadFile'] ?? []), 'required')) {
                    $this->dispatch('swal:modal', [
                        'type' => 'warning',
                        'title' => 'Tunggu Sebentar',
                        'text' => 'File sedang diunggah ke server. Silakan tunggu sampai loading selesai, lalu klik Upload lagi.',
                    ]);
                    return;
                }
                throw $e;
            }

            // Pastikan file adalah instance UploadedFile
            $file = $this->uploadFile;
            if (!($file instanceof \Illuminate\Http\UploadedFile)) {
                \Log::warning('File is not UploadedFile instance', [
                    'file_type' => gettype($file),
                    'file_class' => is_object($file) ? get_class($file) : 'not object'
                ]);
                $this->dispatch('swal:modal', [
                    'type' => 'warning',
                    'title' => 'File Belum Siap',
                    'text' => 'File sedang diunggah ke server. Silakan tunggu sampai muncul pesan "File siap" (hijau), lalu klik Upload lagi.',
                ]);
                return;
            }

            $index = $this->selectedAngsuranIndex;

            if (!isset($this->jadwal_angsuran[$index])) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'Data angsuran tidak ditemukan.',
                ]);
                return;
            }

            $angsuran = $this->jadwal_angsuran[$index];
            $noAngsuran = $angsuran['no'];

            DB::beginTransaction();

            // Upload file
            $filename = 'bukti_pembayaran_' . $this->program->id_program_restrukturisasi . '_' . $noAngsuran . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('restrukturisasi/bukti_pembayaran', $filename, 'public');

            // Hapus file lama jika ada
            if (!empty($angsuran['bukti_pembayaran']) && Storage::disk('public')->exists($angsuran['bukti_pembayaran'])) {
                Storage::disk('public')->delete($angsuran['bukti_pembayaran']);
            }

            // Update jadwal angsuran di database
            if (!isset($angsuran['id'])) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'ID angsuran tidak ditemukan. Silakan refresh halaman dan coba lagi.',
                ]);
                DB::rollBack();
                return;
            }

            $jadwalAngsuran = JadwalAngsuran::find($angsuran['id']);
            if (!$jadwalAngsuran) {
                $this->dispatch('swal:modal', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => 'Data jadwal angsuran tidak ditemukan di database.',
                ]);
                DB::rollBack();
                return;
            }

            $jadwalAngsuran->update([
                'bukti_pembayaran' => $path,
                'status' => 'Lunas',
                'tanggal_bayar' => now(),
                'nominal_bayar' => $angsuran['total_cicilan'],
            ]);

            // Reload jadwal angsuran dengan relasi untuk notifikasi
            $jadwalAngsuran->refresh();
            $jadwalAngsuran->load('programRestrukturisasi.pengajuanRestrukturisasi.debitur');

            DB::commit();

            // Kirim notifikasi saat debitur melakukan pembayaran restrukturisasi
            ListNotifSFinance::pembayaranRestrukturisasi($jadwalAngsuran);

            // Reload data dulu
            $this->loadData();

            // Tutup modal dan clear data
            $this->closeUploadModal();

            $this->dispatch('swal:modal', [
                'type' => 'success',
                'title' => 'Berhasil',
                'text' => 'Bukti pembayaran berhasil diupload dan status angsuran telah diperbarui menjadi Lunas.',
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = collect($e->errors())->flatten()->join(', ');
            \Log::error('Validation error uploading bukti: ' . $errors);
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Validasi Gagal',
                'text' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error uploading bukti pembayaran: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'selected_index' => $this->selectedAngsuranIndex,
                'has_file' => $this->uploadFile ? 'yes' : 'no',
            ]);
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'System Error',
                'text' => 'Gagal mengupload bukti pembayaran: ' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.program-restrukturisasi.edit')->layout('layouts.app');
    }
}
