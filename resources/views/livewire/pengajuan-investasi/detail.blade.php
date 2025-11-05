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

                    <div class="stepper-item" data-step="1">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 1</div>
                            <div class="step-name">Pengajuan Investasi</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="2">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 2</div>
                            <div class="step-name">Validasi Bagi Hasil</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="3">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 3</div>
                            <div class="step-name">Validasi CEO SKI</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="4">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 4</div>
                            <div class="step-name">Upload Bukti Transfer</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="5">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 5</div>
                            <div class="step-name">Generate Kontrak</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="6">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 6</div>
                            <div class="step-name">Selesai</div>
                        </div>
                    </div>

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
                                                <button type="button" class="btn btn-primary d-none" id="btnSetujuiPengajuan">
                                                    <i class="fas fa-check me-2"></i>
                                                    Validasi Bagi Hasil
                                                </button>
                                                <button type="button" class="btn btn-primary d-none" id="btnValidasiCEO">
                                                    <i class="ti ti-check me-2"></i>
                                                    Validasi CEO SKI
                                                </button>
                                            </div>
                                        </div>

                                        <hr class="my-3 my-md-4">

                                        <!-- Data Investasi -->
                                        <h6 class="text-dark mb-3">Data Investasi</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nama
                                                        Investor</small>
                                                    <p class="fw-bold mb-0">{{ $investasi['nama_investor'] ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Jenis
                                                        Deposito</small>
                                                    <p class="fw-bold mb-0">{{ ucfirst($investasi['deposito'] ?? '-') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Tanggal
                                                        Investasi</small>
                                                    <p class="fw-bold mb-0">
                                                        {{ $investasi['tanggal_investasi'] ? \Carbon\Carbon::parse($investasi['tanggal_investasi'])->format('d F Y') : '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Lama
                                                        Investasi</small>
                                                    <p class="fw-bold mb-0">{{ $investasi['lama_investasi'] ?? '-' }} Bulan</p>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-3 my-md-4">

                                        <!-- Data Pembiayaan -->
                                        <h6 class="text-dark mb-3">Data Pembiayaan</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Jumlah
                                                        Investasi</small>
                                                    <p class="fw-bold mb-0">Rp {{ number_format($investasi['jumlah_investasi'] ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Persentase Bagi
                                                        Hasil</small>
                                                    <p class="fw-bold mb-0">{{ $investasi['bagi_hasil_pertahun'] ?? 0 }}%</p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nominal Bagi Hasil
                                                        Keseluruhan</small>
                                                    <p class="fw-bold mb-0">Rp {{ number_format($investasi['nominal_bagi_hasil_yang_didapatkan'] ?? 0, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Konten Default -->
                                </div>

                                <!-- Detail Kontrak Tab -->
                                <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                                    <!-- Konten Default (Before Step 4) -->
                                    <div id="kontrak-default">
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <i class="ti ti-file-text display-4 text-muted"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">Kontrak Belum Tersedia</h5>
                                            <p class="text-muted mb-0">
                                                Kontrak akan tersedia setelah proses validasi selesai.
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Konten Step 4: Generate Kontrak -->
                                    <div id="kontrak-step4" class="d-none">
                                        <h5 class="mb-4">Generate Kontrak Investasi</h5>
                                        <form id="formGenerateKontrak">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nomorKontrak" class="form-label">Nomor Kontrak <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nomorKontrak"
                                                        placeholder="Masukkan nomor kontrak" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="tanggalKontrak" class="form-label">Tanggal Kontrak <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control flatpickr-date"
                                                        id="tanggalKontrak" placeholder="DD/MM/YYYY" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="catatanKontrak" class="form-label">Catatan
                                                        Tambahan</label>
                                                    <textarea class="form-control" id="catatanKontrak" rows="3" placeholder="Masukkan catatan jika ada"></textarea>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-label-secondary"
                                                    id="btnBatalKontrak">
                                                    Batal
                                                </button>
                                                <button type="submit" class="btn btn-primary" id="btnSimpanKontrak">
                                                    <span class="spinner-border spinner-border-sm me-2 d-none"
                                                        id="btnSimpanKontrakSpinner"></span>
                                                    Generate Kontrak
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- End Konten Step 4 -->
                                </div>

                                <!-- Activity Tab -->
                                <div class="tab-pane fade" id="activity" role="tabpanel">
                                    <div class="mb-4">
                                        <h5 class="mb-0">Aktivitas Terakhir</h5>
                                    </div>

                                    <hr class="my-3">

                                    <!-- Empty state untuk step 1 -->
                                    <div id="activity-empty" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="ti ti-clipboard-list display-4 text-muted"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
                                        <p class="text-muted mb-0">
                                            Aktivitas akan muncul setelah proses validasi dimulai.
                                        </p>
                                    </div>

                                    <!-- Timeline Container - hanya muncul dari step 2 -->
                                    <div class="d-none" id="timeline-container">
                                        <!-- Step 2: Validasi Bagi Hasil -->
                                        <div class="activity-item activity-step-2 d-none mb-4">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span class="avatar-initial rounded-circle bg-label-warning step-2-icon">
                                                                    <i class="ti ti-report-search"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 step-2-title">Validasi Bagi Hasil</h6>
                                                            <p class="text-muted mb-0 small step-2-desc">
                                                                Pengajuan sedang dalam proses validasi. Harap menunggu
                                                                hingga proses selesai.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-2">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end"></div>
                                            </div>
                                        </div>

                                        <!-- Step 3: Validasi CEO SKI -->
                                        <div class="activity-item activity-step-3 d-none mt-3 mb-4">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span class="avatar-initial rounded-circle bg-label-info step-3-icon">
                                                                    <i class="ti ti-user-check"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 step-3-title">Validasi CEO SKI</h6>
                                                            <p class="text-muted mb-0 small step-3-desc">
                                                                Menunggu persetujuan dari CEO SKI.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-3">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end"></div>
                                            </div>
                                        </div>

                                        <!-- Step 4: Upload Bukti Transfer -->
                                        <div class="activity-item d-none mt-3 mb-4">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary">
                                                                    <i class="ti ti-file-upload"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Upload Bukti Transfer</h6>
                                                            <p class="text-muted mb-0 small">Bukti transfer investasi
                                                                telah terkirim.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-4">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary"
                                                        id="btnUploadBuktiTransfer" title="Upload">
                                                        <i class="ti ti-upload"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 5: Generate Kontrak -->
                                        <div class="activity-item d-none mt-3 mb-4">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-success">
                                                                    <i class="ti ti-file-text"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Kontrak Investasi</h6>
                                                            <p class="text-muted mb-0 small">Kontrak investasi telah
                                                                dibuat.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-5">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-info"
                                                        id="btnPreviewKontrak" title="Preview">
                                                        <i class="ti ti-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 6: Selesai -->
                                        <div class="activity-item d-none mt-3 mb-4">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-success">
                                                                    <i class="ti ti-check"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Proses Selesai</h6>
                                                            <p class="text-muted mb-0 small">Investasi Anda telah
                                                                diproses.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-6">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Persetujuan Investasi -->
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

    <!-- Modal Hasil Review (Penolakan) -->
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

    <!-- Modal Upload Bukti Transfer -->
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
                        <div class="mb-3">
                            <label for="keteranganBukti" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keteranganBukti" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
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

    <!-- Modal Validasi CEO SKI -->
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const investasiId = '{{ $investasi['id'] }}';
            const currentStatus = '{{ $investasi['status'] }}';
            let currentStep = {{ $investasi['current_step'] ?? 1 }};

         

            // Update UI berdasarkan step
            function updateUI() {
                
                // Update stepper
                document.querySelectorAll('.stepper-item').forEach((item, index) => {
                    const step = index + 1;
                    item.classList.remove('completed', 'active');
                    if (step < currentStep) item.classList.add('completed');
                    else if (step === currentStep) item.classList.add('active');
                });

                // Show/hide buttons based on status
                // Submit button: show if Draft OR (Ditolak AND Step 1)
                const showSubmitBtn = currentStatus === 'Draft' || (currentStatus === 'Ditolak' && currentStep === 1);
                $('#btnSubmitPengajuan').toggleClass('d-none', !showSubmitBtn);
                $('#btnSetujuiPengajuan').toggleClass('d-none', currentStep !== 2 || currentStatus === 'Draft' || currentStatus === 'Ditolak');
                $('#btnValidasiCEO').toggleClass('d-none', currentStep !== 3 || currentStatus === 'Draft' || currentStatus === 'Ditolak');
                
                // Show/hide alerts
                if (currentStatus === 'Draft') {
                    $('#alertDraft').show();
                    $('#alertDitolak').hide();
                    $('#alertPeninjauan').hide();
                } else if (currentStatus === 'Ditolak') {
                    $('#alertDraft').hide();
                    $('#alertDitolak').show();
                    $('#alertPeninjauan').hide();
                } else {
                    $('#alertDraft').hide();
                    $('#alertDitolak').hide();
                    $('#alertPeninjauan').toggle(currentStep < 6);
                }

                // Detail Kontrak content
                $('#kontrak-default').toggleClass('d-none', currentStep === 5);
                $('#kontrak-step4').toggleClass('d-none', currentStep !== 5);
                if (currentStep === 5) initFlatpickr();

                // Activity timeline
                const showTimeline = currentStep >= 2 && currentStatus !== 'Draft';
                $('#activity-empty').toggleClass('d-none', showTimeline);
                $('#timeline-container').toggleClass('d-none', !showTimeline);

                if (showTimeline) {
                    const date = new Date().toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                    
                    $('.activity-item').each(function(index) {
                        const step = index + 2;
                        $(this).toggleClass('d-none', step > currentStep);
                        $(this).find(`#date-step-${step}`).text(date);
                    });

                    // Update tampilan jika ditolak
                    if (currentStatus === 'Ditolak') {
                        if (currentStep === 1) {
                            // Ditolak di Step 2, kembali ke Step 1
                            $('.activity-step-2 .step-2-icon').removeClass('bg-label-warning').addClass('bg-label-danger');
                            $('.activity-step-2 .step-2-icon i').removeClass('ti-report-search').addClass('ti-x');
                            $('.activity-step-2 .step-2-title').text('Validasi Bagi Hasil - Ditolak');
                            $('.activity-step-2 .step-2-desc').text('Pengajuan ditolak. Silakan perbaiki dan submit ulang.');
                        } else if (currentStep === 6) {
                            // Ditolak di Step 3, langsung ke Step 6
                            $('.activity-step-3 .step-3-icon').removeClass('bg-label-info').addClass('bg-label-danger');
                            $('.activity-step-3 .step-3-icon i').removeClass('ti-user-check').addClass('ti-x');
                            $('.activity-step-3 .step-3-title').text('Validasi CEO SKI - Ditolak');
                            $('.activity-step-3 .step-3-desc').text('Pengajuan ditolak oleh CEO SKI. Proses investasi ditutup.');
                        }
                    }
                }
            }

            // Init flatpickr
            function initFlatpickr() {
                const el = document.getElementById('tanggalKontrak');
                if (el && !el._flatpickr) {
                    flatpickr(el, {
                        dateFormat: 'd/m/Y',
                        altInput: true,
                        altFormat: 'j F Y'
                    });
                }
            }

            // Stepper click - DISABLED (stepper hanya mengikuti step dari database)
            // $('.stepper-wrapper').on('click', '.stepper-item', function() {
            //     currentStep = parseInt($(this).data('step'));
            //     updateUI();
            //     if (currentStep === 5) {
            //         new bootstrap.Tab($('[data-bs-target="#detail-kontrak"]')[0]).show();
            //     }
            // });

            // Button Submit Pengajuan
            $('#btnSubmitPengajuan').click(function() {
                $.ajax({
                    url: `/pengajuan-investasi/${investasiId}/approval`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: 'Submit Dokumen'
                    },
                    beforeSend: function() {
                        $('#btnSubmitPengajuan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...');
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Pengajuan investasi berhasil di-submit.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                            $('#btnSubmitPengajuan').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Submit Pengajuan');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat submit pengajuan.', 'error');
                        $('#btnSubmitPengajuan').prop('disabled', false).html('<i class="fas fa-paper-plane me-2"></i>Submit Pengajuan');
                    }
                });
            });

            // Button Setujui Pengajuan (Step 2: Validasi Bagi Hasil)
            $('#btnSetujuiPengajuan').click(() => {
                new bootstrap.Modal($('#modalPersetujuanInvestasi')[0]).show();
            });

            $('#btnKonfirmasiSetuju').click(function() {
                const modal = bootstrap.Modal.getInstance($('#modalPersetujuanInvestasi')[0]);
                
                $.ajax({
                    url: `/pengajuan-investasi/${investasiId}/approval`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: 'Dokumen Tervalidasi',
                        validasi_bagi_hasil: 'disetujui'
                    },
                    beforeSend: function() {
                        $('#btnKonfirmasiSetuju').prop('disabled', true).text('Memproses...');
                    },
                    success: function(response) {
                        if (response.success) {
                            modal.hide();
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Pengajuan investasi berhasil disetujui.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                            $('#btnKonfirmasiSetuju').prop('disabled', false).text('Setuju');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menyetujui pengajuan.', 'error');
                        $('#btnKonfirmasiSetuju').prop('disabled', false).text('Setuju');
                    }
                });
            });

            // Button Tolak (Step 2: Validasi Bagi Hasil)
            $('#btnTolakInvestasi').click(() => {
                const modalPersetujuan = bootstrap.Modal.getInstance($('#modalPersetujuanInvestasi')[0]);
                modalPersetujuan.hide();
                setTimeout(() => {
                    new bootstrap.Modal($('#modalHasilReview')[0]).show();
                }, 300);
            });

            // Form Review (Penolakan)
            $('#formHasilReview').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                const alasanPenolakan = $('#hasilReview').val();

                $.ajax({
                    url: `/pengajuan-investasi/${investasiId}/approval`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: 'Ditolak',
                        validasi_bagi_hasil: 'ditolak',
                        catatan: alasanPenolakan
                    },
                    beforeSend: function() {
                        $('#formHasilReview button[type="submit"]').prop('disabled', true).text('Mengirim...');
                    },
                    success: function(response) {
                        if (response.success) {
                            bootstrap.Modal.getInstance($('#modalHasilReview')[0]).hide();
                            $('#formHasilReview').removeClass('was-validated')[0].reset();
                            
                            Swal.fire({
                                title: 'Pengajuan Ditolak',
                                text: 'Pengajuan investasi telah ditolak.',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                            $('#formHasilReview button[type="submit"]').prop('disabled', false).text('Kirim');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menolak pengajuan.', 'error');
                        $('#formHasilReview button[type="submit"]').prop('disabled', false).text('Kirim');
                    }
                });
            });

            // Button Validasi CEO (Step 3)
            $('#btnValidasiCEO').click(() => {
                new bootstrap.Modal($('#modalValidasiCEO')[0]).show();
            });

            $('#btnKonfirmasiCEO').click(function() {
                const modal = bootstrap.Modal.getInstance($('#modalValidasiCEO')[0]);
                
                $.ajax({
                    url: `/pengajuan-investasi/${investasiId}/approval`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: 'Disetujui oleh CEO SKI'
                    },
                    beforeSend: function() {
                        $('#btnKonfirmasiCEO').prop('disabled', true).html('<i class="ti ti-check me-1"></i>Memproses...');
                    },
                    success: function(response) {
                        if (response.success) {
                            modal.hide();
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Pengajuan investasi berhasil disetujui oleh CEO.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                            $('#btnKonfirmasiCEO').prop('disabled', false).html('<i class="ti ti-check me-1"></i>Setujui');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menyetujui pengajuan.', 'error');
                        $('#btnKonfirmasiCEO').prop('disabled', false).html('<i class="ti ti-check me-1"></i>Setujui');
                    }
                });
            });

            // Button Tolak CEO (Step 3 - akan langsung ke Step 6/Selesai)
            $('#btnTolakCEO').click(() => {
                const modalValidasiCEO = bootstrap.Modal.getInstance($('#modalValidasiCEO')[0]);
                modalValidasiCEO.hide();
                
                // Tunggu sebentar sebelum membuka modal hasil review
                setTimeout(() => {
                    new bootstrap.Modal($('#modalHasilReview')[0]).show();
                }, 300);
            });

            // Upload Bukti
            $('#btnUploadBuktiTransfer').click(() => {
                new bootstrap.Modal($('#modalUploadBuktiTransfer')[0]).show();
            });

            $('#formUploadBuktiTransfer').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                $('#btnUploadSpinner').removeClass('d-none');
                setTimeout(() => {
                    $('#btnUploadSpinner').addClass('d-none');
                    bootstrap.Modal.getInstance($('#modalUploadBuktiTransfer')[0]).hide();
                    $(this).removeClass('was-validated')[0].reset();
                    currentStep = 5;
                    updateUI();
                    new bootstrap.Tab($('[data-bs-target="#activity"]')[0]).show();
                }, 1500);
            });

            // Generate Kontrak
            $('#formGenerateKontrak').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                $('#btnSimpanKontrak').prop('disabled', true);
                $('#btnSimpanKontrakSpinner').removeClass('d-none');

                setTimeout(() => {
                    $('#btnSimpanKontrak').prop('disabled', false);
                    $('#btnSimpanKontrakSpinner').addClass('d-none');
                    $(this).removeClass('was-validated');
                    currentStep = 6;
                    updateUI();
                    new bootstrap.Tab($('[data-bs-target="#activity"]')[0]).show();
                }, 2000);
            });

            $('#btnBatalKontrak').click(() => {
                $('#formGenerateKontrak').removeClass('was-validated');
            });

            // Preview Kontrak
            $('#btnPreviewKontrak').click(() => {
                window.open('/form-kerja-investor/1/preview-kontrak', '_blank');
            });

            // Initialize
            updateUI();
        });
    </script>
@endsection
