<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Menu Pengajuan Peminjaman</h4>
                <a href="{{ route('ajukanpeminjaman') }}"
                    class="btn btn-primary d-flex justify-center align-items-center gap-3">
                    <i class="fa-solid fa-plus"></i>
                    Ajukan Peminjaman
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
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
                                            <input type="search" class="form-control rounded-md"
                                                placeholder="Cari..." />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <table class="datatables-basic table border-top">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th>Nama Perusahaan</th>
                                    <th class="text-center">Lampiran SID</th>
                                    <th class="text-center">Nilai Kol</th>
                                    <th class="text-center" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Row 1 -->
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">PT. Maju Bersama</span>
                                            <small class="text-muted">NPWP: 01.234.567.8-901.000</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-icon btn-label-info">
                                            <i class="ti ti-file-text"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-success">A</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-center align-items-center gap-2">
                                            <a class="dropdown-item" href="#">
                                                <i class="ti ti-file-text"></i>
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="ti ti-edit me-1"></i>
                                            </a>
                                            <a class="dropdown-item text-danger" href="#">
                                                <i class="ti ti-trash me-1"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Row 2 -->
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">CV. Sejahtera Mandiri</span>
                                            <small class="text-muted">NPWP: 02.345.678.9-012.000</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-icon btn-label-info">
                                            <i class="ti ti-file-text"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-warning">B</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-center align-items-center gap-2">
                                            <a class="dropdown-item" href="#">
                                                <i class="ti ti-file-text"></i>
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="ti ti-edit me-1"></i>
                                            </a>
                                            <a class="dropdown-item text-danger" href="#">
                                                <i class="ti ti-trash me-1"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Row 3 -->
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">PT. Karya Jaya</span>
                                            <small class="text-muted">NPWP: 03.456.789.0-123.000</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-icon btn-label-info">
                                            <i class="ti ti-file-text"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-danger">D</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-center align-items-center gap-2">
                                            <a class="dropdown-item" href="#">
                                                <i class="ti ti-file-text"></i>
                                            </a>
                                            <a class="dropdown-item" href="#">
                                                <i class="ti ti-edit me-1"></i>
                                            </a>
                                            <a class="dropdown-item text-danger" href="#">
                                                <i class="ti ti-trash me-1"></i>
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
                                    Menampilkan 1 sampai 5 dari 24 data
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
                                        <li class="paginate_button page-item">
                                            <a href="#" class="page-link">3</a>
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
</div>
