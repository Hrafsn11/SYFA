{{-- Data Invoice/Kontrak Table - Matching Original Design --}}
@if ($jenis_pembiayaan === 'Installment')
    <h6 class="text-muted mb-3">Data Installment</h6>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NO. INVOICE</th>
                    <th>NAMA CLIENT</th>
                    <th>NILAI KONTRAK</th>
                    <th>INVOICE DATE</th>
                    <th>NAMA BARANG</th>
                    <th>DOKUMEN INVOICE</th>
                    <th>DOKUMEN LAINNYA</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($detailsData as $idx => $inst)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $inst['no_invoice'] ?? '-' }}</td>
                        <td>{{ $inst['nama_client'] ?? '-' }}</td>
                        <td>Rp. {{ number_format($inst['nilai_invoice'] ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $inst['invoice_date'] ?? '-' }}</td>
                        <td>{{ $inst['nama_barang'] ?? '-' }}</td>
                        <td>
                            @if (!empty($inst['dokumen_invoice']))
                                <a href="{{ asset('storage/' . $inst['dokumen_invoice']) }}"
                                    target="_blank">{{ basename($inst['dokumen_invoice']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($inst['dokumen_lainnya']))
                                <a href="{{ asset('storage/' . $inst['dokumen_lainnya']) }}"
                                    target="_blank">{{ basename($inst['dokumen_lainnya']) }}</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data Installment</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@elseif($jenis_pembiayaan === 'PO Financing')
    <h6 class="text-muted mb-3">Data PO Financing</h6>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NO. KONTRAK</th>
                    <th>NILAI INVOICE</th>
                    <th>NAMA CLIENT</th>
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
                @forelse($detailsData as $idx => $po)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $po['no_kontrak'] ?? '-' }}</td>
                        <td>Rp. {{ number_format($po['nilai_invoice'] ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $po['nama_client'] ?? '-' }}</td>
                        <td>Rp. {{ number_format($po['nilai_pinjaman'] ?? 0, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($po['nilai_bagi_hasil'] ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $po['kontrak_date'] ?? '-' }}</td>
                        <td>{{ $po['due_date'] ?? '-' }}</td>
                        <td>
                            @if (!empty($po['dokumen_kontrak']))
                                <a href="{{ asset('storage/' . $po['dokumen_kontrak']) }}"
                                    target="_blank">{{ basename($po['dokumen_kontrak']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($po['dokumen_so']))
                                <a href="{{ asset('storage/' . $po['dokumen_so']) }}"
                                    target="_blank">{{ basename($po['dokumen_so']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($po['dokumen_bast']))
                                <a href="{{ asset('storage/' . $po['dokumen_bast']) }}"
                                    target="_blank">{{ basename($po['dokumen_bast']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($po['dokumen_lainnya']))
                                <a href="{{ asset('storage/' . $po['dokumen_lainnya']) }}"
                                    target="_blank">{{ basename($po['dokumen_lainnya']) }}</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada data PO</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@elseif($jenis_pembiayaan === 'Factoring')
    <h6 class="text-muted mb-3">Data Factoring</h6>
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
                    <th>DOKUMEN INVOICE</th>
                    <th>DOKUMEN KONTRAK</th>
                    <th>DOKUMEN SO</th>
                    <th>DOKUMEN BAST</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($detailsData as $idx => $fact)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $fact['no_kontrak'] ?? '-' }}</td>
                        <td>{{ $fact['nama_client'] ?? '-' }}</td>
                        <td>Rp. {{ number_format($fact['nilai_invoice'] ?? 0, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($fact['nilai_pinjaman'] ?? 0, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($fact['nilai_bagi_hasil'] ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $fact['kontrak_date'] ?? '-' }}</td>
                        <td>{{ $fact['due_date'] ?? '-' }}</td>
                        <td>
                            @if (!empty($fact['dokumen_invoice']))
                                <a href="{{ asset('storage/' . $fact['dokumen_invoice']) }}"
                                    target="_blank">{{ basename($fact['dokumen_invoice']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($fact['dokumen_kontrak']))
                                <a href="{{ asset('storage/' . $fact['dokumen_kontrak']) }}"
                                    target="_blank">{{ basename($fact['dokumen_kontrak']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($fact['dokumen_so']))
                                <a href="{{ asset('storage/' . $fact['dokumen_so']) }}"
                                    target="_blank">{{ basename($fact['dokumen_so']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($fact['dokumen_bast']))
                                <a href="{{ asset('storage/' . $fact['dokumen_bast']) }}"
                                    target="_blank">{{ basename($fact['dokumen_bast']) }}</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada data Factoring</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    {{-- Invoice Financing (Default) --}}
    <h6 class="text-muted mb-3">Data Invoicing</h6>
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
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse($detailsData as $idx => $inv)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $inv['no_invoice'] ?? '-' }}</td>
                        <td>{{ $inv['nama_client'] ?? '-' }}</td>
                        <td>Rp. {{ number_format($inv['nilai_invoice'] ?? 0, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($inv['nilai_pinjaman'] ?? 0, 0, ',', '.') }}</td>
                        <td>Rp. {{ number_format($inv['nilai_bagi_hasil'] ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $inv['invoice_date'] ?? '-' }}</td>
                        <td>{{ $inv['due_date'] ?? '-' }}</td>
                        <td>
                            @if (!empty($inv['dokumen_invoice']))
                                <a href="{{ asset('storage/' . $inv['dokumen_invoice']) }}"
                                    target="_blank">{{ basename($inv['dokumen_invoice']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($inv['dokumen_kontrak']))
                                <a href="{{ asset('storage/' . $inv['dokumen_kontrak']) }}"
                                    target="_blank">{{ basename($inv['dokumen_kontrak']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($inv['dokumen_so']))
                                <a href="{{ asset('storage/' . $inv['dokumen_so']) }}"
                                    target="_blank">{{ basename($inv['dokumen_so']) }}</a>
                            @endif
                        </td>
                        <td>
                            @if (!empty($inv['dokumen_bast']))
                                <a href="{{ asset('storage/' . $inv['dokumen_bast']) }}"
                                    target="_blank">{{ basename($inv['dokumen_bast']) }}</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center">Tidak ada invoice</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
