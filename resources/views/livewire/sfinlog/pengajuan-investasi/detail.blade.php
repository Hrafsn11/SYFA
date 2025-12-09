<div>
    @php
        $currentStep = $pengajuan->current_step ?? 1;
        $status = $pengajuan->status ?? 'Draft';
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
                            <div class="step-name">Upload Bukti Transfer</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 6 ? 'completed' : '' }} {{ $currentStep == 6 ? 'active' : '' }}" data-step="6">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 6</div>
                            <div class="step-name">Generate Kontrak</div>
                        </div>
                    </div>
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

    <!-- Modal Validasi Finance SKI -->
    <div class="modal fade" id="modalValidasiFinanceSKI" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validasi Finance SKI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formValidasiFinanceSKI">
                    <div class="modal-body">
                        <h5 class="mb-3">Apakah anda yakin menyetujui pengajuan investasi ini?</h5>
                        
                        <div class="mb-3">
                            <label for="tanggal_investasi_validasi" class="form-label">Tanggal Investasi</label>
                            <div class="input-group">
                                <input type="text" class="form-control bs-datepicker" id="tanggal_investasi_validasi" 
                                    value="{{ $pengajuan->tanggal_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_investasi)->format('Y-m-d') : '' }}" placeholder="yyyy-mm-dd">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                            <small class="text-muted">Anda dapat mengubah tanggal investasi atau membiarkan seperti semula</small>
                        </div>

                        <p class="mb-0 text-muted">Silahkan klik button hijau jika anda akan menyetujui, atau button merah untuk menolak.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="btnKonfirmasiSetujuFinanceSKI">
                            <i class="ti ti-check me-1"></i>
                            Setuju
                        </button>
                        <button type="button" class="btn btn-danger" id="btnTolakFinanceSKI">
                            <i class="ti ti-x me-1"></i>
                            Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Alasan Penolakan Finance SKI -->
    <div class="modal fade" id="modalAlasanPenolakan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hasil Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAlasanPenolakan">
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
                    <h5 class="mb-2">Apakah anda yakin menyetujui pengajuan investasi ini?</h5>
                    <p class="mb-0">Silahkan klik button hijau jika anda akan menyetujui, atau button merah untuk menolak.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btnKonfirmasiCEO">
                        Setuju
                    </button>
                    <button type="button" class="btn btn-danger" id="btnTolakCEO">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Alasan Penolakan CEO -->
    <div class="modal fade" id="modalAlasanPenolakanCEO" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hasil Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAlasanPenolakanCEO">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="alasan_penolakan_ceo" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alasan_penolakan_ceo" rows="4" 
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

    <!-- Modal Informasi Rekening -->
    <div class="modal fade" id="modalInformasiRekening" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informasi Rekening</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formInformasiRekening">
                    <div class="modal-body">
                       
                        <div class="mb-3">
                            <label for="informasi_rekening" class="form-label">Informasi Rekening <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="informasi_rekening" rows="4" 
                                placeholder="Masukan Nomor Rekening" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-send me-1"></i>
                            Kirim Informasi
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
        const PENGAJUAN_ID = '{{ $pengajuan->id_pengajuan_investasi_finlog ?? '' }}';
        const CSRF = '{{ csrf_token() }}';

        const alert = (icon, html, title = icon === 'error' ? 'Error!' : icon === 'success' ? 'Berhasil!' : 'Perhatian') => 
            Swal.fire({ 
                icon, 
                title, 
                [icon === 'error' || icon === 'warning' ? 'html' : 'text']: html, 
                ...(icon === 'success' && { timer: 2000, showConfirmButton: false }) 
            });

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

        // Step 2: Validasi Finance SKI - Open Modal
        $('#btnValidasiFinanceSKI').click(function() {
            $('#modalValidasiFinanceSKI').modal('show');
        });

        // Step 2: Konfirmasi Setuju
        $('#btnKonfirmasiSetujuFinanceSKI').click(function() {
            const tanggalInvestasi = $('#tanggal_investasi_validasi').val();
            
            $('#modalValidasiFinanceSKI').modal('hide');
            
            $.ajax({
                url: `/sfinlog/pengajuan-investasi/${PENGAJUAN_ID}/validasi-finance-ski`,
                method: 'POST',
                data: {
                    _token: CSRF,
                    validasi_pengajuan: 'disetujui',
                    tanggal_investasi: tanggalInvestasi
                },
                success: (res) => {
                    if (!res.error) {
                        alert('success', res.message || 'Pengajuan berhasil divalidasi!')
                            .then(() => window.location.reload());
                    } else {
                        alert('error', res.message);
                    }
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat().join('<br>');
                        alert('error', errorList);
                    } else {
                        alert('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
                    }
                }
            });
        });

        // Step 2: Tolak - Open Alasan Modal
        $('#btnTolakFinanceSKI').click(function() {
            $('#modalValidasiFinanceSKI').modal('hide');
            setTimeout(() => $('#modalAlasanPenolakan').modal('show'), 300);
        });

        // Step 2: Submit Alasan Penolakan
        $('#formAlasanPenolakan').submit(function(e) {
            e.preventDefault();
            
            const catatan = $('#alasan_penolakan').val();
            
            if (!catatan || catatan.trim() === '') {
                alert('error', 'Alasan penolakan wajib diisi');
                return;
            }

            $.ajax({
                url: `/sfinlog/pengajuan-investasi/${PENGAJUAN_ID}/validasi-finance-ski`,
                method: 'POST',
                data: {
                    _token: CSRF,
                    validasi_pengajuan: 'ditolak',
                    catatan_penolakan: catatan
                },
                success: (res) => {
                    if (!res.error) {
                        $('#modalAlasanPenolakan').modal('hide');
                        alert('success', res.message || 'Pengajuan telah ditolak')
                            .then(() => window.location.reload());
                    } else {
                        alert('error', res.message);
                    }
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat().join('<br>');
                        alert('error', errorList);
                    } else {
                        alert('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
                    }
                }
            });
        });

        // Step 3: Validasi CEO - Open Modal
        $('#btnValidasiCEO').click(function() {
            $('#modalValidasiCEO').modal('show');
        });

        // Step 3: Konfirmasi Setuju CEO
        $('#btnKonfirmasiCEO').click(function() {
            $('#modalValidasiCEO').modal('hide');
            
            $.ajax({
                url: `/sfinlog/pengajuan-investasi/${PENGAJUAN_ID}/validasi-ceo`,
                method: 'POST',
                data: {
                    _token: CSRF,
                    persetujuan_ceo_finlog: 'disetujui'
                },
                success: (res) => {
                    if (!res.error) {
                        alert('success', res.message || 'Pengajuan berhasil disetujui CEO!')
                            .then(() => window.location.reload());
                    } else {
                        alert('error', res.message);
                    }
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat().join('<br>');
                        alert('error', errorList);
                    } else {
                        alert('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
                    }
                }
            });
        });

        // Step 3: Tolak CEO - Open Alasan Modal
        $('#btnTolakCEO').click(function() {
            $('#modalValidasiCEO').modal('hide');
            setTimeout(() => $('#modalAlasanPenolakanCEO').modal('show'), 300);
        });

        // Step 3: Submit Alasan Penolakan CEO
        $('#formAlasanPenolakanCEO').submit(function(e) {
            e.preventDefault();
            
            const catatan = $('#alasan_penolakan_ceo').val();
            
            if (!catatan || catatan.trim() === '') {
                alert('error', 'Alasan penolakan wajib diisi');
                return;
            }

            $.ajax({
                url: `/sfinlog/pengajuan-investasi/${PENGAJUAN_ID}/validasi-ceo`,
                method: 'POST',
                data: {
                    _token: CSRF,
                    persetujuan_ceo_finlog: 'ditolak',
                    catatan_penolakan: catatan
                },
                success: (res) => {
                    if (!res.error) {
                        $('#modalAlasanPenolakanCEO').modal('hide');
                        alert('success', res.message || 'Pengajuan telah ditolak')
                            .then(() => window.location.reload());
                    } else {
                        alert('error', res.message);
                    }
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat().join('<br>');
                        alert('error', errorList);
                    } else {
                        alert('error', xhr.responseJSON?.message || 'Terjadi kesalahan');
                    }
                }
            });
        });

        // Step 4: Kirim Informasi Rekening - Open Modal
        $('#btnKirimInformasiRekening').click(function() {
            $('#modalInformasiRekening').modal('show');
        });

        // Step 4: Submit Form Informasi Rekening
        $('#formInformasiRekening').submit(function(e) {
            e.preventDefault();
            
            const informasiRekening = $('#informasi_rekening').val();
            
            if (!informasiRekening || informasiRekening.trim() === '') {
                alert('error', 'Informasi rekening wajib diisi');
                return;
            }

            $.ajax({
                url: `/sfinlog/pengajuan-investasi/${PENGAJUAN_ID}/informasi-rekening`,
                method: 'POST',
                data: {
                    _token: CSRF,
                    informasi_rekening: informasiRekening
                },
                success: (res) => {
                    if (!res.error) {
                        $('#modalInformasiRekening').modal('hide');
                        alert('success', res.message || 'Informasi rekening berhasil dikirim!')
                            .then(() => window.location.reload());
                    } else {
                        alert('error', res.message);
                    }
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat().join('<br>');
                        alert('error', errorList);
                    } else {
                        alert('error', xhr.responseJSON?.message || 'Gagal mengirim informasi rekening');
                    }
                }
            });
        });

        // Step 5: Upload Bukti Transfer
        $('#btnUploadBuktiTransfer').click(function() {
            $('#modalUploadBuktiTransfer').modal('show');
        });

        $('#formUploadBuktiTransfer').submit(function(e) {
            e.preventDefault();
            
            const file = $('#file_bukti_transfer')[0].files[0];
            
            if (!file) {
                alert('error', 'Silakan pilih file terlebih dahulu');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('error', 'Ukuran file maksimal 2MB');
                return;
            }

            const formData = new FormData();
            formData.append('upload_bukti_transfer', file);
            formData.append('_token', CSRF);

            $.ajax({
                url: `/sfinlog/pengajuan-investasi/${PENGAJUAN_ID}/upload-bukti`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: (res) => {
                    if (!res.error) {
                        $('#modalUploadBuktiTransfer').modal('hide');
                        alert('success', res.message || 'Bukti transfer berhasil diupload!')
                            .then(() => window.location.reload());
                    } else {
                        alert('error', res.message);
                    }
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat().join('<br>');
                        alert('error', errorList);
                    } else {
                        alert('error', xhr.responseJSON?.message || 'Gagal upload bukti transfer');
                    }
                }
            });
        });

        // Step 6: Generate Kontrak
        $('#btnGenerateKontrak').click(function() {
            $('#modalGenerateKontrak').modal('show');
        });

        $('#formGenerateKontrak').submit(function(e) {
            e.preventDefault();
            
            const nomorKontrak = $('#nomor_kontrak').val();
            
            if (!nomorKontrak) {
                alert('error', 'Nomor kontrak wajib diisi');
                return;
            }

            $.ajax({
                url: `/sfinlog/pengajuan-investasi/${PENGAJUAN_ID}/generate-kontrak`,
                method: 'POST',
                data: {
                    _token: CSRF,
                    nomor_kontrak: nomorKontrak
                },
                success: (res) => {
                    if (!res.error) {
                        $('#modalGenerateKontrak').modal('hide');
                        alert('success', res.message || 'Kontrak berhasil digenerate!')
                            .then(() => window.location.reload());
                    } else {
                        alert('error', res.message);
                    }
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat().join('<br>');
                        alert('error', errorList);
                    } else {
                        alert('error', xhr.responseJSON?.message || 'Gagal generate kontrak');
                    }
                }
            });
        });

        // Preview Bukti Transfer
        window.previewBuktiTransfer = function(url) {
            const fileExt = url.split('.').pop().toLowerCase();
            
            if (fileExt === 'pdf') {
                $('#previewPdf').attr('src', url).removeClass('d-none');
                $('#previewImage').addClass('d-none');
            } else {
                $('#previewImage').attr('src', url).removeClass('d-none');
                $('#previewPdf').addClass('d-none');
            }
            
            $('#downloadBukti').attr('href', url);
            $('#modalPreviewBukti').modal('show');
        };
    });
</script>
@endpush
</div>
