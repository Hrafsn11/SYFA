@extends('layouts.app')
<style>
    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0 auto;
        max-width: 1200px;
    }

    .stepper-item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .stepper-item:not(:last-child):after {
        content: '';
        position: absolute;
        width: 100%;
        height: 3px;
        background-color: #dee2e6;
        top: 20px;
        left: 50%;
        z-index: -1;
    }

    .stepper-item.active:not(:last-child):after {
        background-color: #17a2b8;
    }

    .step-counter {
        position: relative;
        z-index: 5;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #dee2e6;
        color: #6c757d;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .stepper-item.active .step-counter {
        background-color: #17a2b8;
        color: white;
    }

    .stepper-item.completed .step-counter {
        background-color: #17a2b8;
        color: white;
    }

    .step-name {
        text-align: center;
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
        max-width: 150px;
    }

    .stepper-item.active .step-name {
        color: #17a2b8;
        font-weight: 600;
    }

    .stepper-item.completed .step-name {
        color: #17a2b8;
    }

    .stepper-arrow {
        clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 50%, calc(100% - 20px) 100%, 0 100%, 20px 50%);
        background-color: #dee2e6;
        padding: 12px 30px 12px 40px;
        margin-right: -18px;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 180px;
    }

    .stepper-arrow.active {
        background-color: #13ABAB;
    }

    .stepper-arrow.completed {
        background-color: #13ABAB;
    }

    .stepper-arrow:first-child {
        padding-left: 20px;
        border-radius: 50px 0 0 50px;
        clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 50%, calc(100% - 20px) 100%, 0 100%);
    }

    .stepper-arrow:last-child {
        margin-right: 0;
        padding-right: 20px;
        border-radius: 0 50px 50px 0;
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 20px 50%);
    }

    .arrow-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #6c757d;
        flex-shrink: 0;
    }

    .stepper-arrow.active .arrow-number,
    .stepper-arrow.completed .arrow-number {
        color: #17a2b8;
    }

    .arrow-text {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }

    .stepper-arrow.active .arrow-text,
    .stepper-arrow.completed .arrow-text {
        color: white;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .stepper-arrow {
            min-width: 140px;
            padding: 10px 25px 10px 35px;
            font-size: 12px;
        }

        .arrow-number {
            width: 24px;
            height: 24px;
            font-size: 12px;
        }

        .arrow-text {
            font-size: 11px;
        }
    }
