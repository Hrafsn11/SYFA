@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Debitur dan Investor</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    data-bs-toggle="modal" data-bs-target="#modalTambahDebiturInvestor">
                    <i class="fa-solid fa-plus"></i>
                    Debitur dan Investor
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
                                    <th class="text-center">Nama Perusahaan</th>
                                    <th class="text-center">Flagging</th>
                                    <th class="text-center">Nama ceo</th>
                                    <th class="text-center">alamat perusahaan</th>
                                    <th class="text-center">email</th>
                                    <th class="text-center">KOL Perusahaan</th>
                                    <th class="text-center">Nama bank</th>
                                    <th class="text-center">no.rek</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr data-id="{{ $item['id'] }}">
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $item['nama_perusahaan'] }}</td>
                                        <td class="text-center">{{ $item['Flagging'] }}</td>
                                        <td class="text-center">{{ $item['nama_ceo'] }}</td>
                                        <td class="text-center">{{ $item['alamat_perusahaan'] }}</td>
                                        <td class="text-center">{{ $item['email'] }}</td>
                                        <td class="text-center">{{ $item['kol_perusahaan'] }}</td>
                                        <td class="text-center">{{ $item['nama_bank'] }}</td>
                                        <td class="text-center">{{ $item['no_rek'] }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect edit-btn"
                                                data-id="{{ $item['id'] }}" data-bs-toggle="modal" data-bs-target="#modalTambahDebiturInvestor">
                                                <i class="ti ti-edit"></i>
                                            </button>
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

    <!-- Modal Tambah/Edit Debitur dan Investor -->
    <div class="modal fade" id="modalTambahDebiturInvestor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Debitur dan Investor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="modalForm" action="#" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Nama Perusahaan -->
                            <div class="col-12 mb-3">
                                <label for="nama_perusahaan" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" placeholder="Masukkan Nama Perusahaan" required>
                            </div>

                            <!-- Flagging -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Flagging <span class="text-danger">*</span></label>
                                <p class="text-muted small mb-2">Apakah anda termasuk investor?</p>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input name="flagging" class="form-check-input" type="radio" value="ya" id="flaggingYa" required>
                                        <label class="form-check-label" for="flaggingYa">Ya</label>
                                    </div>
                                    <div class="form-check">
                                        <input name="flagging" class="form-check-input" type="radio" value="tidak" id="flaggingTidak" required>
                                        <label class="form-check-label" for="flaggingTidak">Tidak</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Nama CEO -->
                            <div class="col-12 mb-3">
                                <label for="nama_ceo" class="form-label">Nama CEO <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_ceo" name="nama_ceo" placeholder="Masukkan Nama CEO" required>
                            </div>

                            <!-- Alamat Perusahaan -->
                            <div class="col-12 mb-3">
                                <label for="alamat_perusahaan" class="form-label">Alamat Perusahaan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="2" placeholder="Masukkan alamat perusahaan" required></textarea>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
                            </div>

                            <!-- KOL Perusahaan -->
                            <div class="col-md-6 mb-3">
                                <label for="kol" class="form-label">KOL Perusahaan <span class="text-danger">*</span></label>
                                <select id="kol" name="kol_perusahaan" class="form-select" data-placeholder="Pilih KOL" required>
                                    <option value="">Pilih KOL</option>
                                    @foreach ($kol as $kolItem)
                                        <option value="{{ $kolItem['id'] }}">{{ $kolItem['kol'] }}</option>
                                    @endforeach                                   
                                </select>
                            </div>

                            <!-- Nama Bank -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_bank" class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_bank" name="nama_bank" placeholder="Masukkan nama bank" required>
                            </div>

                            <!-- No. Rekening -->
                            <div class="col-md-6 mb-3">
                                <label for="no_rek" class="form-label">No. Rekening <span class="text-danger">*</span></label>
                                <input type="number" step="1" min="0" pattern="[0-9]*"class="form-control" id="no_rek" name="no_rek" placeholder="Masukkan no rekening" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="modalCancelOrDelete" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <span id="submitButtonText">Simpan Data</span>
                            <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="confirmDeleteButton" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('modalTambahDebiturInvestor'));
        const modalTitle = document.getElementById('modalTitle');
        const modalForm = document.getElementById('modalForm');
        const formMethod = document.getElementById('formMethod');
        const submitButtonText = document.getElementById('submitButtonText');
        const SERVER_KOL = @json($kol);
        
        console.log('SERVER_KOL:', SERVER_KOL);
        console.log('KOL select options count:', document.querySelectorAll('#kol option').length);

        document.getElementById('modalTambahDebiturInvestor').addEventListener('hidden.bs.modal', function () {
            modalForm.reset();
            modalTitle.textContent = 'Tambah Debitur dan Investor';
            submitButtonText.textContent = 'Simpan Data';
            modalForm.action = "#";
            formMethod.value = 'POST';
            $('#kol').val('').trigger('change');
        });

        const $selectKol = $('#kol');
        let select2Initialized = false;
        
        function initSelect2() {
            if (select2Initialized) {
                try { $selectKol.select2('destroy'); } catch(e){}
            }
            $selectKol.select2({
                dropdownParent: $('#modalTambahDebiturInvestor'),
                placeholder: 'Pilih KOL Perusahaan',
                allowClear: true,
                width: '100%'
            });
            select2Initialized = true;
        }
        
        document.getElementById('modalTambahDebiturInvestor').addEventListener('shown.bs.modal', function () {
            initSelect2();
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.edit-btn');
            if (!btn) return;
            const id = btn.dataset.id;
            fetch(`/master-data/debitur-investor/${id}/edit`, {credentials:'same-origin'})
                .then(r => r.json())
                .then(data => {
                    modalTitle.textContent = 'Edit Debitur dan Investor';
                    submitButtonText.textContent = 'Update Data';
                    modalForm.action = `/master-data/debitur-investor/${id}`;
                    formMethod.value = 'PUT';

                    document.getElementById('nama_perusahaan').value = data.nama_perusahaan || '';
                    if (data.flagging_raw && data.flagging_raw.toLowerCase() === 'ya') {
                        document.getElementById('flaggingYa').checked = true;
                    } else {
                        document.getElementById('flaggingTidak').checked = true;
                    }
                    document.getElementById('nama_ceo').value = data.nama_ceo || '';
                    document.getElementById('alamat_perusahaan').value = data.alamat_perusahaan || '';
                    document.getElementById('email').value = data.email || '';
                    $('#kol').val(data.kol_perusahaan).trigger('change');
                    document.getElementById('nama_bank').value = data.nama_bank || '';
                    document.getElementById('no_rek').value = data.no_rek || '';
                    const modalCancelBtn = document.getElementById('modalCancelOrDelete');
                    modalCancelBtn.textContent = 'Hapus';
                    modalCancelBtn.classList.remove('btn-label-secondary');
                    modalCancelBtn.classList.add('btn-danger');
                    modalCancelBtn.removeAttribute('data-bs-dismiss');
                    function onModalDeleteClick(ev) {
                        ev.preventDefault();
                        window.currentEditingId = id;
                        confirmModal.show();
                    }
                    const newModalCancel = modalCancelBtn.cloneNode(true);
                    modalCancelBtn.parentNode.replaceChild(newModalCancel, modalCancelBtn);
                    newModalCancel.addEventListener('click', onModalDeleteClick);

                    modal.show();
                });
        });

        modalForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const method = formMethod.value === 'PUT' ? 'PUT' : 'POST';
            const action = modalForm.action && modalForm.action !== '#' ? modalForm.action : '/master-data/debitur-investor';
            const fd = new FormData(modalForm);
            
            if (!fd.has('flagging')) {
                const flag = document.querySelector('input[name="flagging"]:checked');
                if (flag) fd.set('flagging', flag.value);
            }

            fetch(action, {
                method: method === 'PUT' ? 'POST' : method,
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: fd
            }).then(r => r.json()).then(res => {
                if (!res.success) {
                    alert('Gagal menyimpan');
                    return;
                }
                const d = res.data;
                
                if (method === 'POST') {
                    const tbody = document.querySelector('table.datatables-basic tbody');
                    const row = document.createElement('tr');
                    row.dataset.id = d.id;
                    row.innerHTML = `
                        <td class="text-center">${tbody.children.length + 1}</td>
                        <td class="text-center">${d.nama_perusahaan}</td>
                        <td class="text-center">${d.Flagging}</td>
                        <td class="text-center">${d.nama_ceo || ''}</td>
                        <td class="text-center">${d.alamat_perusahaan || ''}</td>
                        <td class="text-center">${d.email || ''}</td>
                        <td class="text-center">${d.kol_perusahaan || d.kol_id}</td>
                        <td class="text-center">${d.nama_bank || ''}</td>
                        <td class="text-center">${d.no_rek || ''}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect edit-btn" data-id="${d.id}" data-bs-toggle="modal" data-bs-target="#modalTambahDebiturInvestor"><i class="ti ti-edit"></i></button>
                        </td>
                    `;
                    tbody.appendChild(row);
                } else {
                    const row = document.querySelector(`table.datatables-basic tbody tr[data-id="${d.id}"]`);
                    if (row) {
                        const cells = row.querySelectorAll('td');
                        cells[1].textContent = d.nama_perusahaan;
                        cells[2].textContent = d.Flagging;
                        cells[3].textContent = d.nama_ceo || '';
                        cells[4].textContent = d.alamat_perusahaan || '';
                        cells[5].textContent = d.email || '';
                        cells[6].textContent = d.kol_perusahaan || d.kol_id;
                        cells[7].textContent = d.nama_bank || '';
                        cells[8].textContent = d.no_rek || '';
                    }
                }
                
                modalForm.reset();
                formMethod.value = 'POST';
                modal.hide();
            }).catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat menyimpan');
            });
        });

        document.addEventListener('click', function (e) {
            return;
        });

        const confirmModalEl = document.getElementById('modalConfirmDelete');
        const confirmModal = new bootstrap.Modal(confirmModalEl);

        document.getElementById('confirmDeleteButton').addEventListener('click', function () {
            const id = window.currentEditingId;
            if (!id) return;
            fetch(`/master-data/debitur-investor/${id}`, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    const row = document.querySelector(`table.datatables-basic tbody tr[data-id="${id}"]`);
                    if (row) row.remove();
                    confirmModal.hide();
                    modal.hide();
                } else {
                    alert('Gagal menghapus');
                }
            }).catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat menghapus');
            }).finally(() => {
                window.currentEditingId = null;
            });
        });

        document.getElementById('modalTambahDebiturInvestor').addEventListener('hidden.bs.modal', function () {
            const modalCancelBtn = document.getElementById('modalCancelOrDelete');
            if (modalCancelBtn) {
                modalCancelBtn.textContent = 'Batal';
                modalCancelBtn.classList.remove('btn-danger');
                modalCancelBtn.classList.add('btn-label-secondary');
                modalCancelBtn.setAttribute('data-bs-dismiss', 'modal');
                const clone = modalCancelBtn.cloneNode(true);
                modalCancelBtn.parentNode.replaceChild(clone, modalCancelBtn);
            }
            formMethod.value = 'POST';
            window.currentEditingId = null;
        });
    });
</script>
@endpush