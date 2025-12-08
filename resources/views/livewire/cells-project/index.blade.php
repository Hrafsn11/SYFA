<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Cells Project</h4>
                @can('master_data.add')
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        data-bs-toggle="modal" data-bs-target="#modalTambahCellsProject" id="btnTambahCellsProject">
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
                    <h4 class="text-center fw-bold">Data Cells Project</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit Sumber Pendanaan --}}
    <div class="modal fade" id="modalTambahCellsProject" wire:ignore>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahCellsProjectLabel">Tambah Cells Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit='#'>
                    <div class="modal-body">
                        <div class="mb-3 form-group">
                            <label for="nama_project" class="form-label">Nama Project</label>
                            <input type="text" class="form-control" id="nama_project"
                                placeholder="Masukkan nama project" wire:model.blur="nama_project">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanCellsProject">
                            <span class="spinner-border spinner-border-sm me-2" wire:loading
                                wire:target="saveData"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
