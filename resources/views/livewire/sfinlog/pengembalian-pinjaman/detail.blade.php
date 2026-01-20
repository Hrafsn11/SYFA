<div>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <a wire:navigate.hover href="{{ route('sfinlog.pengembalian-pinjaman.index') }}"
                class="btn btn-outline-primary mb-3">
                <i class="ti ti-arrow-left me-1"></i>
                Kembali ke Daftar
            </a>
            <h4 class="fw-bold mb-1">Detail Pengembalian Peminjaman</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a
                            href="{{ route('sfinlog.pengembalian-pinjaman.index') }}">Pengembalian</a></li>
                    <li class="breadcrumb-item active">{{ $peminjaman->nomor_peminjaman }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded fs-6 {{ $status === 'Lunas' ? 'bg-success' : 'bg-label-warning text-warning' }}">
                <i class="ti {{ $status === 'Lunas' ? 'ti-circle-check' : 'ti-clock' }} me-1"></i>
                {{ $status }}
            </span>
        </div>
    </div>

    <div class="row g-4">
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
                            <span class="fw-semibold text-end">{{ $peminjaman->debitur->nama ?? '-' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Nomor Peminjaman</span>
                            <span class="fw-semibold text-end">
                                <code class="text-primary">{{ $peminjaman->nomor_peminjaman }}</code>
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Nama Project</span>
                            <span class="fw-semibold text-end">{{ $peminjaman->nama_project ?? '-' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Tanggal Pencairan</span>
                            <span class="fw-semibold text-end">
                                {{ optional($peminjaman->harapan_tanggal_pencairan)->format('d F Y') ?? '-' }}
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Durasi Project</span>
                            <span class="fw-semibold text-end">
                                {{ $peminjaman->durasi_project ?? 0 }} Bulan
                                {{ $peminjaman->durasi_project_hari ?? 0 }} Hari
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">TOP (Term of Payment)</span>
                            <span class="fw-semibold text-end">{{ $peminjaman->top ?? 0 }} Hari</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3">
                            <span class="text-muted">Rencana Tanggal Pengembalian</span>
                            <span class="fw-semibold text-end">
                                {{ optional($peminjaman->rencana_tgl_pengembalian)->format('d F Y') ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                            <span class="text-muted">Nilai Pinjaman (Pokok)</span>
                            <span class="fw-bold text-end">Rp
                                {{ number_format($breakdown['total_pinjaman'], 0, ',', '.') }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Bagi Hasil ({{ $peminjaman->presentase_bagi_hasil ?? 0 }}%)</span>
                            <span class="fw-bold text-end">Rp
                                {{ number_format($breakdown['total_bagi_hasil'], 0, ',', '.') }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Total Tagihan (Pokok + Bagi Hasil)</span>
                            <span class="fw-bold text-end text-primary">Rp
                                {{ number_format($breakdown['total_tagihan'], 0, ',', '.') }}</span>
                        </div>

                        <div class="py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">Total Dibayarkan</span>
                                <span class="fw-bold text-success fs-5">Rp
                                    {{ number_format($breakdown['total_dibayarkan'], 0, ',', '.') }}</span>
                            </div>

                            @if ($breakdown['total_dibayarkan'] > 0)
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted d-block mb-2">
                                        <i class="ti ti-chart-pie me-1"></i> Alokasi Pembayaran:
                                    </small>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex justify-content-between align-items-center px-3 py-2 rounded"
                                            style="background-color: rgba(113, 221, 55, 0.1);">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-success rounded-circle p-1">
                                                    <i class="ti ti-percentage ti-xs"></i>
                                                </span>
                                                <span class="text-muted">Bayar Bagi Hasil</span>
                                            </div>
                                            <span class="fw-semibold text-success">
                                                Rp {{ number_format($breakdown['paid_to_bagi_hasil'], 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center px-3 py-2 rounded"
                                            style="background-color: rgba(105, 108, 255, 0.1);">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary rounded-circle p-1">
                                                    <i class="ti ti-wallet ti-xs"></i>
                                                </span>
                                                <span class="text-muted">Bayar Pokok Pinjaman</span>
                                            </div>
                                            <span class="fw-semibold text-primary">
                                                Rp {{ number_format($breakdown['paid_to_pokok'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Sisa Bayar Pokok</span>
                            <span class="text-end">
                                <span
                                    class="badge {{ $breakdown['sisa_pinjaman'] == 0 ? 'bg-success' : 'bg-warning' }}">
                                    Rp {{ number_format($breakdown['sisa_pinjaman'], 0, ',', '.') }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3 border-bottom">
                            <span class="text-muted">Sisa Bagi Hasil</span>
                            <span class="text-end">
                                <span
                                    class="badge {{ $breakdown['sisa_bagi_hasil'] == 0 ? 'bg-success' : 'bg-warning' }}">
                                    Rp {{ number_format($breakdown['sisa_bagi_hasil'], 0, ',', '.') }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item d-flex justify-content-between py-3">
                            <span class="fw-semibold">Total Sisa</span>
                            <span class="text-end">
                                <span
                                    class="badge fs-6 {{ $breakdown['sisa_total'] == 0 ? 'bg-success' : 'bg-danger' }}">
                                    Rp {{ number_format($breakdown['sisa_total'], 0, ',', '.') }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-receipt text-info"></i>
                <h6 class="mb-0 fw-semibold">Riwayat Pembayaran</h6>
            </div>
            <span class="badge bg-primary rounded">
                {{ $pengembalianList->count() }} Pembayaran
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Tanggal Pembayaran</th>
                            <th class="text-end">Jumlah Dibayar</th>
                            <th class="text-end">Sisa Pokok</th>
                            <th class="text-end">Sisa Bagi Hasil</th>
                            <th class="text-center">Status</th>
                            <th>Catatan</th>
                            <th class="text-center">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengembalianList->reverse() as $index => $pengembalian)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ optional($pengembalian->tanggal_pengembalian)->format('d F Y') ?? '-' }}</td>
                                <td class="text-end fw-semibold">Rp
                                    {{ number_format($pengembalian->jumlah_pengembalian, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">Rp
                                    {{ number_format($pengembalian->sisa_pinjaman, 0, ',', '.') }}</td>
                                <td class="text-end text-warning">Rp
                                    {{ number_format($pengembalian->sisa_bagi_hasil, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = match ($pengembalian->status) {
                                            'Lunas' => 'bg-success',
                                            'Terlambat' => 'bg-danger',
                                            default => 'bg-warning',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $pengembalian->status }}</span>
                                </td>
                                <td>{{ $pengembalian->catatan ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($pengembalian->bukti_pembayaran)
                                        <a href="{{ Storage::disk('public')->url($pengembalian->bukti_pembayaran) }}"
                                            class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                                            <i class="ti ti-file-text me-1"></i>Lihat
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="avatar avatar-lg mb-3">
                                            <span class="avatar-initial rounded-circle bg-label-secondary">
                                                <i class="ti ti-receipt-off ti-lg"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-1">Belum Ada Pembayaran</h6>
                                        <p class="text-muted mb-0">Data pembayaran akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($pengembalianList->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="fw-bold">Total Pembayaran</td>
                                <td class="fw-bold text-success text-end">
                                    Rp {{ number_format($breakdown['total_dibayarkan'], 0, ',', '.') }}
                                </td>
                                <td colspan="5"></td>
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
    </style>
@endpush
