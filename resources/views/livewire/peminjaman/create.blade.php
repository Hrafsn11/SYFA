<div>
    <div>
        <a href="{{ route('peminjaman') }}" class="btn btn-outline-primary mb-4">
            <i class="tf-icons ti ti-arrow-left me-1"></i>
            Kembali
        </a>
        <h4 class="fw-bold">Menu Pengajuan Peminjaman</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg mb-3">
                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                    <input type="text" class="form-control" id="nama_perusahaan" value="Techno Infinity" disabled>
                </div>
            </div>
            <div class="card border-1 mb-3 shadow-none" id="cardSumberPembiayaan"
                style="display: {{ $jenis_pembiayaan === 'Installment' ? 'none' : 'block' }};">
                <div class="card-body">
                    <div class="col-md-12 mb-3">
                        <label class="form-label mb-2">Sumber Pembiayaan</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input name="sumber_pembiayaan" class="form-check-input" type="radio"
                                    value="Eksternal" id="sumber_eksternal" wire:model.live="sumber_pembiayaan">
                                <label class="form-check-label" for="sumber_eksternal">
                                    Eksternal
                                </label>
                            </div>
                            <div class="form-check">
                                <input name="sumber_pembiayaan" class="form-check-input" type="radio" value="Internal"
                                    id="sumber_internal" wire:model.live="sumber_pembiayaan">
                                <label class="form-check-label" for="sumber_internal">
                                    Internal
                                </label>
                            </div>
                        </div>

                        @if ($sumber_pembiayaan === 'Eksternal')
                            <div class="mt-2" wire:key="tampil-eksternal">
                                <div wire:ignore>
                                    <select id="select2Basic" class="form-select"
                                        data-placeholder="Pilih Sumber Pembiayaan Eksternal" data-allow-clear="true">
                                        <option value="">Pilih Sumber Pembiayaan</option>
                                        @foreach ($sumber_eksternal as $sumber)
                                            <option value="{{ $sumber['id'] }}">
                                                {{ $sumber['nama'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card border-1 mb-3 shadow-none">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-3 col-sm-1 mb-6">
                            <label for="selectBank" class="form-label">Nama Bank</label>
                            <select class="form-select" id="selectBank" data-placeholder="Pilih Bank">
                                <option value="">Pilih Bank</option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank }}">{{ $bank }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="no_rekening" class="form-label">No. Rekening</label>
                            <input type="text" class="form-control" id="no_rekening"
                                placeholder="Masukkan No. Rekening">
                        </div>
                        <div class="col-md-5">
                            <label for="nama_rekening" class="form-label">Nama Rekening</label>
                            <input type="text" class="form-control" id="nama_rekening"
                                placeholder="Masukkan Nama Rekening">
                        </div>
                    </div>

                    <div class="row mb-3" id="rowLampiranSID"
                        style="display: {{ $jenis_pembiayaan === 'Installment' ? 'none' : '' }};">
                        <div class="col-md-6">
                            <label for="lampiran_sid" class="form-label">Lampiran SID</label>
                            <input class="form-control" type="file" id="lampiran_sid">
                            <div class="form-text">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar,
                                zip)
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="nilai_kol" class="form-label">Nilai KOL</label>
                            <input type="text" class="form-control" id="nilai_kol" placeholder="Nilai KOL" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label mb-2">Jenis Pembiayaan</label>
                            <div class="d-flex">
                                <div class="form-check me-3" id="radioInvoiceFinancing">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Invoice Financing" id="invoice_financing" 
                                        wire:model.live="jenis_pembiayaan">
                                    <label class="form-check-label" for="invoice_financing">
                                        Invoice Financing
                                    </label>
                                </div>
                                <div class="form-check me-3" id="radioPOFinancing">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="PO Financing" id="po_financing"
                                        wire:model.live="jenis_pembiayaan">
                                    <label class="form-check-label" for="po_financing">
                                        PO Financing
                                    </label>
                                </div>
                                <div class="form-check me-3" id="radioInstallment">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Installment" id="installment"
                                        wire:model.live="jenis_pembiayaan">
                                    <label class="form-check-label" for="installment">
                                        Installment
                                    </label>
                                </div>
                                <div class="form-check" id="radioFactoring">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Factoring" id="factoring"
                                        wire:model.live="jenis_pembiayaan">
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

            <!-- Modal Tambah Invoice/Kontrak -->
            @include('livewire.peminjaman.partials._modal-tambah-invoice')

            <div class="card border-1 mb-4 shadow-none">
                <div class="card-body">
                    <!-- Form untuk selain Installment -->
                    <div id="formNonInstallment" style="display: {{ $jenis_pembiayaan === 'Installment' ? 'none' : 'block' }};">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="total_pinjaman" class="form-label">Total Pinjaman</label>
                                <input type="text" class="form-control" id="total_pinjaman" value="RP. 9.000.000"
                                    disabled>
                            </div>
                            <div class="col-md-6 col-12 mb-6">
                                <label for="flatpickr-tanggal-pencairan" class="form-label">Harapan Tanggal
                                    Pencairan</label>
                                <input type="text" class="form-control flatpickr-date" placeholder="DD/MM/YYYY"
                                    id="flatpickr-tanggal-pencairan" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="total_bagi_hasil" class="form-label">Total Bagi Hasil</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="total_bagi_hasil"
                                        value="2% (Rp. 180.000)" disabled>
                                    <span class="input-group-text">/Bulan</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="flatpickr-tanggal-pembayaran" class="form-label">Rencana Tanggal
                                    Pembayaran</label>
                                <input type="text" class="form-control flatpickr-date" placeholder="DD/MM/YYYY"
                                    id="flatpickr-tanggal-pembayaran" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pembayaran_total" class="form-label">Pembayaran Total</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pembayaran_total"
                                        value="Rp. 9.180.000" disabled>
                                    <span class="input-group-text">/Bulan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form khusus untuk Installment -->
                    <div id="formInstallment" style="display: {{ $jenis_pembiayaan === 'Installment' ? 'block' : 'none' }};">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kebutuhanPinjaman" class="form-label">Kebutuhan Pinjaman</label>
                                <select class="form-select" id="kebutuhanPinjaman"
                                    data-placeholder="Pilih Kebutuhan Pinjaman">
                                    <option value="">Pilih Kebutuhan Pinjaman</option>
                                    @foreach ($kebutuhan_pinjaman as $kebutuhan)
                                        <option value="{{ $kebutuhan['value'] }}">{{ $kebutuhan['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="deskripsi_pinjaman" class="form-label">Deskripsi Pinjaman</label>
                                <input type="text" class="form-control bg-light" id="Deskripsi"
                                    value="Lorem Ipsum" disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nominal_pinjaman" class="form-label">Nominal Pinjaman</label>
                                <input type="text" class="form-control" id="nominal_pinjaman"
                                    placeholder="RP. 9.000.000">
                            </div>
                            <div class="col-md-6">
                                <label for="tenorPembayaran" class="form-label">Tenor Pembayaran</label>
                                <select class="form-select" id="tenorPembayaran" data-placeholder="Pilih Tenor">
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
                                <input type="text" class="form-control bg-light" id="total_pembayaran_installment"
                                    value="Rp 9.540.000" disabled>
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
                    <textarea class="form-control" id="catatan_lainnya" rows="3" placeholder="Masukkan Catatan"></textarea>
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
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            setTimeout(function() {
                initializeSelect2Elements();
                initializeAllFlatpickr();
            }, 100);
        });

        function initializeSelect2Elements() {
            // Initialize select2Basic (Sumber Eksternal)
            if ($('#select2Basic').length && !$('#select2Basic').hasClass('select2-hidden-accessible')) {
                $('#select2Basic').select2({
                    placeholder: 'Pilih Sumber Pembiayaan Eksternal',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    @this.set('sumber_eksternal_id', $(this).val());
                });
            }

            // Initialize selectBank
            if ($('#selectBank').length && !$('#selectBank').hasClass('select2-hidden-accessible')) {
                $('#selectBank').select2({
                    placeholder: 'Pilih Bank',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Initialize kebutuhanPinjaman
            if ($('#kebutuhanPinjaman').length && !$('#kebutuhanPinjaman').hasClass('select2-hidden-accessible')) {
                $('#kebutuhanPinjaman').select2({
                    placeholder: 'Pilih Kebutuhan Pinjaman',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Initialize tenorPembayaran
            if ($('#tenorPembayaran').length && !$('#tenorPembayaran').hasClass('select2-hidden-accessible')) {
                $('#tenorPembayaran').select2({
                    placeholder: 'Pilih Tenor',
                    allowClear: true,
                    width: '100%'
                });
            }
        }

        function initializeAllFlatpickr() {
            document.querySelectorAll('.flatpickr-date').forEach(function(element) {
                if (!element._flatpickr) {
                    flatpickr(element, {
                        monthSelectorType: 'static',
                        dateFormat: 'd/m/Y',
                        altInput: true,
                        altFormat: 'j F Y'
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalTambah = document.getElementById('modalTambahInvoice');
            
            if (!modalTambah) {
                return;
            }
            
            const bsModal = new bootstrap.Modal(modalTambah);

            // Listen for Livewire event to open modal (Livewire v3 syntax)
            document.addEventListener('open-modal', (event) => {
                const jenisPembiayaan = event.detail.jenisPembiayaan || 'Invoice Financing';
                
                // Show the modal first
                bsModal.show();
                
                // IMPORTANT: Wait for modal to be fully rendered before updating content
                setTimeout(function() {
                    updateModalContent(jenisPembiayaan);
                    
                    // Initialize flatpickr for modal dates
                    setTimeout(function() {
                        initializeModalFlatpickr();
                    }, 50);
                }, 100);
            });
        });

        function updateModalContent(jenisPembiayaan) {
            const modalTitle = document.getElementById('modalTitle');
            const allForms = document.querySelectorAll('.modal-form-content');
            
            // Hide all forms
            allForms.forEach(form => {
                form.style.display = 'none';
            });

            // Show appropriate form and update title
            switch (jenisPembiayaan) {
                case 'Invoice Financing':
                    modalTitle.textContent = 'Tambah Invoice Financing';
                    document.getElementById('formModalInvoiceFinancing').style.display = 'block';
                    break;
                case 'PO Financing':
                    modalTitle.textContent = 'Tambah PO Financing';
                    document.getElementById('formModalPOFinancing').style.display = 'block';
                    break;
                case 'Installment':
                    modalTitle.textContent = 'Tambah Invoice Penjamin';
                    document.getElementById('formModalInstallment').style.display = 'block';
                    break;
                case 'Factoring':
                    modalTitle.textContent = 'Tambah Kontrak Penjamin';
                    document.getElementById('formModalFactoring').style.display = 'block';
                    break;
            }
        }

        function initializeModalFlatpickr() {
            document.querySelectorAll('.flatpickr-modal-date').forEach(function(element) {
                if (!element._flatpickr) {
                    flatpickr(element, {
                        monthSelectorType: 'static',
                        dateFormat: 'd/m/Y',
                        altInput: true,
                        altFormat: 'j F Y',
                        onChange: function(selectedDates, dateStr, instance) {
                            const elementId = instance.input.id;
                            const formattedDate = selectedDates[0] ? 
                                selectedDates[0].toISOString().split('T')[0] : '';
                            
                            switch(elementId) {
                                case 'invoiceContractDate':
                                case 'poContractDate':
                                case 'factoringContractDate':
                                case 'installmentInvoiceDate':
                                    @this.set('new_invoice_date', formattedDate);
                                    break;
                                case 'invoiceDueDate':
                                case 'poDueDate':
                                case 'factoringDueDate':
                                    @this.set('new_due_date', formattedDate);
                                    break;
                            }
                        }
                    });
                }
            });
        }

        document.addEventListener('livewire:navigated', function() {
            setTimeout(function() {
                initializeSelect2Elements();
                initializeAllFlatpickr();
            }, 100);
        });

        Livewire.hook('morph.updated', ({
            el,
            component
        }) => {
            setTimeout(function() {
                initializeSelect2Elements();
                initializeAllFlatpickr();
                
                const modal = document.getElementById('modalTambahInvoice');
                if (modal && modal.classList.contains('show')) {
                    const selectedJenisPembiayaan = @this.modal_jenis_pembiayaan || 'Invoice Financing';
                    updateModalContent(selectedJenisPembiayaan);
                    
                    setTimeout(function() {
                        initializeModalFlatpickr();
                    }, 100);
                }
            }, 100);
        });
    </script>
@endpush