</style>
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Arrow Style Stepper -->
            <div class="d-flex overflow-auto mb-5">
                <div class="stepper-arrow active">
                    <div class="arrow-number">1</div>
                    <div class="arrow-text">Pengajuan Pinjaman</div>
                </div>
                <div class="stepper-arrow ">
                    <div class="arrow-number">2</div>
                    <div class="arrow-text">Validasi Dokumen</div>
                </div>
                <div class="stepper-arrow">
                    <div class="arrow-number">3</div>
                    <div class="arrow-text">Dokumen Tervalidasi</div>
                </div>
                <div class="stepper-arrow">
                    <div class="arrow-number">4</div>
                    <div class="arrow-text">Persetujuan Debitur</div>
                </div>
                <div class="stepper-arrow">
                    <div class="arrow-number">5</div>
                    <div class="arrow-text">Validasi Direktur SKI</div>
                </div>
                <div class="stepper-arrow">
                    <div class="arrow-number">6</div>
                    <div class="arrow-text">Generate Kontrak</div>
                </div>
                <div class="stepper-arrow">
                    <div class="arrow-number">7</div>
                    <div class="arrow-text">Selesai</div>
                </div>
            </div>
            <div class="alert alert-warning" role="alert">Pengajuan Pinjaman Anda sedang kami tinjau. Harap tunggu
                beberapa saat hingga proses verifikasi selesai.</div>

            <div class="page-wrapper">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header px-0 pt-0">
                                <div class="nav-align-top">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <button type="button" class="nav-link active" data-bs-toggle="tab"
                                                data-bs-target="#detail-pinjaman" role="tab" aria-selected="true">
                                                <i class="fas fa-file-invoice d-sm-none"></i>
                                                <span class="d-none d-sm-inline">Detail Pinjaman</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab"
                                                data-bs-target="#detail-kontrak" role="tab" aria-selected="false">
                                                <i class="far fa-file-alt d-sm-none"></i>
                                                <span class="d-none d-sm-inline">Detail Kontrak</span>
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab"
                                                data-bs-target="#activity" role="tab" aria-selected="false">
                                                <i class="fas fa-chart-line d-sm-none"></i>
                                                <span class="d-none d-sm-inline">Activity</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <!-- Detail Pinjaman Tab -->
                                    <div class="tab-pane fade show active" id="detail-pinjaman" role="tabpanel">
                                        <h5 class="mb-4">Detail Pinjaman</h5>

                                        <!-- Data Perusahaan -->
                                        <h6 class="text-muted mb-3">Data Perusahaan</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6 col-lg-4 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nama Perusahaan</small>
                                                    <p class="mb-0">Techno Infinity</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nama Bank</small>
                                                    <p class="mb-0">HC Service</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">No Rekening</small>
                                                    <p class="mb-0">130023032390239</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Lampiran SID</small>
                                                    <p class="mb-0">Pertanyaan Untuk BP Tapera</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nilai KOL</small>
                                                    <p class="mb-0">3 KOL</p>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <!-- Data Peminjaman -->
                                        <h6 class="text-muted mb-3">Data Peminjaman</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nominal Pinjaman</small>
                                                    <p class="mb-0 text-primary fw-semibold">Rp. 300.000.000</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Harapan Tanggal Pencairan</small>
                                                    <p class="mb-0">24 Agustus 2024</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Persentase Bagi Hasil</small>
                                                    <p class="mb-0">2%</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Jenis Pembiayaan</small>
                                                    <p class="mb-0">Invoice Financing</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Rencana Tanggal Bayar</small>
                                                    <p class="mb-0">24 September 2024</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Pembayaran Total</small>
                                                    <p class="mb-0 text-warning fw-semibold">Rp. 100.000.000</p>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <!-- Data Invoicing -->
                                        <h6 class="text-muted mb-3">Data Invoicing</h6>
                                        <div class="table-responsive text-nowrap">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-uppercase small">NO</th>
                                                        <th class="text-uppercase small">NO. INVOICE</th>
                                                        <th class="text-uppercase small">NAMA CLIENT</th>
                                                        <th class="text-uppercase small">NILAI INVOICE</th>
                                                        <th class="text-uppercase small">INVOICE DATE</th>
                                                        <th class="text-uppercase small">DUE DATE</th>
                                                        <th class="text-uppercase small">DOKUMEN INVOICE</th>
                                                        <th class="text-uppercase small">DOKUMEN SO</th>
                                                        <th class="text-uppercase small">DOKUMEN KONTRAK</th>
                                                        <th class="text-uppercase small">DOKUMEN BAST</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td><span class="fw-semibold">2222</span></td>
                                                        <td>Pelni</td>
                                                        <td><span class="fw-semibold">Rp. 10.000.000</span></td>
                                                        <td>15 August 2025</td>
                                                        <td>24 August 2025</td>
                                                        <td><a href="#" class="text-primary"><i class="far fa-file-pdf me-1"></i>Dokumen.pdf</a></td>
                                                        <td><a href="#" class="text-primary"><i class="far fa-file-pdf me-1"></i>Dokumen.pdf</a></td>
                                                        <td><a href="#" class="text-primary"><i class="far fa-file-pdf me-1"></i>Dokumen.pdf</a></td>
                                                        <td><a href="#" class="text-primary"><i class="far fa-file-pdf me-1"></i>Dokumen.pdf</a></td>
                                                    </tr>
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
                                        <div class="text-center py-5">
                                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Activity</h5>
                                            <p class="text-muted">Konten activity akan ditampilkan di sini.</p>
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



    {{-- <script>
        let currentStep = 2;
        const totalSteps = 7;
        
        function updateStepper() {
            const arrows = document.querySelectorAll('.stepper-arrow');
            
            arrows.forEach((arrow, index) => {
                arrow.classList.remove('active', 'completed');
                
                if (index + 1 < currentStep) {
                    arrow.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    arrow.classList.add('active');
                }
            });
        }
        
        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                updateStepper();
            }
        }
        
        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                updateStepper();
            }
        }
        
        // Initialize
        updateStepper();
    </script> --}}
@endsection
