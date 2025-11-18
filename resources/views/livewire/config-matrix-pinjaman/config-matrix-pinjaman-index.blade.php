<div wire:ignore>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Config Matrix Nominal Peminjaman</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3" data-bs-toggle="modal" data-bs-target="#modalConfigMatrix" id="btnTambahConfig">
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
                    <livewire:config-matrix-pinjaman.table />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfigMatrix" tabindex="-1" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfigMatrixLabel">Tambah Config Matrix</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit='{{ $urlAction['store_config'] }}'>
                    <div class="modal-body">
                        <div class="mb-3 form-group">
                            <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control input-rupiah" id="nominal" placeholder="Masukkan nominal" data-format="rupiah" wire:model.blur="nominal">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="approve_oleh" class="form-label">Approve Oleh <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="approve_oleh" placeholder="Masukkan nama approver" wire:model.blur="approve_oleh">
                            <div class="invalid-feedback"></div>
                        </div>    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm me-2" wire:loading wire:target="saveData"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function afterAction(payload) {
        Livewire.dispatch('refreshConfigMatrixTable');
        $('.modal').modal('hide');
    }

    function editData(payload) {
        const data = payload.data;
        
        const modal = $('#modalConfigMatrix');
        const form = modal.find('form');

        form.attr('wire:submit', `{!! $urlAction["update_config"] !!}`.replace('id_placeholder', data.id_matrix_pinjaman));

        // ubah title modal
        modal.find('.modal-title').text('Edit KOL');
        // tampilkan modal
        modal.modal('show');

        @this.set('nominal', data.nominal);
        @this.set('approve_oleh', data.approve_oleh);
    }

    $('.modal').on('hide.bs.modal', function() {
        $(this).find('form').attr('wire:submit', `{!! $urlAction["store_config"] !!}`);
        $(this).find('.modal-title').text('Tambah KOL');
    });

    $(document).on('click', '.config-delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        sweetAlertConfirm({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus data config matrix ini? Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }, () => {
            @this.saveData("config-matrix-pinjaman.destroy", {"id" : id, "callback" : "afterAction"});
        });
    });
</script>
@endpush