<div id="content-default">
    @php
        // Dummy data - nanti bisa diganti dengan data dari Livewire component
        $currentStep = 2; // Step saat ini (1-6)
        $status = 'Menunggu Validasi'; // Draft, Menunggu Validasi, Disetujui, Ditolak
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2 mt-3">
        <h5 class="mb-3 mb-md-4">Detail Investasi</h5>
        <div class="d-flex gap-2">
            @if($currentStep == 1 && $status == 'Draft')
                <button type="button" class="btn btn-success" id="btnSubmitPengajuan">
                    <i class="fas fa-paper-plane me-2"></i>
                    Submit Pengajuan
                </button>
            @endif

            @if($currentStep == 2 && $status == 'Menunggu Validasi')
                <button type="button" class="btn btn-primary" id="btnSetujuiPengajuan">
                    <i class="fas fa-check me-2"></i>
                    Validasi Pengajuan
                </button>
            @endif

            @if($currentStep == 3 && $status == 'Menunggu Validasi')
                <button type="button" class="btn btn-info" id="btnValidasiCEO">
                    <i class="ti ti-check me-2"></i>
                    Validasi CEO Finlog
                </button>
            @endif

            @if($currentStep == 4 && $status == 'Menunggu Validasi')
                <button type="button" class="btn btn-warning" id="btnValidasiInvestor">
                    <i class="ti ti-user-check me-2"></i>
                    Validasi Investor
                </button>
            @endif

            @if($currentStep == 5 && $status == 'Menunggu Validasi')
                <button type="button" class="btn btn-success" id="btnValidasiTransaksi">
                    <i class="ti ti-cash me-2"></i>
                    Validasi Transaksi
                </button>
            @endif

            @if($status == 'Ditolak')
                <span class="badge bg-danger fs-6">
                    <i class="ti ti-x me-1"></i>
                    Pengajuan Ditolak
                </span>
            @endif

            @if($status == 'Disetujui')
                <span class="badge bg-success fs-6">
                    <i class="ti ti-check me-1"></i>
                    Pengajuan Disetujui
                </span>
            @endif
        </div>
    </div>

    <hr class="my-3 my-md-4">

    <!-- Data Investasi -->
    <h6 class="text-dark mb-3">Data Investasi</h6>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nama Investor</small>
                <p class="fw-bold mb-0">John Doe</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Project</small>
                <p class="fw-bold mb-0">Velocity</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Tanggal Investasi</small>
                <p class="fw-bold mb-0">08 December 2025</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Lama Berinvestasi</small>
                <p class="fw-bold mb-0">12 Bulan</p>
            </div>
        </div>
    </div>

    <hr class="my-3 my-md-4">

    <!-- Data Pembiayaan -->
    <h6 class="text-dark mb-3">Data Pembiayaan</h6>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Jumlah Investasi</small>
                <p class="fw-bold mb-0">Rp 100.000.000</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Bagi Hasil (%)/Tahun</small>
                <p class="fw-bold mb-0">12%</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nominal Bagi Hasil Yang Didapat</small>
                <p class="fw-bold mb-0">Rp 12.000.000</p>
            </div>
        </div>
    </div>
</div>