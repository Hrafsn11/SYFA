@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">KOL</h4>
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        id="btnTambahKOL">
                        <i class="fa-solid fa-plus"></i>
                        KOL
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable">
                        <livewire:kol-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
{{-- Modal Tambah/Edit KOL --}}
<div class="modal fade" id="modalTambahKOL" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahKOLLabel">Tambah KOL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahKOL" novalidate>
                    <input type="hidden" id="editKolId">
                    <div class="mb-3">
                        <label for="kol" class="form-label">KOL <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="kol" 
                            placeholder="Masukkan KOL" required min="1" step="1">
                        <div class="invalid-feedback">KOL wajib diisi</div>
                    </div>
                    <div class="mb-3">
                        <label for="persentase_keterlambatan" class="form-label">
                            Persentase Pencairan <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="persentase_keterlambatan"
                            placeholder="Masukkan Persentase Pencairan" required min="0" max="100" step="0.01">
                        <div class="invalid-feedback">Persentase wajib diisi (0-100)</div>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_tenggat" class="form-label">
                            Jumlah Hari Keterlambatan <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="tanggal_tenggat"
                            placeholder="Masukkan Jumlah Hari Keterlambatan" required min="0" step="1">
                        <div class="invalid-feedback">Jumlah hari wajib diisi</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanKOL">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirm Delete --}}
<div class="modal fade" id="modalConfirmDeleteKOL" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus KOL ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteKOL">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="btnDeleteSpinner"></span>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
    /* Rappasoft Table Styling */
    .card-datatable {
        padding: 1.5rem;
    }
    
    /* Header Styling */
    .card-datatable .table-light th {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 600;
        font-size: 0.9375rem;
        color: #566a7f;
        padding: 0.875rem 1.25rem;
    }
    
    /* Table Cell Styling */
    .card-datatable table tbody td {
        padding: 0.875rem 1.25rem;
        vertical-align: middle;
        font-size: 0.9375rem;
    }
    
    /* Search & Filter Controls */
    .card-datatable .form-control,
    .card-datatable .form-select {
        font-size: 0.9375rem;
    }
    
    /* Pagination Styling */
    .card-datatable nav {
        margin-top: 1rem;
    }
    
    /* Remove extra borders */
    .card-datatable table {
        border-collapse: collapse;
    }
    
    .card-datatable table td,
    .card-datatable table th {
        border: 1px solid #e7e7e7;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const $modal = $('#modalTambahKOL');
    const $modalDelete = $('#modalConfirmDeleteKOL');
    const $form = $('#formTambahKOL');
    let deleteKolId = null;

    // Button Tambah KOL - Open empty modal
    $('#btnTambahKOL').on('click', function() {
        $form[0].reset();
        $form.removeClass('was-validated');
        $('#editKolId').val('');
        $('#modalTambahKOLLabel').text('Tambah KOL');
        $modal.modal('show');
    });

    // Delegated Edit Handler - reads data-id from button rendered by Livewire
    $(document).on('click', '.kol-edit-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id) { 
            alert('ID tidak valid'); 
            return; 
        }
        
        $.ajax({
            url: `/master-data/kol/${id}/edit`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const d = response.data;
                    $('#editKolId').val(d.id_kol);
                    $('#kol').val(d.kol);
                    $('#persentase_keterlambatan').val(d.persentase_pencairan);
                    $('#tanggal_tenggat').val(d.jmlh_hari_keterlambatan);
                    $('#modalTambahKOLLabel').text('Edit KOL');
                    $modal.modal('show');
                }
            },
            error: function(xhr) {
                alert('Gagal mengambil data');
            }
        });
    });

    // Delegated Delete Handler - reads data-id from button rendered by Livewire
    $(document).on('click', '.kol-delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id) { 
            alert('ID tidak valid'); 
            return; 
        }
        
        deleteKolId = id;
        $modalDelete.modal('show');
    });

    // Submit form (Add or Edit)
    $('#btnSimpanKOL').on('click', function() {
        if (!$form[0].checkValidity()) {
            $form.addClass('was-validated');
            return;
        }

        const id = $('#editKolId').val();
        const formData = {
            kol: parseInt($('#kol').val()),
            persentase_pencairan: parseFloat($('#persentase_keterlambatan').val()) || 0,
            jmlh_hari_keterlambatan: parseInt($('#tanggal_tenggat').val()) || 0,
            _token: '{{ csrf_token() }}'
        };

        const url = id ? `/master-data/kol/${id}` : '/master-data/kol';
        const method = id ? 'PUT' : 'POST';

        $('#btnSimpanSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    $modal.modal('hide');
                    Livewire.dispatch('refreshKolTable');
                }
            },
            error: function(xhr) {
                alert('Gagal menyimpan data');
            },
            complete: function() {
                $('#btnSimpanSpinner').addClass('d-none');
                $('#btnSimpanKOL').prop('disabled', false);
            }
        });
    });

    // Confirm Delete
    $('#btnConfirmDeleteKOL').on('click', function() {
        if (!deleteKolId) return;

        $('#btnDeleteSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url: `/master-data/kol/${deleteKolId}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $modalDelete.modal('hide');
                    Livewire.dispatch('refreshKolTable');
                    deleteKolId = null;
                }
            },
            error: function(xhr) {
                alert('Gagal menghapus data');
            },
            complete: function() {
                $('#btnDeleteSpinner').addClass('d-none');
                $('#btnConfirmDeleteKOL').prop('disabled', false);
            }
        });
    });

    // Reset deleteKolId when modal closed
    $modalDelete.on('hidden.bs.modal', function() {
        deleteKolId = null;
    });
});
</script>
@endpush
