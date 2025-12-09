<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Cells Project</h4>
                @can('master_data.add')
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3" data-bs-toggle="modal" data-bs-target="#modalTambahCellsProject" id="btnTambahCellsProject">
                        <i class="fa-solid fa-plus"></i>
                        Cells Project
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable">
                    <livewire:cells-project-table/>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit Cells Project --}}
    <div class="modal fade" id="modalTambahCellsProject" wire:ignore>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahCellsProjectLabel">Tambah Cells Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit='{{ $urlAction['store_cells_project'] }}'>
                    <div class="modal-body">
                        <div class="mb-3 form-group">
                            <label for="nama_project" class="form-label">Nama Project <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_project" placeholder="Masukkan Nama Project" wire:model.blur="nama_project">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanCellsProject">
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
        Livewire.dispatch('refreshCellsProjectTable');
        $('.modal').modal('hide');
    }

    function editData(payload) {
        const data = payload.data;
        
        const modal = $('.modal');
        const form = modal.find('form');

        form.attr('wire:submit', `{!! $urlAction["update_cells_project"] !!}`.replace('id_placeholder', data.id_cells_project));

        // ubah title modal
        modal.find('.modal-title').text('Edit Cells Project');
        // tampilkan modal
        modal.modal('show');

        @this.set('id_cells_project', data.id_cells_project);
        @this.set('nama_project', data.nama_project);
    }

    $('.modal').on('hide.bs.modal', function() {
        $(this).find('form').attr('wire:submit', `{!! $urlAction["store_cells_project"] !!}`);
        $(this).find('.modal-title').text('Tambah Cells Project');
        @this.set('id_cells_project', null);
        @this.set('nama_project', '');
    });

    $(document).on('click', '.cells-project-delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        sweetAlertConfirm({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus Cells Project ini? Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }, () => {
            @this.saveData("master-data.cells-project.destroy", {"id" : id, "callback" : "afterAction"});
        });
    });
</script>
@endpush
