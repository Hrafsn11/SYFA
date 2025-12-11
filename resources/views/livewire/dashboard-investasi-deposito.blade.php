<div>
    {{-- Page Header --}}
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold py-3 mb-4">Dashboard Investasi Deposito SFinance</h4>
        </div>
    </div>

    {{-- Summary Cards Row 1 (TIDAK BERUBAH) --}}
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
                            <small class="text-muted">Compared to {{ $summaryData['total_deposito_pokok_period'] }}</small>
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
                            <small class="text-muted">Compared to {{ $summaryData['total_cof_period'] }}</small>
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
                            <small class="text-muted">Compared to {{ $summaryData['total_pengembalian_period'] }}</small>
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
                            <small class="text-muted">Compared to {{ $summaryData['total_outstanding_period'] }}</small>
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
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
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
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
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
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
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
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $selectedMonth == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
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
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

<style>
    /* Fix Select2 dropdown width */
    .select2-container {
        width: 100% !important;
    }
    
    /* Mengatur lebar Select2 pada filter bulan chart (150px) */
    #filterBulanDepositoPokok + .select2-container,
    #filterBulanCoF + .select2-container,
    #filterBulanPengembalian + .select2-container,
    #filterBulanSisaDeposito + .select2-container {
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
    }
    
    /* Override warna tema Select2 agar konsisten dengan tema Sfinance/Bootstrap biru */
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
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    // Inisialisasi variabel global di awal untuk chart instances
    window.chartDepositoPokok = null;
    window.chartCoF = null;
    window.chartPengembalian = null;
    window.chartSisaDeposito = null;

    // Store chart options globally for re-rendering
    // PASTIKAN data ini diisi di Livewire/PHP backend Anda agar tidak kosong
    window.chartOptions = {
        depositoPokok: @json($chartDepositoPokok),
        coF: @json($chartCoF),
        pengembalian: @json($chartPengembalian),
        sisaDeposito: @json($chartSisaDeposito)
    };

    function initSelect2() {
        const selectIds = [
            'filterBulanDepositoPokok', 'filterBulanCoF', 'filterBulanPengembalian', 'filterBulanSisaDeposito'
        ];
        
        // Hancurkan instance yang sudah ada
        selectIds.forEach(function(id) {
            const $select = $('#' + id);
            if ($select.length && $select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
        });

        // Inisialisasi Select2
        selectIds.forEach(function(id) {
            const $select = $('#' + id);
            if (!$select.length) return;
            
            const width = 150; 
            
            $select.select2({
                placeholder: $select.attr('data-placeholder') || 'Pilih...',
                minimumResultsForSearch: Infinity,
                width: 'resolve',
                allowClear: true,
                dropdownAutoWidth: false
            });
            
            // Force set width after initialization
            setTimeout(function() {
                $select.next('.select2-container').css({
                    'width': width + 'px',
                    'min-width': width + 'px',
                    'max-width': width + 'px'
                });
            }, 10);
            
            // Handle change events and dispatch to Livewire
            $select.off('change.livewire'); // Hapus event listener lama
            $select.on('change.livewire', function() {
                const bulan = $(this).val();
                const componentId = $(this).closest('[wire\\:id]').attr('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    // Mengirim nilai ke properti $selectedMonth
                    Livewire.find(componentId).set('selectedMonth', bulan || null);
                }
            });
        });
    }

    function initCharts() {
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts is not loaded');
            return;
        }

        // Penghancuran (Destroy) Chart yang Aman
        if (window.chartDepositoPokok) window.chartDepositoPokok.destroy();
        if (window.chartCoF) window.chartCoF.destroy();
        if (window.chartPengembalian) window.chartPengembalian.destroy();
        if (window.chartSisaDeposito) window.chartSisaDeposito.destroy();
        
        // Fungsi pembantu untuk format Rupiah
        const formatterRupiah = function(val) {
            if (val === 0) return 'Rp. 0';
            const numVal = parseFloat(val);
            if (isNaN(numVal)) return '';

            var formatted = numVal.toLocaleString('id-ID', { maximumFractionDigits: 0 });
            return 'Rp. ' + formatted;
        };
        
        // Opsi Dasar untuk Bar Chart
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
                labels: { style: { fontSize: '12px', colors: '#697a8d' } },
                axisBorder: { show: true, color: '#e0e0e0' },
                axisTicks: { show: true, color: '#e0e0e0' }
            },
            yaxis: {
                labels: {
                    formatter: formatterRupiah,
                    style: { fontSize: '12px', colors: '#697a8d' }
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
        
        // Mendapatkan data chart dari Blade/PHP
        const chartData = {
            depositoPokok: @json($chartDepositoPokok ?? ['series' => [], 'categories' => []]),
            coF: @json($chartCoF ?? ['series' => [], 'categories' => []]),
            pengembalian: @json($chartPengembalian ?? ['series' => [], 'categories' => []]),
            sisaDeposito: @json($chartSisaDeposito ?? ['series' => [], 'categories' => []])
        };

        // Chart 1: Total Deposito Pokok yang masuk Per Bulan
        if (document.querySelector("#chartDepositoPokok")) {
            const options = baseBarOptions(chartData.depositoPokok);
            window.chartDepositoPokok = new ApexCharts(document.querySelector("#chartDepositoPokok"), options);
            window.chartDepositoPokok.render();
        }

        // Chart 2: Total CoF per bulan
        if (document.querySelector("#chartCoF")) {
            const options = baseBarOptions(chartData.coF);
            window.chartCoF = new ApexCharts(document.querySelector("#chartCoF"), options);
            window.chartCoF.render();
        }

        // Chart 3: Total Pengembalian Pokok dan Bagi Hasil Perbulan 
        if (document.querySelector("#chartPengembalian")) {
            const options = baseBarOptions(chartData.pengembalian, ['#71dd37', '#ffab00']);
            window.chartPengembalian = new ApexCharts(document.querySelector("#chartPengembalian"), options);
            window.chartPengembalian.render();
        }

        // Chart 4: Total Sisa Deposito Pokok dan CoF yang Belum Dikembalikan
        if (document.querySelector("#chartSisaDeposito")) {
            const options = baseBarOptions(chartData.sisaDeposito, ['#71dd37', '#ffab00']);
            window.chartSisaDeposito = new ApexCharts(document.querySelector("#chartSisaDeposito"), options);
            window.chartSisaDeposito.render();
        }
    }

    // Function to resize all charts
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
    
    // Setup ResizeObserver for chart containers
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

    // Initialize Select2 dan Charts pada DOM Ready
    $(document).ready(function() {
        setTimeout(function() {
            initSelect2(); 
            initCharts();
            setupResizeObservers();
        }, 500);
        
        // Listeners untuk perubahan layout (sidebar toggle)
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

    // Re-render saat Livewire selesai memuat atau memperbarui (Livewire 3+)
    document.addEventListener('livewire:navigated', function() {
        setTimeout(function() {
            initSelect2(); 
            initCharts();
            setupResizeObservers();
        }, 300);
    });

    // Re-render saat Livewire component terupdate (penting untuk Select2 dan Chart)
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('morph.updated', ({ el, component }) => {
            setTimeout(() => {
                // Re-initialize Select2
                initSelect2(); 
                
                // Set value Select2 setelah re-init
                const $component = $(el);
                $component.find('#filterBulanDepositoPokok, #filterBulanCoF, #filterBulanPengembalian, #filterBulanSisaDeposito').each(function() {
                    const $select = $(this);
                    // Mengambil nilai dari properti $selectedMonth (yang kini mungkin null atau '')
                    const value = component.get('selectedMonth') || ''; 
                    
                    if ($select.val() !== value) {
                        $select.val(value).trigger('change.select2');
                    }
                });

                // Re-initialize charts dengan data baru
                initCharts(); 
            }, 100);
        });
    }

</script>
@endpush