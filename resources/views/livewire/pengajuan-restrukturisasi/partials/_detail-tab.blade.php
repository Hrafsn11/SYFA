@php
    $currentStep = $pengajuan->current_step ?? 1;
    $currentStatus = $pengajuan->status ?? 'Draft';

    $evaluasi = \App\Models\EvaluasiPengajuanRestrukturisasi::where(
        'id_pengajuan_restrukturisasi',
        $pengajuan->id_pengajuan_restrukturisasi,
    )->first();

    $hasEvaluasi = !is_null($evaluasi);

    // Status checks
    $isDraft = $currentStatus === 'Draft';
    $isPerbaikanDokumen = $currentStatus === 'Perbaikan Dokumen';
    $isPerluEvaluasiUlang = $currentStatus === 'Perlu Evaluasi Ulang';
    $isRejectedStatus = in_array($currentStatus, ['Draft', 'Ditolak']);

    $isEditModeRequested = request()->query('edit') === 'true';
    $isEditDokumenMode = request()->query('edit-dokumen') === 'true';

    $canSubmitPengajuan = $isDraft || ($isPerbaikanDokumen && $currentStep == 1);
    $canApproveStep2 = $currentStep == 2 && !$isRejectedStatus && $hasEvaluasi && !$isPerluEvaluasiUlang;
    $showEditButtonStep2 = $currentStep == 2 && $isPerluEvaluasiUlang && !$isEditModeRequested && $hasEvaluasi;
    $showEditModeStep2 = $currentStep == 2 && $isPerluEvaluasiUlang && $isEditModeRequested;

    $canApproveStep3 = $currentStep == 3 && !$isRejectedStatus;
    $canApproveStep4 = $currentStep == 4 && !$isRejectedStatus;

    // Check if user can edit documents (when status is Perbaikan Dokumen)
    $canEditDokumen = $isPerbaikanDokumen && $currentStep == 1 && auth()->user()->can('pengajuan_restrukturisasi.ajukan_restrukturisasi');
@endphp

