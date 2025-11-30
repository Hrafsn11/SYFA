<div class="card shadow-none border mb-4">
    <div class="card-body">
        @switch($jenis_pembiayaan)
            @case('Invoice Financing')
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
                                @forelse ($form_data_invoice as $index => $invoice)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $invoice['no_invoice'] }}</td>
                                        <td>{{ $invoice['nama_client'] }}</td>
                                        <td>{{ rupiahFormatter($invoice['nilai_invoice']) }}</td>
                                        <td>{{ rupiahFormatter($invoice['nilai_pinjaman']) }}</td>
                                        <td>{{ rupiahFormatter($invoice['nilai_bagi_hasil']) }}</td>
                                        <td>{{ parseCarbonDate($invoice['invoice_date'])->format('d F Y') }}</td>
                                        <td>{{ parseCarbonDate($invoice['due_date'])->format('d F Y') }}</td>
                                        <td>
                                            @if(!empty($invoice['dokumen_invoice_file']))
                                                <a href="{{ getFileUrl($invoice['dokumen_invoice_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($invoice['dokumen_kontrak_file']))
                                                <a href="{{ getFileUrl($invoice['dokumen_kontrak_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($invoice['dokumen_so_file']))
                                                <a href="{{ getFileUrl($invoice['dokumen_so_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($invoice['dokumen_bast_file']))
                                                <a href="{{ getFileUrl($invoice['dokumen_bast_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><a onclick="return false;" class="btn btn-sm btn-outline-primary" title="Edit" wire:click='editInvoice({{ $index }})'>
                                            <i class="ti ti-edit" wire:loading.remove wire:target='editInvoice({{ $index }})'></i>
                                            <span class="spinner-border spinner-border-sm" wire:loading wire:target='editInvoice({{ $index }})'></span>
                                        </a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="13" class="text-center">No Data</td></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @break
            @case('PO Financing')
                <!-- PO Financing Table -->
                <div class="card shadow-none border mb-4 financing-table" id="poFinancingTable">
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
                                @forelse ($form_data_invoice as $index => $po)
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
                                        <td>
                                            @if(!empty($po['dokumen_kontrak_file']))
                                                <a href="{{ getFileUrl($po['dokumen_kontrak_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($po['dokumen_so_file']))
                                                <a href="{{ getFileUrl($po['dokumen_so_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($po['dokumen_bast_file']))
                                                <a href="{{ getFileUrl($po['dokumen_bast_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($po['dokumen_lainnnya_file']))
                                                <a href="{{ getFileUrl($po['dokumen_lainnnya_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><a href="#"><i class="fas fa-edit"></i></a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="13" class="text-center">No Data</td></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @break
            @case('Installment')
                <!-- Installment Table -->
                <div class="card shadow-none border mb-4 financing-table" id="installmentTable">
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
                                @forelse ($form_data_invoice as $index => $inst)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $inst['no_invoice'] }}</td>
                                        <td>{{ $inst['nama_client'] }}</td>
                                        <td>Rp. {{ number_format((int) $inst['nilai_invoice'], 0, ',', '.') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($inst['invoice_date'])->format('d F Y') }}
                                        </td>
                                        <td>{{ $inst['nama_barang'] }}</td>
                                        <td>
                                            @if(!empty($inst['dokumen_invoice_file']))
                                                <a href="{{ getFileUrl($inst['dokumen_invoice_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($inst['dokumen_lainnnya_file']))
                                                <a href="{{ getFileUrl($inst['dokumen_lainnnya_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><a href="#"><i class="fas fa-edit"></i></a></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="13" class="text-center">No Data</td></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @break
            @case('Factoring')
                <!-- Factoring Table -->
                <div class="card shadow-none border mb-4 financing-table" id="factoringTable">
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
                                @forelse ($form_data_invoice as $fact)
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
                                        <td>
                                            @if(!empty($fact['dokumen_invoice_file']))
                                                <a href="{{ getFileUrl($fact['dokumen_invoice_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($fact['dokumen_kontrak_file']))
                                                <a href="{{ getFileUrl($fact['dokumen_kontrak_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($fact['dokumen_so_file']))
                                                <a href="{{ getFileUrl($fact['dokumen_so_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($fact['dokumen_bast_file']))
                                                <a href="{{ getFileUrl($fact['dokumen_bast_file']) }}" target="_blank" class="text-primary">Lihat Dokumen</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><a href="#"><i class="fas fa-edit"></i></a></td>

                                    </tr>
                                @empty
                                    <tr><td colspan="13" class="text-center">No Data</td></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @break
            @default
        @endswitch
        <button type="button" class="btn btn-outline-primary" id="btnTambahInvoice" data-bs-toggle="modal" data-bs-target="#modalTambahInvoice">
            <i class="fa-solid fa-plus me-1"></i>
            Tambah
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        Livewire.hook('morphed',  ({ el, component }) => {
            const isTableCreate = component?.name === 'pengajuan-pinjaman.create';
            if (!isTableCreate) return;
            initAllComponents();
        });
    });
</script>
@endpush