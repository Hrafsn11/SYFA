<div>
    {{-- Header Section --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <a wire:navigate.hover href="{{ route('pengembalian.index') }}" class="btn btn-outline-primary mb-3">
                <i class="ti ti-arrow-left me-1"></i>
                Kembali ke Daftar
            </a>
            <h4 class="fw-bold mb-1">Detail Pengembalian Peminjaman</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('pengembalian.index') }}">Pengembalian</a></li>
                    <li class="breadcrumb-item active">{{ $pengembalian->nomor_peminjaman }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span
                class="badge rounded fs-6 {{ $pengembalian->status === 'Lunas' ? 'bg-success' : 'bg-label-warning text-warning' }}">
                <i class="ti {{ $pengembalian->status === 'Lunas' ? 'ti-circle-check' : 'ti-clock' }} me-1"></i>
                {{ $pengembalian->status ?? 'Belum Lunas' }}
            </span>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row g-4">
        {{-- Left Column: Loan Information --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-file-description text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Informasi Peminjaman</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-container">
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Nama Perusahaan</span>
                            <span class="fw-semibold text-end">{{ $pengembalian->nama_perusahaan }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Nomor Peminjaman</span>
                            <span class="fw-semibold text-end">
                                <code class="text-primary">{{ $pengembalian->nomor_peminjaman }}</code>
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Tanggal Pencairan</span>
                            <span class="fw-semibold text-end">
                                {{ optional($pengembalian->tanggal_pencairan)->format('d F Y') }}
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Jenis Pembiayaan</span>
                            <span class="fw-semibold text-end">
                                <span class="badge bg-label-primary">
                                    {{ $pengembalian->pengajuanPeminjaman->jenis_pembiayaan ?? '-' }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Invoice Dibayarkan</span>
                            <span class="fw-semibold text-end">{{ $pengembalian->invoice_dibayarkan ?? '-' }}</span>
                        </div>
                        @if ($pengembalian->bulan_pembayaran)
                            <div class="info-item d-flex justify-content-between py-3">
                                <span class="text-muted">Bulan Pembayaran</span>
                                <span class="fw-semibold text-end">{{ $pengembalian->bulan_pembayaran }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Financial Summary --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ti ti-report-money text-success"></i>
                        <h6 class="mb-0 fw-semibold">Ringkasan Keuangan</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-container">
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Total Pinjaman</span>
                            <span class="fw-bold text-end">Rp
                                {{ number_format($pengembalian->total_pinjaman, 0, ',', '.') }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Total Bagi Hasil</span>
                            <span class="fw-bold text-end">Rp
                                {{ number_format($pengembalian->total_bagi_hasil, 0, ',', '.') }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Nominal Invoice</span>
                            <span class="fw-bold text-end">Rp
                                {{ number_format($pengembalian->nominal_invoice ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div
                            class="info-item d-flex justify-content-between py-3 border-bottom bg-opacity-10 rounded px-2 -mx-2">
                            <span class="fw-semibold">Total Dibayarkan</span>
                            <span class="fw-bold text-success text-end">Rp
                                {{ number_format($totalDibayarkan, 0, ',', '.') }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Sisa Bayar Pokok</span>
                            <span class="text-end">
                                <span
                                    class="badge {{ $pengembalian->sisa_bayar_pokok == 0 ? 'bg-success' : 'bg-warning' }}">
                                    Rp {{ number_format($pengembalian->sisa_bayar_pokok, 0, ',', '.') }}
                                </span>
                            </span>
                        </div>
                        <div
                            class="info-item d-flex justify-content-between py-3 {{ $pengembalian->catatan ? 'border-bottom' : '' }}">
                            <span class="text-muted">Sisa Bagi Hasil</span>
                            <span class="text-end">
                                <span
                                    class="badge {{ $pengembalian->sisa_bagi_hasil == 0 ? 'bg-success' : 'bg-warning' }}">
                                    Rp {{ number_format($pengembalian->sisa_bagi_hasil, 0, ',', '.') }}
                                </span>
                            </span>
                        </div>
                        @if ($pengembalian->catatan)
                            <div class="info-item py-3">
                                <span class="text-muted d-block mb-2">Catatan</span>
                                <div class="alert alert-secondary mb-0 py-2">
                                    <i class="ti ti-notes me-1"></i>
                                    {{ $pengembalian->catatan }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment History Table --}}
    <div class="card mt-4">
        <div class="card-header d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-receipt text-info"></i>
                <h6 class="mb-0 fw-semibold">Riwayat Pembayaran Invoice</h6>
            </div>
            <span class="badge bg-primary rounded">
                {{ $pengembalian->pengembalianInvoices->count() }} Pembayaran
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nominal Dibayarkan</th>
                            <th>Bukti Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengembalian->pengembalianInvoices as $index => $invoice)
                            <tr>
                                <td>
                                    <span class="fw-semibold">Rp
                                        {{ number_format($invoice->nominal_yg_dibayarkan, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if ($invoice->bukti_pembayaran)
                                        <a href="{{ Storage::disk('public')->url($invoice->bukti_pembayaran) }}"
                                           class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                {{-- <td class="text-center">
                                    @if ($invoice->bukti_pembayaran)
                                        <a href="{{ Storage::disk('public')->url($invoice->bukti_pembayaran) }}"
                                           class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                           target="_blank" rel="noopener" title="Lihat Bukti">
                                            <i class="ti ti-file-text"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avatar avatar-lg mb-3">
                                            <span class="avatar-initial rounded-circle bg-label-secondary">
                                                <i class="ti ti-receipt-off ti-lg"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-1">Belum Ada Pembayaran</h6>
                                        <p class="text-muted mb-0">Data pembayaran invoice akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($pengembalian->pengembalianInvoices->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td class="fw-bold">Total Pembayaran</td>
                                <td class="fw-bold text-success">
                                    Rp
                                    {{ number_format($pengembalian->pengembalianInvoices->sum('nominal_yg_dibayarkan'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .info-item {
            transition: background-color 0.15s ease;
        }

        .info-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .-mx-2 {
            margin-left: -0.5rem;
            margin-right: -0.5rem;
        }
    </style>
@endpush
