<div>
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold py-3 mb-4">Dashboard Investasi Deposito SFinance</h4>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 text-muted">Total Deposito Pokok Masuk Bulan Ini</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_deposito_pokok'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-arrow-up text-success me-1"></i>
                                <span class="text-success fw-semibold">{{ $summaryData['total_deposito_pokok_percent'] }}% dari bulan lalu</span>
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 text-muted">Total CoF (Cost of Fund) Bulan Ini</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_cof'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-arrow-up text-success me-1"></i>
                                <span class="text-success fw-semibold">{{ $summaryData['total_cof_percent'] }}% lebih lancar</span>
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 text-muted">Total Pengembalian Bulan Ini</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_pengembalian'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-arrow-up text-success me-1"></i>
                                <span class="text-success fw-semibold">{{ $summaryData['total_pengembalian_percent'] }}%</span>
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1 text-muted">Total Outstanding Deposito</h6>
                            <h4 class="mb-2 fw-bold">Rp {{ number_format($summaryData['total_outstanding'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ti ti-arrow-down text-warning me-1"></i>
                                <span class="text-warning fw-semibold">{{ $summaryData['total_outstanding_percent'] }}% dari bulan lalu</span>
                            </div>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ti ti-currency-dollar text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 (Filter) --}}
    <div class="row mb-4 g-3">
        <div class="col-lg-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Deposito Pokok yang masuk Per Bulan</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        @php
                            $bulanNama = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        <select id="filterBulanDepositoPokok" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ ($selectedMonthDepositoPokok ?? $selectedMonth) == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $bulanNama[$month] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div id="chartDepositoPokok" wire:ignore style="min-height: 350px; width: 100%; position: relative;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total CoF per bulan</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanCoF" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ ($selectedMonthCoF ?? $selectedMonth) == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $bulanNama[$month] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div id="chartCoF" wire:ignore style="min-height: 350px; width: 100%; position: relative;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 3 (Filter) --}}
    <div class="row mb-4 g-3">
        <div class="col-lg-6 col-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pengembalian Pokok dan Bagi Hasil Perbulan</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanPengembalian" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ ($selectedMonthPengembalian ?? $selectedMonth) == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $bulanNama[$month] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div id="chartPengembalian" wire:ignore style="min-height: 350px; width: 100%; position: relative;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Sisa Deposito Pokok dan CoF yang Belum Dikembalikan</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanSisaDeposito" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ ($selectedMonthSisaDeposito ?? $selectedMonth) == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $bulanNama[$month] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body p-3" style="position: relative;">
                    <div id="chartSisaDeposito" wire:ignore style="min-height: 350px; width: 100%; position: relative;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden element untuk membawa data chart terbaru ke JS (agar bisa diparse ulang setelah Livewire morph) --}}
    <div id="chart-data-json"
         data-deposito='@json($chartDepositoPokok ?? ["series" => [], "categories" => []])'
         data-cof='@json($chartCoF ?? ["series" => [], "categories" => []])'
         data-pengembalian='@json($chartPengembalian ?? ["series" => [], "categories" => []])'
         data-sisa='@json($chartSisaDeposito ?? ["series" => [], "categories" => []])'
         class="d-none"></div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

<style>
    .select2-container {
        width: 100% !important;
    }
    
    #filterBulanDepositoPokok + .select2-container,
    #filterBulanCoF + .select2-container,
    #filterBulanPengembalian + .select2-container,
    #filterBulanSisaDeposito + .select2-container {
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
    
    .select2-container--default.select2-container--focus .select2-selection:focus,
    .select2-container--default.select2-container--open .select2-selection:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }

    .row.mb-4 > [class*="col-"] > .card {
        min-height: 160px; 
        height: 100%;
    }
    
    .row.mb-4 > [class*="col-"] .card-body {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between; 
        padding: 1.25rem !important; 
    }

    .row.mb-4 > [class*="col-"] .card-title {
        white-space: normal; 
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical;
        line-height: 1.4; 
        min-height: 40px; 
        margin-bottom: 0.5rem !important; 
        font-size: 0.9em; 
    }
    
    .row.mb-4 > [class*="col-"] h4.mb-2 {
        white-space: nowrap; 
        overflow: hidden;
        text-overflow: ellipsis; 
        line-height: 1.3; 
        min-height: 35px; 
        margin-bottom: 0.5rem !important; 
        font-size: 1.4rem; 
    }
    
    .row.mb-4 > [class*="col-"] .d-flex.align-items-center {
        margin-top: 0.25rem; 
        margin-bottom: 0 !important; 
        min-height: 25px; 
    }

    .row.mb-4.g-3 .card-header .card-title {
        font-size: 1.25rem !important; 
        font-weight: bold; 
    }

    .row.mb-4 .flex-grow-1 {
        min-width: 0; 
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    window.chartDepositoPokok = null;
    window.chartCoF = null;
    window.chartPengembalian = null;
    window.chartSisaDeposito = null;

    window.chartOptions = {
        depositoPokok: @json($chartDepositoPokok),
        coF: @json($chartCoF),
        pengembalian: @json($chartPengembalian),
        sisaDeposito: @json($chartSisaDeposito)
    };

    function refreshChartOptionsFromDom() {
        const holder = document.getElementById('chart-data-json');
        if (!holder) return;
        const parseJson = (attr) => {
            try { return JSON.parse(holder.dataset[attr] || '{"series":[],"categories":[]}'); }
            catch (e) { return { series: [], categories: [] }; }
        };
        window.chartOptions = {
            depositoPokok: parseJson('deposito'),
            coF: parseJson('cof'),
            pengembalian: parseJson('pengembalian'),
            sisaDeposito: parseJson('sisa')
        };
    }

    function initSelect2() {
        const filterMapping = {
            'filterBulanDepositoPokok': 'selectedMonthDepositoPokok',
            'filterBulanCoF': 'selectedMonthCoF',
            'filterBulanPengembalian': 'selectedMonthPengembalian',
            'filterBulanSisaDeposito': 'selectedMonthSisaDeposito'
        };
        
        const selectIds = Object.keys(filterMapping);
        
        selectIds.forEach(function(id) {
            const $select = $('#' + id);
            if ($select.length && $select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
        });

        selectIds.forEach(function(id) {
            const $select = $('#' + id);
            if (!$select.length) return;
            
            const width = 150;
            const propertyName = filterMapping[id];
            
            $select.select2({
                placeholder: $select.attr('data-placeholder') || 'Pilih...',
                minimumResultsForSearch: Infinity,
                width: 'resolve',
                allowClear: true,
                dropdownAutoWidth: false
            });
            
            setTimeout(function() {
                $select.next('.select2-container').css({
                    'width': width + 'px',
                    'min-width': width + 'px',
                    'max-width': width + 'px'
                });
            }, 10);
            
            $select.off('change.livewire');
            $select.on('change.livewire', function() {
                const bulan = $(this).val();
                const componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    Livewire.find(componentId).set(propertyName, bulan || null);
                }
            });
        });
    }

    function initCharts() {
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts is not loaded');
            return;
        }

        refreshChartOptionsFromDom();

        if (window.chartDepositoPokok) window.chartDepositoPokok.destroy();
        if (window.chartCoF) window.chartCoF.destroy();
        if (window.chartPengembalian) window.chartPengembalian.destroy();
        if (window.chartSisaDeposito) window.chartSisaDeposito.destroy();
        
        const formatterRupiah = function(val) {
            if (val === 0) return 'Rp. 0';
            const numVal = parseFloat(val);
            if (isNaN(numVal)) return '';

            var formatted = numVal.toLocaleString('id-ID', { maximumFractionDigits: 0 });
            return 'Rp. ' + formatted;
        };
        
        const baseBarOptions = (data, colors = ['#71dd37']) => ({
            series: data.series,
            chart: {
                type: 'bar',
                height: 350,
                width: '100%',
                toolbar: { show: false },
                zoom: { enabled: false },
                parentHeightOffset: 0
            },
            plotOptions: {
                bar: { 
                    horizontal: false, 
                    columnWidth: '55%', 
                    endingShape: 'rounded',
                    borderRadius: 4
                }
            },
            dataLabels: { enabled: false },
            stroke: { 
                show: true, 
                width: 2, 
                colors: ['transparent'] 
            },
            grid: {
                show: true,
                borderColor: '#e0e0e0',
                xaxis: { lines: { show: false } },
                yaxis: { lines: { show: true, offsetX: 0 } },
                padding: { top: 10, right: 10, bottom: 0, left: 10 }
            },
            xaxis: {
                categories: data.categories,
                labels: { style: { fontSize: '14px', colors: '#697a8d' } }, 
                axisBorder: { show: true, color: '#e0e0e0' },
                axisTicks: { show: true, color: '#e0e0e0' }
            },
            yaxis: {
                labels: {
                    formatter: formatterRupiah,
                    style: { fontSize: '14px', colors: '#697a8d' } 
                },
                min: 0,
                max: 200000000, 
                tickAmount: 4,
                forceNiceScale: false,
                axisBorder: { show: true, color: '#e0e0e0' },
                axisTicks: { show: true, color: '#e0e0e0' }
            },
            fill: { opacity: 1 },
            colors: colors,
            legend: { 
                show: data.series.length > 1, 
                position: 'top', 
                horizontalAlign: 'right', 
                markers: { width: 12, height: 12, radius: 12 } 
            },
            tooltip: {
                y: { formatter: formatterRupiah }
            }
        });
        
        const chartData = {
            depositoPokok: window.chartOptions.depositoPokok ?? { series: [], categories: [] },
            coF: window.chartOptions.coF ?? { series: [], categories: [] },
            pengembalian: window.chartOptions.pengembalian ?? { series: [], categories: [] },
            sisaDeposito: window.chartOptions.sisaDeposito ?? { series: [], categories: [] }
        };

        if (document.querySelector("#chartDepositoPokok")) {
            const options = baseBarOptions(chartData.depositoPokok);
            window.chartDepositoPokok = new ApexCharts(document.querySelector("#chartDepositoPokok"), options);
            window.chartDepositoPokok.render();
        }

        if (document.querySelector("#chartCoF")) {
            const options = baseBarOptions(chartData.coF);
            window.chartCoF = new ApexCharts(document.querySelector("#chartCoF"), options);
            window.chartCoF.render();
        }

        if (document.querySelector("#chartPengembalian")) {
            const options = baseBarOptions(chartData.pengembalian, ['#71dd37', '#ffab00']);
            window.chartPengembalian = new ApexCharts(document.querySelector("#chartPengembalian"), options);
            window.chartPengembalian.render();
        }

        if (document.querySelector("#chartSisaDeposito")) {
            const options = baseBarOptions(chartData.sisaDeposito, ['#71dd37', '#ffab00']);
            window.chartSisaDeposito = new ApexCharts(document.querySelector("#chartSisaDeposito"), options);
            window.chartSisaDeposito.render();
        }
    }

    function resizeCharts() {
        const charts = [window.chartDepositoPokok, window.chartCoF, window.chartPengembalian, window.chartSisaDeposito];
        setTimeout(function() {
            charts.forEach(chart => {
                if (chart && typeof chart.resize === 'function') {
                    chart.resize();
                }
            });
        }, 100);
    }
    
    function setupResizeObservers() {
        const chartContainers = [
            document.querySelector("#chartDepositoPokok"),
            document.querySelector("#chartCoF"),
            document.querySelector("#chartPengembalian"),
            document.querySelector("#chartSisaDeposito")
        ];
        
        chartContainers.forEach(function(container) {
            if (container && window.ResizeObserver) {
                const resizeObserver = new ResizeObserver(function(entries) {
                    resizeCharts();
                });
                resizeObserver.observe(container);
            }
        });
    }

    $(document).ready(function() {
        setTimeout(function() {
            refreshChartOptionsFromDom();
            initSelect2(); 
            initCharts();
            setupResizeObservers();
        }, 500);
        
        const menuToggle = document.querySelector('.layout-menu-toggle');
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                setTimeout(resizeCharts, 200);
            });
        }
        document.addEventListener('menu:collapsed', function() { setTimeout(resizeCharts, 200); });
        document.addEventListener('menu:expanded', function() { setTimeout(resizeCharts, 200); });
        
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(resizeCharts, 250);
        });
    });

    document.addEventListener('livewire:navigated', function() {
        setTimeout(function() {
            refreshChartOptionsFromDom();
            initSelect2(); 
            initCharts();
            setupResizeObservers();
        }, 300);
    });

    if (typeof Livewire !== 'undefined') {
        Livewire.hook('morph.updated', ({ el, component }) => {
            setTimeout(() => {
                initSelect2(); 
                
                const $component = $(el);
                const filterMapping = {
                    'filterBulanDepositoPokok': 'selectedMonthDepositoPokok',
                    'filterBulanCoF': 'selectedMonthCoF',
                    'filterBulanPengembalian': 'selectedMonthPengembalian',
                    'filterBulanSisaDeposito': 'selectedMonthSisaDeposito'
                };
                
                Object.keys(filterMapping).forEach(function(filterId) {
                    const $select = $component.find('#' + filterId);
                    if ($select.length) {
                        const propertyName = filterMapping[filterId];
                        const value = component.get(propertyName) || '';
                        
                        if ($select.val() !== value) {
                            $select.val(value).trigger('change.select2');
                        }
                    }
                });

                refreshChartOptionsFromDom();
                initCharts(); 
            }, 100);
        });
    }

</script>
@endpush