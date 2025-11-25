@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Penyaluran Deposito</h4>
                <button class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    id="btnTambahPenyaluranDeposito">
                    <i class="fa fa-plus"></i>
                    Penyaluran Deposito
                </button>
            </div>

            <div class="content-wrapper">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-history-kol table table-bordered" id="tableHistoryKol">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No. Kontrak</th>
                                    <th class="text-center">Nama Perusahaan</th>
                                    <th class="text-center">Nominal yang Disalurkan</th>
                                    <th class="text-center">Tanggal Pengiriman Dana</th>
                                    <th class="text-center">Tanggal Pengembalian Dana</th>
                                    <th class="text-center">Bukti Pengembalian Dana</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">KTR-001</td>
                                    <td class="text-center">Malaka</td>
                                    <td class="text-center">Rp. 100.000.000</td>
                                    <td class="text-center">15 Maret 2025</td>
                                    <td class="text-center">15 Maret 2025</td>
                                    <td class="text-center">
                                        <a href="#">Dokumen.jpg</a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="text-center">KTR-002</td>
                                    <td class="text-center">Techno Infinity</td>
                                    <td class="text-center">Rp. 50.000.000</td>
                                    <td class="text-center">16 Maret 2025</td>
                                    <td class="text-center">16 Maret 2025</td>
                                    <td class="text-center">
                                        <a href="#">Dokumen.jpg</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalTambahPenyaluranDeposito" tabindex="-1"
            aria-labelledby="modalTambahPenyaluranDepositoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahPenyaluranDepositoLabel">Tambah Penyaluran Deposito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambahPenyaluranDeposito" novalidate>
                            <div class="col-12 mb-3">
                                <label for="NoKontrak" class="form-label">No. Kontrak</label>
                                <input type="text" class="form-control" id="NoKontrak" placeholder="KTR-001" required
                                    disabled>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="NamaPerusahaan" class="form-label">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="NamaPerusahaan"
                                    placeholder="Masukkan Nama Perusahaan" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="NominalDisalurkan" class="form-label">Nominal yang Disalurkan</label>
                                <input type="text" class="form-control input-rupiah" id="NominalDisalurkan"
                                    placeholder="Rp 0" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="TanggalPengirimanDana" class="form-label">Tanggal Pengiriman Dana</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="TanggalPengirimanDana"
                                            placeholder="Pilih Tanggal" required readonly>
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="TanggalPengembalianDana" class="form-label">Tanggal Pengembalian
                                        Dana</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" id="TanggalPengembalianDana"
                                            placeholder="Pilih Tanggal" required readonly>
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger">Hapus Data</button>
                        <button type="button" class="btn btn-primary" id="btnSimpanPenyaluranDeposito">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                            Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const $modal = $('#modalTambahPenyaluranDeposito');
            const $form = $('#formTambahPenyaluranDeposito');

            $('#tableHistoryKol').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                },
                order: [
                    [0, 'asc']
                ]
            });

            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom auto'
            });

            window.initCleaveRupiah();

            $('#btnTambahPenyaluranDeposito').on('click', function() {
                $form[0].reset();
                $form.removeClass('was-validated');
                $('.datepicker').datepicker('update', '');

                window.initCleaveRupiah();

                $modal.modal('show');
            });

            $('#btnSimpanPenyaluranDeposito').on('click', function() {
                if (!$form[0].checkValidity()) {
                    $form.addClass('was-validated');
                    return;
                }

                const nominalRaw = window.getCleaveRawValue(document.getElementById('NominalDisalurkan'));

                const formData = {
                    no_kontrak: $('#NoKontrak').val(),
                    nama_perusahaan: $('#NamaPerusahaan').val(),
                    nominal_disalurkan: nominalRaw,
                    nominal_formatted: $('#NominalDisalurkan').val(),
                    tanggal_pengiriman: $('#TanggalPengirimanDana').val(),
                    tanggal_pengembalian: $('#TanggalPengembalianDana').val()
                };

                console.log('Data to save:', formData);

                $modal.modal('hide');
            });

            $modal.on('hidden.bs.modal', function() {
                $form.removeClass('was-validated');
            });
        });
    </script>
@endpush

