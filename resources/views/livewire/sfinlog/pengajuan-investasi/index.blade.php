<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengajuan Investasi</h4>
                @can('pengajuan_investasi_finlog.add')
                @if($currentInvestor)
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    id="btnTambahPengajuanInvestasi" data-bs-toggle="modal" data-bs-target="#modalPengajuanInvestasi">
                    <i class="fa-solid fa-plus"></i>
                    Pengajuan Investasi
                </button>
                @endif
                @endcan
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <livewire:sfinlog.pengajuan-investasi-finlog-table />
        </div>
    </div>
    
    <div class="modal modal-lg fade" id="modalPengajuanInvestasi">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPengajuanInvestasiLabel">Tambah Pengajuan Investasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="formTambahPengajuanInvestasi" novalidate>
                        <input type="hidden" id="editPengajuanInvestasiId" value="">
                        <input type="hidden" id="id_debitur_dan_investor" value="">
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label for="nama_investor" class="form-label">Nama Investor <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_investor" name="nama_investor"
                                    placeholder="Nama Investor" required readonly>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="id_cells_project" class="form-label">
                                    Pilih Project <span class="text-danger">*</span>
                                </label>
                                <div wire:ignore>
                                    <select id="id_cells_project" name="id_cells_project" class="form-select select2"
                                        data-placeholder="Pilih Project" required>
                                        <option value=""></option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id_cells_project }}">{{ $project->nama_cells_bisnis }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_investasi" class="form-label">Tanggal
                                    Investasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control bs-datepicker" placeholder="yyyy-mm-dd"
                                        id="tanggal_investasi" name="tanggal_investasi" required />
                                    <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lama_investasi" class="form-label">Lama Berinvestasi (Bulan) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="lama_investasi" name="lama_investasi"
                                    placeholder="Masukkan lama berinvestasi" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nominal_investasi" class="form-label">Nominal Investasi <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control input-rupiah" id="nominal_investasi"
                                    name="nominal_investasi" placeholder="Rp 0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="persentase_bagi_hasil" class="form-label">Persentase Bagi Hasil (%)/Tahun <span
                                        class="text-danger">*</span></label>
                                <div wire:ignore>
                                    <select id="persentase_bagi_hasil" name="persentase_bagi_hasil" class="form-select select2"
                                        data-placeholder="Pilih Persentase" required>
                                        <option value=""></option>
                                        <option value="12">12%</option>
                                        <option value="13">13%</option>
                                        <option value="14">14%</option>
                                        <option value="15">15%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="nominal_bagi_hasil_yang_didapat" class="form-label">Nominal Bagi Hasil Yang Didapat
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control input-rupiah non-editable"
                                    id="nominal_bagi_hasil_yang_didapat" placeholder="Rp 0" required disabled readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-none" id="btnHapusPengajuanInvestasi">
                        <i class="ti ti-trash me-1"></i>
                        Hapus Data
                    </button>
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanPengajuanInvestasi">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const $modal = $('#modalPengajuanInvestasi');
        const $form = $('#formTambahPengajuanInvestasi');
        const INVESTOR = { 
            nama: '{{ optional($currentInvestor)->nama ?? '' }}', 
            id: '{{ optional($currentInvestor)->id_debitur ?? '' }}' 
        };
        const CSRF = '{{ csrf_token() }}';
        let cleaveInstances = {};

        const alert = (icon, html, title = icon === 'error' ? 'Error!' : icon === 'success' ? 'Berhasil!' : 'Perhatian') => 
            Swal.fire({ icon, title, [icon === 'error' || icon === 'warning' ? 'html' : 'text']: html, ...(icon === 'success' && { timer: 2000, showConfirmButton: false }) });

        const cleaveConfig = { numeral: true, numeralThousandsGroupStyle: 'thousand', numeralDecimalScale: 0, prefix: 'Rp ', rawValueTrimPrefix: true, noImmediatePrefix: false };
        
        const initCleave = () => ['nominal_investasi', 'nominal_bagi_hasil_yang_didapat'].forEach(id => {
            cleaveInstances[id]?.destroy();
            cleaveInstances[id] = new Cleave(`#${id}`, cleaveConfig);
        });

        const getCleave = (id) => parseInt(cleaveInstances[id]?.getRawValue()) || 0;
        const setCleave = (id, val) => cleaveInstances[id]?.setRawValue(val);

        // Initialize Select2
        $('#id_cells_project, #persentase_bagi_hasil').select2({
            dropdownParent: $modal,
            width: '100%'
        });

        // Initialize Datepicker
        $('.bs-datepicker').datepicker({ 
            format: 'yyyy-mm-dd', 
            todayHighlight: true, 
            autoclose: true, 
            orientation: 'bottom auto',
            startDate: 'today'
        });

        const resetForm = () => {
            $form[0].reset();
            $form.removeClass('was-validated');
            $('#editPengajuanInvestasiId').val('');
            $('#modalTambahPengajuanInvestasiLabel').text('Tambah Pengajuan Investasi');
            $('#btnHapusPengajuanInvestasi').addClass('d-none');
            $('#id_cells_project, #persentase_bagi_hasil').val(null).trigger('change');
            $('.bs-datepicker').datepicker('clearDates');
            ['nominal_investasi', 'nominal_bagi_hasil_yang_didapat'].forEach(id => setCleave(id, 0));
        };

        const calculateBagiHasil = () => {
            const nominal = getCleave('nominal_investasi');
            const persen = parseFloat($('#persentase_bagi_hasil').val()) || 0;
            const lama = parseInt($('#lama_investasi').val()) || 0;
            
            if (nominal > 0 && persen > 0 && lama > 0) {
                const bagiHasil = Math.round((nominal * persen / 100) * (lama / 12));
                setCleave('nominal_bagi_hasil_yang_didapat', bagiHasil);
            } else {
                setCleave('nominal_bagi_hasil_yang_didapat', 0);
            }
        };

        $('#btnTambahPengajuanInvestasi').click(() => {
            if (!INVESTOR.nama || !INVESTOR.id) {
                return alert('warning', 'Anda belum terdaftar sebagai investor.<br>Silakan hubungi admin untuk mendaftar sebagai investor.', 'Data Investor Tidak Ditemukan');
            }
            
            resetForm();
            $('#nama_investor').val(INVESTOR.nama);
            $('#id_debitur_dan_investor').val(INVESTOR.id);
            $modal.modal('show');
            setTimeout(initCleave, 100);
        });

        $modal.on('shown.bs.modal', () => {
            if (!cleaveInstances.nominal_investasi) initCleave();
        }).on('hidden.bs.modal', () => {
            resetForm();
            $('#btnSimpanSpinner').addClass('d-none');
            $('#btnSimpanPengajuanInvestasi').prop('disabled', false);
        });

        // Calculate on input
        $('#nominal_investasi, #persentase_bagi_hasil, #lama_investasi').on('input change', calculateBagiHasil);

        $('#btnSimpanPengajuanInvestasi').click(function() {
            if (!$form[0].checkValidity()) {
                return $form.addClass('was-validated');
            }

            if (!INVESTOR.id) {
                return alert('warning', 'ID Investor tidak tersedia. Silakan refresh halaman atau hubungi admin.', 'Data Investor Tidak Ditemukan');
            }

            const editId = $('#editPengajuanInvestasiId').val();
            const $spinner = $('#btnSimpanSpinner');
            const $btn = $(this);

            const data = {
                id_debitur_dan_investor: INVESTOR.id,
                id_cells_project: $('#id_cells_project').val(),
                nama_investor: $('#nama_investor').val(),
                tanggal_investasi: $('#tanggal_investasi').val(),
                lama_investasi: $('#lama_investasi').val(),
                nominal_investasi: getCleave('nominal_investasi'),
                persentase_bagi_hasil: $('#persentase_bagi_hasil').val(),
                _token: CSRF,
                ...(editId && { _method: 'PUT' })
            };

            $spinner.removeClass('d-none');
            $btn.prop('disabled', true);

            $.ajax({
                url: editId ? `/sfinlog/pengajuan-investasi/${editId}` : '/sfinlog/pengajuan-investasi',
                method: 'POST',
                data: data,
                success: (res) => {
                    if (!res.error) {
                        $modal.modal('hide');
                        Livewire.dispatch('refreshPengajuanInvestasiFinlogTable');
                        alert('success', res.message || (editId ? 'Data berhasil diperbarui' : 'Pengajuan investasi berhasil dibuat dan disubmit!'));
                    } else {
                        alert('error', res.message || 'Terjadi kesalahan');
                    }
                },
                error: (xhr) => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const errorList = Object.values(errors).flat().join('<br>');
                        alert('error', errorList);
                    } else {
                        alert('error', xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data');
                    }
                },
                complete: () => {
                    $spinner.addClass('d-none');
                    $btn.prop('disabled', false);
                }
            });
        });

        // Edit function
        window.editPengajuanInvestasi = function(data) {
            resetForm();
            $('#editPengajuanInvestasiId').val(data.id_pengajuan_investasi_finlog);
            $('#id_debitur_dan_investor').val(data.id_debitur_dan_investor);
            $('#nama_investor').val(data.nama_investor);
            $('#id_cells_project').val(data.id_cells_project).trigger('change');
            $('#tanggal_investasi').datepicker('setDate', data.tanggal_investasi);
            $('#lama_investasi').val(data.lama_investasi);
            $('#persentase_bagi_hasil').val(data.persentase_bagi_hasil).trigger('change');
            
            setTimeout(() => {
                setCleave('nominal_investasi', data.nominal_investasi);
                calculateBagiHasil();
            }, 100);

            $('#modalTambahPengajuanInvestasiLabel').text('Edit Pengajuan Investasi');
            $('#btnHapusPengajuanInvestasi').removeClass('d-none');
            $modal.modal('show');
        };

        // Delete function
        $('#btnHapusPengajuanInvestasi').click(function(e) {
            e.preventDefault();
            const id = $('#editPengajuanInvestasiId').val();
            
            if (!id) return;

            sweetAlertConfirm({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }, () => {
                $.ajax({
                    url: `/sfinlog/pengajuan-investasi/${id}`,
                    method: 'DELETE',
                    data: { _token: CSRF },
                    success: (res) => {
                        if (!res.error) {
                            $modal.modal('hide');
                            Livewire.dispatch('refreshPengajuanInvestasiFinlogTable');
                            alert('success', res.message || 'Data berhasil dihapus');
                        } else {
                            alert('error', res.message);
                        }
                    },
                    error: (xhr) => {
                        alert('error', xhr.responseJSON?.message || 'Gagal menghapus data');
                    }
                });
            });
        });
    });
</script>
@endpush
