                    <div class="card shadow-none border mb-4">
                        <div class="card-body">
                            <!-- Invoice Financing Table -->
                            <div class="card shadow-none border mb-4 financing-table" id="invoiceFinancingTable">
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
                                                <th>DOKUMEN INVOICE <span class="text-danger">*</span></th>
                                                <th>DOKUMEN KONTRAK</th>
                                                <th>DOKUMEN SO</th>
                                                <th>DOKUMEN BAST</th>
                                                <th>AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <!-- Data will be populated by JavaScript -->
                                            @foreach ($invoice_financing_data as $index => $invoice)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $invoice['no_invoice'] }}</td>
                                                    <td>{{ $invoice['nama_client'] }}</td>
                                                    <td>Rp.
                                                        {{ number_format((int) $invoice['nilai_invoice'], 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp.
                                                        {{ number_format((int) $invoice['nilai_pinjaman'], 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp.
                                                        {{ number_format((int) $invoice['nilai_bagi_hasil'], 0, ',', '.') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($invoice['invoice_date'])->format('d F Y') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($invoice['due_date'])->format('d F Y') }}
                                                    </td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $invoice['dokumen_invoice'] }}</a>
                                                    </td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $invoice['dokumen_kontrak'] }}</a>
                                                    </td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $invoice['dokumen_so'] }}</a></td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $invoice['dokumen_bast'] }}</a></td>
                                                    <td><a href="#"><i class="fas fa-edit"></i></a></td>
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
                                                <th>NO. KONTRAK</th>
                                                <th>NAMA CLIENT</th>
                                                <th>NILAI INVOICE</th>
                                                <th>NILAI PINJAMAN</th>
                                                <th>NILAI BAGI HASIL</th>
                                                <th>KONTRAK DATE</th>
                                                <th>DUE DATE</th>
                                                <th>DOKUMEN KONTRAK <span class="text-danger">*</span></th>
                                                <th>DOKUMEN SO</th>
                                                <th>DOKUMEN BAST</th>
                                                <th>DOKUMEN LAINNYA</th>
                                                <th>AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <!-- Data will be populated by JavaScript -->
                                            @foreach ($po_financing_data as $index => $po)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $po['no_invoice'] }}</td>
                                                    <td>{{ $po['nama_client'] }}</td>
                                                    <td>Rp. {{ number_format((int) $po['nilai_invoice'], 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp. {{ number_format((int) $po['nilai_pinjaman'], 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp.
                                                        {{ number_format((int) $po['nilai_bagi_hasil'], 0, ',', '.') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($po['invoice_date'])->format('d F Y') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($po['due_date'])->format('d F Y') }}</td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $po['dokumen_kontrak'] }}</a></td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $po['dokumen_so'] }}</a></td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $po['dokumen_bast'] }}</a></td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $po['dokumen_lainnya'] }}</a></td>
                                                    <td><a href="#"><i class="fas fa-edit"></i></a></td>
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
                                                <th>DOKUMEN INVOICE <span class="text-danger">*</span></th>
                                                <th>DOKUMEN LAINNYA</th>
                                                <th>AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <!-- Data will be populated by JavaScript -->
                                            @foreach ($installment_data as $index => $inst)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $inst['no_invoice'] }}</td>
                                                    <td>{{ $inst['nama_client'] }}</td>
                                                    <td>Rp. {{ number_format((int) $inst['nilai_invoice'], 0, ',', '.') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($inst['invoice_date'])->format('d F Y') }}
                                                    </td>
                                                    <td>{{ $inst['nama_barang'] }}</td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $inst['dokumen_invoice'] }}</a></td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $inst['dokumen_lainnya'] }}</a></td>
                                                    <td><a href="#"><i class="fas fa-edit"></i></a></td>
                                                </tr>
                                            @endforeach
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
                                                <th>NO. KONTRAK</th>
                                                <th>NAMA CLIENT</th>
                                                <th>NILAI INVOICE</th>
                                                <th>NILAI PINJAMAN</th>
                                                <th>NILAI BAGI HASIL</th>
                                                <th>KONTRAK DATE</th>
                                                <th>DUE DATE</th>
                                                <th>DOKUMEN INVOICE <span class="text-danger">*</span></th>
                                                <th>DOKUMEN KONTRAK <span class="text-danger">*</span></th>
                                                <th>DOKUMEN SO</th>
                                                <th>DOKUMEN BAST</th>
                                                <th>AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <!-- Data will be populated by JavaScript -->
                                            @foreach ($factoring_data as $index => $fact)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $fact['no_invoice'] }}</td>
                                                    <td>{{ $fact['nama_client'] }}</td>
                                                    <td>Rp. {{ number_format((int) $fact['nilai_invoice'], 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp. {{ number_format((int) $fact['nilai_pinjaman'], 0, ',', '.') }}
                                                    </td>
                                                    <td>Rp.
                                                        {{ number_format((int) $fact['nilai_bagi_hasil'], 0, ',', '.') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($fact['invoice_date'])->format('d F Y') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($fact['due_date'])->format('d F Y') }}
                                                    </td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $fact['dokumen_invoice'] }}</a></td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $fact['dokumen_kontrak'] }}</a></td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $fact['dokumen_so'] }}</a></td>
                                                    <td><a href="#"
                                                            class="text-primary">{{ $fact['dokumen_bast'] }}</a></td>
                                                    <td><a href="#"><i class="fas fa-edit"></i></a></td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary wave-effect" id="btnTambahInvoice">
                                <i class="fa-solid fa-plus me-1"></i>
                                Tambah
                            </button>
                        </div>
                    </div>