<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard Pembiayaan SFinance</h4>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Disbursement</h6>
                            <h4 class="mb-2 fw-bold">Rp
                                {{ number_format($summaryData['total_disbursement'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center">
                                @php
                                    $persen = $summaryData['total_disbursement_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_disbursement_is_increase'] ?? false;
                                    $isNew = $summaryData['total_disbursement_is_new'] ?? false;
                                @endphp
                                @if ($isNew)
                                    <i class="ti ti-sparkles text-info me-1"></i>
                                    <small class="text-info fw-medium">Baru dari
                                        {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <i
                                        class="ti {{ $isIncrease ? 'ti-arrow-up' : 'ti-arrow-down' }} {{ $isIncrease ? 'text-success' : 'text-danger' }} me-1"></i>
                                    <small
                                        class="{{ $isIncrease ? 'text-success' : 'text-danger' }} fw-medium">{{ number_format($persen, 1) }}%</small>
                                    <small class="text-muted ms-1">dari
                                        {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Pembayaran Masuk</h6>
                            <h4 class="mb-2 fw-bold">Rp
                                {{ number_format($summaryData['total_pembayaran_masuk'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center">
                                @php
                                    $persen = $summaryData['total_pembayaran_masuk_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_pembayaran_masuk_is_increase'] ?? false;
                                    $isNew = $summaryData['total_pembayaran_masuk_is_new'] ?? false;
                                @endphp
                                @if ($isNew)
                                    <i class="ti ti-sparkles text-info me-1"></i>
                                    <small class="text-info fw-medium">Baru dari
                                        {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <i
                                        class="ti {{ $isIncrease ? 'ti-arrow-up' : 'ti-arrow-down' }} {{ $isIncrease ? 'text-success' : 'text-danger' }} me-1"></i>
                                    <small
                                        class="{{ $isIncrease ? 'text-success' : 'text-danger' }} fw-medium">{{ number_format($persen, 1) }}%</small>
                                    <small class="text-muted ms-1">dari
                                        {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Sisa Belum Terbayar</h6>
                            <h4 class="mb-2 fw-bold">Rp
                                {{ number_format($summaryData['total_sisa_belum_terbayar'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center">
                                @php
                                    $persen = $summaryData['total_sisa_belum_terbayar_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_sisa_belum_terbayar_is_increase'] ?? false;
                                    $isNew = $summaryData['total_sisa_belum_terbayar_is_new'] ?? false;
                                    $colorClass = $isIncrease ? 'text-danger' : 'text-success';
                                @endphp
                                @if ($isNew)
                                    <i class="ti ti-sparkles text-info me-1"></i>
                                    <small class="text-info fw-medium">Baru dari
                                        {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <i
                                        class="ti {{ $isIncrease ? 'ti-arrow-up' : 'ti-arrow-down' }} {{ $colorClass }} me-1"></i>
                                    <small
                                        class="{{ $colorClass }} fw-medium">{{ number_format($persen, 1) }}%</small>
                                    <small class="text-muted ms-1">dari
                                        {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Outstanding Piutang</h6>
                            <h4 class="mb-2 fw-bold">Rp
                                {{ number_format($summaryData['total_outstanding_piutang'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center">
                                @php
                                    $persen = $summaryData['total_outstanding_piutang_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_outstanding_piutang_is_increase'] ?? false;
                                    $isNew = $summaryData['total_outstanding_piutang_is_new'] ?? false;
                                    $colorClass = $isIncrease ? 'text-danger' : 'text-success';
                                @endphp
                                @if ($isNew)
                                    <i class="ti ti-sparkles text-info me-1"></i>
                                    <small class="text-info fw-medium">Baru dari
                                        {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @else
                                    <i
                                        class="ti {{ $isIncrease ? 'ti-arrow-up' : 'ti-arrow-down' }} {{ $colorClass }} me-1"></i>
                                    <small
                                        class="{{ $colorClass }} fw-medium">{{ number_format($persen, 1) }}%</small>
                                    <small class="text-muted ms-1">dari
                                        {{ $summaryData['previous_month_name'] ?? 'bulan lalu' }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Disbursement Pokok dan Bagi Hasil</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanDisbursement" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach ($monthOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $bulanDisbursement == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartDisbursement" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Pokok dan Bagi Hasil</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanPembayaran" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach ($monthOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $bulanPembayaran == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartPembayaran" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Sisa yang Belum Terbayar</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanSisa" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach ($monthOptions as $value => $label)
                                <option value="{{ $value }}" {{ $bulanSisa == $value ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartSisaBelumTerbayar" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Piutang Per Tahun</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterTahunPiutang" class="form-select select2" data-placeholder="Pilih Tahun">
                            @foreach ($yearOptions as $value => $label)
                                <option value="{{ $value }}" {{ $tahunPiutang == $value ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartPembayaranPiutang" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Total AR Berdasarkan Kriteria Keterlambatan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label class="form-label small">Bulan</label>
                            <div wire:ignore>
                                <select id="filterBulanTable" class="form-select select2"
                                    data-placeholder="Pilih Bulan">
                                    <option value=""></option>
                                    @foreach ($monthOptions as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $bulanTable == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Tahun</label>
                            <div wire:ignore>
                                <select id="filterTahunTable" class="form-select select2"
                                    data-placeholder="Pilih Tahun">
                                    @foreach ($yearOptions as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ $tahunTable == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>DEBITUR</th>
                                    <th>DEL 1-30</th>
                                    <th>DEL 31-60</th>
                                    <th>DEL 61-90</th>
                                    <th>NPL 91-179</th>
                                    <th>WRITE OFF >180</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($arTableData ?? []) as $row)
                                    <tr>
                                        <td>{{ $row['debitur'] ?? '-' }}</td>
                                        <td>Rp {{ number_format($row['del_1_30'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['del_31_60'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['del_61_90'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['npl_91_179'] ?? 0, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($row['write_off'] ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Perbandingan AR dan Utang Pengembalian Deposito</h5>
                    <div class="d-flex gap-2">
                        <div wire:ignore style="width: 120px;">
                            <select id="filterBulanComparison1" class="form-select select2"
                                data-placeholder="Bulan 1">
                                <option value=""></option>
                                @foreach ($monthOptions as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ ($bulan1 ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div wire:ignore style="width: 120px;">
                            <select id="filterBulanComparison2" class="form-select select2"
                                data-placeholder="Bulan 2">
                                <option value=""></option>
                                @foreach ($monthOptions as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ ($bulan2 ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartComparison" style="min-height: 300px;"></div>

                    <div class="row g-3 mt-4">
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3" style="background-color: #f0f7ff;">
                                <small class="text-muted d-block mb-2">Selisih AR</small>
                                <h5 class="mb-0 fw-bold">
                                    Rp
                                    {{ number_format(abs($chartData['comparison']['ar_selisih'] ?? 0), 0, ',', '.') }}
                                </h5>
                                @php $arSelisih = $chartData['comparison']['ar_selisih'] ?? 0; @endphp
                                <small class="text-muted">
                                    @if ($arSelisih > 0)
                                        <i class="ti ti-arrow-up text-danger"></i> Naik
                                    @elseif($arSelisih < 0)
                                        <i class="ti ti-arrow-down text-success"></i> Turun
                                    @else
                                        <i class="ti ti-minus text-secondary"></i> Tidak ada perubahan
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="p-3 rounded-3" style="background-color: #fff8f0;">
                                <small class="text-muted d-block mb-2">Selisih Utang Pengembalian Deposito</small>
                                <h5 class="mb-0 fw-bold">
                                    Rp
                                    {{ number_format(abs($chartData['comparison']['utang_selisih'] ?? 0), 0, ',', '.') }}
                                </h5>
                                @php $utangSelisih = $chartData['comparison']['utang_selisih'] ?? 0; @endphp
                                <small class="text-muted">
                                    @if ($utangSelisih > 0)
                                        <i class="ti ti-arrow-up text-danger"></i> Naik
                                    @elseif($utangSelisih < 0)
                                        <i class="ti ti-arrow-down text-success"></i> Turun
                                    @else
                                        <i class="ti ti-minus text-secondary"></i> Tidak ada perubahan
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="chart-data-holder" class="d-none" data-disbursement='@json($chartData['disbursement'] ?? [])'
        data-pembayaran='@json($chartData['pembayaran'] ?? [])' data-sisa='@json($chartData['sisa_belum_terbayar'] ?? [])'
        data-piutang='@json($chartData['pembayaran_piutang_tahun'] ?? [])' data-comparison='@json($chartData['comparison'] ?? [])'>
    </div>
</div>

@push('vendor-scripts')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('styles')
    <style>
        .select2-container {
            width: 100% !important;
        }

        #filterBulanDisbursement+.select2-container,
        #filterBulanPembayaran+.select2-container,
        #filterBulanSisa+.select2-container,
        #filterTahunPiutang+.select2-container {
            width: 150px !important;
            min-width: 150px !important;
            max-width: 150px !important;
        }

        #filterBulanComparison1+.select2-container,
        #filterBulanComparison2+.select2-container {
            width: 120px !important;
            min-width: 120px !important;
            max-width: 120px !important;
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

            let charts = {
                disbursement: null,
                pembayaran: null,
                sisa: null,
                piutang: null,
                comparison: null
            };

            const chartColors = ['#71dd37', '#ffab00'];

            function getBarOptions(series, categories, colors = chartColors) {
                return {
                    series: series,
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        },
                        animations: {
                            enabled: true,
                            speed: 300
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: (val) => "Rp " + val.toLocaleString('id-ID'),
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    colors: colors,
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    tooltip: {
                        y: {
                            formatter: (val) => "Rp " + val.toLocaleString('id-ID')
                        }
                    },
                    noData: {
                        text: 'Tidak ada data',
                        style: {
                            fontSize: '14px'
                        }
                    }
                };
            }

            function getChartData() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return null;

                try {
                    return {
                        disbursement: JSON.parse(holder.getAttribute('data-disbursement') || '{}'),
                        pembayaran: JSON.parse(holder.getAttribute('data-pembayaran') || '{}'),
                        sisa: JSON.parse(holder.getAttribute('data-sisa') || '{}'),
                        piutang: JSON.parse(holder.getAttribute('data-piutang') || '{}'),
                        comparison: JSON.parse(holder.getAttribute('data-comparison') || '{}')
                    };
                } catch (e) {
                    console.error('Error parsing chart data:', e);
                    return null;
                }
            }

            function updateChart(chartInstance, series, categories) {
                if (!chartInstance) return false;

                try {
                    chartInstance.updateOptions({
                        xaxis: {
                            categories: categories
                        }
                    }, false, false);
                    chartInstance.updateSeries(series, true);
                    return true;
                } catch (e) {
                    console.error('Error updating chart:', e);
                    return false;
                }
            }

            function createChart(elementId, series, categories, colors = chartColors) {
                const el = document.querySelector('#' + elementId);
                if (!el) return null;

                const chart = new ApexCharts(el, getBarOptions(series, categories, colors));
                chart.render();
                return chart;
            }

            function renderCharts() {
                if (typeof ApexCharts === 'undefined') return;

                const data = getChartData();
                if (!data) return;

                const disbursementSeries = [{
                        name: 'Pokok',
                        data: data.disbursement.pokok || []
                    },
                    {
                        name: 'Bagi Hasil',
                        data: data.disbursement.bagi_hasil || []
                    }
                ];
                const disbursementCategories = data.disbursement.categories || [];

                if (charts.disbursement) {
                    updateChart(charts.disbursement, disbursementSeries, disbursementCategories);
                } else {
                    charts.disbursement = createChart('chartDisbursement', disbursementSeries, disbursementCategories);
                }

                const pembayaranSeries = [{
                        name: 'Pokok',
                        data: data.pembayaran.pokok || []
                    },
                    {
                        name: 'Bagi Hasil',
                        data: data.pembayaran.bagi_hasil || []
                    }
                ];
                const pembayaranCategories = data.pembayaran.categories || [];

                if (charts.pembayaran) {
                    updateChart(charts.pembayaran, pembayaranSeries, pembayaranCategories);
                } else {
                    charts.pembayaran = createChart('chartPembayaran', pembayaranSeries, pembayaranCategories);
                }

                const sisaSeries = [{
                        name: 'Pokok',
                        data: data.sisa.pokok || []
                    },
                    {
                        name: 'Bagi Hasil',
                        data: data.sisa.bagi_hasil || []
                    }
                ];
                const sisaCategories = data.sisa.categories || [];

                if (charts.sisa) {
                    updateChart(charts.sisa, sisaSeries, sisaCategories);
                } else {
                    charts.sisa = createChart('chartSisaBelumTerbayar', sisaSeries, sisaCategories);
                }

                const piutangSeries = [{
                        name: 'Pokok',
                        data: data.piutang.pokok || []
                    },
                    {
                        name: 'Bagi Hasil',
                        data: data.piutang.bagi_hasil || []
                    }
                ];
                const piutangCategories = data.piutang.categories || [];

                if (charts.piutang) {
                    updateChart(charts.piutang, piutangSeries, piutangCategories);
                } else {
                    charts.piutang = createChart('chartPembayaranPiutang', piutangSeries, piutangCategories);
                }

                const comparisonSeries = [{
                        name: 'AR',
                        data: [data.comparison.ar_bulan2 || 0, data.comparison.ar_bulan1 || 0]
                    },
                    {
                        name: 'Utang Pengembalian',
                        data: [data.comparison.utang_bulan2 || 0, data.comparison.utang_bulan1 || 0]
                    }
                ];
                const comparisonCategories = data.comparison.categories || ['Bulan Lalu', 'Bulan Ini'];

                if (charts.comparison) {
                    updateChart(charts.comparison, comparisonSeries, comparisonCategories);
                } else {
                    charts.comparison = createChart('chartComparison', comparisonSeries, comparisonCategories);
                }
            }

            function initSelect2() {
                const filterConfigs = [{
                        id: 'filterBulanDisbursement',
                        property: 'bulanDisbursement',
                        width: 150
                    },
                    {
                        id: 'filterBulanPembayaran',
                        property: 'bulanPembayaran',
                        width: 150
                    },
                    {
                        id: 'filterBulanSisa',
                        property: 'bulanSisa',
                        width: 150
                    },
                    {
                        id: 'filterTahunPiutang',
                        property: 'tahunPiutang',
                        width: 150
                    },
                    {
                        id: 'filterBulanTable',
                        property: 'bulanTable',
                        width: null
                    },
                    {
                        id: 'filterTahunTable',
                        property: 'tahunTable',
                        width: null
                    },
                    {
                        id: 'filterBulanComparison1',
                        property: 'bulan1',
                        width: 120
                    },
                    {
                        id: 'filterBulanComparison2',
                        property: 'bulan2',
                        width: 120
                    }
                ];

                filterConfigs.forEach(config => {
                    const $select = $('#' + config.id);
                    if (!$select.length) return;

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        placeholder: $select.data('placeholder') || 'Pilih...',
                        minimumResultsForSearch: Infinity,
                        width: 'resolve',
                        allowClear: true,
                        dropdownAutoWidth: false
                    });

                    if (config.width) {
                        setTimeout(() => {
                            $select.next('.select2-container').css({
                                'width': config.width + 'px',
                                'min-width': config.width + 'px',
                                'max-width': config.width + 'px'
                            });
                        }, 10);
                    }

                    $select.off('change.dashboard').on('change.dashboard', function() {
                        const val = $(this).val();
                        const $component = $(this).closest('[wire\\:id]');
                        const componentId = $component.attr('wire:id');

                        if (componentId && typeof Livewire !== 'undefined') {
                            Livewire.find(componentId).set(config.property, val || null);
                        }
                    });
                });
            }

            function setupDataObserver() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return;

                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'attributes') {
                            renderCharts();
                        }
                    });
                });

                observer.observe(holder, {
                    attributes: true,
                    attributeFilter: ['data-disbursement', 'data-pembayaran', 'data-sisa', 'data-piutang',
                        'data-comparison'
                    ]
                });
            }

            function initializeDashboard() {
                if (typeof ApexCharts === 'undefined') {
                    setTimeout(initializeDashboard, 200);
                    return;
                }

                initSelect2();
                renderCharts();
                setupDataObserver();
            }

            $(document).ready(function() {
                setTimeout(initializeDashboard, 300);
            });

            document.addEventListener('livewire:navigated', function() {
                setTimeout(initializeDashboard, 200);
            });

            document.addEventListener('livewire:init', function() {
                Livewire.hook('morph.updated', function({
                    el,
                    component
                }) {
                    if (el.id === 'chart-data-holder' || el.querySelector('#chart-data-holder')) {
                        setTimeout(renderCharts, 100);
                    }
                });
            });
        })();
    </script>
@endpush
