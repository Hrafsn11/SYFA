<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">KOL</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3" data-bs-toggle="modal" data-bs-target="#modalTambahKOL" id="btnTambahKOL">
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
    {{-- Modal Tambah/Edit KOL --}}
    <div class="modal fade" id="modalTambahKOL" tabindex="-1" wire:ignore>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah KOL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit='saveData("master-data.kol.store", @json(["callback" => "afterAction"]))'>
                    <div class="modal-body">
                        <div class="mb-3 form-group">
                            <label for="kol" class="form-label">KOL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kol" placeholder="Masukkan KOL" wire:model.live="kol">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="persentase_keterlambatan" class="form-label">
                                Persentase Pencairan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="persentase_keterlambatan" placeholder="Masukkan Persentase Pencairan" wire:model.live="persentase_pencairan">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3 form-group">
                            <label for="tanggal_tenggat" class="form-label">
                                Jumlah Hari Keterlambatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tanggal_tenggat" placeholder="Masukkan Jumlah Hari Keterlambatan" wire:model.live="jmlh_hari_keterlambatan">
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

    {{-- Modal Confirm Delete --}}
    <div class="modal fade" id="modalConfirmDeleteKOL" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit='saveData("master-data.kol.store", @json([]))'>
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
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- script  --}}
<script>
    function afterAction(payload) {
        Livewire.dispatch('refreshKolTable');
        $('.modal').modal('hide');
    }

    function editData(payload) {
        const data = payload.data;
        
        const modal = $('#modalTambahKOL');
        const form = modal.find('form');

        form.attr('wire:submit', `saveData("master-data.kol.update", {"id":"id_placeholder", "callback":"afterAction"})`.replace('"id_placeholder"', '"' + data.id_kol + '"'));

        // ubah title modal
        modal.find('.modal-title').text('Edit KOL');
        // tampilkan modal
        modal.modal('show');

        @this.set('form_data', {
            kol: data.kol,
            persentase_pencairan: data.persentase_pencairan,
            jmlh_hari_keterlambatan: data.jmlh_hari_keterlambatan,
        });
    }

    $(document).on('click', '.kol-delete-btn', function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        sweetAlertConfirm({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus KOL ini? Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }, () => {
            @this.saveData("master-data.kol.destroy", {"id" : id, "callback" : "afterAction"});
        });
    });
</script>
@endpush