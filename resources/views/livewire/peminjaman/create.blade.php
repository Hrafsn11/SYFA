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
                                            <input type="text" class="form-control non-editable" id="nama_perusahaan" name="nama_perusahaan"
                                                value="{{ old('nama_perusahaan', optional($master)->nama_debitur ?? 'Techno Infinity') }}" required readonly tabindex="-1">
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
                                    <select class="form-select non-editable select-non-editable" id="selectBank" name="nama_bank" required disabled aria-disabled="true">
                                        <option value="">Pilih Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank }}" {{ (old('nama_bank', optional(value: $master)->nama_bank) == $bank) ? 'selected' : '' }}>{{ $bank }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="no_rekening" class="form-label">No. Rekening</label>
                                    <input type="text" class="form-control non-editable" id="no_rekening" name="no_rekening"
                                        value="{{ old('no_rekening', optional($master)->no_rek) }}" placeholder="Masukkan No. Rekening" required readonly tabindex="-1">
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
                                    <input type="text" class="form-control non-editable" id="nilai_kol" name="nilai_kol"
                                        value="{{ old('nilai_kol', optional($master->kol)->kol ?? '') }}" placeholder="Nilai KOL" readonly tabindex="-1">
                                </div>
                                <div class="">
                                    <label for="tujuan_pembiayaan" class="form-label">Tujuan Pembiayaan</label>
                                    <input type="text" class="form-control" id="defaultFormControlInput" name="tujuan_pembiayaan"
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
                                        <input type="text" class="form-control input-rupiah" id="total_pinjaman"
                                            name="total_pinjaman" placeholder="Rp 0">
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
                                            <input type="text" class="form-control input-rupiah non-editable" id="total_bagi_hasil"
                                                        name="total_bagi_hasil" placeholder="2%" readonly tabindex="-1">
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
                                            <input type="text" class="form-control input-rupiah non-editable" id="pembayaran_total"
                                                name="pembayaran_total" placeholder="" readonly tabindex="-1">
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
                                        <input type="text" class="form-control input-rupiah" id="nominal_pinjaman"
                                            name="nominal_pinjaman" placeholder="Rp 0">
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
                                    <div class="col-md-4">
                                        <label for="pps_debit" class="form-label">Persentase Bagi Hasil (Debit Cost)</label>
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
            
            // Initialize Cleave.js untuk format rupiah
            initCleaveRupiah();

            const totalPinjamanEl = document.getElementById('total_pinjaman');
            const totalBagiHasilEl = document.getElementById('total_bagi_hasil');
            const pembayaranTotalEl = document.getElementById('pembayaran_total');

            function recalcBagiHasil() {
                if (!totalPinjamanEl) return;
                const raw = window.getCleaveRawValue(totalPinjamanEl);
                const bagi = Math.round(raw * 0.02); // 2%
                const pembayaran = raw + bagi;
                window.setCleaveValue(totalBagiHasilEl, 'Rp ' + bagi.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                window.setCleaveValue(pembayaranTotalEl, 'Rp ' + pembayaran.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            }

            if (totalPinjamanEl) {
                totalPinjamanEl.addEventListener('input', function() {
                    clearTimeout(totalPinjamanEl._calcTimeout);
                    totalPinjamanEl._calcTimeout = setTimeout(recalcBagiHasil, 150);
                });
            }

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

        let editInvoiceIndex = -1;

        function saveInvoiceData() {
            const index = editInvoiceIndex >= 0 ? editInvoiceIndex : invoiceFinancingData.length;
            const no_invoice = $('#modal_no_invoice').val();
            const nama_client = $('#modal_nama_client').val();
            const nilai_invoice = window.getCleaveRawValue(document.getElementById('modal_nilai_invoice')) || 0;
            const nilai_pinjaman = window.getCleaveRawValue(document.getElementById('modal_nilai_pinjaman')) || 0;
            const nilai_bagi_hasil = window.getCleaveRawValue(document.getElementById('modal_nilai_bagi_hasil')) || Math.round(nilai_pinjaman * 0.02);
            let invoice_date = $('#modal_invoice_date').val();
            let due_date = $('#modal_due_date').val();

            invoice_date = convertDMYToISO(invoice_date);
            due_date = convertDMYToISO(due_date);

            const dokumen_invoice_file = document.getElementById('modal_dokumen_invoice').files[0] || null;
            const dokumen_kontrak_file = document.getElementById('modal_dokumen_kontrak').files[0] || null;
            const dokumen_so_file = document.getElementById('modal_dokumen_so').files[0] || null;
            const dokumen_bast_file = document.getElementById('modal_dokumen_bast').files[0] || null;

            // Basic validation
            if (!no_invoice || nilai_pinjaman <= 0) {
                alert('No. Invoice dan Nilai Pinjaman wajib diisi dan > 0');
                return;
            }

            const payload = {
                no_invoice: no_invoice,
                nama_client: nama_client,
                nilai_invoice: parseFloat(nilai_invoice),
                nilai_pinjaman: parseFloat(nilai_pinjaman),
                nilai_bagi_hasil: parseFloat(nilai_bagi_hasil),
                invoice_date: invoice_date,
                due_date: due_date,
                dokumen_invoice_file: dokumen_invoice_file,
                dokumen_kontrak_file: dokumen_kontrak_file,
                dokumen_so_file: dokumen_so_file,
                dokumen_bast_file: dokumen_bast_file,
            };

            if (editInvoiceIndex >= 0) {
                // update existing
                invoiceFinancingData[editInvoiceIndex] = payload;
                editInvoiceIndex = -1;
            } else {
                invoiceFinancingData.push(payload);
            }

            modalInstance.hide();
            renderInvoiceTables();
        }

        function renderInvoiceTables() {
            // Render Invoice Financing Table tbody
            const tbody = $('#invoiceFinancingTable tbody');
            tbody.empty();
            invoiceFinancingData.forEach(function(inv, idx) {
                const row = `<tr>
                    <td>${idx+1}</td>
                    <td>${inv.no_invoice}</td>
                    <td>${inv.nama_client || ''}</td>
                    <td>Rp. ${numberWithThousandSeparator(inv.nilai_invoice || 0)}</td>
                    <td>Rp. ${numberWithThousandSeparator(inv.nilai_pinjaman || 0)}</td>
                    <td>Rp. ${numberWithThousandSeparator(inv.nilai_bagi_hasil || 0)}</td>
                    <td>${inv.invoice_date || ''}</td>
                    <td>${inv.due_date || ''}</td>
                    <td>${inv.dokumen_invoice_file ? inv.dokumen_invoice_file.name : ''}</td>
                    <td>${inv.dokumen_kontrak_file ? inv.dokumen_kontrak_file.name : ''}</td>
                    <td>${inv.dokumen_so_file ? inv.dokumen_so_file.name : ''}</td>
                    <td>${inv.dokumen_bast_file ? inv.dokumen_bast_file.name : ''}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-outline-primary btn-edit-invoice" data-idx="${idx}" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="#" class="btn btn-sm btn-danger btn-remove-invoice" data-idx="${idx}">Hapus</a>
                    </td>
                </tr>`;
                tbody.append(row);
            });

            // handle remove
            $('.btn-remove-invoice').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                invoiceFinancingData.splice(idx, 1);
                renderInvoiceTables();
            });

            // handle edit
            $('.btn-edit-invoice').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                const inv = invoiceFinancingData[idx];
                if (!inv) return;

                $('#modal_no_invoice').val(inv.no_invoice);
                $('#modal_nama_client').val(inv.nama_client);
                window.setCleaveValue(document.getElementById('modal_nilai_invoice'), 'Rp ' + numberWithThousandSeparator(inv.nilai_invoice || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_pinjaman'), 'Rp ' + numberWithThousandSeparator(inv.nilai_pinjaman || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_bagi_hasil'), 'Rp ' + numberWithThousandSeparator(inv.nilai_bagi_hasil || 0));
                $('#modal_invoice_date').val(inv.invoice_date || '');
                $('#modal_due_date').val(inv.due_date || '');

                // Note: file inputs cannot be pre-filled for security reasons. Inform the user to re-upload if necessary.
                $('#modal_dokumen_invoice').val('');
                $('#modal_dokumen_kontrak').val('');
                $('#modal_dokumen_so').val('');
                $('#modal_dokumen_bast').val('');

                editInvoiceIndex = idx;
                modalInstance.show();
            });

        }

        function numberWithThousandSeparator(x) {
            if (!x && x !== 0) return '';
            return parseFloat(x).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $('#formPeminjaman').on('submit', function(e) {
            e.preventDefault();
            if (invoiceFinancingData.length === 0) {
                if (!confirm('Anda belum menambahkan invoice, lanjutkan menyimpan?')) return;
            }

            const form = document.getElementById('formPeminjaman');
            const fd = new FormData(form);

            // Append master id (if available)
            const masterId = '{{ optional($master)->id_debitur ?? '' }}';
            if (masterId) fd.set('id_debitur', masterId);

            // Normalize and append header date fields (convert to ISO)
            const tanggalPencairanRaw = $('#flatpickr-tanggal-pencairan').val();
            const tanggalPencairanISO = convertDMYToISO(tanggalPencairanRaw);
            if (tanggalPencairanISO) fd.set('harapan_tanggal_pencairan', tanggalPencairanISO);

            const tanggalPembayaranRaw = $('#flatpickr-tanggal-pembayaran').val();
            const tanggalPembayaranISO = convertDMYToISO(tanggalPembayaranRaw);
            if (tanggalPembayaranISO) fd.set('rencana_tgl_pembayaran', tanggalPembayaranISO);

            // include derived readonly totals if present
            const totalBagi = window.getCleaveRawValue(document.getElementById('total_bagi_hasil')) || 0;
            const pembayaranTotalRaw = window.getCleaveRawValue(document.getElementById('pembayaran_total')) || 0;
            fd.set('total_bagi_hasil', totalBagi);
            fd.set('pembayaran_total', pembayaranTotalRaw);

            // Disabled inputs aren't submitted by forms; include some explicitly
            const selectedBank = $('#selectBank').val();
            if (selectedBank) fd.set('nama_bank', selectedBank);
            const noRek = $('#no_rekening').val();
            if (noRek) fd.set('no_rekening', noRek);
            const nilaiKol = $('#nilai_kol').val();
            if (nilaiKol) fd.set('nilai_kol', nilaiKol);
            const namaPerusahaan = $('#nama_perusahaan').val();
            if (namaPerusahaan) fd.set('nama_perusahaan', namaPerusahaan);

            // Ensure lampiran SID file is appended (FormData(form) should include it but be defensive)
            const lampiranEl = document.getElementById('lampiran_sid');
            if (lampiranEl && lampiranEl.files && lampiranEl.files[0]) {
                fd.append('lampiran_sid', lampiranEl.files[0]);
            }

            // Ensure sumber pembiayaan eksternal selection is included (select2 may wrap it)
            const sumberEksternalVal = $('#select2Basic').val();
            if (sumberEksternalVal) fd.set('sumber_eksternal_id', sumberEksternalVal);

            // normalize sumber pembiayaan value to lowercase
            const sumberVal = $('input[name="sumber_pembiayaan"]:checked').val();
            if (sumberVal) fd.set('sumber_pembiayaan', sumberVal.toLowerCase());

            // Append invoices JSON
            fd.set('invoices', JSON.stringify(invoiceFinancingData.map(i => ({
                no_invoice: i.no_invoice,
                nama_client: i.nama_client,
                nilai_invoice: i.nilai_invoice,
                nilai_pinjaman: i.nilai_pinjaman,
                nilai_bagi_hasil: i.nilai_bagi_hasil,
                invoice_date: i.invoice_date,
                due_date: i.due_date
            }))));

            // Append files
            invoiceFinancingData.forEach(function(inv, idx) {
                if (inv.dokumen_invoice_file) fd.append(`files[${idx}][dokumen_invoice]`, inv.dokumen_invoice_file);
                if (inv.dokumen_kontrak_file) fd.append(`files[${idx}][dokumen_kontrak]`, inv.dokumen_kontrak_file);
                if (inv.dokumen_so_file) fd.append(`files[${idx}][dokumen_so]`, inv.dokumen_so_file);
                if (inv.dokumen_bast_file) fd.append(`files[${idx}][dokumen_bast]`, inv.dokumen_bast_file);
            });

            // send via AJAX
            $.ajax({
                url: '{{ route('peminjaman.invoice.store') }}',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                beforeSend: function() {
                    // optional: show loader
                },
                success: function(resp) {
                    if (resp.success) {
                        alert('Peminjaman berhasil disimpan');
                        // redirect to detail
                        if (resp.data && resp.data.id_peminjaman) {
                            window.location.href = '/peminjaman/' + resp.data.id_peminjaman;
                        } else {
                            window.location.href = '/peminjaman';
                        }
                    } else {
                        alert('Gagal: ' + (resp.message || 'Unknown error'));
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    alert(msg);
                }
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
                initCleaveRupiah(); // Reinitialize Cleave for modal inputs
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

                    if ($this.hasClass('select-non-editable')) {
                        $this.prop('disabled', true);
                        const container = $this.next('.select2-container');
                        if (container && container.length) {
                            container.css('pointer-events', 'none').css('opacity', 0.6);
                        }
                    }
                });
            }
        }

        // Menggunakan pola Vuexy untuk Flatpickr
        function initFlatpickrElements() {
            const flatpickrDate = document.querySelectorAll('.flatpickr-date');
            if (flatpickrDate) {
                flatpickrDate.forEach(function(elem) {
                    if (!elem._flatpickr) {
                        // Config khusus untuk tanggal pencairan (minimal 4 hari dari sekarang)
                        if (elem.id === 'flatpickr-tanggal-pencairan') {
                            // Hitung tanggal minimal (4 hari dari sekarang)
                            const minDate = new Date();
                            minDate.setDate(minDate.getDate() + 4);
                            
                            elem.flatpickr({
                                monthSelectorType: 'static',
                                dateFormat: 'd/m/Y',
                                altInput: true,
                                altFormat: 'j F Y',
                                minDate: minDate,
                                disable: [
                                    function(date) {
                                        // Disable tanggal kurang dari 4 hari dari sekarang
                                        const today = new Date();
                                        today.setHours(0, 0, 0, 0);
                                        const checkDate = new Date(date);
                                        checkDate.setHours(0, 0, 0, 0);
                                        const minDateCheck = new Date(today);
                                        minDateCheck.setDate(minDateCheck.getDate() + 4);
                                        return checkDate < minDateCheck;
                                    }
                                ],
                                locale: {
                                    firstDayOfWeek: 1
                                }
                            });
                        } else {
                            // Config default untuk flatpickr lainnya
                            elem.flatpickr({
                                monthSelectorType: 'static',
                                dateFormat: 'd/m/Y',
                                altInput: true,
                                altFormat: 'j F Y'
                            });
                        }
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

        function convertDMYToISO(dmy) {
            if (!dmy) return null;
            if (/^\d{4}-\d{2}-\d{2}$/.test(dmy)) return dmy;
            const parts = dmy.split('/');
            if (parts.length === 3) {
                const d = parts[0].padStart(2, '0');
                const m = parts[1].padStart(2, '0');
                const y = parts[2];
                return `${y}-${m}-${d}`;
            }
            return dmy;
        }

        (function() {
            const style = document.createElement('style');
            style.innerHTML = `
                .non-editable[readonly] { background-color: #e9ecef; cursor: not-allowed; }
                .select-non-editable[disabled] + .select2-container { pointer-events: none; opacity: 0.6; }
            `;
            document.head.appendChild(style);

        })();
    </script>
@endpush
