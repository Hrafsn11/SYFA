@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold mb-0">
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
                    <div class="arrow-text">Generate Kontrak</div>
                </div>
                <div class="stepper-arrow" data-step="7">
                    <div class="arrow-number">7</div>
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
                                            <i class="fas fa-wallet me-2"></i>
                                            <span class="d-none d-sm-inline">Detail Pinjaman</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#detail-kontrak" role="tab" aria-selected="false">
                                            <i class="far fa-file me-2"></i>
                                            <span class="d-none d-sm-inline">Detail Kontrak</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#activity" role="tab" aria-selected="false">
                                            <i class="fas fa-chart-line me-2"></i>
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

                                    <!-- Empty state untuk step 1 & 2 -->
                                    <div id="activity-empty" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="ti ti-clipboard-list display-4 text-muted"></i>
                                        </div>
                                        <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
                                        <p class="text-muted mb-0">Aktivitas akan muncul setelah proses validasi dimulai.
                                        </p>
                                    </div>

                                    <!-- Timeline Container - hanya muncul dari step 3 -->
                                    <div class="d-none" id="timeline-container">
                                        <!-- Step 3: Validasi Dokumen -->
                                        <div class="activity-item" id="activity-step-3">
                                            <div class="row align-items-center mb-3">
                                                <!-- Keterangan + Icon (Kiri) -->
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-warning">
                                                                    <i class="ti ti-file-text"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">Validasi Dokumen</h6>
                                                            <p class="text-muted mb-0 small">Pengajuan sedang dalam proses
                                                                validasi. Harap menunggu hingga proses selesai.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tanggal (Tengah) -->
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-3">-</small>
                                                </div>

                                                <!-- Button (Kanan) -->
                                                <div class="col-6 col-md-3 text-end">
                                                    <!-- No action button for step 3 -->
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Step 4: Draft Pengajuan Disetujui -->
                                        <div class="activity-item d-none mt-3" id="activity-step-4">
                                            <div class="row align-items-center mb-3">
                                                <!-- Keterangan + Icon (Kiri) -->
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="avatar avatar-sm">
                                                                <span
                                                                    class="avatar-initial rounded-circle bg-label-primary">
                                                                    <i class="ti ti-file-check"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">
                                                                Draft: Pengajuan Pinjaman
                                                                <i class="ti ti-arrow-right mx-1"></i>
                                                                Pengajuan Disetujui
                                                            </h6>
                                                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tanggal (Tengah) -->
                                                <div class="col-6 col-md-3 text-center">
                                                    <small class="text-muted" id="date-step-4">-</small>
                                                </div>

                                                <!-- Button Edit (Kanan) -->
                                                <div class="col-6 col-md-3 text-end">
                                                    <button type="button" class="btn btn-icon btn-sm btn-label-primary"
                                                        id="btnEditPencairan" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
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
                                        <input type="text" class="form-control input-rupiah" id="editNominalDisetujui" disabled>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // State management
            let currentStep = 1;
            const totalSteps = 7;
            let pencairanData = {
                nominalDisetujui: '',
                tanggalPencairan: '',
                catatan: ''
            };

            // DOM Elements - Cache untuk performa
            const elements = {
                btnSetujui: document.getElementById('btnSetujuiPeminjaman'),
                btnKonfirmasiSetuju: document.getElementById('btnKonfirmasiSetuju'),
                btnTolakPinjaman: document.getElementById('btnTolakPinjaman'),
                btnEditPencairan: document.getElementById('btnEditPencairan'),
                alertPeninjauan: document.getElementById('alertPeninjauan'),
                activityEmpty: document.getElementById('activity-empty'),
                timelineContainer: document.getElementById('timeline-container'),
                forms: {
                    pencairan: document.getElementById('formPencairanDana'),
                    review: document.getElementById('formHasilReview'),
                    edit: document.getElementById('formEditPencairan')
                }
            };

            // Bootstrap Modals
            const modals = {
                persetujuan: new bootstrap.Modal(document.getElementById('modalPersetujuanPinjaman')),
                pencairan: new bootstrap.Modal(document.getElementById('modalPencairanDana')),
                review: new bootstrap.Modal(document.getElementById('modalHasilReview')),
                edit: new bootstrap.Modal(document.getElementById('modalEditPencairan'))
            };

            // Helper: Get formatted date
            const getFormattedDate = () => new Date().toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            // Helper: Toggle element visibility
            const toggleDisplay = (element, show) => {
                element?.classList.toggle('d-none', !show);
            };

            // Update stepper UI
            function updateStepper() {
                document.querySelectorAll('.stepper-arrow').forEach((arrow, index) => {
                    arrow.classList.remove('active', 'completed');
                    if (index + 1 < currentStep) arrow.classList.add('completed');
                    else if (index + 1 === currentStep) arrow.classList.add('active');
                });

                toggleDisplay(elements.btnSetujui, currentStep === 2);
                elements.alertPeninjauan.style.display = currentStep >= 2 ? 'none' : 'block';
                updateActivityTimeline();
            }

            // Update activity timeline
            function updateActivityTimeline() {
                const currentDate = getFormattedDate();

                if (currentStep < 3) {
                    toggleDisplay(elements.activityEmpty, true);
                    toggleDisplay(elements.timelineContainer, false);
                    return;
                }

                toggleDisplay(elements.activityEmpty, false);
                toggleDisplay(elements.timelineContainer, true);

                if (currentStep >= 3) {
                    const step3 = document.getElementById('activity-step-3');
                    toggleDisplay(step3, true);
                    document.getElementById('date-step-3').textContent = currentDate;
                }

                if (currentStep >= 4) {
                    const step4 = document.getElementById('activity-step-4');
                    toggleDisplay(step4, true);
                    document.getElementById('date-step-4').textContent = currentDate;
                }
            }

            // Navigate to step
            function goToStep(step) {
                if (step >= 1 && step <= totalSteps) {
                    currentStep = step;
                    updateStepper();
                }
            }

            // Initialize flatpickr
            function initFlatpickr() {
                const input = document.getElementById('flatpickr-tanggal-pencairan');
                if (input?._flatpickr) input._flatpickr.destroy();

                flatpickr(input, {
                    monthSelectorType: 'static',
                    dateFormat: 'd/m/Y',
                    altInput: true,
                    altFormat: 'j F Y',
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            }

            // Reset form
            function resetForm(form) {
                form.reset();
                form.classList.remove('was-validated');
            }

            // Switch modal with delay
            function switchModal(hideModal, showModal, callback) {
                hideModal.hide();
                setTimeout(() => {
                    callback?.();
                    showModal.show();
                    // Reinitialize Cleave for modal inputs
                    initCleaveRupiah();
                }, 300);
            }

            // Switch to activity tab
            function switchToActivityTab() {
                setTimeout(() => {
                    const tab = document.querySelector('[data-bs-target="#activity"]');
                    new bootstrap.Tab(tab).show();
                }, 300);
            }

            // Event: Stepper arrows click
            document.querySelectorAll('.stepper-arrow').forEach(arrow => {
                arrow.style.cursor = 'pointer';
                arrow.addEventListener('click', () => {
                    goToStep(parseInt(arrow.getAttribute('data-step')));
                });
            });

            // Event: Show approval modal
            elements.btnSetujui?.addEventListener('click', () => modals.persetujuan.show());

            // Event: Approve - Show pencairan modal
            elements.btnKonfirmasiSetuju?.addEventListener('click', () => {
                switchModal(modals.persetujuan, modals.pencairan, () => {
                    resetForm(elements.forms.pencairan);
                    document.getElementById('nominalPengajuan').value = '300000000';
                    document.getElementById('flatpickr-tanggal-harapan').value = '24/08/2024';
                    initFlatpickr();
                });
            });

            // Event: Reject - Show review modal
            elements.btnTolakPinjaman?.addEventListener('click', () => {
                switchModal(modals.persetujuan, modals.review, () => {
                    resetForm(elements.forms.review);
                });
            });

            // Event: Submit pencairan dana
            elements.forms.pencairan?.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    return;
                }

                pencairanData = {
                    nominalDisetujui: document.getElementById('nominalDisetujui').value,
                    tanggalPencairan: document.getElementById('flatpickr-tanggal-pencairan').value,
                    catatan: document.getElementById('catatanLainnya').value.trim()
                };

                modals.pencairan.hide();
                resetForm(this);
                goToStep(4);
                switchToActivityTab();
            });

            // Event: Submit review (reject)
            elements.forms.review?.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    e.stopPropagation();
                    this.classList.add('was-validated');
                    return;
                }

                modals.review.hide();
                resetForm(this);
                goToStep(1);
            });

            // Event: Edit pencairan
            elements.btnEditPencairan?.addEventListener('click', () => {
                document.getElementById('editNominalPengajuan').value = '300000000';
                document.getElementById('editNominalDisetujui').value = pencairanData.nominalDisetujui;
                document.getElementById('editTanggalPencairan').value = pencairanData.tanggalPencairan;
                document.getElementById('editTanggalHarapan').value = '24/08/2024';
                document.getElementById('editCatatanLainnya').value = pencairanData.catatan;
                modals.edit.show();
            });

            // Event: Submit edit pencairan
            elements.forms.edit?.addEventListener('submit', function(e) {
                e.preventDefault();
                pencairanData.catatan = document.getElementById('editCatatanLainnya').value.trim();
                modals.edit.hide();
            });

            // Initialize
            updateStepper();
            
            // Initialize Cleave.js for rupiah inputs
            initCleaveRupiah();
        });
    </script>
@endsection
