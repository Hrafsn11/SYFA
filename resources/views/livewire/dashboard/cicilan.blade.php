<div>
    {{-- =============================================
         HEADER
         ============================================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Dashboard Cicilan Restrukturisasi</h4>
        </div>
    </div>

    {{-- =============================================
         ROW 1 – KPI CARDS
         ============================================= --}}
    <div class="row g-4 mb-4">

        {{-- Card 1: Total Pengajuan --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Pengajuan Cicilan</h6>
                            <h4 class="mb-2 fw-bold">{{ number_format($summaryData['total_pengajuan'] ?? 0) }}</h4>
                            <small class="text-muted">Semua data restrukturisasi</small>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-primary rounded" style="width: 48px; height: 48px;">
                                <i class="ti ti-file-text text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Dalam Proses --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Pengajuan Dalam Proses</h6>
                            <h4 class="mb-2 fw-bold">{{ number_format($summaryData['dalam_proses'] ?? 0) }}</h4>
                            <small class="text-muted">Belum selesai / ditolak</small>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-warning rounded" style="width: 48px; height: 48px;">
                                <i class="ti ti-loader text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Cicilan Restrukturisasi --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Cicilan Restrukturisasi</h6>
                            <h4 class="mb-2 fw-bold">Rp
                                {{ number_format($summaryData['total_cicilan_keseluruhan'] ?? 0, 0, ',', '.') }}</h4>
                            <small class="text-muted">Dari semua penyesuaian cicilan</small>
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

        {{-- Card 4: Persentase Terbayar --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Realisasi Pembayaran</h6>
                            <h4 class="mb-2 fw-bold">{{ number_format($summaryData['persen_terbayar'] ?? 0, 1) }}%
                            </h4>
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-info"
                                    style="width: {{ min(100, $summaryData['persen_terbayar'] ?? 0) }}%"></div>
                            </div>
                            <small class="text-muted">
                                Rp {{ number_format($summaryData['total_terbayar'] ?? 0, 0, ',', '.') }}
                                /
                                Rp {{ number_format($summaryData['total_cicilan_keseluruhan'] ?? 0, 0, ',', '.') }}
                            </small>
                        </div>
                        <div class="avatar flex-shrink-0 ms-3">
                            <div class="avatar-initial bg-info rounded" style="width: 48px; height: 48px;">
                                <i class="ti ti-chart-pie text-white" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =============================================
         ROW 2 – CHART TREN PENGAJUAN + REALISASI PEMBAYARAN
         ============================================= --}}
    <div class="row g-4 mb-4">

        {{-- Chart Tren Pengajuan --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tren Pengajuan Cicilan</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanTahunTren" class="form-select select2" data-placeholder="Pilih Bulan">
                            @foreach ($monthYearOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $bulanTahunTren == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartTrenPengajuan" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        {{-- Chart 2: Distribusi Jenis Restrukturisasi --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Distribusi Jenis Restrukturisasi</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartJenisRestrukturisasi" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- =============================================
         ROW 3 – CHART POKOK & MARGIN + ANGSURAN PER DEBITUR
         ============================================= --}}
    <div class="row g-4 mb-4">

        {{-- Chart 3: Pokok & Margin per Bulan --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Pokok & Margin Penyesuaian</h5>
                    <div wire:ignore style="width: 150px;">
                        <select id="filterBulanPokokMargin" class="form-select select2" data-placeholder="Pilih Bulan">
                            @foreach ($monthYearOptions as $value => $label)
                                <option value="{{ $value }}"
                                    {{ $bulanTahunPokokMargin == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartPokokMargin" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        {{-- Chart 4: Angsuran per Debitur --}}
        <div class="col-12 col-xl-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Angsuran Lunas vs Belum Lunas per Debitur</h5>
                </div>
                <div class="card-body">
                    <div wire:ignore id="chartAngsuranDebitur" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- =============================================
         ROW 4 – TABEL MONITORING DEBITUR
         ============================================= --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Monitoring Debitur Restrukturisasi</h5>
                    <span class="badge bg-secondary rounded-pill">{{ count($debiturMonitoringData) }} debitur</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Perusahaan</th>
                                    <th>No. Kontrak</th>
                                    <th class="text-end">Total Cicilan</th>
                                    <th class="text-end">Terbayar</th>
                                    <th class="text-end">Sisa</th>
                                    <th class="text-center" style="min-width: 120px;">Progress</th>
                                    <th class="text-center">Angsuran</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($debiturMonitoringData as $row)
                                    <tr>
                                        <td class="fw-medium">{{ $row['nama_perusahaan'] }}</td>
                                        <td><small>{{ $row['nomor_kontrak'] }}</small></td>
                                        <td class="text-end">Rp {{ number_format($row['total_cicilan'], 0, ',', '.') }}</td>
                                        <td class="text-end text-success">Rp {{ number_format($row['total_terbayar'], 0, ',', '.') }}</td>
                                        <td class="text-end text-warning">Rp {{ number_format($row['total_sisa'], 0, ',', '.') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ min(100, $row['persen_terbayar']) }}%"></div>
                                                </div>
                                                <small style="white-space: nowrap;">{{ $row['persen_terbayar'] }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <small>{{ $row['lunas_count'] }}/{{ $row['total_angsuran'] }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $row['health_color'] }}">{{ $row['health'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-3">
                                            <i class="ti ti-inbox me-1"></i> Belum ada data penyesuaian cicilan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =============================================
         HIDDEN DATA HOLDER – dipakai JavaScript
         ============================================= --}}
    <div id="chart-data-holder" class="d-none"
        data-tren='@json($chartData['tren_pengajuan'] ?? [])'
        data-jenis='@json($chartData['jenis_restrukturisasi'] ?? [])'
        data-pokok-margin='@json($chartData['pokok_margin'] ?? [])'
        data-debitur='@json($chartData['angsuran_per_debitur'] ?? [])'>
    </div>
</div>

@push('vendor-scripts')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('styles')
    <style>
        .select2-container { width: 100% !important; }
        #filterBulanTahunTren + .select2-container,
        #filterBulanPokokMargin + .select2-container {
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

            let charts = { tren: null, jenis: null, pokokMargin: null, debitur: null };

            const colors3 = ['#71dd37', '#ffab00', '#ff3e1d'];
            const colors2 = ['#71dd37', '#ffab00'];

            function getBarOptions(series, categories, colors) {
                return {
                    series: series,
                    chart: { type: 'bar', height: 350, toolbar: { show: false }, animations: { enabled: true, speed: 300 } },
                    plotOptions: { bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' } },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: { categories: categories, labels: { style: { fontSize: '11px' }, rotate: -30 } },
                    yaxis: { labels: { formatter: (val) => val.toLocaleString('id-ID'), style: { fontSize: '12px' } } },
                    fill: { opacity: 1 },
                    colors: colors || colors2,
                    legend: { position: 'top', horizontalAlign: 'right' },
                    tooltip: { y: { formatter: (val) => val.toLocaleString('id-ID') } },
                    noData: { text: 'Tidak ada data', style: { fontSize: '14px' } }
                };
            }

            function getRupiahBarOptions(series, categories, colors) {
                const opts = getBarOptions(series, categories, colors);
                opts.yaxis.labels.formatter = (val) => 'Rp ' + val.toLocaleString('id-ID');
                opts.tooltip.y.formatter   = (val) => 'Rp ' + val.toLocaleString('id-ID');
                return opts;
            }

            function getChartData() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return null;
                try {
                    return {
                        tren:        JSON.parse(holder.getAttribute('data-tren')         || '{}'),
                        jenis:       JSON.parse(holder.getAttribute('data-jenis')        || '{}'),
                        pokokMargin: JSON.parse(holder.getAttribute('data-pokok-margin') || '{}'),
                        debitur:     JSON.parse(holder.getAttribute('data-debitur')      || '{}')
                    };
                } catch (e) { console.error('Chart data parse error:', e); return null; }
            }

            function updateChart(instance, series, categories) {
                if (!instance) return false;
                try {
                    instance.updateOptions({ xaxis: { categories } }, false, false);
                    instance.updateSeries(series, true);
                    return true;
                } catch (e) { return false; }
            }

            function createChart(elementId, series, categories, colors, rupiah) {
                const el = document.querySelector('#' + elementId);
                if (!el) return null;
                const opts = rupiah
                    ? getRupiahBarOptions(series, categories, colors)
                    : getBarOptions(series, categories, colors);
                const chart = new ApexCharts(el, opts);
                chart.render();
                return chart;
            }

            function renderCharts() {
                if (typeof ApexCharts === 'undefined') return;
                const data = getChartData();
                if (!data) return;

                // Chart 1 – Tren Pengajuan
                const trenSeries = [
                    { name: 'Masuk',   data: data.tren.masuk   || [] },
                    { name: 'Selesai', data: data.tren.selesai || [] },
                    { name: 'Ditolak', data: data.tren.ditolak || [] }
                ];
                const trenCat = data.tren.categories || [];
                if (charts.tren) { updateChart(charts.tren, trenSeries, trenCat); }
                else { charts.tren = createChart('chartTrenPengajuan', trenSeries, trenCat, colors3, false); }

                // Chart 2 – Distribusi Jenis Restrukturisasi (single series)
                const jenisSeries = [{ name: 'Jumlah Pengajuan', data: data.jenis.data || [] }];
                const jenisCat   = data.jenis.categories || [];
                if (charts.jenis) { updateChart(charts.jenis, jenisSeries, jenisCat); }
                else { charts.jenis = createChart('chartJenisRestrukturisasi', jenisSeries, jenisCat, ['#696cff'], false); }

                // Chart 3 – Pokok & Margin per tahun (rupiah)
                const pokokMarginSeries = [
                    { name: 'Pokok',  data: data.pokokMargin.pokok  || [] },
                    { name: 'Margin', data: data.pokokMargin.margin || [] }
                ];
                const pokokMarginCat = data.pokokMargin.categories || [];
                if (charts.pokokMargin) { updateChart(charts.pokokMargin, pokokMarginSeries, pokokMarginCat); }
                else { charts.pokokMargin = createChart('chartPokokMargin', pokokMarginSeries, pokokMarginCat, colors2, true); }

                // Chart 4 – Angsuran per Debitur
                const debiturSeries = [
                    { name: 'Lunas',       data: data.debitur.lunas      || [] },
                    { name: 'Belum Lunas', data: data.debitur.belumLunas || [] }
                ];
                const debiturCat = data.debitur.categories || [];
                if (charts.debitur) { updateChart(charts.debitur, debiturSeries, debiturCat); }
                else { charts.debitur = createChart('chartAngsuranDebitur', debiturSeries, debiturCat, colors2, false); }
            }

            function initSelect2() {
                const filters = [
                    { id: 'filterBulanTahunTren',   prop: 'bulanTahunTren' },
                    { id: 'filterBulanPokokMargin', prop: 'bulanTahunPokokMargin' },
                ];
                filters.forEach(function ({ id, prop }) {
                    const $sel = $('#' + id);
                    if (!$sel.length) return;
                    if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
                    $sel.select2({ minimumResultsForSearch: Infinity, width: 'resolve', dropdownAutoWidth: false });
                    setTimeout(() => $sel.next('.select2-container').css({ width: '150px', 'min-width': '150px', 'max-width': '150px' }), 10);
                    $sel.off('change.dashboard').on('change.dashboard', function () {
                        const val = $(this).val();
                        const wid = $(this).closest('[wire\\:id]').attr('wire:id');
                        if (wid && typeof Livewire !== 'undefined') Livewire.find(wid).set(prop, val || null);
                    });
                });
            }

            function setupDataObserver() {
                const holder = document.getElementById('chart-data-holder');
                if (!holder) return;
                const observer = new MutationObserver(() => renderCharts());
                observer.observe(holder, {
                    attributes: true,
                    attributeFilter: ['data-tren', 'data-jenis', 'data-pokok-margin', 'data-debitur']
                });
            }

            // ----------------------------------------
            // Init
            // ----------------------------------------
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
                Livewire.hook('morph.updated', function({ el, component }) {
                    if (el.id === 'chart-data-holder' || el.querySelector('#chart-data-holder')) {
                        setTimeout(renderCharts, 100);
                    }
                });
            });
        })();
    </script>
@endpush
