<div>
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-0">Debitur Piutang</h4>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <div class="row mx-2 mt-3 align-items-center mb-3">
                <div class="col-md-2">
                    <div class="d-flex align-items-center">
                        <span class="me-2">Show</span>
                        <select wire:model.live="perPage" class="form-select" style="width: auto;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="ms-2">Entries</span>
                    </div>
                </div>

                <div class="col-md-10">
                    <div class="d-flex justify-content-end gap-2">
                        <input type="search" wire:model.live.debounce.500ms="search" class="form-control"
                            placeholder="Cari debitur, invoice, atau kontrak..." style="max-width: 300px;">

                        @if ($search)
                            <button type="button" wire:click="clearSearch" class="btn btn-outline-secondary">
                                <i class="ti ti-x"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div wire:loading.delay wire:target="search, perPage, setPeriod" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                <span>Loading...</span>
            </div>

            <div wire:loading.remove wire:target="search, perPage, setPeriod" style="overflow-x: auto; white-space: nowrap;">
                <div class="table-container">
                    <div class="filter-placeholder"></div>

                    <table class="table table-bordered border-top">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th>Nama Debitur</th>
                                <th>Objek Jaminan</th>
                                <th>Tgl Pengajuan</th>
                                <th>Nilai Yang Diajukan</th>
                                <th>Nilai Yang Dicairkan</th>
                                <th>Tanggal Pencairan</th>
                                <th>Masa Penggunaan</th>
                                <th>Bagi Hasil Oleh Debitur</th>
                                <th>Nilai Yang Harus Dibayar</th>
                                <th>Status</th>
                                <th>Tanggal Bayar</th>
                                <th>Lama Pinjaman</th>
                                <th>Nilai Bayar</th>
                                <th>Total Sisa Pokok + Bagi Hasil</th>
                                <th>Total kurang bayar bagi hasil</th>
                                <th>Nilai Pokok Januari dan nilai pokok yang belum bayar sama sekali </th>
                                <th>% Bagi Hasil</th>
                                <th>Bagi Hasil/Bulan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($debiturPiutangData as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $debiturPiutangData->firstItem() + $index }}</td>
                                    <td>{{ $item->nama_debitur ?? '-' }}</td>
                                    <td>{{ $item->objek_jaminan ?? '-' }}</td>
                                    <td class="text-center">
                                        {{ $item->tanggal_pengajuan ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item->nilai_diajukan ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">Rp
                                        {{ number_format($item->nilai_dicairkan ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        {{ $item->tanggal_pencairan ? \Carbon\Carbon::parse($item->tanggal_pencairan)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center">{{ $item->masa_penggunaan ?? 0 }} Bulan</td>
                                    <td class="text-end">Rp
                                        {{ number_format($item->total_bagi_hasil ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp
                                        {{ number_format($item->yang_harus_dibayar ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge bg-label-{{ $item->status_color ?? 'secondary' }}">{{ $item->status ?? '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $item->tanggal_bayar_terakhir ? \Carbon\Carbon::parse($item->tanggal_bayar_terakhir)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center">{{ $item->lama_pinjaman_bulan ?? 0 }} Bulan</td>
                                    <td class="text-end">Rp
                                        {{ number_format($item->nilai_bayar_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($item->total_sisa_ar ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">Rp
                                        {{ number_format($item->kurang_bayar_bagi_hasil ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp
                                        {{ number_format($item->nilai_pokok_belum_bayar ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item->persentase_bagi_hasil ?? 0 }}%</td>
                                    <td class="text-end">Rp
                                        {{ number_format($item->bagi_hasil_perbulan ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="19" class="text-center text-muted py-4">
                                        <i class="ti ti-inbox mb-2" style="font-size: 2rem;"></i>
                                        <p class="mb-0">
                                            @if ($search)
                                                Tidak ada data untuk "{{ $search }}"
                                            @else
                                                Tidak ada data debitur piutang
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="mb-3" style="width: 250px;">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Select Period"
                                id="flatpickr-period-filter" wire:ignore>
                            <span class="input-group-text cursor-pointer"><i class="ti ti-filter"></i></span>
                        </div>
                    </div>

                    <table class="table table-bordered border-top">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal Bayar</th>
                                <th>Nilai Bayar</th>
                                <th>Pembayaran Pokok</th>
                                <th>Pokok Bulan Selanjutnya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historiPembayaran as $pembayaran)
                                <tr>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</td>
                                    <td class="text-end">Rp {{ number_format($pembayaran->nilai_bayar, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">Rp
                                        {{ number_format($pembayaran->pembayaran_pokok, 0, ',', '.') }}</td>
                                    <td class="text-end text-primary fw-bold">Rp
                                        {{ number_format($pembayaran->sisa_pokok_selanjutnya, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <small>Tidak ada data pembayaran</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="table-container" style="margin-right: 0;">
                    <div class="filter-placeholder"></div>

                    <table class="table table-bordered border-top">
                        <thead class="table-light">
                            <tr>
                                <th>Subtotal Sisa Pokok</th>
                                <th>Pokok</th>
                                <th>Sisa Bagi Hasil</th>
                                <th>Telat Hari</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-end">Rp {{ number_format($summaryData['subtotal_sisa'], 0, ',', '.') }}
                                </td>
                                <td class="text-end">Rp {{ number_format($summaryData['pokok'], 0, ',', '.') }}</td>
                                <td class="text-end">Rp
                                    {{ number_format($summaryData['sisa_bagi_hasil'], 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $summaryData['telat_hari'] > 0 ? 'danger' : 'success' }}">
                                        {{ $summaryData['telat_hari'] }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mx-2 mt-3 mb-3">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_info">
                        Menampilkan {{ $debiturPiutangData->firstItem() ?? 0 }} -
                        {{ $debiturPiutangData->lastItem() ?? 0 }} dari {{ $debiturPiutangData->total() }} total
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    {{ $debiturPiutangData->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .table-container {
            display: inline-block;
            vertical-align: top;
            margin-right: 20px;
            white-space: normal;
            min-width: 250px;
        }

        .filter-placeholder {
            width: 250px;
            height: 42px;
            margin-bottom: 0.5rem;
        }

        .table-container table {
            width: auto;
        }

        .table-container table th,
        .table-container table td {
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .table-container {
                display: block;
                width: 100%;
                margin-right: 0;
            }

            .filter-placeholder {
                display: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-alert', (event) => {
                const data = event[0] || event;
                Swal.fire({
                    icon: data.type || 'success',
                    title: data.type === 'success' ? 'Berhasil!' : 'Perhatian!',
                    text: data.message || 'Success!',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: `btn btn-${data.type || 'success'}`
                    }
                });
            });
        });

        $(document).ready(function() {
            const periodInput = document.getElementById('flatpickr-period-filter');
            if (periodInput) {
                const fp = flatpickr(periodInput, {
                    dateFormat: "Y-m",
                    altInput: true,
                    altFormat: "F Y",
                    plugins: [new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "Y-m",
                        altFormat: "F Y"
                    })],
                    onChange: function(selectedDates, dateStr) {
                        @this.call('setPeriod', dateStr);
                    }
                });

                // Clear filter button
                const clearBtn = $(
                    '<button type="button" class="btn btn-sm btn-outline-secondary ms-2" title="Clear Filter"><i class="ti ti-x"></i></button>'
                );
                clearBtn.on('click', function() {
                    fp.clear();
                    @this.call('setPeriod', null);
                });
                $(periodInput).closest('.input-group').after(clearBtn);
            }

            // Auto-refresh every 60 seconds for real-time updates
            setInterval(() => {
                Livewire.dispatch('refresh-data');
            }, 60000);
        });
    </script>
@endpush
