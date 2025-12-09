<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PengajuanRestrukturisasi;
use App\Models\ProgramRestrukturisasi;
use App\Models\JadwalAngsuran;
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
            'plafon_pembiayaan', 'suku_bunga_per_tahun', 'jangka_waktu_total', 
            'masa_tenggang', 'tanggal_mulai_cicilan', 'metode_perhitungan'
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

    public function hitungJadwalAngsuran()
    {
        // Sanitasi Input
        $this->plafon_pembiayaan = (float) $this->plafon_pembiayaan;
        $this->suku_bunga_per_tahun = (float) $this->suku_bunga_per_tahun;

        try {
            $this->validate([
                'plafon_pembiayaan' => 'required|numeric|min:0',
                'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
                'jangka_waktu_total' => 'required|integer|min:1',
                'masa_tenggang' => 'required|integer|min:0',
                'tanggal_mulai_cicilan' => 'required|date',
            ], [
                'plafon_pembiayaan.required' => 'Plafon pembiayaan wajib diisi.',
                'suku_bunga_per_tahun.max' => 'Suku bunga tidak boleh lebih dari 100%.',
                'jangka_waktu_total.required' => 'Jangka waktu wajib diisi.',
                'masa_tenggang.required' => 'Masa tenggang wajib diisi.',
                'tanggal_mulai_cicilan.required' => 'Tanggal mulai cicilan wajib diisi.',
            ]);

        } catch (ValidationException $e) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Cek Inputan',
                'text' => implode("\n", collect($e->errors())->flatten()->toArray()),
            ]);
            throw $e;
        }

        // Validasi Logika Masa Tenggang
        if ($this->masa_tenggang >= $this->jangka_waktu_total) {
            $this->dispatch('swal:modal', [
                'type' => 'error',
                'title' => 'Logika Salah',
                'text' => 'Masa tenggang tidak boleh melebihi atau sama dengan jangka waktu total!',
            ]);
            $this->addError('masa_tenggang', 'Masa tenggang invalid!');
            $this->show_jadwal = false;
            return;
        }

        // [LOGIC PERHITUNGAN DISINI]
        if ($this->metode_perhitungan === 'Flat') {
            $this->calculateFlatMethod();
        } elseif ($this->metode_perhitungan === 'Anuitas') {
            $this->calculateAnuitasMethod();
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
        $bulanEfektif = $this->jangka_waktu_total - $this->masa_tenggang;
        $pokokPerBulan = $bulanEfektif > 0 ? $this->plafon_pembiayaan / $bulanEfektif : 0;

        $jadwal = [];
        $totalPokok = 0; $totalMargin = 0; $totalCicilan = 0;
        $startDate = \Carbon\Carbon::parse($this->tanggal_mulai_cicilan);

        for ($i = 1; $i <= $this->jangka_waktu_total; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            $pokok = 0; $margin = $marginPerBulan; $catatan = '';

            if ($i <= $this->masa_tenggang) {
                $pokok = 0;
                $catatan = 'Masa Tenggang - Hanya Bayar Margin';
            } else {
                $pokok = $pokokPerBulan;
            }

            $total = $pokok + $margin;
            $totalPokok += $pokok; $totalMargin += $margin; $totalCicilan += $total;

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

    // [METODE ANUITAS - ADA SISA PINJAMAN]
    private function calculateAnuitasMethod()
    {
        $bungaPerBulan = ($this->suku_bunga_per_tahun / 100) / 12; 
        $sisaPokok = $this->plafon_pembiayaan;
        $bulanEfektif = $this->jangka_waktu_total - $this->masa_tenggang;

        $cicilanAnuitasTetap = 0;
        if ($bulanEfektif > 0 && $bungaPerBulan > 0) {
            $penyebut = 1 - pow(1 + $bungaPerBulan, -$bulanEfektif);
            $cicilanAnuitasTetap = $sisaPokok * ($bungaPerBulan / $penyebut);
        }

        $jadwal = [];
        $totalPokok = 0; $totalMargin = 0; $totalCicilan = 0;
        $startDate = \Carbon\Carbon::parse($this->tanggal_mulai_cicilan);

        for ($i = 1; $i <= $this->jangka_waktu_total; $i++) {
            $dueDate = $startDate->copy()->addMonths($i - 1);
            $isGracePeriod = $i <= $this->masa_tenggang;
            
            // Simpan Sisa Pinjaman Awal Bulan untuk Ditampilkan
            $sisaPinjamanDisplay = $sisaPokok;

            $pokok = 0; $margin = 0; $total = 0; $catatan = '';

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
                    $total = $cicilanAnuitasTetap;
                    $pokok = $total - $margin;
                }

                $sisaPokok -= $pokok;
                if ($sisaPokok < 0) $sisaPokok = 0;
            }

            $totalPokok += $pokok; $totalMargin += $margin; $totalCicilan += $total;

            $jadwal[] = [
                'no' => $i,
                'tanggal_jatuh_tempo' => $dueDate->format('d F Y'),
                'tanggal_jatuh_tempo_raw' => $dueDate->format('Y-m-d'),
                'sisa_pinjaman' => $sisaPinjamanDisplay, // <--- HANYA ADA DI ANUITAS
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
                'metode_perhitungan' => 'required|in:Flat,Anuitas',
                'plafon_pembiayaan' => 'required|numeric|min:0',
                'suku_bunga_per_tahun' => 'required|numeric|min:0|max:100',
                'jangka_waktu_total' => 'required|integer|min:1',
                'masa_tenggang' => 'required|integer|min:0',
                'tanggal_mulai_cicilan' => 'required|date',
                'jadwal_angsuran' => 'required|array|min:1',
            ], [
                'id_pengajuan_restrukturisasi.required' => 'Silakan pilih pengajuan restrukturisasi.',
                'suku_bunga_per_tahun.max' => 'Suku bunga tidak boleh lebih dari 100%.',
                'jadwal_angsuran.required' => 'Jadwal angsuran kosong. Harap hitung ulang.',
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

            $program = ProgramRestrukturisasi::create([
                'id_pengajuan_restrukturisasi' => $this->id_pengajuan_restrukturisasi,
                'metode_perhitungan' => $this->metode_perhitungan,
                'plafon_pembiayaan' => $this->plafon_pembiayaan,
                'suku_bunga_per_tahun' => $this->suku_bunga_per_tahun,
                'jangka_waktu_total' => $this->jangka_waktu_total,
                'masa_tenggang' => $this->masa_tenggang,
                'tanggal_mulai_cicilan' => $this->tanggal_mulai_cicilan,
                'total_pokok' => $this->total_pokok,
                'total_margin' => $this->total_margin,
                'total_cicilan' => $this->total_cicilan,
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

    public function render()
    {
        return view('livewire.program-restrukturisasi')->layout('layouts.app');
    }
}