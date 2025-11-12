@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Pengajuan Investasi</h4>
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        id="btnTambahFormKerjaInvestor">
                        <i class="fa-solid fa-plus"></i>
                        Pengajuan Investasi
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive">
                <livewire:pengajuan-investasi-table />
            </div>
        </div>

        <div class="modal modal-lg fade" id="modalFormKerjaInvestor" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahFormKerjaInvestorLabel">Tambah Pengajuan Investasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
                            <i class="fas fa-info-circle me-2"></i>
                            Deposito yang masuk setelah tanggal 20, untuk bagi hasil akan dihitung di bulan selanjutnya
                        </div>
                        <form id="formTambahFormKerjaInvestor" novalidate>
                            <input type="hidden" id="editFormKerjaInvestorId" value="">
                            <input type="hidden" id="id_debitur_dan_investor" value="{{ optional($investor)->id_debitur ?? '' }}">
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label for="nama_investor" class="form-label">Nama Investor <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_investor"
                                        name="nama_investor" placeholder="Nama Investor" required readonly>
                                </div>
                                <div class="col-12 mb-3" id="div-deposito">
                                    <label class="form-label">Deposito <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deposito"
                                                id="deposito_reguler" value="reguler" required disabled>
                                            <label class="form-check-label" for="deposito_reguler">
                                                Reguler
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deposito"
                                                id="deposito_khusus" value="khusus" required disabled>
                                            <label class="form-check-label" for="deposito_khusus">
                                                Khusus
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bs-datepicker-tanggal-pembayaran" class="form-label">Tanggal
                                        Investasi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="yyyy-mm-dd"
                                            id="bs-datepicker-tanggal-pembayaran" name="tanggal_pembayaran" required />
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lama-investasi" class="form-label">Lama Berinvestasi (Bulan) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="lama_investasi"
                                        placeholder="Masukkan lama berinvestasi" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah_investasi" class="form-label">Jumlah Investasi <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control input-rupiah" id="jumlah_investasi"
                                        name="jumlah_investasi" placeholder="Rp 0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bagi_hasil" class="form-label">Bagi Hasil (%)/Tahun <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="bagi_hasil" name="bagi_hasil_pertahun"
                                        placeholder="Masukan bagi hasil" min="0" max="100" step="0.01"
                                        required>
                                    <small class="text-muted d-none" id="bagi-hasil-hint">
                                        <i class="ti ti-info-circle"></i> Minimum 7% untuk deposito khusus
                                    </small>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label for="bagi_hasil_keseluruhan" class="form-label">Nominal Bagi Hasil Yang Didapat
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control input-rupiah non-editable" id="bagi_hasil_keseluruhan"
                                        placeholder="Rp 0" required disabled readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary d-none" id="btnHapusFormKerjaInvestor">
                            <i class="ti ti-trash me-1"></i>
                            Hapus Data
                        </button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnSimpanFormKerjaInvestor">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modalConfirmDeleteInvestor" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteInvestor">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="btnDeleteSpinner"></span>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            const $modal = $('#modalFormKerjaInvestor');
            const $modalDelete = $('#modalConfirmDeleteInvestor');
            const $form = $('#formTambahFormKerjaInvestor');
            const $btnSimpan = $('#btnSimpanFormKerjaInvestor');
            const $spinner = $('#btnSimpanSpinner');
            const namaInvestor = '{{ optional($investor)->nama ?? '' }}';
            const depositoInvestor = '{{ optional($investor)->deposito ?? '' }}';
            let deleteInvestorId = null;

            let cleaveInstances = {
                jumlah_investasi: null,
                bagi_hasil_keseluruhan: null
            };

            const showError = (message, title = 'Error!') => {
                Swal.fire({ icon: 'error', title, html: message });
            };

            const showSuccess = (message, title = 'Berhasil!', timer = 2000) => {
                Swal.fire({ icon: 'success', title, text: message, timer, showConfirmButton: false });
            };

            const showWarning = (html, title = 'Perhatian') => {
                Swal.fire({ icon: 'warning', title, html, confirmButtonText: 'OK' });
            };

            const setLoadingState = (loading) => {
                $spinner.toggleClass('d-none', !loading);
                $btnSimpan.prop('disabled', loading);
            };

            const cleaveConfig = {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalScale: 0,
                prefix: 'Rp ',
                rawValueTrimPrefix: true,
                noImmediatePrefix: false
            };

            const initCleave = () => {
                Object.keys(cleaveInstances).forEach(key => {
                    cleaveInstances[key]?.destroy();
                    cleaveInstances[key] = new Cleave(`#${key}`, cleaveConfig);
                });
            };

            const getCleaveValue = (fieldName) => {
                return cleaveInstances[fieldName] ? parseInt(cleaveInstances[fieldName].getRawValue()) || 0 : 0;
            };

            const setCleaveValue = (fieldName, value) => {
                cleaveInstances[fieldName]?.setRawValue(value);
            };

            $('#bs-datepicker-tanggal-pembayaran').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
                orientation: 'bottom auto',
                startDate: 'today'
            });

            const resetForm = () => {
                $form[0].reset();
                $form.removeClass('was-validated');
                $('#editFormKerjaInvestorId').val('');
                $('#modalTambahFormKerjaInvestorLabel').text('Tambah Pengajuan Investasi');
                $('#btnHapusFormKerjaInvestor').addClass('d-none');
                setCleaveValue('jumlah_investasi', 0);
                setCleaveValue('bagi_hasil_keseluruhan', 0);
            };

            const validateInvestorData = () => {
                if (!namaInvestor || !depositoInvestor) {
                    showWarning('Anda belum terdaftar sebagai investor.<br>Silakan hubungi admin untuk mendaftar sebagai investor.', 'Data Investor Tidak Ditemukan');
                    return false;
                }
                return true;
            };

            const setDepositoMode = (deposito) => {
                const isReguler = deposito === 'reguler';
                $('#bagi_hasil')
                    .val(isReguler ? '10' : '')
                    .prop('readonly', isReguler)
                    .prop('disabled', isReguler)
                    .toggleClass('non-editable', isReguler);

                $('#bagi-hasil-hint').toggleClass('d-none', isReguler);
                $('#bagi_hasil_keseluruhan').prop('disabled', true).prop('readonly', true).addClass('non-editable');
                calculateBagiHasil();
            };

            const calculateBagiHasil = () => {
                const jumlah = getCleaveValue('jumlah_investasi');
                const persen = parseFloat($('#bagi_hasil').val()) || 0;
                const lama = parseInt($('#lama_investasi').val()) || 0;

                const nominal = (jumlah > 0 && persen > 0 && lama > 0) 
                    ? Math.round((jumlah * persen / 100) / 12 * lama) 
                    : 0;
                setCleaveValue('bagi_hasil_keseluruhan', nominal);
            };

            $('#btnTambahFormKerjaInvestor').on('click', function() {
                if (!validateInvestorData()) return;

                resetForm();

                $('#nama_investor').val(namaInvestor);
                $('#id_debitur_dan_investor').val('{{ optional($investor)->id_debitur ?? '' }}');
                $('input[name="deposito"]').prop('checked', false).prop('disabled', true);

                const depositoValue = depositoInvestor.toLowerCase();
                $(`input[name="deposito"][value="${depositoValue}"]`).prop('checked', true);
                setDepositoMode(depositoValue);

                $modal.modal('show');
                setTimeout(initCleave, 100);
            });

            $modal.on('shown.bs.modal', function() {
                if (!cleaveInstances.jumlah_investasi) {
                    initCleave();
                }
            });

            $modal.on('hidden.bs.modal', function() {
                resetForm();
                setLoadingState(false);
            });

            // Auto-calculate saat input berubah
            $('#jumlah_investasi').on('input', calculateBagiHasil);
            $('#bagi_hasil').on('input', calculateBagiHasil);
            $('#lama_investasi').on('input', calculateBagiHasil);

            $('#btnHapusFormKerjaInvestor').on('click', function(e) {
                e.preventDefault();
                const id = $('#editFormKerjaInvestorId').val();
                if (!id) return;

                deleteInvestorId = id;
                $modal.modal('hide');
                $modalDelete.modal('show');
            });

            $('#btnConfirmDeleteInvestor').on('click', function() {
                if (!deleteInvestorId) return;

                $('#btnDeleteSpinner').removeClass('d-none');
                $(this).prop('disabled', true);

                $.ajax({
                    url: `/pengajuan-investasi/${deleteInvestorId}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: (response) => {
                        if (!response.error) {
                            $modalDelete.modal('hide');
                            Livewire.dispatch('refreshPengajuanInvestasiTable');
                            showSuccess(response.message || 'Data berhasil dihapus');
                            deleteInvestorId = null;
                        }
                    },
                    complete: () => {
                        $('#btnDeleteSpinner').addClass('d-none');
                        $('#btnConfirmDeleteInvestor').prop('disabled', false);
                    }
                });
            });

            $modalDelete.on('hidden.bs.modal', function() {
                deleteInvestorId = null;
            });

            $btnSimpan.on('click', function() {
                if (!$form[0].checkValidity()) {
                    $form.addClass('was-validated');
                    return;
                }

                if (!$('input[name="deposito"]:checked').val()) {
                    showWarning('Pilih jenis deposito terlebih dahulu');
                    return;
                }

                const idDebitur = $('#id_debitur_dan_investor').val();
                if (!idDebitur) {
                    showWarning('ID Investor tidak tersedia. Silakan refresh halaman atau hubungi admin.', 'Data Investor Tidak Ditemukan');
                    return;
                }

                const editId = $('#editFormKerjaInvestorId').val();
                const isEdit = !!editId;
                const depositoValue = $('input[name="deposito"]:checked').val();

                $('input[name="deposito"]').prop('disabled', false);

                const formData = {
                    id_debitur_dan_investor: idDebitur,
                    nama_investor: $('#nama_investor').val(),
                    deposito: depositoValue.charAt(0).toUpperCase() + depositoValue.slice(1),
                    tanggal_investasi: $('#bs-datepicker-tanggal-pembayaran').val(),
                    lama_investasi: $('#lama_investasi').val(),
                    jumlah_investasi: getCleaveValue('jumlah_investasi'),
                    bagi_hasil_pertahun: $('#bagi_hasil').val(),
                    _token: '{{ csrf_token() }}'
                };

                if (isEdit) formData._method = 'PUT';
                $('input[name="deposito"]').prop('disabled', true);
                setLoadingState(true);

                $.ajax({
                    url: isEdit ? `/pengajuan-investasi/${editId}` : '{{ route('pengajuan-investasi.store') }}',
                    method: 'POST',
                    data: formData,
                    success: (response) => {
                        if (!response.error) {
                            $modal.modal('hide');
                            Livewire.dispatch('refreshPengajuanInvestasiTable');
                            showSuccess(response.message || (isEdit ? 'Data berhasil diupdate' : 'Data berhasil ditambahkan'));
                        }
                    },
                    error: (xhr) => {
                        const errorMessage = xhr.responseJSON?.errors 
                            ? Object.values(xhr.responseJSON.errors).flat().join('<br>')
                            : xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                        showError(errorMessage, 'Gagal!');
                    },
                    complete: () => setLoadingState(false)
                });
            });

            $(document).on('click', '.investor-detail-btn', (e) => {
                e.preventDefault();
                const id = $(e.currentTarget).data('id');
                id ? window.location.href = `/pengajuan-investasi/${id}` : showError('ID tidak ditemukan');
            });

            $(document).on('click', '.investor-edit-btn', (e) => {
                e.preventDefault();
                const id = $(e.currentTarget).data('id');
                
                if (!id) {
                    showError('ID tidak ditemukan');
                    return;
                }

                $.ajax({
                    url: `/pengajuan-investasi/${id}/edit`,
                    method: 'GET',
                    success: (response) => {
                        if (!response.error && response.data) {
                            const d = response.data;
                            resetForm();

                            // Set form values
                            $('#editFormKerjaInvestorId').val(d.id_pengajuan_investasi);
                            $('#id_debitur_dan_investor').val(d.id_debitur_dan_investor);
                            $('#modalTambahFormKerjaInvestorLabel').text('Edit Pengajuan Investasi');
                            $('#btnHapusFormKerjaInvestor').removeClass('d-none');
                            $('#nama_investor').val(d.nama_investor);
                            $('#lama_investasi').val(d.lama_investasi);
                            $('#bagi_hasil').val(d.bagi_hasil_pertahun);

                            // Set deposito
                            $('input[name="deposito"]').prop('checked', false).prop('disabled', true);
                            if (d.deposito) {
                                const depositoValue = d.deposito.toLowerCase();
                                $(`input[name="deposito"][value="${depositoValue}"]`).prop('checked', true);
                                setDepositoMode(depositoValue);
                            }

                            // Set date
                            if (d.tanggal_investasi) {
                                $('#bs-datepicker-tanggal-pembayaran').datepicker('setDate', d.tanggal_investasi);
                            }

                            $modal.modal('show');

                            // Init Cleave & set values after modal shown
                            setTimeout(() => {
                                initCleave();
                                setTimeout(() => {
                                    setCleaveValue('jumlah_investasi', d.jumlah_investasi || 0);
                                    setCleaveValue('bagi_hasil_keseluruhan', d.nominal_bagi_hasil_yang_didapatkan || 0);
                                }, 50);
                            }, 100);
                        }
                    },
                    error: () => showError('Gagal memuat data untuk diedit', 'Gagal!')
                });
            });
        });
    </script>
@endpush
