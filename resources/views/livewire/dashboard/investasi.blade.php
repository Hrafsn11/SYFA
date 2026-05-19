<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Investasi</h4>
            <div class="dashboard-subtitle text-muted">Ringkasan arus investasi</div>
        </div>
    </div>

    @php
        $totalInvest = (float) ($summaryData['total_deposito_pokok'] ?? 0);
        $totalReturn = (float) ($summaryData['total_pengembalian'] ?? 0);
        $totalOutstanding = (float) ($summaryData['total_outstanding'] ?? 0);
        $ratioReturn = $totalInvest > 0 ? ($totalReturn / $totalInvest) * 100 : 0;
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Investasi Masuk</div>
                            <div class="kpi-value">Rp {{ number_format($totalInvest, 0, ',', '.') }}</div>
                            <div class="kpi-meta text-muted">Total pokok investasi</div>
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
                            <div class="kpi-meta text-muted">Total pengembalian</div>
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
                            <div class="kpi-meta text-muted">Sisa investasi aktif</div>
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
                            <div class="kpi-meta text-muted">Pengembalian vs pokok</div>
                        </div>
                        <div class="kpi-icon bg-soft-danger">
                            <i class="ti ti-chart-pie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Investasi Pokok Per Bulan</h5>
                    <div wire:ignore style="width: 160px;">
                        <select id="filterBulanInvestasiPokok" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach ($monthOptions as $value => $label)
                                <option value="{{ $value }}" {{ $bulanInvestasiPokok == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartInvestasiPokok" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Investasi</h5>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <div class="status-label">Total CoF</div>
                        <div class="status-value">
                            Rp {{ number_format($summaryData['total_cof'] ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Total Pengembalian</div>
                        <div class="status-value">
                            Rp {{ number_format($summaryData['total_pengembalian'] ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Sisa Investasi</div>
                        <div class="status-value">
                            Rp {{ number_format($summaryData['total_outstanding'] ?? 0, 0, ',', '.') }}
                            <span class="status-sub">Nilai investasi aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="chart-data-holder" class="d-none" data-investasi-pokok='@json($chartData['investasi_pokok'] ?? [])'></div>
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

        .status-item {
            padding: 0.75rem 0;
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
            font-weight: 600;
        }

        .status-sub {
            display: block;
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.2rem;
        }

        .select2-container { width: 100% !important; }
        #filterBulanInvestasiPokok + .select2-container {
            width: 160px !important;
            min-width: 160px !important;
            max-width: 160px !important;
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

            let investasiChart = null;

            function getChartData() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return null;
                try {
                    return JSON.parse(holder.getAttribute('data-investasi-pokok') || '{}');
                } catch (e) {
                    console.error('Chart data parse error:', e);
                    return null;
                }
            }

            function renderChart() {
                if (typeof ApexCharts === 'undefined') return;
                const data = getChartData();
                if (!data || !data.categories) return;

                const options = {
                    series: data.series || [],
                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: { show: false }
                    },
                    plotOptions: {
                        bar: { columnWidth: '50%', borderRadius: 6 }
                    },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: { categories: data.categories || [] },
                    yaxis: {
                        labels: {
                            formatter: (val) => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    },
                    colors: ['#0ea5e9'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: {
                        y: {
                            formatter: (val) => 'Rp ' + val.toLocaleString('id-ID')
                        }
                    },
                    noData: { text: 'Tidak ada data' }
                };

                const el = document.querySelector('#chartInvestasiPokok');
                if (!el) return;

                if (investasiChart) {
                    investasiChart.destroy();
                    investasiChart = null;
                    el.innerHTML = '';
                }

                investasiChart = new ApexCharts(el, options);
                investasiChart.render();
            }

            function initSelect2() {
                const $select = $('#filterBulanInvestasiPokok');
                if (!$select.length) return;
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({ minimumResultsForSearch: Infinity, width: 'resolve', dropdownAutoWidth: false });
                setTimeout(() => $select.next('.select2-container').css({ width: '160px', 'min-width': '160px', 'max-width': '160px' }), 10);
                $select.off('change.dashboard').on('change.dashboard', function () {
                    const val = $(this).val();
                    const wid = $(this).closest('[wire\\:id]').attr('wire:id');
                    if (wid && typeof Livewire !== 'undefined') Livewire.find(wid).set('bulanInvestasiPokok', val || null);
                });
            }

            function setupDataObserver() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return;
                const observer = new MutationObserver(() => renderChart());
                observer.observe(holder, { attributes: true, attributeFilter: ['data-investasi-pokok'] });
            }

            function initializeDashboard() {
                if (typeof ApexCharts === 'undefined') {
                    setTimeout(initializeDashboard, 200);
                    return;
                }
                initSelect2();
                renderChart();
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
