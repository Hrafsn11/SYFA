<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Sumber Pendanaan Eksternal</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    id="btnTambahSumberPendanaan">
                    <i class="fa-solid fa-plus"></i>
                    Sumber Pendanaan
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-datatable">
                    <livewire:sumber-pendanaan-eksternal-table />
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit Sumber Pendanaan --}}
    <div class="modal fade" id="modalTambahSumberPendanaan" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahSumberPendanaanLabel">Tambah Sumber Pendanaan Eksternal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit='{{ $urlAction['store_pendanaan'] }}'>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_instansi" class="form-label">Nama Instansi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_instansi" placeholder="Masukkan nama instansi" wire:model.live="nama_instansi">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="persentase_bagi_hasil" class="form-label">Persentase Bagi Hasil <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="persentase_bagi_hasil" placeholder="Masukkan persentase bagi hasil" wire:model.live="persentase_bagi_hasil">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="btnSimpanSumberPendanaan">
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
        Livewire.dispatch('refreshSumberPendanaanEksternalTable');
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
            nama_instansi: data.nama_instansi,
            persentase_bagi_hasil: data.persentase_bagi_hasil
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