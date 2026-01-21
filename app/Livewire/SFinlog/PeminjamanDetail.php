<?php

namespace App\Livewire\SFinlog;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PeminjamanFinlog;
use App\Models\HistoryPengajuanPinjamanFinlog;
use App\Helpers\ListNotifSFinlog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanDetail extends Component
{
    use WithFileUploads;

    public $id;
    public $peminjaman;
    public $bagi_hasil_disetujui;
    public $catatan;
    public $catatan_penolakan;
    public $bukti_transfer;
    public $nomor_kontrak;
    public $biaya_administrasi;
    public $jaminan;

    public function mount($id)
    {
        $this->id = $id;
        $this->loadData();
    }

    public function loadData()
    {
        $this->peminjaman = PeminjamanFinlog::with([
            'debitur',
            'cellsProject',
            'histories' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'histories.submitBy',
            'histories.approvedBy',
            'histories.rejectedBy'
        ])->findOrFail($this->id);
    }

    public function refreshData()
    {
        $this->loadData();
    }

    // Step 1: Submit Pengajuan
    public function submitPengajuan()
    {
        try {
            DB::beginTransaction();

            $this->peminjaman->update([
                'current_step' => 2,
                'status' => 'Menunggu Persetujuan'
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 1,
                'status' => 'Pengajuan Disubmit',
                'submit_step1_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Pengajuan berhasil disubmit');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 2: Validasi IO - Approve
    public function validasiIOApprove()
    {
        $this->validate([
            'bagi_hasil_disetujui' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Recalculate nilai_bagi_hasil dan total_pinjaman berdasarkan bagi_hasil_disetujui
            $nilai_pinjaman = $this->peminjaman->nilai_pinjaman ?? 0;
            $nilai_bagi_hasil_baru = $nilai_pinjaman * ($this->bagi_hasil_disetujui / 100);
            $total_pinjaman_baru = $nilai_pinjaman + $nilai_bagi_hasil_baru;

            $this->peminjaman->update([
                'current_step' => 3,
                'status' => 'Menunggu Persetujuan',
                'presentase_bagi_hasil' => $this->bagi_hasil_disetujui,
                'nilai_bagi_hasil' => $nilai_bagi_hasil_baru,
                'total_pinjaman' => $total_pinjaman_baru
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 2,
                'status' => 'Disetujui Investment Officer',
                'bagi_hasil_disetujui' => $this->bagi_hasil_disetujui,
                'catatan' => $this->catatan,
                'approve_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->reset(['bagi_hasil_disetujui']);
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Validasi IO berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 2: Validasi IO - Reject
    public function validasiIOReject()
    {
        $this->validate([
            'catatan_penolakan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Step 2 ditolak: kembali ke Draft (step 1)
            $this->peminjaman->update([
                'current_step' => 1,
                'status' => 'Draft'
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 2,
                'status' => 'Ditolak Investment Officer',
                'catatan' => $this->catatan_penolakan,
                'reject_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->reset(['catatan_penolakan']);
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Penolakan berhasil dicatat. Pengajuan kembali ke Draft');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 3: Persetujuan Debitur - Approve
    public function persetujuanDebiturApprove()
    {
        try {
            DB::beginTransaction();

            // Get bagi hasil from last history
            $lastHistory = $this->peminjaman->histories()->latest()->first();
            
            $this->peminjaman->update([
                'current_step' => 4,
                'status' => 'Menunggu Persetujuan',
                'presentase_bagi_hasil' => $lastHistory->bagi_hasil_disetujui ?? 0
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 3,
                'status' => 'Disetujui Debitur',
                'approve_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Persetujuan debitur berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 3: Persetujuan Debitur - Reject
    public function persetujuanDebiturReject()
    {
        $this->validate([
            'catatan_penolakan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Step 3 ditolak: Ditolak permanent (tidak bisa revisi)
            $this->peminjaman->update([
                'status' => 'Ditolak'
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 3,
                'status' => 'Ditolak Debitur',
                'catatan' => $this->catatan_penolakan,
                'reject_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->reset(['catatan_penolakan']);
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Pengajuan ditolak oleh Debitur');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 4: Persetujuan SKI Finance - Approve
    public function persetujuanSKIFinanceApprove()
    {
        try {
            DB::beginTransaction();

            $this->peminjaman->update([
                'current_step' => 5,
                'status' => 'Menunggu Persetujuan'
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 4,
                'status' => 'Disetujui SKI Finance',
                'approve_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Persetujuan SKI Finance berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 4: Persetujuan SKI Finance - Reject
    public function persetujuanSKIFinanceReject()
    {
        $this->validate([
            'catatan_penolakan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Step 4 ditolak: kembali ke Step 2 (IO harus validasi ulang)
            $this->peminjaman->update([
                'current_step' => 2,
                'status' => 'Menunggu Persetujuan'
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 4,
                'status' => 'Ditolak SKI Finance',
                'catatan' => $this->catatan_penolakan,
                'reject_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->reset(['catatan_penolakan']);
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Penolakan berhasil dicatat. Kembali ke Validasi IO');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 5: Persetujuan CEO - Approve
    public function persetujuanCEOApprove()
    {
        try {
            DB::beginTransaction();

            $this->peminjaman->update([
                'current_step' => 6,
                'status' => 'Disetujui'
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 5,
                'status' => 'Disetujui CEO Finlog',
                'approve_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Persetujuan CEO berhasil');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 5: Persetujuan CEO - Reject
    public function persetujuanCEOReject()
    {
        $this->validate([
            'catatan_penolakan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Step 5 ditolak: kembali ke Step 4 (SKI Finance harus approve ulang)
            $this->peminjaman->update([
                'current_step' => 4,
                'status' => 'Menunggu Persetujuan'
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 5,
                'status' => 'Ditolak CEO Finlog',
                'catatan' => $this->catatan_penolakan,
                'reject_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->reset(['catatan_penolakan']);
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Penolakan berhasil dicatat. Kembali ke Persetujuan SKI Finance');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 6: Generate Kontrak
    public function generateKontrak()
    {
        $this->validate([
            'biaya_administrasi' => 'required|numeric|min:0',
            'jaminan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Auto-generate nomor kontrak: FINLOG + running_number + bulan + tanggal + tahun
            // Contoh: FINLOG01012026 = kontrak ke-1, tanggal 01 bulan 01 tahun 2026
            $today = now();
            $bulan = $today->format('m');  // 01-12
            $tanggal = $today->format('d'); // 01-31
            $tahun = $today->format('y');   // 2 digit tahun (26 untuk 2026)
            
            // Cari running number untuk bulan ini
            $prefix = 'FINLOG';
            $suffix = $bulan . $tanggal . $tahun;
            
            // Cari kontrak terakhir dengan pattern yang sama di bulan & tahun ini
            $lastKontrak = PeminjamanFinlog::where('nomor_kontrak', 'LIKE', $prefix . '%' . $bulan . '%' . $tahun)
                ->orderBy('nomor_kontrak', 'desc')
                ->first();
            
            if ($lastKontrak && $lastKontrak->nomor_kontrak) {
                // Extract running number (2 digit setelah FINLOG)
                $lastRunning = (int) substr($lastKontrak->nomor_kontrak, 6, 2);
                $runningNumber = str_pad($lastRunning + 1, 2, '0', STR_PAD_LEFT);
            } else {
                $runningNumber = '01';
            }
            
            $nomorKontrak = $prefix . $runningNumber . $suffix;

            $this->peminjaman->update([
                'current_step' => 7,
                'status' => 'Dicairkan',
                'nomor_kontrak' => $nomorKontrak,
                'biaya_administrasi' => $this->biaya_administrasi,
                'jaminan' => $this->jaminan,
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 6,
                'status' => 'Kontrak Digenerate',
                'submit_step1_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->reset(['biaya_administrasi', 'jaminan']);
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Kontrak berhasil digenerate dengan nomor: ' . $nomorKontrak);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    // Step 7: Upload Bukti Transfer
    public function uploadBuktiTransfer()
    {
        $this->validate([
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $path = $this->bukti_transfer->store('peminjaman/bukti-transfer', 'public');

            $this->peminjaman->update([
                'current_step' => 8,
                'status' => 'Selesai',
                'bukti_transfer' => $path
            ]);

            $history = HistoryPengajuanPinjamanFinlog::create([
                'id_peminjaman_finlog' => $this->peminjaman->id_peminjaman_finlog,
                'current_step' => 7,
                'status' => 'Bukti Transfer Diupload',
                'submit_step1_by' => Auth::id(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
            ]);

            DB::commit();
            
            // Auto-update AR Perbulan saat status berubah ke "Selesai"
            app(\App\Services\ArPerbulanFinlogService::class)->updateAROnSelesai(
                $this->peminjaman->id_debitur,
                now()
            );
            
            // Reload peminjaman dengan relasi debitur untuk notifikasi
            $this->peminjaman->refresh();
            $this->peminjaman->load('debitur');
            ListNotifSFinlog::menuPeminjaman($history->status, $this->peminjaman);
            
            $this->reset(['bukti_transfer']);
            $this->dispatch('alert', icon: 'success', title: 'Berhasil', text: 'Bukti transfer berhasil diupload');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', icon: 'error', title: 'Error', text: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.sfinlog.peminjaman.detail')
            ->layout('layouts.app', [
                'title' => 'Detail Peminjaman Dana - SFinlog',
            ]);
    }
}
