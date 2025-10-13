@extends('layouts.app')

@section('content')
    <div>
        <div>
            <a href="{{ route('peminjaman') }}" class="btn btn-outline-primary mb-4">
                <i class="tf-icons ti ti-arrow-left me-1"></i>
                Kembali
            </a>
            <h4 class="fw-bold">Menu Pengajuan Peminjaman</h4>
        </div>

        <form action="#" method="POST" enctype="multipart/form-data" id="formPeminjaman">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan"
                                value="Techno Infinity" required>
                        </div>
                    </div>
                    <div class="card border-1 mb-3 shadow-none" id="cardSumberPembiayaan">
                        <div class="card-body">
                            <div class="col-md-12 mb-3">
                                <label class="form-label mb-2">Sumber Pembiayaan</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input name="sumber_pembiayaan" class="form-check-input sumber-pembiayaan-radio"
                                            type="radio" value="Eksternal" id="sumber_eksternal" checked required>
                                        <label class="form-check-label" for="sumber_eksternal">
                                            Eksternal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input name="sumber_pembiayaan" class="form-check-input sumber-pembiayaan-radio"
                                            type="radio" value="Internal" id="sumber_internal" required>
                                        <label class="form-check-label" for="sumber_internal">
                                            Internal
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-2" id="divSumberEksternal" style="display: block;">
                                    <select id="select2Basic" name="sumber_eksternal_id" class="form-select"
                                        data-placeholder="Pilih Sumber Pembiayaan Eksternal">
                                        <option value="">Pilih Sumber Pembiayaan</option>
                                        @foreach ($sumber_eksternal as $sumber)
                                            <option value="{{ $sumber['id'] }}">{{ $sumber['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-1 mb-3 shadow-none">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-sm-12 mb-3">
                                    <label for="selectBank" class="form-label">Nama Bank</label>
                                    <select class="form-select" id="selectBank" name="nama_bank" required>
                                        <option value="">Pilih Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank }}">{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="no_rekening" class="form-label">No. Rekening</label>
                                    <input type="text" class="form-control" id="no_rekening" name="no_rekening"
                                        placeholder="Masukkan No. Rekening" required>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label for="nama_rekening" class="form-label">Nama Rekening</label>
                                    <input type="text" class="form-control" id="nama_rekening" name="nama_rekening"
                                        placeholder="Masukkan Nama Rekening" required>
                                </div>
                            </div>

                            <div class="row mb-3" id="rowLampiranSID">
                                <div class="col-md-6">
                                    <label for="lampiran_sid" class="form-label">Lampiran SID</label>
                                    <input class="form-control" type="file" id="lampiran_sid" name="lampiran_sid">
                                    <div class="form-text mb-3">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png,
                                        rar, zip)</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="nilai_kol" class="form-label">Nilai KOL</label>
                                    <input type="text" class="form-control" id="nilai_kol" name="nilai_kol"
                                        placeholder="Nilai KOL" disabled>
                                </div>
                                <div class="">
                                    <label for="tujuan_pembiayaan" class="form-label">Tujuan Pembiayaan</label>
                                    <input type="text" class="form-control" id="defaultFormControlInput"
                                        placeholder="Tujuan Pembiayaan" aria-describedby="defaultFormControlHelp" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label mb-2">Jenis Pembiayaan</label>
                                    <div class="d-flex">
                                        <div class="form-check me-3">
                                            <input name="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio"
                                                type="radio" value="Invoice Financing" id="invoice_financing" checked
                                                required>
                                            <label class="form-check-label" for="invoice_financing">
                                                Invoice Financing
                                            </label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input name="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio"
                                                type="radio" value="PO Financing" id="po_financing" required>
                                            <label class="form-check-label" for="po_financing">
                                                PO Financing
                                            </label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input name="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio"
                                                type="radio" value="Installment" id="installment" required>
                                            <label class="form-check-label" for="installment">
                                                Installment
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio"
                                                type="radio" value="Factoring" id="factoring" required>
                                            <label class="form-check-label" for="factoring">
                                                Factoring
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Invoice/Kontrak -->
                    @include('livewire.peminjaman.partials._invoice-table')

                    <div class="card border-1 mb-4 shadow-none">
                        <div class="card-body">
                            <!-- Form untuk selain Installment -->
                            <div id="formNonInstallment">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="total_pinjaman" class="form-label" id="labelTotalPinjaman">Total
                                            Pinjaman</label>
                                        <input type="text" class="form-control" id="total_pinjaman"
                                            name="total_pinjaman" placeholder="RP. 9.000.000">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="flatpickr-tanggal-pencairan" class="form-label">Harapan Tanggal
                                            Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control rounded-start flatpickr-date"
                                                placeholder="DD/MM/YYYY" id="flatpickr-tanggal-pencairan"
                                                name="tanggal_pencairan" />
                                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="total_bagi_hasil" class="form-label">Total Bagi Hasil</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="total_bagi_hasil"
                                                name="total_bagi_hasil" placeholder="2% (Rp. 180.000)">
                                            <span class="input-group-text">/Bulan</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="flatpickr-tanggal-pembayaran" class="form-label">Rencana Tanggal
                                            Pembayaran</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control rounded-start flatpickr-date"
                                                placeholder="DD/MM/YYYY" id="flatpickr-tanggal-pembayaran"
                                                name="tanggal_pembayaran" />
                                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pembayaran_total" class="form-label">Pembayaran Total</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="pembayaran_total"
                                                name="pembayaran_total" placeholder="Rp. 9.180.000">
                                            <span class="input-group-text">/Bulan</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form khusus untuk Installment -->
                            <div id="formInstallment" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nominal_pinjaman" class="form-label">Total Pinjaman</label>
                                        <input type="text" class="form-control" id="nominal_pinjaman"
                                            name="nominal_pinjaman" placeholder="RP. 9.000.000">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tenorPembayaran" class="form-label">Tenor Pembayaran</label>
                                        <select class="form-select" id="tenorPembayaran" name="tenor_pembayaran">
                                            <option value="">Pilih Tenor</option>
                                            @foreach ($tenor_pembayaran as $tenor)
                                                <option value="{{ $tenor['value'] }}">{{ $tenor['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Persentase Bagi Hasil (Debit Cost)</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pps_debit" class="form-label">PPS</label>
                                        <input type="text" class="form-control bg-light" id="pps_debit"
                                            value="10% (Rp. 900.000)" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="pps_percentage" class="form-label">PPS</label>
                                        <input type="text" class="form-control bg-light" id="pps_percentage"
                                            value="40% (Rp. 360.000)" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="s_finance" class="form-label">S Finance</label>
                                        <input type="text" class="form-control bg-light" id="s_finance"
                                            value="60% (Rp. 540.000)" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="total_pembayaran_installment" class="form-label">Total Pembayaran
                                            <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Total yang harus dibayarkan"></i>
                                        </label>
                                        <input type="text" class="form-control bg-light"
                                            id="total_pembayaran_installment" value="Rp 9.540.000" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bayar_per_bulan" class="form-label">Yang harus dibayarkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bg-light" id="bayar_per_bulan"
                                                value="Rp. 3.180.000" disabled>
                                            <span class="input-group-text">/Bulan</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="catatan_lainnya" class="form-label">Catatan Lainnya</label>
                            <textarea class="form-control" id="catatan_lainnya" name="catatan_lainnya" rows="3"
                                placeholder="Masukkan Catatan"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <span class="align-middle">Simpan Data</span>
                            <i class="tf-icons ti ti-arrow-right ms-1"></i>
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    @include('livewire.peminjaman.partials._modal-tambah-invoice')
@endsection

@push('scripts')
    <script>
        // Data storage for invoice tables
        let invoiceFinancingData = [];
        let poFinancingData = [];
        let installmentData = [];
        let factoringData = [];
        let currentJenisPembiayaan = 'Invoice Financing';
        let modalInstance;
        let currentSumberPembiayaan = 'Eksternal';

        $(document).ready(function() {
            modalInstance = new bootstrap.Modal(document.getElementById('modalTambahInvoice'));

            initSelect2Elements();

            initFlatpickrElements();

            // Handle Sumber Pembiayaan Radio
            $('.sumber-pembiayaan-radio').on('change', function() {
                if ($(this).val() === 'Eksternal') {
                    $('#divSumberEksternal').slideDown();
                } else {
                    $('#divSumberEksternal').slideUp();
                }
            });

            // Handle Jenis Pembiayaan Radio
            $('.jenis-pembiayaan-radio').on('change', function() {
                currentJenisPembiayaan = $(this).val();
                handleJenisPembiayaanChange(currentJenisPembiayaan);
            });

            // Handle Tambah Invoice Button
            $('#btnTambahInvoice').on('click', function() {
                openModal(currentJenisPembiayaan);
            });

            // Handle Simpan Invoice Button
            $('#btnSimpanInvoice').on('click', function() {
                saveInvoiceData();
            });
        });

        function handleJenisPembiayaanChange(jenisPembiayaan) {
            // Hide all tables first
            $('.financing-table').hide();

            if (jenisPembiayaan === 'Installment') {
                $('#formNonInstallment').hide();
                $('#formInstallment').show();
                $('#cardSumberPembiayaan').hide();
                $('#rowLampiranSID').hide();
                $('#installmentTable').show();
            } else {
                $('#formNonInstallment').show();
                $('#formInstallment').hide();

                if (jenisPembiayaan === 'Invoice Financing' || jenisPembiayaan === 'PO Financing') {
                    $('#cardSumberPembiayaan').show();
                    $('#rowLampiranSID').show();
                } else {
                    $('#cardSumberPembiayaan').hide();
                    $('#rowLampiranSID').hide();
                }

                // Update label and show appropriate table based on type
                if (jenisPembiayaan === 'Factoring') {
                    $('#labelTotalPinjaman').text('Total Nominal Yang Dialihkan');
                    $('#factoringTable').show();
                } else if (jenisPembiayaan === 'PO Financing') {
                    $('#labelTotalPinjaman').text('Total Pinjaman');
                    $('#poFinancingTable').show();
                } else {
                    $('#labelTotalPinjaman').text('Total Pinjaman');
                    $('#invoiceFinancingTable').show();
                }
            }
        }

        function openModal(jenisPembiayaan) {
            // Hide all modal forms
            $('.modal-form-content').hide();

            // Clear all modal inputs
            $('.modal-form-content input[type="text"]').val('');
            $('.modal-form-content input[type="file"]').val('');

            // Show appropriate form and update title
            switch (jenisPembiayaan) {
                case 'Invoice Financing':
                    $('#modalTitle').text('Tambah Invoice Financing');
                    $('#formModalInvoiceFinancing').show();
                    break;
                case 'PO Financing':
                    $('#modalTitle').text('Tambah PO Financing');
                    $('#formModalPOFinancing').show();
                    break;
                case 'Installment':
                    $('#modalTitle').text('Tambah Invoice Penjamin');
                    $('#formModalInstallment').show();
                    break;
                case 'Factoring':
                    $('#modalTitle').text('Tambah Kontrak Penjamin');
                    $('#formModalFactoring').show();
                    break;
            }

            // Initialize flatpickr for modal after showing
            setTimeout(function() {
                initModalFlatpickr();
            }, 100);

            modalInstance.show();
        }


        // Menggunakan pola Vuexy untuk Select2
        function initSelect2Elements() {
            const select2Elements = $('.form-select');
            if (select2Elements.length) {
                select2Elements.each(function() {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: $this.data('placeholder') || 'Select value',
                        dropdownParent: $this.closest('.modal').length ? $this.closest('.modal') : $this
                            .parent()
                    });
                });
            }
        }

        // Menggunakan pola Vuexy untuk Flatpickr
        function initFlatpickrElements() {
            const flatpickrDate = document.querySelectorAll('.flatpickr-date');
            if (flatpickrDate) {
                flatpickrDate.forEach(function(elem) {
                    if (!elem._flatpickr) {
                        elem.flatpickr({
                            monthSelectorType: 'static',
                            dateFormat: 'd/m/Y',
                            altInput: true,
                            altFormat: 'j F Y'
                        });
                    }
                });
            }
        }

        // Init flatpickr untuk modal
        function initModalFlatpickr() {
            const modalFlatpickr = document.querySelectorAll('.flatpickr-modal-date');
            if (modalFlatpickr) {
                modalFlatpickr.forEach(function(elem) {
                    // Destroy existing instance
                    if (elem._flatpickr) {
                        elem._flatpickr.destroy();
                    }
                    // Reinitialize
                    elem.flatpickr({
                        monthSelectorType: 'static',
                        dateFormat: 'd/m/Y',
                        altInput: true,
                        altFormat: 'j F Y'
                    });
                });
            }
        }
    </script>
@endpush
