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
                                value="Techno Infinity" required disabled>
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
                                            @php
                                                // support both array and object representations
                                                if (is_array($sumber)) {
                                                    $percent = $sumber['persentase'] ?? $sumber['bagi_hasil'] ?? $sumber['persentase_bagi_hasil'] ?? 2;
                                                    $label = $sumber['nama'] ?? $sumber['nama_instansi'] ?? '';
                                                    $val = $sumber['id'] ?? '';
                                                } else {
                                                    $percent = $sumber->persentase ?? $sumber->bagi_hasil ?? $sumber->persentase_bagi_hasil ?? 2;
                                                    $label = $sumber->nama ?? $sumber->nama_instansi ?? '';
                                                    $val = $sumber->id ?? $sumber->id_instansi ?? '';
                                                }
                                            @endphp
                                            <option value="{{ $val }}" data-percent="{{ $percent }}">{{ $label }}</option>
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
                                    <div class="form-text mb-3">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls,
                                        png,
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
                                            <input type="number" class="form-control" id="total_bagi_hasil"
                                                name="total_bagi_hasil" placeholder="2% (Rp. 180.000)" disabled>
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
                                                name="pembayaran_total" placeholder="Rp. 9.180.000" disabled>
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
                                        <label for="pps_debit" class="form-label">Persentase Bagi Hasil (Debit
                                            Cost)</label>
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
                const raw = Number(window.getCleaveRawValue(totalPinjamanEl)) || 0;
                const percent = (typeof window.getBagiPercent === 'function') ? window.getBagiPercent() : 2;
                const bagi = Math.round(raw * (percent / 100) * 100) / 100;
                const pembayaran = Math.round((raw + bagi) * 100) / 100;
                window.setCleaveValue(totalBagiHasilEl, 'Rp ' + bagi.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                window.setCleaveValue(pembayaranTotalEl, 'Rp ' + pembayaran.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            }

            if (totalPinjamanEl) {
                totalPinjamanEl.addEventListener('input', function() {
                    clearTimeout(totalPinjamanEl._calcTimeout);
                    totalPinjamanEl._calcTimeout = setTimeout(recalcBagiHasil, 150);
                });
            }

            // Installment live calculation
            const nominalPinjamanEl = document.getElementById('nominal_pinjaman');
            const tenorEl = document.getElementById('tenorPembayaran');

            // If Select2 is used, bind its change event explicitly so recalcInstallment runs
            try {
                // use jQuery in case select2 wraps the select
                $('#tenorPembayaran').on('change.select2', function() {
                    try { recalcInstallment(); } catch(e) { console.error(e); }
                });
            } catch (e) {
                // ignore if jQuery/select2 not available
            }

            function formatCurrency(value) {
                if (value === null || value === undefined || value === '') return '';
                return 'Rp. ' + numberWithThousandSeparator(Number(value).toFixed(0));
            }

            function recalcInstallment() {
                try {
                    const raw = window.getCleaveRawValue(nominalPinjamanEl) || 0;
                    const totalPinjamanVal = Number(raw);
                    // default tenor to 3 months if user hasn't chosen one yet
                    const tenorVal = tenorEl ? (parseInt(tenorEl.value) || 3) : 3;

                    // Business rules fixed: bagi hasil 10%
                    const bagiPercent = 10.0;
                    const totalBagi = Math.round((totalPinjamanVal * (bagiPercent / 100)) * 100) / 100;
                    const ppsAmount = Math.round(totalBagi * 0.40 * 100) / 100;
                    const sfinanceAmount = Math.round(totalBagi * 0.60 * 100) / 100;
                    const totalPembayaranVal = Math.round((totalPinjamanVal + totalBagi) * 100) / 100;
                    const monthlyPay = tenorVal > 0 ? Math.round((totalPembayaranVal / tenorVal) * 100) / 100 : totalPembayaranVal;

                    // Update UI display fields (they are disabled inputs)
                    const elPpsDebit = document.getElementById('pps_debit');
                    const elPpsPercentage = document.getElementById('pps_percentage');
                    const elSFinance = document.getElementById('s_finance');
                    const elTotalPembayaran = document.getElementById('total_pembayaran_installment');
                    const elBayarPerBulan = document.getElementById('bayar_per_bulan');

                    if (elPpsDebit) elPpsDebit.value = `${bagiPercent}% (Rp. ${numberWithThousandSeparator(totalBagi)})`;
                    if (elPpsPercentage) elPpsPercentage.value = `40% (Rp. ${numberWithThousandSeparator(ppsAmount)})`;
                    if (elSFinance) elSFinance.value = `60% (Rp. ${numberWithThousandSeparator(sfinanceAmount)})`;
                    if (elTotalPembayaran) elTotalPembayaran.value = formatCurrency(totalPembayaranVal);
                    if (elBayarPerBulan) elBayarPerBulan.value = formatCurrency(monthlyPay);

                    // store computed values on element for submit use
                    nominalPinjamanEl._computed = {
                        totalPinjaman: totalPinjamanVal,
                        tenor: tenorVal,
                        persentase_bagi_hasil: bagiPercent,
                        pps: ppsAmount,
                        sfinance: sfinanceAmount,
                        total_pembayaran: totalPembayaranVal,
                        yang_harus_dibayarkan: monthlyPay
                    };
                } catch (err) {
                    console.error('recalcInstallment error', err);
                }
            }

                // Update nominal_pinjaman from installmentData (sum nilai_invoice) and recalc
                function updateNominalFromDetails() {
                    try {
                        if (!nominalPinjamanEl) return;
                        let sum = 0;
                        installmentData.forEach(function(it) {
                            const v = Number(normalizeNumericForServer(it.nilai_invoice || 0)) || 0;
                            sum += v;
                        });
                        if (typeof window.setCleaveValue === 'function') {
                            window.setCleaveValue(nominalPinjamanEl, 'Rp ' + numberWithThousandSeparator(sum));
                        } else {
                            nominalPinjamanEl.value = sum;
                        }
                        // ensure tenor defaults to 3 if not set and force recalc so UI updates
                        try {
                            if (tenorEl) {
                                // set via jQuery so Select2 UI updates and triggers change
                                try {
                                    $(tenorEl).val('3').trigger('change');
                                } catch (_) {
                                    tenorEl.value = '3';
                                }
                            }
                        } catch (e) {
                            // ignore
                        }
                        recalcInstallment();
                    } catch (err) {
                        console.error('updateNominalFromDetails error', err);
                    }
                }

            if (nominalPinjamanEl) {
                nominalPinjamanEl.addEventListener('input', function() {
                    clearTimeout(nominalPinjamanEl._timeout);
                    nominalPinjamanEl._timeout = setTimeout(recalcInstallment, 120);
                });
            }
            if (tenorEl) {
                // Ensure default selection exists on load (use jQuery to keep Select2 in sync)
                try {
                    if (!tenorEl.value || tenorEl.value === '') $(tenorEl).val('3').trigger('change');
                } catch (_) {
                    if (!tenorEl.value || tenorEl.value === '') tenorEl.value = '3';
                }
                tenorEl.addEventListener('change', function() {
                    recalcInstallment();
                });
            }

            // Run recalcInstallment on load to populate fields if nominal already exists
            setTimeout(function() {
                try {
                    // If there are installment details present on load, ensure nominal is populated
                    updateNominalFromDetails();
                    // and ensure recalc to reflect default tenor
                    recalcInstallment();
                } catch (e) {
                    // ignore
                }
            }, 200);

            // Handle Sumber Pembiayaan Radio
            $('.sumber-pembiayaan-radio').on('change', function() {
                if ($(this).val() === 'Eksternal') {
                    $('#divSumberEksternal').slideDown();
                } else {
                    $('#divSumberEksternal').slideUp();
                }
                // Recalculate bagi hasil when sumber type changes
                try { recalcBagiHasil(); } catch (e) {}
            });

            // When external sumber selection changes, recalc bagi hasil
            $('#select2Basic').on('change', function() {
                try { recalcBagiHasil(); } catch (e) {}
                try {
                } catch (e) {
                    // suppressed
                }
                try {
                } catch (e) {}
            });


            

            // Helper: get current bagi hasil percent based on sumber selection
            window.getBagiPercent = function() {
                try {
                    const sumberType = $('input[name="sumber_pembiayaan"]:checked').val();
                    if (sumberType === 'Internal') return 2; // internal fixed 2%
                    // external: read data-percent from selected option
                    // When select2 is used, option:selected should still be queryable but also check underlying select value
                    let p = NaN;
                    const $sel = $('#select2Basic');
                    if ($sel && $sel.length) {
                        // try data on selected option
                        const opt = $sel.find('option:selected');
                        if (opt && opt.length) {
                            p = parseFloat(opt.data('percent'));
                            if (!isNaN(p)) return p;
                        }
                        // try data attribute on select element itself
                        const sp = parseFloat($sel.data('percent'));
                        if (!isNaN(sp)) return sp;
                        // try parsing percent from the option text (e.g., "Name (50%)") as a last resort
                        const val = $sel.val();
                        if (val) {
                            const optByVal = $sel.find('option[value="' + val + '"]');
                            if (optByVal && optByVal.length) {
                                const text = optByVal.text() || '';
                                const m = text.match(/(\d+(?:\.\d+)?)\s*%/);
                                if (m && m[1]) {
                                    const parsed = parseFloat(m[1]);
                                    if (!isNaN(parsed)) return parsed;
                                }
                            }
                        }
                    }
                    // suppressed
                } catch (e) {
                    // fallback
                }
                return 2;
            };

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
            if (currentJenisPembiayaan === 'Invoice Financing') {
                const index = editInvoiceIndex >= 0 ? editInvoiceIndex : invoiceFinancingData.length;
                const no_invoice = $('#modal_no_invoice').val();
                const nama_client = $('#modal_nama_client').val();
                const nilai_invoice = window.getCleaveRawValue(document.getElementById('modal_nilai_invoice')) || 0;
                const nilai_pinjaman = window.getCleaveRawValue(document.getElementById('modal_nilai_pinjaman')) || 0;
                const defaultPercent = getBagiPercent();
                const nilai_bagi_hasil = window.getCleaveRawValue(document.getElementById('modal_nilai_bagi_hasil')) || Math.round(nilai_pinjaman * (defaultPercent/100));
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

                // Duplicate check: ensure no_invoice is unique in current invoiceFinancingData
                const duplicateIndex = invoiceFinancingData.findIndex(function(it) { return it.no_invoice === no_invoice; });
                if (duplicateIndex !== -1 && (editInvoiceIndex === -1 || duplicateIndex !== editInvoiceIndex)) {
                    alert('Nomor Invoice sudah terdaftar di daftar. Silakan gunakan nomor lain atau edit entri yang ada.');
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
                    invoiceFinancingData[editInvoiceIndex] = payload;
                    editInvoiceIndex = -1;
                } else {
                    invoiceFinancingData.push(payload);
                }

                modalInstance.hide();
                renderInvoiceTables();
            } else if (currentJenisPembiayaan === 'PO Financing') {
                const index = editInvoiceIndex >= 0 ? editInvoiceIndex : poFinancingData.length;
                const no_kontrak = $('#modal_no_kontrak_po').val();
                const nama_client = $('#modal_nama_client_po').val();
                const nilai_invoice = window.getCleaveRawValue(document.getElementById('modal_nilai_invoice_po')) || 0;
                const nilai_pinjaman = window.getCleaveRawValue(document.getElementById('modal_nilai_pinjaman_po')) || 0;
                const defaultPercentPo = getBagiPercent();
                const nilai_bagi_hasil = window.getCleaveRawValue(document.getElementById('modal_nilai_bagi_hasil_po')) || Math.round(nilai_pinjaman * (defaultPercentPo/100));
                let contract_date = $('#modal_contract_date_po').val();
                let due_date = $('#modal_due_date_po').val();

                contract_date = convertDMYToISO(contract_date);
                due_date = convertDMYToISO(due_date);

                const dokumen_kontrak_file = document.getElementById('modal_dokumen_kontrak_po').files[0] || null;
                const dokumen_so_file = document.getElementById('modal_dokumen_so_po').files[0] || null;
                const dokumen_bast_file = document.getElementById('modal_dokumen_bast_po').files[0] || null;
                const dokumen_lainnya_file = document.getElementById('modal_dokumen_lainnya_po').files[0] || null;

                // Basic validation
                if (!no_kontrak || nilai_pinjaman <= 0) {
                    alert('No. Kontrak dan Nilai Pinjaman wajib diisi dan > 0');
                    return;
                }

                // Duplicate check for PO contract numbers (block on save)
                const dupPo = poFinancingData.findIndex(function(it) { return it.no_kontrak === no_kontrak; });
                if (dupPo !== -1 && (editInvoiceIndex === -1 || dupPo !== editInvoiceIndex)) {
                    alert('Nomor Kontrak PO sudah terdaftar di daftar. Silakan gunakan nomor lain atau edit entri yang ada.');
                    return;
                }

                const payload = {
                    no_kontrak: no_kontrak,
                    nama_client: nama_client,
                    nilai_invoice: parseFloat(nilai_invoice),
                    nilai_pinjaman: parseFloat(nilai_pinjaman),
                    nilai_bagi_hasil: parseFloat(nilai_bagi_hasil),
                    contract_date: contract_date,
                    due_date: due_date,
                    dokumen_kontrak_file: dokumen_kontrak_file,
                    dokumen_so_file: dokumen_so_file,
                    dokumen_bast_file: dokumen_bast_file,
                    dokumen_lainnya_file: dokumen_lainnya_file,
                };

                if (editInvoiceIndex >= 0) {
                    poFinancingData[editInvoiceIndex] = payload;
                    editInvoiceIndex = -1;
                } else {
                    poFinancingData.push(payload);
                }

                modalInstance.hide();
                renderPOFinancingTable();
            }
            else if (currentJenisPembiayaan === 'Installment') {
                const index = editInvoiceIndex >= 0 ? editInvoiceIndex : installmentData.length;
                const no_invoice = $('#modal_no_invoice_inst').val();
                const nama_client = $('#modal_nama_client_inst').val();
                const nilai_invoice = window.getCleaveRawValue(document.getElementById('modal_nilai_invoice_inst')) || 0;
                let invoice_date = $('#modal_invoice_date_inst').val();
                const nama_barang = $('#modal_nama_barang').val();

                invoice_date = convertDMYToISO(invoice_date);

                const dokumen_invoice_file = document.getElementById('modal_dokumen_invoice_inst').files[0] || null;
                const dokumen_lainnya_file = document.getElementById('modal_dokumen_lainnya_inst').files[0] || null;

                // Basic validation
                if (!no_invoice || Number(normalizeNumericForServer(nilai_invoice)) <= 0) {
                    alert('No. Invoice dan Nilai Invoice wajib diisi dan > 0');
                    return;
                }

                // Duplicate check for installment invoices
                const dupInst = installmentData.findIndex(function(it) { return it.no_invoice === no_invoice; });
                if (dupInst !== -1 && (editInvoiceIndex === -1 || dupInst !== editInvoiceIndex)) {
                    alert('Nomor Invoice sudah terdaftar di daftar. Silakan gunakan nomor lain atau edit entri yang ada.');
                    return;
                }

                const payload = {
                    no_invoice: no_invoice,
                    nama_client: nama_client,
                    nilai_invoice: parseFloat(nilai_invoice),
                    invoice_date: invoice_date,
                    nama_barang: nama_barang,
                    dokumen_invoice_file: dokumen_invoice_file,
                    dokumen_lainnya_file: dokumen_lainnya_file,
                };

                if (editInvoiceIndex >= 0) {
                    installmentData[editInvoiceIndex] = payload;
                    editInvoiceIndex = -1;
                } else {
                    installmentData.push(payload);
                }

                modalInstance.hide();
                renderInstallmentTable();
            }
            else if (currentJenisPembiayaan === 'Factoring') {
                const index = editInvoiceIndex >= 0 ? editInvoiceIndex : factoringData.length;
                const no_kontrak = $('#modal_no_kontrak_fact').val();
                const nama_client = $('#modal_nama_client_fact').val();
                const nilai_invoice = window.getCleaveRawValue(document.getElementById('modal_nilai_invoice_fact')) || 0;
                const nilai_pinjaman = window.getCleaveRawValue(document.getElementById('modal_nilai_pinjaman_fact')) || 0;
                const nilai_bagi_hasil = window.getCleaveRawValue(document.getElementById('modal_nilai_bagi_hasil_fact')) || Math.round(nilai_pinjaman * 0.02);
                let contract_date = $('#modal_contract_date_fact').val();
                let due_date = $('#modal_due_date_fact').val();

                contract_date = convertDMYToISO(contract_date);
                due_date = convertDMYToISO(due_date);

                const dokumen_invoice_file = document.getElementById('modal_dokumen_invoice_fact').files[0] || null;
                const dokumen_kontrak_file = document.getElementById('modal_dokumen_kontrak_fact').files[0] || null;
                const dokumen_so_file = document.getElementById('modal_dokumen_so_fact').files[0] || null;
                const dokumen_bast_file = document.getElementById('modal_dokumen_bast_fact').files[0] || null;

                // Basic validation
                if (!no_kontrak || Number(normalizeNumericForServer(nilai_invoice)) <= 0) {
                    alert('No. Kontrak dan Nilai Invoice wajib diisi dan > 0');
                    return;
                }

                // Duplicate check for Factoring contract numbers (block on save)
                const dupFact = factoringData.findIndex(function(it) { return it.no_kontrak === no_kontrak; });
                if (dupFact !== -1 && (editInvoiceIndex === -1 || dupFact !== editInvoiceIndex)) {
                    alert('Nomor Kontrak Factoring sudah terdaftar di daftar. Silakan gunakan nomor lain atau edit entri yang ada.');
                    return;
                }

                const payload = {
                    no_kontrak: no_kontrak,
                    nama_client: nama_client,
                    nilai_invoice: parseFloat(nilai_invoice),
                    nilai_pinjaman: parseFloat(nilai_pinjaman),
                    nilai_bagi_hasil: parseFloat(nilai_bagi_hasil),
                    contract_date: contract_date,
                    due_date: due_date,
                    dokumen_invoice_file: dokumen_invoice_file,
                    dokumen_kontrak_file: dokumen_kontrak_file,
                    dokumen_so_file: dokumen_so_file,
                    dokumen_bast_file: dokumen_bast_file,
                };

                if (editInvoiceIndex >= 0) {
                    factoringData[editInvoiceIndex] = payload;
                    editInvoiceIndex = -1;
                } else {
                    factoringData.push(payload);
                }

                modalInstance.hide();
                renderFactoringTable();
            }
        }
        function renderPOFinancingTable() {
            const tbody = $('#poFinancingTable tbody');
            tbody.empty();
            poFinancingData.forEach(function(po, idx) {
                const row = `<tr>
                    <td>${idx + 1}</td>
                    <td>${po.no_kontrak}</td>
                    <td>${po.nama_client}</td>
                    <td>Rp. ${numberWithThousandSeparator(po.nilai_invoice)}</td>
                    <td>Rp. ${numberWithThousandSeparator(po.nilai_pinjaman)}</td>
                    <td>Rp. ${numberWithThousandSeparator(po.nilai_bagi_hasil)}</td>
                    <td>${po.contract_date || ''}</td>
                    <td>${po.due_date || ''}</td>
                    <td>${po.dokumen_kontrak_file ? po.dokumen_kontrak_file.name : ''}</td>
                    <td>${po.dokumen_so_file ? po.dokumen_so_file.name : ''}</td>
                    <td>${po.dokumen_bast_file ? po.dokumen_bast_file.name : ''}</td>
                    <td>${po.dokumen_lainnya_file ? po.dokumen_lainnya_file.name : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit-po" data-idx="${idx}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-remove-po" data-idx="${idx}">Hapus</button>
                    </td>
                </tr>`;
                tbody.append(row);
            });

            // handle remove
            $('.btn-remove-po').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                poFinancingData.splice(idx, 1);
                renderPOFinancingTable();
            });

            // handle edit
            $('.btn-edit-po').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                const po = poFinancingData[idx];
                if (!po) return;

                $('#modal_no_kontrak_po').val(po.no_kontrak);
                $('#modal_nama_client_po').val(po.nama_client);
                window.setCleaveValue(document.getElementById('modal_nilai_invoice_po'), 'Rp ' + numberWithThousandSeparator(po.nilai_invoice || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_pinjaman_po'), 'Rp ' + numberWithThousandSeparator(po.nilai_pinjaman || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_bagi_hasil_po'), 'Rp ' + numberWithThousandSeparator(po.nilai_bagi_hasil || 0));
                $('#modal_contract_date_po').val(po.contract_date || '');
                $('#modal_due_date_po').val(po.due_date || '');

                // File inputs cannot be pre-filled
                $('#modal_dokumen_kontrak_po').val('');
                $('#modal_dokumen_so_po').val('');
                $('#modal_dokumen_bast_po').val('');
                $('#modal_dokumen_lainnya_po').val('');

                editInvoiceIndex = idx;
                modalInstance.show();
            });
        }

        function renderInstallmentTable() {
            const tbody = $('#installmentTable tbody');
            tbody.empty();
            installmentData.forEach(function(inst, idx) {
                const row = `<tr>
                    <td>${idx + 1}</td>
                    <td>${inst.no_invoice}</td>
                    <td>${inst.nama_client || ''}</td>
                    <td>Rp. ${numberWithThousandSeparator(inst.nilai_invoice || 0)}</td>
                    <td>${inst.invoice_date || ''}</td>
                    <td>${inst.nama_barang || ''}</td>
                    <td>${inst.dokumen_invoice_file ? inst.dokumen_invoice_file.name : ''}</td>
                    <td>${inst.dokumen_lainnya_file ? inst.dokumen_lainnya_file.name : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit-installment" data-idx="${idx}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-remove-installment" data-idx="${idx}">Hapus</button>
                    </td>
                </tr>`;
                tbody.append(row);
            });

            // After rendering, update header nominal from details so totals follow details
            try {
                updateNominalFromDetails();
            } catch (e) {
                console.error('renderInstallmentTable updateNominalFromDetails error', e);
            }

            // handle remove
            $('.btn-remove-installment').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                installmentData.splice(idx, 1);
                renderInstallmentTable();
            });

            // handle edit
            $('.btn-edit-installment').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                const inst = installmentData[idx];
                if (!inst) return;

                $('#modal_no_invoice_inst').val(inst.no_invoice);
                $('#modal_nama_client_inst').val(inst.nama_client);
                window.setCleaveValue(document.getElementById('modal_nilai_invoice_inst'), 'Rp ' + numberWithThousandSeparator(inst.nilai_invoice || 0));
                $('#modal_invoice_date_inst').val(inst.invoice_date || '');
                $('#modal_nama_barang').val(inst.nama_barang || '');

                // File inputs cannot be pre-filled
                $('#modal_dokumen_invoice_inst').val('');
                $('#modal_dokumen_lainnya_inst').val('');

                editInvoiceIndex = idx;
                modalInstance.show();
            });
        }

        function renderFactoringTable() {
            const tbody = $('#factoringTable tbody');
            tbody.empty();
            factoringData.forEach(function(f, idx) {
                const row = `<tr>
                    <td>${idx + 1}</td>
                    <td>${f.no_kontrak}</td>
                    <td>${f.nama_client || ''}</td>
                    <td>Rp. ${numberWithThousandSeparator(f.nilai_invoice || 0)}</td>
                    <td>Rp. ${numberWithThousandSeparator(f.nilai_pinjaman || 0)}</td>
                    <td>Rp. ${numberWithThousandSeparator(f.nilai_bagi_hasil || 0)}</td>
                    <td>${f.contract_date || ''}</td>
                    <td>${f.due_date || ''}</td>
                    <td>${f.dokumen_invoice_file ? f.dokumen_invoice_file.name : ''}</td>
                    <td>${f.dokumen_kontrak_file ? f.dokumen_kontrak_file.name : ''}</td>
                    <td>${f.dokumen_so_file ? f.dokumen_so_file.name : ''}</td>
                    <td>${f.dokumen_bast_file ? f.dokumen_bast_file.name : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit-factoring" data-idx="${idx}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-remove-factoring" data-idx="${idx}">Hapus</button>
                    </td>
                </tr>`;
                tbody.append(row);
            });

            // handle remove
            $('.btn-remove-factoring').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                factoringData.splice(idx, 1);
                renderFactoringTable();
            });

            // handle edit
            $('.btn-edit-factoring').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                const f = factoringData[idx];
                if (!f) return;

                $('#modal_no_kontrak_fact').val(f.no_kontrak);
                $('#modal_nama_client_fact').val(f.nama_client);
                window.setCleaveValue(document.getElementById('modal_nilai_invoice_fact'), 'Rp ' + numberWithThousandSeparator(f.nilai_invoice || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_pinjaman_fact'), 'Rp ' + numberWithThousandSeparator(f.nilai_pinjaman || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_bagi_hasil_fact'), 'Rp ' + numberWithThousandSeparator(f.nilai_bagi_hasil || 0));
                $('#modal_contract_date_fact').val(f.contract_date || '');
                $('#modal_due_date_fact').val(f.due_date || '');

                // reset file inputs
                $('#modal_dokumen_invoice_fact').val('');
                $('#modal_dokumen_kontrak_fact').val('');
                $('#modal_dokumen_so_fact').val('');
                $('#modal_dokumen_bast_fact').val('');

                editInvoiceIndex = idx;
                modalInstance.show();
            });
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

            const form = document.getElementById('formPeminjaman');
            const fd = new FormData(form);

            // Basic client-side check depending on selected jenis pembiayaan
            if (currentJenisPembiayaan === 'Invoice Financing' && invoiceFinancingData.length === 0) {
                if (!confirm('Anda belum menambahkan invoice, lanjutkan menyimpan?')) return;
            }
            if (currentJenisPembiayaan === 'PO Financing' && poFinancingData.length === 0) {
                if (!confirm('Anda belum menambahkan kontrak PO, lanjutkan menyimpan?')) return;
            }

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
            if (sumberEksternalVal) fd.set('id_instansi', sumberEksternalVal);

            // normalize sumber pembiayaan value to lowercase
            const sumberVal = $('input[name="sumber_pembiayaan"]:checked').val();
            if (sumberVal) fd.set('sumber_pembiayaan', sumberVal.toLowerCase());

            // Attach detail arrays and files depending on jenis pembiayaan
            let postUrl = '{{ route('peminjaman.invoice.store') }}';
            if (currentJenisPembiayaan === 'Invoice Financing') {
                fd.set('invoices', JSON.stringify(invoiceFinancingData.map(i => ({
                    no_invoice: i.no_invoice,
                    nama_client: i.nama_client,
                    nilai_invoice: i.nilai_invoice,
                    nilai_pinjaman: i.nilai_pinjaman,
                    nilai_bagi_hasil: i.nilai_bagi_hasil,
                    invoice_date: i.invoice_date,
                    due_date: i.due_date
                }))));

                invoiceFinancingData.forEach(function(inv, idx) {
                    if (inv.dokumen_invoice_file) fd.append(`files[${idx}][dokumen_invoice]`, inv.dokumen_invoice_file);
                    if (inv.dokumen_kontrak_file) fd.append(`files[${idx}][dokumen_kontrak]`, inv.dokumen_kontrak_file);
                    if (inv.dokumen_so_file) fd.append(`files[${idx}][dokumen_so]`, inv.dokumen_so_file);
                    if (inv.dokumen_bast_file) fd.append(`files[${idx}][dokumen_bast]`, inv.dokumen_bast_file);
                });
                postUrl = '{{ route('peminjaman.invoice.store') }}';
            } else if (currentJenisPembiayaan === 'PO Financing') {
                // Append each detail as form fields so PHP/Laravel parses them as array
                poFinancingData.forEach(function(p, idx) {
                    fd.append(`details[${idx}][no_kontrak]`, p.no_kontrak || '');
                    fd.append(`details[${idx}][nama_client]`, p.nama_client || '');
                    fd.append(`details[${idx}][nilai_invoice]`, normalizeNumericForServer(p.nilai_invoice || 0));
                    fd.append(`details[${idx}][nilai_pinjaman]`, normalizeNumericForServer(p.nilai_pinjaman || 0));
                    fd.append(`details[${idx}][nilai_bagi_hasil]`, normalizeNumericForServer(p.nilai_bagi_hasil || 0));
                    fd.append(`details[${idx}][kontrak_date]`, p.contract_date || '');
                    fd.append(`details[${idx}][due_date]`, p.due_date || '');

                    // Append files for each PO detail using keys like details[0][dokumen_kontrak]
                    if (p.dokumen_kontrak_file) fd.append(`details[${idx}][dokumen_kontrak]`, p.dokumen_kontrak_file);
                    if (p.dokumen_so_file) fd.append(`details[${idx}][dokumen_so]`, p.dokumen_so_file);
                    if (p.dokumen_bast_file) fd.append(`details[${idx}][dokumen_bast]`, p.dokumen_bast_file);
                    if (p.dokumen_lainnya_file) fd.append(`details[${idx}][dokumen_lainnya]`, p.dokumen_lainnya_file);
                });

                postUrl = '{{ route('peminjaman.po.store') }}';
            }

            // Factoring append
            if (currentJenisPembiayaan === 'Factoring') {
                factoringData.forEach(function(f, idx) {
                    fd.append(`details[${idx}][no_kontrak]`, f.no_kontrak || '');
                    fd.append(`details[${idx}][nama_client]`, f.nama_client || '');
                    fd.append(`details[${idx}][nilai_invoice]`, normalizeNumericForServer(f.nilai_invoice || 0));
                    fd.append(`details[${idx}][nilai_pinjaman]`, normalizeNumericForServer(f.nilai_pinjaman || 0));
                    fd.append(`details[${idx}][nilai_bagi_hasil]`, normalizeNumericForServer(f.nilai_bagi_hasil || 0));
                    fd.append(`details[${idx}][kontrak_date]`, f.contract_date || '');
                    fd.append(`details[${idx}][due_date]`, f.due_date || '');

                    if (f.dokumen_invoice_file) fd.append(`details[${idx}][dokumen_invoice]`, f.dokumen_invoice_file);
                    if (f.dokumen_kontrak_file) fd.append(`details[${idx}][dokumen_kontrak]`, f.dokumen_kontrak_file);
                    if (f.dokumen_so_file) fd.append(`details[${idx}][dokumen_so]`, f.dokumen_so_file);
                    if (f.dokumen_bast_file) fd.append(`details[${idx}][dokumen_bast]`, f.dokumen_bast_file);
                });

                // compute header totals if not provided
                let sumInvoice = 0;
                factoringData.forEach(function(f) { sumInvoice += Number(normalizeNumericForServer(f.nilai_invoice || 0) || 0); });
                fd.set('total_nominal_yang_dialihkan', normalizeNumericForServer(sumInvoice));
                // compute total_bagi_hasil as 2% fallback
                const bagi = Math.round(sumInvoice * 0.02 * 100) / 100;
                fd.set('total_bagi_hasil', normalizeNumericForServer(bagi));
                fd.set('pembayaran_total', normalizeNumericForServer(sumInvoice + bagi));
                if (!fd.get('status') || fd.get('status') === '') fd.set('status', 'submitted');

                postUrl = '{{ route('peminjaman.factoring.store') }}';
            }

            // Installment append
            if (currentJenisPembiayaan === 'Installment') {
                installmentData.forEach(function(it, idx) {
                    fd.append(`details[${idx}][no_invoice]`, it.no_invoice || '');
                    fd.append(`details[${idx}][nama_client]`, it.nama_client || '');
                    fd.append(`details[${idx}][nilai_invoice]`, normalizeNumericForServer(it.nilai_invoice || 0));
                    fd.append(`details[${idx}][invoice_date]`, it.invoice_date || '');
                    fd.append(`details[${idx}][nama_barang]`, it.nama_barang || '');

                    if (it.dokumen_invoice_file) fd.append(`details[${idx}][dokumen_invoice]`, it.dokumen_invoice_file);
                    if (it.dokumen_lainnya_file) fd.append(`details[${idx}][dokumen_lainnya]`, it.dokumen_lainnya_file);
                });

                postUrl = '{{ route('peminjaman.installment.store') }}';
            }

            // If posting Installment, ensure header computed fields are present
            if (currentJenisPembiayaan === 'Installment') {
                const nominalElForSubmit = document.getElementById('nominal_pinjaman');
                const computed = (nominalElForSubmit && nominalElForSubmit._computed) ? nominalElForSubmit._computed : null;
                if (computed) {
                    fd.set('total_pinjaman', computed.totalPinjaman);
                    fd.set('tenor_pembayaran', computed.tenor);
                    fd.set('persentase_bagi_hasil', computed.persentase_bagi_hasil);
                    fd.set('pps', computed.pps);
                    fd.set('sfinance', computed.sfinance);
                    fd.set('total_pembayaran', computed.total_pembayaran);
                    fd.set('yang_harus_dibayarkan', computed.yang_harus_dibayarkan);
                } else {
                    // fallback: if not computed on client, rely on server to compute
                }
            }

            // If posting PO Financing, ensure required header fields exist
            if (currentJenisPembiayaan === 'PO Financing') {
                // no_kontrak is required by server; prefer explicit header input if present, otherwise use first detail
                if (!fd.get('no_kontrak')) {
                    if (poFinancingData.length > 0 && poFinancingData[0].no_kontrak) {
                        fd.set('no_kontrak', poFinancingData[0].no_kontrak);
                    }
                }

                // Ensure total_pinjaman is present; compute from details if empty
                let totalPinjamanValue = fd.get('total_pinjaman');
                if (!totalPinjamanValue || totalPinjamanValue === '') {
                    let sum = 0;
                    poFinancingData.forEach(function(p) {
                        sum += Number(normalizeNumericForServer(p.nilai_pinjaman || 0) || 0);
                    });
                    fd.set('total_pinjaman', normalizeNumericForServer(sum));
                } else {
                    fd.set('total_pinjaman', normalizeNumericForServer(totalPinjamanValue));
                }

                // Ensure total_bagi_hasil and pembayaran_total exist (we set total_bagi_hasil earlier from form if present)
                const existingBagi = fd.get('total_bagi_hasil') || 0;
                if (!existingBagi || existingBagi === '0') {
                    // compute as 2% of total_pinjaman
                    const tp = Number(normalizeNumericForServer(fd.get('total_pinjaman') || 0));
                    const bagi = Math.round(tp * 0.02 * 100) / 100;
                    fd.set('total_bagi_hasil', normalizeNumericForServer(bagi));
                    fd.set('pembayaran_total', normalizeNumericForServer(tp + bagi));
                } else {
                    // ensure pembayaran_total exists
                    if (!fd.get('pembayaran_total') || fd.get('pembayaran_total') === '0') {
                        const tp = Number(normalizeNumericForServer(fd.get('total_pinjaman') || 0));
                        const bagi = Number(normalizeNumericForServer(fd.get('total_bagi_hasil') || 0));
                        fd.set('pembayaran_total', normalizeNumericForServer(tp + bagi));
                    }
                }

                // Ensure status provided
                if (!fd.get('status')) fd.set('status', 'submitted');
            }

            // send via AJAX
            $.ajax({
                url: postUrl,
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
                        if (resp.success) {
                            // prefer to include explicit type to avoid ambiguous numeric ids across tables
                            const mapType = {
                                'Invoice Financing': 'invoice',
                                'PO Financing': 'po',
                                'Installment': 'installment',
                                'Factoring': 'factoring'
                            };

                            let targetId = null;
                            if (resp.data && resp.data.id_invoice_financing) targetId = resp.data.id_invoice_financing;
                            else if (resp.id) targetId = resp.id;

                            if (targetId) {
                                const t = mapType[currentJenisPembiayaan] || null;
                                const url = '/peminjaman/' + targetId + (t ? ('?type=' + t) : '');
                                window.location.href = url;
                            } else {
                                window.location.href = '/peminjaman';
                            }
                        } else {
                            alert('Gagal: ' + (resp.message || 'Unknown error'));
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
                        // disable past dates by setting minDate to today
                        const today = new Date();
                        today.setHours(0,0,0,0);
                        elem.flatpickr({
                            monthSelectorType: 'static',
                            dateFormat: 'd/m/Y',
                            altInput: true,
                            altFormat: 'j F Y',
                            minDate: today,
                            disable: [function(date) {
                                const d = new Date(date);
                                d.setHours(0,0,0,0);
                                // disable dates before today
                                return d < today;
                            }]
                        });
                });
            }
        }
    </script>
@endpush
