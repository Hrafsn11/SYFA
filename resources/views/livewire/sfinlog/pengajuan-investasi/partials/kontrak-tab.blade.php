<div id="content-kontrak">
    @if(!$pengajuan->nomor_kontrak && $pengajuan->current_step >= 5)
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
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nama Investor</label>
                            <input type="text" class="form-control" value="{{ $pengajuan->nama_investor }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nama Perusahaan</label>
                            <input type="text" class="form-control" value="{{ $pengajuan->nama_investor }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Project</label>
                            <input type="text" class="form-control" value="{{ $pengajuan->project->nama_project ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Jumlah Investasi</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($pengajuan->nominal_investasi, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Persentase Bagi Hasil</label>
                            <input type="text" class="form-control" value="{{ number_format($pengajuan->persentase_bagi_hasil, 2) }}%" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Lama Investasi</label>
                            <input type="text" class="form-control" value="{{ $pengajuan->lama_investasi }} Bulan" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tanggal Investasi</label>
                            <input type="text" class="form-control" value="{{ $pengajuan->tanggal_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_investasi)->format('d F Y') : '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Tanggal Jatuh Tempo</label>
                            <input type="text" class="form-control" value="{{ $pengajuan->tanggal_berakhir_investasi ? \Carbon\Carbon::parse($pengajuan->tanggal_berakhir_investasi)->format('d F Y') : '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Alamat</label>
                            <textarea class="form-control" rows="2" readonly>{{ $pengajuan->investor->alamat ?? '-' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="nomorKontrakFinlog" class="form-label">Nomor Kontrak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomorKontrakFinlog" name="nomor_kontrak" placeholder="Contoh: 001/SKI/INV/2025" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-primary" id="btnPreviewKontrakFinlog">
                            <i class="ti ti-eye me-2"></i>
                            Preview Kontrak
                        </button>
                        <button type="submit" class="btn btn-success" id="btnGenerateKontrakFinlog">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="btnGenerateKontrakFinlogSpinner"></span>
                            <i class="ti ti-file-check me-2"></i>
                            Generate Kontrak
                        </button>
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
    $(document).ready(function() {
        // Preview Kontrak Finlog
        $('#btnPreviewKontrakFinlog').click(function() {
            const nomorKontrak = $('#nomorKontrakFinlog').val();
            
            if (!nomorKontrak) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Nomor kontrak wajib diisi untuk preview'
                });
                return;
            }

            // Open preview in new tab
            const pengajuanId = '{{ $pengajuan->id_pengajuan_investasi_finlog }}';
            const previewUrl = `/sfinlog/pengajuan-investasi/${pengajuanId}/preview-kontrak?nomor_kontrak=${encodeURIComponent(nomorKontrak)}`;
            window.open(previewUrl, '_blank');
        });

        // Generate Kontrak Finlog
        $('#formGenerateKontrakFinlog').submit(function(e) {
            e.preventDefault();
            
            const nomorKontrak = $('#nomorKontrakFinlog').val();
            
            if (!nomorKontrak) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Nomor kontrak wajib diisi'
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
                    nomor_kontrak: nomorKontrak
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
        window.previewBuktiTransfer = function(url) {
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