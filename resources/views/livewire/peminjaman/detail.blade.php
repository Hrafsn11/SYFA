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
                                    <!-- Konten Default (Step 1-6, 8-9) -->
                                    <div id="content-default">
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
                                    <!-- End Konten Default -->
                                </div>

                                <!-- Detail Kontrak Tab -->
                                <!-- Detail Kontrak Tab -->
                                <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                                    <!-- Konten Default (Before Step 7) -->
                                    <div id="kontrak-default">
                                        <div class="text-center py-5">
                                            <i class="far fa-file-alt fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Detail Kontrak</h5>
                                            <p class="text-muted">Konten detail kontrak akan ditampilkan di sini.</p>
                                        </div>
                                    </div>

                                    <!-- Konten Step 7: Generate Kontrak -->
                                    <div id="kontrak-step7" class="d-none">
                                        <h5 class="mb-4">Generate Kontrak Peminjaman</h5>
                                        <form action="" id="formGenerateKontrak">
                                            <div class="col-lg mb-3">
                                                <label for="jenis_pembiayaan" class="form-label">Jenis
                                                    Pembiayaan</label>
                                                <input type="text" class="form-control" id="jenis_pembiayaan"
                                                    name="jenis_pembiayaan" value="Invoice & Project Financing"
                                                    required disabled>
                                            </div>

                                            <div class="card border-1 shadow-none mb-3">
                                                <div class="card-body">
                                                    <div class="col-lg mb-3">
                                                        <label for="nama_perusahaan" class="form-label">Nama
                                                            Perusahaan</label>
                                                        <input type="text" class="form-control"
                                                            id="nama_perusahaan" name="nama_perusahaan"
                                                            value="Techno Infinity" required disabled>
                                                    </div>

                                                    <div class="col-lg mb-3">
                                                        <label for="nama_pimpinan" class="form-label">
                                                            Nama Pimpinan
                                                        </label>
                                                        <input type="text" class="form-control" id="nama_pimpinan"
                                                            name="nama_pimpinan" value="Cahyo" required disabled>
                                                    </div>

                                                    <div class="col-lg mb-3">
                                                        <label for="alamat" class="form-label">
                                                            Alamat Perusahaan
                                                        </label>
                                                        <input type="text" class="form-control" id="alamat"
                                                            name="alamat"
                                                            value="Gd. Permata Kuningan Lantai 17 Unit 07 Jl. Kuningan Mulia"
                                                            required disabled>
                                                    </div>

                                                    <div class="col-lg mb-3">
                                                        <label for="tujuan" class="form-label">
                                                            Tujuan Pembiayaan
                                                        </label>
                                                        <input type="text" class="form-control" id="tujuan"
                                                            name="tujuan"
                                                            value="Kebutuhan Gaji Operasional/Umum Sept" required
                                                            disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-2">
                                                    <label for="nilai_pembiayaan">Nilai Pembiayaan</label>
                                                    <input type="text" class="form-control" id="nilai_pembiayaan"
                                                        name="nilai_pembiayaan" value="Rp.250.000.000" disabled>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="hutang_pokok">Hutang Pokok</label>
                                                    <input type="text" class="form-control" id="hutang_pokok"
                                                        name="hutang_pokok" value="Rp.250.000.000" disabled>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-2">
                                                    <label for="tenor">Tenor Pembiayaan</label>
                                                    <input type="text" class="form-control" id="tenor"
                                                        name="tenor" value="1 Bulan" disabled>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="biaya_admin">Biaya Administrasi</label>
                                                    <input type="text" class="form-control" id="biaya_admin"
                                                        name="biaya_admin" value="Rp.0.00" disabled>
                                                </div>
                                            </div>

                                            <div class="col-lg mb-3">
                                                <label for="nisbah" class="form-label">Bagi Hasil (Nisbah)</label>
                                                <input type="text" class="form-control" id="nisbah"
                                                    name="nisbah" value="2% flat / bulan" required disabled>
                                            </div>

                                            <div class="col-lg mb-3">
                                                <label for="denda_keterlambatan" class="form-label">
                                                    Denda Keterlambatan
                                                </label>
                                                <input type="text" class="form-control" id="denda_keterlambatan"
                                                    name="denda_keterlambatan"
                                                    value="2% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut"
                                                    required disabled>
                                            </div>

                                            <div class="col-lg mb-3">
                                                <label for="jaminan" class="form-label">
                                                    Jaminan
                                                </label>
                                                <input type="text" class="form-control" id="jaminan"
                                                    name="jaminan" value="Invoice & Project Financing" required
                                                    disabled>
                                            </div>

                                            <div class="col-lg mb-3">
                                                <label for="ttd_debitur" class="form-label">
                                                    Tanda Tangan Debitur
                                                </label>
                                                <input type="file" class="form-control" id="ttd_debitur" required>
                                                <div class="invalid-feedback">
                                                    Silakan pilih file untuk diupload.
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary" id="btnSimpanKontrak">
                                                    <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanKontrakSpinner"></span>
                                                    Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- End Konten Step 7 -->
                                </div>

                                <!-- Activity Tab -->
                                @include('livewire.peminjaman.partials._activity-tabs')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Modal -->
    @include('livewire.peminjaman.partials._modal-detail-peminjaman')

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
                detailKontrakTab: document.querySelector('[data-bs-target="#detail-kontrak"]'),
                timeline: {
                    container: document.getElementById('timeline-container'),
                    empty: document.getElementById('activity-empty'),
                    items: document.querySelectorAll('.activity-item'),
                },
                buttons: {
                    setujuiPeminjaman: document.getElementById('btnSetujuiPeminjaman'),
                    konfirmasiSetuju: document.getElementById('btnKonfirmasiSetuju'),
                    tolakPinjaman: document.getElementById('btnTolakPinjaman'),
                    editPencairan: document.querySelectorAll(
                        '#btnEditPencairan'), // Menggunakan querySelectorAll
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
                const hideModalEl = document.getElementById(hideModal._element.id);
                const showModalEl = document.getElementById(showModal._element.id);

                const handleHidden = () => {
                    onShow?.();
                    showModal.show();
                    showModalEl.addEventListener('shown.bs.modal', function() {
                        initCleaveRupiah(); // Re-initialize cleave on new modal
                    }, {
                        once: true
                    });

                    hideModalEl.removeEventListener('hidden.bs.modal', handleHidden);
                };

                hideModalEl.addEventListener('hidden.bs.modal', handleHidden);
                hideModal.hide();
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
                updateDetailKontrakContent(); // Update konten tab Detail Kontrak
                updateActivityTimeline();
            };

            const updateDetailKontrakContent = () => {
                const kontrakDefault = document.getElementById('kontrak-default');
                const kontrakStep7 = document.getElementById('kontrak-step7');

                if (state.currentStep === 7) {
                    // Step 7: Tampilkan form Generate Kontrak
                    toggleDisplay(kontrakDefault, false);
                    toggleDisplay(kontrakStep7, true);
                } else {
                    // Step lainnya: Tampilkan konten default
                    toggleDisplay(kontrakDefault, true);
                    toggleDisplay(kontrakStep7, false);
                }
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
                new bootstrap.Tab(dom.activityTab).show();
            };

            const switchToDetailKontrakTab = () => {
                new bootstrap.Tab(dom.detailKontrakTab).show();
            };


            // --- EVENT HANDLERS ---
            const handleStepperClick = (e) => {
                const target = e.target.closest('.stepper-arrow');
                if (target) {
                    const step = parseInt(target.dataset.step);
                    goToStep(step);
                    
                    // Auto switch ke tab Detail Kontrak jika step 7
                    if (step === 7) {
                        switchToDetailKontrakTab();
                    }
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
                goToStep(1);
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

                dom.modals.upload.hide();
                resetForm(dom.forms.upload);
                goToStep(9);
                switchToActivityTab();
            };

            const handleGenerateKontrakSubmit = (e) => {
                e.preventDefault();
                
                const form = e.target;
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }

                const btnSimpan = document.getElementById('btnSimpanKontrak');
                const spinner = document.getElementById('btnSimpanKontrakSpinner');
                const originalText = btnSimpan.innerHTML;

                // Show loading
                btnSimpan.disabled = true;
                spinner.classList.remove('d-none');

                // Simulasi proses generate kontrak (ganti dengan AJAX call sebenarnya)
                setTimeout(() => {
                    btnSimpan.disabled = false;
                    spinner.classList.add('d-none');
                    
                    // Reset form
                    form.classList.remove('was-validated');
                    
                    // Success - pindah ke step 8
                    goToStep(8);
                    switchToActivityTab();
                }, 2000);
            };

            const handleBatalKontrak = () => {
                const form = document.getElementById('formGenerateKontrak');
                form.classList.remove('was-validated');
                // Kembali ke step sebelumnya atau tetap di step 7
            };

            const handlePreviewKontrak = () => {
                // Get peminjaman ID from current page
                const peminjamanId = {{ $peminjaman['id'] ?? 1 }};
                
                // Open preview in new tab
                window.open(`/peminjaman/${peminjamanId}/preview-kontrak`, '_blank');
            };


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

            dom.buttons.editPencairan.forEach(btn => {
                btn.addEventListener('click', handleEditPencairanShow);
            });

            dom.forms.pencairan?.addEventListener('submit', handlePencairanSubmit);
            dom.forms.review?.addEventListener('submit', handleReviewSubmit);
            dom.forms.edit?.addEventListener('submit', handleEditPencairanSubmit);
            dom.forms.upload?.addEventListener('submit', handleUploadSubmit);

            // Event listener untuk form Generate Kontrak di Step 7
            const formGenerateKontrak = document.getElementById('formGenerateKontrak');
            formGenerateKontrak?.addEventListener('submit', handleGenerateKontrakSubmit);
            
            const btnBatalKontrak = document.getElementById('btnBatalKontrak');
            btnBatalKontrak?.addEventListener('click', handleBatalKontrak);

            // Event listener untuk button Preview Kontrak di Activity Tab
            const btnPreviewKontrak = document.getElementById('btnPreviewKontrak');
            btnPreviewKontrak?.addEventListener('click', handlePreviewKontrak);

            updateStepper();
            initCleaveRupiah();

        });
    </script>
@endsection
