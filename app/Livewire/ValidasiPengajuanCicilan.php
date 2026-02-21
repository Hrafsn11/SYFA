<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PengajuanCicilan;
use App\Models\EvaluasiPengajuanCicilan;
use App\Models\EvaluasiKelengkapanDokumen;
use App\Models\EvaluasiKelayakanDebitur;
use App\Models\EvaluasiAnalisaCicilan;
use App\Models\PersetujuanKomiteCicilan;
use App\Models\HistoryStatusPengajuanCicilan;
use App\Helpers\ListNotifSFinance;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ValidasiPengajuanCicilan extends Component
{
    use WithFileUploads;
    
    // Properties
    public $pengajuanId;
    public $pengajuan;
    public $debitur;
    
    // Section E: Committee (Livewire managed)
    public $committee = [];
    public $committeeFiles = [];
    
    // History
    public $allHistory = null;
    
    // Modal properties
    public $rejectNote = '';
    public $pendingRejectStep = null;
    public $showRejectModal = false;

    public function mount($id)
    {
        $this->pengajuanId = $id;
        $this->loadData();
    }

    public function loadData()
    {
        $this->pengajuan = PengajuanCicilan::with(['debitur', 'peminjaman'])->findOrFail($this->pengajuanId);
        $this->debitur = $this->pengajuan->debitur;
        
        // Load existing committee data
        $evaluasi = EvaluasiPengajuanCicilan::where('id_pengajuan_cicilan', $this->pengajuanId)->first();
        if ($evaluasi) {
            $persetujuans = PersetujuanKomiteCicilan::where('id_evaluasi_cicilan', $evaluasi->id_evaluasi_cicilan)
                ->orderBy('urutan')
                ->get();
                
            if ($persetujuans->count() > 0) {
                $this->committee = $persetujuans->map(function($p) {
                    return [
                        'nama_anggota' => $p->nama_anggota,
                        'jabatan' => $p->jabatan,
                        'tanggal_persetujuan' => $p->tanggal_persetujuan ? $p->tanggal_persetujuan->toDateString() : null,
                        'ttd_digital' => $p->ttd_digital,
                    ];
                })->toArray();
            }
        }

        // Initialize dengan minimal 1 row
        if (empty($this->committee)) {
            $this->committee = [
                ['nama_anggota' => '', 'jabatan' => '', 'tanggal_persetujuan' => null, 'ttd_digital' => null]
            ];
        }

        // Load history
        $this->allHistory = HistoryStatusPengajuanCicilan::where('id_pengajuan_cicilan', $this->pengajuanId)
            ->with(['submittedBy', 'approvedBy', 'rejectedBy'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * ✅ PURE LIVEWIRE: Save evaluasi (called from JavaScript with collected data)
     */
    public function saveEvaluasiFromJS($payload)
    {
        Log::info('saveEvaluasiFromJS called', ['payload_keys' => array_keys($payload ?? [])]);
        
        // Permission check
        if (!Gate::allows('pengajuan_cicilan.validasi_dokumen')) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menyimpan evaluasi');
            $this->dispatch('evaluasiError', message: 'Unauthorized');
            return;
        }

        // Validate payload structure
        if (empty($payload['kelengkapan']) || empty($payload['kelayakan']) || empty($payload['analisa'])) {
            session()->flash('error', 'Data evaluasi tidak lengkap');
            $this->dispatch('evaluasiError', message: 'Data evaluasi tidak lengkap');
            return;
        }

        try {
            DB::beginTransaction();

            // Get or create evaluasi header
            $evaluasi = EvaluasiPengajuanCicilan::firstOrCreate([
                'id_pengajuan_cicilan' => $this->pengajuanId,
            ]);

            // Update header data
            $evaluasi->rekomendasi = $payload['rekomendasi'] ?? null;
            $evaluasi->justifikasi_rekomendasi = $payload['justifikasi_rekomendasi'] ?? null;
            $evaluasi->evaluator_id = auth()->id();
            $evaluasi->evaluated_at = Carbon::now();
            $evaluasi->save();

            Log::info('Evaluasi header saved', ['id' => $evaluasi->id_evaluasi_cicilan]);

            // ✅ Save Section A: Kelengkapan Dokumen
            if (!empty($payload['kelengkapan']) && is_array($payload['kelengkapan'])) {
                EvaluasiKelengkapanDokumen::where('id_evaluasi_cicilan', $evaluasi->id_evaluasi_cicilan)->delete();
                
                foreach ($payload['kelengkapan'] as $idx => $row) {
                    EvaluasiKelengkapanDokumen::create([
                        'id_evaluasi_cicilan' => $evaluasi->id_evaluasi_cicilan,
                        'nama_dokumen' => $row['nama_dokumen'] ?? '',
                        'status' => $row['status'] ?? 'Tidak',
                        'catatan' => $row['catatan'] ?? null,
                        'urutan' => $row['urutan'] ?? ($idx + 1),
                    ]);
                }
                Log::info('Section A saved', ['count' => count($payload['kelengkapan'])]);
            }

            // ✅ Save Section B: Kelayakan Debitur
            if (!empty($payload['kelayakan']) && is_array($payload['kelayakan'])) {
                EvaluasiKelayakanDebitur::where('id_evaluasi_cicilan', $evaluasi->id_evaluasi_cicilan)->delete();
                
                foreach ($payload['kelayakan'] as $idx => $row) {
                    EvaluasiKelayakanDebitur::create([
                        'id_evaluasi_cicilan' => $evaluasi->id_evaluasi_cicilan,
                        'kriteria' => $row['kriteria'] ?? '',
                        'status' => $row['status'] ?? 'Tidak',
                        'catatan' => $row['catatan'] ?? null,
                        'urutan' => $row['urutan'] ?? ($idx + 1),
                    ]);
                }
                Log::info('Section B saved', ['count' => count($payload['kelayakan'])]);
            }

            // ✅ Save Section C: Analisa Restrukturisasi
            if (!empty($payload['analisa']) && is_array($payload['analisa'])) {
                EvaluasiAnalisaCicilan::where('id_evaluasi_cicilan', $evaluasi->id_evaluasi_cicilan)->delete();
                
                foreach ($payload['analisa'] as $idx => $row) {
                    EvaluasiAnalisaCicilan::create([
                        'id_evaluasi_cicilan' => $evaluasi->id_evaluasi_cicilan,
                        'aspek' => $row['aspek'] ?? '',
                        'evaluasi' => $row['evaluasi'] ?? null,
                        'catatan' => $row['catatan'] ?? null,
                        'urutan' => $row['urutan'] ?? ($idx + 1),
                    ]);
                }
                Log::info('Section C saved', ['count' => count($payload['analisa'])]);
            }

            // ✅ Save Section E: Persetujuan Komite (from Livewire property)
            if (!empty($this->committee) && is_array($this->committee)) {
                // Validate committee
                $this->validate([
                    'committee.*.nama_anggota' => 'required|string|max:255',
                    'committee.*.jabatan' => 'required|string|max:255',
                    'committee.*.tanggal_persetujuan' => 'required|date',
                    'committeeFiles.*' => 'nullable|image|max:2048',
                ], [
                    'committee.*.nama_anggota.required' => 'Nama anggota komite harus diisi',
                    'committee.*.jabatan.required' => 'Jabatan harus diisi',
                    'committee.*.tanggal_persetujuan.required' => 'Tanggal persetujuan harus diisi',
                ]);

                PersetujuanKomiteCicilan::where('id_evaluasi_cicilan', $evaluasi->id_evaluasi_cicilan)->delete();
                
                foreach ($this->committee as $idx => $row) {
                    $ttdPath = null;
                    
                    // Handle file upload
                    if (isset($this->committeeFiles[$idx]) && $this->committeeFiles[$idx]) {
                        try {
                            $file = $this->committeeFiles[$idx];
                            $filename = time() . '_' . $idx . '_' . $file->getClientOriginalName();
                            $ttdPath = $file->storeAs('restrukturisasi/ttd', $filename, 'public');
                        } catch (\Exception $e) {
                            Log::warning('File upload failed', ['error' => $e->getMessage()]);
                        }
                    } elseif (!empty($row['ttd_digital'])) {
                        $ttdPath = $row['ttd_digital'];
                    }

                    PersetujuanKomiteCicilan::create([
                        'id_evaluasi_cicilan' => $evaluasi->id_evaluasi_cicilan,
                        'nama_anggota' => $row['nama_anggota'],
                        'jabatan' => $row['jabatan'],
                        'tanggal_persetujuan' => $row['tanggal_persetujuan'],
                        'ttd_digital' => $ttdPath,
                        'user_id' => $row['user_id'] ?? null,
                        'urutan' => $idx + 1,
                    ]);
                }
                Log::info('Section E saved', ['count' => count($this->committee)]);
            }

            DB::commit();

            session()->flash('success', 'Evaluasi berhasil disimpan!');
            $this->dispatch('evaluasiSaved', message: 'Evaluasi berhasil disimpan!');
            
            // Reload data
            $this->loadData();

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $errors = collect($e->errors())->flatten()->join(', ');
            Log::error('Validation failed', ['errors' => $e->errors()]);
            session()->flash('error', 'Validasi gagal: ' . $errors);
            $this->dispatch('evaluasiError', message: 'Validasi gagal: ' . $errors);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save evaluasi error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Gagal menyimpan evaluasi: ' . $e->getMessage());
            $this->dispatch('evaluasiError', message: 'Gagal menyimpan evaluasi: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Submit pengajuan (Draft → Submit Dokumen)
     */
    public function submitPengajuan()
    {
        if (!Gate::allows('pengajuan_cicilan.ajukan_cicilan')) {
            $this->dispatch('evaluasiError', message: 'Unauthorized');
            return;
        }

        try {
            DB::beginTransaction();
            
            $this->pengajuan->status = 'Submit Dokumen';
            $this->pengajuan->current_step = 2;
            $this->pengajuan->save();

            HistoryStatusPengajuanCicilan::create([
                'id_pengajuan_cicilan' => $this->pengajuan->id_pengajuan_cicilan,
                'status' => $this->pengajuan->status,
                'current_step' => $this->pengajuan->current_step,
                'date' => Carbon::now()->toDateString(),
                'time' => Carbon::now()->toTimeString(),
                'submit_by' => auth()->id(),
            ]);

            // Reload pengajuan dengan relasi
            $this->pengajuan->load('debitur');

            DB::commit();

            // Kirim notifikasi saat pengajuan baru di-submit
            ListNotifSFinance::menuRestrukturisasi($this->pengajuan->status, $this->pengajuan, 1);

            session()->flash('success', 'Pengajuan berhasil disubmit!');
            $this->dispatch('evaluasiSaved', message: 'Pengajuan berhasil disubmit!');
            $this->loadData();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submit pengajuan error', ['message' => $e->getMessage()]);
            $this->dispatch('evaluasiError', message: 'Gagal submit: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Handle approve
     */
    public function handleApprove($step)
    {
        // Check permission based on step
        if ($step == 2 && !Gate::allows('pengajuan_cicilan.validasi_dokumen')) {
            $this->dispatch('evaluasiError', message: 'Unauthorized');
            return;
        }
        if ($step == 3 && !Gate::allows('pengajuan_cicilan.persetujuan_ceo_ski')) {
            $this->dispatch('evaluasiError', message: 'Unauthorized');
            return;
        }
        if ($step == 4 && !Gate::allows('pengajuan_cicilan.persetujuan_direktur')) {
            $this->dispatch('evaluasiError', message: 'Unauthorized');
            return;
        }

        try {
            DB::beginTransaction();
            
            $this->pengajuan->current_step = $step + 1;
            $this->pengajuan->status = ($step >= 4) ? 'Selesai' : 'Dalam Proses';
            $this->pengajuan->save();

            HistoryStatusPengajuanCicilan::create([
                'id_pengajuan_cicilan' => $this->pengajuan->id_pengajuan_cicilan,
                'status' => $this->pengajuan->status,
                'current_step' => $this->pengajuan->current_step,
                'date' => Carbon::now()->toDateString(),
                'time' => Carbon::now()->toTimeString(),
                'submit_by' => auth()->id(),
                'approve_by' => auth()->id(),
                'validasi_dokumen' => 'disetujui',
            ]);

            DB::commit();

            session()->flash('success', 'Persetujuan berhasil diproses!');
            $this->dispatch('decisionProcessed', message: 'Persetujuan berhasil diproses!');
            $this->loadData();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve error', ['message' => $e->getMessage()]);
            $this->dispatch('evaluasiError', message: 'Gagal approve: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Open reject modal
     */
    public function openRejectModal($step)
    {
        $this->pendingRejectStep = $step;
        $this->rejectNote = '';
        $this->showRejectModal = true;
    }

    /**
     * ✅ Close reject modal
     */
    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectNote = '';
        $this->pendingRejectStep = null;
    }

    /**
     * ✅ Submit rejection
     */
    public function submitRejection()
    {
        $this->validate([
            'rejectNote' => 'required|string|min:10',
        ], [
            'rejectNote.required' => 'Alasan penolakan harus diisi',
            'rejectNote.min' => 'Alasan penolakan minimal 10 karakter',
        ]);

        $step = $this->pendingRejectStep;

        try {
            DB::beginTransaction();

            // Rejection logic based on step
            if ($step === 2) {
                $this->pengajuan->status = 'Perbaikan Dokumen';
                $this->pengajuan->current_step = 1;
            } elseif ($step === 3) {
                $this->pengajuan->status = 'Perlu Evaluasi Ulang';
                $this->pengajuan->current_step = 2;
            } else {
                $this->pengajuan->status = 'Ditolak';
                $this->pengajuan->current_step = $step;
            }
            
            $this->pengajuan->save();

            HistoryStatusPengajuanCicilan::create([
                'id_pengajuan_cicilan' => $this->pengajuan->id_pengajuan_cicilan,
                'status' => $this->pengajuan->status,
                'current_step' => $this->pengajuan->current_step,
                'date' => Carbon::now()->toDateString(),
                'time' => Carbon::now()->toTimeString(),
                'submit_by' => auth()->id(),
                'reject_by' => auth()->id(),
                'validasi_dokumen' => 'ditolak',
                'catatan_validasi_dokumen' => $this->rejectNote,
                'catatan' => $this->rejectNote,
            ]);

            DB::commit();

            $this->closeRejectModal();
            session()->flash('success', 'Penolakan berhasil diproses');
            $this->dispatch('decisionProcessed', message: 'Penolakan berhasil diproses');
            $this->loadData();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject error', ['message' => $e->getMessage()]);
            $this->dispatch('evaluasiError', message: 'Gagal reject: ' . $e->getMessage());
        }
    }

    /**
     * Committee row management
     */
    public function addCommitteeRow()
    {
        $this->committee[] = [
            'nama_anggota' => '',
            'jabatan' => '',
            'tanggal_persetujuan' => null,
            'ttd_digital' => null
        ];
    }

    public function removeCommitteeRow($index)
    {
        if (count($this->committee) > 1 && isset($this->committee[$index])) {
            unset($this->committee[$index]);
            $this->committee = array_values($this->committee);
        }
        
        if (isset($this->committeeFiles[$index])) {
            unset($this->committeeFiles[$index]);
            $this->committeeFiles = array_values($this->committeeFiles);
        }
    }

    public function render()
    {
        return view('livewire.pengajuan-cicilan.detail');
    }
}
