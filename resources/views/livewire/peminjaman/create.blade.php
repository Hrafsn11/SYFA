@extends('layouts.app')

@section('content')
    <div>
        <div>
            <a href="{{ route('peminjaman') }}" class="btn btn-outline-primary mb-4">
                <i class="tf-icons ti ti-arrow-left me-1"></i>
                Kembali
            </a>
            <h4 class="fw-bold">{{ isset($isEdit) && $isEdit ? 'Edit' : 'Menu' }} Pengajuan Peminjaman {{ isset($pengajuan) ? '- ' . ($pengajuan->nomor_peminjaman ?? 'Draft') : '' }}</h4>
        </div>

        <form action="{{ isset($isEdit) && $isEdit ? route('peminjaman.update', $pengajuan->id_pengajuan_peminjaman) : '#' }}" 
              method="POST" enctype="multipart/form-data" id="formPeminjaman">
            @csrf
            @if(isset($isEdit) && $isEdit)
                @method('PUT')
                <input type="hidden" name="id_debitur" value="{{ $pengajuan->id_debitur }}">
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control non-editable" id="nama_perusahaan"
                                name="nama_perusahaan"
                                value="{{ old('nama_perusahaan', optional($master)->nama ?? 'Techno Infinity') }}"
                                required readonly tabindex="-1">
                        </div>
                    </div>
                    <div class="card border-1 mb-3 shadow-none" id="cardSumberPembiayaan">
                        <div class="card-body">
                            <div class="col-md-12 mb-3">
                                <label class="form-label mb-2">Sumber Pembiayaan</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input name="sumber_pembiayaan" class="form-check-input sumber-pembiayaan-radio"
                                            type="radio" value="Eksternal" id="sumber_eksternal" 
                                            {{ (isset($pengajuan) && strtolower($pengajuan->sumber_pembiayaan ?? '') == 'eksternal') || !isset($pengajuan) ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="sumber_eksternal">
                                            Eksternal
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input name="sumber_pembiayaan" class="form-check-input sumber-pembiayaan-radio"
                                            type="radio" value="Internal" id="sumber_internal" 
                                            {{ isset($pengajuan) && strtolower($pengajuan->sumber_pembiayaan ?? '') == 'internal' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="sumber_internal">
                                            Internal
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-2" id="divSumberEksternal" 
                                     style="display: {{ (isset($pengajuan) && strtolower($pengajuan->sumber_pembiayaan ?? '') == 'internal') ? 'none' : 'block' }};">
                                    <select id="select2Basic" name="sumber_eksternal_id" class="form-select"
                                        data-placeholder="Pilih Sumber Pembiayaan Eksternal">
                                        <option value="">Pilih Sumber Pembiayaan</option>
                                        @foreach ($sumber_eksternal as $sumber)
                                            @php
                                                // support both array and object representations
                                                if (is_array($sumber)) {
                                                    $percent =
                                                        $sumber['persentase'] ??
                                                        ($sumber['bagi_hasil'] ??
                                                            ($sumber['persentase_bagi_hasil'] ?? 2));
                                                    $label = $sumber['nama'] ?? ($sumber['nama_instansi'] ?? '');
                                                    $val = $sumber['id'] ?? '';
                                                } else {
                                                    $percent =
                                                        $sumber->persentase ??
                                                        ($sumber->bagi_hasil ?? ($sumber->persentase_bagi_hasil ?? 2));
                                                    $label = $sumber->nama ?? ($sumber->nama_instansi ?? '');
                                                    $val = $sumber->id ?? ($sumber->id_instansi ?? '');
                                                }
                                            @endphp
                                            <option value="{{ $val }}" data-percent="{{ $percent }}" 
                                                {{ isset($pengajuan) && $pengajuan->id_instansi == $val ? 'selected' : '' }}>
                                                {{ $label }}</option>
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
                                    <select class="form-select non-editable select-non-editable" id="selectBank"
                                        name="nama_bank" required disabled aria-disabled="true"
                                        data-selected="{{ old('nama_bank', isset($isEdit) && $isEdit ? ($pengajuan->nama_bank ?? optional($master)->nama_bank) : optional($master)->nama_bank) }}">
                                        <option value="">Pilih Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{ $bank }}"
                                                {{ old('nama_bank', isset($isEdit) && $isEdit ? ($pengajuan->nama_bank ?? optional($master)->nama_bank) : optional($master)->nama_bank) == $bank ? 'selected' : '' }}>
                                                {{ $bank }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="no_rekening" class="form-label">No. Rekening</label>
                                    <input type="text" class="form-control non-editable" id="no_rekening"
                                        name="no_rekening" value="{{ old('no_rekening', isset($isEdit) && $isEdit ? ($pengajuan->no_rekening ?? optional($master)->no_rek) : optional($master)->no_rek) }}"
                                        placeholder="Masukkan No. Rekening" required readonly tabindex="-1">
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label for="nama_rekening" class="form-label">Nama Rekening</label>
                                    <input type="text" class="form-control" id="nama_rekening" name="nama_rekening"
                                        value="{{ old('nama_rekening', isset($isEdit) && $isEdit ? $pengajuan->nama_rekening : '') }}"
                                        placeholder="Masukkan Nama Rekening">
                                </div>
                            </div>

                            <div class="row mb-3" id="rowLampiranSID">
                                <div class="col-md-6">
                                    <label for="lampiran_sid" class="form-label">Lampiran SID</label>
                                    <input class="form-control" type="file" id="lampiran_sid" name="lampiran_sid">
                                    <div class="form-text mb-3">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls,
                                        png,
                                        rar, zip)
                                        @if(isset($isEdit) && $isEdit && $pengajuan->lampiran_sid)
                                            <br><small>File saat ini: <a href="{{ Storage::url($pengajuan->lampiran_sid) }}" target="_blank">Lihat File</a></small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="nilai_kol" class="form-label">Nilai KOL</label>
                                    <input type="text" class="form-control non-editable" id="nilai_kol" name="nilai_kol"
                                        value="{{ old('nilai_kol', isset($isEdit) && $isEdit ? $pengajuan->nilai_kol : (optional($master->kol)->kol ?? '')) }}"
                                        placeholder="Nilai KOL" readonly tabindex="-1">
                                </div>
                                <div class="">
                                    <label for="tujuan_pembiayaan" class="form-label">Tujuan Pembiayaan</label>
                                    <input type="text" class="form-control" id="defaultFormControlInput"
                                        name="tujuan_pembiayaan" 
                                        value="{{ old('tujuan_pembiayaan', isset($isEdit) && $isEdit ? $pengajuan->tujuan_pembiayaan : '') }}"
                                        placeholder="Tujuan Pembiayaan"
                                        aria-describedby="defaultFormControlHelp" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label mb-2">Jenis Pembiayaan</label>
                                    <div class="d-flex">
                                        <div class="form-check me-3">
                                            <input name="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio"
                                                type="radio" value="Invoice Financing" id="invoice_financing" 
                                                {{ old('jenis_pembiayaan', isset($isEdit) && $isEdit ? $pengajuan->jenis_pembiayaan : 'Invoice Financing') == 'Invoice Financing' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="invoice_financing">
                                                Invoice Financing
                                            </label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input name="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio"
                                                type="radio" value="PO Financing" id="po_financing"
                                                {{ old('jenis_pembiayaan', isset($isEdit) && $isEdit ? $pengajuan->jenis_pembiayaan : '') == 'PO Financing' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="po_financing">
                                                PO Financing
                                            </label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input name="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio"
                                                type="radio" value="Installment" id="installment"
                                                {{ old('jenis_pembiayaan', isset($isEdit) && $isEdit ? $pengajuan->jenis_pembiayaan : '') == 'Installment' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="installment">
                                                Installment
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input name="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio"
                                                type="radio" value="Factoring" id="factoring"
                                                {{ old('jenis_pembiayaan', isset($isEdit) && $isEdit ? $pengajuan->jenis_pembiayaan : '') == 'Factoring' ? 'checked' : '' }}
                                                required>
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
                                        <input type="text" class="form-control input-rupiah non-editable" id="total_pinjaman"
                                            name="total_pinjaman" placeholder="Rp 0" readonly tabindex="-1">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="bs-datepicker-tanggal-pencairan" class="form-label">Harapan Tanggal
                                            Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bs-datepicker"
                                                placeholder="DD/MM/YYYY" id="bs-datepicker-tanggal-pencairan"
                                                name="tanggal_pencairan" />
                                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="total_bagi_hasil" class="form-label">Total Bagi Hasil</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-rupiah non-editable"
                                                id="total_bagi_hasil" name="total_bagi_hasil" placeholder="2%" readonly
                                                tabindex="-1">
                                            <span class="input-group-text">/Bulan</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="bs-datepicker-tanggal-pembayaran" class="form-label">Rencana Tanggal
                                            Pembayaran</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control bs-datepicker"
                                                placeholder="DD/MM/YYYY" id="bs-datepicker-tanggal-pembayaran"
                                                name="tanggal_pembayaran" />
                                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pembayaran_total" class="form-label">Pembayaran Total</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-rupiah non-editable"
                                                id="pembayaran_total" name="pembayaran_total" placeholder="" readonly
                                                tabindex="-1">
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
                                            name="nominal_pinjaman" placeholder="Rp 0" readonly>
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
                                                disabled>
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
                                placeholder="Masukkan Catatan">{{ isset($pengajuan) ? $pengajuan->catatan_lainnya : '' }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        @if(isset($isEdit) && $isEdit)
                            <a href="{{ route('peminjaman') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-x me-1"></i>
                                Batal
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-{{ isset($isEdit) && $isEdit ? 'device-floppy' : 'check' }} me-1"></i>
                            <span class="align-middle">{{ isset($isEdit) && $isEdit ? 'Update' : 'Simpan' }} Data</span>
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
        let invoiceFinancingData = @json($invoice_financing_data ?? []);
        let poFinancingData = @json($po_financing_data ?? []);
        let installmentData = @json($installment_data ?? []);
        let factoringData = @json($factoring_data ?? []);
        let currentJenisPembiayaan = '{{ isset($pengajuan) ? $pengajuan->jenis_pembiayaan : "Invoice Financing" }}';
        let currentSumberPembiayaan = '{{ isset($pengajuan) ? $pengajuan->sumber_pembiayaan : "Eksternal" }}';
        const isEdit = {{ isset($isEdit) && $isEdit ? 'true' : 'false' }};
        let modalInstance;

        $(document).ready(function() {
            modalInstance = new bootstrap.Modal(document.getElementById('modalTambahInvoice'));

            initSelect2Elements();

            initBootstrapDatepicker();

            // Initialize Cleave.js untuk format rupiah
            initCleaveRupiah();

            // Initialize file validation
            initFileValidation();

            // Pre-fill data in edit mode
            @if(isset($isEdit) && $isEdit && isset($pengajuan))
                // Fill total pinjaman
                @if($pengajuan->total_pinjaman)
                    window.setCleaveValue(document.getElementById('total_pinjaman'), 'Rp {{ number_format($pengajuan->total_pinjaman, 0, ',', '.') }}');
                @endif
                
                // Fill harapan tanggal pencairan
                @if($pengajuan->harapan_tanggal_pencairan)
                    $('#bs-datepicker-tanggal-pencairan').val('{{ \Carbon\Carbon::parse($pengajuan->harapan_tanggal_pencairan)->format('d/m/Y') }}');
                @endif
                
                // Fill total bagi hasil
                @if($pengajuan->total_bagi_hasil)
                    window.setCleaveValue(document.getElementById('total_bagi_hasil'), 'Rp {{ number_format($pengajuan->total_bagi_hasil, 0, ',', '.') }}');
                @endif
                
                // Fill rencana tanggal pembayaran
                @if($pengajuan->rencana_tgl_pembayaran)
                    $('#bs-datepicker-tanggal-pembayaran').val('{{ \Carbon\Carbon::parse($pengajuan->rencana_tgl_pembayaran)->format('d/m/Y') }}');
                @endif
                
                // Fill pembayaran total
                @if($pengajuan->pembayaran_total)
                    window.setCleaveValue(document.getElementById('pembayaran_total'), 'Rp {{ number_format($pengajuan->pembayaran_total, 0, ',', '.') }}');
                @endif
                
                console.log('Jenis Pembiayaan:', '{{ $pengajuan->jenis_pembiayaan ?? 'null' }}');
                console.log('Tenor Pembayaran dari DB:', '{{ $pengajuan->tenor_pembayaran ?? 'null' }}');
                
                // Fill installment specific fields
                @if($pengajuan->jenis_pembiayaan == 'Installment')
                    console.log('✅ Masuk kondisi Installment');
                    @if($pengajuan->tenor_pembayaran)
                        console.log('Setting tenor_pembayaran:', '{{ $pengajuan->tenor_pembayaran }}');
                        $('#tenorPembayaran').val('{{ $pengajuan->tenor_pembayaran }}').trigger('change');
                        console.log('Tenor after set:', $('#tenorPembayaran').val());
                    @else
                        console.log('❌ tenor_pembayaran kosong');
                    @endif
                    @if($pengajuan->persentase_bagi_hasil)
                        $('#persentase_bagi_hasil').val('{{ $pengajuan->persentase_bagi_hasil }}');
                    @endif
                    @if($pengajuan->pps)
                        window.setCleaveValue(document.getElementById('pps'), 'Rp {{ number_format($pengajuan->pps, 0, ',', '.') }}');
                    @endif
                    @if($pengajuan->s_finance)
                        window.setCleaveValue(document.getElementById('s_finance'), 'Rp {{ number_format($pengajuan->s_finance, 0, ',', '.') }}');
                    @endif
                    @if($pengajuan->yang_harus_dibayarkan)
                        window.setCleaveValue(document.getElementById('yang_harus_dibayarkan'), 'Rp {{ number_format($pengajuan->yang_harus_dibayarkan, 0, ',', '.') }}');
                    @endif
                @endif
                
                // Fill factoring specific fields
                @if($pengajuan->jenis_pembiayaan == 'Factoring' && $pengajuan->total_nominal_yang_dialihkan)
                    window.setCleaveValue(document.getElementById('total_nominal_yang_dialihkan'), 'Rp {{ number_format($pengajuan->total_nominal_yang_dialihkan, 0, ',', '.') }}');
                @endif
            @endif

            const totalPinjamanEl = document.getElementById('total_pinjaman');
            const totalBagiHasilEl = document.getElementById('total_bagi_hasil');
            const pembayaranTotalEl = document.getElementById('pembayaran_total');

            // Total Pinjaman and Total Bagi Hasil are now auto-calculated from modal data
            // No manual calculation needed here, they are updated by updateTotalFrom* functions

            // Installment live calculation - Store in window for global access
            window.nominalPinjamanEl = document.getElementById('nominal_pinjaman');
            window.tenorEl = document.getElementById('tenorPembayaran');

            // If Select2 is used, bind its change event explicitly so recalcInstallment runs
            try {
                // use jQuery in case select2 wraps the select
                $('#tenorPembayaran').on('change.select2', function() {
                    try {
                        window.recalcInstallment();
                    } catch (e) {
                        console.error(e);
                    }
                });
            } catch (e) {
                // ignore if jQuery/select2 not available
            }

            // Define formatCurrency in window scope for global access
            window.formatCurrency = function(value) {
                if (value === null || value === undefined || value === '') return '';
                return 'Rp. ' + numberWithThousandSeparator(Number(value).toFixed(0));
            };

            // Define recalcInstallment in window scope for global access
            window.recalcInstallment = function() {
                try {
                    console.log('🧮 recalcInstallment called');
                    const raw = window.getCleaveRawValue(window.nominalPinjamanEl) || 0;
                    const totalPinjamanVal = Number(raw);
                    console.log('💵 Total Pinjaman Value:', totalPinjamanVal);
                    // default tenor to 3 months if user hasn't chosen one yet
                    const tenorVal = window.tenorEl ? (parseInt(window.tenorEl.value) || 3) : 3;
                    console.log('📅 Tenor Value:', tenorVal);

                    // Business rules fixed: bagi hasil 10%
                    const bagiPercent = 10.0;
                    const totalBagi = Math.round((totalPinjamanVal * (bagiPercent / 100)) * 100) / 100;
                    const ppsAmount = Math.round(totalBagi * 0.40 * 100) / 100;
                    const sfinanceAmount = Math.round(totalBagi * 0.60 * 100) / 100;
                    const totalPembayaranVal = Math.round((totalPinjamanVal + totalBagi) * 100) / 100;
                    const monthlyPay = tenorVal > 0 ? Math.round((totalPembayaranVal / tenorVal) * 100) / 100 :
                        totalPembayaranVal;

                    console.log('📊 Calculations:', {
                        totalBagi,
                        ppsAmount,
                        sfinanceAmount,
                        totalPembayaranVal,
                        monthlyPay
                    });

                    // Update UI display fields (they are disabled inputs)
                    const elPpsDebit = document.getElementById('pps_debit');
                    const elPpsPercentage = document.getElementById('pps_percentage');
                    const elSFinance = document.getElementById('s_finance');
                    const elTotalPembayaran = document.getElementById('total_pembayaran_installment');
                    const elBayarPerBulan = document.getElementById('bayar_per_bulan');

                    if (elPpsDebit) elPpsDebit.value =
                        `${bagiPercent}% (Rp. ${numberWithThousandSeparator(totalBagi)})`;
                    if (elPpsPercentage) elPpsPercentage.value =
                        `40% (Rp. ${numberWithThousandSeparator(ppsAmount)})`;
                    if (elSFinance) elSFinance.value = `60% (Rp. ${numberWithThousandSeparator(sfinanceAmount)})`;
                    if (elTotalPembayaran) elTotalPembayaran.value = window.formatCurrency(totalPembayaranVal);
                    if (elBayarPerBulan) elBayarPerBulan.value = window.formatCurrency(monthlyPay);

                    console.log('✅ UI fields updated successfully');

                    // store computed values on element for submit use
                    if (window.nominalPinjamanEl) {
                        window.nominalPinjamanEl._computed = {
                            totalPinjaman: totalPinjamanVal,
                            tenor: tenorVal,
                            persentase_bagi_hasil: bagiPercent,
                            pps: ppsAmount,
                            sfinance: sfinanceAmount,
                            total_pembayaran: totalPembayaranVal,
                            yang_harus_dibayarkan: monthlyPay
                        };
                    }
                } catch (err) {
                    console.error('recalcInstallment error', err);
                }
            };

            // Update nominal_pinjaman from installmentData (sum nilai_invoice) and recalc
            // Define in window scope for global access
            window.updateNominalFromDetails = function() {
                try {
                    console.log('🔄 updateNominalFromDetails called, installmentData:', installmentData);
                    if (!window.nominalPinjamanEl) {
                        console.warn('⚠️ nominalPinjamanEl not found!');
                        return;
                    }
                    let sum = 0;
                    installmentData.forEach(function(it) {
                        const v = Number(normalizeNumericForServer(it.nilai_invoice || 0)) || 0;
                        sum += v;
                    });
                    console.log('💰 Sum of nilai_invoice:', sum);
                    if (typeof window.setCleaveValue === 'function') {
                        window.setCleaveValue(window.nominalPinjamanEl, 'Rp ' + numberWithThousandSeparator(sum));
                    } else {
                        window.nominalPinjamanEl.value = sum;
                    }
                    // ensure tenor defaults to 3 if not set and force recalc so UI updates
                    try {
                        if (window.tenorEl) {
                            // set via jQuery so Select2 UI updates and triggers change
                            try {
                                $(window.tenorEl).val('3').trigger('change');
                            } catch (_) {
                                window.tenorEl.value = '3';
                            }
                        }
                    } catch (e) {
                        // ignore
                    }
                    window.recalcInstallment();
                } catch (err) {
                    console.error('updateNominalFromDetails error', err);
                }
            };

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

            // Initialize form based on current jenis pembiayaan (important for edit mode)
            handleJenisPembiayaanChange(currentJenisPembiayaan);

            // Render existing data tables for edit mode
            @if(isset($isEdit) && $isEdit)
                console.log('📝 Edit mode - Current Jenis Pembiayaan:', currentJenisPembiayaan);
                console.log('📊 Data counts:', {
                    invoice: invoiceFinancingData.length,
                    po: poFinancingData.length,
                    installment: installmentData.length,
                    factoring: factoringData.length
                });
                
                if (currentJenisPembiayaan === 'Invoice Financing' && invoiceFinancingData.length > 0) {
                    console.log('✅ Rendering Invoice Financing table');
                    renderInvoiceTables();
                } else if (currentJenisPembiayaan === 'PO Financing' && poFinancingData.length > 0) {
                    console.log('✅ Rendering PO Financing table');
                    renderPOFinancingTable();
                } else if (currentJenisPembiayaan === 'Installment' && installmentData.length > 0) {
                    console.log('✅ Rendering Installment table');
                    renderInstallmentTable();
                } else if (currentJenisPembiayaan === 'Factoring' && factoringData.length > 0) {
                    console.log('✅ Rendering Factoring table');
                    renderFactoringTable();
                } else {
                    console.warn('⚠️ No matching table to render or no data');
                }
            @endif

            // Run recalcInstallment on load to populate fields if nominal already exists (only for Installment)
            setTimeout(function() {
                try {
                    if (currentJenisPembiayaan === 'Installment') {
                        // If there are installment details present on load, ensure nominal is populated
                        window.updateNominalFromDetails();
                        // and ensure recalc to reflect default tenor
                        window.recalcInstallment();
                    }
                } catch (e) {
                    console.error('Initial recalcInstallment error', e);
                }
            }, 200);

            // Handle Sumber Pembiayaan Radio
            $('.sumber-pembiayaan-radio').on('change', function() {
                if ($(this).val() === 'Eksternal') {
                    $('#divSumberEksternal').slideDown();
                } else {
                    $('#divSumberEksternal').slideUp();
                }
                // No need to recalc - totals are calculated from modal data
            });

            // When external sumber selection changes, update getBagiPercent for modal calculations
            $('#select2Basic').on('change', function() {
                // The getBagiPercent() function will return updated percentage
                // This affects modal calculations, not main form totals
                try {} catch (e) {
                    // suppressed
                }
                try {} catch (e) {}
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

            // For edit mode, render tables from loaded data
            if (isEdit) {
                renderInvoiceTables();
                // Update totals on load based on current jenis pembiayaan
                setTimeout(function() {
                    if (currentJenisPembiayaan === 'Invoice Financing') {
                        updateTotalFromInvoiceFinancing();
                    } else if (currentJenisPembiayaan === 'PO Financing') {
                        updateTotalFromPOFinancing();
                    } else if (currentJenisPembiayaan === 'Factoring') {
                        updateTotalFromFactoring();
                    } else if (currentJenisPembiayaan === 'Installment') {
                        // Update nominal and recalculate for Installment
                        window.updateNominalFromDetails();
                    }
                }, 300);
            }

        });

        let editInvoiceIndex = -1;

        const FileValidator = {
            maxSize: 2 * 1024 * 1024, // 2MB in bytes
            allowedTypes: ['pdf', 'docx', 'doc', 'xls', 'xlsx', 'png', 'rar', 'zip'],
            
            validate(file) {
                if (!file) return { valid: false, message: 'Tidak ada file yang dipilih' };
                
                // Check file size
                if (file.size > this.maxSize) {
                    return { 
                        valid: false, 
                        message: `Ukuran file melebihi 2 MB<br>File Anda: ${(file.size / (1024 * 1024)).toFixed(2)} MB` 
                    };
                }
                
                // Check file type
                const ext = file.name.split('.').pop().toLowerCase();
                if (!this.allowedTypes.includes(ext)) {
                    return { 
                        valid: false, 
                        message: `Tipe file tidak diizinkan!<br>Format yang diperbolehkan: ${this.allowedTypes.join(', ')}` 
                    };
                }
                
                return { valid: true };
            },
            
            showError(message, inputId = null) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi File Gagal!',
                    html: message,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary waves-effect waves-light'
                    },
                    buttonsStyling: false
                });
                
                if (inputId) {
                    const input = document.getElementById(inputId);
                    if (input) input.value = '';
                }
            }
        };

        // Initialize file validation on all modal file inputs
        function initFileValidation() {
            const fileInputIds = [
                'modal_dokumen_invoice', 'modal_dokumen_kontrak', 'modal_dokumen_so', 'modal_dokumen_bast',
                'modal_dokumen_kontrak_po', 'modal_dokumen_so_po', 'modal_dokumen_bast_po', 'modal_dokumen_lainnya_po',
                'modal_dokumen_invoice_inst', 'modal_dokumen_lainnya_inst',
                'modal_dokumen_invoice_fact', 'modal_dokumen_kontrak_fact', 'modal_dokumen_so_fact', 'modal_dokumen_bast_fact'
            ];
            
            fileInputIds.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const result = FileValidator.validate(file);
                            if (!result.valid) {
                                FileValidator.showError(result.message, id);
                            }
                        }
                    });
                }
            });
        }

        function saveInvoiceData() {
            if (currentJenisPembiayaan === 'Invoice Financing') {
                const index = editInvoiceIndex >= 0 ? editInvoiceIndex : invoiceFinancingData.length;
                const no_invoice = $('#modal_no_invoice').val();
                const nama_client = $('#modal_nama_client').val();
                const nilai_invoice = window.getCleaveRawValue(document.getElementById('modal_nilai_invoice')) || 0;
                const nilai_pinjaman = window.getCleaveRawValue(document.getElementById('modal_nilai_pinjaman')) || 0;
                const defaultPercent = getBagiPercent();
                const nilai_bagi_hasil = window.getCleaveRawValue(document.getElementById('modal_nilai_bagi_hasil')) || Math
                    .round(nilai_pinjaman * (defaultPercent / 100));
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
                const duplicateIndex = invoiceFinancingData.findIndex(function(it) {
                    return it.no_invoice === no_invoice;
                });
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
                    // Preserve existing document paths if no new file uploaded
                    const existing = invoiceFinancingData[editInvoiceIndex];
                    if (!dokumen_invoice_file && existing.dokumen_invoice) payload.dokumen_invoice = existing.dokumen_invoice;
                    if (!dokumen_kontrak_file && existing.dokumen_kontrak) payload.dokumen_kontrak = existing.dokumen_kontrak;
                    if (!dokumen_so_file && existing.dokumen_so) payload.dokumen_so = existing.dokumen_so;
                    if (!dokumen_bast_file && existing.dokumen_bast) payload.dokumen_bast = existing.dokumen_bast;
                    
                    invoiceFinancingData[editInvoiceIndex] = payload;
                    editInvoiceIndex = -1;
                } else {
                    invoiceFinancingData.push(payload);
                }

                modalInstance.hide();
                renderInvoiceTables();
                updateTotalFromInvoiceFinancing(); // Update totals after saving
            } else if (currentJenisPembiayaan === 'PO Financing') {
                const index = editInvoiceIndex >= 0 ? editInvoiceIndex : poFinancingData.length;
                const no_kontrak = $('#modal_no_kontrak_po').val();
                const nama_client = $('#modal_nama_client_po').val();
                const nilai_invoice = window.getCleaveRawValue(document.getElementById('modal_nilai_invoice_po')) || 0;
                const nilai_pinjaman = window.getCleaveRawValue(document.getElementById('modal_nilai_pinjaman_po')) || 0;
                const defaultPercentPo = getBagiPercent();
                const nilai_bagi_hasil = window.getCleaveRawValue(document.getElementById('modal_nilai_bagi_hasil_po')) ||
                    Math.round(nilai_pinjaman * (defaultPercentPo / 100));
                let kontrak_date = $('#modal_contract_date_po').val();
                let due_date = $('#modal_due_date_po').val();

                kontrak_date = convertDMYToISO(kontrak_date);
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
                
                // Validate kontrak_date is required
                if (!kontrak_date) {
                    alert('Kontrak Date wajib diisi');
                    return;
                }

                // Duplicate check for PO contract numbers (block on save)
                const dupPo = poFinancingData.findIndex(function(it) {
                    return it.no_kontrak === no_kontrak;
                });
                if (dupPo !== -1 && (editInvoiceIndex === -1 || dupPo !== editInvoiceIndex)) {
                    alert(
                        'Nomor Kontrak PO sudah terdaftar di daftar. Silakan gunakan nomor lain atau edit entri yang ada.');
                    return;
                }

                const payload = {
                    no_kontrak: no_kontrak,
                    nama_client: nama_client,
                    nilai_invoice: parseFloat(nilai_invoice),
                    nilai_pinjaman: parseFloat(nilai_pinjaman),
                    nilai_bagi_hasil: parseFloat(nilai_bagi_hasil),
                    kontrak_date: kontrak_date,
                    due_date: due_date,
                    dokumen_kontrak_file: dokumen_kontrak_file,
                    dokumen_so_file: dokumen_so_file,
                    dokumen_bast_file: dokumen_bast_file,
                    dokumen_lainnya_file: dokumen_lainnya_file,
                };

                if (editInvoiceIndex >= 0) {
                    // Preserve existing document paths if no new file uploaded
                    const existing = poFinancingData[editInvoiceIndex];
                    if (!dokumen_kontrak_file && existing.dokumen_kontrak) payload.dokumen_kontrak = existing.dokumen_kontrak;
                    if (!dokumen_so_file && existing.dokumen_so) payload.dokumen_so = existing.dokumen_so;
                    if (!dokumen_bast_file && existing.dokumen_bast) payload.dokumen_bast = existing.dokumen_bast;
                    if (!dokumen_lainnya_file && existing.dokumen_lainnya) payload.dokumen_lainnya = existing.dokumen_lainnya;
                    
                    poFinancingData[editInvoiceIndex] = payload;
                    editInvoiceIndex = -1;
                } else {
                    poFinancingData.push(payload);
                }

                modalInstance.hide();
                renderPOFinancingTable();
                updateTotalFromPOFinancing(); // Update totals after saving
            } else if (currentJenisPembiayaan === 'Installment') {
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

                // Nama barang wajib untuk Installment
                if (!nama_barang || String(nama_barang).trim() === '') {
                    // mark input invalid and show message
                    $('#modal_nama_barang').addClass('is-invalid');
                    alert('Nama Barang wajib diisi untuk Installment.');
                    return;
                } else {
                    $('#modal_nama_barang').removeClass('is-invalid');
                }

                // Duplicate check for installment invoices
                const dupInst = installmentData.findIndex(function(it) {
                    return it.no_invoice === no_invoice;
                });
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
                    // Preserve existing document paths if no new file uploaded
                    const existing = installmentData[editInvoiceIndex];
                    if (!dokumen_invoice_file && existing.dokumen_invoice) payload.dokumen_invoice = existing.dokumen_invoice;
                    if (!dokumen_lainnya_file && existing.dokumen_lainnya) payload.dokumen_lainnya = existing.dokumen_lainnya;
                    
                    installmentData[editInvoiceIndex] = payload;
                    editInvoiceIndex = -1;
                } else {
                    installmentData.push(payload);
                }

                modalInstance.hide();
                renderInstallmentTable();
                
                // IMPORTANT: Update nominal pinjaman and recalculate after adding installment data
                window.updateNominalFromDetails();
            } else if (currentJenisPembiayaan === 'Factoring') {
                const index = editInvoiceIndex >= 0 ? editInvoiceIndex : factoringData.length;
                const no_kontrak = $('#modal_no_kontrak_fact').val();
                const nama_client = $('#modal_nama_client_fact').val();
                const nilai_invoice = window.getCleaveRawValue(document.getElementById('modal_nilai_invoice_fact')) || 0;
                const nilai_pinjaman = window.getCleaveRawValue(document.getElementById('modal_nilai_pinjaman_fact')) || 0;
                const nilai_bagi_hasil = window.getCleaveRawValue(document.getElementById('modal_nilai_bagi_hasil_fact')) ||
                    Math.round(nilai_pinjaman * 0.02);
                let kontrak_date = $('#modal_contract_date_fact').val();
                let due_date = $('#modal_due_date_fact').val();

                kontrak_date = convertDMYToISO(kontrak_date);
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
                
                // Validate kontrak_date is required
                if (!kontrak_date) {
                    alert('Kontrak Date wajib diisi');
                    return;
                }

                // Duplicate check for Factoring contract numbers (block on save)
                const dupFact = factoringData.findIndex(function(it) {
                    return it.no_kontrak === no_kontrak;
                });
                if (dupFact !== -1 && (editInvoiceIndex === -1 || dupFact !== editInvoiceIndex)) {
                    alert(
                        'Nomor Kontrak Factoring sudah terdaftar di daftar. Silakan gunakan nomor lain atau edit entri yang ada.');
                    return;
                }

                const payload = {
                    no_kontrak: no_kontrak,
                    nama_client: nama_client,
                    nilai_invoice: parseFloat(nilai_invoice),
                    nilai_pinjaman: parseFloat(nilai_pinjaman),
                    nilai_bagi_hasil: parseFloat(nilai_bagi_hasil),
                    kontrak_date: kontrak_date,
                    due_date: due_date,
                    dokumen_invoice_file: dokumen_invoice_file,
                    dokumen_kontrak_file: dokumen_kontrak_file,
                    dokumen_so_file: dokumen_so_file,
                    dokumen_bast_file: dokumen_bast_file,
                };

                if (editInvoiceIndex >= 0) {
                    // Preserve existing document paths if no new file uploaded
                    const existing = factoringData[editInvoiceIndex];
                    if (!dokumen_invoice_file && existing.dokumen_invoice) payload.dokumen_invoice = existing.dokumen_invoice;
                    if (!dokumen_kontrak_file && existing.dokumen_kontrak) payload.dokumen_kontrak = existing.dokumen_kontrak;
                    if (!dokumen_so_file && existing.dokumen_so) payload.dokumen_so = existing.dokumen_so;
                    if (!dokumen_bast_file && existing.dokumen_bast) payload.dokumen_bast = existing.dokumen_bast;
                    
                    factoringData[editInvoiceIndex] = payload;
                    editInvoiceIndex = -1;
                } else {
                    factoringData.push(payload);
                }

                modalInstance.hide();
                renderFactoringTable();
                updateTotalFromFactoring(); // Update totals after saving
            }
        }

        // Update total pinjaman and bagi hasil from Invoice Financing data
        function updateTotalFromInvoiceFinancing() {
            try {
                let totalPinjaman = 0;
                let totalBagiHasil = 0;

                invoiceFinancingData.forEach(function(inv) {
                    totalPinjaman += Number(inv.nilai_pinjaman) || 0;
                    totalBagiHasil += Number(inv.nilai_bagi_hasil) || 0;
                });

                const totalPinjamanEl = document.getElementById('total_pinjaman');
                const totalBagiHasilEl = document.getElementById('total_bagi_hasil');
                const pembayaranTotalEl = document.getElementById('pembayaran_total');

                if (totalPinjamanEl) {
                    window.setCleaveValue(totalPinjamanEl, 'Rp ' + totalPinjaman.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }
                if (totalBagiHasilEl) {
                    window.setCleaveValue(totalBagiHasilEl, 'Rp ' + totalBagiHasil.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }
                if (pembayaranTotalEl) {
                    const pembayaranTotal = totalPinjaman + totalBagiHasil;
                    window.setCleaveValue(pembayaranTotalEl, 'Rp ' + pembayaranTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }

                console.log('Invoice Financing Totals Updated:', { totalPinjaman, totalBagiHasil });
            } catch (e) {
                console.error('Error updating Invoice Financing totals:', e);
            }
        }

        // Update total pinjaman and bagi hasil from PO Financing data
        function updateTotalFromPOFinancing() {
            try {
                let totalPinjaman = 0;
                let totalBagiHasil = 0;

                poFinancingData.forEach(function(po) {
                    totalPinjaman += Number(po.nilai_pinjaman) || 0;
                    totalBagiHasil += Number(po.nilai_bagi_hasil) || 0;
                });

                const totalPinjamanEl = document.getElementById('total_pinjaman');
                const totalBagiHasilEl = document.getElementById('total_bagi_hasil');
                const pembayaranTotalEl = document.getElementById('pembayaran_total');

                if (totalPinjamanEl) {
                    window.setCleaveValue(totalPinjamanEl, 'Rp ' + totalPinjaman.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }
                if (totalBagiHasilEl) {
                    window.setCleaveValue(totalBagiHasilEl, 'Rp ' + totalBagiHasil.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }
                if (pembayaranTotalEl) {
                    const pembayaranTotal = totalPinjaman + totalBagiHasil;
                    window.setCleaveValue(pembayaranTotalEl, 'Rp ' + pembayaranTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }

                console.log('PO Financing Totals Updated:', { totalPinjaman, totalBagiHasil });
            } catch (e) {
                console.error('Error updating PO Financing totals:', e);
            }
        }

        // Update total pinjaman and bagi hasil from Factoring data
        function updateTotalFromFactoring() {
            try {
                let totalPinjaman = 0;
                let totalBagiHasil = 0;

                factoringData.forEach(function(fact) {
                    totalPinjaman += Number(fact.nilai_pinjaman) || 0;
                    totalBagiHasil += Number(fact.nilai_bagi_hasil) || 0;
                });

                const totalPinjamanEl = document.getElementById('total_pinjaman');
                const totalBagiHasilEl = document.getElementById('total_bagi_hasil');
                const pembayaranTotalEl = document.getElementById('pembayaran_total');

                if (totalPinjamanEl) {
                    window.setCleaveValue(totalPinjamanEl, 'Rp ' + totalPinjaman.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }
                if (totalBagiHasilEl) {
                    window.setCleaveValue(totalBagiHasilEl, 'Rp ' + totalBagiHasil.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }
                if (pembayaranTotalEl) {
                    const pembayaranTotal = totalPinjaman + totalBagiHasil;
                    window.setCleaveValue(pembayaranTotalEl, 'Rp ' + pembayaranTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }

                console.log('Factoring Totals Updated:', { totalPinjaman, totalBagiHasil });
            } catch (e) {
                console.error('Error updating Factoring totals:', e);
            }
        }

        function renderPOFinancingTable() {
            const tbody = $('#poFinancingTable tbody');
            tbody.empty();
            poFinancingData.forEach(function(po, idx) {
                const dokKontrak = getDocumentDisplay(po.dokumen_kontrak_file, po.dokumen_kontrak);
                const dokSo = getDocumentDisplay(po.dokumen_so_file, po.dokumen_so);
                const dokBast = getDocumentDisplay(po.dokumen_bast_file, po.dokumen_bast);
                const dokLainnya = getDocumentDisplay(po.dokumen_lainnya_file, po.dokumen_lainnya);
                
                const row = `<tr>
                    <td>${idx + 1}</td>
                    <td>${po.no_kontrak}</td>
                    <td>${po.nama_client}</td>
                    <td>Rp. ${numberWithThousandSeparator(po.nilai_invoice)}</td>
                    <td>Rp. ${numberWithThousandSeparator(po.nilai_pinjaman)}</td>
                    <td>Rp. ${numberWithThousandSeparator(po.nilai_bagi_hasil)}</td>
                    <td>${po.kontrak_date || ''}</td>
                    <td>${po.due_date || ''}</td>
                    <td>${dokKontrak ? '<span class="badge bg-label-success">' + dokKontrak + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokSo ? '<span class="badge bg-label-success">' + dokSo + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokBast ? '<span class="badge bg-label-success">' + dokBast + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokLainnya ? '<span class="badge bg-label-success">' + dokLainnya + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-outline-primary btn-edit-po" data-idx="${idx}" title="Edit"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>`;
                tbody.append(row);
            });

            // handle edit
            $('.btn-edit-po').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                const po = poFinancingData[idx];
                if (!po) return;

                // Open modal with correct form
                openModal('PO Financing');
                
                // Update modal title for edit
                $('#modalTitle').text('Edit PO Financing');
                
                // Fill data after a short delay to ensure modal form is visible
                setTimeout(function() {
                    $('#modal_no_kontrak_po').val(po.no_kontrak);
                    $('#modal_nama_client_po').val(po.nama_client);
                    window.setCleaveValue(document.getElementById('modal_nilai_invoice_po'), 'Rp ' +
                        numberWithThousandSeparator(po.nilai_invoice || 0));
                    window.setCleaveValue(document.getElementById('modal_nilai_pinjaman_po'), 'Rp ' +
                        numberWithThousandSeparator(po.nilai_pinjaman || 0));
                    window.setCleaveValue(document.getElementById('modal_nilai_bagi_hasil_po'), 'Rp ' +
                        numberWithThousandSeparator(po.nilai_bagi_hasil || 0));
                    $('#modal_contract_date_po').val(po.kontrak_date || '');
                    $('#modal_due_date_po').val(po.due_date || '');

                    // Show existing files with proper format
                    const dokKontrak = getDocumentDisplay(po.dokumen_kontrak_file, po.dokumen_kontrak);
                    const dokSo = getDocumentDisplay(po.dokumen_so_file, po.dokumen_so);
                    const dokBast = getDocumentDisplay(po.dokumen_bast_file, po.dokumen_bast);
                    const dokLainnya = getDocumentDisplay(po.dokumen_lainnya_file, po.dokumen_lainnya);
                    
                    if (dokKontrak) {
                        $('#modal_dokumen_kontrak_po').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokKontrak + '</small>');
                    }
                    if (dokSo) {
                        $('#modal_dokumen_so_po').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokSo + '</small>');
                    }
                    if (dokBast) {
                        $('#modal_dokumen_bast_po').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokBast + '</small>');
                    }
                    if (dokLainnya) {
                        $('#modal_dokumen_lainnya_po').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokLainnya + '</small>');
                    }

                    editInvoiceIndex = idx;
                    initModalBootstrapDatepicker();
                }, 150);
            });
        }

        function renderInstallmentTable() {
            const tbody = $('#installmentTable tbody');
            tbody.empty();
            installmentData.forEach(function(inst, idx) {
                const dokInvoice = getDocumentDisplay(inst.dokumen_invoice_file, inst.dokumen_invoice);
                const dokLainnya = getDocumentDisplay(inst.dokumen_lainnya_file, inst.dokumen_lainnya);
                
                const row = `<tr>
                    <td>${idx + 1}</td>
                    <td>${inst.no_invoice}</td>
                    <td>${inst.nama_client || ''}</td>
                    <td>Rp. ${numberWithThousandSeparator(inst.nilai_invoice || 0)}</td>
                    <td>${inst.invoice_date || ''}</td>
                    <td>${inst.nama_barang || ''}</td>
                    <td>${dokInvoice ? '<span class="badge bg-label-success">' + dokInvoice + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokLainnya ? '<span class="badge bg-label-success">' + dokLainnya + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-outline-primary btn-edit-installment" data-idx="${idx}" title="Edit"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>`;
                tbody.append(row);
            });

            // After rendering, update header nominal from details so totals follow details
            try {
                window.updateNominalFromDetails();
            } catch (e) {
                console.error('renderInstallmentTable updateNominalFromDetails error', e);
            }

            // handle edit
            $('.btn-edit-installment').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                const inst = installmentData[idx];
                if (!inst) return;

                // Open modal with correct form
                openModal('Installment');
                
                // Update modal title for edit
                $('#modalTitle').text('Edit Invoice Penjamin');
                
                // Fill data after a short delay
                setTimeout(function() {
                    $('#modal_no_invoice_inst').val(inst.no_invoice);
                    $('#modal_nama_client_inst').val(inst.nama_client || '');
                    window.setCleaveValue(document.getElementById('modal_nilai_invoice_inst'), 'Rp ' +
                        numberWithThousandSeparator(inst.nilai_invoice || 0));
                    $('#modal_invoice_date_inst').val(inst.invoice_date || '');
                    $('#modal_nama_barang').val(inst.nama_barang || '');

                    // Show existing files with proper format
                    const dokInvoice = getDocumentDisplay(inst.dokumen_invoice_file, inst.dokumen_invoice);
                    const dokLainnya = getDocumentDisplay(inst.dokumen_lainnya_file, inst.dokumen_lainnya);
                    
                    if (dokInvoice) {
                        $('#modal_dokumen_invoice_inst').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokInvoice + '</small>');
                    }
                    if (dokLainnya) {
                        $('#modal_dokumen_lainnya_inst').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokLainnya + '</small>');
                    }

                    editInvoiceIndex = idx;
                    initModalBootstrapDatepicker();
                }, 150);
            });
        }

        function getDocumentDisplay(fileObj, pathStr) {
            if (fileObj && fileObj.name) {
                return fileObj.name;
            } else if (pathStr && typeof pathStr === 'string') {
                const parts = pathStr.split('/');
                return parts[parts.length - 1] || pathStr;
            }
            return '';
        }

        function renderFactoringTable() {
            const tbody = $('#factoringTable tbody');
            tbody.empty();
            factoringData.forEach(function(f, idx) {
                const dokInvoice = getDocumentDisplay(f.dokumen_invoice_file, f.dokumen_invoice);
                const dokKontrak = getDocumentDisplay(f.dokumen_kontrak_file, f.dokumen_kontrak);
                const dokSo = getDocumentDisplay(f.dokumen_so_file, f.dokumen_so);
                const dokBast = getDocumentDisplay(f.dokumen_bast_file, f.dokumen_bast);
                
                const row = `<tr>
                    <td>${idx + 1}</td>
                    <td>${f.no_kontrak}</td>
                    <td>${f.nama_client || ''}</td>
                    <td>Rp. ${numberWithThousandSeparator(f.nilai_invoice || 0)}</td>
                    <td>Rp. ${numberWithThousandSeparator(f.nilai_pinjaman || 0)}</td>
                    <td>Rp. ${numberWithThousandSeparator(f.nilai_bagi_hasil || 0)}</td>
                    <td>${f.kontrak_date || ''}</td>
                    <td>${f.due_date || ''}</td>
                    <td>${dokInvoice ? '<span class="badge bg-label-success">' + dokInvoice + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokKontrak ? '<span class="badge bg-label-success">' + dokKontrak + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokSo ? '<span class="badge bg-label-success">' + dokSo + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokBast ? '<span class="badge bg-label-success">' + dokBast + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-outline-primary btn-edit-factoring" data-idx="${idx}" title="Edit"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>`;
                tbody.append(row);
            });

            // handle edit
            $('.btn-edit-factoring').on('click', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                const f = factoringData[idx];
                if (!f) return;

                $('.modal-form-content').hide();
                $('#formModalFactoring').show();
                $('#modalTitle').text('Edit Kontrak Penjamin');

                $('#modal_no_kontrak_fact').val(f.no_kontrak);
                $('#modal_nama_client_fact').val(f.nama_client);
                window.setCleaveValue(document.getElementById('modal_nilai_invoice_fact'), 'Rp ' +
                    numberWithThousandSeparator(f.nilai_invoice || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_pinjaman_fact'), 'Rp ' +
                    numberWithThousandSeparator(f.nilai_pinjaman || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_bagi_hasil_fact'), 'Rp ' +
                    numberWithThousandSeparator(f.nilai_bagi_hasil || 0));
                $('#modal_contract_date_fact').val(f.kontrak_date || '');
                $('#modal_due_date_fact').val(f.due_date || '');

                // reset file inputs
                $('#modal_dokumen_invoice_fact').val('');
                $('#modal_dokumen_kontrak_fact').val('');
                $('#modal_dokumen_so_fact').val('');
                $('#modal_dokumen_bast_fact').val('');

                const dokInvoice = getDocumentDisplay(f.dokumen_invoice_file, f.dokumen_invoice);
                const dokKontrak = getDocumentDisplay(f.dokumen_kontrak_file, f.dokumen_kontrak);
                const dokSo = getDocumentDisplay(f.dokumen_so_file, f.dokumen_so);
                const dokBast = getDocumentDisplay(f.dokumen_bast_file, f.dokumen_bast);
                
                $('#modal_dokumen_invoice_fact').parent().find('.existing-file-info').remove();
                if (dokInvoice) {
                    $('#modal_dokumen_invoice_fact').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokInvoice + '</small>');
                }
                
                $('#modal_dokumen_kontrak_fact').parent().find('.existing-file-info').remove();
                if (dokKontrak) {
                    $('#modal_dokumen_kontrak_fact').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokKontrak + '</small>');
                }
                
                $('#modal_dokumen_so_fact').parent().find('.existing-file-info').remove();
                if (dokSo) {
                    $('#modal_dokumen_so_fact').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokSo + '</small>');
                }
                
                $('#modal_dokumen_bast_fact').parent().find('.existing-file-info').remove();
                if (dokBast) {
                    $('#modal_dokumen_bast_fact').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokBast + '</small>');
                }

                editInvoiceIndex = idx;
                modalInstance.show();
                
                setTimeout(function() {
                    initModalBootstrapDatepicker();
                }, 100);
            });
        }

        function renderInvoiceTables() {
            // Render Invoice Financing Table tbody
            const tbody = $('#invoiceFinancingTable tbody');
            tbody.empty();
            invoiceFinancingData.forEach(function(inv, idx) {
                const dokInvoice = getDocumentDisplay(inv.dokumen_invoice_file, inv.dokumen_invoice);
                const dokKontrak = getDocumentDisplay(inv.dokumen_kontrak_file, inv.dokumen_kontrak);
                const dokSo = getDocumentDisplay(inv.dokumen_so_file, inv.dokumen_so);
                const dokBast = getDocumentDisplay(inv.dokumen_bast_file, inv.dokumen_bast);
                
                const row = `<tr>
                    <td>${idx+1}</td>
                    <td>${inv.no_invoice}</td>
                    <td>${inv.nama_client || ''}</td>
                    <td>Rp. ${numberWithThousandSeparator(inv.nilai_invoice || 0)}</td>
                    <td>Rp. ${numberWithThousandSeparator(inv.nilai_pinjaman || 0)}</td>
                    <td>Rp. ${numberWithThousandSeparator(inv.nilai_bagi_hasil || 0)}</td>
                    <td>${inv.invoice_date || ''}</td>
                    <td>${inv.due_date || ''}</td>
                    <td>${dokInvoice ? '<span class="badge bg-label-success">' + dokInvoice + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokKontrak ? '<span class="badge bg-label-success">' + dokKontrak + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokSo ? '<span class="badge bg-label-success">' + dokSo + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>${dokBast ? '<span class="badge bg-label-success">' + dokBast + '</span>' : '<span class="text-muted">-</span>'}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-outline-primary btn-edit-invoice" data-idx="${idx}" title="Edit"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>`;
                tbody.append(row);
            });

            // Note: removal is handled from the modal 'Hapus Data' button now.

            // handle edit - using event delegation for dynamically added elements
            $(document).on('click', '.btn-edit-invoice', function(e) {
                e.preventDefault();
                const idx = $(this).data('idx');
                const inv = invoiceFinancingData[idx];
                if (!inv) return;

                $('.modal-form-content').hide();
                $('#formModalInvoiceFinancing').show();
                $('#modalTitle').text('Edit Invoice Financing');

                $('#modal_no_invoice').val(inv.no_invoice);
                $('#modal_nama_client').val(inv.nama_client);
                window.setCleaveValue(document.getElementById('modal_nilai_invoice'), 'Rp ' +
                    numberWithThousandSeparator(inv.nilai_invoice || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_pinjaman'), 'Rp ' +
                    numberWithThousandSeparator(inv.nilai_pinjaman || 0));
                window.setCleaveValue(document.getElementById('modal_nilai_bagi_hasil'), 'Rp ' +
                    numberWithThousandSeparator(inv.nilai_bagi_hasil || 0));
                $('#modal_invoice_date').val(inv.invoice_date || '');
                $('#modal_due_date').val(inv.due_date || '');

                // Note: file inputs cannot be pre-filled for security reasons. Inform the user to re-upload if necessary.
                $('#modal_dokumen_invoice').val('');
                $('#modal_dokumen_kontrak').val('');
                $('#modal_dokumen_so').val('');
                $('#modal_dokumen_bast').val('');
                
                const dokInvoice = getDocumentDisplay(inv.dokumen_invoice_file, inv.dokumen_invoice);
                const dokKontrak = getDocumentDisplay(inv.dokumen_kontrak_file, inv.dokumen_kontrak);
                const dokSo = getDocumentDisplay(inv.dokumen_so_file, inv.dokumen_so);
                const dokBast = getDocumentDisplay(inv.dokumen_bast_file, inv.dokumen_bast);
                
                $('#modal_dokumen_invoice').parent().find('.existing-file-info').remove();
                if (dokInvoice) {
                    $('#modal_dokumen_invoice').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokInvoice + '</small>');
                }
                
                $('#modal_dokumen_kontrak').parent().find('.existing-file-info').remove();
                if (dokKontrak) {
                    $('#modal_dokumen_kontrak').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokKontrak + '</small>');
                }
                
                $('#modal_dokumen_so').parent().find('.existing-file-info').remove();
                if (dokSo) {
                    $('#modal_dokumen_so').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokSo + '</small>');
                }
                
                $('#modal_dokumen_bast').parent().find('.existing-file-info').remove();
                if (dokBast) {
                    $('#modal_dokumen_bast').after('<small class="existing-file-info text-success d-block mt-1"><i class="ti ti-file-check me-1"></i>File saat ini: ' + dokBast + '</small>');
                }

                editInvoiceIndex = idx;
                modalInstance.show();
                
                setTimeout(function() {
                    initModalBootstrapDatepicker();
                }, 100);
            });

            $('#btnHapusDataModal').off('click').on('click', function(e) {
                e.preventDefault();
                if (typeof editInvoiceIndex !== 'undefined' && editInvoiceIndex >= 0) {
                    if (!confirm('Yakin ingin menghapus data ini?')) return;
                    switch (currentJenisPembiayaan) {
                        case 'Invoice Financing':
                            invoiceFinancingData.splice(editInvoiceIndex, 1);
                            renderInvoiceTables();
                            updateTotalFromInvoiceFinancing(); // Update totals after delete
                            break;
                        case 'PO Financing':
                            poFinancingData.splice(editInvoiceIndex, 1);
                            renderPOFinancingTable();
                            updateTotalFromPOFinancing(); // Update totals after delete
                            break;
                        case 'Installment':
                            installmentData.splice(editInvoiceIndex, 1);
                            renderInstallmentTable();
                            break;
                        case 'Factoring':
                            factoringData.splice(editInvoiceIndex, 1);
                            renderFactoringTable();
                            updateTotalFromFactoring(); // Update totals after delete
                            break;
                    }
                    editInvoiceIndex = -1;
                }
                if (typeof modalInstance !== 'undefined' && modalInstance) modalInstance.hide();
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
            const tanggalPencairanRaw = $('#bs-datepicker-tanggal-pencairan').val();
            const tanggalPencairanISO = convertDMYToISO(tanggalPencairanRaw);
            if (tanggalPencairanISO) fd.set('harapan_tanggal_pencairan', tanggalPencairanISO);

            const tanggalPembayaranRaw = $('#bs-datepicker-tanggal-pembayaran').val();
            const tanggalPembayaranISO = convertDMYToISO(tanggalPembayaranRaw);
            if (tanggalPembayaranISO) fd.set('rencana_tgl_pembayaran', tanggalPembayaranISO);

            // Clean and set total_pinjaman (remove Rp prefix and formatting)
            const totalPinjamanRaw = window.getCleaveRawValue(document.getElementById('total_pinjaman')) || 0;
            fd.set('total_pinjaman', totalPinjamanRaw);

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
            // Set URL based on edit or create mode
            let postUrl = isEdit ? '{{ isset($pengajuan) ? route('peminjaman.update', $pengajuan->id_pengajuan_peminjaman) : '' }}' : '{{ route('peminjaman.store') }}';
            
            if (currentJenisPembiayaan === 'Invoice Financing') {
                // Append each invoice as form fields (same format as PO/Factoring)
                invoiceFinancingData.forEach(function(inv, idx) {
                    fd.append(`details[${idx}][no_invoice]`, inv.no_invoice || '');
                    fd.append(`details[${idx}][nama_client]`, inv.nama_client || '');
                    fd.append(`details[${idx}][nilai_invoice]`, normalizeNumericForServer(inv.nilai_invoice || 0));
                    fd.append(`details[${idx}][nilai_pinjaman]`, normalizeNumericForServer(inv.nilai_pinjaman || 0));
                    fd.append(`details[${idx}][nilai_bagi_hasil]`, normalizeNumericForServer(inv.nilai_bagi_hasil || 0));
                    fd.append(`details[${idx}][invoice_date]`, inv.invoice_date || '');
                    fd.append(`details[${idx}][due_date]`, inv.due_date || '');

                    if (inv.dokumen_invoice_file) fd.append(`details[${idx}][dokumen_invoice]`, inv.dokumen_invoice_file);
                    if (inv.dokumen_kontrak_file) fd.append(`details[${idx}][dokumen_kontrak]`, inv.dokumen_kontrak_file);
                    if (inv.dokumen_so_file) fd.append(`details[${idx}][dokumen_so]`, inv.dokumen_so_file);
                    if (inv.dokumen_bast_file) fd.append(`details[${idx}][dokumen_bast]`, inv.dokumen_bast_file);
                });
            } else if (currentJenisPembiayaan === 'PO Financing') {
                // Append each detail as form fields so PHP/Laravel parses them as array
                poFinancingData.forEach(function(p, idx) {
                    fd.append(`details[${idx}][no_kontrak]`, p.no_kontrak || '');
                    fd.append(`details[${idx}][nama_client]`, p.nama_client || '');
                    fd.append(`details[${idx}][nilai_invoice]`, normalizeNumericForServer(p.nilai_invoice ||
                        0));
                    fd.append(`details[${idx}][nilai_pinjaman]`, normalizeNumericForServer(p
                        .nilai_pinjaman || 0));
                    fd.append(`details[${idx}][nilai_bagi_hasil]`, normalizeNumericForServer(p
                        .nilai_bagi_hasil || 0));
                    fd.append(`details[${idx}][kontrak_date]`, p.kontrak_date || '');
                    fd.append(`details[${idx}][due_date]`, p.due_date || '');

                    // Append files for each PO detail using keys like details[0][dokumen_kontrak]
                    if (p.dokumen_kontrak_file) fd.append(`details[${idx}][dokumen_kontrak]`, p
                        .dokumen_kontrak_file);
                    if (p.dokumen_so_file) fd.append(`details[${idx}][dokumen_so]`, p.dokumen_so_file);
                    if (p.dokumen_bast_file) fd.append(`details[${idx}][dokumen_bast]`, p
                    .dokumen_bast_file);
                    if (p.dokumen_lainnya_file) fd.append(`details[${idx}][dokumen_lainnya]`, p
                        .dokumen_lainnya_file);
                });
                
                // Compute header totals for PO Financing only if not already set
                if (!fd.get('total_bagi_hasil') || fd.get('total_bagi_hasil') === '0') {
                    let sumBagiHasil = 0;
                    poFinancingData.forEach(function(p) {
                        sumBagiHasil += Number(normalizeNumericForServer(p.nilai_bagi_hasil || 0) || 0);
                    });
                    fd.set('total_bagi_hasil', normalizeNumericForServer(sumBagiHasil));
                }
                
                // Compute pembayaran_total if not already set
                if (!fd.get('pembayaran_total') || fd.get('pembayaran_total') === '0') {
                    const tp = Number(fd.get('total_pinjaman') || 0);
                    const bagi = Number(fd.get('total_bagi_hasil') || 0);
                    fd.set('pembayaran_total', normalizeNumericForServer(tp + bagi));
                }
            }

            // Factoring append
            if (currentJenisPembiayaan === 'Factoring') {
                // Force-set financing source to internal with 2% fixed rate
                fd.set('sumber_pembiayaan', 'internal');
                fd.set('id_instansi', '');
                fd.set('persentase_bagi_hasil', '2');

                factoringData.forEach(function(f, idx) {
                    fd.append(`details[${idx}][no_kontrak]`, f.no_kontrak || '');
                    fd.append(`details[${idx}][nama_client]`, f.nama_client || '');
                    fd.append(`details[${idx}][nilai_invoice]`, normalizeNumericForServer(f.nilai_invoice ||
                        0));
                    fd.append(`details[${idx}][nilai_pinjaman]`, normalizeNumericForServer(f
                        .nilai_pinjaman || 0));
                    fd.append(`details[${idx}][nilai_bagi_hasil]`, normalizeNumericForServer(f
                        .nilai_bagi_hasil || 0));
                    fd.append(`details[${idx}][kontrak_date]`, f.kontrak_date || '');
                    fd.append(`details[${idx}][due_date]`, f.due_date || '');

                    if (f.dokumen_invoice_file) fd.append(`details[${idx}][dokumen_invoice]`, f
                        .dokumen_invoice_file);
                    if (f.dokumen_kontrak_file) fd.append(`details[${idx}][dokumen_kontrak]`, f
                        .dokumen_kontrak_file);
                    if (f.dokumen_so_file) fd.append(`details[${idx}][dokumen_so]`, f.dokumen_so_file);
                    if (f.dokumen_bast_file) fd.append(`details[${idx}][dokumen_bast]`, f
                    .dokumen_bast_file);
                });

                // compute header totals if not provided
                let sumInvoice = 0;
                factoringData.forEach(function(f) {
                    sumInvoice += Number(normalizeNumericForServer(f.nilai_invoice || 0) || 0);
                });
                fd.set('total_nominal_yang_dialihkan', normalizeNumericForServer(sumInvoice));
                
                // compute total_bagi_hasil as 2% fallback only if not already set
                if (!fd.get('total_bagi_hasil') || fd.get('total_bagi_hasil') === '0') {
                    const bagi = Math.round(sumInvoice * 0.02 * 100) / 100;
                    fd.set('total_bagi_hasil', normalizeNumericForServer(bagi));
                }
                
                // compute pembayaran_total only if not already set
                if (!fd.get('pembayaran_total') || fd.get('pembayaran_total') === '0') {
                    const tp = Number(fd.get('total_pinjaman') || 0);
                    const bagi = Number(fd.get('total_bagi_hasil') || 0);
                    fd.set('pembayaran_total', normalizeNumericForServer(tp + bagi));
                }
                
                if (!fd.get('status') || fd.get('status') === '') fd.set('status', 'submitted');
            }

            // Installment append
            if (currentJenisPembiayaan === 'Installment') {
                installmentData.forEach(function(it, idx) {
                    fd.append(`details[${idx}][no_invoice]`, it.no_invoice || '');
                    fd.append(`details[${idx}][nama_client]`, it.nama_client || '');
                    fd.append(`details[${idx}][nilai_invoice]`, normalizeNumericForServer(it
                        .nilai_invoice || 0));
                    fd.append(`details[${idx}][invoice_date]`, it.invoice_date || '');
                    fd.append(`details[${idx}][nama_barang]`, it.nama_barang || '');

                    if (it.dokumen_invoice_file) fd.append(`details[${idx}][dokumen_invoice]`, it
                        .dokumen_invoice_file);
                    if (it.dokumen_lainnya_file) fd.append(`details[${idx}][dokumen_lainnya]`, it
                        .dokumen_lainnya_file);
                });
            }

            // If posting Installment, ensure header computed fields are present
            if (currentJenisPembiayaan === 'Installment') {
                // Force set sumber_pembiayaan to internal and persentase to 10%
                fd.set('sumber_pembiayaan', 'internal');
                fd.set('id_instansi', '');
                fd.set('persentase_bagi_hasil', '10');
                
                const nominalElForSubmit = document.getElementById('nominal_pinjaman');
                const computed = (nominalElForSubmit && nominalElForSubmit._computed) ? nominalElForSubmit
                    ._computed : null;
                if (computed) {
                    // For Installment, we ALWAYS use computed values as they are calculated from nominal_pinjaman
                    fd.set('total_pinjaman', normalizeNumericForServer(computed.totalPinjaman));
                    fd.set('tenor_pembayaran', computed.tenor);
                    fd.set('pps', normalizeNumericForServer(computed.pps));
                    fd.set('sfinance', normalizeNumericForServer(computed.sfinance));
                    fd.set('total_pembayaran', normalizeNumericForServer(computed.total_pembayaran));
                    fd.set('yang_harus_dibayarkan', normalizeNumericForServer(computed.yang_harus_dibayarkan));
                } else {
                    // fallback: if not computed on client, use the value from nominal_pinjaman field
                    // This preserves existing value during edit mode
                    if (nominalElForSubmit) {
                        const cleanNominal = window.getCleaveRawValue(nominalElForSubmit) || 0;
                        if (cleanNominal && cleanNominal !== '0') {
                            fd.set('total_pinjaman', normalizeNumericForServer(cleanNominal));
                        }
                    }
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
                if (!totalPinjamanValue || totalPinjamanValue === '' || totalPinjamanValue === '0') {
                    let sum = 0;
                    poFinancingData.forEach(function(p) {
                        sum += Number(normalizeNumericForServer(p.nilai_pinjaman || 0) || 0);
                    });
                    fd.set('total_pinjaman', normalizeNumericForServer(sum));
                }
                // Note: If totalPinjamanValue already exists and is not empty/zero, 
                // keep the value that was already set earlier (line 1563) - don't normalize again

                // Ensure total_bagi_hasil and pembayaran_total exist
                const existingBagi = fd.get('total_bagi_hasil') || 0;
                if (!existingBagi || existingBagi === '0') {
                    // compute as 2% of total_pinjaman
                    const tp = Number(fd.get('total_pinjaman') || 0);
                    const bagi = Math.round(tp * 0.02 * 100) / 100;
                    fd.set('total_bagi_hasil', normalizeNumericForServer(bagi));
                    fd.set('pembayaran_total', normalizeNumericForServer(tp + bagi));
                } else {
                    // ensure pembayaran_total exists
                    if (!fd.get('pembayaran_total') || fd.get('pembayaran_total') === '0') {
                        const tp = Number(fd.get('total_pinjaman') || 0);
                        const bagi = Number(fd.get('total_bagi_hasil') || 0);
                        fd.set('pembayaran_total', normalizeNumericForServer(tp + bagi));
                    }
                }

                // Ensure status provided
                if (!fd.get('status')) {
                    // In edit mode, preserve existing status; in create mode, set to 'submitted'
                    const defaultStatus = isEdit ? '{{ $pengajuan->status ?? "submitted" }}' : 'submitted';
                    fd.set('status', defaultStatus);
                }
                
                // Add _method for PUT request in edit mode
                if (isEdit) {
                    fd.append('_method', 'PUT');
                }
            }

            // send via AJAX
            $.ajax({
                url: postUrl,
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                beforeSend: function() {
                    // optional: show loader
                },
                success: function(resp) {
                    if (resp.success || resp.message) {
                        const message = isEdit ? 'Pengajuan pinjaman berhasil diupdate!' : 'Peminjaman berhasil disimpan!';
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route("peminjaman") }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: resp.message || 'Terjadi kesalahan',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    let msg = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        msg += ':\n' + Object.values(errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: msg,
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        function handleJenisPembiayaanChange(jenisPembiayaan) {
            console.log('🔄 handleJenisPembiayaanChange called with:', jenisPembiayaan);
            // Hide all tables first
            $('.financing-table').hide();

            if (jenisPembiayaan === 'Installment') {
                $('#formNonInstallment').hide();
                $('#formInstallment').show();
                $('#cardSumberPembiayaan').hide();
                $('#rowLampiranSID').hide();
                $('#installmentTable').show();
                // Ensure tenor default and recalc so monthly payment updates immediately
                try {
                    const tEl = document.getElementById('tenorPembayaran');
                    if (tEl && (!tEl.value || tEl.value === '')) {
                        tEl.value = '3';
                    }
                    // Update nominal from details (sum) and recalc
                    if (typeof window.updateNominalFromDetails === 'function') window.updateNominalFromDetails();
                    if (typeof window.recalcInstallment === 'function') window.recalcInstallment();
                } catch (e) {
                    // ignore
                }
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
                    console.log('📋 Showing Factoring table');
                    $('#labelTotalPinjaman').text('Total Nominal Yang Dialihkan');
                    $('#factoringTable').show();
                } else if (jenisPembiayaan === 'PO Financing') {
                    console.log('📋 Showing PO Financing table');
                    $('#labelTotalPinjaman').text('Total Pinjaman');
                    $('#poFinancingTable').show();
                } else if (jenisPembiayaan === 'Invoice Financing') {
                    console.log('📋 Showing Invoice Financing table');
                    $('#labelTotalPinjaman').text('Total Pinjaman');
                    $('#invoiceFinancingTable').show();
                } else {
                    console.warn('⚠️ Unknown jenis pembiayaan:', jenisPembiayaan);
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
            
            // Clear existing file info
            $('.existing-file-info').remove();
            
            // Reset edit index
            editInvoiceIndex = -1;

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

            setTimeout(function() {
                initModalBootstrapDatepicker();
                initCleaveRupiah(); 
                // Re-initialize event listeners after a slight delay to ensure Cleave is ready
                setTimeout(function() {
                    initModalBagiHasilCalculation();
                    console.log('Modal bagi hasil calculation initialized');
                }, 200);
            }, 100);

            modalInstance.show();
        }

        function initModalBagiHasilCalculation() {
            console.log('Setting up bagi hasil calculation listeners...');
            
            $('#modal_nilai_pinjaman').off('input keyup').on('input keyup', function() {
                try {
                    console.log('Invoice Financing nilai pinjaman changed');
                    const nilaiPinjaman = window.getCleaveRawValue(this) || 0;
                    console.log('Nilai Pinjaman:', nilaiPinjaman);
                    
                    const bagiPercent = window.getBagiPercent();
                    console.log('Bagi Percent:', bagiPercent);
                    
                    const nilaiBagiHasil = Math.round(nilaiPinjaman * (bagiPercent / 100));
                    console.log('Nilai Bagi Hasil:', nilaiBagiHasil);
                    
                    const bagiHasilEl = document.getElementById('modal_nilai_bagi_hasil');
                    if (bagiHasilEl) {
                        window.setCleaveValue(bagiHasilEl, 'Rp ' + nilaiBagiHasil.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        console.log('Bagi hasil updated for Invoice Financing');
                    }
                } catch (e) {
                    console.error('Error calculating Invoice Financing bagi hasil:', e);
                }
            });

            $('#modal_nilai_pinjaman_po').off('input keyup').on('input keyup', function() {
                try {
                    console.log('PO Financing nilai pinjaman changed');
                    const nilaiPinjaman = window.getCleaveRawValue(this) || 0;
                    const bagiPercent = window.getBagiPercent();
                    const nilaiBagiHasil = Math.round(nilaiPinjaman * (bagiPercent / 100));
                    
                    const bagiHasilEl = document.getElementById('modal_nilai_bagi_hasil_po');
                    if (bagiHasilEl) {
                        window.setCleaveValue(bagiHasilEl, 'Rp ' + nilaiBagiHasil.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        console.log('Bagi hasil updated for PO Financing');
                    }
                } catch (e) {
                    console.error('Error calculating PO Financing bagi hasil:', e);
                }
            });

            $('#modal_nilai_pinjaman_fact').off('input keyup').on('input keyup', function() {
                try {
                    console.log('Factoring nilai pinjaman changed');
                    const nilaiPinjaman = window.getCleaveRawValue(this) || 0;
                    const bagiPercent = window.getBagiPercent();
                    const nilaiBagiHasil = Math.round(nilaiPinjaman * (bagiPercent / 100));
                    
                    const bagiHasilEl = document.getElementById('modal_nilai_bagi_hasil_fact');
                    if (bagiHasilEl) {
                        window.setCleaveValue(bagiHasilEl, 'Rp ' + nilaiBagiHasil.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                        console.log('Bagi hasil updated for Factoring');
                    }
                } catch (e) {
                    console.error('Error calculating Factoring bagi hasil:', e);
                }
            });
            
            console.log('All bagi hasil listeners set up successfully');
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

                    // If server passed a preselected value, apply it so Select2 renders it
                    const preselected = $this.data('selected');
                    if (preselected) {
                        $this.val(preselected).trigger('change.select2');
                    }

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

        // Menggunakan pola Vuexy untuk Bootstrap Datepicker
        function initBootstrapDatepicker() {
            // Datepicker untuk tanggal pencairan (minimal 4 hari dari sekarang)
            const pencairanDatepicker = $('#bs-datepicker-tanggal-pencairan');
            if (pencairanDatepicker.length) {
                const minDate = new Date();
                minDate.setDate(minDate.getDate() + 4);
                
                pencairanDatepicker.datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    startDate: minDate,
                    orientation: 'bottom auto'
                });
            }

            // Datepicker untuk tanggal pembayaran (default)
            const pembayaranDatepicker = $('#bs-datepicker-tanggal-pembayaran');
            if (pembayaranDatepicker.length) {
                pembayaranDatepicker.datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    startDate: new Date(),
                    orientation: 'bottom auto'
                });
            }
        }

        // Init bootstrap datepicker untuk modal
        function initModalBootstrapDatepicker() {
            const modalDatepickers = $('.bs-datepicker-modal');
            if (modalDatepickers.length) {
                modalDatepickers.each(function() {
                    const $this = $(this);
                    // Destroy existing instance if any
                    if ($this.data('datepicker')) {
                        $this.datepicker('destroy');
                    }
                    // Initialize with config
                    $this.datepicker({
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                        todayHighlight: true,
                        startDate: new Date(),
                        orientation: 'bottom auto'
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

        // Normalize numeric values to plain number string for server (remove currency formatting)
        function normalizeNumericForServer(v) {
            if (v === null || typeof v === 'undefined') return '';
            if (typeof v === 'number') return v.toString();
            // If Cleave raw getter exists and returned a number-like string, keep digits and dot
            return String(v).toString().replace(/[^0-9\.\-]+/g, '');
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
