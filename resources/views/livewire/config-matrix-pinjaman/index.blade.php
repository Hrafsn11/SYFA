@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Config Matrix Nominal Peminjaman</h4>
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        id="btnTambahConfig">
                        <i class="fa-solid fa-plus"></i>
                        Tambah Data
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-datatable">
                        <livewire:config-matrix-pinjaman-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
{{-- Modal Tambah/Edit Config Matrix --}}
<div class="modal fade" id="modalConfigMatrix" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfigMatrixLabel">Tambah Config Matrix</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formConfigMatrix" novalidate>
                    <input type="hidden" id="editConfigId">
                    <div class="mb-3">
                        <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-rupiah" id="nominal" 
                            placeholder="Masukkan nominal" required data-format="rupiah">
                        <div class="invalid-feedback">Nominal wajib diisi</div>
                    </div>
                    <div class="mb-3">
                        <label for="approve_oleh" class="form-label">
                            Approve Oleh <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="approve_oleh"
                            placeholder="Masukkan nama approver" required>
                        <div class="invalid-feedback">Approve Oleh wajib diisi</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanConfig">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Confirm Delete --}}
<div class="modal fade" id="modalConfirmDeleteConfig" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus data config matrix ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteConfig">
                    <span class="spinner-border spinner-border-sm me-2 d-none" id="btnDeleteSpinner"></span>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const $modal = $('#modalConfigMatrix');
    const $modalDelete = $('#modalConfirmDeleteConfig');
    const $form = $('#formConfigMatrix');
    let deleteConfigId = null;

    // Button Tambah Config - Open empty modal
    $('#btnTambahConfig').on('click', function() {
        $form[0].reset();
        $form.removeClass('was-validated');
        $('#editConfigId').val('');
        $('#modalConfigMatrixLabel').text('Tambah Config Matrix');
        
        // Reset and initialize cleave for rupiah
        $('#nominal').val('');
        if (window.initCleaveRupiah) {
            window.initCleaveRupiah();
        }
        
        $modal.modal('show');
    });

    // Delegated Edit Handler - reads data-id from button rendered by Livewire
    $(document).on('click', '.config-edit-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id) { 
            return; 
        }
        
        $.ajax({
            url: `/config-matrix-pinjaman/${id}/edit`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const d = response.data;
                    $('#editConfigId').val(d.id_matrix_pinjaman);
                    
                    // Set nominal dengan cleave helper
                    if (window.setCleaveValue) {
                        window.setCleaveValue($('#nominal')[0], d.nominal);
                    } else {
                        $('#nominal').val(d.nominal);
                    }
                    
                    $('#approve_oleh').val(d.approve_oleh);
                    $('#modalConfigMatrixLabel').text('Edit Config Matrix');
                    $modal.modal('show');
                }
            },
            error: function(xhr) {
                console.error('Error loading data:', xhr);
            }
        });
    });

    // Delegated Delete Handler - reads data-id from button rendered by Livewire
    $(document).on('click', '.config-delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        if (!id) {
            return;
        }
        
        deleteConfigId = id;
        $modalDelete.modal('show');
    });

    // Save Handler (Create or Update)
    $('#btnSimpanConfig').on('click', function() {
        const editId = $('#editConfigId').val();
        
        // Get raw value from Cleave
        let nominal = 0;
        if (window.getCleaveRawValue) {
            nominal = window.getCleaveRawValue($('#nominal')[0]);
        } else {
            nominal = $('#nominal').val().replace(/[^0-9]/g, '');
        }
        
        const approve_oleh = $('#approve_oleh').val().trim();
        
        // Validation
        if (!nominal || nominal <= 0) {
            return;
        }
        
        if (!approve_oleh) {
            return;
        }
        
        const $btn = $(this);
        const $spinner = $('#btnSimpanSpinner');
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        const url = editId 
            ? `/config-matrix-pinjaman/${editId}` 
            : '/config-matrix-pinjaman';
        const method = editId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: {
                nominal: nominal,
                approve_oleh: approve_oleh,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $modal.modal('hide');
                    Livewire.dispatch('refreshConfigMatrixTable');
                }
            },
            error: function(xhr) {
                console.error('Error saving data:', xhr);
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
            }
        });
    });

    // Confirm Delete Handler
    $('#btnConfirmDeleteConfig').on('click', function() {
        if (!deleteConfigId) return;
        
        const $btn = $(this);
        const $spinner = $('#btnDeleteSpinner');
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        
        $.ajax({
            url: `/config-matrix-pinjaman/${deleteConfigId}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $modalDelete.modal('hide');
                    Livewire.dispatch('refreshConfigMatrixTable');
                }
            },
            error: function(xhr) {
                console.error('Error deleting data:', xhr);
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                deleteConfigId = null;
            }
        });
    });

    // Initialize Cleave for Rupiah if available
    if (window.initCleaveRupiah) {
        window.initCleaveRupiah();
    }
});
</script>
@endpush