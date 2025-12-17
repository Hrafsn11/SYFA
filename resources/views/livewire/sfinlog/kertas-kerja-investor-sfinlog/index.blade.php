@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-12">
                <div class="mb-4">
                    <h4 class="fw-bold">Kertas Kerja Investor SFinlog</h4>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <div class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <!-- Search and Filter -->
                        <div class="row mx-2 mt-3 align-items-center mb-3">
                            <div class="col-md-2">
                                <form method="GET" action="{{ route('sfinlog.kertas-kerja-investor-sfinlog.index') }}" id="perPageForm">
                                    <input type="hidden" name="year" value="{{ $year }}">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">Show</span>
                                        <select class="form-select" style="width: auto;" name="per_page" id="perPageSelect" onchange="document.getElementById('perPageForm').submit();">
                                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                        <span class="ms-2">Entries</span>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-3">
                                <!-- Year Filter -->
                                <form method="GET" action="{{ route('sfinlog.kertas-kerja-investor-sfinlog.index') }}">
                                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Select Year" id="flatpickr-year"
                                            name="year" value="{{ $year }}" />
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-filter"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-7">
                                <div class="d-flex justify-content-end">
                                    <input type="search" class="form-control search-input" placeholder="Cari..." />
                                </div>
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
                                            <th class="text-center">Deposan</th>
                                            <th class="text-center">Nominal Deposito</th>
                                            <th class="text-center">Lama Deposito (Bulan)</th>
                                            <th class="text-center">Bagi Hasil (%PA)</th>
                                            <th class="text-center">Bagi Hasil (Nominal/PA)</th>
                                            <th class="text-center">Bagi Hasil (%Bulan)</th>
                                            <th class="text-center">Bagi Hasil (COF/Bulan)</th>
                                            <th class="text-center">CoF Per Akhir Des {{ $year }}</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data as $index => $row)
                                            <tr>
                                                <td class="text-center">{{ $data->firstItem() + $index }}</td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($row['tanggal_uang_masuk'])->format('d-m-Y') }}
                                                </td>
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
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center">Tidak ada data</td>
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
                                        @forelse($data as $row)
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
                                        @forelse($data as $row)
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
                                    Menampilkan {{ $data->firstItem() ?? 0 }} sampai {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} data
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {{ $data->appends(request()->query())->links('pagination::bootstrap-5') }}
                                </div>
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
            // Flatpickr for year selection
            flatpickr('#flatpickr-year', {
                mode: "single",
                dateFormat: "Y",
                defaultDate: "{{ $year }}"
            });
        });
    </script>
@endpush

