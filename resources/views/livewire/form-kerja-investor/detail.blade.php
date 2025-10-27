@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <h4 class="fw-bold mb-4">
                    Detail Pengajuan Investasi
                </h4>

                <!-- AStepper -->
                <div class="stepper-container mb-4">
                    <div class="stepper-wrapper">

                        <div class="stepper-item completed" data-step="1">
                            <div class="stepper-node">
                            </div>
                            <div class="stepper-content">
                                <div class="step-label">STEP 1</div>
                                <div class="step-name">Pengajuan Investor</div>
                            </div>
                        </div>

                        <div class="stepper-item active" data-step="2">
                            <div class="stepper-node">
                            </div>
                            <div class="stepper-content">
                                <div class="step-label">STEP 2</div>
                                <div class="step-name">Validasi Bagi Hasil</div>
                            </div>
                        </div>

                        <div class="stepper-item" data-step="3">
                            <div class="stepper-node"></div>
                            <div class="stepper-content">
                                <div class="step-label">STEP 3</div>
                                <div class="step-name">Upload Bukti Transfer</div>
                            </div>
                        </div>

                        <div class="stepper-item" data-step="4">
                            <div class="stepper-node"></div>
                            <div class="stepper-content">
                                <div class="step-label">STEP 4</div>
                                <div class="step-name">Generate Kontak</div>
                            </div>
                        </div>

                        <div class="stepper-item" data-step="5">
                            <div class="stepper-node"></div>
                            <div class="stepper-content">
                                <div class="step-label">STEP 5</div>
                                <div class="step-name">Selesai</div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
                    <i class="fas fa-info-circle me-2"></i>
                    Pengajuan Investasi Anda sedang kami tinjau. Harap tunggu beberapa saat hingga proses verifikasi
                    selesai.
                </div>
            </div>
        </div>
    </div>
@endsection
