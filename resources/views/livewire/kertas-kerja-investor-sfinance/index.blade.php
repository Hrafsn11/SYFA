@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-12">
                <div class="mb-4">
                    <h4 class="fw-bold">Kertas Kerja Investor SFinance</h4>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <div class="dataTables_wrapper dt-bootstrap5 no-footer">
                        {{-- Filter Controls --}}
                        <div class="row mx-2 mt-3 align-items-center mb-3">
                            {{-- Per Page --}}
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <span class="me-2">Show</span>
                                    <select class="form-select" style="width: auto;" id="perPageSelect">
                                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                    <span class="ms-2">Entries</span>
                                </div>
                            </div>

                            {{-- Year Filter --}}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">Tahun</span>
                                    <select class="form-select" id="yearSelect">
                                        @for ($y = date('Y'); $y >= date('Y') - 10; $y--)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                                {{ $y }}</option>
                                        @endfor
                                    </select>
                                    <button type="button" class="btn btn-primary" id="filterYearBtn">
                                        <i class="ti ti-filter"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Search --}}
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                                    <input type="search" class="form-control"
                                        placeholder="Cari deposan, nomor kontrak, status..." value="{{ $search }}"
                                        id="searchInput">
                                    @if ($search)
                                        <button type="button" class="btn btn-outline-secondary" id="clearSearchBtn">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Info Badge --}}
                            <div class="col-md-3 text-end">
                                @if ($search)
                                    <span class="badge bg-info">
                                        <i class="ti ti-search me-1"></i>
                                        Hasil pencarian: "{{ $search }}"
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Table -->
                        <div style="overflow-x: auto; white-space: nowrap;">
                            <!-- Tabel 1 -->
                            <div style="display: inline-block; vertical-align: top; margin-right: 20px;">
                                <table class="datatables-basic table border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="5%">No</th>
                                            <th class="text-center">Tanggal Uang Masuk</th>
                                            <th class="text-center">Deposito</th>
                                            <th class="text-center">Deposan</th>
                                            <th class="text-center">Nominal Deposit</th>
                                            <th class="text-center">Lama Deposito (Bulan)</th>
                                            <th class="text-center">Bagi Hasil (%PA)</th>
                                            <th class="text-center">Bagi Hasil Nominal</th>
                                            <th class="text-center">Bagi Hasil (%Bulan)</th>
                                            <th class="text-center">Bagi Hasil (COF/Bulan)</th>
                                            <th class="text-center">CoF Per Akhir Desember</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Tanggal Pengembalian</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paginatedData as $index => $row)
                                            <tr>
                                                <td class="text-center">
                                                    {{ ($pagination['current_page'] - 1) * $perPage + $index + 1 }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($row['tanggal_uang_masuk'])->format('d-m-Y') }}
                                                </td>
                                                <td class="text-center">{{ $row['deposito'] ?? '-' }}</td>
                                                <td class="text-center">{{ $row['deposan'] }}</td>
                                                <td class="text-center">Rp
                                                    {{ number_format($row['nominal_deposito'], 0, ',', '.') }}</td>
                                                <td class="text-center">{{ $row['lama_deposito'] }} Bulan</td>
                                                <td class="text-center">{{ number_format($row['bagi_hasil_pa'], 2) }}%</td>
                                                <td class="text-center">Rp
                                                    {{ number_format($row['bagi_hasil_nominal'], 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    {{ number_format($row['bagi_hasil_per_bulan'], 2) }}%</td>
                                                <td class="text-center">Rp
                                                    {{ number_format($row['cof_bulan'], 0, ',', '.') }}</td>
                                                <td class="text-center">Rp
                                                    {{ number_format($row['cof_akhir_periode'], 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    @if ($row['status'] === 'Lunas')
                                                        <span class="badge bg-label-success">Lunas</span>
                                                    @else
                                                        <span class="badge bg-label-warning">Aktif</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $row['tgl_pengembalian'] ? \Carbon\Carbon::parse($row['tgl_pengembalian'])->format('d-m-Y') : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="13" class="text-center py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="ti ti-database-off ti-xl text-muted mb-2"></i>
                                                        @if ($search)
                                                            <span class="text-muted">Tidak ada data yang cocok dengan
                                                                pencarian "{{ $search }}"</span>
                                                        @else
                                                            <span class="text-muted">Tidak ada data</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabel 2 dengan Select Period -->
                            <div style="display: inline-block; vertical-align: top; margin-right: 20px;">
                                <table class="datatables-basic table border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">Jan</th>
                                            <th class="text-center">Feb</th>
                                            <th class="text-center">Mar</th>
                                            <th class="text-center">Apr</th>
                                            <th class="text-center">Mei</th>
                                            <th class="text-center">Jun</th>
                                            <th class="text-center">Jul</th>
                                            <th class="text-center">Agu</th>
                                            <th class="text-center">Sep</th>
                                            <th class="text-center">Okt</th>
                                            <th class="text-center">Nov</th>
                                            <th class="text-center">Des</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paginatedData as $row)
                                            <tr>
                                                <td class="text-center">Rp {{ number_format($row['jan'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['feb'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['mar'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['apr'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['mei'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['jun'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['jul'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['agu'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['sep'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['okt'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['nov'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">Rp {{ number_format($row['des'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabel 3 -->
                            <div style="display: inline-block; vertical-align: top;">
                                <table class="datatables-basic table border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">Pengembalian Pokok Deposito</th>
                                            <th class="text-center">Pengembalian Bagi Hasil Deposito</th>
                                            <th class="text-center">Sisa Pokok Belum Dikembalikan</th>
                                            <th class="text-center">Sisa Bagi Hasil Belum Dikembalikan</th>
                                            <th class="text-center">Total Belum Dikembalikan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paginatedData as $row)
                                            <tr>
                                                <td class="text-center">Rp
                                                    {{ number_format($row['pengembalian_pokok'], 0, ',', '.') }}</td>
                                                <td class="text-center">Rp
                                                    {{ number_format($row['pengembalian_bagi_hasil'], 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    <strong class="text-danger">Rp
                                                        {{ number_format($row['sisa_pokok'], 0, ',', '.') }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong class="text-danger">Rp
                                                        {{ number_format($row['sisa_bagi_hasil'], 0, ',', '.') }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong class="text-danger">Rp
                                                        {{ number_format($row['total_belum_dikembalikan'], 0, ',', '.') }}</strong>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="row mx-2 mt-3 mb-3">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_info">
                                    @if ($pagination['total'] > 0)
                                        Menampilkan {{ $pagination['from'] }} sampai {{ $pagination['to'] }} dari
                                        {{ $pagination['total'] }} data
                                    @else
                                        Tidak ada data untuk ditampilkan
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                @if ($pagination['last_page'] > 1)
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        <ul class="pagination justify-content-end mb-0">
                                            <!-- Previous -->
                                            <li
                                                class="paginate_button page-item {{ $pagination['current_page'] == 1 ? 'disabled' : '' }}">
                                                <a href="javascript:void(0)" class="page-link"
                                                    onclick="goToPage({{ $pagination['current_page'] - 1 }})">
                                                    <i class="ti ti-chevron-left ti-xs"></i>
                                                </a>
                                            </li>

                                            @php
                                                $start = max(1, $pagination['current_page'] - 2);
                                                $end = min($pagination['last_page'], $pagination['current_page'] + 2);
                                            @endphp

                                            @if ($start > 1)
                                                <li class="paginate_button page-item">
                                                    <a href="javascript:void(0)" class="page-link"
                                                        onclick="goToPage(1)">1</a>
                                                </li>
                                                @if ($start > 2)
                                                    <li class="paginate_button page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                @endif
                                            @endif

                                            @for ($i = $start; $i <= $end; $i++)
                                                <li
                                                    class="paginate_button page-item {{ $pagination['current_page'] == $i ? 'active' : '' }}">
                                                    <a href="javascript:void(0)" class="page-link"
                                                        onclick="goToPage({{ $i }})">{{ $i }}</a>
                                                </li>
                                            @endfor

                                            @if ($end < $pagination['last_page'])
                                                @if ($end < $pagination['last_page'] - 1)
                                                    <li class="paginate_button page-item disabled">
                                                        <span class="page-link">...</span>
                                                    </li>
                                                @endif
                                                <li class="paginate_button page-item">
                                                    <a href="javascript:void(0)" class="page-link"
                                                        onclick="goToPage({{ $pagination['last_page'] }})">{{ $pagination['last_page'] }}</a>
                                                </li>
                                            @endif

                                            <!-- Next -->
                                            <li
                                                class="paginate_button page-item {{ $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : '' }}">
                                                <a href="javascript:void(0)" class="page-link"
                                                    onclick="goToPage({{ $pagination['current_page'] + 1 }})">
                                                    <i class="ti ti-chevron-right ti-xs"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Current filter state
            var baseUrl = '{{ route('kertas-kerja-investor-sfinance.index') }}';
            var currentYear = '{{ $year }}';
            var currentPerPage = '{{ $perPage }}';
            var currentSearch = '{{ $search }}';
            var lastPage = {{ $pagination['last_page'] }};

            // Get elements
            var yearSelect = document.getElementById('yearSelect');
            var perPageSelect = document.getElementById('perPageSelect');
            var searchInput = document.getElementById('searchInput');
            var filterYearBtn = document.getElementById('filterYearBtn');
            var clearBtn = document.getElementById('clearSearchBtn');

            // Navigate with params
            function navigate(page) {
                page = page || 1;
                var url = baseUrl + '?year=' + currentYear + '&per_page=' + currentPerPage + '&page=' + page;
                if (currentSearch && currentSearch.trim() !== '') {
                    url += '&search=' + encodeURIComponent(currentSearch);
                }
                console.log('Navigating to:', url);
                window.location.href = url;
            }

            // Year filter button click
            if (filterYearBtn) {
                filterYearBtn.onclick = function() {
                    if (yearSelect) {
                        currentYear = yearSelect.value;
                        console.log('Selected year:', currentYear);
                    }
                    navigate(1);
                };
            }

            // Per page change
            if (perPageSelect) {
                perPageSelect.onchange = function() {
                    currentPerPage = this.value;
                    navigate(1);
                };
            }

            // Search input - Enter key
            if (searchInput) {
                searchInput.onkeydown = function(e) {
                    if (e.key === 'Enter' || e.keyCode === 13) {
                        e.preventDefault();
                        currentSearch = this.value;
                        console.log('Search:', currentSearch);
                        navigate(1);
                    }
                };
            }

            // Clear search button
            if (clearBtn) {
                clearBtn.onclick = function() {
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    currentSearch = '';
                    navigate(1);
                };
            }

            // Pagination function - make it global
            window.goToPage = function(page) {
                if (page < 1 || page > lastPage) return;
                navigate(page);
            };

            console.log('Kertas Kerja Investor JS loaded. Year:', currentYear, 'PerPage:', currentPerPage);
        });
    </script>
@endpush
