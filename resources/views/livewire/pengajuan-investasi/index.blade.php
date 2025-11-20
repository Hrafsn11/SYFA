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
            const $form = $('#formTambahFormKerjaInvestor');
            const INVESTOR = { nama: '{{ optional($investor)->nama ?? '' }}', deposito: '{{ optional($investor)->deposito ?? '' }}', id: '{{ optional($investor)->id_debitur ?? '' }}' };
            const CSRF = '{{ csrf_token() }}';
            let cleaveInstances = {}, deleteId = null;

            const alert = (icon, html, title = icon === 'error' ? 'Error!' : icon === 'success' ? 'Berhasil!' : 'Perhatian') => 
                Swal.fire({ icon, title, [icon === 'error' || icon === 'warning' ? 'html' : 'text']: html, ...(icon === 'success' && { timer: 2000, showConfirmButton: false }) });

            const cleaveConfig = { numeral: true, numeralThousandsGroupStyle: 'thousand', numeralDecimalScale: 0, prefix: 'Rp ', rawValueTrimPrefix: true, noImmediatePrefix: false };
            
            const initCleave = () => ['jumlah_investasi', 'bagi_hasil_keseluruhan'].forEach(id => {
                cleaveInstances[id]?.destroy();
                cleaveInstances[id] = new Cleave(`#${id}`, cleaveConfig);
            });

            const getCleave = (id) => parseInt(cleaveInstances[id]?.getRawValue()) || 0;
            const setCleave = (id, val) => cleaveInstances[id]?.setRawValue(val);

            $('#bs-datepicker-tanggal-pembayaran').datepicker({ format: 'yyyy-mm-dd', todayHighlight: true, autoclose: true, orientation: 'bottom auto', startDate: 'today' });

            const resetForm = () => {
                $form[0].reset();
                $form.removeClass('was-validated');
                $('#editFormKerjaInvestorId, #modalTambahFormKerjaInvestorLabel').val('').filter(':last').text('Tambah Pengajuan Investasi');
                $('#btnHapusFormKerjaInvestor').addClass('d-none');
                ['jumlah_investasi', 'bagi_hasil_keseluruhan'].forEach(id => setCleave(id, 0));
            };

            const setDepositoMode = (deposito) => {
                const isReguler = deposito === 'reguler';
                $('#bagi_hasil').val(isReguler ? '10' : '').prop({ readonly: isReguler, disabled: isReguler }).toggleClass('non-editable', isReguler);
                $('#bagi-hasil-hint').toggleClass('d-none', isReguler);
                calculateBagiHasil();
            };

            const calculateBagiHasil = () => {
                const [jumlah, persen, lama] = [getCleave('jumlah_investasi'), parseFloat($('#bagi_hasil').val()) || 0, parseInt($('#lama_investasi').val()) || 0];
                setCleave('bagi_hasil_keseluruhan', (jumlah > 0 && persen > 0 && lama > 0) ? Math.round((jumlah * persen / 100) / 12 * lama) : 0);
            };

            $('#btnTambahFormKerjaInvestor').click(() => {
                if (!INVESTOR.nama || !INVESTOR.deposito) return alert('warning', 'Anda belum terdaftar sebagai investor.<br>Silakan hubungi admin untuk mendaftar sebagai investor.', 'Data Investor Tidak Ditemukan');
                
                resetForm();
                $('#nama_investor').val(INVESTOR.nama);
                $('#id_debitur_dan_investor').val(INVESTOR.id);
                const deposito = INVESTOR.deposito.toLowerCase();
                $('input[name="deposito"]').prop({ checked: false, disabled: true }).filter(`[value="${deposito}"]`).prop('checked', true);
                setDepositoMode(deposito);
                $modal.modal('show');
                setTimeout(initCleave, 100);
            });

            $modal.on('shown.bs.modal', () => !cleaveInstances.jumlah_investasi && initCleave())
                  .on('hidden.bs.modal', () => (resetForm(), $('#btnSimpanSpinner').addClass('d-none'), $('#btnSimpanFormKerjaInvestor').prop('disabled', false)));

            $('#jumlah_investasi, #bagi_hasil, #lama_investasi').on('input', calculateBagiHasil);

            $('#btnHapusFormKerjaInvestor').click((e) => {
                e.preventDefault();
                const id = $('#editFormKerjaInvestorId').val();
                if (id) { deleteId = id; $modal.modal('hide'); $('#modalConfirmDeleteInvestor').modal('show'); }
            });

            $('#btnConfirmDeleteInvestor').click(function() {
                if (!deleteId) return;
                const $spinner = $('#btnDeleteSpinner');
                $spinner.removeClass('d-none');
                $(this).prop('disabled', true);

                $.ajax({
                    url: `/pengajuan-investasi/${deleteId}`,
                    method: 'DELETE',
                    data: { _token: CSRF },
                    success: (res) => !res.error && ($('#modalConfirmDeleteInvestor').modal('hide'), Livewire.dispatch('refreshPengajuanInvestasiTable'), alert('success', res.message || 'Data berhasil dihapus'), deleteId = null),
                    complete: () => ($spinner.addClass('d-none'), $(this).prop('disabled', false))
                });
            });

            $('#modalConfirmDeleteInvestor').on('hidden.bs.modal', () => deleteId = null);

            $('#btnSimpanFormKerjaInvestor').click(function() {
                if (!$form[0].checkValidity()) return $form.addClass('was-validated');
                if (!$('input[name="deposito"]:checked').val()) return alert('warning', 'Pilih jenis deposito terlebih dahulu');
                if (!INVESTOR.id) return alert('warning', 'ID Investor tidak tersedia. Silakan refresh halaman atau hubungi admin.', 'Data Investor Tidak Ditemukan');

                const editId = $('#editFormKerjaInvestorId').val();
                const deposito = $('input[name="deposito"]:checked').val();
                const $spinner = $('#btnSimpanSpinner');

                $('input[name="deposito"]').prop('disabled', false);

                const data = {
                    id_debitur_dan_investor: INVESTOR.id,
                    nama_investor: $('#nama_investor').val(),
                    deposito: deposito.charAt(0).toUpperCase() + deposito.slice(1),
                    tanggal_investasi: $('#bs-datepicker-tanggal-pembayaran').val(),
                    lama_investasi: $('#lama_investasi').val(),
                    jumlah_investasi: getCleave('jumlah_investasi'),
                    bagi_hasil_pertahun: $('#bagi_hasil').val(),
                    _token: CSRF,
                    ...(editId && { _method: 'PUT' })
                };

                $('input[name="deposito"]').prop('disabled', true);
                $spinner.removeClass('d-none');
                $(this).prop('disabled', true);

                $.ajax({
                    url: editId ? `/pengajuan-investasi/${editId}` : '{{ route('pengajuan-investasi.store') }}',
                    method: 'POST',
                    data,
                    success: (res) => !res.error && ($modal.modal('hide'), Livewire.dispatch('refreshPengajuanInvestasiTable'), alert('success', res.message || (editId ? 'Data berhasil diupdate' : 'Data berhasil ditambahkan'))),
                    error: (xhr) => alert('error', xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join('<br>') : xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data', 'Gagal!'),
                    complete: () => ($spinner.addClass('d-none'), $(this).prop('disabled', false))
                });
            });

            $(document).on('click', '.investor-detail-btn', (e) => {
                e.preventDefault();
                const id = $(e.currentTarget).data('id');
                id ? window.location.href = `/pengajuan-investasi/${id}` : alert('error', 'ID tidak ditemukan');
            });

            $(document).on('click', '.investor-edit-btn', (e) => {
                e.preventDefault();
                const id = $(e.currentTarget).data('id');
                if (!id) return alert('error', 'ID tidak ditemukan');

                $.ajax({
                    url: `/pengajuan-investasi/${id}/edit`,
                    method: 'GET',
                    success: (res) => {
                        if (!res.error && res.data) {
                            const d = res.data;
                            resetForm();
                            
                            Object.entries({
                                editFormKerjaInvestorId: d.id_pengajuan_investasi,
                                id_debitur_dan_investor: d.id_debitur_dan_investor,
                                nama_investor: d.nama_investor,
                                lama_investasi: d.lama_investasi,
                                bagi_hasil: d.bagi_hasil_pertahun
                            }).forEach(([id, val]) => $(`#${id}`).val(val));

                            $('#modalTambahFormKerjaInvestorLabel').text('Edit Pengajuan Investasi');
                            $('#btnHapusFormKerjaInvestor').removeClass('d-none');

                            const deposito = d.deposito?.toLowerCase();
                            if (deposito) {
                                $('input[name="deposito"]').prop({ checked: false, disabled: true }).filter(`[value="${deposito}"]`).prop('checked', true);
                                setDepositoMode(deposito);
                            }

                            d.tanggal_investasi && $('#bs-datepicker-tanggal-pembayaran').datepicker('setDate', d.tanggal_investasi);
                            
                            $modal.modal('show');
                            setTimeout(() => {
                                initCleave();
                                setTimeout(() => {
                                    setCleave('jumlah_investasi', d.jumlah_investasi || 0);
                                    setCleave('bagi_hasil_keseluruhan', d.nominal_bagi_hasil_yang_didapatkan || 0);
                                }, 50);
                            }, 100);
                        }
                    },
                    error: () => alert('error', 'Gagal memuat data untuk diedit', 'Gagal!')
                });
            });
        });
    </script>
@endpush
