<div id="content-activity">
    <div class="mb-4">
        <h5 class="mb-0">Aktivitas Terakhir</h5>
    </div>

    <hr class="my-3">

    @if($peminjaman->histories->isEmpty())
    <!-- Empty state -->
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="ti ti-clipboard-list display-4 text-muted"></i>
        </div>
        <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
        <p class="text-muted mb-0">
            Aktivitas akan muncul setelah proses workflow dimulai.
        </p>
    </div>
    @else
    <!-- Timeline -->
    <div class="timeline">
        @foreach($peminjaman->histories->sortByDesc('created_at') as $history)
        <div class="activity-item mb-4">
            <div class="row align-items-start">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                @php
                                    $statusIcons = [
                                        'Pengajuan Disubmit' => ['color' => 'primary', 'icon' => 'ti-send'],
                                        'Disetujui Investment Officer' => ['color' => 'success', 'icon' => 'ti-check'],
                                        'Ditolak Investment Officer' => ['color' => 'danger', 'icon' => 'ti-x'],
                                        'Disetujui Debitur' => ['color' => 'success', 'icon' => 'ti-check'],
                                        'Ditolak Debitur' => ['color' => 'danger', 'icon' => 'ti-x'],
                                        'Disetujui SKI Finance' => ['color' => 'success', 'icon' => 'ti-check'],
                                        'Ditolak SKI Finance' => ['color' => 'danger', 'icon' => 'ti-x'],
                                        'Disetujui CEO Finlog' => ['color' => 'success', 'icon' => 'ti-check'],
                                        'Ditolak CEO Finlog' => ['color' => 'danger', 'icon' => 'ti-x'],
                                        'Kontrak Digenerate' => ['color' => 'info', 'icon' => 'ti-file-text'],
                                        'Bukti Transfer Diupload' => ['color' => 'success', 'icon' => 'ti-upload'],
                                    ];
                                    
                                    $statusConfig = $statusIcons[$history->status] ?? ['color' => 'primary', 'icon' => 'ti-clock'];
                                @endphp
                                <span class="avatar-initial rounded-circle bg-label-{{ $statusConfig['color'] }}">
                                    <i class="ti {{ $statusConfig['icon'] }}"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                @php
                                    $statusTitles = [
                                        'Pengajuan Disubmit' => 'Pengajuan Disubmit',
                                        'Disetujui Investment Officer' => 'Validasi Investment Officer - Disetujui',
                                        'Ditolak Investment Officer' => 'Validasi Investment Officer - Ditolak',
                                        'Disetujui Debitur' => 'Persetujuan Debitur - Disetujui',
                                        'Ditolak Debitur' => 'Persetujuan Debitur - Ditolak',
                                        'Disetujui SKI Finance' => 'Persetujuan SKI Finance - Disetujui',
                                        'Ditolak SKI Finance' => 'Persetujuan SKI Finance - Ditolak',
                                        'Disetujui CEO Finlog' => 'Persetujuan CEO Finlog - Disetujui',
                                        'Ditolak CEO Finlog' => 'Persetujuan CEO Finlog - Ditolak',
                                        'Kontrak Digenerate' => 'Kontrak Pinjaman Digenerate',
                                        'Bukti Transfer Diupload' => 'Bukti Transfer Diupload',
                                    ];
                                @endphp
                                {{ $statusTitles[$history->status] ?? $history->status }}
                            </h6>
                            <p class="text-muted mb-0 small">
                                @php
                                    $statusDescriptions = [
                                        'Pengajuan Disubmit' => 'Pengajuan peminjaman dana telah disubmit dan menunggu validasi Investment Officer.',
                                        'Disetujui Investment Officer' => $history->bagi_hasil_disetujui 
                                            ? 'Validasi IO berhasil dengan bagi hasil: <span class="fw-semibold text-success">' . number_format($history->bagi_hasil_disetujui, 2) . '%</span>' . ($history->catatan ? '. Catatan: ' . $history->catatan : '')
                                            : 'Pengajuan telah divalidasi dan disetujui oleh Investment Officer.',
                                        'Ditolak Investment Officer' => $history->catatan 
                                            ? '<span class="text-danger fw-semibold">Alasan: </span>' . $history->catatan
                                            : 'Pengajuan ditolak oleh Investment Officer.',
                                        'Disetujui Debitur' => 'Debitur menyetujui pengajuan peminjaman.',
                                        'Ditolak Debitur' => $history->catatan 
                                            ? '<span class="text-danger fw-semibold">Alasan: </span>' . $history->catatan
                                            : 'Pengajuan ditolak oleh Debitur.',
                                        'Disetujui SKI Finance' => 'Pengajuan telah disetujui oleh SKI Finance.',
                                        'Ditolak SKI Finance' => $history->catatan 
                                            ? '<span class="text-danger fw-semibold">Alasan: </span>' . $history->catatan
                                            : 'Pengajuan ditolak oleh SKI Finance.',
                                        'Disetujui CEO Finlog' => 'Pengajuan telah disetujui oleh CEO Finlog.',
                                        'Ditolak CEO Finlog' => $history->catatan 
                                            ? '<span class="text-danger fw-semibold">Alasan: </span>' . $history->catatan
                                            : 'Pengajuan ditolak oleh CEO Finlog.',
                                        'Kontrak Digenerate' => 'Kontrak pinjaman telah digenerate dan siap untuk ditandatangani.',
                                        'Bukti Transfer Diupload' => 'Bukti transfer dana pinjaman telah diupload.',
                                    ];
                                @endphp
                                {!! $statusDescriptions[$history->status] ?? $history->status !!}
                            </p>
                            @if($history->approvedBy)
                                <small class="text-muted">
                                    oleh {{ $history->approvedBy->name ?? '-' }}
                                </small>
                            @elseif($history->rejectedBy)
                                <small class="text-danger">
                                    ditolak oleh {{ $history->rejectedBy->name ?? '-' }}
                                </small>
                            @elseif($history->submitBy)
                                <small class="text-muted">
                                    oleh {{ $history->submitBy->name ?? '-' }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <div class="d-flex flex-column align-items-md-end gap-2">
                        <small class="text-muted">
                            @if($history->date && $history->time)
                                {{ \Carbon\Carbon::parse($history->date)->format('d M Y') }}, {{ \Carbon\Carbon::parse($history->time)->format('H:i') }}
                            @elseif($history->created_at)
                                {{ \Carbon\Carbon::parse($history->created_at)->format('d M Y, H:i') }}
                            @else
                                -
                            @endif
                        </small>
                        @if($history->status == 'Bukti Transfer Diupload' && $peminjaman->bukti_transfer)
                            <button type="button" class="btn btn-sm btn-primary" onclick="previewBuktiTransfer('{{ asset('storage/' . $peminjaman->bukti_transfer) }}')">
                                <i class="ti ti-eye me-1"></i>
                                Lihat Bukti
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @if(!$loop->last)
                <hr class="my-3">
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>