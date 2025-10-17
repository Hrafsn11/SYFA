@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengembalian Peminjaman</h4>
                <a href="{{ route('pengembalian.create') }}" class="btn btn-primary d-flex justify-center align-items-center gap-3">
                    <i class="fa-solid fa-plus"></i>
                    Pengembalian Pinjaman
                </a>
            </div>

            <div class="card">
                <div class="card-datatable table-responsive">
                    <div class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <!-- Search and Filter -->
                        <div class="row mx-2 mt-3">
                            <div class="col-md-2">
                                <div class="me-3">
                                    <div class="dataTables_length">
                                        <label>
                                            <span class="me-2">Show</span>
                                            <select class="form-select rounded-md">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                            <span class="me-2">Entries</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div
                                    class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                    <div class="dataTables_filter">
                                        <label>
                                            <input type="search" class="form-control rounded-md" placeholder="Cari..." />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <table class="datatables-basic table border-top">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th class="text-center">Nama Perusahaan</th>
                                    <th class="text-center">Tanggal pembayaran</th>
                                    <th class="text-center">No Invoice</th>
                                    <th class="text-center">nominal pembayaran</th>
                                    <th class="text-center">sisa utang</th>
                                    <th class="text-center" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">PT. Maju Mundur</td>
                                    <td class="text-center">12-06-2024</td>
                                    <td class="text-center">INV-00123</td>
                                    <td class="text-center">Rp 5.000.000</td>
                                    <td class="text-center">Rp 15.000.000</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="#" class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

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
