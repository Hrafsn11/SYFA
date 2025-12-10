<div id="content-kontrak">
    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2 mt-3">
        <h5 class="mb-0">Detail Kontrak</h5>
    </div>

    <hr class="my-3 my-md-4">

    @if($pengajuan->nomor_kontrak)
        <!-- Data Kontrak -->
        <h6 class="text-dark mb-3">Informasi Kontrak</h6>
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nomor Kontrak</small>
                    <p class="fw-bold mb-0">{{ $pengajuan->nomor_kontrak }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Tanggal Mulai</small>
                    <p class="fw-bold mb-0">{{ $pengajuan->tanggal_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_investasi)->format('d F Y') : '-' }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Tanggal Berakhir</small>
                    <p class="fw-bold mb-0">{{ $pengajuan->tanggal_berakhir_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_berakhir_investasi)->format('d F Y') : '-' }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nama Investor</small>
                    <p class="fw-bold mb-0">{{ $pengajuan->nama_investor }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nominal Investasi</small>
                    <p class="fw-bold mb-0">Rp {{ number_format($pengajuan->nominal_investasi, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Status Kontrak</small>
                    <span class="badge bg-success">Aktif</span>
                </div>
            </div>
        </div>

        <hr class="my-3 my-md-4">

        <!-- Bukti Transfer -->
        @if($pengajuan->upload_bukti_transfer)
            <h6 class="text-dark mb-3">Bukti Transfer</h6>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" onclick="previewBuktiTransfer('{{ asset('storage/' . $pengajuan->upload_bukti_transfer) }}')">
                        <i class="ti ti-eye me-2"></i>
                        Lihat Bukti Transfer
                    </button>
                </div>
            </div>
        @endif
    @else
        <!-- Info Kontrak belum ada -->
        <div class="alert alert-info mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            Kontrak belum digenerate. Menunggu proses validasi selesai.
        </div>
    @endif

    
</div>