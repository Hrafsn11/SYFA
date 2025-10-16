@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold mb-4">
                Detail Pengajuan Peminjaman
            </h4>


            <!-- Arrow Style Stepper -->
            <div class="d-flex overflow-auto mb-4 mb-md-5">
                <div class="stepper-arrow active" data-step="1">
                    <div class="arrow-number">1</div>
                    <div class="arrow-text">Pengajuan Pinjaman</div>
                </div>
                <div class="stepper-arrow" data-step="2">
                    <div class="arrow-number">2</div>
                    <div class="arrow-text">Validasi Dokumen</div>
                </div>
                <div class="stepper-arrow" data-step="3">
                    <div class="arrow-number">3</div>
                    <div class="arrow-text">Dokumen Tervalidasi</div>
                </div>
                <div class="stepper-arrow" data-step="4">
                    <div class="arrow-number">4</div>
                    <div class="arrow-text">Persetujuan Debitur</div>
                </div>
                <div class="stepper-arrow" data-step="5">
                    <div class="arrow-number">5</div>
                    <div class="arrow-text">Validasi Direktur SKI</div>
                </div>
                <div class="stepper-arrow" data-step="6">
                    <div class="arrow-number">6</div>
                    <div class="arrow-text">Validasi Direktur</div>
                </div>
                <div class="stepper-arrow" data-step="7">
                    <div class="arrow-number">7</div>
                    <div class="arrow-text">Generate Kontrak</div>
                </div>
                <div class="stepper-arrow" data-step="8">
                    <div class="arrow-number">8</div>
                    <div class="arrow-text">Upload Dokumen</div>
                </div>
                <div class="stepper-arrow" data-step="9">
                    <div class="arrow-number">9</div>
                    <div class="arrow-text">Selesai</div>
                </div>
            </div>

            <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
                <i class="fas fa-info-circle me-2"></i>
                Pengajuan Pinjaman Anda sedang kami tinjau. Harap tunggu
                beberapa saat hingga proses verifikasi selesai.
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-0">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#detail-pinjaman" role="tab" aria-selected="true">
                                            <i class="ti ti-wallet me-2"></i>
                                            <span class="d-none d-sm-inline">Detail Pinjaman</span>
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

                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Detail Pinjaman Tab -->
                                <div class="tab-pane fade show active" id="detail-pinjaman" role="tabpanel">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
                                        <h5 class="mb-3 mb-md-4">Detail Pinjaman</h5>
                                        <button type="button" class="btn btn-primary d-none" id="btnSetujuiPeminjaman">
                                            <i class="fas fa-check me-2"></i>
                                            Setujui Peminjaman
                                        </button>
                                    </div>

                                    <hr class="my-3 my-md-4">

                                    <!-- Data Perusahaan -->
                                    <h6 class="text-dark mb-3">Data Perusahaan</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Nama
                                                    Perusahaan</small>
                                                <p class="fw-bold mb-0">Techno Infinity</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Nama Bank</small>
                                                <p class="fw-bold mb-0">HC Service</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">No Rekening</small>
                                                <p class="fw-bold mb-0">130023032390239</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Lampiran SID</small>
                                                <p class="fw-bold mb-0">Pertanyaan Untuk BP Tapera</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Nilai KOL</small>
                                                <p class="fw-bold mb-0">3 KOL</p>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-3 my-md-4">

                                    <!-- Data Peminjaman -->
                                    <h6 class="text-dark mb-3">Data Peminjaman</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Nominal
                                                    Pinjaman</small>
                                                <p class="mb-0 text-success fw-semibold">Rp. 300.000.000</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Harapan Tanggal
                                                    Pencairan</small>
                                                <p class="fw-bold mb-0">24 Agustus 2024</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Persentase Bagi
                                                    Hasil</small>
                                                <p class="fw-bold mb-0">2%</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Jenis
                                                    Pembiayaan</small>
                                                <p class="fw-bold mb-0">Invoice Financing</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Rencana Tanggal
                                                    Bayar</small>
                                                <p class="fw-bold mb-0">24 September 2024</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                            <div class="mb-0">
                                                <small class="text-light fw-semibold d-block mb-1">Pembayaran
                                                    Total</small>
                                                <p class="mb-0 text-warning fw-semibold">Rp. 100.000.000</p>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-3 my-md-4">

                                    <!-- Data Invoicing -->
                                    <h6 class="text-muted mb-3">Data Invoicing</h6>

                                    <div class="table-responsive text-nowrap">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>NO</th>
                                                    <th>NO. INVOICE</th>
                                                    <th>NAMA CLIENT</th>
                                                    <th>NILAI INVOICE</th>
                                                    <th>NILAI PINJAMAN</th>
                                                    <th>NILAI BAGI HASIL</th>
                                                    <th>INVOICE DATE</th>
                                                    <th>DUE DATE</th>
                                                    <th>DOKUMEN INVOICE <span class="text-danger">*</span></th>
                                                    <th>DOKUMEN KONTRAK</th>
                                                    <th>DOKUMEN SO</th>
                                                    <th>DOKUMEN BAST</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <!-- Data will be populated by JavaScript -->
                                                <tr>
                                                    <td>1</td>
                                                    <td>INV-001</td>
                                                    <td>Client A</td>
                                                    <td>Rp. 150.000.000</td>
                                                    <td>Rp. 100.000.000</td>
                                                    <td>Rp. 2.000.000</td>
                                                    <td>01/07/2024</td>
                                                    <td>01/08/2024</td>
                                                    <td>
                                                        <a href="#" class="">Dokumen.pdf</a>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="">Dokumen.pdf</a>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="">Dokumen.pdf</a>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="">Dokumen.pdf</a>
                                                    </td>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Detail Kontrak Tab -->
                                <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                                    <div class="text-center py-5">
                                        <i class="far fa-file-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Detail Kontrak</h5>
                                        <p class="text-muted">Konten detail kontrak akan ditampilkan di sini.</p>
                                    </div>
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
                                        <p class="text-muted mb-0">Aktivitas akan muncul setelah proses validasi dimulai.
                                        </p>
                                    </div>

                                    <!-- Timeline Container - hanya muncul dari step 2 -->
                                    <div class="d-none" id="timeline-container">
                                        <!-- Step 2: Validasi Dokumen -->
                                        <div class="activity-item d-none mb-4" id="activity-step-2">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-warning"><i
                                                                        class="ti ti-report-search"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Validasi Dokumen</h6>
                                                            <p class="text-muted mb-0 small">Pengajuan sedang dalam proses
                                                                validasi.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-2">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end"></div>
                                            </div>
                                        </div>

                                        <!-- Step 3: Dokumen Tervalidasi -->
                                        <div class="activity-item d-none mt-3 mb-4" id="activity-step-3">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary"><i
                                                                        class="ti ti-file-text"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Draft: Dokumen Tervalidasi <i
                                                                    class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui
                                                            </h6>
                                                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-3">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary"
                                                        id="btnEditPencairan" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 4: Persetujuan Debitur -->
                                        <div class="activity-item d-none mt-3 mb-4" id="activity-step-4">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary"><i
                                                                        class="ti ti-file-text"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Draft: Persetujuan Debitur <i
                                                                    class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui</h6>
                                                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-4">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary" id="btnEditPencairan" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 5: Validasi Direktur SKI -->
                                        <div class="activity-item d-none mt-3 mb-4" id="activity-step-5">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary"><i
                                                                        class="ti ti-file-text"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Draft: Validasi Direktur SKI <i
                                                                    class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui
                                                            </h6>
                                                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-5">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary" id="btnEditPencairan" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 6: Validasi Direktur -->
                                        <div class="activity-item d-none mt-3 mb-4" id="activity-step-6">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary"><i
                                                                        class="ti ti-file-text"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Draft: Validasi Direktur <i
                                                                    class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui
                                                            </h6>
                                                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-6">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary" id="btnEditPencairan" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 7: Generate Kontrak -->
                                        <div class="activity-item d-none mt-3 mb-4" id="activity-step-7">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary"><i
                                                                        class="ti ti-file-text"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Draft: Generate Kontrak <i
                                                                    class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui</h6>
                                                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-7">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary" id="btnEditPencairan" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 8: Upload Dokumen -->
                                        <div class="activity-item d-none mt-3 mb-4" id="activity-step-8">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-warning"><i
                                                                        class="ti ti-upload"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Upload Dokumen <i class="ti ti-arrow-right mx-1"></i> Pengajuan Disetujui</h6>
                                                            <p class="text-muted mb-0 small">Bukti Pengiriman telah terkirim.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-8">-</small>
                                                </div>
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-success"
                                                        id="btnUploadDokumen" title="Upload Dokumen">
                                                        <i class="ti ti-upload"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 9: Selesai -->
                                        <div class="activity-item d-none mt-3 mb-4" id="activity-step-9">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-success"><i
                                                                        class="ti ti-circle-check"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Selesai</h6>
                                                            <p class="text-muted mb-0 small">Proses pengajuan pinjaman
                                                                telah selesai.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-9">-</small>
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

    <!-- Modal Persetujuan Pinjaman -->
    <div class="modal fade" id="modalPersetujuanPinjaman" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Persetujuan Pinjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="my-2">
                <div class="modal-body">
                    <h5 class="mb-2">Apakah anda yakin menyetujui Pengajuan Pinjaman?</h5>
                    <p class="mb-0">Silahkan klik button hijau jika anda akan menyetujui Pengajuan Pinjaman, dan isi
                        perjanjian Kontrak terlebih dahulu. Pastikan dokumen yang diperlukan sudah sesuai!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btnKonfirmasiSetuju">
                        Setuju
                    </button>
                    <button type="button" class="btn btn-danger" id="btnTolakPinjaman">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Pencairan Dana -->
    <div class="modal fade" id="modalPencairanDana" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Pencairan Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPencairanDana">
                    <div class="modal-body">
                        <!-- Card Data Nominal dan Tanggal -->
                        <div class="card border mb-3 shadow-none">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nominalPengajuan" class="form-label">Nominal Pengajuan</label>
                                        <input type="text" class="form-control input-rupiah" id="nominalPengajuan"
                                            value="300000000" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="nominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" class="form-control input-rupiah" id="nominalDisetujui"
                                            placeholder="Rp 0" required>
                                        <div class="invalid-feedback">
                                            Silakan isi nominal yang disetujui.
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="flatpickr-tanggal-pencairan" class="form-label">Tanggal
                                            Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control flatpickr-date-modal rounded-start"
                                                placeholder="DD/MM/YYYY" id="flatpickr-tanggal-pencairan" required>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">
                                            Silakan pilih tanggal pencairan.
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="flatpickr-tanggal-harapan" class="form-label">Tanggal Pencairan yang
                                            Diharapkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control rounded-start"
                                                placeholder="DD/MM/YYYY" id="flatpickr-tanggal-harapan"
                                                value="24/08/2024" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Catatan -->
                        <div class="card border shadow-none">
                            <div class="card-body">
                                <label for="catatanLainnya" class="form-label">Catatan Lainnya</label>
                                <textarea class="form-control" id="catatanLainnya" rows="4"
                                    placeholder="Berikan catatan tambahan jika diperlukan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Submit Pencairan Dana
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
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
                            <input type="text" class="form-control" id="hasilReview"
                                placeholder="Berikan catatan alasan penolakan" required>
                            <div class="invalid-feedback">
                                Silakan isi hasil review terlebih dahulu.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pencairan Dana -->
    <div class="modal fade" id="modalEditPencairan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Konfirmasi Pencairan Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPencairan">
                    <div class="modal-body">
                        <!-- Card Data Nominal dan Tanggal -->
                        <div class="card border mb-3 shadow-none">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="editNominalPengajuan" class="form-label">Nominal Pengajuan</label>
                                        <input type="text" class="form-control input-rupiah" id="editNominalPengajuan"
                                            value="300000000" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="editNominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" class="form-control input-rupiah" id="editNominalDisetujui"
                                            disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="editTanggalPencairan" class="form-label">Tanggal Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="editTanggalPencairan" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="editTanggalHarapan" class="form-label">Tanggal Pencairan yang
                                            Diharapkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="editTanggalHarapan" value="24/08/2024" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Catatan - Only editable field -->
                        <div class="card border shadow-none">
                            <div class="card-body">
                                <label for="editCatatanLainnya" class="form-label">Catatan Lainnya</label>
                                <textarea class="form-control" id="editCatatanLainnya" rows="4"
                                    placeholder="Berikan catatan tambahan jika diperlukan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Tolak
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Terima
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Upload Dokumen -->
    <div class="modal fade" id="modalUploadDokumen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUploadDokumen" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileUpload" class="form-label">Upload Dokumen Kontrak</label>
                            <input type="file" class="form-control" id="fileUpload" required>
                            <div class="invalid-feedback">
                                Silakan pilih file untuk diupload.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- STATE MANAGEMENT ---
            const state = {
                currentStep: 1,
                totalSteps: 9,
                pencairanData: {
                    nominalDisetujui: '',
                    tanggalPencairan: '',
                    catatan: ''
                },
            };

            // --- DOM ELEMENT CACHE ---
            const dom = {
                stepper: document.querySelector('.d-flex.overflow-auto'),
                alertPeninjauan: document.getElementById('alertPeninjauan'),
                activityTab: document.querySelector('[data-bs-target="#activity"]'),
                timeline: {
                    container: document.getElementById('timeline-container'),
                    empty: document.getElementById('activity-empty'),
                    items: document.querySelectorAll('.activity-item'),
                },
                buttons: {
                    setujuiPeminjaman: document.getElementById('btnSetujuiPeminjaman'),
                    konfirmasiSetuju: document.getElementById('btnKonfirmasiSetuju'),
                    tolakPinjaman: document.getElementById('btnTolakPinjaman'),
                    editPencairan: document.querySelectorAll('#btnEditPencairan'), // Menggunakan querySelectorAll
                    uploadDokumen: document.getElementById('btnUploadDokumen'),
                },
                forms: {
                    pencairan: document.getElementById('formPencairanDana'),
                    review: document.getElementById('formHasilReview'),
                    edit: document.getElementById('formEditPencairan'),
                    upload: document.getElementById('formUploadDokumen'),
                },
                modals: {
                    persetujuan: new bootstrap.Modal(document.getElementById('modalPersetujuanPinjaman')),
                    pencairan: new bootstrap.Modal(document.getElementById('modalPencairanDana')),
                    review: new bootstrap.Modal(document.getElementById('modalHasilReview')),
                    edit: new bootstrap.Modal(document.getElementById('modalEditPencairan')),
                    upload: new bootstrap.Modal(document.getElementById('modalUploadDokumen')),
                },
                inputs: {
                    nominalPengajuan: document.getElementById('nominalPengajuan'),
                    nominalDisetujui: document.getElementById('nominalDisetujui'),
                    tanggalPencairan: document.getElementById('flatpickr-tanggal-pencairan'),
                    tanggalHarapan: document.getElementById('flatpickr-tanggal-harapan'),
                    catatanLainnya: document.getElementById('catatanLainnya'),
                    editNominalPengajuan: document.getElementById('editNominalPengajuan'),
                    editNominalDisetujui: document.getElementById('editNominalDisetujui'),
                    editTanggalPencairan: document.getElementById('editTanggalPencairan'),
                    editTanggalHarapan: document.getElementById('editTanggalHarapan'),
                    editCatatanLainnya: document.getElementById('editCatatanLainnya'),
                }
            };

            // --- HELPERS ---
            const getFormattedDate = () => new Date().toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            const toggleDisplay = (element, show) => element?.classList.toggle('d-none', !show);
            const resetForm = (form) => {
                form?.reset();
                form?.classList.remove('was-validated');
            };
            const switchModal = (hideModal, showModal, onShow) => {
                hideModal.hide();
                const handleShow = () => {
                    onShow?.();
                    showModal.show();
                    initCleaveRupiah(); // Re-initialize cleave on new modal
                    document.getElementById(showModal._element.id).removeEventListener('shown.bs.modal',
                        handleShow);
                };
                document.getElementById(showModal._element.id).addEventListener('shown.bs.modal', handleShow);
            };

            // --- INITIALIZATION ---
            const initFlatpickr = () => {
                if (dom.inputs.tanggalPencairan?._flatpickr) dom.inputs.tanggalPencairan._flatpickr.destroy();
                flatpickr(dom.inputs.tanggalPencairan, {
                    monthSelectorType: 'static',
                    dateFormat: 'd/m/Y',
                    altInput: true,
                    altFormat: 'j F Y',
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            };

            // --- UI UPDATES ---
            const updateStepper = () => {
                document.querySelectorAll('.stepper-arrow').forEach((arrow, index) => {
                    const step = index + 1;
                    arrow.classList.toggle('completed', step < state.currentStep);
                    arrow.classList.toggle('active', step === state.currentStep);
                });
                toggleDisplay(dom.buttons.setujuiPeminjaman, state.currentStep === 2);
                dom.alertPeninjauan.style.display = state.currentStep >= 2 ? 'none' : 'block';
                updateActivityTimeline();
            };

            const updateActivityTimeline = () => {
                const showTimeline = state.currentStep >= 2;
                toggleDisplay(dom.timeline.empty, !showTimeline);
                toggleDisplay(dom.timeline.container, showTimeline);

                if (!showTimeline) return;

                const currentDate = getFormattedDate();
                dom.timeline.items.forEach((item, index) => {
                    const step = index + 2; // Timeline items start from step 2
                    const shouldShow = step <= state.currentStep;
                    toggleDisplay(item, shouldShow);
                    if (shouldShow) {
                        const dateEl = item.querySelector(`#date-step-${step}`);
                        if (dateEl) dateEl.textContent = currentDate;
                    }
                });
            };

            const goToStep = (step) => {
                if (step >= 1 && step <= state.totalSteps) {
                    state.currentStep = step;
                    updateStepper();
                }
            };

            const switchToActivityTab = () => {
                setTimeout(() => new bootstrap.Tab(dom.activityTab).show(), 300);
            };


            // --- EVENT HANDLERS ---
            const handleStepperClick = (e) => {
                const target = e.target.closest('.stepper-arrow');
                if (target) {
                    goToStep(parseInt(target.dataset.step));
                }
            };

            const handlePencairanSubmit = (e) => {
                e.preventDefault();
                if (!dom.forms.pencairan.checkValidity()) {
                    e.stopPropagation();
                    dom.forms.pencairan.classList.add('was-validated');
                    return;
                }
                Object.assign(state.pencairanData, {
                    nominalDisetujui: dom.inputs.nominalDisetujui.value,
                    tanggalPencairan: dom.inputs.tanggalPencairan.value,
                    catatan: dom.inputs.catatanLainnya.value.trim(),
                });
                dom.modals.pencairan.hide();
                resetForm(dom.forms.pencairan);
                goToStep(4);
                switchToActivityTab();
            };

            const handleReviewSubmit = (e) => {
                e.preventDefault();
                if (!dom.forms.review.checkValidity()) {
                    e.stopPropagation();
                    dom.forms.review.classList.add('was-validated');
                    return;
                }
                dom.modals.review.hide();
                resetForm(dom.forms.review);
                goToStep(1); // Back to the first step after rejection
            };

            const handleEditPencairanShow = () => {
                dom.inputs.editNominalPengajuan.value = '300000000';
                dom.inputs.editTanggalHarapan.value = '24/08/2024';
                dom.inputs.editNominalDisetujui.value = state.pencairanData.nominalDisetujui;
                dom.inputs.editTanggalPencairan.value = state.pencairanData.tanggalPencairan;
                dom.inputs.editCatatanLainnya.value = state.pencairanData.catatan;
                dom.modals.edit.show();
            };

            const handleEditPencairanSubmit = (e) => {
                e.preventDefault();
                state.pencairanData.catatan = dom.inputs.editCatatanLainnya.value.trim();
                dom.modals.edit.hide();
            };

            const handleUploadSubmit = (e) => {
                e.preventDefault();
                if (!dom.forms.upload.checkValidity()) {
                    e.stopPropagation();
                    dom.forms.upload.classList.add('was-validated');
                    return;
                }
                // Implement upload logic here
                console.log('File to upload:', document.getElementById('fileUpload').files[0]);
                console.log('Catatan:', document.getElementById('uploadCatatan').value);

                dom.modals.upload.hide();
                resetForm(dom.forms.upload);
                goToStep(9); // Move to the final step
                switchToActivityTab();
            };


            // --- EVENT LISTENERS ---
            dom.stepper.addEventListener('click', handleStepperClick);
            dom.buttons.setujuiPeminjaman?.addEventListener('click', () => dom.modals.persetujuan.show());
            dom.buttons.konfirmasiSetuju?.addEventListener('click', () => {
                switchModal(dom.modals.persetujuan, dom.modals.pencairan, () => {
                    resetForm(dom.forms.pencairan);
                    dom.inputs.nominalPengajuan.value = '300000000';
                    dom.inputs.tanggalHarapan.value = '24/08/2024';
                    initFlatpickr();
                });
            });
            dom.buttons.tolakPinjaman?.addEventListener('click', () => {
                switchModal(dom.modals.persetujuan, dom.modals.review, () => resetForm(dom.forms.review));
            });
            dom.buttons.uploadDokumen?.addEventListener('click', () => dom.modals.upload.show());

            // Gunakan forEach karena btnEditPencairan sekarang adalah NodeList
            dom.buttons.editPencairan.forEach(btn => {
                btn.addEventListener('click', handleEditPencairanShow);
            });

            dom.forms.pencairan?.addEventListener('submit', handlePencairanSubmit);
            dom.forms.review?.addEventListener('submit', handleReviewSubmit);
            dom.forms.edit?.addEventListener('submit', handleEditPencairanSubmit);
            dom.forms.upload?.addEventListener('submit', handleUploadSubmit);


            // --- INITIAL EXECUTION ---
            updateStepper();
            initCleaveRupiah(); // Initialize Cleave for initial inputs
        });
    </script>
@endsection
