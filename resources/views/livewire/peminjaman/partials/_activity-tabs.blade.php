<div class="tab-pane fade" id="activity" role="tabpanel">
    <div class="mb-4">
        <h5 class="mb-0">Aktivitas Terakhir</h5>
    </div>

    <hr class="my-3">

    @if($allHistory->isEmpty())
        <!-- Empty state untuk tidak ada history -->
        <div id="activity-empty" class="text-center py-5">
            <div class="mb-3">
                <i class="ti ti-clipboard-list display-4 text-muted"></i>
            </div>
            <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
            <p class="text-muted mb-0">Aktivitas akan muncul setelah proses validasi dimulai.</p>
        </div>
    @else
        <!-- Timeline Container - menampilkan data dari database -->
        <div id="timeline-container">
            @foreach($allHistory as $history)
                <div class="activity-item mb-4">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm">
                                        @php
                                            $statusConfig = [
                                                'Submit Dokumen' => ['icon' => 'ti-report-search', 'color' => 'warning'],
                                                'Dokumen Tervalidasi' => ['icon' => 'ti-file-text', 'color' => 'primary'],
                                                'Validasi Ditolak' => ['icon' => 'ti-circle-x', 'color' => 'danger'],
                                                'Debitur Setuju' => ['icon' => 'ti-file-text', 'color' => 'primary'],
                                                'Pengajuan Ditolak Debitur' => ['icon' => 'ti-user-x', 'color' => 'danger'],
                                                'Disetujui oleh CEO SKI' => ['icon' => 'ti-file-text', 'color' => 'primary'],
                                                'Ditolak oleh CEO SKI' => ['icon' => 'ti-crown-off', 'color' => 'danger'],
                                                'Disetujui oleh Direktur SKI' => ['icon' => 'ti-file-text', 'color' => 'primary'],
                                                'Ditolak oleh Direktur SKI' => ['icon' => 'ti-building-bank', 'color' => 'danger'],
                                                'Generate Kontrak' => ['icon' => 'ti-file-text', 'color' => 'primary'],
                                                'Dana Dicairkan' => ['icon' => 'ti-circle-check', 'color' => 'success'],
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
                                        switch($history->status) {
                                            case 'Submit Dokumen':
                                                $displayText = 'Validasi Dokumen';
                                                $description = 'Pengajuan sedang dalam proses validasi.';
                                                break;
                                            case 'Dokumen Tervalidasi':
                                                $displayText = 'Draft: Dokumen Tervalidasi <i class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui';
                                                $description = 'Pengajuan telah terkirim.';
                                                break;
                                            case 'Debitur Setuju':
                                                $displayText = 'Draft: Persetujuan Debitur <i class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui';
                                                $description = 'Pengajuan telah terkirim.';
                                                break;
                                            case 'Disetujui oleh CEO SKI':
                                                $displayText = 'Draft: Validasi CEO SKI <i class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui';
                                                $description = 'Pengajuan telah terkirim.';
                                                break;
                                            case 'Disetujui oleh Direktur SKI':
                                                $displayText = 'Draft: Validasi Direktur SKI <i class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui';
                                                $description = 'Pengajuan telah terkirim.';
                                                break;
                                            case 'Generate Kontrak':
                                                $displayText = 'Draft: Generate Kontrak <i class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui';
                                                $description = 'Pengajuan telah terkirim.';
                                                break;
                                            case 'Dana Dicairkan':
                                                $displayText = 'Selesai';
                                                $description = 'Proses pengajuan pinjaman telah selesai.';
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
                            @if($history->status == 'Dokumen Tervalidasi' || $history->status == 'Debitur Setuju' || $history->status == 'Disetujui oleh CEO SKI' || $history->status == 'Disetujui oleh Direktur SKI')
                                <button type="button" class="btn btn-icon btn-sm btn-label-primary" title="Detail" 
                                        onclick="showHistory('{{ $history->id_history_status_pengajuan_pinjaman }}')">
                                    <i class="ti ti-file"></i>
                                </button>
                            @elseif($history->status == 'Generate Kontrak')
                                <button type="button" class="btn btn-icon btn-sm btn-label-primary" title="Preview Kontrak" onclick="previewKontrakActivity()">
                                    <i class="ti ti-file-text"></i>
                                </button>
                            @elseif($history->status == 'Upload Dokumen')
                                <button type="button" class="btn btn-icon btn-sm btn-label-success" title="Upload Dokumen">
                                    <i class="ti ti-upload"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif


</div>