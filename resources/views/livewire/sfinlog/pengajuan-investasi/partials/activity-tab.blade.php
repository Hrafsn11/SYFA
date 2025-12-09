<div id="content-activity">
    <div class="mb-4">
        <h5 class="mb-0">Aktivitas Terakhir</h5>
    </div>

    <hr class="my-3">

    @if($pengajuan->histories->isEmpty())
    <!-- Empty state -->
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="ti ti-clipboard-list display-4 text-muted"></i>
        </div>
        <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
        <p class="text-muted mb-0">
            Aktivitas akan muncul setelah proses validasi dimulai.
        </p>
    </div>
    @else
    <!-- Timeline -->
    <div class="timeline">
        @foreach($pengajuan->histories->sortByDesc('created_at') as $history)
        <div class="activity-item mb-4">
            <div class="row align-items-start">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                @php
                                    $statusIcons = [
                                        'Menunggu Validasi Finance SKI' => ['color' => 'info', 'icon' => 'ti-clock'],
                                        'Menunggu Persetujuan CEO Finlog' => ['color' => 'warning', 'icon' => 'ti-clock'],
                                        'Disetujui CEO Finlog' => ['color' => 'success', 'icon' => 'ti-check'],
                                        'Ditolak Finance SKI' => ['color' => 'danger', 'icon' => 'ti-x'],
                                        'Ditolak CEO Finlog' => ['color' => 'danger', 'icon' => 'ti-x'],
                                        'Informasi Rekening Terkirim' => ['color' => 'primary', 'icon' => 'ti-send'],
                                        'Bukti Transfer Diupload' => ['color' => 'success', 'icon' => 'ti-upload'],
                                        'Selesai' => ['color' => 'success', 'icon' => 'ti-check-circle'],
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
                                        'Menunggu Validasi Finance SKI' => 'Pengajuan Disubmit',
                                        'Menunggu Persetujuan CEO Finlog' => 'Validasi Finance SKI - Disetujui',
                                        'Disetujui CEO Finlog' => 'Validasi CEO Finlog - Disetujui',
                                        'Ditolak Finance SKI' => 'Validasi Finance SKI - Ditolak',
                                        'Ditolak CEO Finlog' => 'Validasi CEO Finlog - Ditolak',
                                        'Informasi Rekening Terkirim' => 'Informasi Rekening Dikirim ke Investor',
                                        'Bukti Transfer Diupload' => 'Bukti Transfer Diupload',
                                        'Selesai' => 'Kontrak Digenerate - Proses Selesai',
                                    ];
                                @endphp
                                {{ $statusTitles[$history->status] ?? $history->status }}
                            </h6>
                            <p class="text-muted mb-0 small">
                                @php
                                    $statusDescriptions = [
                                        'Menunggu Validasi Finance SKI' => 'Pengajuan investasi telah disubmit dan menunggu validasi dari Finance SKI.',
                                        'Menunggu Persetujuan CEO Finlog' => 'Validasi Finance SKI berhasil, menunggu persetujuan CEO Finlog.',
                                        'Disetujui CEO Finlog' => 'Pengajuan telah disetujui oleh CEO Finlog.',
                                        'Ditolak Finance SKI' => $history->catatan_penolakan 
                                            ? '<span class="text-danger fw-semibold">Alasan: </span>' . $history->catatan_penolakan
                                            : 'Pengajuan ditolak oleh Finance SKI.',
                                        'Ditolak CEO Finlog' => $history->catatan_penolakan 
                                            ? '<span class="text-danger fw-semibold">Alasan: </span>' . $history->catatan_penolakan
                                            : 'Pengajuan ditolak oleh CEO Finlog.',
                                        'Informasi Rekening Terkirim' => 'Informasi nomor rekening telah dikirim ke investor untuk transfer dana.',
                                        'Bukti Transfer Diupload' => 'Bukti transfer dana investasi telah diupload.',
                                        'Selesai' => 'Kontrak investasi telah digenerate. Proses pengajuan selesai.',
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
                        @if($history->status == 'Bukti Transfer Diupload' && $pengajuan->upload_bukti_transfer)
                            <button type="button" class="btn btn-sm btn-primary" onclick="previewBuktiTransfer('{{ asset('storage/' . $pengajuan->upload_bukti_transfer) }}')">
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
