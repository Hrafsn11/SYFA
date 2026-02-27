<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">
            Daftar AR Performance
            @if($bulan)
                @php
                    $bulanNama = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                @endphp
                - {{ $bulanNama[$bulan] ?? $bulan }}
            @endif
            Tahun {{ $tahun }}
        </h5>
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 monitoring-ar-table" style="white-space: nowrap;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle" style="width: 50px; position: sticky; left: 0; z-index: 3; background-color: #f8f9fa !important; box-shadow: 2px 0 5px rgba(0,0,0,0.07);">No</th>
                        <th class="text-center align-middle" style="min-width: 200px; position: sticky; left: 50px; z-index: 3; background-color: #f8f9fa !important; box-shadow: 2px 0 5px rgba(0,0,0,0.07);">Debitur</th>
                        <th class="text-center" style="min-width: 150px;">Belum Jatuh Tempo</th>
                        <th class="text-center" style="min-width: 120px;">By Transaction</th>
                        <th class="text-center" style="min-width: 150px;">DEL (1-30)</th>
                        <th class="text-center" style="min-width: 120px;">By Transaction</th>
                        <th class="text-center" style="min-width: 150px;">DEL (31-60)</th>
                        <th class="text-center" style="min-width: 120px;">By Transaction</th>
                        <th class="text-center" style="min-width: 150px;">DEL (61-90)</th>
                        <th class="text-center" style="min-width: 120px;">By Transaction</th>
                        <th class="text-center" style="min-width: 150px;">NPL (91-179)</th>
                        <th class="text-center" style="min-width: 120px;">By Transaction</th>
                        <th class="text-center" style="min-width: 150px;">WriteOff (>180)</th>
                        <th class="text-center" style="min-width: 120px;">By Transaction</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arData as $index => $debitur)
                        <tr>
                            <td class="text-center sticky-col" style="position: sticky; left: 0; z-index: 2; background-color: #ffffff !important; box-shadow: 2px 0 5px rgba(0,0,0,0.07);">{{ $index + 1 }}</td>
                            <td class="fw-semibold sticky-col" style="position: sticky; left: 50px; z-index: 2; background-color: #ffffff !important; box-shadow: 2px 0 5px rgba(0,0,0,0.07);">{{ $debitur['nama_debitur'] }}</td>

                            {{-- Belum Jatuh Tempo --}}
                            <td class="text-end">
                                @if($debitur['belum_jatuh_tempo']['total'] > 0)
                                    <span class="text-success fw-semibold">
                                        Rp {{ number_format($debitur['belum_jatuh_tempo']['total'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($debitur['belum_jatuh_tempo']['count'] > 0)
                                    <a href="javascript:void(0);" 
                                       class="view-transactions text-primary text-decoration-none"
                                       data-debitur-id="{{ $debitur['id_debitur'] }}"
                                       data-debitur-name="{{ $debitur['nama_debitur'] }}"
                                       data-category="belum_jatuh_tempo">
                                        <i class="ti ti-eye me-1"></i>{{ $debitur['belum_jatuh_tempo']['count'] }} transaksi
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- DEL (1-30) --}}
                            <td class="text-end">
                                @if($debitur['del_1_30']['total'] > 0)
                                    <span class="text-warning fw-semibold">
                                        Rp {{ number_format($debitur['del_1_30']['total'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($debitur['del_1_30']['count'] > 0)
                                    <a href="javascript:void(0);"
                                       class="view-transactions text-warning text-decoration-none"
                                       data-debitur-id="{{ $debitur['id_debitur'] }}"
                                       data-debitur-name="{{ $debitur['nama_debitur'] }}"
                                       data-category="del_1_30">
                                        <i class="ti ti-eye me-1"></i>{{ $debitur['del_1_30']['count'] }} transaksi
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- DEL (31-60) --}}
                            <td class="text-end">
                                @if($debitur['del_31_60']['total'] > 0)
                                    <span class="text-warning fw-semibold">
                                        Rp {{ number_format($debitur['del_31_60']['total'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($debitur['del_31_60']['count'] > 0)
                                    <a href="javascript:void(0);"
                                       class="view-transactions text-warning text-decoration-none"
                                       data-debitur-id="{{ $debitur['id_debitur'] }}"
                                       data-debitur-name="{{ $debitur['nama_debitur'] }}"
                                       data-category="del_31_60">
                                        <i class="ti ti-eye me-1"></i>{{ $debitur['del_31_60']['count'] }} transaksi
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- DEL (61-90) --}}
                            <td class="text-end">
                                @if($debitur['del_61_90']['total'] > 0)
                                    <span class="text-warning fw-semibold">
                                        Rp {{ number_format($debitur['del_61_90']['total'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($debitur['del_61_90']['count'] > 0)
                                    <a href="javascript:void(0);"
                                       class="view-transactions text-warning text-decoration-none"
                                       data-debitur-id="{{ $debitur['id_debitur'] }}"
                                       data-debitur-name="{{ $debitur['nama_debitur'] }}"
                                       data-category="del_61_90">
                                        <i class="ti ti-eye me-1"></i>{{ $debitur['del_61_90']['count'] }} transaksi
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- NPL (91-179) --}}
                            <td class="text-end">
                                @if($debitur['npl_91_179']['total'] > 0)
                                    <span class="text-danger fw-semibold">
                                        Rp {{ number_format($debitur['npl_91_179']['total'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($debitur['npl_91_179']['count'] > 0)
                                    <a href="javascript:void(0);"
                                       class="view-transactions text-danger text-decoration-none"
                                       data-debitur-id="{{ $debitur['id_debitur'] }}"
                                       data-debitur-name="{{ $debitur['nama_debitur'] }}"
                                       data-category="npl_91_179">
                                        <i class="ti ti-eye me-1"></i>{{ $debitur['npl_91_179']['count'] }} transaksi
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- WriteOff (>180) --}}
                            <td class="text-end">
                                @if($debitur['writeoff_180']['total'] > 0)
                                    <span class="text-dark fw-semibold">
                                        Rp {{ number_format($debitur['writeoff_180']['total'], 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($debitur['writeoff_180']['count'] > 0)
                                    <a href="javascript:void(0);"
                                       class="view-transactions text-dark text-decoration-none"
                                       data-debitur-id="{{ $debitur['id_debitur'] }}"
                                       data-debitur-name="{{ $debitur['nama_debitur'] }}"
                                       data-category="writeoff_180">
                                        <i class="ti ti-eye me-1"></i>{{ $debitur['writeoff_180']['count'] }} transaksi
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14">
                                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                                    <div class="mb-3" style="opacity: 0.25;">
                                        <i class="ti ti-report-off" style="font-size: 3.5rem; line-height: 1;"></i>
                                    </div>
                                    <p class="fw-semibold mb-1" style="font-size: 1rem; color: #566a7f;">
                                        Belum ada data pembayaran
                                    </p>
                                    <p class="small text-center mb-0" style="max-width: 340px; color: #8592a3;">
                                        Tidak ada transaksi untuk periode
                                        @if($bulan)
                                            @php
                                                $emptyBulanNama = [
                                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                                ];
                                            @endphp
                                            <strong>{{ $emptyBulanNama[$bulan] ?? $bulan }} {{ $tahun }}</strong>.
                                        @else
                                            <strong>Tahun {{ $tahun }}</strong>.
                                        @endif
                                        Data akan muncul setelah debitur melakukan pembayaran.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ── Sticky NO + DEBITUR columns ─────────────────────── */
    .monitoring-ar-table th:nth-child(1),
    .monitoring-ar-table td:nth-child(1) {
        position: sticky;
        left: 0;
        z-index: 2;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.07);
    }
    .monitoring-ar-table th:nth-child(2),
    .monitoring-ar-table td:nth-child(2) {
        position: sticky;
        left: 50px;
        z-index: 2;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.07);
    }
    /* Header cells sit above body cells */
    .monitoring-ar-table thead th:nth-child(1),
    .monitoring-ar-table thead th:nth-child(2) {
        z-index: 3;
        background: #f8f9fa !important;
    }
    /* Body rows: white by default */
    .monitoring-ar-table tbody td:nth-child(1),
    .monitoring-ar-table tbody td:nth-child(2) {
        background-color: #ffffff !important;
    }
    /* Preserve hover color on sticky cells */
    .monitoring-ar-table tbody tr:hover td.sticky-col {
        background-color: #eef2ff !important;
    }
</style>
@endpush
