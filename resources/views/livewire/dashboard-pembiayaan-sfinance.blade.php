<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Pembiayaan Sfinance</h4>
        </div>
    </div>

    {{-- Summary Cards Row 1 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Disbursement Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($summaryData['total_disbursement'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                @if(!empty($summaryData['total_disbursement_is_new']))
                                    <i class="ti ti-info-circle text-muted me-1"></i>
                                    <small class="text-muted">Baru</small>
                                @else
                                    <i class="ti ti-arrow-{{ $summaryData['total_disbursement_is_increase'] ? 'up text-success' : 'down text-danger' }} me-1"></i>
                                    <small class="text-muted">{{ number_format($summaryData['total_disbursement_percentage'] ?? 0, 1) }}% {{ $summaryData['total_disbursement_is_increase'] ? 'naik' : 'turun' }} dari bulan lalu</small>
                                @endif
                            </div>
                        </div>
                        <div class="bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                            <i class="ti ti-currency-dollar text-white fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Pembayaran Masuk Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($summaryDataPembayaran['total_pembayaran_masuk'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                @if(!empty($summaryDataPembayaran['total_pembayaran_masuk_is_new']))
                                    <i class="ti ti-info-circle text-muted me-1"></i>
                                    <small class="text-muted">Baru</small>
                                @else
                                    <i class="ti ti-arrow-{{ $summaryDataPembayaran['total_pembayaran_masuk_is_increase'] ? 'up text-success' : 'down text-danger' }} me-1"></i>
                                    <small class="text-muted">{{ number_format($summaryDataPembayaran['total_pembayaran_masuk_percentage'] ?? 0, 1) }}% {{ $summaryDataPembayaran['total_pembayaran_masuk_is_increase'] ? 'naik' : 'turun' }} dari bulan lalu</small>
                                @endif
                            </div>
                        </div>
                        <div class="bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                            <i class="ti ti-currency-dollar text-white fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Sisa yang Belum Terbayar Bulan Ini</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($summaryDataSisa['total_sisa_belum_terbayar'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                @if(!empty($summaryDataSisa['total_sisa_belum_terbayar_is_new']))
                                    <i class="ti ti-info-circle text-muted me-1"></i>
                                    <small class="text-muted">Baru</small>
                                @else
                                    <i class="ti ti-arrow-{{ $summaryDataSisa['total_sisa_belum_terbayar_is_increase'] ? 'up text-danger' : 'down text-success' }} me-1"></i>
                                    <small class="text-muted">{{ number_format($summaryDataSisa['total_sisa_belum_terbayar_percentage'] ?? 0, 1) }}% {{ $summaryDataSisa['total_sisa_belum_terbayar_is_increase'] ? 'naik' : 'turun' }} dari bulan lalu</small>
                                @endif
                            </div>
                        </div>
                        <div class="bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                            <i class="ti ti-currency-dollar text-white fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2">Total Outstanding Piutang</h6>
                            <h4 class="mb-0 fw-bold">Rp {{ number_format($summaryDataOutstanding['total_outstanding_piutang'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center mt-2">
                                @if(!empty($summaryDataOutstanding['total_outstanding_piutang_is_new']))
                                    <i class="ti ti-info-circle text-muted me-1"></i>
                                    <small class="text-muted">Baru</small>
                                @else
                                    <i class="ti ti-arrow-{{ $summaryDataOutstanding['total_outstanding_piutang_is_increase'] ? 'up text-danger' : 'down text-success' }} me-1"></i>
                                    <small class="text-muted">{{ number_format($summaryDataOutstanding['total_outstanding_piutang_percentage'] ?? 0, 1) }}% {{ $summaryDataOutstanding['total_outstanding_piutang_is_increase'] ? 'naik' : 'turun' }} dari bulan lalu</small>
                                @endif
                            </div>
                        </div>
                        <div class="bg-success rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                            <i class="ti ti-currency-dollar text-white fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Disburse Pokok dan Bagi Hasil Perbulan</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanDisbursement" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @php
                                $bulanNama = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                ];
                            @endphp
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $bulanDisbursement == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $bulanNama[$month] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($hasDataDisbursement)
                        <div id="chartDisbursement" wire:key="chart-disbursement-{{ $bulanDisbursement }}-{{ $tahun }}" style="min-height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-5">
                            <p>Belum ada data untuk bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Pokok dan Bagi Hasil Perbulan</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanPembayaran" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $bulanPembayaran == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $bulanNama[$month] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($hasDataPembayaran)
                        <div id="chartPembayaran" wire:key="chart-pembayaran-{{ $bulanPembayaran }}-{{ $tahun }}" style="min-height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-5">
                            <p>Belum ada data untuk bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 3 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Sisa yang Belum Terbayar Pokok dan Bagi Hasil Perbulan</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterBulanSisa" class="form-select select2" data-placeholder="Pilih Bulan">
                            <option value=""></option>
                            @for($month = 1; $month <= 12; $month++)
                                <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $bulanSisa == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ $bulanNama[$month] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($hasDataSisa)
                        <div id="chartSisaBelumTerbayar" wire:key="chart-sisa-{{ $bulanSisa }}-{{ $tahun }}" style="min-height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-5">
                            <p>Belum ada data untuk bulan ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Total Pembayaran Piutang Per Tahun</h5>
                    <div wire:ignore style="width: 150px; flex-shrink: 0;">
                        <select id="filterTahunPiutang" class="form-select select2" data-placeholder="Pilih Tahun">
                            @for($year = date('Y'); $year >= 2020; $year--)
                                <option value="{{ $year }}" {{ $tahunPiutang == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    @if($hasDataPiutang)
                        <div id="chartPembayaranPiutang" wire:key="chart-piutang-{{ $tahunPiutang }}" style="min-height: 300px;"></div>
                    @else
                        <div class="text-center text-muted py-5">
                            <p>Belum ada data untuk tahun ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Table and Comparison Chart Row 4 --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Total AR yang Terbagi Berdasarkan Kriteria Keterlambatan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Bulan</label>
                            <div wire:ignore>
                                <select id="filterBulanTable" class="form-select select2" data-placeholder="Pilih Bulan">
                                    <option value=""></option>
                                    @for($month = 1; $month <= 12; $month++)
                                        <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $bulanTable == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                            {{ $bulanNama[$month] }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tahun</label>
                            <div wire:ignore>
                                <select id="filterTahunTable" class="form-select select2" data-placeholder="Pilih Tahun">
                                    @for($year = date('Y'); $year >= 2020; $year--)
                                        <option value="{{ $year }}" {{ $tahunTable == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
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
                                @forelse($arTableData as $row)
                                    <tr>
                                        <td>{{ $row['debitur'] }}</td>
                                        <td>Rp. {{ number_format($row['del_1_30'], 0, ',', '.') }}</td>
                                        <td>Rp. {{ number_format($row['del_31_60'], 0, ',', '.') }}</td>
                                        <td>Rp. {{ number_format($row['del_61_90'], 0, ',', '.') }}</td>
                                        <td>Rp. {{ number_format($row['npl_91_179'], 0, ',', '.') }}</td>
                                        <td>Rp. {{ number_format($row['write_off'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Perbandingan AR dan Utang Pengembalian Deposito Perbulan</h5>
                    <div class="d-flex gap-2">
                        <div wire:ignore style="width: 120px; flex-shrink: 0;">
                            <select id="filterBulanComparison1" class="form-select select2" data-placeholder="Bulan 1">
                                <option value=""></option>
                                @for($month = 1; $month <= 12; $month++)
                                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $bulan1 == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ $bulanNama[$month] }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div wire:ignore style="width: 120px; flex-shrink: 0;">
                            <select id="filterBulanComparison2" class="form-select select2" data-placeholder="Bulan 2">
                                <option value=""></option>
                                @for($month = 1; $month <= 12; $month++)
                                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ $bulan2 == str_pad($month, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                        {{ $bulanNama[$month] }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chartComparison" wire:key="chart-comparison-{{ $bulan1 }}-{{ $bulan2 }}-{{ $tahun }}" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('vendor-scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endpush

@push('styles')
<style>
    /* Fix Select2 dropdown width */
    .select2-container {
        width: 100% !important;
    }
    
    #filterBulanDisbursement + .select2-container,
    #filterBulanPembayaran + .select2-container,
    #filterBulanSisa + .select2-container,
    #filterBulanPiutang + .select2-container {
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
    }
    
    #filterBulanComparison1 + .select2-container,
    #filterBulanComparison2 + .select2-container {
        width: 120px !important;
        min-width: 120px !important;
        max-width: 120px !important;
    }
    
    /* Override purple theme color for Select2 */
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
<script>
    (function() {
        'use strict';
        
        let chartDisbursement, chartPembayaran, chartSisaBelumTerbayar, chartPembayaranPiutang, chartComparison;

        // Initialize Select2
        function initSelect2() {
            // Destroy existing instances first
            const selectIds = [
                'filterBulanDisbursement', 'filterBulanPembayaran', 'filterBulanSisa', 
                'filterBulanPiutang', 'filterBulanTable', 'filterTahunTable',
                'filterBulanComparison1', 'filterBulanComparison2'
            ];
            
            selectIds.forEach(function(id) {
                const $select = $('#' + id);
                if ($select.length && $select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
            });

            // Initialize Select2 with consistent styling
            function initSelect(id, width, placeholder) {
                const $select = $('#' + id);
                if (!$select.length) return;
                
                $select.select2({
                    placeholder: placeholder || 'Pilih...',
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
            }

            // Initialize all selects
            initSelect('filterBulanDisbursement', 150, 'Pilih Bulan');
            initSelect('filterBulanPembayaran', 150, 'Pilih Bulan');
            initSelect('filterBulanSisa', 150, 'Pilih Bulan');
            initSelect('filterBulanPiutang', 150, 'Pilih Bulan');
            initSelect('filterBulanTable', null, 'Pilih Bulan');
            initSelect('filterTahunTable', null, 'Pilih Tahun');
            initSelect('filterBulanComparison1', 120, 'Bulan 1');
            initSelect('filterBulanComparison2', 120, 'Bulan 2');

            // Handle change events with ISOLATED property mapping per filter
            // Each filter updates its own bulan property to prevent state collision
            
            $(document).off('change', '#filterBulanDisbursement');
            $(document).on('change', '#filterBulanDisbursement', function() {
                const bulan = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Disbursement filter changed to:', bulan);
                    Livewire.find(componentId).set('bulanDisbursement', bulan || null);
                }
            });

            $(document).off('change', '#filterBulanPembayaran');
            $(document).on('change', '#filterBulanPembayaran', function() {
                const bulan = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Pembayaran filter changed to:', bulan);
                    Livewire.find(componentId).set('bulanPembayaran', bulan || null);
                }
            });

            $(document).off('change', '#filterBulanSisa');
            $(document).on('change', '#filterBulanSisa', function() {
                const bulan = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Sisa filter changed to:', bulan);
                    Livewire.find(componentId).set('bulanSisa', bulan || null);
                }
            });

            $(document).off('change', '#filterBulanPiutang');
            $(document).on('change', '#filterBulanPiutang', function() {
                const bulan = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Piutang filter changed to:', bulan);
                    Livewire.find(componentId).set('bulanPiutang', bulan || null);
                }
            });

            $(document).off('change', '#filterTahunPiutang');
            $(document).on('change', '#filterTahunPiutang', function() {
                const tahun = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Tahun Piutang filter changed to:', tahun);
                    Livewire.find(componentId).set('tahunPiutang', tahun);
                }
            });

            $(document).off('change', '#filterBulanTable');
            $(document).on('change', '#filterBulanTable', function() {
                const bulan = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Table filter changed to:', bulan);
                    Livewire.find(componentId).set('bulanTable', bulan || null);
                }
            });

            $(document).off('change', '#filterTahunTable');
            $(document).on('change', '#filterTahunTable', function() {
                const tahun = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Tahun filter changed to:', tahun);
                    Livewire.find(componentId).set('tahunTable', tahun);
                }
            });

            $(document).off('change', '#filterBulanComparison1');
            $(document).on('change', '#filterBulanComparison1', function() {
                const bulan = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Comparison1 filter changed to:', bulan);
                    Livewire.find(componentId).set('bulan1', bulan || null);
                }
            });

            $(document).off('change', '#filterBulanComparison2');
            $(document).on('change', '#filterBulanComparison2', function() {
                const bulan = $(this).val();
                let componentId = null;
                const parent = $(this).closest('[wire\\:id]')[0];
                if (parent) componentId = parent.getAttribute('wire:id');
                if (componentId && typeof Livewire !== 'undefined') {
                    console.log('Comparison2 filter changed to:', bulan);
                    Livewire.find(componentId).set('bulan2', bulan || null);
                }
            });
        }

        function initCharts() {
            // Check if ApexCharts is available
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts is not loaded');
                return;
            }

            // Destroy existing charts first - always destroy to ensure clean state
            try {
                if (chartDisbursement) {
                    chartDisbursement.destroy();
                }
            } catch(e) {}
            chartDisbursement = null;
            
            try {
                if (chartPembayaran) {
                    chartPembayaran.destroy();
                }
            } catch(e) {}
            chartPembayaran = null;
            
            try {
                if (chartSisaBelumTerbayar) {
                    chartSisaBelumTerbayar.destroy();
                }
            } catch(e) {}
            chartSisaBelumTerbayar = null;
            
            try {
                if (chartPembayaranPiutang) {
                    chartPembayaranPiutang.destroy();
                }
            } catch(e) {}
            chartPembayaranPiutang = null;
            
            try {
                if (chartComparison) {
                    chartComparison.destroy();
                }
            } catch(e) {}
            chartComparison = null;

            // Chart Disbursement
            const disbursementOptions = {
                series: [
                    {
                        name: 'Pokok',
                        data: @json($chartData['disbursement']['pokok'])
                    },
                    {
                        name: 'Bagi Hasil',
                        data: @json($chartData['disbursement']['bagi_hasil'])
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
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
                    categories: @json($chartData['disbursement']['categories'] ?? [])
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#71dd37', '#ffab00'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartDisbursementEl = document.querySelector("#chartDisbursement");
            if (chartDisbursementEl) {
                chartDisbursement = new ApexCharts(chartDisbursementEl, disbursementOptions);
                chartDisbursement.render();
            }

            // Chart Pembayaran
            const pembayaranOptions = {
                series: [
                    {
                        name: 'Pokok',
                        data: @json($chartData['pembayaran']['pokok'])
                    },
                    {
                        name: 'Bagi Hasil',
                        data: @json($chartData['pembayaran']['bagi_hasil'])
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
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
                    categories: @json($chartData['pembayaran']['categories'] ?? [])
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#71dd37', '#ffab00'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartPembayaranEl = document.querySelector("#chartPembayaran");
            if (chartPembayaranEl) {
                chartPembayaran = new ApexCharts(chartPembayaranEl, pembayaranOptions);
                chartPembayaran.render();
            }

            // Chart Sisa Belum Terbayar
            const sisaData = @json($chartData['sisa_belum_terbayar'] ?? ['pokok' => [], 'bagi_hasil' => [], 'categories' => []]);
            const pokokArray = sisaData.pokok || [];
            const bagiHasilArray = sisaData.bagi_hasil || [];
            const maxSisaValue = pokokArray.length > 0 || bagiHasilArray.length > 0 
                ? Math.max(...pokokArray, ...bagiHasilArray) 
                : 0;
            
            const sisaOptions = {
                series: [
                    {
                        name: 'Pokok',
                        data: pokokArray
                    },
                    {
                        name: 'Bagi Hasil',
                        data: bagiHasilArray
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
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
                    categories: sisaData.categories || []
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#71dd37', '#ffab00'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartSisaEl = document.querySelector("#chartSisaBelumTerbayar");
            if (chartSisaEl) {
                chartSisaBelumTerbayar = new ApexCharts(chartSisaEl, sisaOptions);
                chartSisaBelumTerbayar.render();
            }

            // Chart Pembayaran Piutang
            const piutangOptions = {
                series: [
                    {
                        name: 'Pokok',
                        data: @json($chartData['pembayaran_piutang_tahun']['pokok'] ?? [])
                    },
                    {
                        name: 'Bagi Hasil',
                        data: @json($chartData['pembayaran_piutang_tahun']['bagi_hasil'] ?? [])
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
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
                    categories: @json($chartData['pembayaran_piutang_tahun']['categories'] ?? [])
                },
                yaxis: {
                    min: 0,
                    labels: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#71dd37', '#ffab00'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartPiutangEl = document.querySelector("#chartPembayaranPiutang");
            if (chartPiutangEl) {
                chartPembayaranPiutang = new ApexCharts(chartPiutangEl, piutangOptions);
                chartPembayaranPiutang.render();
            }

            // Chart Comparison
            const comparisonOptions = {
                series: [
                    {
                        name: @json($chartData['comparison']['nama_bulan1'] ?? 'Bulan 1'),
                        data: @json($chartData['comparison']['bulan1'])
                    },
                    {
                        name: @json($chartData['comparison']['nama_bulan2'] ?? 'Bulan 2'),
                        data: @json($chartData['comparison']['bulan2'])
                    },
                    {
                        name: 'Selisih',
                        data: @json($chartData['comparison']['selisih'])
                    }
                ],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false }
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
                    categories: @json($chartData['comparison']['categories'] ?? [])
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            return 'Rp. ' + val.toLocaleString('id-ID');
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                colors: ['#71dd37', '#ffab00', '#ff3e1d'],
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + val.toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chartComparisonEl = document.querySelector("#chartComparison");
            if (chartComparisonEl) {
                chartComparison = new ApexCharts(chartComparisonEl, comparisonOptions);
                chartComparison.render();
            }
        }

        // Initialize when everything is ready
        function initializeDashboard() {
            // Check if ApexCharts is loaded
            if (typeof ApexCharts === 'undefined') {
                console.warn('ApexCharts not loaded yet, retrying...');
                setTimeout(initializeDashboard, 200);
                return;
            }
            
            // Check if jQuery is loaded
            if (typeof $ === 'undefined') {
                console.warn('jQuery not loaded yet, retrying...');
                setTimeout(initializeDashboard, 200);
                return;
            }
            
            // Determine which chart elements are present on the page.
            // Some charts are intentionally omitted when there's no data for the selected month,
            // so we should NOT keep retrying waiting for all of them. Instead initialize whatever
            // chart elements are present and let `initCharts()` handle missing elements.
            const chartElements = [
                '#chartDisbursement',
                '#chartPembayaran',
                '#chartSisaBelumTerbayar',
                '#chartPembayaranPiutang',
                '#chartComparison'
            ];

            // Collect selectors that actually exist now - safely check with try-catch
            let presentChartSelectors = [];
            try {
                presentChartSelectors = chartElements.filter(function(selector) {
                    const el = document.querySelector(selector);
                    return el !== null && el !== undefined;
                });
            } catch(e) {
                console.warn('Error checking chart elements:', e);
            }

            // If no chart elements are present, still initialize Select2 (so filters work)
            // and skip the retry loop — charts will be initialized later when Livewire updates.
            if (presentChartSelectors.length === 0) {
                // Initialize Select2 so filters are interactive even when charts are absent
                try {
                    initSelect2();
                } catch(e) {
                    console.warn('Error initializing Select2:', e);
                }
                // Do not treat this as an error — return without retrying
                return;
            }
            
            // Initialize Select2 and Charts
            try {
                initSelect2();
                initCharts();
            } catch (error) {
                console.error('Error initializing dashboard:', error);
            }
        }

        // Initialize on page load
        $(document).ready(function() {
            // Wait for all scripts to load
            setTimeout(function() {
                initializeDashboard();
            }, 500);
        });

        // Also initialize when Livewire is ready
        document.addEventListener('livewire:init', function() {
            setTimeout(function() {
                initializeDashboard();
            }, 300);
        });

        // Reinitialize when Livewire updates
        document.addEventListener('livewire:init', function() {
            Livewire.hook('morph.updated', ({ el, component }) => {
                setTimeout(() => {
                    // Reinitialize Select2
                    initSelect2();
                    



                    // Debounce chart re-initialization to avoid redundant renders
                    if (window._chartReinitTimeout) {
                        clearTimeout(window._chartReinitTimeout);
                    }
                    window._chartReinitTimeout = setTimeout(() => {
                        // Reinitialize charts - destroy first
                        if (chartDisbursement) {
                            chartDisbursement.destroy();
                            chartDisbursement = null;
                        }
                        if (chartPembayaran) {
                            chartPembayaran.destroy();
                            chartPembayaran = null;
                        }
                        if (chartSisaBelumTerbayar) {
                            chartSisaBelumTerbayar.destroy();
                            chartSisaBelumTerbayar = null;
                        }
                        if (chartPembayaranPiutang) {
                            chartPembayaranPiutang.destroy();
                            chartPembayaranPiutang = null;
                        }
                        if (chartComparison) {
                            chartComparison.destroy();
                            chartComparison = null;
                        }
                        // Wait a bit then reinitialize charts
                        setTimeout(() => {
                            initCharts();
                        }, 200);
                    }, 100);
                }, 150);
            });
        });

        // Listen for Livewire updates
        document.addEventListener('livewire:updated', function() {
            setTimeout(() => {
                initSelect2();
                initCharts();
            }, 200);
        });

        // Only re-init charts if the data for that chart is actually changed
        // Use Livewire events to trigger chart updates for each box independently
        document.addEventListener('livewire:updated', function(e) {
            // Check for specific event detail if available (Livewire >= v3)
            // Otherwise, fallback to always re-init (current behavior)
            // Example: if (e.detail && e.detail.component && e.detail.component.name === 'dashboard-pembiayaan-sfinance')
            // You can emit custom events from Livewire for more granular control
        });

        // Example: Listen for custom Livewire events to update only specific charts
        window.livewire && window.livewire.on && window.livewire.on('updateChartDisbursement', () => {
            if (chartDisbursement) {
                chartDisbursement.destroy();
                chartDisbursement = null;
            }
            setTimeout(() => {
                initCharts();
            }, 100);
        });
        window.livewire && window.livewire.on && window.livewire.on('updateChartPembayaran', () => {
            if (chartPembayaran) {
                chartPembayaran.destroy();
                chartPembayaran = null;
            }
            setTimeout(() => {
                initCharts();
            }, 100);
        });
        // Tambahkan event serupa untuk chart lain jika ingin lebih granular
    })();
</script>
@endpush