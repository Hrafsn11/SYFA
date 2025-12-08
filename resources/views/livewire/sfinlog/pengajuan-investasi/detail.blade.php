<div>
    @php
        // Dummy data - nanti bisa diganti dengan data dari Livewire component
        $currentStep = 2; // Step saat ini (1-6)
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Detail Pengajuan Investasi</h4>
            </div>

            <!-- Stepper -->
            <div class="stepper-container mb-4">
                <div class="stepper-wrapper">

                    <div class="stepper-item {{ $currentStep >= 1 ? 'completed' : '' }} {{ $currentStep == 1 ? 'active' : '' }}" data-step="1">
                        <div class="stepper-node">
                        </div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 1</div>
                            <div class="step-name">Pengajuan Investasi</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 2 ? 'completed' : '' }} {{ $currentStep == 2 ? 'active' : '' }}" data-step="2">
                        <div class="stepper-node">
                        </div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 2</div>
                            <div class="step-name">Validasi Pengajuan</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 3 ? 'completed' : '' }} {{ $currentStep == 3 ? 'active' : '' }}" data-step="3">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 3</div>
                            <div class="step-name">Persetujuan CEO Finlog</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 4 ? 'completed' : '' }} {{ $currentStep == 4 ? 'active' : '' }}" data-step="4">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 4</div>
                            <div class="step-name">Validasi Investor</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 5 ? 'completed' : '' }} {{ $currentStep == 5 ? 'active' : '' }}" data-step="5">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 5</div>
                            <div class="step-name">Validasi Transaksi</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 6 ? 'completed' : '' }} {{ $currentStep == 6 ? 'active' : '' }}" data-step="6">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 6</div>
                            <div class="step-name">Generate Kontrak</div>
                        </div>
                    </div>

                    {{-- <div class="stepper-item" data-step="7">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 7</div>
                            <div class="step-name">Upload Dokumen Transfer</div>
                        </div>
                    </div> --}}

                    {{-- <div class="stepper-item" data-step="8">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 8</div>
                            <div class="step-name">Selesai</div>
                        </div>
                    </div> --}}

                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-0">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#detail-investasi" role="tab" aria-selected="true">
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

                        <div class="card-body">
                            <div class="tab-content p-0">
                                <!-- Detail Investasi Tab -->
                                <div class="tab-pane fade show active" id="detail-investasi" role="tabpanel">
                                    @include('livewire.sfinlog.pengajuan-investasi.partials.detail-tab')
                                </div>

                                <!-- Detail Kontrak Tab -->
                                <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                                    @include('livewire.sfinlog.pengajuan-investasi.partials.kontrak-tab')
                                </div>

                                <!-- Activity Tab -->
                                <div class="tab-pane fade" id="activity" role="tabpanel">
                                    @include('livewire.sfinlog.pengajuan-investasi.partials.activity-tab')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Persetujuan Pengajuan -->
    <div class="modal fade" id="modalPersetujuanPengajuan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <hr class="my-2">
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menyetujui pengajuan investasi ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnTolakPengajuan">Tolak</button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiSetuju">Setuju</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Validasi CEO -->
<div class="modal fade" id="modalValidasiCEO" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validasi CEO Finlog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <hr class="my-2">
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menyetujui pengajuan investasi ini sebagai CEO Finlog?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnTolakCEO">Tolak</button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiCEO">Setuju</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Validasi Investor -->
<div class="modal fade" id="modalValidasiInvestor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validasi Investor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <hr class="my-2">
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin untuk memvalidasi pengajuan investasi ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnTolakInvestor">Tolak</button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiInvestor">Setuju</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formHasilReview">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan" rows="4" 
                            placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-x me-1"></i>
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Generate Kontrak -->
<div class="modal fade" id="modalGenerateKontrak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Kontrak Investasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formGenerateKontrak">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomor_kontrak" class="form-label">Nomor Kontrak <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nomor_kontrak" 
                            placeholder="FINLOG/INV/2025/001" required>
                        <small class="text-muted">Format: FINLOG/INV/TAHUN/NOMOR</small>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_kontrak" class="form-label">Tanggal Kontrak <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control bs-datepicker" id="tanggal_kontrak" 
                                placeholder="yyyy-mm-dd" required>
                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        Kontrak akan digenerate berdasarkan data investasi yang sudah divalidasi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-file-text me-1"></i>
                        Generate Kontrak
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUploadBuktiTransfer">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file_bukti_transfer" class="form-label">File Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file_bukti_transfer" 
                            accept="image/*,.pdf" required>
                        <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan_transfer" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan_transfer" rows="3" 
                            placeholder="Masukkan keterangan (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-upload me-1"></i>
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview Bukti Transfer -->
<div class="modal fade" id="modalPreviewBukti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Bukti Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid d-none" alt="Bukti Transfer">
                <iframe id="previewPdf" src="" class="d-none" style="width:100%; height:500px;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="downloadBukti" class="btn btn-primary" download>
                    <i class="ti ti-download me-1"></i>
                    Download
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize datepicker for kontrak modal
        $('.bs-datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto'
        });

        // Button handlers
        $('#btnSubmitPengajuan').click(function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin submit pengajuan investasi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Submit',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Berhasil!', 'Pengajuan berhasil disubmit.', 'success')
                        .then(() => location.reload());
                }
            });
        });

        $('#btnSetujuiPengajuan').click(function() {
            $('#modalPersetujuanPengajuan').modal('show');
        });

        $('#btnKonfirmasiSetuju').click(function() {
            $('#modalPersetujuanPengajuan').modal('hide');
            Swal.fire('Berhasil!', 'Pengajuan berhasil divalidasi.', 'success')
                .then(() => location.reload());
        });

        $('#btnTolakPengajuan').click(function() {
            $('#modalPersetujuanPengajuan').modal('hide');
            setTimeout(() => $('#modalHasilReview').modal('show'), 300);
        });

        $('#btnValidasiCEO').click(function() {
            $('#modalValidasiCEO').modal('show');
        });

        $('#btnKonfirmasiCEO').click(function() {
            $('#modalValidasiCEO').modal('hide');
            Swal.fire('Berhasil!', 'Pengajuan berhasil divalidasi oleh CEO Finlog.', 'success')
                .then(() => location.reload());
        });

        $('#btnTolakCEO').click(function() {
            $('#modalValidasiCEO').modal('hide');
            setTimeout(() => $('#modalHasilReview').modal('show'), 300);
        });

        $('#btnValidasiInvestor').click(function() {
            $('#modalValidasiInvestor').modal('show');
        });

        $('#btnKonfirmasiInvestor').click(function() {
            $('#modalValidasiInvestor').modal('hide');
            Swal.fire('Berhasil!', 'Pengajuan berhasil divalidasi oleh investor.', 'success')
                .then(() => location.reload());
        });

        $('#btnTolakInvestor').click(function() {
            $('#modalValidasiInvestor').modal('hide');
            setTimeout(() => $('#modalHasilReview').modal('show'), 300);
        });

        $('#btnValidasiTransaksi').click(function() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin memvalidasi transaksi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Validasi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Berhasil!', 'Transaksi berhasil divalidasi.', 'success')
                        .then(() => location.reload());
                }
            });
        });

        $('#formHasilReview').submit(function(e) {
            e.preventDefault();
            $('#modalHasilReview').modal('hide');
            Swal.fire('Ditolak!', 'Pengajuan berhasil ditolak.', 'info')
                .then(() => location.reload());
        });

        $('#formGenerateKontrak').submit(function(e) {
            e.preventDefault();
            $('#modalGenerateKontrak').modal('hide');
            Swal.fire('Berhasil!', 'Kontrak berhasil digenerate.', 'success')
                .then(() => location.reload());
        });

        $('#btnPreviewKontrak').click(function() {
            Swal.fire({
                title: 'Preview Kontrak',
                text: 'Kontrak dengan nomor FINLOG/INV/2025/001',
                icon: 'info'
            });
        });

        $('#formUploadBuktiTransfer').submit(function(e) {
            e.preventDefault();
            const file = $('#file_bukti_transfer')[0].files[0];
            
            if (!file) {
                Swal.fire('Error!', 'Silakan pilih file terlebih dahulu.', 'error');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('Error!', 'Ukuran file maksimal 2MB.', 'error');
                return;
            }

            $('#modalUploadBuktiTransfer').modal('hide');
            Swal.fire('Berhasil!', 'Bukti transfer berhasil diupload.', 'success')
                .then(() => location.reload());
        });
    });
</script>
@endpush
</div>
