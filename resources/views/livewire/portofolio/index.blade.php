<div wire:ignore>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Portofolio</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3" data-bs-toggle="modal" data-bs-target="#modalTambahKOL" id="btnTambahKOL">
                    <i class="fa-solid fa-plus"></i>
                    Portofolio
                </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable">
                    <livewire:portofolio.table />
                </div>
            </div>
        </div>
    </div>
    {{-- Modal Tambah/Edit --}}
    <div class="modal fade" id="modal-edit-porto">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah/Edit portofolio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit='{!! $urlAction["store_porto"] !!}'>
                    <div class="modal-body">
                        <div class="mb-3 form-group">
                            <label for="nama_sbu" class="form-label">Nama SBU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_sbu" placeholder="Masukkan Nama SBU" wire:model.blur="nama_sbu" disabled>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tahun" placeholder="Masukkan Persentase Pencairan" wire:model.blur="tahun">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="tahun" class="form-label">Tahun <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tahun" placeholder="Masukkan Persentase Pencairan" wire:model.blur="tahun">
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
{{-- script  --}}
<script>
    function afterAction(payload) {
        const action = payload.action;

        // tutup modal
        $('#modal-edit-porto').modal('hide');
    }

    function editData(payload) {
        const data = payload.data;
        
        const modal = $('#modal-edit-porto');
        const form = modal.find('form');

        // tampilkan modal
        modal.modal('show');

        @this.set('nama_sbu', data.nama_sbu);
        @this.set('tahun', data.tahun);
    }
</script>
@endpush