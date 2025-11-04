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
    <div class="modal fade" id="modalTambahKOL" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahKOLLabel">Tambah KOL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit="saveData('master-data.kol.store', 'afterAction')">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kol" class="form-label">KOL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kol" placeholder="Masukkan KOL" wire:model.blur="form_data.kol">
                            @error('form_data.kol')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="persentase_keterlambatan" class="form-label">
                                Persentase Pencairan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="persentase_keterlambatan" placeholder="Masukkan Persentase Pencairan" wire:model.blur="form_data.persentase_pencairan">
                            @error('form_data.persentase_pencairan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_tenggat" class="form-label">
                                Jumlah Hari Keterlambatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="tanggal_tenggat" placeholder="Masukkan Jumlah Hari Keterlambatan" wire:model.blur="form_data.jmlh_hari_keterlambatan">
                            @error('form_data.jmlh_hari_keterlambatan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
    <livewire:universal-form-action />
</div>

@push('scripts')
{{-- script  --}}
<script>
    function afterAction(payload) {
        console.log('masuk');
        console.log(payload);
        
        // modal harus di close setelah action selesai terlebih dahulu
        // $('.modal').modal('hide');
        // baru jalankan action lain
        // Livewire.dispatch('refreshKolTable');
    }
</script>
@endpush