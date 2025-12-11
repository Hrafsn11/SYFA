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
            <table class="table table-bordered table-hover mb-0" style="white-space: nowrap;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center align-middle" style="width: 50px;">No</th>
                        <th class="text-center align-middle" style="min-width: 200px;">Debitur</th>
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
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $debitur['nama_debitur'] }}</td>

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
                            <td colspan="14" class="text-center text-muted py-4">
                                <i class="ti ti-inbox mb-2" style="font-size: 2rem;"></i>
                                <p class="mb-0">Tidak ada data pembayaran</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
