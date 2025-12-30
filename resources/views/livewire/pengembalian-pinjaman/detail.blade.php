<div>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a wire:navigate.hover href="{{ route('pengembalian.index') }}" class="btn btn-outline-primary mb-2">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Kembali
            </a>
            <h4 class="fw-bold mb-0">Detail Pengembalian Peminjaman</h4>
            <p class="text-muted mb-0">Nomor: <strong>{{ $pengembalian->nomor_peminjaman }}</strong></p>
        </div>
        <span class="badge {{ $pengembalian->status === 'Lunas' ? 'bg-success' : 'bg-warning' }}">
            {{ $pengembalian->status ?? 'Belum Lunas' }}
        </span>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card shadow-none border">
                <div class="card-header">
                    <h6 class="mb-0">Informasi Peminjaman</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Nama Perusahaan</dt>
                        <dd class="col-7">{{ $pengembalian->nama_perusahaan }}</dd>

                        <dt class="col-5">Nomor Peminjaman</dt>
                        <dd class="col-7">{{ $pengembalian->nomor_peminjaman }}</dd>

                        <dt class="col-5">Tanggal Pencairan</dt>
                        <dd class="col-7">{{ optional($pengembalian->tanggal_pencairan)->format('d-m-Y') }}</dd>

                        <dt class="col-5">Jenis Pembiayaan</dt>
                        <dd class="col-7">{{ $pengembalian->pengajuanPeminjaman->jenis_pembiayaan ?? '-' }}</dd>

                        <dt class="col-5">Invoice Dibayarkan</dt>
                        <dd class="col-7">{{ $pengembalian->invoice_dibayarkan ?? '-' }}</dd>

                        @if ($pengembalian->bulan_pembayaran)
                            <dt class="col-5">Bulan Pembayaran</dt>
                            <dd class="col-7">{{ $pengembalian->bulan_pembayaran }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card shadow-none border">
                <div class="card-header">
                    <h6 class="mb-0">Ringkasan Keuangan</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-6">Total Pinjaman</dt>
                        <dd class="col-6 text-end">Rp {{ number_format($pengembalian->total_pinjaman, 0, ',', '.') }}</dd>

                        <dt class="col-6">Total Bagi Hasil</dt>
                        <dd class="col-6 text-end">Rp {{ number_format($pengembalian->total_bagi_hasil, 0, ',', '.') }}</dd>

                        <dt class="col-6">Nominal Invoice</dt>
                        <dd class="col-6 text-end">Rp {{ number_format($pengembalian->nominal_invoice ?? 0, 0, ',', '.') }}</dd>

                        <dt class="col-6">Total Dibayarkan</dt>
                        <dd class="col-6 text-end fw-bold text-success">Rp {{ number_format($totalDibayarkan, 0, ',', '.') }}</dd>

                        <dt class="col-6">Sisa Bayar Pokok</dt>
                        <dd class="col-6 text-end">
                            <span class="badge {{ $pengembalian->sisa_bayar_pokok == 0 ? 'bg-success' : 'bg-warning' }}">
                                Rp {{ number_format($pengembalian->sisa_bayar_pokok, 0, ',', '.') }}
                            </span>
                        </dd>

                        <dt class="col-6">Sisa Bagi Hasil</dt>
                        <dd class="col-6 text-end">
                            <span class="badge {{ $pengembalian->sisa_bagi_hasil == 0 ? 'bg-success' : 'bg-warning' }}">
                                Rp {{ number_format($pengembalian->sisa_bagi_hasil, 0, ',', '.') }}
                            </span>
                        </dd>

                        @if ($pengembalian->catatan)
                            <dt class="col-6">Catatan</dt>
                            <dd class="col-6 text-end">{{ $pengembalian->catatan }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header">
            <h6 class="mb-0">Detail Pembayaran Invoice</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nominal yang Dibayarkan</th>
                            <th>Bukti Pembayaran</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengembalian->pengembalianInvoices as $index => $invoice)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>Rp {{ number_format($invoice->nominal_yg_dibayarkan, 0, ',', '.') }}</td>
                                <td>
                                    @if ($invoice->bukti_pembayaran)
                                        <a href="{{ route('file.preview', ['filename' => $invoice->bukti_pembayaran]) }}"
                                           class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                            Lihat Bukti
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($invoice->bukti_pembayaran)
                                        <a href="{{ route('file.preview', ['filename' => $invoice->bukti_pembayaran]) }}"
                                           class="btn btn-sm btn-icon btn-text-primary rounded-pill"
                                           target="_blank" rel="noopener" title="Lihat Bukti">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada data pembayaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

