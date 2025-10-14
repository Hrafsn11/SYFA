<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Sumber Pendanaan Eksternal</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    id="btnTambahSumberPendanaan">
                    <i class="fa-solid fa-plus"></i>
                    Sumber Pendanaan
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
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Instansi</th>
                                    <th class="text-center">Presentase Bagi Hasil</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $item['nama_instansi'] }}</td>
                                        <td class="text-center">{{ $item['presentase_bagi_hasil'] }}%</td>
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
                                    Menampilkan data {{ count($data) }} dari {{ count($data) }}
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

    <div class="modal fade" id="modalTambahSumberPendanaan" tabindex="-1"
        aria-labelledby="modalTambahSumberPendanaanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahSumberPendanaanLabel">Tambah Sumber Pendanaan Eksternal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form -->
                    <form id="formTambahSumberPendanaan">
                        <div class="mb-3">
                            <label for="nama_instansi" class="form-label">Nama Instansi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_instansi"
                                placeholder="Masukkan nama instansi" required>
                        </div>
                        <div class="mb-3">
                            <label for="presentase_bagi_hasil" class="form-label">Presentase Bagi Hasil <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="presentase_bagi_hasil"
                                placeholder="Masukkan presentase bagi hasil" required min="0" max="100" step="1">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanSumberPendanaan">Simpan</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalTambahSumberPendanaan = new bootstrap.Modal(document.getElementById('modalTambahSumberPendanaan'));
        var formTambahSumberPendanaan = document.getElementById('formTambahSumberPendanaan');

        document.getElementById('btnTambahSumberPendanaan').addEventListener('click', function() {
            // Reset form ketika modal dibuka
            formTambahSumberPendanaan.reset();
            formTambahSumberPendanaan.classList.remove('was-validated');
            modalTambahSumberPendanaan.show();
        });

        document.getElementById('btnSimpanSumberPendanaan').addEventListener('click', function() {
            // Validasi form
            if (!formTambahSumberPendanaan.checkValidity()) {
                formTambahSumberPendanaan.classList.add('was-validated');
                return;
            }

            // Ambil data dari form
            var sumberPendanaanData = {
                nama_instansi: document.getElementById('nama_instansi').value,
                presentase_bagi_hasil: parseInt(document.getElementById('presentase_bagi_hasil').value),
                kol: parseInt(document.getElementById('kol').value)
            };

            // Logic to save Sumber Pendanaan data goes here
            console.log('Data Sumber Pendanaan:', sumberPendanaanData);

            // Reset form dan tutup modal
            formTambahSumberPendanaan.reset();
            formTambahSumberPendanaan.classList.remove('was-validated');
            modalTambahSumberPendanaan.hide();
        });
    });
</script>
