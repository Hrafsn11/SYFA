<div id="content-kontrak">
    @if (!$pengajuan->nomor_kontrak && $pengajuan->current_step >= 5)
        <!-- Generate Kontrak Form (Step 5) -->
        <h5 class="mb-4 mt-3">Generate Kontrak Investasi Deposito</h5>

        <!-- Data Kontrak -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Data Kontrak</h6>
            </div>
            <div class="card-body">
                <form id="formGenerateKontrakFinlog">
                    <div class="row g-3">
                        {{-- Nama PIC - Field pertama (editable) --}}
                        <div class="col-md-6">
                            <label for="namaPicKontrakFinlog" class="form-label">Nama PIC/CEO Investor <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namaPicKontrakFinlog" name="nama_pic_kontrak"
                                value="{{ $pengajuan->nama_pic_kontrak ?? '' }}"
                                placeholder="Masukkan nama PIC/CEO investor" 
                                @if(!empty($pengajuan->nomor_kontrak))
                                    readonly
                                @elseif(!auth()->user()->can('pengajuan_investasi_finlog.generate_kontrak'))
                                    disabled
                                @else
                                    required
                                @endif
                            >
                            <small class="text-muted">Nama ini akan digunakan dalam kontrak investasi</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nama Perusahaan</label>
                            <input type="text" class="form-control" value="{{ $pengajuan->nama_investor }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Project</label>
                            <input type="text" class="form-control"
                                value="{{ $pengajuan->project->nama_cells_bisnis ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Jumlah Investasi</label>
                            <input type="text" class="form-control"
                                value="Rp {{ number_format($pengajuan->nominal_investasi, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Persentase Bagi Hasil</label>
                            <input type="text" class="form-control"
                                value="{{ number_format($pengajuan->persentase_bagi_hasil, 2) }}%" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Lama Investasi</label>
                            <input type="text" class="form-control" value="{{ $pengajuan->lama_investasi }} Bulan" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tanggal Investasi</label>
                            <input type="text" class="form-control"
                                value="{{ $pengajuan->tanggal_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_investasi)->format('d F Y') : '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tanggal Jatuh Tempo</label>
                            <input type="text" class="form-control"
                                value="{{ $pengajuan->tanggal_berakhir_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_berakhir_investasi)->format('d F Y') : '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Alamat</label>
                            <textarea class="form-control" rows="2"
                                readonly>{{ $pengajuan->investor->alamat ?? '-' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="nomorKontrakFinlog" class="form-label">Nomor Kontrak</label>
                            @if(!empty($pengajuan->nomor_kontrak))
                                <input type="text" class="form-control" value="{{ $pengajuan->nomor_kontrak }}" readonly>
                                <div class="form-text text-success">
                                    <i class="ti ti-check-circle me-1"></i>Nomor kontrak sudah di-generate
                                </div>
                            @elseif(!empty($pengajuan->preview_nomor_kontrak))
                                <input type="text" class="form-control bg-light" id="nomorKontrakFinlog"
                                    value="{{ $pengajuan->preview_nomor_kontrak }}" readonly>
                                <div class="form-text text-warning">
                                    <i class="ti ti-alert-circle me-1"></i>Preview nomor kontrak (belum tersimpan)
                                </div>
                            @elseif(!empty($pengajuan->kode_perusahaan_missing))
                                <input type="text" class="form-control bg-light" id="nomorKontrakFinlog"
                                    value="Kode perusahaan investor belum diisi" readonly>
                                <div class="form-text text-danger">
                                    <i class="ti ti-alert-triangle me-1"></i>Hubungi admin untuk mengisi kode perusahaan
                                    investor terlebih dahulu
                                </div>
                            @else
                                <input type="text" class="form-control bg-light" id="nomorKontrakFinlog"
                                    value="Menunggu approval CEO" readonly>
                                <div class="form-text text-muted">
                                    <i class="ti ti-info-circle me-1"></i>Nomor kontrak akan muncul setelah disetujui CEO
                                </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Nomor kontrak akan di-generate otomatis</strong> berdasarkan kode perusahaan
                                investor dan tanggal investasi.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        @if(!empty($pengajuan->nomor_kontrak))
                            @can('pengajuan_investasi_finlog.generate_kontrak')
                                <a href="{{ route('sfinlog.pengajuan-investasi.preview-kontrak', ['id' => $pengajuan->id_pengajuan_investasi_finlog]) }}"
                                    class="btn btn-outline-primary" target="_blank">
                                    <i class="ti ti-eye me-2"></i>
                                    Preview Kontrak
                                </a>
                            @endcan
                        @elseif(!empty($pengajuan->preview_nomor_kontrak))
                            @can('pengajuan_investasi_finlog.generate_kontrak')
                                <button type="button" class="btn btn-outline-primary" id="btnPreviewKontrakFinlog">
                                    <i class="ti ti-eye me-2"></i>
                                    Preview Kontrak
                                </button>
                                <button type="submit" class="btn btn-success" id="btnGenerateKontrakFinlog">
                                    <span class="spinner-border spinner-border-sm me-2 d-none"
                                        id="btnGenerateKontrakFinlogSpinner"></span>
                                    <i class="ti ti-file-check me-2"></i>
                                    Generate Kontrak
                                </button>
                            @endcan
                        @elseif(!empty($pengajuan->kode_perusahaan_missing))
                            <button type="button" class="btn btn-secondary" disabled>
                                <i class="ti ti-alert-triangle me-2"></i>
                                Kode Perusahaan Belum Diisi
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- Konten Default (Before Step 5) -->
        <div id="kontrak-default">
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="ti ti-file-text display-4 text-muted"></i>
                </div>
                <h5 class="text-muted mb-2">Kontrak Belum Tersedia</h5>
                <p class="text-muted mb-0">
                    Kontrak akan tersedia setelah dana dicairkan.
                </p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            // Preview Kontrak Finlog
            $('#btnPreviewKontrakFinlog').click(function () {
                const namaPic = $('#namaPicKontrakFinlog').val();

                if (!namaPic) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Nama PIC/CEO harus diisi untuk preview'
                    });
                    return;
                }

                // Open preview in new tab (will use auto-generated contract number)
                const pengajuanId = '{{ $pengajuan->id_pengajuan_investasi_finlog }}';
                const previewUrl =
                    `/sfinlog/pengajuan-investasi/${pengajuanId}/preview-kontrak`;
                window.open(previewUrl, '_blank');
            });

            // Generate Kontrak Finlog
            $('#formGenerateKontrakFinlog').submit(function (e) {
                e.preventDefault();

                const namaPic = $('#namaPicKontrakFinlog').val();

                if (!namaPic) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Nama PIC/CEO harus diisi'
                    });
                    return;
                }

                const btnSubmit = $('#btnGenerateKontrakFinlog');
                const spinner = $('#btnGenerateKontrakFinlogSpinner');

                btnSubmit.prop('disabled', true);
                spinner.removeClass('d-none');

                const pengajuanId = '{{ $pengajuan->id_pengajuan_investasi_finlog }}';

                $.ajax({
                    url: `/sfinlog/pengajuan-investasi/${pengajuanId}/generate-kontrak`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nama_pic_kontrak: namaPic
                    },
                    success: (res) => {
                        if (!res.error) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Kontrak berhasil digenerate!'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.message
                            });
                        }
                    },
                    error: (xhr) => {
                        const errors = xhr.responseJSON?.errors;
                        let errorMessage = 'Gagal generate kontrak';

                        if (errors) {
                            errorMessage = Object.values(errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage
                        });
                    },
                    complete: () => {
                        btnSubmit.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });

            // Preview Bukti Transfer
            window.previewBuktiTransfer = function (url) {
                const fileExt = url.split('.').pop().toLowerCase();
                let content = '';

                if (fileExt === 'pdf') {
                    content = `<iframe src="${url}" width="100%" height="500px" style="border:none;"></iframe>`;
                } else {
                    content = `<img src="${url}" class="img-fluid" alt="Bukti Transfer">`;
                }

                Swal.fire({
                    title: 'Bukti Transfer',
                    html: content,
                    width: '80%',
                    showCloseButton: true,
                    showConfirmButton: false
                });
            };
        });
    </script>
@endpush