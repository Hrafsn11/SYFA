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
                            <div class="col-md-3">
                                <select class="form-select" style="color: #999;">
                                    <option value="">Select Period</option>
                                    <option value="2025-01">Januari 2025</option>
                                    <option value="2025-02">Februari 2025</option>
                                    <option value="2025-03">Maret 2025</option>
                                    <option value="2025-04">April 2025</option>
                                    <option value="2025-05">Mei 2025</option>
                                    <option value="2025-06">Juni 2025</option>
                                    <option value="2025-07">Juli 2025</option>
                                    <option value="2025-08">Agustus 2025</option>
                                    <option value="2025-09">September 2025</option>
                                    <option value="2025-10">Oktober 2025</option>
                                    <option value="2025-11">November 2025</option>
                                    <option value="2025-12">Desember 2025</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <div class="d-flex justify-content-end">
                                    <input type="search" class="form-control" placeholder="Cari..." style="max-width: 250px;" />
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div style="overflow-x: auto; white-space: nowrap;">
                            <table class="datatables-basic table border-top" style="display: inline-table; margin-right: 20px; vertical-align: top;">
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
                                        <th class="text-center">Nilai Pokok Jan 2025 Dan Nilai Pokok Yang Belum Bayar Sama Sekali</th>
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

                            
                            <table class="datatables-basic table border-top" style="display: inline-table; margin-right: 20px; vertical-align: top;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">Tanggal Bayar</th>
                                        <th class="text-center">Nilai Bayar Januari 2025</th>
                                        <th class="text-center">Nilai Pembayaran Pokok Jan 2025</th>
                                        <th class="text-center">Nilai Pokok Februari 2025</th>
                                        
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

                            <table class="datatables-basic table border-top" style="display: inline-table; vertical-align: top;">
                                
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
    .table {
        margin-bottom: 0;
        font-size: 0.875rem;
    }
    
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        white-space: nowrap;
        padding: 0.75rem;
        vertical-align: middle;
    }
    
    .table tbody td {
        white-space: nowrap;
        padding: 0.75rem;
        vertical-align: middle;
    }
</style>
@endpush
