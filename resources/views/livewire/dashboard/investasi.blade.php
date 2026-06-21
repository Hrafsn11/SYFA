<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Investasi</h4>
            <div class="dashboard-subtitle text-muted">Ringkasan analitik dan arus investasi perusahaan</div>
        </div>
    </div>

    @php
        $totalInvest = (float) ($summaryData['total_deposito_pokok'] ?? 0);
        $totalReturn = (float) ($summaryData['total_pengembalian'] ?? 0);
        $totalOutstanding = (float) ($summaryData['total_outstanding'] ?? 0);
        $ratioReturn = $totalInvest > 0 ? ($totalReturn / $totalInvest) * 100 : 0;
    @endphp

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Investasi Masuk</div>
                            <div class="kpi-value">Rp {{ number_format($totalInvest, 0, ',', '.') }}</div>
                            <div class="kpi-meta text-muted">Total pokok investasi aktif</div>
                        </div>
                        <div class="kpi-icon bg-soft-success">
                            <i class="ti ti-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Pengembalian</div>
                            <div class="kpi-value">Rp {{ number_format($totalReturn, 0, ',', '.') }}</div>
                            <div class="kpi-meta text-muted">Total pokok + bunga dibayar</div>
                        </div>
                        <div class="kpi-icon bg-soft-info">
                            <i class="ti ti-arrow-down-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Outstanding</div>
                            <div class="kpi-value">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</div>
                            <div class="kpi-meta text-muted">Sisa investasi aktif berjalan</div>
                        </div>
                        <div class="kpi-icon bg-soft-warning">
                            <i class="ti ti-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Rasio Pengembalian</div>
                            <div class="kpi-value">{{ number_format($ratioReturn, 1) }}%</div>
                            <div class="kpi-meta text-muted">Pengembalian vs pokok investasi</div>
                        </div>
                        <div class="kpi-icon bg-soft-danger">
                            <i class="ti ti-chart-pie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 12-Month Trend Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Tren Investasi & Pengembalian</h5>
                        <small class="text-muted">
                            @if($bulanTahunTren)
                                Analisis arus dana harian untuk bulan yang dipilih
                            @else
                                Analisis arus dana 12 bulan terakhir
                            @endif
                        </small>
                    </div>
                    <div wire:ignore style="width: 180px;">
                        <select id="filterBulanTahunTren" class="form-select select2">
                            <option></option>
                            @foreach ($monthYearOptions as $value => $label)
                                <option value="{{ $value }}" {{ $bulanTahunTren == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartTrenInvestasi" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Maturities & Financial Status -->
    <div class="row g-4">
        <!-- Upcoming Maturing Tables (Left) -->
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-header pb-2">
                    <h5 class="card-title mb-0" style="font-size: 0.95rem;">Jatuh Tempo (30 Hari Ke Depan)</h5>
                    @if($isRestricted)
                        <small class="text-muted" style="font-size: 0.75rem;">Pemantauan jadwal pencairan investasi Anda</small>
                    @else
                        <small class="text-muted" style="font-size: 0.75rem;">Pemantauan pembayaran investor dan penagihan debitur</small>
                    @endif
                </div>
                <div class="card-body p-0 d-flex flex-column @if(!$isRestricted) justify-content-between @endif">
                    <!-- Section 1: Investasi Jatuh Tempo (Investor) -->
                    <div class="flex-grow-1">
                        @if(!$isRestricted)
                            <div class="px-4 py-2 bg-light border-top border-bottom fw-bold text-uppercase d-flex align-items-center text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                <i class="ti ti-wallet me-2 text-success" style="font-size: 0.85rem;"></i>
                                <span>Investasi Jatuh Tempo</span>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                <thead class="table-light text-muted" style="font-size: 0.7rem;">
                                    <tr>
                                        <th class="ps-4 py-2">No. Kontrak</th>
                                        <th>Investor</th>
                                        <th class="text-end py-2">Sisa Pokok</th>
                                        <th class="text-center py-2">Jatuh Tempo</th>
                                        <th class="text-center pe-4 py-2">Hari Tersisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(array_slice($upcomingMaturities, 0, 3) as $up)
                                        <tr>
                                            <td class="ps-4 py-2"><strong>{{ $up['nomor_kontrak'] }}</strong></td>
                                            <td class="py-2">
                                                <div class="fw-semibold">{{ $up['nama_investor'] }}</div>
                                                <small class="text-muted" style="font-size: 0.7rem;">PIC: {{ $up['pic'] }}</small>
                                            </td>
                                            <td class="text-end fw-semibold py-2">
                                                Rp {{ number_format($up['sisa_pokok'], 0, ',', '.') }}
                                            </td>
                                            <td class="text-center py-2">{{ $up['tanggal_jatuh_tempo'] }}</td>
                                            <td class="text-center pe-4 py-2">
                                                @if($up['hari_tersisa'] <= 7)
                                                    <span class="badge bg-label-danger py-1 px-2" style="font-size: 0.7rem;">
                                                        {{ $up['hari_tersisa'] }} hari lagi
                                                    </span>
                                                @elseif($up['hari_tersisa'] <= 15)
                                                    <span class="badge bg-label-warning py-1 px-2" style="font-size: 0.7rem;">
                                                        {{ $up['hari_tersisa'] }} hari lagi
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-success py-1 px-2" style="font-size: 0.7rem;">
                                                        {{ $up['hari_tersisa'] }} hari lagi
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4 ps-4 pe-4">
                                                Tidak ada investasi yang akan jatuh tempo dalam 30 hari ke depan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if(!$isRestricted)
                        <!-- Section 2: Penyaluran Jatuh Tempo (Debitur) -->
                        <div class="flex-grow-1 border-top">
                            <div class="px-4 py-2 bg-light border-bottom fw-bold text-uppercase d-flex align-items-center text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                <i class="ti ti-building me-2 text-primary" style="font-size: 0.85rem;"></i>
                                <span>Penyaluran Jatuh Tempo</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem;">
                                    <thead class="table-light text-muted" style="font-size: 0.7rem;">
                                        <tr>
                                            <th class="ps-4 py-2">No. Kontrak</th>
                                            <th class="py-2">Debitur</th>
                                            <th class="text-end py-2">Sisa Tagihan</th>
                                            <th class="text-center py-2">Jatuh Tempo</th>
                                            <th class="text-center pe-4 py-2">Hari Tersisa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(array_slice($upcomingDistributions, 0, 3) as $ud)
                                            <tr>
                                                <td class="ps-4 py-2"><strong>{{ $ud['nomor_kontrak_investasi'] }}</strong></td>
                                                <td class="py-2">
                                                    <div class="fw-semibold">{{ $ud['nama_debitur'] }}</div>
                                                    <small class="text-muted" style="font-size: 0.7rem;">PIC: {{ $ud['pic'] }}</small>
                                                </td>
                                                <td class="text-end fw-semibold py-2">
                                                    Rp {{ number_format($ud['sisa_tagihan'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center py-2">{{ $ud['tanggal_jatuh_tempo'] }}</td>
                                                <td class="text-center pe-4 py-2">
                                                    @if($ud['hari_tersisa'] <= 7)
                                                        <span class="badge bg-label-danger py-1 px-2" style="font-size: 0.7rem;">
                                                            {{ $ud['hari_tersisa'] }} hari lagi
                                                        </span>
                                                    @elseif($ud['hari_tersisa'] <= 15)
                                                        <span class="badge bg-label-warning py-1 px-2" style="font-size: 0.7rem;">
                                                            {{ $ud['hari_tersisa'] }} hari lagi
                                                        </span>
                                                    @else
                                                        <span class="badge bg-label-success py-1 px-2" style="font-size: 0.7rem;">
                                                            {{ $ud['hari_tersisa'] }} hari lagi
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4 ps-4 pe-4">
                                                    Tidak ada penyaluran dana yang akan jatuh tempo dalam 30 hari ke depan.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Investasi Summary (Right) -->
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-header pb-2">
                    <h5 class="card-title mb-0">Status Keuangan Investasi</h5>
                    @if($isRestricted)
                        <small class="text-muted" style="font-size: 0.75rem;">Rincian pengembalian saldo investasi Anda</small>
                    @else
                        <small class="text-muted" style="font-size: 0.75rem;">Rincian arus kas keluar dan sisa tagihan</small>
                    @endif
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        @if($isRestricted)
                            <div class="status-item">
                                <div class="status-label">Estimasi Bunga per Bulan</div>
                                <div class="status-value text-danger" style="color: #dc3545 !important;">
                                    Rp {{ number_format($summaryData['total_cof'] ?? 0, 0, ',', '.') }}
                                    <span class="status-sub">Bagi hasil yang didapatkan per bulan</span>
                                </div>
                            </div>
                        @else
                            <div class="status-item">
                                <div class="status-label">Total Cost of Fund (CoF)</div>
                                <div class="status-value text-danger">
                                    Rp {{ number_format($summaryData['total_cof'] ?? 0, 0, ',', '.') }}
                                    <span class="status-sub">Beban bunga per bulan</span>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-label">Total Dana Disalurkan</div>
                                <div class="status-value text-primary">
                                    Rp {{ number_format($summaryData['total_disalurkan'] ?? 0, 0, ',', '.') }}
                                    <span class="status-sub">Penyaluran aktif ke debitur</span>
                                </div>
                            </div>
                            <div class="status-item">
                                <div class="status-label">Outstanding Penyaluran</div>
                                <div class="status-value" style="color: #6610f2 !important;">
                                    Rp {{ number_format($summaryData['outstanding_penyaluran'] ?? 0, 0, ',', '.') }}
                                    <span class="status-sub">Sisa dana berjalan di debitur</span>
                                </div>
                            </div>
                        @endif

                        <div class="status-item">
                            <div class="status-label">Total Pengembalian</div>
                            <div class="status-value text-success">
                                Rp {{ number_format($summaryData['total_pengembalian'] ?? 0, 0, ',', '.') }}
                                <span class="status-sub">Pokok + Bunga terbayar</span>
                            </div>
                        </div>
                        <div class="status-item border-bottom-0">
                            <div class="status-label">Sisa Investasi Aktif</div>
                            <div class="status-value text-warning">
                                Rp {{ number_format($summaryData['total_outstanding'] ?? 0, 0, ',', '.') }}
                                <span class="status-sub">Sisa pokok + sisa bunga berjalan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Data Holders -->
    <div id="chart-data-holder" class="d-none" 
         data-tren-investasi='@json($trenInvestasi ?? [])'>
    </div>
</div>

@push('vendor-scripts')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('styles')
    <style>
        .dashboard-subtitle {
            font-size: 0.9rem;
        }

        .kpi-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.25s ease-in-out;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        .kpi-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.35rem;
        }

        .kpi-value {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .kpi-meta {
            font-size: 0.8rem;
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .bg-soft-danger { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
        .bg-soft-success { background: rgba(25, 135, 84, 0.12); color: #198754; }
        .bg-soft-warning { background: rgba(255, 193, 7, 0.16); color: #b58100; }
        .bg-soft-info { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
        .bg-soft-primary { background: rgba(13, 110, 253, 0.08); color: #0d6efd; }
        .bg-soft-indigo { background: rgba(102, 16, 242, 0.12); color: #6610f2; }

        .status-item {
            padding: 1rem 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .status-value {
            font-weight: 700;
            font-size: 1.15rem;
        }

        .status-sub {
            display: block;
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.1rem;
            font-weight: normal;
        }

        .select2-container { width: 100% !important; }
        #filterBulanTahunTren + .select2-container {
            width: 180px !important;
            min-width: 180px !important;
            max-width: 180px !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        .select2-container--default.select2-container--focus .select2-selection,
        .select2-container--default.select2-container--open .select2-selection {
            border-color: #86b7fe !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            'use strict';

            let trenChart = null;

            function getHolderData(attr) {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return null;
                try {
                    return JSON.parse(holder.getAttribute(attr) || '{}');
                } catch (e) {
                    console.error('Data parse error for ' + attr + ':', e);
                    return null;
                }
            }

            function renderTrenChart() {
                if (typeof ApexCharts === 'undefined') return;
                const data = getHolderData('data-tren-investasi');
                if (!data || !data.categories) return;

                const options = {
                    series: [
                        { name: 'Investasi Masuk', data: data.masuk || [] },
                        { name: 'Pengembalian', data: data.pengembalian || [] }
                    ],
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                    colors: ['#198754', '#0d6efd'],
                    xaxis: { categories: data.categories || [] },
                    yaxis: {
                        labels: {
                            formatter: (val) => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: (val) => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    },
                    noData: { text: 'Tidak ada data' }
                };

                const el = document.querySelector('#chartTrenInvestasi');
                if (!el) return;

                if (trenChart) {
                    trenChart.destroy();
                    trenChart = null;
                    el.innerHTML = '';
                }

                trenChart = new ApexCharts(el, options);
                trenChart.render();
            }

            function setupDataObserver() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return;
                const observer = new MutationObserver(() => {
                    renderTrenChart();
                });
                observer.observe(holder, { attributes: true, subtree: true });
            }

            function initSelect2() {
                const $select = $('#filterBulanTahunTren');
                if (!$select.length) return;
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({ 
                    minimumResultsForSearch: Infinity, 
                    width: 'resolve', 
                    dropdownAutoWidth: false,
                    allowClear: true,
                    placeholder: '12 Bulan Terakhir'
                });
                setTimeout(() => $select.next('.select2-container').css({ width: '180px', 'min-width': '180px', 'max-width': '180px' }), 10);
                $select.off('change.dashboard').on('change.dashboard', function () {
                    const val = $(this).val();
                    const wid = $(this).closest('[wire\\:id]').attr('wire:id');
                    if (wid && typeof Livewire !== 'undefined') Livewire.find(wid).set('bulanTahunTren', val || null);
                });
            }

            function initializeDashboard() {
                if (typeof ApexCharts === 'undefined') {
                    setTimeout(initializeDashboard, 200);
                    return;
                }
                initSelect2();
                renderTrenChart();
                setupDataObserver();
            }

            $(document).ready(function() {
                setTimeout(initializeDashboard, 300);
            });

            document.addEventListener('livewire:navigated', function() {
                setTimeout(initializeDashboard, 200);
            });
        })();
    </script>
@endpush
