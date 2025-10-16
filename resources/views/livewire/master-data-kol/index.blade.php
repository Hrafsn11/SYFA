@extends('layouts.app')

@section('content')
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
                                                    <tr data-id="{{ $item->id_kol }}">
                                                        <td class="text-center">{{ $item->id_kol }}</td>
                                                        <td class="text-center">{{ $item->kol }}</td>
                                                           <td class="text-center">{{ $item->persentase_label }}</td>
                                                           <td class="text-center">{{ $item->tanggal_tenggat_label }}</td>
                                                        <td class="text-center">
                                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                                <button class="btn btn-sm btn-outline-primary btn-edit-kol" title="Edit">
                                                                    <i class="ti ti-edit"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger btn-delete-kol" title="Hapus">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
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
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modalTambahKOL = new bootstrap.Modal(document.getElementById('modalTambahKOL'));
        var formTambahKOL = document.getElementById('formTambahKOL');

        document.getElementById('btnTambahKOL').addEventListener('click', function() {
            formTambahKOL.reset();
            formTambahKOL.classList.remove('was-validated');
            if (formTambahKOL.dataset.editId) delete formTambahKOL.dataset.editId;
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
                persentase_pencairan: parseFloat(document.getElementById('persentase_keterlambatan').value) || 0,
                jmlh_hari_keterlambatan: parseInt(document.getElementById('tanggal_tenggat').value) || 0,
            };

            var editId = formTambahKOL.dataset.editId;
            var url = editId ? ('/master-data/kol/' + editId) : '/master-data/kol';
            var method = editId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(kolData)
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    const tbody = document.querySelector('table.datatables-basic tbody');
                    if (method === 'POST') {
                        const d = res.data;
                        const rowHtml = `
                            <tr data-id="${d.id_kol}">
                                <td class="text-center">${d.id_kol}</td>
                                <td class="text-center">${d.kol}</td>
                                    <td class="text-center">${d.persentase_label}</td>
                                    <td class="text-center">${d.tanggal_tenggat_label}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary btn-edit-kol" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-kol" title="Hapus">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', rowHtml);
                    } else {
                        const d = res.data;
                        const row = document.querySelector(`table.datatables-basic tbody tr[data-id="${d.id_kol}"]`);
                        if (row) {
                            const cells = row.querySelectorAll('td');
                            cells[1].textContent = d.kol;
                                cells[2].textContent = d.persentase_label;
                                cells[3].textContent = d.tanggal_tenggat_label;
                        }
                    }
                    delete formTambahKOL.dataset.editId;
                } else {
                    alert('Gagal menyimpan KOL');
                }
            }).catch(err => {
                console.error(err);
                alert('Terjadi kesalahan');
            });

            formTambahKOL.reset();
            formTambahKOL.classList.remove('was-validated');
            modalTambahKOL.hide();
        });

        const tableBody = document.querySelector('table.datatables-basic tbody');
        tableBody.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.btn-edit-kol');
            const deleteBtn = e.target.closest('.btn-delete-kol');
            if (editBtn) {
                const row = editBtn.closest('tr');
                const id = row.dataset.id;
                fetch('/master-data/kol/' + id + '/edit', { credentials: 'same-origin' })
                    .then(r => { console.log('Edit fetch status', r.status); return r.json(); })
                    .then(res => {
                    const d = res.data;
                    document.getElementById('kol').value = d.kol;
                    document.getElementById('persentase_keterlambatan').value = d.persentase_pencairan;
                    document.getElementById('tanggal_tenggat').value = d.jmlh_hari_keterlambatan;
                    formTambahKOL.dataset.editId = id;
                    modalTambahKOL.show();
                }).catch(err => console.error(err));
            }

            if (deleteBtn) {
                    const row = deleteBtn.closest('tr');
                    const id = row.dataset.id;
                    window._deleteTarget = { id: id, row: row };
                    modalConfirmDeleteKOL.show();
            }
        });
    });
</script>

    @push('modals')
        <!-- Confirmation modal (vertical centered) -->
        <div class="modal fade" id="modalConfirmDeleteKOL" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Apakah Anda yakin ingin menghapus KOL ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="btnConfirmDeleteKOL">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalEl = document.getElementById('modalConfirmDeleteKOL');
            window.modalConfirmDeleteKOL = new bootstrap.Modal(modalEl);
            document.getElementById('btnConfirmDeleteKOL').addEventListener('click', function() {
                var target = window._deleteTarget;
                if (!target) return modalConfirmDeleteKOL.hide();
                fetch('/master-data/kol/' + target.id, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(r => r.json()).then(res => {
                    if (res.success) {
                        if (target.row) target.row.remove();
                    } else {
                        alert('Gagal menghapus');
                    }
                }).catch(err => { console.error(err); alert('Error'); })
                .finally(() => {
                    modalConfirmDeleteKOL.hide();
                    window._deleteTarget = null;
                });
            });
        });
    </script>
    @endpush