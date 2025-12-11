<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Attributes\FieldInput;
use App\Attributes\ParameterIDRoute;
use Livewire\Attributes\Renderless;
use App\Livewire\Traits\HasValidate;
use App\Models\PengajuanPeminjaman;
use App\Models\PengembalianPinjaman;
use App\Models\MasterDebiturDanInvestor;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\PengajuanRestrukturisasiRequest;

class PengajuanRestrukturisasi extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;

    private string $validateClass = PengajuanRestrukturisasiRequest::class;

    #[ParameterIDRoute]
    public $id;

    // Step 1: Identitas Debitur
    #[FieldInput]
    public $id_debitur, $id_pengajuan_peminjaman, $nama_perusahaan, $npwp, $alamat_kantor, $nomor_telepon, $nama_pic, $jabatan_pic;

    // Step 2: Data Pembiayaan
    #[FieldInput]
    public $nomor_kontrak_pembiayaan, $tanggal_akad, $jenis_pembiayaan, $jumlah_plafon_awal, $sisa_pokok_belum_dibayar;
    
    #[FieldInput]
    public $tunggakan_pokok, $tunggakan_margin_bunga, $jatuh_tempo_terakhir, $status_dpd, $alasan_restrukturisasi;

    // Step 3: Permohonan Restrukturisasi
    #[FieldInput]
    public $jenis_restrukturisasi = [], $jenis_restrukturisasi_lainnya, $rencana_pemulihan_usaha;

    // Step 4: Dokumen Pendukung
    #[FieldInput]
    #[Renderless]
    public $dokumen_ktp_pic, $dokumen_npwp_perusahaan, $dokumen_laporan_keuangan, $dokumen_arus_kas;
    
    #[FieldInput]
    #[Renderless]
    public $dokumen_kondisi_eksternal, $dokumen_kontrak_pembiayaan, $dokumen_lainnya, $dokumen_tanda_tangan;
    
    #[FieldInput]
    public $tempat, $tanggal;

    // Additional data
    public $debiturList = [];
    public $peminjamanList = [];
    public $availableJenisPembiayaan = ['Invoice Financing', 'PO Financing', 'Installment', 'Factoring'];

    public function mount()
    {
        // Setup URL actions untuk CRUD
        $this->setUrlSaveData('store_restrukturisasi', 'pengajuan-restrukturisasi.store', ['callback' => 'afterAction']);
        $this->setUrlSaveData('update_restrukturisasi', 'pengajuan-restrukturisasi.update', ['id' => 'id_placeholder', 'callback' => 'afterAction']);
        
        // Load data debitur untuk dropdown (jika diperlukan)
        $this->debiturList = MasterDebiturDanInvestor::where('flagging', 'tidak')
            ->where('status', 'active')
            ->select('id_debitur', 'nama')
            ->get();

        // Jika user adalah debitur, auto-fill data identitas
        $this->loadDebiturData();
        
        // Load pengajuan peminjaman untuk dropdown Nomor Kontrak
        $this->loadPeminjamanData();
    }

    public function loadDebiturData()
    {
        // Ambil data debitur berdasarkan user yang login
        $userId = auth()->id();
        $debitur = MasterDebiturDanInvestor::where('user_id', $userId)->first();

        if ($debitur) {
            $this->id_debitur = $debitur->id_debitur;
            $this->nama_perusahaan = $debitur->nama;
            $this->npwp = $debitur->npwp;
            $this->alamat_kantor = $debitur->alamat;
            $this->nomor_telepon = $debitur->no_telepon;
        }
    }
    
    public function loadPeminjamanData()
    {
        // Load pengajuan peminjaman yang sudah dicairkan dan milik debitur yang login
        if ($this->id_debitur) {
            $this->peminjamanList = PengajuanPeminjaman::where('id_debitur', $this->id_debitur)
                ->where('status', 'Dana Sudah Dicairkan')
                ->select('id_pengajuan_peminjaman', 'nomor_peminjaman', 'jenis_pembiayaan', 'total_pinjaman')
                ->get();
        }
    }
    
    public function loadPengajuanData($id)
    {
        // Auto-fill data ketika nomor peminjaman dipilih
        if ($id) {
            $pengajuan = PengajuanPeminjaman::find($id);
            
            if ($pengajuan) {
                $this->id_pengajuan_peminjaman = $pengajuan->id_pengajuan_peminjaman;
                $this->jenis_pembiayaan = $pengajuan->jenis_pembiayaan;
                $this->jumlah_plafon_awal = $pengajuan->total_pinjaman;
                
                // Ambil data pengembalian terakhir untuk menghitung sisa pokok
                $pengembalianTerakhir = PengembalianPinjaman::where('id_pengajuan_peminjaman', $pengajuan->id_pengajuan_peminjaman)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($pengembalianTerakhir) {
                    // Jika sudah ada pengembalian, ambil sisa dari pengembalian terakhir
                    $this->sisa_pokok_belum_dibayar = $pengembalianTerakhir->sisa_bayar_pokok;
                } else {
                    // Jika belum ada pengembalian, sisa = total pinjaman
                    $this->sisa_pokok_belum_dibayar = $pengajuan->total_pinjaman;
                }
                
                // Return data untuk JavaScript update
                $this->dispatch('pengajuanDataLoaded', [
                    'jenis_pembiayaan' => $pengajuan->jenis_pembiayaan,
                    'jumlah_plafon_awal' => $pengajuan->total_pinjaman,
                    'sisa_pokok_belum_dibayar' => $this->sisa_pokok_belum_dibayar,
                ]);
            }
        }
    }
    
    public function submitForm()
    {
        // Data akan di-sync dari JavaScript sebelum submit
        $this->saveData();
    }

    public function render()
    {
        return view('livewire.pengajuan-restrukturisasi.index')
            ->layout('layouts.app', [
                'title' => 'Pengajuan Restrukturisasi'
            ]);
    }
}
