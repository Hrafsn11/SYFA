@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable table-responsive">
                    <div class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <!-- Search and Filter -->
                        <div class="row mx-2 mt-3 align-items-center mb-3">
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <span class="me-2">Show</span>
                                    <select class="form-select" style="width: auto;">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="ms-2">Entries</span>
                                </div>
                            </div>

                            <div class="col-md-10">
                                <div class="d-flex justify-content-end">
                                    <input type="search" class="form-control search-input" placeholder="Cari..." />
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                         <div style="overflow-x: auto; white-space: nowrap;">
                            <!-- Tabel 1 -->
                            <div class="table-container">
                                <!-- Placeholder kosong untuk sejajarkan dengan tabel 2 -->
                                <div class="filter-placeholder"></div>
                                
                                <table class="datatables-basic table border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="5%">No</th>
                                            <th class="text-center">Nama Debitur</th>
                                            <th class="text-center">Objek Jaminan</th>
                                            <th class="text-center">Tanggal Peminjaman Pengajuan</th>
                                            <th class="text-center">Nilai Pinjaman Yang Di Ajukan</th>
                                            <th class="text-center">Nilai Pinjaman Yang Dicairkan</th>
                                            <th class="text-center">Tanggal Pencairan</th>
                                            <th class="text-center">Masa Penggunaan</th>
                                            <th class="text-center">Nilai Bagi Hasil Object Debitur</th>
                                            <th class="text-center">Nilai Yang Harus Dibayar Debitur</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Tanggal Bayar</th>
                                            <th class="text-center">Lamanya Pinjaman</th>
                                            <th class="text-center">Nilai Bayar</th>
                                            <th class="text-center">Total Sisa Pokok + Bagi Hasil</th>
                                            <th class="text-center">Total Kurang Bayar Bagi Hasil</th>
                                            <th class="text-center">Nilai Pokok Januari Dan Nilai Pokok Yang Belum Bayar Sama Sekali</th>
                                            <th class="text-center">% Bagi Hasil</th>
                                            <th class="text-center">Bagi Hasil Perbulan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td>Ahmad Supriadi</td>
                                            <td>Emas 10 gram</td>
                                            <td class="text-center">01/01/2024</td>
                                            <td class="text-center">Rp 5.000.000</td>
                                            <td class="text-center">Rp 4.800.000</td>
                                            <td class="text-center">05/01/2024</td>
                                            <td class="text-center">6 Bulan</td>
                                            <td class="text-center">Rp 600.000</td>
                                            <td class="text-center">Rp 5.400.000</td>
                                            <td class="text-center">Aktif</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">-</td>
                                            <td class="text-center">Rp 5.400.000</td>
                                            <td class="text-center">Rp 600.000</td>
                                            <td class="text-center">Rp 4.800.000</td>
                                            <td class="text-center">12%</td>
                                            <td class="text-center">Rp 100.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabel 2 dengan Select Period -->
                            <div class="table-container">
                                <div class="mb-3" style="width: 250px;">
                                    <div class="input-group input-group-md">
                                        <input type="text" class="form-control" placeholder="Select Period"
                                            id="flatpickr-tahun-pencarian" name="tahun_pencarian" />
                                        <span class="input-group-text cursor-pointer">
                                            <i class="ti ti-filter"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <table class="datatables-basic table border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">Tanggal Bayar</th>
                                            <th class="text-center">Nilai Bayar </th>
                                            <th class="text-center">Nilai Pembayaran Pokok </th>
                                            <th class="text-center">Nilai Pokok Bulan Selanjutnya </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">15/01/2025</td>
                                            <td class="text-center">Rp 1.000.000</td>
                                            <td class="text-center">Rp 800.000</td>
                                            <td class="text-center">Rp 4.000.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabel 3 -->
                            <div class="table-container" style="margin-right: 0;">
                                <!-- Placeholder kosong untuk sejajarkan dengan tabel 2 -->
                                <div class="filter-placeholder"></div>
                                
                                <table class="datatables-basic table border-top">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">Subtotal Sisa Pokok Dan Bagi Hasil</th>
                                            <th class="text-center">Pokok</th>
                                            <th class="text-center">Sisa Bagi Hasil</th>
                                            <th class="text-center">Telat Hari</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">Rp 4.600.000</td>
                                            <td class="text-center">Rp 4.000.000</td>
                                            <td class="text-center">Rp 600.000</td>
                                            <td class="text-center">0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="row mx-2 mt-3 mb-3">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_info">
                                    Menampilkan data
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    <ul class="pagination">
                                        <li class="paginate_button page-item previous disabled">
                                            <a href="#" class="page-link">Sebelumnya</a>
                                        </li>
                                        <li class="paginate_button page-item active">
                                            <a href="#" class="page-link">1</a>
                                        </li>
                                        <li class="paginate_button page-item">
                                            <a href="#" class="page-link">2</a>
                                        </li>
                                        <li class="paginate_button page-item next">
                                            <a href="#" class="page-link">Selanjutnya</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
       
        .table-container {
            display: inline-block;
            vertical-align: top;
            margin-right: 20px;
            white-space: normal; 
            min-width: 250px; 
        }

        .filter-placeholder {
            width: 250px; 
            height: 42px; 
            margin-bottom: 0.5rem;
        }

        .table-container .input-group {
            width: 250px;
        }

        .table-container .table {
            margin-bottom: 0;
        }

    
        .table-container table {
            display: inline-table; 
            width: auto;
            table-layout: auto; 
        }

        .table-container table th,
        .table-container table td {
            white-space: nowrap;
        }
        media (max-width: 768px) {
            .table-container {
                display: block;
                width: 100%;
                margin-right: 0;
            }

            .filter-placeholder {
                display: none; 
            }

            .table-container .input-group {
                width: 100%;
            }
        }
    </style>
@endpush
