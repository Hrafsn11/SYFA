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
            <div class="card border-1 mb-3 shadow-none" id="cardSumberPembiayaan">
                <div class="card-body">
                    <div class="col-md-12">
                        <label class="form-label">Sumber Pembiayaan</label>
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

                        {{-- Bagian @if ini akan merespon perubahan dari wire:model.live --}}
                        @if ($sumber_pembiayaan === 'Eksternal')
                            <div class="mt-3" wire:key="tampil-eksternal">
                                <label class="form-label">Pilih Sumber Eksternal</label>
                                <select class="form-select" wire:model="sumber_eksternal_id">
                                    <option selected disabled>Pilih Sumber Pembiayaan</option>
                                    <option value="1">Pemberi Dana A</option>
                                    <option value="2">Pemberi Dana B</option>
                                    <option value="3">Pemberi Dana C</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card border-1 mb-3 shadow-none">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-3 col-sm-1 mb-6">
                            <label for="exampleFormControlSelect1" class="form-label">Nama Bank</label>
                            <select class="form-select" id="exampleFormControlSelect1"
                                aria-label="Default select example">
                                <option selected>Pilih Bank</option>
                                <option value="1">BCA</option>
                                <option value="2">Mandiri</option>
                                <option value="3">BNI</option>
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

                    <div class="row mb-3" id="rowLampiranSID">
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

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Jenis Pembiayaan</label>
                            <div class="d-flex">
                                <div class="form-check me-3" id="radioInvoiceFinancing">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Invoice Financing" id="invoice_financing" checked>
                                    <label class="form-check-label" for="invoice_financing">
                                        Invoice Financing
                                    </label>
                                </div>
                                <div class="form-check me-3" id="radioPOFinancing">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="PO Financing" id="po_financing">
                                    <label class="form-check-label" for="po_financing">
                                        PO Financing
                                    </label>
                                </div>
                                <div class="form-check me-3" id="radioInstallment">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Installment" id="installment">
                                    <label class="form-check-label" for="installment">
                                        Installment
                                    </label>
                                </div>
                                <div class="form-check" id="radioFactoring">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Factoring" id="factoring">
                                    <label class="form-check-label" for="factoring">
                                        Factoring
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-none border mb-4">
                <div class="card-body">
                    <!-- Invoice Table with Collapse based on Jenis Pembiayaan -->
                    <div class="card shadow-none border mb-4 financing-table" id="invoiceFinancingTable"
                        style="display: block;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Invoice Penjamin</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NO. INVOICE</th>
                                        <th>NAMA CLIENT</th>
                                        <th>NILAI INVOICE</th>
                                        <th>NILAI PINJAMAN</th>
                                        <th>NILAI BAGI HASIL</th>
                                        <th>INVOICE DATE</th>
                                        <th>DUE DATE</th>
                                        <th>DOKUMEN INVOICE</th>
                                        <th>DOKUMEN KONTRAK</th>
                                        <th>DOKUMEN SO</th>
                                        <th>DOKUMEN BAST</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($invoices as $index => $invoice)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $invoice['no_invoice'] }}</td>
                                            <td>{{ $invoice['nama_client'] }}</td>
                                            <td>Rp. {{ number_format((int) $invoice['nilai_invoice'], 0, ',', '.') }}
                                            </td>
                                            <td>Rp. {{ number_format((int) $invoice['nilai_pinjaman'], 0, ',', '.') }}
                                            </td>
                                            <td>Rp. {{ number_format((int) $invoice['nilai_bagi_hasil'], 0, ',', '.') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($invoice['invoice_date'])->format('d F Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($invoice['due_date'])->format('d F Y') }}</td>
                                            <td><a href="#">{{ $invoice['dokumen_invoice'] }}</a></td>
                                            <td><a href="#">{{ $invoice['dokumen_kontrak'] }}</a></td>
                                            <td><a href="#">{{ $invoice['dokumen_so'] }}</a></td>
                                            <td><a href="#">{{ $invoice['dokumen_bast'] }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- PO Financing Table -->
                    <div class="card shadow-none border mb-4 financing-table" id="poFinancingTable"
                        style="display: none;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Kontrak Penjamin</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NO. INVOICE</th>
                                        <th>NAMA CLIENT</th>
                                        <th>NILAI INVOICE</th>
                                        <th>NILAI PINJAMAN</th>
                                        <th>NILAI BAGI HASIL</th>
                                        <th>KONTRAK DATE</th>
                                        <th>DUE DATE</th>
                                        <th>DOKUMEN KONTRAK</th>
                                        <th>DOKUMEN SO</th>
                                        <th>DOKUMEN BAST</th>
                                        <th>DOKUMEN LAINNYA</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @foreach ($invoices as $index => $invoice)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $invoice['no_invoice'] }}</td>
                                            <td>{{ $invoice['nama_client'] }}</td>
                                            <td>Rp. {{ number_format((int) $invoice['nilai_invoice'], 0, ',', '.') }}
                                            </td>
                                            <td>Rp. {{ number_format((int) $invoice['nilai_pinjaman'], 0, ',', '.') }}
                                            </td>
                                            <td>Rp.
                                                {{ number_format((int) $invoice['nilai_bagi_hasil'], 0, ',', '.') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($invoice['invoice_date'])->format('d F Y') }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($invoice['due_date'])->format('d F Y') }}</td>
                                            <td><a href="#">{{ $invoice['dokumen_invoice'] }}</a></td>
                                            <td><a href="#">{{ $invoice['dokumen_kontrak'] }}</a></td>
                                            <td><a href="#">{{ $invoice['dokumen_so'] }}</a></td>
                                            <td><a href="#">{{ $invoice['dokumen_bast'] }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Installment Table -->
                    <div class="card shadow-none border mb-4 financing-table" id="installmentTable"
                        style="display: none;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Invoice Penjamin</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NO.INVOICE</th>
                                        <th>NAMA CLIENT</th>
                                        <th>NILAI INVOICE</th>
                                        <th>INVOICE DATE</th>
                                        <th>NAMA BARANG</th>
                                        <th>DOKUMEN INVOICE</th>
                                        <th>DOKUMEN LAINNYA</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td colspan="10" class="text-center">Belum ada data installment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Factoring Table -->
                    <div class="card shadow-none border mb-4 financing-table" id="factoringTable"
                        style="display: none;">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Kontrak Penjamin</h5>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NO. INVOICE</th>
                                        <th>NAMA CLIENT</th>
                                        <th>NILAI INVOICE</th>
                                        <th>NILAI PINJAMAN</th>
                                        <th>NILAI BAGI HASIL</th>
                                        <th>INVOICE DATE</th>
                                        <th>DUE DATE</th>
                                        <th>DOKUMEN INVOICE</th>
                                        <th>DOKUMEN KONTRAK</th>
                                        <th>DOKUMEN SO</th>
                                        <th>DOKUMEN BAST</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <tr>
                                        <td colspan="10" class="text-center">Belum ada data factoring</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <button type="button" class="btn btn-outline-primary wave-effect">
                        <i class="fa-solid fa-plus me-1"></i>
                        Tambah Invoice
                    </button>


                </div>
            </div>

            <div class="row justify-content-end mb-3">
                <div class="col-md-6 text-end">
                    <span class="badge bg-label-secondary">H+30 dari harapan tanggal pencairan</span>
                </div>
            </div>

            <div class="card border-1 mb-4 shadow-none">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="total_pinjaman" class="form-label">Total Pinjaman</label>
                            <input type="text" class="form-control" id="total_pinjaman" value="RP. 9.000.000"
                                disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="harapan_tanggal_pencairan" class="form-label">Harapan Tanggal
                                Pencairan</label>
                            <input class="form-control" type="date" value="2021-06-18" id="html5-date-input" />
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
                            <label for="rencana_tanggal_pembayaran" class="form-label">Rencana Tanggal Pembayaran <i
                                    class="tf-icons ti ti-info-circle data-bs-toggle="tooltip"
                                    title="Info"></i></label>
                            <input class="form-control" type="date" value="2021-06-18" id="html5-date-input" />

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
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initialize flatpickr
            flatpickr(".flatpickr-date", {
                dateFormat: "d/m/Y",
            });
        });

        // Handle Jenis Pembiayaan Radio Button Change - Pure JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[name="jenis_pembiayaan"]');
            const cardSumberPembiayaan = document.getElementById('cardSumberPembiayaan');
            const rowLampiranSID = document.getElementById('rowLampiranSID');
            const radioInvoiceFinancing = document.getElementById('radioInvoiceFinancing');
            const radioPOFinancing = document.getElementById('radioPOFinancing');
            const radioInstallment = document.getElementById('radioInstallment');
            const radioFactoring = document.getElementById('radioFactoring');

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedValue = this.value;

                    // Hide all tables
                    document.querySelectorAll('.financing-table').forEach(table => {
                        table.style.display = 'none';
                    });

                    // Check if Installment is selected
                    if (selectedValue === 'Installment') {
                        // Hide other radio buttons
                        radioInvoiceFinancing.style.display = 'none';
                        radioPOFinancing.style.display = 'none';
                        radioFactoring.style.display = 'none';

                        // Hide Sumber Pembiayaan card
                        cardSumberPembiayaan.style.display = 'none';

                        // Hide Lampiran SID and Nilai KOL row
                        rowLampiranSID.style.display = 'none';

                        // Show Installment table
                        document.getElementById('installmentTable').style.display = 'block';
                    } else {
                        // Show all radio buttons again
                        radioInvoiceFinancing.style.display = '';
                        radioPOFinancing.style.display = '';
                        radioInstallment.style.display = '';
                        radioFactoring.style.display = '';

                        // Show Sumber Pembiayaan card
                        cardSumberPembiayaan.style.display = '';

                        // Show Lampiran SID and Nilai KOL row
                        rowLampiranSID.style.display = '';

                        // Show selected table
                        if (selectedValue === 'Invoice Financing') {
                            document.getElementById('invoiceFinancingTable').style.display =
                            'block';
                        } else if (selectedValue === 'PO Financing') {
                            document.getElementById('poFinancingTable').style.display = 'block';
                        } else if (selectedValue === 'Factoring') {
                            document.getElementById('factoringTable').style.display = 'block';
                        }
                    }

                    // Smooth scroll to visible table
                    setTimeout(() => {
                        const visibleTable = document.querySelector(
                            '.financing-table[style*="display: block"]');
                        if (visibleTable) {
                            visibleTable.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest'
                            });
                        }
                    }, 100);
                });
            });
        });
    </script>
@endpush
