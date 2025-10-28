    <!-- Modal Persetujuan Pinjaman -->
    <div class="modal fade" id="modalPersetujuanPinjaman" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Persetujuan Pinjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="my-2">
                <div class="modal-body">
                    <h5 class="mb-2">Apakah anda yakin menyetujui Pengajuan Pinjaman?</h5>
                    <p class="mb-0">Silahkan klik button hijau jika anda akan menyetujui Pengajuan Pinjaman, dan isi
                        perjanjian Kontrak terlebih dahulu. Pastikan dokumen yang diperlukan sudah sesuai!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btnKonfirmasiSetuju">
                        Setuju
                    </button>
                    <button type="button" class="btn btn-danger" id="btnTolakPinjaman">
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Pencairan Dana -->
    <div class="modal fade" id="modalPencairanDana" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Pencairan Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPencairanDana">
                    <div class="modal-body">
                        <!-- Card Data Nominal dan Tanggal -->
                        <div class="card border mb-3 shadow-none">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3" id="div-deviasi">
                                        <label class="form-label">Deviasi <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi"
                                                    id="deviasi_ya" value="ya" required>
                                                <label class="form-check-label" for="deviasi_ya">
                                                    Ya
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi"
                                                    id="deviasi_tidak" value="tidak" required>
                                                <label class="form-check-label" for="deviasi_tidak">
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nominalPengajuan" class="form-label">Nominal Pengajuan</label>
                                        <input type="text" class="form-control input-rupiah" id="nominalPengajuan"
                                            value="300000000" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="nominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" class="form-control input-rupiah" id="nominalDisetujui"
                                            placeholder="Rp 0" required>
                                        <div class="invalid-feedback">
                                            Silakan isi nominal yang disetujui.
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="flatpickr-tanggal-pencairan" class="form-label">Tanggal
                                            Pencairan</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control flatpickr-date-modal rounded-start"
                                                placeholder="DD/MM/YYYY" id="flatpickr-tanggal-pencairan" required>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">
                                            Silakan pilih tanggal pencairan.
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="flatpickr-tanggal-harapan" class="form-label">Tanggal Pencairan yang
                                            Diharapkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control rounded-start"
                                                placeholder="DD/MM/YYYY" id="flatpickr-tanggal-harapan"
                                                value="24/08/2024" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Catatan -->
                        <div class="card border shadow-none">
                            <div class="card-body">
                                <label for="catatanLainnya" class="form-label">Catatan Lainnya</label>
                                <textarea class="form-control" id="catatanLainnya" rows="4"
                                    placeholder="Berikan catatan tambahan jika diperlukan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Submit Pencairan Dana
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hasil Review (Penolakan) -->
    <div class="modal fade" id="modalHasilReview" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hasil Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formHasilReview">
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="hasilReview"
                                placeholder="Berikan catatan alasan penolakan" required>
                            <div class="invalid-feedback">
                                Silakan isi hasil review terlebih dahulu.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pencairan Dana -->
    <div class="modal fade" id="modalEditPencairan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Konfirmasi Pencairan Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPencairan">
                    <div class="modal-body">
                        <!-- Card Data Nominal dan Tanggal -->
                        <div class="card border mb-3 shadow-none">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="editNominalPengajuan" class="form-label">Nominal Pengajuan</label>
                                        <input type="text" class="form-control input-rupiah"
                                            id="editNominalPengajuan" value="300000000" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="editNominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" class="form-control input-rupiah"
                                            id="editNominalDisetujui" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="editTanggalPencairan" class="form-label">Tanggal Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="editTanggalPencairan" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="editTanggalHarapan" class="form-label">Tanggal Pencairan yang
                                            Diharapkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="editTanggalHarapan" value="24/08/2024" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Catatan - Only editable field -->
                        <div class="card border shadow-none">
                            <div class="card-body">
                                <label for="editCatatanLainnya" class="form-label">Catatan Lainnya</label>
                                <textarea class="form-control" id="editCatatanLainnya" rows="4"
                                    placeholder="Berikan catatan tambahan jika diperlukan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Tolak
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Terima
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Upload Dokumen -->
    <div class="modal fade" id="modalUploadDokumen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formUploadDokumen" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileUpload" class="form-label">Upload Dokumen Kontrak</label>
                            <input type="file" class="form-control" id="fileUpload" required>
                            <div class="invalid-feedback">
                                Silakan pilih file untuk diupload.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
