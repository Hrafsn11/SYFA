{{-- Activity Tab - Matching Original Design --}}
<div class="mb-4">
    <h5 class="mb-0">Aktivitas Terakhir</h5>
</div>

<hr class="my-3">

@if (count($allHistory) === 0)
    {{-- Empty state untuk tidak ada history --}}
    <div id="activity-empty" class="text-center py-5">
        <div class="mb-3">
            <i class="ti ti-clipboard-list display-4 text-muted"></i>
        </div>
        <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
        <p class="text-muted mb-0">Aktivitas akan muncul setelah proses validasi dimulai.</p>
    </div>
@else
    {{-- Timeline Container --}}
    <div id="timeline-container">
        @foreach ($allHistory as $key => $history)
            <div class="activity-item mb-4">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-sm">
                                    @php
                                        $statusConfig = [
                                            'Submit Dokumen' => ['icon' => 'ti-send', 'color' => 'warning'],
                                            'Dokumen Tervalidasi' => ['icon' => 'ti-circle-check', 'color' => 'success'],
                                            'Validasi Ditolak' => ['icon' => 'ti-circle-x', 'color' => 'danger'],
                                            'Debitur Setuju' => ['icon' => 'ti-user-check', 'color' => 'success'],
                                            'Pengajuan Ditolak Debitur' => ['icon' => 'ti-user-x', 'color' => 'danger'],
                                            'Disetujui oleh CEO SKI' => ['icon' => 'ti-crown', 'color' => 'success'],
                                            'Ditolak oleh CEO SKI' => ['icon' => 'ti-crown-off', 'color' => 'danger'],
                                            'Disetujui oleh Direktur SKI' => ['icon' => 'ti-building-bank', 'color' => 'success'],
                                            'Ditolak oleh Direktur SKI' => ['icon' => 'ti-building-bank', 'color' => 'danger'],
                                            'Generate Kontrak' => ['icon' => 'ti-file-certificate', 'color' => 'info'],
                                            'Upload Dokumen' => ['icon' => 'ti-upload', 'color' => 'info'],
                                            'Menunggu Konfirmasi Debitur' => ['icon' => 'ti-clock', 'color' => 'warning'],
                                            'Dana Sudah Dicairkan' => ['icon' => 'ti-wallet', 'color' => 'success'],
                                            'Konfirmasi Disetujui Debitur' => ['icon' => 'ti-file-check', 'color' => 'success'],
                                            'Konfirmasi Ditolak Debitur' => ['icon' => 'ti-file-x', 'color' => 'danger'],
                                            'Dana Dicairkan' => ['icon' => 'ti-cash', 'color' => 'success'],
                                            'Proses Restrukturisasi' => ['icon' => 'ti-refresh', 'color' => 'info'],
                                            'Peminjaman Direstrukturisasi' => ['icon' => 'ti-check', 'color' => 'primary'],
                                        ];
                                        $config = $statusConfig[$history->status] ?? ['icon' => 'ti-circle', 'color' => 'secondary'];
                                    @endphp
                                    <span class="avatar-initial rounded-circle bg-label-{{ $config['color'] }}">
                                        <i class="ti {{ $config['icon'] }}"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                @php
                                    $displayText = '';
                                    $description = '';
                                    switch ($history->status) {
                                        case 'Submit Dokumen':
                                            $displayText = 'Validasi Dokumen';
                                            $description = 'Pengajuan sedang dalam proses validasi.';
                                            break;
                                        case 'Dokumen Tervalidasi':
                                            $displayText = 'Dokumen Tervalidasi';
                                            $description = 'Dokumen telah divalidasi dan disetujui.';
                                            break;
                                        case 'Validasi Ditolak':
                                            $displayText = 'Validasi Ditolak';
                                            $description = 'Dokumen ditolak pada tahap validasi.';
                                            break;
                                        case 'Debitur Setuju':
                                            $displayText = 'Persetujuan Debitur';
                                            $description = 'Debitur telah menyetujui pengajuan.';
                                            break;
                                        case 'Pengajuan Ditolak Debitur':
                                            $displayText = 'Ditolak Debitur';
                                            $description = 'Pengajuan ditolak oleh debitur.';
                                            break;
                                        case 'Disetujui oleh CEO SKI':
                                            $displayText = 'Disetujui CEO SKI';
                                            $description = 'Pengajuan disetujui oleh CEO SKI.';
                                            break;
                                        case 'Ditolak oleh CEO SKI':
                                            $displayText = 'Ditolak CEO SKI';
                                            $description = 'Pengajuan ditolak oleh CEO SKI.';
                                            break;
                                        case 'Disetujui oleh Direktur SKI':
                                            $displayText = 'Disetujui Direktur SKI';
                                            $description = 'Pengajuan disetujui oleh Direktur SKI.';
                                            break;
                                        case 'Ditolak oleh Direktur SKI':
                                            $displayText = 'Ditolak Direktur SKI';
                                            $description = 'Pengajuan ditolak oleh Direktur SKI.';
                                            break;
                                        case 'Generate Kontrak':
                                            $displayText = 'Generate Kontrak';
                                            $description = 'Kontrak peminjaman telah digenerate.';
                                            break;
                                        case 'Upload Dokumen':
                                            $displayText = 'Upload Dokumen Transfer';
                                            $description = 'Dokumen bukti transfer telah diupload.';
                                            break;
                                        case 'Menunggu Konfirmasi Debitur':
                                            $displayText = 'Upload Bukti Transfer';
                                            $description = 'Bukti transfer telah diupload. Menunggu konfirmasi dari debitur.';
                                            break;
                                        case 'Dana Sudah Dicairkan':
                                            $displayText = 'Dana Sudah Dicairkan';
                                            $description = 'Dana telah dicairkan dan dikonfirmasi oleh debitur.';
                                            break;
                                        case 'Konfirmasi Disetujui Debitur':
                                            $displayText = 'Konfirmasi Disetujui Debitur';
                                            $description = 'Bukti transfer disetujui oleh debitur.';
                                            break;
                                        case 'Konfirmasi Ditolak Debitur':
                                            $displayText = 'Konfirmasi Ditolak Debitur';
                                            $description = 'Bukti transfer ditolak oleh debitur.';
                                            break;
                                        case 'Dana Dicairkan':
                                            $displayText = 'Dana Dicairkan';
                                            $description = 'Proses pencairan dana telah selesai.';
                                            break;
                                        case 'Proses Restrukturisasi':
                                            $displayText = 'Proses Restrukturisasi';
                                            $description = 'Peminjaman sedang dalam proses restrukturisasi.';
                                            break;
                                        case 'Peminjaman Direstrukturisasi':
                                            $displayText = 'Peminjaman Direstrukturisasi';
                                            $description = 'Restrukturisasi peminjaman telah selesai.';
                                            break;
                                        default:
                                            $displayText = $history->status;
                                            $description = 'Status: ' . $history->status;
                                    }
                                @endphp
                                <h6 class="mb-1">{!! $displayText !!}</h6>
                                <p class="text-muted mb-0 small">{{ $description }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($history->created_at)->format('d M Y') }}
                        </small>
                    </div>
                    <div class="col-6 col-md-3 text-end">
                        @if ($history->status == 'Generate Kontrak')
                            <button type="button" class="btn btn-icon btn-sm btn-label-primary"
                                title="Preview Kontrak" wire:click="previewKontrak">
                                <i class="ti ti-file-text"></i>
                            </button>
                        @elseif($history->status == 'Menunggu Konfirmasi Debitur' && $upload_bukti_transfer)
                            <a href="{{ asset('storage/' . $upload_bukti_transfer) }}" 
                               target="_blank"
                               class="btn btn-icon btn-sm btn-label-primary">
                                <i class="ti ti-file-text"></i>
                            </a>
                        @elseif(
                            $history->status != 'Submit Dokumen' &&
                            $history->status != 'Upload Dokumen' &&
                            $history->status != 'Menunggu Konfirmasi Debitur' &&
                            $history->status != 'Dana Sudah Dicairkan')
                            <button type="button" class="btn btn-icon btn-sm btn-label-primary" title="Detail"
                                wire:click="showHistoryDetail('{{ $history->id_history_status_pengajuan_pinjaman }}')">
                                <i class="ti ti-file"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
