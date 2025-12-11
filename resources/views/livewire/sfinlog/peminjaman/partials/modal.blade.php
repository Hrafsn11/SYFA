<!-- Modal Submit Pengajuan -->
<div class="modal fade" id="modalSubmitPengajuan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Submit Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <hr class="my-2">
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin submit pengajuan peminjaman ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnKonfirmasiSubmit">
                    <i class="ti ti-send me-1"></i>
                    Ya, Submit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Validasi Investment Officer (Step 2) -->
<div class="modal fade" id="modalValidasiIO" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validasi Investment Officer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formValidasiIO">
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-info-circle me-2"></i>
                            <div>
                                <strong>Bagi Hasil yang Diajukan:</strong>
                                <span class="fw-bold">{{ $peminjaman->presentase_bagi_hasil ?? 0 }}%</span>
                                <span class="text-muted">(Rp {{ number_format($peminjaman->nilai_bagi_hasil ?? 0, 0, ',', '.') }})</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bagi Hasil Disetujui (%)<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="bagi_hasil_disetujui" name="bagi_hasil_disetujui" 
                               step="0.01" min="0" max="100" required>
                        <small class="text-muted">Masukkan persentase bagi hasil yang disetujui</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnTolakIO">
                        <i class="ti ti-x me-1"></i>
                        Tolak
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>
                        Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Alasan Penolakan IO -->
<div class="modal fade" id="modalAlasanPenolakanIO" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alasan Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAlasanPenolakanIO">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan_io" name="catatan_penolakan" rows="4" 
                                  placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-x me-1"></i>
                        Konfirmasi Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Persetujuan Debitur (Step 3) -->
<div class="modal fade" id="modalPersetujuanDebitur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan Debitur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <hr class="my-2">
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Bagi Hasil yang Disetujui:</strong> <span id="displayBagiHasil">{{ $peminjaman->histories()->latest()->first()->bagi_hasil_disetujui ?? 0 }}</span>%
                </div>
                <p class="mb-0">Apakah Anda menyetujui persentase bagi hasil tersebut?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnTolakDebitur">
                    <i class="ti ti-x me-1"></i>
                    Tolak
                </button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiDebitur">
                    <i class="ti ti-check me-1"></i>
                    Ya, Setuju
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alasan Penolakan Debitur -->
<div class="modal fade" id="modalAlasanPenolakanDebitur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alasan Penolakan Debitur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAlasanPenolakanDebitur">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan_debitur" name="catatan_penolakan" rows="4" 
                                  placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-x me-1"></i>
                        Konfirmasi Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Persetujuan SKI Finance (Step 4) -->
<div class="modal fade" id="modalPersetujuanSKIFinance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan SKI Finance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <hr class="my-2">
            <div class="modal-body">
                <p class="mb-0">Apakah Anda menyetujui pengajuan peminjaman ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnTolakSKIFinance">
                    <i class="ti ti-x me-1"></i>
                    Tolak
                </button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiSKIFinance">
                    <i class="ti ti-check me-1"></i>
                    Ya, Setuju
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alasan Penolakan SKI Finance -->
<div class="modal fade" id="modalAlasanPenolakanSKIFinance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alasan Penolakan SKI Finance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAlasanPenolakanSKIFinance">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan_ski" name="catatan_penolakan" rows="4" 
                                  placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-x me-1"></i>
                        Konfirmasi Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Persetujuan CEO Finlog (Step 5) -->
<div class="modal fade" id="modalPersetujuanCEOFinlog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan CEO Finlog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <hr class="my-2">
            <div class="modal-body">
                <p class="mb-0">Apakah Anda menyetujui pengajuan peminjaman ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnTolakCEOFinlog">
                    <i class="ti ti-x me-1"></i>
                    Tolak
                </button>
                <button type="button" class="btn btn-primary" id="btnKonfirmasiCEOFinlog">
                    <i class="ti ti-check me-1"></i>
                    Ya, Setuju
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alasan Penolakan CEO Finlog -->
<div class="modal fade" id="modalAlasanPenolakanCEOFinlog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alasan Penolakan CEO Finlog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAlasanPenolakanCEOFinlog">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="alasan_penolakan_ceo" name="catatan_penolakan" rows="4" 
                                  placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-x me-1"></i>
                        Konfirmasi Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Upload Bukti Transfer (Step 7) -->
<div class="modal fade" id="modalUploadBuktiTransfer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUploadBuktiTransfer">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Bukti Transfer<span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file_bukti_transfer" name="bukti_transfer" 
                               accept="image/*,application/pdf" required>
                        <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-upload me-1"></i>
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview Bukti Transfer -->
<div class="modal fade" id="modalPreviewBukti" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Bukti Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid d-none" alt="Bukti Transfer">
                <iframe id="previewPdf" src="" class="d-none" style="width:100%; height:500px;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="downloadBukti" class="btn btn-primary" download>
                    <i class="ti ti-download me-1"></i>
                    Download
                </a>
            </div>
        </div>
    </div>
</div>
