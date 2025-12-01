@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold mb-4">
                Detail Pengajuan Investasi
            </h4>

            <!-- Stepper -->
            <div class="stepper-container mb-4">
                <div class="stepper-wrapper">
                    @foreach(['Pengajuan Investasi', 'Validasi Bagi Hasil', 'Validasi CEO SKI', 'Upload Bukti Transfer', 'Generate Kontrak', 'Selesai'] as $i => $name)
                    <div class="stepper-item" data-step="{{ $i + 1 }}">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP {{ $i + 1 }}</div>
                            <div class="step-name">{{ $name }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($investasi['status'] === 'Draft')
            <div class="alert alert-info mb-4" role="alert" id="alertDraft">
                <i class="fas fa-info-circle me-2"></i>
                Pengajuan investasi masih dalam status <strong>Draft</strong>. Silakan klik tombol <strong>"Submit Pengajuan"</strong> untuk melanjutkan proses verifikasi.
            </div>
            @elseif($investasi['status'] === 'Ditolak')
                @if($investasi['current_step'] == 1)
                <div class="alert alert-danger mb-4" role="alert" id="alertDitolak">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Pengajuan investasi Anda <strong>Ditolak pada Validasi Bagi Hasil</strong>. Anda dapat mengedit dan submit ulang pengajuan dengan memperbaiki data yang diperlukan.
                </div>
                @elseif($investasi['current_step'] == 6)
                <div class="alert alert-danger mb-4" role="alert" id="alertDitolak">
                    <i class="fas fa-times-circle me-2"></i>
                    Pengajuan investasi Anda <strong>Ditolak oleh CEO SKI</strong>. Proses investasi telah ditutup dan tidak dapat diajukan ulang.
                </div>
                @endif
            @else
            <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
                <i class="fas fa-info-circle me-2"></i>
                Pengajuan Investasi Anda sedang kami tinjau. Harap tunggu beberapa saat hingga proses verifikasi
                selesai.
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-0">
                            <!-- tabs menu -->
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#detail-pinjaman" role="tab" aria-selected="true">
                                            <i class="ti ti-wallet me-2"></i>
                                            <span class="d-none d-sm-inline">Detail Investasi</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#detail-kontrak" role="tab" aria-selected="false">
                                            <i class="ti ti-report-money me-2"></i>
                                            <span class="d-none d-sm-inline">Detail Kontrak</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#activity" role="tab" aria-selected="false">
                                            <i class="ti ti-activity me-2"></i>
                                            <span class="d-none d-sm-inline">Activity</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- isi table -->
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Detail Investasi Tab -->
                                <div class="tab-pane fade show active" id="detail-pinjaman" role="tabpanel">
                                    <!-- Konten Default -->
                                    <div id="content-default">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
                                            <h5 class="mb-3 mb-md-4">Detail Investasi</h5>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success d-none" id="btnSubmitPengajuan">
                                                    <i class="fas fa-paper-plane me-2"></i>
                                                    Submit Pengajuan
                                                </button>
                                                @can('investasi.validasi_bagi_hasil')
                                                <button type="button" class="btn btn-primary d-none" id="btnSetujuiPengajuan">
                                                    <i class="fas fa-check me-2"></i>
                                                    Validasi Bagi Hasil
                                                </button>
                                                @endcan
                                                @can('investasi.validasi_ceo_ski')
                                                <button type="button" class="btn btn-primary d-none" id="btnValidasiCEO">
                                                    <i class="ti ti-check me-2"></i>
                                                    Validasi CEO SKI
                                                </button>
                                                @endcan
                                                @can('investasi.generate_kontrak')
                                                <button type="button" class="btn btn-primary d-none" id="btnUploadBukti">
                                                    <i class="ti ti-upload me-2"></i>
                                                    Upload Bukti Transfer
                                                </button>
                                                @endcan
                                            </div>
                                        </div>

                                        <hr class="my-3 my-md-4">

                                        @php
                                            $dataFields = [
                                                'Data Investasi' => [
                                                    'Nama Investor' => $investasi['nama_investor'] ?? '-',
                                                    'Jenis Deposito' => ucfirst($investasi['deposito'] ?? '-'),
                                                    'Tanggal Investasi' => $investasi['tanggal_investasi'] ? \Carbon\Carbon::parse($investasi['tanggal_investasi'])->format('d F Y') : '-',
                                                    'Lama Investasi' => ($investasi['lama_investasi'] ?? '-') . ' Bulan'
                                                ],
                                                'Data Pembiayaan' => [
                                                    'Jumlah Investasi' => 'Rp ' . number_format($investasi['jumlah_investasi'] ?? 0, 0, ',', '.'),
                                                    'Persentase Bagi Hasil' => ($investasi['bagi_hasil_pertahun'] ?? 0) . '%',
                                                    'Nominal Bagi Hasil Keseluruhan' => 'Rp ' . number_format($investasi['nominal_bagi_hasil_yang_didapatkan'] ?? 0, 0, ',', '.')
                                                ]
                                            ];
                                        @endphp
                                        @foreach($dataFields as $section => $fields)
                                        <h6 class="text-dark mb-3">{{ $section }}</h6>
                                        <div class="row g-3 mb-4">
                                            @foreach($fields as $label => $value)
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">{{ $label }}</small>
                                                    <p class="fw-bold mb-0">{{ $value }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @if(!$loop->last)<hr class="my-3 my-md-4">@endif
                                        @endforeach
                                    </div>
                                    <!-- End Konten Default -->
                                </div>

                                <!-- Detail Kontrak Tab -->
                                <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                                    <!-- Konten Default (Before Step 5) -->
                                    <div id="kontrak-default">
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="ti ti-file-text display-4 text-muted"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">Kontrak Belum Tersedia</h5>
                                            <p class="text-muted mb-0">
                                                Kontrak akan tersedia setelah dana dicairkan.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Konten Step 5: Generate Kontrak (After Dana Sudah Dicairkan) -->
                                    <div id="kontrak-step5" class="d-none">
                                        @can('investasi.generate_kontrak')
                                        <h5 class="mb-4">Generate Kontrak Investasi Deposito</h5>
                                        
                                        <!-- Data Kontrak (10 Fields) -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="mb-0">Data Kontrak</h6>
                                            </div>
                                            <div class="card-body">
                                                <form id="formGenerateKontrak">
                                                    <div class="row g-3">
                                                        @php
                                                            $kontrakFields = [
                                                                'Nama Investor' => $investasi['nama_investor'] ?? '-',
                                                                'Nama Perusahaan' => $investasi['nama_investor'] ?? '-',
                                                                'Jenis Deposito' => ucfirst($investasi['deposito'] ?? '-'),
                                                                'Jumlah Investasi' => 'Rp ' . number_format($investasi['jumlah_investasi'] ?? 0, 0, ',', '.'),
                                                                'Persentase Bagi Hasil' => ($investasi['deposito'] === 'Reguler' ? '10%' : ($investasi['bagi_hasil_pertahun'] ?? 0) . '%'),
                                                                'Lama Investasi' => ($investasi['lama_investasi'] ?? '-') . ' Bulan',
                                                                'Tanggal Investasi' => $investasi['tanggal_investasi'] ? \Carbon\Carbon::parse($investasi['tanggal_investasi'])->format('d F Y') : '-',
                                                                'Tanggal Jatuh Tempo' => $investasi['tanggal_investasi'] ? 
                                                                    ($investasi['deposito'] === 'Reguler' ? 
                                                                        \Carbon\Carbon::createFromDate(\Carbon\Carbon::parse($investasi['tanggal_investasi'])->year, 12, 31)->format('d F Y') :
                                                                        \Carbon\Carbon::parse($investasi['tanggal_investasi'])->addMonths($investasi['lama_investasi'])->format('d F Y')) : '-'
                                                            ];
                                                        @endphp
                                                        @foreach($kontrakFields as $label => $value)
                                                        <div class="col-md-6">
                                                            <label class="form-label text-muted small">{{ $label }}</label>
                                                            @if($label === 'Alamat')
                                                            <textarea class="form-control" rows="2" readonly>{{ $value }}</textarea>
                                                            @else
                                                            <input type="text" class="form-control" value="{{ $value }}" readonly>
                                                            @endif
                                                        </div>
                                                        @endforeach
                                                        <div class="col-md-6">
                                                            <label class="form-label text-muted small">Alamat</label>
                                                            <textarea class="form-control" rows="2" readonly>{{ $investasi['alamat'] ?? '-' }}</textarea>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="nomorKontrak" class="form-label">Nomor Kontrak <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="nomorKontrak" name="nomor_kontrak" placeholder="Contoh: 001/SKI/INV/2025" value="{{ old('nomor_kontrak', $investasi['nomor_kontrak'] ?? '') }}" required>
                                                        </div>
                                                    </div>

                                                    <hr class="my-4">

                                                    <div class="d-flex justify-content-end gap-2">
                                                        <button type="button" class="btn btn-outline-primary" id="btnPreviewKontrak">
                                                            <i class="ti ti-eye me-2"></i>
                                                            Preview Kontrak
                                                        </button>
                                                        <button type="submit" class="btn btn-success" id="btnGenerateKontrak">
                                                            <span class="spinner-border spinner-border-sm me-2 d-none" id="btnGenerateKontrakSpinner"></span>
                                                            <i class="ti ti-file-check me-2"></i>
                                                            Generate Kontrak
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @else
                                        <div class="alert alert-info">
                                            Anda tidak memiliki izin untuk melakukan generate kontrak.
                                        </div>
                                        @endcan
                                    </div>
                                    <!-- End Konten Step 5 -->
                                </div>

                                <!-- Activity Tab -->
                                <div class="tab-pane fade" id="activity" role="tabpanel">
                                    <div class="mb-4">
                                        <h5 class="mb-0">Aktivitas Terakhir</h5>
                                    </div>

                                    <hr class="my-3">

                                    @if($histories->isEmpty())
                                    <!-- Empty state jika belum ada history -->
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
                                    <!-- Timeline dari database -->
                                    <div class="timeline">
                                        @foreach($histories->reverse() as $history)
                                        <div class="activity-item mb-4">
                                            <div class="row align-items-start">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                @php
                                                                    $statusIcons = [
                                                                        'Draft' => ['color' => 'secondary', 'icon' => 'ti-pencil'],
                                                                        'Submit Dokumen' => ['color' => 'info', 'icon' => 'ti-send'],
                                                                        'Dokumen Tervalidasi' => ['color' => 'success', 'icon' => 'ti-check'],
                                                                        'Ditolak' => ['color' => 'danger', 'icon' => 'ti-x'],
                                                                        'Disetujui oleh CEO SKI' => ['color' => 'success', 'icon' => 'ti-user-check'],
                                                                        'Dana Sudah Dicairkan' => ['color' => 'primary', 'icon' => 'ti-file-upload'],
                                                                        'Generate Kontrak' => ['color' => 'info', 'icon' => 'ti-file-text'],
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
                                                                        'Draft' => 'Draft Pengajuan',
                                                                        'Submit Dokumen' => 'Dokumen Disubmit',
                                                                        'Dokumen Tervalidasi' => 'Validasi Bagi Hasil - Disetujui',
                                                                        'Ditolak' => match($history->current_step) {
                                                                            2 => 'Validasi Bagi Hasil - Ditolak',
                                                                            3 => 'Validasi CEO SKI - Ditolak',
                                                                            default => 'Pengajuan Ditolak'
                                                                        },
                                                                        'Disetujui oleh CEO SKI' => 'Validasi CEO SKI - Disetujui',
                                                                        'Dana Sudah Dicairkan' => 'Upload Bukti Transfer',
                                                                        'Generate Kontrak' => 'Generate Kontrak',
                                                                        'Selesai' => 'Proses Selesai',
                                                                    ];
                                                                @endphp
                                                                {{ $statusTitles[$history->status] ?? $history->status }}
                                                            </h6>
                                                            <p class="text-muted mb-0 small">
                                                                @php
                                                                    $statusDescriptions = [
                                                                        'Draft' => 'Pengajuan investasi dibuat sebagai draft.',
                                                                        'Submit Dokumen' => 'Dokumen pengajuan berhasil disubmit untuk validasi.',
                                                                        'Dokumen Tervalidasi' => 'Bagi hasil telah divalidasi dan disetujui.',
                                                                        'Ditolak' => $history->catatan_validasi_dokumen_ditolak 
                                                                            ? '<span class="text-danger fw-semibold">Alasan: </span>' . $history->catatan_validasi_dokumen_ditolak
                                                                            : 'Pengajuan ditolak.',
                                                                        'Disetujui oleh CEO SKI' => 'Pengajuan telah disetujui oleh CEO SKI.',
                                                                        'Dana Sudah Dicairkan' => 'Bukti transfer investasi telah diupload.',
                                                                        'Generate Kontrak' => 'Kontrak investasi telah digenerate.',
                                                                        'Selesai' => 'Proses investasi telah selesai.',
                                                                    ];
                                                                @endphp
                                                                {!! $statusDescriptions[$history->status] ?? $history->status !!}
                                                            </p>
                                                            @if($history->submittedBy)
                                                                <small class="text-muted">
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
                                                            @elseif($history->date)
                                                                {{ \Carbon\Carbon::parse($history->date)->format('d M Y, H:i') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </small>
                                                        @if($history->status == 'Dana Sudah Dicairkan' && $investasi['upload_bukti_transfer'])
                                                            <button type="button" class="btn btn-sm btn-primary" onclick="previewBuktiTransfer('{{ asset('storage/' . $investasi['upload_bukti_transfer']) }}')">
                                                                <i class="ti ti-eye me-1"></i>
                                                                Lihat Bukti
                                                            </button>
                                                        @endif
                                                        @if($history->status == 'Generate Kontrak')
                                                            <button type="button" class="btn btn-sm btn-success" onclick="previewKontrakFromHistory()">
                                                                <i class="ti ti-file-text me-1"></i>
                                                                Preview Kontrak
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Persetujuan Investasi -->
    @can('investasi.validasi_bagi_hasil')
    <div class="modal fade" id="modalPersetujuanInvestasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validasi Bagi Hasil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="my-2">
                <div class="modal-body">
                    <h5 class="mb-2">Apakah anda yakin menyetujui Bagi Hasil Investasi ini?</h5>
                    <p class="mb-0">Silahkan klik button hijau jika anda akan menyetujui, atau button merah untuk menolak.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btnKonfirmasiSetuju">
                        Setuju
                    </button>
                    <button type="button" class="btn btn-danger" id="btnTolakInvestasi">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Modal Hasil Review (Penolakan) -->
    @canany(['investasi.validasi_bagi_hasil','investasi.validasi_ceo_ski'])
    <div class="modal fade" id="modalHasilReview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hasil Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formHasilReview">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="hasilReview" class="form-label">Alasan Penolakan <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="hasilReview" rows="3" placeholder="Berikan catatan alasan penolakan"
                                required></textarea>
                            <div class="invalid-feedback">
                                Berikan catatan alasan penolakan
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcanany

    <!-- Modal Upload Bukti Transfer -->
    @can('investasi.generate_kontrak')
    <div class="modal fade" id="modalUploadBuktiTransfer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUploadBuktiTransfer">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileBuktiTransfer" class="form-label">File Bukti Transfer <span
                                    class="text-danger">*</span></label>
                            <input class="form-control" type="file" id="fileBuktiTransfer"
                                accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">Format: PDF, JPG, PNG (Max: 2MB)</div>
                            <div class="invalid-feedback">
                                Pilih file bukti transfer
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="btnUploadSpinner"></span>
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    <!-- Modal Validasi CEO SKI -->
    @can('investasi.validasi_ceo_ski')
    <div class="modal fade" id="modalValidasiCEO" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validasi CEO SKI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="my-2">
                <div class="modal-body">
                    <h5 class="mb-2">Apakah Anda yakin menyetujui pengajuan investasi ini?</h5>
                    <p class="mb-0">Dengan menyetujui, pengajuan akan dilanjutkan ke proses upload bukti transfer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btnKonfirmasiCEO">
                        <i class="ti ti-check me-1"></i>
                        Setujui
                    </button>
                    <button type="button" class="btn btn-danger" id="btnTolakCEO">
                        <i class="ti ti-x me-1"></i>
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Modal Preview Bukti Transfer -->
    <div class="modal fade" id="modalPreviewBukti" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" src="" alt="Bukti Transfer" class="img-fluid d-none" style="max-height: 70vh;">
                    <iframe id="previewPdf" src="" class="d-none" style="width: 100%; height: 70vh;" frameborder="0"></iframe>
                </div>
                <div class="modal-footer">
                    <a id="downloadBukti" href="" download class="btn btn-primary">
                        <i class="ti ti-download me-1"></i>
                        Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview Bukti Transfer
        function previewBuktiTransfer(fileUrl) {
            const ext = fileUrl.split('.').pop().toLowerCase();
            const $img = $('#previewImage'), $pdf = $('#previewPdf');
            
            $img.add($pdf).addClass('d-none');
            $('#downloadBukti').attr('href', fileUrl);
            
            (['jpg', 'jpeg', 'png', 'gif'].includes(ext) ? $img : $pdf)
                .attr('src', fileUrl).removeClass('d-none');
            
            new bootstrap.Modal($('#modalPreviewBukti')[0]).show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ID = '{{ $investasi['id'] }}';
            const STATUS = '{{ $investasi['status'] }}';
            const STEP = {{ $investasi['current_step'] ?? 1 }};
            const CSRF = '{{ csrf_token() }}';
            // Use nomor kontrak from database if available, otherwise from localStorage
            const nomorKontrakFromDB = '{{ $investasi['nomor_kontrak'] ?? '' }}';
            let savedNomorKontrak = nomorKontrakFromDB || localStorage.getItem(`kontrak_${ID}`);
            
            // Update localStorage if nomor kontrak from DB exists
            if (nomorKontrakFromDB) {
                localStorage.setItem(`kontrak_${ID}`, nomorKontrakFromDB);
            }

            // Update UI berdasarkan step
            function updateUI() {
                // Update stepper
                $('.stepper-item').each((i, el) => {
                    const step = i + 1;
                    $(el).toggleClass('completed', step < STEP).toggleClass('active', step === STEP);
                });

                // Toggle buttons
                const isDraft = STATUS === 'Draft';
                const isDitolak = STATUS === 'Ditolak';
                const canSubmit = isDraft || (isDitolak && STEP === 1);
                
                $('#btnSubmitPengajuan').toggleClass('d-none', !canSubmit);
                $('#btnSetujuiPengajuan').toggleClass('d-none', STEP !== 2 || isDraft || isDitolak);
                $('#btnValidasiCEO').toggleClass('d-none', STEP !== 3 || isDraft || isDitolak);
                $('#btnUploadBukti').toggleClass('d-none', STEP !== 4 || isDraft || isDitolak);
                
                // Toggle alerts
                const alerts = { Draft: '#alertDraft', Ditolak: '#alertDitolak' };
                Object.entries(alerts).forEach(([status, id]) => $(id).toggle(STATUS === status));
                $('#alertPeninjauan').toggle(!isDraft && !isDitolak && STEP < 6);

                // Kontrak tab
                $('#kontrak-default').toggleClass('d-none', STEP === 5);
                $('#kontrak-step5').toggleClass('d-none', STEP !== 5);
            }


            // Generic AJAX handler
            const ajaxPost = (url, data, onSuccess, btnSelector, loadingText) => {
                const $btn = $(btnSelector);
                const originalHtml = $btn.html();
                
                $.ajax({
                    url, method: 'POST',
                    data: { _token: CSRF, ...data },
                    beforeSend: () => $btn.prop('disabled', true).html(loadingText),
                    success: (res) => res.error ? 
                        (Swal.fire('Error!', res.message, 'error'), $btn.prop('disabled', false).html(originalHtml)) :
                        onSuccess(res),
                    
                });
            };

            const showSuccessReload = (msg) => Swal.fire('Berhasil!', msg, 'success').then(() => location.reload());
            const showModal = (id) => new bootstrap.Modal($(`#${id}`)[0]).show();
            const hideModal = (id) => bootstrap.Modal.getInstance($(`#${id}`)[0])?.hide();

            // Button handlers
            $('#btnSubmitPengajuan').click(() => ajaxPost(
                `/pengajuan-investasi/${ID}/approval`,
                { status: 'Submit Dokumen' },
                () => showSuccessReload('Pengajuan berhasil di-submit'),
                '#btnSubmitPengajuan',
                '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...'
            ));

            $('#btnSetujuiPengajuan').click(() => showModal('modalPersetujuanInvestasi'));
            
            $('#btnKonfirmasiSetuju').click(() => ajaxPost(
                `/pengajuan-investasi/${ID}/approval`,
                { status: 'Dokumen Tervalidasi', validasi_bagi_hasil: 'disetujui' },
                () => (hideModal('modalPersetujuanInvestasi'), showSuccessReload('Pengajuan berhasil disetujui')),
                '#btnKonfirmasiSetuju',
                'Memproses...'
            ));

            $('#btnTolakInvestasi').click(() => (hideModal('modalPersetujuanInvestasi'), setTimeout(() => showModal('modalHasilReview'), 300)));

            $('#formHasilReview').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) return (e.stopPropagation(), $(this).addClass('was-validated'));
                
                ajaxPost(
                    `/pengajuan-investasi/${ID}/approval`,
                    { status: 'Ditolak', validasi_bagi_hasil: 'ditolak', catatan: $('#hasilReview').val() },
                    () => (hideModal('modalHasilReview'), $('#formHasilReview').removeClass('was-validated')[0].reset(), 
                           Swal.fire('Pengajuan Ditolak', 'Pengajuan telah ditolak', 'info').then(() => location.reload())),
                    '#formHasilReview button[type="submit"]',
                    'Mengirim...'
                );
            });

            $('#btnValidasiCEO').click(() => showModal('modalValidasiCEO'));
            
            $('#btnKonfirmasiCEO').click(() => ajaxPost(
                `/pengajuan-investasi/${ID}/approval`,
                { status: 'Disetujui oleh CEO SKI' },
                () => (hideModal('modalValidasiCEO'), showSuccessReload('Pengajuan disetujui CEO')),
                '#btnKonfirmasiCEO',
                '<i class="ti ti-check me-1"></i>Memproses...'
            ));

            $('#btnTolakCEO').click(() => (hideModal('modalValidasiCEO'), setTimeout(() => showModal('modalHasilReview'), 300)));

            $('#btnUploadBukti').click(() => showModal('modalUploadBuktiTransfer'));

            $('#formUploadBuktiTransfer').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) return (e.stopPropagation(), $(this).addClass('was-validated'));
                
                const fd = new FormData();
                fd.append('_token', CSRF);
                fd.append('status', 'Dana Sudah Dicairkan');
                fd.append('file', $('#fileBuktiTransfer')[0].files[0]);

                const $btn = $('#formUploadBuktiTransfer button[type="submit"]');
                const $spinner = $('#btnUploadSpinner');

                $.ajax({
                    url: `/pengajuan-investasi/${ID}/upload-bukti`,
                    method: 'POST', data: fd, processData: false, contentType: false,
                    beforeSend: () => ($btn.prop('disabled', true), $spinner.removeClass('d-none')),
                    success: (res) => res.error ? 
                        (Swal.fire('Error!', res.message, 'error'), $btn.prop('disabled', false), $spinner.addClass('d-none')) :
                        (hideModal('modalUploadBuktiTransfer'), $('#formUploadBuktiTransfer').removeClass('was-validated')[0].reset(), showSuccessReload('Bukti transfer berhasil diupload')),
                    error: () => (Swal.fire('Error!', 'Terjadi kesalahan', 'error'), $btn.prop('disabled', false), $spinner.addClass('d-none'))
                });
            });

            const openPreview = (nomor = savedNomorKontrak) => 
                window.open(`/pengajuan-investasi/${ID}/preview-kontrak${nomor ? '?nomor_kontrak=' + encodeURIComponent(nomor) : ''}`, '_blank');

            $('#btnPreviewKontrak').click(() => {
                const nomor = $('#nomorKontrak').val();
                nomor ? openPreview(nomor) : (Swal.fire('Perhatian!', 'Mohon isi nomor kontrak', 'warning'), $('#nomorKontrak').focus());
            });

            window.previewKontrakFromHistory = () => openPreview();

            $('#formGenerateKontrak').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) return (e.stopPropagation(), $(this).addClass('was-validated'));
                
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Generate kontrak ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Generate'
                }).then(result => {
                    if (result.isConfirmed) {
                        const nomor = $('#nomorKontrak').val();
                        savedNomorKontrak = nomor;
                        localStorage.setItem(`kontrak_${ID}`, nomor);
                        ajaxPost(
                            `/pengajuan-investasi/${ID}/generate-kontrak`,
                            { nomor_kontrak: nomor, status: 'Selesai' },
                            () => showSuccessReload('Kontrak berhasil digenerate'),
                            '#btnGenerateKontrak',
                            '<span class="spinner-border spinner-border-sm me-2"></span>Generate...'
                        );
                    }
                });
            });

            updateUI();
        });
    </script>
@endsection
