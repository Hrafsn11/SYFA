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

                    <div class="stepper-item {{ $currentStep >= 1 ? 'completed' : '' }} {{ $currentStep == 1 ? 'active' : '' }}"
                        data-step="1">
                        <div class="stepper-node">
                        </div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 1</div>
                            <div class="step-name">Pengajuan Investasi</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 2 ? 'completed' : '' }} {{ $currentStep == 2 ? 'active' : '' }}"
                        data-step="2">
                        <div class="stepper-node">
                        </div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 2</div>
                            <div class="step-name">Validasi Pengajuan</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 3 ? 'completed' : '' }} {{ $currentStep == 3 ? 'active' : '' }}"
                        data-step="3">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 3</div>
                            <div class="step-name">Persetujuan CEO Finlog</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 4 ? 'completed' : '' }} {{ $currentStep == 4 ? 'active' : '' }}"
                        data-step="4">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 4</div>
                            <div class="step-name">Upload Bukti Transfer</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 5 ? 'completed' : '' }} {{ $currentStep == 5 ? 'active' : '' }}"
                        data-step="5">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 5</div>
                            <div class="step-name">Generate Kontrak</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 6 ? 'completed' : '' }} {{ $currentStep == 6 ? 'active' : '' }}"
                        data-step="6">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 6</div>
                            <div class="step-name">Selesai</div>
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
                                    @if ($pengajuan->status_approval !== 'Selesai')
                                        <li class="nav-item">
                                            <button type="button" class="nav-link" data-bs-toggle="tab"
                                                data-bs-target="#detail-kontrak" role="tab" aria-selected="false">
                                                <i class="ti ti-report-money me-2"></i>
                                                <span class="d-none d-sm-inline">Detail Kontrak</span>
                                            </button>
                                        </li>
                                    @endif
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
                                @if ($pengajuan->status_approval !== 'Selesai')
                                    <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                                        @include('livewire.sfinlog.pengajuan-investasi.partials.kontrak-tab')
                                    </div>
                                @endif

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
                                <input type="text" class="form-control bs-datepicker"
                                    id="tanggal_investasi_validasi"
                                    value="{{ $pengajuan->tanggal_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_investasi)->format('Y-m-d') : '' }}"
                                    placeholder="yyyy-mm-dd">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                            <small class="text-muted">Anda dapat mengubah tanggal investasi atau membiarkan seperti
                                semula</small>
                        </div>

                        <div class="mb-3">
                            <label for="catatan_validasi_finance" class="form-label">Catatan Validasi <small class="text-muted">(Opsional)</small></label>
                            <textarea class="form-control" id="catatan_validasi_finance" rows="3" placeholder="Masukkan catatan jika ada..."></textarea>
                        </div>

                        <p class="mb-0 text-muted">Silahkan klik button hijau jika anda akan menyetujui, atau button
                            merah untuk menolak.</p>
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
                            <label for="alasan_penolakan" class="form-label">Alasan Penolakan <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="alasan_penolakan" rows="4" placeholder="Masukkan alasan penolakan..."
                                required></textarea>
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
                    <h5 class="mb-3">Apakah anda yakin menyetujui pengajuan investasi ini?</h5>
                    
                    <div class="mb-3">
                        <label for="catatan_validasi_ceo" class="form-label">Catatan Persetujuan <small class="text-muted">(Opsional)</small></label>
                        <textarea class="form-control" id="catatan_validasi_ceo" rows="3" placeholder="Masukkan catatan jika ada..."></textarea>
                        <small class="text-muted">Catatan ini akan ditampilkan di activity log</small>
                    </div>
                    
                    <p class="mb-0 text-muted">Silahkan klik button hijau jika anda akan menyetujui, atau button merah untuk
                        menolak.</p>
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
                            <label for="alasan_penolakan_ceo" class="form-label">Alasan Penolakan <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="alasan_penolakan_ceo" rows="4" placeholder="Masukkan alasan penolakan..."
                                required></textarea>
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
                            <label for="nomor_kontrak" class="form-label">Nomor Kontrak <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomor_kontrak"
                                placeholder="FINLOG/INV/2025/001" required>
                            <small class="text-muted">Format: FINLOG/INV/TAHUN/NOMOR</small>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_kontrak" class="form-label">Tanggal Kontrak <span
                                    class="text-danger">*</span></label>
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
                        <div class="alert alert-info mb-3">
                            <h6 class="alert-heading mb-2"><i class="ti ti-info-circle me-2"></i>Informasi Rekening
                                Transfer</h6>
                            <p class="mb-1"><strong>Nama:</strong> PT. Synnovac Kapital Indonesia</p>
                            <p class="mb-1"><strong>No. Rekening:</strong> 1240012977113</p>
                            <p class="mb-0"><strong>Bank:</strong> Bank Mandiri</p>
                        </div>
                        <div class="mb-3">
                            <label for="file_bukti_transfer" class="form-label">File Bukti Transfer <span
                                    class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file_bukti_transfer"
                                accept="image/*,.pdf" required>
                            <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan_transfer" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan_transfer" rows="3" placeholder="Masukkan keterangan (opsional)"></textarea>
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
                    <iframe id="previewPdf" src="" class="d-none"
                        style="width:100%; height:500px;"></iframe>
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
            (function() {
                'use strict';

                const CFG = {
                    id: '{{ $pengajuan->id_pengajuan_investasi_finlog ?? '' }}',
                    csrf: '{{ csrf_token() }}',
                    base: '/sfinlog/pengajuan-investasi',
                    maxSize: 2097152
                };
                const url = e => `${CFG.base}/${CFG.id}${e}`;
                const alert = (i, m) => Swal.fire({
                    icon: i,
                    title: i === 'error' ? 'Error!' : 'Berhasil!',
                    [i === 'error' ? 'html' : 'text']: m,
                    ...(i === 'success' && {
                        timer: 2000,
                        showConfirmButton: false
                    })
                });
                const modal = (id, hide, delay = 0) => delay ? setTimeout(() => $(`#${id}`).modal(hide ? 'hide' : 'show'),
                    delay) : $(`#${id}`).modal(hide ? 'hide' : 'show');
                const reload = () => location.reload();
                const errMsg = xhr => Object.values(xhr.responseJSON?.errors || {}).flat().join('<br>') || xhr.responseJSON
                    ?.message || 'Terjadi kesalahan';

                const ajax = {
                    post: (u, d, o = {}) => $.ajax({
                        url: u,
                        method: 'POST',
                        data: {
                            _token: CFG.csrf,
                            ...d
                        },
                        ...o
                    }),
                    respond: (r, m, id) => {
                        // Close modal first if provided
                        if (id) {
                            modal(id, true);
                        }
                        // Handle response
                        if (r.error) {
                            alert('error', r.message);
                        } else {
                            alert('success', r.message || m).then(() => {
                                // Refresh Livewire component instead of full reload
                                Livewire.dispatch('refreshData');
                                setTimeout(() => location.reload(), 500);
                            });
                        }
                    }
                };

                const approval = {
                    submit: (d, m, modalId) => ajax.post(url('/approval'), d)
                        .done(r => {
                            ajax.respond(r, m, modalId);
                        })
                        .fail(x => {
                            alert('error', errMsg(x));
                        }),
                    approve: (s, v, modalId, a = {}) => approval.submit({
                        status: s,
                        [v]: 'disetujui',
                        ...a
                    }, 'Pengajuan berhasil divalidasi!', modalId),
                    reject: (s, v, c, modalId) => c?.trim() ? approval.submit({
                        status: s,
                        [v]: 'ditolak',
                        catatan_penolakan: c
                    }, 'Pengajuan telah ditolak', modalId) : alert('error',
                        'Alasan penolakan wajib diisi')
                };

                const file = {
                    validate: f => !f ? (alert('error', 'Pilih file terlebih dahulu'), false) : f.size >
                        CFG.maxSize ? (
                            alert('error', 'Ukuran file maksimal 2MB'), false) : true,
                    upload: (e, f, n, m, id) => {
                        const fd = new FormData();
                        fd.append(n, f);
                        fd.append('_token', CFG.csrf);
                        $.ajax({
                                url: url(e),
                                method: 'POST',
                                data: fd,
                                processData: false,
                                contentType: false,
                                headers: {
                                    'X-CSRF-TOKEN': CFG.csrf
                                }
                            })
                            .done(r => ajax.respond(r, m, id)).fail(x => alert('error', errMsg(x)));
                    }
                };

                $(document).ready(() => {
                    const initDatepicker = (selector) => {
                        $(selector).datepicker('destroy').datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose: true,
                            todayHighlight: true,
                            orientation: 'bottom auto'
                        }).on('changeDate', function(e) {
                            if (e.date) {
                                const y = e.date.getFullYear();
                                const m = String(e.date.getMonth() + 1).padStart(2, '0');
                                const d = String(e.date.getDate()).padStart(2, '0');
                                $(this).val(`${y}-${m}-${d}`);
                            }
                        });
                    };

                    initDatepicker('.bs-datepicker');
                    $('#modalValidasiFinanceSKI, #modalGenerateKontrak').on('shown.bs.modal', () =>
                        initDatepicker(
                            '.bs-datepicker'));

                    $('#btnSubmitPengajuan').click(() => {
                        sweetAlertConfirm({
                            title: 'Konfirmasi Submit',
                            text: 'Apakah Anda yakin ingin submit pengajuan ini? Setelah submit, data akan divalidasi oleh Finance SKI.',
                            icon: 'question',
                            confirmButtonText: 'Ya, Submit',
                            cancelButtonText: 'Batal',
                        }, () => {
                            approval.approve('Submit Pengajuan', 'submit_step1');
                        });
                    });

                    // Finance SKI
                    $('#btnValidasiFinanceSKI').click(() => modal('modalValidasiFinanceSKI'));
                    $('#btnKonfirmasiSetujuFinanceSKI').click(() => {
                        const tanggalInvestasi = $('#tanggal_investasi_validasi').val();
                        const catatan = $('#catatan_validasi_finance').val();
                        approval.approve('Dokumen Tervalidasi', 'validasi_pengajuan',
                            'modalValidasiFinanceSKI', {
                                tanggal_investasi: tanggalInvestasi,
                                catatan: catatan
                            });
                    });
                    $('#btnTolakFinanceSKI').click(() => (modal('modalValidasiFinanceSKI', true), modal(
                        'modalAlasanPenolakan', false, 300)));
                    $('#formAlasanPenolakan').submit(e => {
                        e.preventDefault();
                        approval.reject('Ditolak Finance SKI', 'validasi_pengajuan', $(
                                '#alasan_penolakan')
                            .val(), 'modalAlasanPenolakan');
                    });

                    // CEO
                    $('#btnValidasiCEO').click(() => modal('modalValidasiCEO'));
                    $('#btnKonfirmasiCEO').click(() => {
                        const catatan = $('#catatan_validasi_ceo').val();
                        approval.approve('Disetujui CEO Finlog', 'persetujuan_ceo_finlog',
                            'modalValidasiCEO', {
                                catatan: catatan
                            });
                    });
                    $('#btnTolakCEO').click(() => (modal('modalValidasiCEO', true), modal(
                        'modalAlasanPenolakanCEO',
                        false, 300)));
                    $('#formAlasanPenolakanCEO').submit(e => {
                        e.preventDefault();
                        approval.reject('Ditolak CEO Finlog', 'persetujuan_ceo_finlog', $(
                            '#alasan_penolakan_ceo').val(), 'modalAlasanPenolakanCEO');
                    });

                    // Upload Bukti
                    $('#btnUploadBuktiTransfer').click(() => modal('modalUploadBuktiTransfer'));
                    $('#formUploadBuktiTransfer').submit(e => {
                        e.preventDefault();
                        const f = $('#file_bukti_transfer')[0].files[0];
                        file.validate(f) && file.upload('/upload-bukti', f, 'file',
                            'Bukti transfer berhasil diupload!', 'modalUploadBuktiTransfer');
                    });

                    // Generate Kontrak
                    $('#btnGenerateKontrak').click(() => modal('modalGenerateKontrak'));
                    $('#formGenerateKontrak').submit(e => {
                        e.preventDefault();
                        const n = $('#nomor_kontrak').val();
                        const t = $('#tanggal_kontrak').val();
                        n ? ajax.post(url('/generate-kontrak'), {
                                nomor_kontrak: n,
                                tanggal_kontrak: t
                            }).done(r => ajax.respond(r, 'Kontrak berhasil digenerate!',
                                'modalGenerateKontrak')).fail(x => alert('error', errMsg(x))) :
                            alert(
                                'error', 'Nomor kontrak wajib diisi');
                    });

                    // Preview
                    window.previewBuktiTransfer = u => {
                        const p = u.split('.').pop().toLowerCase() === 'pdf';
                        $('#previewPdf').attr('src', p ? u : '').toggleClass('d-none', !p);
                        $('#previewImage').attr('src', p ? '' : u).toggleClass('d-none', p);
                        $('#downloadBukti').attr('href', u);
                        modal('modalPreviewBukti');
                    };
                });
            })();
        </script>
    @endpush
</div>
