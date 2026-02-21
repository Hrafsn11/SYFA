<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard Investasi Jenis Investasi SFinance</h4>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Jenis Investasi Pokok</h6>
                            <h4 class="mb-2 fw-bold">Rp
                                {{ number_format($summaryData['total_jenis investasi_pokok'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center">
                                @php
                                    $persen = $summaryData['total_jenis investasi_pokok_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_jenis investasi_pokok_is_increase'] ?? false;
                                    $isNew = $summaryData['total_jenis investasi_pokok_is_new'] ?? false;
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
                            <h6 class="text-muted mb-2">Total CoF (Cost of Fund)</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_cof'] ?? 0, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center">
                                @php
                                    $persen = $summaryData['total_cof_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_cof_is_increase'] ?? false;
                                    $isNew = $summaryData['total_cof_is_new'] ?? false;
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
                            <h6 class="text-muted mb-2">Total Pengembalian</h6>
                            <h4 class="mb-2 fw-bold">Rp
                                {{ number_format($summaryData['total_pengembalian'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center">
                                @php
                                    $persen = $summaryData['total_pengembalian_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_pengembalian_is_increase'] ?? false;
                                    $isNew = $summaryData['total_pengembalian_is_new'] ?? false;
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
                            <h6 class="text-muted mb-2">Total Yang Belum Dibayarkan Jenis Investasi</h6>
                            <h4 class="mb-2 fw-bold">Rp
                                {{ number_format($summaryData['total_outstanding'] ?? 0, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center">
                                @php
                                    $persen = $summaryData['total_outstanding_percentage'] ?? 0;
                                    $isIncrease = $summaryData['total_outstanding_is_increase'] ?? false;
                                    $isNew = $summaryData['total_outstanding_is_new'] ?? false;
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
                    <h5 class="card-title mb-0">Total Jenis Investasi Pokok yang Masuk Per Bulan</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanJenis InvestasiPokok" class="form-select select2"
                            data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach ($monthOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $bulanJenis InvestasiPokok == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartJenis InvestasiPokok" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total CoF Per Bulan</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanCoF" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach ($monthOptions as $value => $label)
                                <option value="{{ $value }}" {{ $bulanCoF == $value ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartCoF" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pengembalian Pokok dan Bunga</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanPengembalian" class="form-select select2"
                            data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach ($monthOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $bulanPengembalian == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartPengembalian" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Sisa Jenis Investasi yang Belum Dikembalikan</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanSisaJenis Investasi" class="form-select select2"
                            data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @foreach ($monthOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $bulanSisaJenis Investasi == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartSisaJenis Investasi" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="chart-data-holder" class="d-none" data-jenis investasi='@json($chartData['jenis investasi_pokok'] ?? [])'
        data-cof='@json($chartData['cof'] ?? [])' data-pengembalian='@json($chartData['pengembalian'] ?? [])'
        data-sisa='@json($chartData['sisa_jenis investasi'] ?? [])'>
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

        #filterBulanJenis InvestasiPokok+.select2-container,
        #filterBulanCoF+.select2-container,
        #filterBulanPengembalian+.select2-container,
        #filterBulanSisaJenis Investasi+.select2-container {
            width: 150px !important;
            min-width: 150px !important;
            max-width: 150px !important;
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
                jenis investasiPokok: null,
                cof: null,
                pengembalian: null,
                sisaJenis Investasi: null
            };

            const chartColors = ['#71dd37', '#ffab00'];

            function getBarOptions(series, categories, colors = ['#71dd37']) {
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
                        jenis investasiPokok: JSON.parse(holder.getAttribute('data-jenis investasi') || '{}'),
                        cof: JSON.parse(holder.getAttribute('data-cof') || '{}'),
                        pengembalian: JSON.parse(holder.getAttribute('data-pengembalian') || '{}'),
                        sisaJenis Investasi: JSON.parse(holder.getAttribute('data-sisa') || '{}')
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

            function createChart(elementId, series, categories, colors = ['#71dd37']) {
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

                const jenis investasiSeries = data.jenis investasiPokok.series || [];
                const jenis investasiCategories = data.jenis investasiPokok.categories || [];

                if (charts.jenis investasiPokok) {
                    updateChart(charts.jenis investasiPokok, jenis investasiSeries, jenis investasiCategories);
                } else {
                    charts.jenis investasiPokok = createChart('chartJenis InvestasiPokok', jenis investasiSeries, jenis investasiCategories);
                }

                const cofSeries = data.cof.series || [];
                const cofCategories = data.cof.categories || [];

                if (charts.cof) {
                    updateChart(charts.cof, cofSeries, cofCategories);
                } else {
                    charts.cof = createChart('chartCoF', cofSeries, cofCategories);
                }

                const pengembalianSeries = data.pengembalian.series || [];
                const pengembalianCategories = data.pengembalian.categories || [];

                if (charts.pengembalian) {
                    updateChart(charts.pengembalian, pengembalianSeries, pengembalianCategories);
                } else {
                    charts.pengembalian = createChart('chartPengembalian', pengembalianSeries, pengembalianCategories,
                        chartColors);
                }

                const sisaSeries = data.sisaJenis Investasi.series || [];
                const sisaCategories = data.sisaJenis Investasi.categories || [];

                if (charts.sisaJenis Investasi) {
                    updateChart(charts.sisaJenis Investasi, sisaSeries, sisaCategories);
                } else {
                    charts.sisaJenis Investasi = createChart('chartSisaJenis Investasi', sisaSeries, sisaCategories, chartColors);
                }
            }

            function initSelect2() {
                const filterConfigs = [{
                        id: 'filterBulanJenis InvestasiPokok',
                        property: 'bulanJenis InvestasiPokok',
                        width: 150
                    },
                    {
                        id: 'filterBulanCoF',
                        property: 'bulanCoF',
                        width: 150
                    },
                    {
                        id: 'filterBulanPengembalian',
                        property: 'bulanPengembalian',
                        width: 150
                    },
                    {
                        id: 'filterBulanSisaJenis Investasi',
                        property: 'bulanSisaJenis Investasi',
                        width: 150
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
                    attributeFilter: ['data-jenis investasi', 'data-cof', 'data-pengembalian', 'data-sisa']
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
