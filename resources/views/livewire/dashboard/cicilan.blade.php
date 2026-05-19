<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Cicilan</h4>
            <div class="dashboard-subtitle text-muted">Ringkasan restrukturisasi cicilan</div>
        </div>
    </div>

    @php
        $totalCicilan = (float) ($summaryData['total_cicilan_keseluruhan'] ?? 0);
        $totalTerbayar = (float) ($summaryData['total_terbayar'] ?? 0);
        $totalSisa = max(0, $totalCicilan - $totalTerbayar);
        $persenTerbayar = (float) ($summaryData['persen_terbayar'] ?? 0);
        $debiturCount = is_array($debiturMonitoringData ?? null) ? count($debiturMonitoringData) : 0;
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 kpi-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="kpi-label">Total Pengajuan</div>
                            <div class="kpi-value">{{ number_format($summaryData['total_pengajuan'] ?? 0) }}</div>
                            <div class="kpi-meta text-muted">Semua pengajuan</div>
                        </div>
                        <div class="kpi-icon bg-soft-info">
                            <i class="ti ti-file-text"></i>
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
                            <div class="kpi-label">Dalam Proses</div>
                            <div class="kpi-value">{{ number_format($summaryData['dalam_proses'] ?? 0) }}</div>
                            <div class="kpi-meta text-muted">Belum selesai</div>
                        </div>
                        <div class="kpi-icon bg-soft-warning">
                            <i class="ti ti-loader"></i>
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
                            <div class="kpi-label">Total Cicilan</div>
                            <div class="kpi-value">Rp {{ number_format($totalCicilan, 0, ',', '.') }}</div>
                            <div class="kpi-meta text-muted">Total penyesuaian</div>
                        </div>
                        <div class="kpi-icon bg-soft-success">
                            <i class="ti ti-currency-dollar"></i>
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
                            <div class="kpi-label">Persentase Terbayar</div>
                            <div class="kpi-value">{{ number_format($persenTerbayar, 1) }}%</div>
                            <div class="kpi-meta text-muted">Cicilan terbayar</div>
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
                    <h5 class="card-title mb-0">Tren Pengajuan Cicilan</h5>
                    <div wire:ignore style="width: 160px;">
                        <select id="filterBulanTahunTren" class="form-select select2" data-placeholder="Pilih Bulan">
                            @foreach ($monthYearOptions as $value => $label)
                                <option value="{{ $value }}" {{ $bulanTahunTren == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartTrenPengajuan" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Cicilan</h5>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <div class="status-label">Total Terbayar</div>
                        <div class="status-value">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Sisa Pembayaran</div>
                        <div class="status-value">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
                    </div>
                    <div class="status-item">
                        <div class="status-label">Debitur Terpantau</div>
                        <div class="status-value">
                            {{ number_format($debiturCount) }} debitur
                            <span class="status-sub">Monitoring aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="chart-data-holder" class="d-none" data-tren='@json($chartData['tren_pengajuan'] ?? [])'></div>
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
        #filterBulanTahunTren + .select2-container {
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

            let cicilanChart = null;

            function getChartData() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return null;
                try {
                    return JSON.parse(holder.getAttribute('data-tren') || '{}');
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
                    series: [
                        { name: 'Masuk', data: data.masuk || [] },
                        { name: 'Selesai', data: data.selesai || [] },
                        { name: 'Ditolak', data: data.ditolak || [] }
                    ],
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
                            formatter: (val) => val.toLocaleString('id-ID')
                        }
                    },
                    colors: ['#0ea5e9', '#22c55e', '#ef4444'],
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: {
                        y: {
                            formatter: (val) => val.toLocaleString('id-ID')
                        }
                    },
                    noData: { text: 'Tidak ada data' }
                };

                const el = document.querySelector('#chartTrenPengajuan');
                if (!el) return;

                if (cicilanChart) {
                    cicilanChart.destroy();
                    cicilanChart = null;
                    el.innerHTML = '';
                }

                cicilanChart = new ApexCharts(el, options);
                cicilanChart.render();
            }

            function initSelect2() {
                const $select = $('#filterBulanTahunTren');
                if (!$select.length) return;
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({ minimumResultsForSearch: Infinity, width: 'resolve', dropdownAutoWidth: false });
                setTimeout(() => $select.next('.select2-container').css({ width: '160px', 'min-width': '160px', 'max-width': '160px' }), 10);
                $select.off('change.dashboard').on('change.dashboard', function () {
                    const val = $(this).val();
                    const wid = $(this).closest('[wire\\:id]').attr('wire:id');
                    if (wid && typeof Livewire !== 'undefined') Livewire.find(wid).set('bulanTahunTren', val || null);
                });
            }

            function setupDataObserver() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return;
                const observer = new MutationObserver(() => renderChart());
                observer.observe(holder, { attributes: true, attributeFilter: ['data-tren'] });
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
