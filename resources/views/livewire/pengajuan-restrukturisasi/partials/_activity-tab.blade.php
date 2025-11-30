<div class="tab-pane fade" id="activity-restrukturisasi" role="tabpanel">
    <div class="mb-4">
        <h5 class="mb-0">Riwayat Aktivitas</h5>
    </div>

    <hr class="my-3">

    @php
        $hasHistory = isset($histories) && $histories && $histories->isNotEmpty();
    @endphp

    {{-- Debug Info (Temporary) --}}
    @if(config('app.debug'))
        <div class="alert alert-info mb-3">
            <small>
                <strong>Debug Info:</strong><br>
                Histories isset: {{ isset($histories) ? 'Yes' : 'No' }}<br>
                Histories count: {{ isset($histories) ? $histories->count() : 0 }}<br>
                Has History: {{ $hasHistory ? 'Yes' : 'No' }}
            </small>
        </div>
    @endif

    @if(!$hasHistory)
        <div id="activity-empty" class="text-center py-5">
            <div class="mb-3">
                <i class="ti ti-clipboard-list display-4 text-muted"></i>
            </div>
            <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
            <p class="text-muted mb-0">Aktivitas akan muncul setelah pengajuan disubmit.</p>
        </div>
    @else
        {{-- Timeline Container --}}
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
                                                'Disetujui CEO SKI' => ['icon' => 'ti-crown', 'color' => 'success'],
                                                'Ditolak CEO SKI' => ['icon' => 'ti-crown-off', 'color' => 'danger'],
                                                'Disetujui Direktur SKI' => ['icon' => 'ti-building-bank', 'color' => 'success'],
                                                'Ditolak Direktur SKI' => ['icon' => 'ti-building-bank', 'color' => 'danger'],
                                                'Restrukturisasi Selesai' => ['icon' => 'ti-circle-check-filled', 'color' => 'success'],
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
                                        
                                        // Get user name
                                        if($history->approve_by) {
                                            $user = \App\Models\User::find($history->approve_by);
                                            $userName = $user ? $user->name : 'User';
                                        } elseif($history->reject_by) {
                                            $user = \App\Models\User::find($history->reject_by);
                                            $userName = $user ? $user->name : 'User';
                                        } elseif($history->submit_by) {
                                            $user = \App\Models\User::find($history->submit_by);
                                            $userName = $user ? $user->name : 'User';
                                        }
                                        
                                        // Determine display text and description
                                        if($history->validasi_dokumen === 'ditolak' || $history->status === 'Ditolak' || $history->status === 'Perbaikan Dokumen' || $history->status === 'Perlu Evaluasi Ulang') {
                                            $stepNames = [1 => 'Pengajuan', 2 => 'Evaluasi', 3 => 'CEO SKI', 4 => 'Direktur'];
                                            $stepName = $stepNames[$history->current_step] ?? 'Step ' . $history->current_step;
                                            $displayText = '<span class="text-danger">Ditolak pada ' . $stepName . '</span>';
                                            $description = $userName ? 'Ditolak oleh ' . $userName : 'Pengajuan ditolak';
                                            if($history->catatan) {
                                                $description .= ' - ' . $history->catatan;
                                            }
                                        } elseif($history->validasi_dokumen === 'disetujui' || in_array($history->status, ['Dalam Proses', 'Selesai'])) {
                                            $stepNames = [1 => 'Pengajuan', 2 => 'Evaluasi', 3 => 'CEO SKI', 4 => 'Direktur', 5 => 'Selesai'];
                                            $stepName = $stepNames[$history->current_step] ?? 'Step ' . $history->current_step;
                                            
                                            if($history->current_step == 5) {
                                                $displayText = '<span class="text-success">Restrukturisasi Selesai</span>';
                                                $description = 'Proses restrukturisasi telah diselesaikan';
                                            } else {
                                                $displayText = '<span class="text-success">Disetujui - ' . $stepName . '</span>';
                                                $description = $userName ? 'Disetujui oleh ' . $userName : 'Pengajuan disetujui';
                                            }
                                        } else {
                                            $displayText = $history->status;
                                            $description = 'Status: ' . $history->status;
                                        }
                                    @endphp
                                    <h6 class="mb-1">{!! $displayText !!}</h6>
                                    <p class="text-muted mb-0 small">{{ $description }}</p>
                                    @if($history->catatan && ($history->validasi_dokumen === 'disetujui'))
                                        <p class="text-muted mb-0 small"><em>Catatan: {{ $history->catatan }}</em></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 text-center">
                            <small class="text-muted d-block">
                                <i class="ti ti-calendar me-1"></i>
                                {{ $history->date ? \Carbon\Carbon::parse($history->date)->format('d M Y') : '-' }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="ti ti-clock me-1"></i>
                                {{ $history->time ?? '-' }}
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
