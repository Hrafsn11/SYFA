@extends('layouts.app')

@section('content')
<div>
    <div class="row">
        <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Config Matrix Nominal Peminjaman</h4>
                <button type="button" id="btnTambahConfig" class="btn btn-primary d-flex justify-center align-items-center gap-3">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Data
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
                                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                    <div class="dataTables_filter">
                                        <label>
                                            <input type="search" class="form-control rounded-md" placeholder="Cari..." />
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Confirm Delete Modal -->
                        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Yakin ingin menghapus data ini?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <table class="datatables-basic table border-top" id="tableConfigMatrix">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="5%">No</th>
                                    <th class="text-center">Nominal</th>
                                    <th class="text-center">Approve Oleh</th>
                                    <th class="text-center" width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $item['nominal'] }}</td>
                                    <td class="text-center">{{ $item['approve_oleh'] }}</td>
                                    <td class="text-center">
                                            <div class="d-flex justify-center align-items-center gap-2">
                                                <a class="dropdown-item btn-edit" href="#" data-id="{{ $item['id'] }}">
                                                    <i class="ti ti-edit me-1"></i>
                                                </a>
                                                <a class="dropdown-item text-danger btn-delete" href="#" data-id="{{ $item['id'] }}">
                                                    <i class="ti ti-trash me-1"></i>
                                                </a>
                                            </div>
                                    </td>
                                </tr>
                                @endforeach
                                
                            </tbody>
                        </table>

                        <!-- Modal CRUD Config Matrix Pinjaman (embedded) -->
                        <div class="modal fade" id="modalConfigMatrix" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalConfigTitle">Tambah Config Matrix</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formConfigMatrix">
                                            <input type="hidden" id="matrix_id" name="id" />
                                            <div class="mb-3">
                                                <label class="form-label">Nominal</label>
                                                <input type="text" class="form-control input-rupiah" id="nominal" name="nominal" placeholder="0.00" data-format="rupiah" />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Approve Oleh</label>
                                                <input type="text" class="form-control" id="approve_oleh" name="approve_oleh" placeholder="Nama approver" />
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-primary" id="saveConfigBtn">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function(){
                                (function(){
                                    const modalEl = document.getElementById('modalConfigMatrix');
                                    if(!modalEl) return;
                                    const modal = new bootstrap.Modal(modalEl);

                                    document.getElementById('btnTambahConfig').addEventListener('click', function(e){
                                        // open add modal
                                        document.getElementById('modalConfigTitle').innerText = 'Tambah Config Matrix';
                                        document.getElementById('matrix_id').value = '';
                                        // reset and initialize cleave
                                        document.getElementById('nominal').value = '';
                                        window.initCleaveRupiah();
                                        document.getElementById('approve_oleh').value = '';
                                        modal.show();
                                    });

                                    function formatNumberInput(v){
                                        // naive numeric clean
                                        return v.replace(/[^0-9\.\,]/g,'').replace(',','');
                                    }

                                    document.getElementById('saveConfigBtn').addEventListener('click', function(){
                                        const id = document.getElementById('matrix_id').value;
                                        // get raw numeric from cleave helper
                                        const nominal = window.getCleaveRawValue(document.getElementById('nominal')) || 0;
                                        const approve = document.getElementById('approve_oleh').value || '';

                                        const payload = { nominal: nominal, approve_oleh: approve };
                                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                                        let url = '/config-matrix-pinjaman';
                                        let method = 'POST';
                                        if(id){ url += '/' + id; method = 'PUT'; }

                                        fetch(url, {
                                            method: method,
                                            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':token },
                                            body: JSON.stringify(payload)
                                        }).then(r=>r.json()).then(res=>{
                                            if(res.success){ location.reload(); } else { alert('Validation error'); }
                                        }).catch(err=>{ console.error(err); alert('Error'); });
                                    });

                                    document.querySelectorAll('.btn-edit').forEach(btn=>{
                                        btn.addEventListener('click', function(e){
                                            e.preventDefault();
                                            const id = this.dataset.id;
                                            fetch('/config-matrix-pinjaman/' + id + '/edit').then(r=>r.json()).then(data=>{
                                                document.getElementById('modalConfigTitle').innerText = 'Edit Config Matrix';
                                                document.getElementById('matrix_id').value = data.id;
                                                // set formatted value and re-init cleave
                                                window.setCleaveValue(document.getElementById('nominal'), data.nominal);
                                                document.getElementById('approve_oleh').value = data.approve_oleh || '';
                                                modal.show();
                                            });
                                        });
                                    });

                                    // delete via confirmation modal
                                    const deleteConfirmEl = document.getElementById('confirmDeleteModal');
                                    let deleteTargetId = null;
                                    const deleteConfirmModal = deleteConfirmEl ? new bootstrap.Modal(deleteConfirmEl) : null;

                                    document.querySelectorAll('.btn-delete').forEach(btn=>{
                                        btn.addEventListener('click', function(e){
                                            e.preventDefault();
                                            deleteTargetId = this.dataset.id;
                                            if(deleteConfirmModal) deleteConfirmModal.show();
                                            else if(confirm('Yakin ingin menghapus data?')) {
                                                doDelete(this.dataset.id);
                                            }
                                        });
                                    });

                                    // handler when confirm delete in modal
                                    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                                    if(confirmDeleteBtn){
                                        confirmDeleteBtn.addEventListener('click', function(){
                                            if(!deleteTargetId) return;
                                            doDelete(deleteTargetId);
                                        });
                                    }

                                    function doDelete(id){
                                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                        fetch('/config-matrix-pinjaman/' + id, { method: 'DELETE', headers: {'X-CSRF-TOKEN': token} })
                                            .then(r=>r.json()).then(res=>{ if(res.success) location.reload(); else alert('Gagal hapus'); })
                                            .catch(()=>alert('Error'));
                                    }

                                })();
                            });
                        </script>
                        @endpush

                        <!-- Pagination -->
                        <div class="row mx-2 mt-3 mb-3">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_info">
                                    Menampilkan {{ count($data) }} dari {{ count($data) }} data
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
</div>
@endsection