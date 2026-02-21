<?php

namespace App\Livewire\JenisInvestasi;

use Livewire\Component;
use App\Models\PengajuanInvestasi;
use App\Models\MasterDebiturDanInvestor;
use App\Models\RiwayatPengembalianDeposito;
use Livewire\WithFileUploads;
use App\Attributes\FieldInput;
use App\Attributes\ParameterIDRoute;
use Livewire\Attributes\Renderless;
use App\Livewire\Traits\HasValidate;
use App\Livewire\Traits\HasUniversalFormAction;
use App\Http\Requests\JenisInvestasiRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JenisInvestasiIndex extends Component
{
    use HasUniversalFormAction, HasValidate, WithFileUploads;

    private string $validateClass = JenisInvestasiRequest::class;

    #[ParameterIDRoute]
    public $id;

    #[FieldInput]
    public $id_pengajuan_investasi, $id_debitur, $nominal_yang_disalurkan, $tanggal_pengiriman_dana, $tanggal_pengembalian, $bukti_pengembalian;

    public $id_penyaluran_selected;
    public $riwayat_list = [];
    
    public $tanggal_input_pengembalian;
    public $bukti_input_pengembalian;
    public $catatan_pengembalian;

    public function mount()
    {
        $this->setUrlSaveData('store_penyaluran_deposito', 'jenis-investasi.store', ["callback" => "afterAction"]);
        $this->setUrlSaveData('update_penyaluran_deposito', 'jenis-investasi.update', ["id" => "id_placeholder", "callback" => "afterAction"]);
    }

    /**
     */
    public function getPengajuanInvestasiProperty()
    {
        return PengajuanInvestasi::query()
            ->withSisaDana()
            ->whereNotNull('pengajuan_investasi.nomor_kontrak')
            ->where('pengajuan_investasi.nomor_kontrak', '!=', '')
            ->orderBy('pengajuan_investasi.created_at', 'desc')
            ->get();
    }

    /**
     */
    public function getDebiturProperty()
    {
        return MasterDebiturDanInvestor::query()
            ->where('flagging', 'tidak')
            ->where('status', 'active')
            ->orderBy('nama', 'asc')
            ->get(['id_debitur', 'nama']);
    }

    public function render()
    {
        return view('livewire.jenis-investasi.index', [
            'pengajuanInvestasi' => $this->pengajuanInvestasi,
            'debitur' => $this->debitur,
        ])
            ->layout('layouts.app', [
                'title' => 'Jenis Investasi'
            ]);
    }

    /**
     * Simpan pengembalian dengan file upload
     */
    public function simpanPengembalian($id, $nominal)
    {
        try {
            DB::beginTransaction();

            $penyaluran = \App\Models\JenisInvestasi::findOrFail($id);
            
            $sisaBelumDikembalikan = $penyaluran->sisa_belum_dikembalikan;

            if ($nominal < 0) {
                $this->dispatch('showAlert', type: 'error', message: 'Nominal yang dikembalikan tidak boleh negatif!');
                return;
            }
            
            if ($nominal > $sisaBelumDikembalikan) {
                $this->dispatch('showAlert', type: 'error', message: 'Nominal yang dikembalikan tidak boleh lebih besar dari sisa yang belum dikembalikan (Rp ' . number_format($sisaBelumDikembalikan, 0, ',', '.') . ')!');
                return;
            }

            $buktiPath = null;
            if ($this->bukti_input_pengembalian) {
                $buktiPath = $this->bukti_input_pengembalian->store('bukti_pengembalian', 'public');
            }

            RiwayatPengembalianDeposito::create([
                'id_penyaluran_deposito' => $id,
                'nominal_dikembalikan' => $nominal,
                'tanggal_pengembalian' => now(),
                'bukti_pengembalian' => $buktiPath,
                'catatan' => $this->catatan_pengembalian,
                'diinput_oleh' => Auth::id(),
            ]);
            
            $totalDikembalikan = $penyaluran->riwayatPengembalian()->sum('nominal_dikembalikan');
            $penyaluran->update([
                'nominal_yang_dikembalikan' => $totalDikembalikan
            ]);

            $pengajuan = \App\Models\PengajuanInvestasi::find($penyaluran->id_pengajuan_investasi);
            if ($pengajuan) {
                $pengajuan->update([
                    'total_kembali_dari_penyaluran' => $pengajuan->penyaluranDeposito()->sum('nominal_yang_dikembalikan')
                ]);
            }

            DB::commit();

            $this->bukti_input_pengembalian = null;
            $this->catatan_pengembalian = '';

            $sisaSekarang = $penyaluran->fresh()->sisa_belum_dikembalikan;
            
            $this->dispatch('refreshPenyaluranDepositoTable');
            
            $message = 'Nominal pengembalian berhasil disimpan!';
            if ($sisaSekarang <= 0) {
                $message .= ' Status: LUNAS!';
            } else {
                $message .= ' Sisa: Rp ' . number_format($sisaSekarang, 0, ',', '.');
            }
            $this->dispatch('pengembalian-success', message: $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error simpan pengembalian: ' . $e->getMessage());
            $this->dispatch('showAlert', type: 'error', message: 'Terjadi kesalahan saat menyimpan data!');
        }
    }

    /**
     * Update nominal yang dikembalikan
     */
    public function updateNominalPengembalian($id, $nominal, $tanggal = null, $bukti = null, $catatan = null)
    {
        try {
            DB::beginTransaction();

            $penyaluran = \App\Models\JenisInvestasi::findOrFail($id);
            
            $sisaBelumDikembalikan = $penyaluran->sisa_belum_dikembalikan;

            // Validasi: nominal tidak boleh negatif
            if ($nominal < 0) {
                $this->dispatch('showAlert', type: 'error', message: 'Nominal yang dikembalikan tidak boleh negatif!');
                return;
            }
            
            if ($nominal > $sisaBelumDikembalikan) {
                $this->dispatch('showAlert', type: 'error', message: 'Nominal yang dikembalikan tidak boleh lebih besar dari sisa yang belum dikembalikan (Rp ' . number_format($sisaBelumDikembalikan, 0, ',', '.') . ')!');
                return;
            }

            RiwayatPengembalianDeposito::create([
                'id_penyaluran_deposito' => $id,
                'nominal_dikembalikan' => $nominal,
                'tanggal_pengembalian' => $tanggal ?? now(),
                'bukti_pengembalian' => $bukti,
                'catatan' => $catatan,
                'diinput_oleh' => Auth::id(),
            ]);
            
            $totalDikembalikan = $penyaluran->riwayatPengembalian()->sum('nominal_dikembalikan');
            $penyaluran->update([
                'nominal_yang_dikembalikan' => $totalDikembalikan
            ]);

            // Update total_kembali_dari_penyaluran di pengajuan_investasi
            $pengajuan = \App\Models\PengajuanInvestasi::find($penyaluran->id_pengajuan_investasi);
            if ($pengajuan) {
                $pengajuan->update([
                    'total_kembali_dari_penyaluran' => $pengajuan->penyaluranDeposito()->sum('nominal_yang_dikembalikan')
                ]);
            }

            DB::commit();

            $sisaSekarang = $penyaluran->fresh()->sisa_belum_dikembalikan;
            
            // Refresh table
            $this->dispatch('refreshPenyaluranDepositoTable');
            
            $message = 'Nominal pengembalian berhasil disimpan!';
            if ($sisaSekarang <= 0) {
                $message .= ' Status: LUNAS!';
            } else {
                $message .= ' Sisa: Rp ' . number_format($sisaSekarang, 0, ',', '.');
            }
            $this->dispatch('pengembalian-success', message: $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating nominal pengembalian: ' . $e->getMessage());
            $this->dispatch('showAlert', type: 'error', message: 'Terjadi kesalahan saat menyimpan data!');
        }
    }

    /**
     * Lihat riwayat pengembalian
     */
    public function lihatRiwayat($id)
    {
        $this->id_penyaluran_selected = $id;
        
        $penyaluran = \App\Models\JenisInvestasi::with(['pengajuanInvestasi', 'debitur', 'riwayatPengembalian.user'])
            ->find($id);
        
        if (!$penyaluran) {
            $this->dispatch('showAlert', type: 'error', message: 'Data tidak ditemukan!');
            return;
        }

        $this->riwayat_list = $penyaluran->riwayatPengembalian->map(function($item) {
            return [
                'id' => $item->id_riwayat,
                'tanggal' => $item->tanggal_pengembalian->format('d/m/Y'),
                'nominal' => 'Rp ' . number_format($item->nominal_dikembalikan, 0, ',', '.'),
                'nominal_raw' => $item->nominal_dikembalikan,
                'bukti' => $item->bukti_pengembalian ? asset('storage/' . $item->bukti_pengembalian) : null,
                'catatan' => $item->catatan,
                'user' => $item->user->name ?? '-',
                'created_at' => $item->created_at->format('d/m/Y H:i'),
            ];
        })->toArray();

        $this->dispatch('open-riwayat-modal', [
            'no_kontrak' => $penyaluran->pengajuanInvestasi->nomor_kontrak ?? '-',
            'nama_perusahaan' => $penyaluran->debitur->nama ?? '-',
            'nominal_disalurkan' => 'Rp ' . number_format($penyaluran->nominal_yang_disalurkan, 0, ',', '.'),
            'total_dikembalikan' => 'Rp ' . number_format($penyaluran->total_dikembalikan, 0, ',', '.'),
            'sisa' => 'Rp ' . number_format($penyaluran->sisa_belum_dikembalikan, 0, ',', '.'),
        ]);
    }
}
