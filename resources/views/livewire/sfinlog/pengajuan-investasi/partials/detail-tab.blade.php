<div id="content-default">
    @php
        $currentStep = $pengajuan->current_step ?? 1;
        $status = $pengajuan->status ?? 'Draft';
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2 mt-3">
        <h5 class="mb-3 mb-md-4">Detail Investasi</h5>
        <div class="d-flex gap-2">
            @can('pengajuan_investasi_finlog.submit')
            @if($currentStep == 1 && $status == 'Draft')
                <button type="button" class="btn btn-success" id="btnSubmitPengajuan">
                    <i class="fas fa-paper-plane me-2"></i>
                    Submit Pengajuan
                </button>
            @endif
            @endcan

            @can('pengajuan_investasi_finlog.validasi_finance_ski')
            @if($currentStep == 2 && str_contains($status, 'Menunggu Validasi Finance SKI'))
                <button type="button" class="btn btn-primary" id="btnValidasiFinanceSKI">
                    <i class="fas fa-check me-2"></i>
                    Validasi Finance SKI
                </button>
            @endif
            @endcan

            @can('pengajuan_investasi_finlog.validasi_ceo_finlog')
            @if($currentStep == 3 && str_contains($status, 'Menunggu Persetujuan CEO Finlog'))
                <button type="button" class="btn btn-primary" id="btnValidasiCEO">
                    <i class="ti ti-check me-2"></i>
                    Validasi CEO Finlog
                </button>
            @endif
            @endcan

            @can('pengajuan_investasi_finlog.upload_bukti')
            @if($currentStep == 4 && str_contains($status, 'Menunggu Upload Bukti Transfer'))
                <button type="button" class="btn btn-success" id="btnUploadBuktiTransfer">
                    <i class="ti ti-upload me-2"></i>
                    Upload Bukti Transfer
                </button>
            @endif
            @endcan

            @if(str_contains($status, 'Ditolak'))
                <span class="badge bg-danger fs-6">
                    <i class="ti ti-x me-1"></i>
                    {{ $status }}
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
                <p class="fw-bold mb-0">{{ $pengajuan->nama_investor ?? '-' }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Project</small>
                <p class="fw-bold mb-0">{{ $pengajuan->project->nama_project ?? '-' }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Tanggal Investasi</small>
                <p class="fw-bold mb-0">{{ $pengajuan->tanggal_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_investasi)->format('d F Y') : '-' }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Lama Berinvestasi</small>
                <p class="fw-bold mb-0">{{ $pengajuan->lama_investasi ?? 0 }} Bulan</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Tanggal Berakhir Investasi</small>
                <p class="fw-bold mb-0">{{ $pengajuan->tanggal_berakhir_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_berakhir_investasi)->format('d F Y') : '-' }}</p>
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
                <p class="fw-bold mb-0">Rp {{ number_format($pengajuan->nominal_investasi ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Bagi Hasil (%)/Tahun</small>
                <p class="fw-bold mb-0">{{ $pengajuan->persentase_bagi_hasil ?? 0 }}%</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nominal Bagi Hasil Yang Didapat</small>
                <p class="fw-bold mb-0">Rp {{ number_format($pengajuan->nominal_bagi_hasil_yang_didapat ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>