<div class="tab-pane fade show active" id="detail-restrukturisasi" role="tabpanel">
    {{-- Action Buttons --}}
    <div class="d-flex justify-content-between items-center mt-4 mb-3 mb-md-4 flex-wrap gap-2">
        <h5 class="mb-3 mb-md-4">Detail Pinjaman</h5>
        <div class="d-flex gap-2 flex-wrap">
            {{-- Perbaikan Dokumen: Edit Dokumen Button --}}
            @can('pengajuan_restrukturisasi.ajukan_restrukturisasi')
                @if ($canEditDokumen && !$isEditDokumenMode)
                    <a href="{{ request()->fullUrlWithQuery(['edit-dokumen' => 'true']) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>
                        Edit Dokumen
                    </a>
                @endif
            @endcan

            {{-- Step 1: Submit Pengajuan --}}
            @can('pengajuan_restrukturisasi.ajukan_restrukturisasi')
                @if ($canSubmitPengajuan)
                    <button type="button" class="btn btn-success" onclick="submitPengajuan()" id="btnSubmitPengajuan">
                        <i class="fas fa-paper-plane me-2"></i>
                        {{ $isPerbaikanDokumen ? 'Submit Ulang Pengajuan' : 'Submit Pengajuan' }}
                    </button>
                @endif
            @endcan

            {{-- Step 2: Approval --}}
            @can('pengajuan_restrukturisasi.validasi_dokumen')
                @if ($canApproveStep2)
                    <button type="button" class="btn btn-primary btn-approve" id="btnSetujuiPeminjaman"
                        onclick="handleApprove(2)">
                        <i class="fas fa-check me-2"></i>
                        Setujui Restrukturisasi
                    </button>
                @elseif($showEditButtonStep2)
                    {{-- Show "Edit Evaluasi" button when Perlu Evaluasi Ulang --}}
                    <a href="{{ request()->fullUrlWithQuery(['edit' => 'true']) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>
                        Edit Evaluasi
                    </a>
                @elseif($currentStep == 2 && !$isRejectedStatus && !$hasEvaluasi)
                    {{-- Show info badge if at Step 2 but evaluation not saved yet --}}
                    <span class="badge bg-warning text-white d-flex gap-2 align-items-center px-3 py-2">
                        <i class="fas fa-info-circle"></i>
                        Simpan Evaluasi terlebih dahulu
                    </span>
                @endif
            @endcan

            {{-- Step 3: CEO Approval --}}
            @can('pengajuan_restrukturisasi.persetujuan_ceo_ski')
                @if ($canApproveStep3)
                    <button type="button" class="btn btn-warning btn-approve" id="btnPersetujuanCEO"
                        onclick="handleApprove(3)">
                        <i class="fas fa-crown me-2"></i>
                        Setujui Restrukturisasi
                    </button>
                @endif
            @endcan

            {{-- Step 4: Direktur Approval --}}
            @can('pengajuan_restrukturisasi.persetujuan_direktur')
                @if ($canApproveStep4)
                    <button type="button" class="btn btn-info btn-approve" id="btnPersetujuanDirektur"
                        onclick="handleApprove(4)">
                        <i class="fas fa-briefcase me-2"></i>
                        Setujui Restrukturisasi
                    </button>
                @endif
            @endcan
        </div>
    </div>

    <hr class="my-3 my-md-4">

    {{-- Form Upload Dokumen (shown when status is Perbaikan Dokumen) --}}
    @if ($canEditDokumen)
        @include('livewire.pengajuan-restrukturisasi.partials._dokumen-upload-form', [
            'pengajuan' => $pengajuan,
            'histories' => $histories,
            'isEditDokumenMode' => $isEditDokumenMode,
        ])
    @endif

    {{-- Detail Information --}}
    <div class="row">
        {{-- Left Column --}}
        <div class="col-md-6">
            <table class="table table-borderless table-sm">
                <tbody>
                    <tr>
                        <td class="text-nowrap" style="width: 35%;"><strong>Nama Perusahaan:</strong></td>
                        <td>{{ $pengajuan->nama_perusahaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>NPWP:</strong></td>
                        <td>{{ $pengajuan->npwp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Nama PIC:</strong></td>
                        <td>{{ $pengajuan->nama_pic ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Jabatan PIC:</strong></td>
                        <td>{{ $pengajuan->jabatan_pic ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Kontrak Pembiayaan:</strong></td>
                        <td>{{ $pengajuan->nomor_kontrak_pembiayaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Jenis Pembiayaan:</strong></td>
                        <td>
                            @if ($pengajuan->jenis_pembiayaan)
                                <span class="badge bg-label-primary">{{ $pengajuan->jenis_pembiayaan }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Right Column --}}
        <div class="col-md-6">
            <table class="table table-borderless table-sm">
                <tbody>
                    <tr>
                        <td class="text-nowrap" style="width: 40%;"><strong>Plafon Awal:</strong></td>
                        <td>{{ $pengajuan->jumlah_plafon_awal ? 'Rp ' . number_format($pengajuan->jumlah_plafon_awal, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Sisa Pokok Belum Bayar:</strong></td>
                        <td class="fw-semibold">
                            {{ $pengajuan->sisa_pokok_belum_dibayar ? 'Rp ' . number_format($pengajuan->sisa_pokok_belum_dibayar, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Tunggakan Margin/Bunga:</strong></td>
                        <td class="{{ $pengajuan->tunggakan_margin_bunga > 0 ? 'text-danger fw-semibold' : '' }}">
                            {{ $pengajuan->tunggakan_margin_bunga ? 'Rp ' . number_format($pengajuan->tunggakan_margin_bunga, 0, ',', '.') : 'Rp 0' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Jatuh Tempo Terakhir:</strong></td>
                        <td>{{ $pengajuan->jatuh_tempo_terakhir ? \Carbon\Carbon::parse($pengajuan->jatuh_tempo_terakhir)->format('d/m/Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Status Saat Ini (DPD):</strong></td>
                        <td>
                            @if ($pengajuan->status_dpd !== null)
                                @php
                                    $dpdClass =
                                        $pengajuan->status_dpd > 90
                                            ? 'danger'
                                            : ($pengajuan->status_dpd > 30
                                                ? 'warning'
                                                : 'info');
                                @endphp
                                @if ($pengajuan->status_dpd > 0)
                                    <span class="badge bg-label-{{ $dpdClass }}">{{ $pengajuan->status_dpd }}
                                        Hari</span>
                                @else
                                    <span class="badge bg-label-success">0 Hari (Lancar)</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Tanggal Pengajuan:</strong></td>
                        <td>{{ $pengajuan->created_at ? $pengajuan->created_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Additional Info --}}
        <div class="col-12 mt-3">
            <div class="alert alert-light py-2" role="alert">
                <p class="mb-1"><strong>Alasan Restrukturisasi:</strong></p>
                <small>{{ $pengajuan->alasan_restrukturisasi ?? 'Tidak ada keterangan' }}</small>
            </div>
            <div class="alert alert-light py-2" role="alert">
                <p class="mb-1"><strong>Rencana Pemulihan Usaha:</strong></p>
                <small>{{ $pengajuan->rencana_pemulihan_usaha ?? 'Tidak ada keterangan' }}</small>
            </div>
            <div class="alert alert-light py-2" role="alert">
                <p class="mb-2"><strong>Jenis Restrukturisasi yang Diajukan:</strong></p>
                @if ($pengajuan->jenis_restrukturisasi && count($pengajuan->jenis_restrukturisasi) > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($pengajuan->jenis_restrukturisasi as $jenis)
                            @php
                                $badgeColor = match (true) {
                                    stripos($jenis, 'penurunan') !== false => 'success',
                                    stripos($jenis, 'perpanjangan') !== false => 'info',
                                    stripos($jenis, 'pengurangan') !== false => 'warning',
                                    stripos($jenis, 'lainnya') !== false => 'secondary',
                                    default => 'primary',
                                };
                            @endphp
                            <span class="badge bg-label-{{ $badgeColor }}">{{ $jenis }}</span>
                        @endforeach
                    </div>
                @else
                    <small class="text-muted">Belum ada jenis restrukturisasi yang dipilih</small>
                @endif
            </div>
            @if ($pengajuan->jenis_restrukturisasi_lainnya)
                <div class="alert alert-light border py-2" role="alert">
                    <p class="mb-1"><strong>Keterangan Tambahan:</strong></p>
                    <small>{{ $pengajuan->jenis_restrukturisasi_lainnya }}</small>
                </div>
            @endif
        </div>
    </div>

    {{-- Evaluasi Forms (shown from Step 2 onwards) --}}
    @if ($currentStep >= 2)
        @include('livewire.pengajuan-restrukturisasi.partials._evaluasi-forms', ['evaluasi' => $evaluasi])
    @endif
</div>
