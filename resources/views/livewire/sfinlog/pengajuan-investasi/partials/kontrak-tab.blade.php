<div id="content-kontrak">
    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2 mt-3">
        <h5 class="mb-0">Detail Kontrak</h5>
        {{-- <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" id="btnGenerateKontrak" data-bs-toggle="modal" data-bs-target="#modalGenerateKontrak">
                <i class="ti ti-file-text me-2"></i>
                Generate Kontrak
            </button>
            <button type="button" class="btn btn-info" id="btnPreviewKontrak">
                <i class="ti ti-eye me-2"></i>
                Preview Kontrak
            </button>
            <button type="button" class="btn btn-success" id="btnUploadBuktiTransfer" data-bs-toggle="modal" data-bs-target="#modalUploadBuktiTransfer">
                <i class="ti ti-upload me-2"></i>
                Upload Bukti Transfer
            </button>
        </div> --}}
    </div>

    <hr class="my-3 my-md-4">

    <!-- Info Kontrak -->
    <div class="alert alert-info mb-4" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        Silakan generate kontrak dan upload bukti transfer untuk melanjutkan proses.
    </div>

    <!-- Data Kontrak -->
    <h6 class="text-dark mb-3">Informasi Kontrak</h6>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nomor Kontrak</small>
                <p class="fw-bold mb-0">FINLOG/INV/2025/001</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Tanggal Generate</small>
                <p class="fw-bold mb-0">08 December 2025</p>
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
    <h6 class="text-dark mb-3">Bukti Transfer</h6>
    <div class="row g-3">
        <div class="col-12">
            <div class="card border">
                <div class="card-body text-center py-5">
                    <i class="ti ti-file-upload" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-3 mb-0">Belum ada bukti transfer yang diupload</p>
                </div>
            </div>
        </div>
    </div>
</div>