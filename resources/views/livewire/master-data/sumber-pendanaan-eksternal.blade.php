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
                    <h4>TOLO 2</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit Sumber Pendanaan --}}
    <div class="modal fade" id="modalTambahSumberPendanaan" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahSumberPendanaanLabel">Tambah Sumber Pendanaan Eksternal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahSumberPendanaan" novalidate>
                        <input type="hidden" id="editSumberId">
                        <div class="mb-3">
                            <label for="nama_instansi" class="form-label">Nama Instansi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_instansi" 
                                placeholder="Masukkan nama instansi" required>
                            <div class="invalid-feedback">Nama instansi wajib diisi</div>
                        </div>
                        <div class="mb-3">
                            <label for="persentase_bagi_hasil" class="form-label">
                                Persentase Bagi Hasil <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="persentase_bagi_hasil"
                                placeholder="Masukkan persentase bagi hasil" required min="0" max="100" step="1">
                            <div class="invalid-feedback">Persentase wajib diisi (0-100)</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanSumberPendanaan">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Confirm Delete --}}
    <div class="modal fade" id="modalConfirmDeleteSumber" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Apakah Anda yakin ingin menghapus Sumber Pendanaan ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteSumber">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="btnDeleteSpinner"></span>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>