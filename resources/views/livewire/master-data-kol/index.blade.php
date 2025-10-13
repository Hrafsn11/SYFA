<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">KOL</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3" id="btnTambahKOL">
                    <i class="fa-solid fa-plus"></i>
                    KOL
                </button>
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
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th class="text-center">KOL</th>
                                    <th class="text-center">Persentase Pencairan</th>
                                    <th class="text-center">Jumlah Hari Keterlambatan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td class="text-center">{{ $item['id'] }}</td>
                                        <td class="text-center">{{ $item['kol'] }}</td>
                                        <td class="text-center">{{ $item['persentase_keterlambatan'] }}</td>
                                        <td class="text-center">{{ $item['tanggal_tenggat'] }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <a class="" href="#">
                                                    <i class="ti ti-edit me-1"></i>
                                                </a>
                                                <a class="text-danger" href="#">
                                                    <i class="ti ti-trash me-1"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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

    <div class="modal fade" id="modalTambahKOL" tabindex="-1" aria-labelledby="modalTambahKOLLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahKOLLabel">Tambah KOL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahKOL">
                        <div class="mb-3">
                            <label for="kol" class="form-label">KOL <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="kol" placeholder="Masukkan KOL" required min="1" step="1">
                        </div>
                        <div class="mb-3">
                            <label for="persentase_keterlambatan" class="form-label">Persentase Pencairan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="persentase_keterlambatan" placeholder="Masukkan Persentase Pencairan" required min="0" max="100" step="1">
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_tenggat" class="form-label">Jumlah Hari Keterlambatan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="tanggal_tenggat" placeholder="Masukkan Jumlah Hari Keterlambatan" required min="0" step="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanKOL">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalTambahKOL = new bootstrap.Modal(document.getElementById('modalTambahKOL'));
        var formTambahKOL = document.getElementById('formTambahKOL');

        document.getElementById('btnTambahKOL').addEventListener('click', function() {
            // Reset form ketika modal dibuka
            formTambahKOL.reset();
            formTambahKOL.classList.remove('was-validated');
            modalTambahKOL.show();
        });

        document.getElementById('btnSimpanKOL').addEventListener('click', function() {
            // Validasi form
            if (!formTambahKOL.checkValidity()) {
                formTambahKOL.classList.add('was-validated');
                return;
            }

            // Ambil data dari form
            var kolData = {
                kol: parseInt(document.getElementById('kol').value),
                persentase_keterlambatan: parseInt(document.getElementById('persentase_keterlambatan').value),
                tanggal_tenggat: parseInt(document.getElementById('tanggal_tenggat').value)
            };

            // Logic to save KOL data goes here
            console.log('Data KOL:', kolData);

            // Reset form dan tutup modal
            formTambahKOL.reset();
            formTambahKOL.classList.remove('was-validated');
            modalTambahKOL.hide();
        });
    });
</script>