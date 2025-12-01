<div class="mb-4 mt-4">
    <h5 class="mb-0">Riwayat Aktivitas</h5>
</div>

<hr class="my-3">

@if($histories->isEmpty())
    <!-- Empty state untuk tidak ada history -->
    <div id="activity-empty" class="text-center py-5">
        <div class="mb-3">
            <i class="ti ti-clipboard-list display-4 text-muted"></i>
        </div>
        <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
        <p class="text-muted mb-0">Aktivitas akan muncul setelah pengajuan disubmit.</p>
    </div>
@else
    <!-- Timeline Container - menampilkan data dari database -->
    <div id="timeline-container">
        @foreach($histories as $key => $history)
                <div class="activity-item mb-4">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm">
                                        @php
                                            $statusConfig = [
                                                'Pengajuan Restrukturisasi' => ['icon' => 'ti-file-text', 'color' => 'info'],
                                                'Submit Dokumen' => ['icon' => 'ti-send', 'color' => 'warning'],
                                                'Dokumen Tervalidasi' => ['icon' => 'ti-circle-check', 'color' => 'success'],
                                                'Validasi Ditolak' => ['icon' => 'ti-circle-x', 'color' => 'danger'],
                                                'Perbaikan Dokumen' => ['icon' => 'ti-file-alert', 'color' => 'warning'],
                                                'Perlu Evaluasi Ulang' => ['icon' => 'ti-reload', 'color' => 'warning'],
                                                'Disetujui CEO SKI' => ['icon' => 'ti-crown', 'color' => 'success'],
                                                'Ditolak CEO SKI' => ['icon' => 'ti-crown-off', 'color' => 'danger'],
                                                'Disetujui Direktur SKI' => ['icon' => 'ti-building-bank', 'color' => 'success'],
                                                'Ditolak Direktur SKI' => ['icon' => 'ti-building-bank', 'color' => 'danger'],
                                                'Dalam Proses' => ['icon' => 'ti-clock', 'color' => 'info'],
                                                'Selesai' => ['icon' => 'ti-circle-check-filled', 'color' => 'success'],
                                                'Ditolak' => ['icon' => 'ti-circle-x', 'color' => 'danger'],
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
                                        $userName = '';
                                        
                                        // Get user name from relationships
                                        if($history->approvedBy) {
                                            $userName = $history->approvedBy->name;
                                        } elseif($history->rejectedBy) {
                                            $userName = $history->rejectedBy->name;
                                        } elseif($history->submittedBy) {
                                            $userName = $history->submittedBy->name;
                                        }
                                        
                                        // Determine display text and description based on status
                                        switch($history->status) {
                                            case 'Pengajuan Restrukturisasi':
                                                $displayText = 'Pengajuan Dibuat';
                                                $description = 'Pengajuan restrukturisasi telah dibuat';
                                                break;
                                            case 'Submit Dokumen':
                                            case 'Dalam Proses':
                                                $stepNames = [1 => 'Pengajuan', 2 => 'Evaluasi', 3 => 'CEO SKI', 4 => 'Direktur', 5 => 'Selesai'];
                                                $stepName = $stepNames[$history->current_step] ?? 'Step ' . $history->current_step;
                                                $displayText = '<span class="text-info">Submit ke ' . $stepName . '</span>';
                                                $description = $userName ? 'Disubmit oleh ' . $userName : 'Pengajuan disubmit untuk evaluasi';
                                                break;
                                            case 'Perbaikan Dokumen':
                                                $displayText = '<span class="text-warning">Perlu Perbaikan Dokumen</span>';
                                                $description = $userName ? 'Ditolak oleh ' . $userName . ' - Perlu perbaikan dokumen' : 'Pengajuan memerlukan perbaikan dokumen';
                                                break;
                                            case 'Perlu Evaluasi Ulang':
                                                $displayText = '<span class="text-warning">Perlu Evaluasi Ulang</span>';
                                                $description = $userName ? 'Ditolak oleh ' . $userName . ' - Perlu evaluasi ulang' : 'Pengajuan memerlukan evaluasi ulang';
                                                break;
                                            case 'Ditolak':
                                                $stepNames = [1 => 'Pengajuan', 2 => 'Evaluasi', 3 => 'CEO SKI', 4 => 'Direktur'];
                                                $stepName = $stepNames[$history->current_step] ?? 'Step ' . $history->current_step;
                                                $displayText = '<span class="text-danger">Ditolak pada ' . $stepName . '</span>';
                                                $description = $userName ? 'Ditolak oleh ' . $userName : 'Pengajuan ditolak';
                                                break;
                                            case 'Selesai':
                                                $displayText = '<span class="text-success">Restrukturisasi Selesai</span>';
                                                $description = 'Proses restrukturisasi telah diselesaikan';
                                                break;
                                            default:
                                                // For approved statuses
                                                if($history->validasi_dokumen === 'disetujui') {
                                                    $stepNames = [1 => 'Pengajuan', 2 => 'Evaluasi', 3 => 'CEO SKI', 4 => 'Direktur'];
                                                    $stepName = $stepNames[$history->current_step] ?? 'Step ' . $history->current_step;
                                                    $displayText = '<span class="text-success">Disetujui - ' . $stepName . '</span>';
                                                    $description = $userName ? 'Disetujui oleh ' . $userName : 'Pengajuan disetujui';
                                                } else {
                                                    $displayText = $history->status;
                                                    $description = 'Status: ' . $history->status;
                                                }
                                        }
                                    @endphp
                                    <h6 class="mb-1">{!! $displayText !!}</h6>
                                    <p class="text-muted mb-0 small">{{ $description }}</p>
                                    @if($history->catatan)
                                        <p class="text-muted mb-0 small mt-1"><em><i class="ti ti-message-circle me-1"></i>{{ $history->catatan }}</em></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <small class="text-muted d-block">
                                <i class="ti ti-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($history->created_at)->format('d M Y') }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="ti ti-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($history->created_at)->format('H:i') }}
                            </small>
                        </div>
                        <div class="col-6 col-md-3 text-end">
                            @if($history->catatan)
                                <span class="badge bg-label-secondary">
                                    <i class="ti ti-message-circle me-1"></i>
                                    Ada Catatan
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
