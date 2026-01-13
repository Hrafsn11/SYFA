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
                        <!-- <input type="hidden" name="status" value="Dokumen Tervalidasi"> -->
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
                                            value="Rp {{ number_format(intval($peminjaman['nominal_pinjaman'] ?? 0), 0, ',', '.') }}" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="nominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" name="nominal_yang_disetujui" class="form-control input-rupiah" id="nominalDisetujui"
                                            placeholder="Rp 0" required>
                                        <div class="invalid-feedback">
                                            Silakan isi nominal yang disetujui.
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Bagi Hasil Disetujui & Persentase -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="persentaseBagiHasil" class="form-label">
                                            Persentase Bagi Hasil (%) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="persentase_bagi_hasil" class="form-control" id="persentaseBagiHasil"
                                            placeholder="2" min="0" max="100" step="0.01" value="{{ $peminjaman['persentase_bagi_hasil'] ?? 2 }}" required>
                                        <div class="form-text">
                                            Default: {{ $peminjaman['jenis_pembiayaan'] === 'Installment' ? '10%' : '2%' }}
                                        </div>
                                        <div class="form-text mt-1">
                                            <strong>Keterangan:</strong> <span id="keteranganBagiHasil" class="text-muted">-</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="totalBagiHasil" class="form-label">Total Bagi Hasil Disetujui (Auto)</label>
                                        <input type="text" class="form-control" id="totalBagiHasil" value="Rp 0" disabled>
                                        <input type="hidden" name="total_bagi_hasil" id="totalBagiHasilValue">
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="flatpickr-tanggal-harapan" class="form-label">Tanggal Pencairan yang
                                            Diharapkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control rounded-start"
                                                placeholder="DD/MM/YYYY" id="flatpickr-tanggal-harapan"
                                                value="{{ $peminjaman['harapan_tanggal_pencairan'] ? \Carbon\Carbon::parse($peminjaman['harapan_tanggal_pencairan'])->format('d/m/Y') : '' }}" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="flatpickr-tanggal-pencairan" class="form-label">Tanggal
                                            Pencairan</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control flatpickr-date-modal rounded-start" name="tanggal_pencairan"
                                                placeholder="DD/MM/YYYY" id="flatpickr-tanggal-pencairan" required>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                        <div class="invalid-feedback">
                                            Silakan pilih tanggal pencairan.
                                        </div>
                                    </div>

                                    
                                </div>
                            </div>
                        </div>

                        <!-- Card Catatan -->
                        <div class="card border shadow-none">
                            <div class="card-body">
                                <label for="catatanLainnya" class="form-label">Catatan Lainnya</label>
                                <textarea name="catatan_validasi_dokumen_disetujui" class="form-control" id="catatanLainnya" rows="4"
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
                            <label for="hasilReview" class="form-label">Catatan Penolakan <span class="text-danger">*</span></label>
                            <input type="text" name="catatan_validasi_dokumen_ditolak" class="form-control" id="hasilReview"
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
                        <button type="button" class="btn btn-success" data-status="Validasi Ditolak" onclick="approval(this)">
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
                    <h5 class="modal-title">Detail Konfirmasi Pencairan Dana</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPencairan">
                    <div class="modal-body">
                        <!-- Card Data Nominal dan Tanggal -->
                        <div class="card border mb-3 shadow-none">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Deviasi</label>
                                        <div class="d-flex gap-3 align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi" id="deviasiYa" value="Ya" disabled>
                                                <label class="form-check-label" for="deviasiYa">
                                                    <i class="ti ti-check-circle text-success me-1"></i>
                                                    Ya
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi" id="deviasiTidak" value="Tidak" disabled>
                                                <label class="form-check-label" for="deviasiTidak">
                                                    <i class="ti ti-x-circle text-danger me-1"></i>
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="editNominalPengajuan" class="form-label">Nominal Pengajuan</label>
                                        <input type="text" class="form-control"
                                            id="editNominalPengajuan" value="{{ isset($peminjaman['nominal_pinjaman']) && $peminjaman['nominal_pinjaman'] ? 'Rp ' . number_format($peminjaman['nominal_pinjaman'], 0, ',', '.') : 'Rp 0' }}" disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="editNominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" class="form-control input-rupiah"
                                            id="editNominalDisetujui" value="@if($peminjaman['nominal_yang_disetujui'])Rp {{ number_format(intval($peminjaman['nominal_yang_disetujui']), 0, ',', '.') }}@endif" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="editPersentaseBagiHasilDisetujui" class="form-label">Persentase Bagi Hasil yang Disetujui</label>
                                        <input type="text" class="form-control"
                                            id="editPersentaseBagiHasilDisetujui" value="{{ isset($peminjaman['persentase_bagi_hasil']) ? $peminjaman['persentase_bagi_hasil'] : 2 }}%" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="editTotalBagiHasilDisetujui" class="form-label">Total Bagi Hasil Disetujui</label>
                                        @php
                                            $totalBagiHasilEdit = 0;
                                            
                                            if (isset($peminjaman['total_bagi_hasil']) && $peminjaman['total_bagi_hasil'] > 0) {
                                                $totalBagiHasilEdit = intval($peminjaman['total_bagi_hasil']);
                                            } 
                                            elseif (isset($peminjaman['nominal_yang_disetujui']) && isset($peminjaman['persentase_bagi_hasil'])) {
                                                $nominal = intval($peminjaman['nominal_yang_disetujui']);
                                                $persentase = floatval($peminjaman['persentase_bagi_hasil']);
                                                if ($nominal > 0 && $persentase > 0) {
                                                    $totalBagiHasilEdit = $nominal * ($persentase / 100);
                                                }
                                            }
                                        @endphp
                                        <input type="text" class="form-control"
                                            id="editTotalBagiHasilDisetujui" value="Rp {{ number_format($totalBagiHasilEdit, 0, ',', '.') }}" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="editTanggalPencairan" class="form-label">Tanggal Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="editTanggalPencairan" value="{{ $peminjaman['tanggal_pencairan'] ? \Carbon\Carbon::parse($peminjaman['tanggal_pencairan'])->format('d/m/Y') : '' }}" disabled>
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
                                                id="editTanggalHarapan" value="{{ $peminjaman['harapan_tanggal_pencairan'] ? \Carbon\Carbon::parse($peminjaman['harapan_tanggal_pencairan'])->format('d/m/Y') : '' }}" disabled>
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
                                    placeholder="Berikan catatan tambahan jika diperlukan" disabled></textarea>
                            </div>
                        </div>
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

    <!-- Modal Persetujuan Debitur -->
    <div class="modal fade" id="modalPersetujuanDebitur" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Persetujuan Debitur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPersetujuanDebitur">
                    <input type="hidden" name="deviasi" value="{{ isset($latestHistory->deviasi) ? $latestHistory->deviasi : '' }}" id="hiddenDeviasiDebitur">
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="ti ti-info-circle me-2"></i>
                            Silakan berikan keputusan Anda terkait pengajuan peminjaman ini.
                        </div>
                        
                        <!-- Card Data Nominal dan Tanggal -->
                        <div class="card border mb-3 shadow-none">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Deviasi</label>
                                        <div class="d-flex gap-3 align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi_debitur" id="deviasiDebiturYa" value="Ya" 
                                                       {{ isset($latestHistory->deviasi) && strtolower($latestHistory->deviasi) == 'ya' ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="deviasiDebiturYa">
                                                    <i class="ti ti-check-circle text-success me-1"></i>
                                                    Ya
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi_debitur" id="deviasiDebiturTidak" value="Tidak" 
                                                       {{ isset($latestHistory->deviasi) && strtolower($latestHistory->deviasi) == 'tidak' ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="deviasiDebiturTidak">
                                                    <i class="ti ti-x-circle text-danger me-1"></i>
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="debiturNominalPengajuan" class="form-label">Nominal Pengajuan</label>
                                        <input type="text" class="form-control"
                                            id="debiturNominalPengajuan" value="{{ $peminjaman['nominal_pinjaman'] ? 'Rp ' . number_format(intval($peminjaman['nominal_pinjaman']), 0, ',', '.') : 'Rp 0' }}" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="debiturNominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" class="form-control"
                                            id="debiturNominalDisetujui" value="{{ isset($latestHistory) && $latestHistory['nominal_yang_disetujui'] ? 'Rp ' . number_format(intval($latestHistory['nominal_yang_disetujui']), 0, ',', '.') : 'Rp 0' }}" required readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="debiturPersentaseBagiHasilDisetujui" class="form-label">Persentase Bagi Hasil yang Disetujui</label>
                                        <input type="text" class="form-control"
                                            id="debiturPersentaseBagiHasilDisetujui" value="{{ isset($latestHistory) && $latestHistory['persentase_bagi_hasil'] ? $latestHistory['persentase_bagi_hasil'] : ($peminjaman['persentase_bagi_hasil'] ?? 2) }}%" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="debiturTotalBagiHasilDisetujui" class="form-label">Total Bagi Hasil Disetujui</label>
                                        @php
                                            $totalBagiHasilDisetujui = 0;
                                            
                                            if (isset($latestHistory) && isset($latestHistory['total_bagi_hasil']) && $latestHistory['total_bagi_hasil'] > 0) {
                                                $totalBagiHasilDisetujui = intval($latestHistory['total_bagi_hasil']);
                                            } 
                                            elseif (isset($latestHistory) && isset($latestHistory['nominal_yang_disetujui']) && isset($latestHistory['persentase_bagi_hasil'])) {
                                                $nominal = intval($latestHistory['nominal_yang_disetujui']);
                                                $persentase = floatval($latestHistory['persentase_bagi_hasil']);
                                                if ($nominal > 0 && $persentase > 0) {
                                                    $totalBagiHasilDisetujui = $nominal * ($persentase / 100);
                                                }
                                            }
                                            elseif (isset($peminjaman['total_bagi_hasil']) && $peminjaman['total_bagi_hasil'] > 0) {
                                                $totalBagiHasilDisetujui = intval($peminjaman['total_bagi_hasil']);
                                            }
                                        @endphp
                                        <input type="text" class="form-control"
                                            id="debiturTotalBagiHasilDisetujui" value="Rp {{ number_format($totalBagiHasilDisetujui, 0, ',', '.') }}" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="debiturTanggalPencairan" class="form-label">Tanggal Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="debiturTanggalPencairan" value="{{ $peminjaman['tanggal_pencairan'] ? \Carbon\Carbon::parse($peminjaman['tanggal_pencairan'])->format('d/m/Y') : '' }}" readonly>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="debiturTanggalHarapan" class="form-label">Tanggal Pencairan yang
                                            Diharapkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="debiturTanggalHarapan" value="{{ $peminjaman['harapan_tanggal_pencairan'] ? \Carbon\Carbon::parse($peminjaman['harapan_tanggal_pencairan'])->format('d/m/Y') : '' }}" disabled>
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
                                <label for="catatanPersetujuanDebitur" class="form-label">Catatan</label>
                                <textarea name="catatan_persetujuan_debitur" class="form-control" id="catatanPersetujuanDebitur" rows="4"
                                    placeholder="Berikan catatan terkait keputusan Anda" required></textarea>
                                <div class="invalid-feedback">
                                    Silakan isi catatan terlebih dahulu.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btnTolakDebitur">
                            <i class="ti ti-x me-2"></i>Tolak
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check me-2"></i>Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Persetujuan CEO SKI -->
    <div class="modal fade" id="modalPersetujuanCEO" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Persetujuan CEO SKI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPersetujuanCEO">
                    <input type="hidden" name="deviasi" value="{{ isset($latestHistory->deviasi) ? $latestHistory->deviasi : '' }}" id="hiddenDeviasiCEO">
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="ti ti-info-circle me-2"></i>
                            Silakan berikan keputusan CEO terkait pengajuan peminjaman ini.
                        </div>
                        
                        <!-- Card Data Nominal dan Tanggal -->
                        <div class="card border mb-3 shadow-none">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Deviasi</label>
                                        <div class="d-flex gap-3 align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi_ceo" id="deviasiCEOYa" value="Ya" 
                                                       {{ isset($latestHistory->deviasi) && strtolower($latestHistory->deviasi) == 'ya' ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="deviasiCEOYa">
                                                    <i class="ti ti-check-circle text-success me-1"></i>
                                                    Ya
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi_ceo" id="deviasiCEOTidak" value="Tidak" 
                                                       {{ isset($latestHistory->deviasi) && strtolower($latestHistory->deviasi) == 'tidak' ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="deviasiCEOTidak">
                                                    <i class="ti ti-x-circle text-danger me-1"></i>
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ceoNominalPengajuan" class="form-label">Nominal Pengajuan</label>
                                        <input type="text" class="form-control"
                                            id="ceoNominalPengajuan" value="{{ $peminjaman['nominal_pinjaman'] ? 'Rp ' . number_format(intval($peminjaman['nominal_pinjaman']), 0, ',', '.') : 'Rp 0' }}" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="ceoNominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" class="form-control"
                                            id="ceoNominalDisetujui" value="{{ isset($latestHistory) && $latestHistory['nominal_yang_disetujui'] ? 'Rp ' . number_format(intval($latestHistory['nominal_yang_disetujui']), 0, ',', '.') : 'Rp 0' }}" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ceoPersentaseBagiHasilDisetujui" class="form-label">Persentase Bagi Hasil yang Disetujui</label>
                                        <input type="text" class="form-control"
                                            id="ceoPersentaseBagiHasilDisetujui" value="{{ isset($latestHistory) && $latestHistory['persentase_bagi_hasil'] ? $latestHistory['persentase_bagi_hasil'] : ($peminjaman['persentase_bagi_hasil'] ?? 2) }}%" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="ceoTotalBagiHasilDisetujui" class="form-label">Total Bagi Hasil Disetujui</label>
                                        @php
                                            $totalBagiHasilCEO = 0;
                                            
                                            if (isset($latestHistory) && isset($latestHistory['total_bagi_hasil']) && $latestHistory['total_bagi_hasil'] > 0) {
                                                $totalBagiHasilCEO = intval($latestHistory['total_bagi_hasil']);
                                            } 
                                            elseif (isset($latestHistory) && isset($latestHistory['nominal_yang_disetujui']) && isset($latestHistory['persentase_bagi_hasil'])) {
                                                $nominal = intval($latestHistory['nominal_yang_disetujui']);
                                                $persentase = floatval($latestHistory['persentase_bagi_hasil']);
                                                if ($nominal > 0 && $persentase > 0) {
                                                    $totalBagiHasilCEO = $nominal * ($persentase / 100);
                                                }
                                            }
                                            elseif (isset($peminjaman['total_bagi_hasil']) && $peminjaman['total_bagi_hasil'] > 0) {
                                                $totalBagiHasilCEO = intval($peminjaman['total_bagi_hasil']);
                                            }
                                        @endphp
                                        <input type="text" class="form-control"
                                            id="ceoTotalBagiHasilDisetujui" value="Rp {{ number_format($totalBagiHasilCEO, 0, ',', '.') }}" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="ceoTanggalPencairan" class="form-label">Tanggal Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="ceoTanggalPencairan" value="{{ isset($latestHistory) && $latestHistory['tanggal_pencairan'] ? \Carbon\Carbon::parse($latestHistory['tanggal_pencairan'])->format('d/m/Y') : '' }}" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="ceoTanggalHarapan" class="form-label">Tanggal Pencairan yang
                                            Diharapkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="ceoTanggalHarapan" value="{{ $peminjaman['harapan_tanggal_pencairan'] ? \Carbon\Carbon::parse($peminjaman['harapan_tanggal_pencairan'])->format('d/m/Y') : '' }}" disabled>
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
                                <label for="catatanPersetujuanCEO" class="form-label">Catatan CEO</label>
                                <textarea name="catatan_persetujuan_ceo" class="form-control" id="catatanPersetujuanCEO" rows="4"
                                    placeholder="Berikan catatan terkait keputusan CEO" required></textarea>
                                <div class="invalid-feedback">
                                    Silakan isi catatan terlebih dahulu.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btnTolakCEO">
                            <i class="ti ti-x me-2"></i>Tolak
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check me-2"></i>Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Persetujuan Direktur SKI -->
    <div class="modal fade" id="modalPersetujuanDirektur" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Persetujuan Direktur SKI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPersetujuanDirektur">
                    <input type="hidden" name="deviasi" value="{{ isset($latestHistory->deviasi) ? $latestHistory->deviasi : '' }}" id="hiddenDeviasiDirektur">
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="ti ti-info-circle me-2"></i>
                            Silakan berikan keputusan Direktur terkait pengajuan peminjaman ini.
                        </div>
                        
                        <!-- Card Data Nominal dan Tanggal -->
                        <div class="card border mb-3 shadow-none">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Deviasi</label>
                                        <div class="d-flex gap-3 align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi_direktur" id="deviasiDirekturYa" value="Ya" 
                                                       {{ isset($latestHistory->deviasi) && strtolower($latestHistory->deviasi) == 'ya' ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="deviasiDirekturYa">
                                                    <i class="ti ti-check-circle text-success me-1"></i>
                                                    Ya
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="deviasi_direktur" id="deviasiDirekturTidak" value="Tidak" 
                                                       {{ isset($latestHistory->deviasi) && strtolower($latestHistory->deviasi) == 'tidak' ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="deviasiDirekturTidak">
                                                    <i class="ti ti-x-circle text-danger me-1"></i>
                                                    Tidak
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="direkturNominalPengajuan" class="form-label">Nominal Pengajuan</label>
                                        <input type="text" class="form-control"
                                            id="direkturNominalPengajuan" value="{{ $peminjaman['nominal_pinjaman'] ? 'Rp ' . number_format(intval($peminjaman['nominal_pinjaman']), 0, ',', '.') : 'Rp 0' }}" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="direkturNominalDisetujui" class="form-label">Nominal Disetujui</label>
                                        <input type="text" class="form-control"
                                            id="direkturNominalDisetujui" value="{{ isset($latestHistory) && $latestHistory['nominal_yang_disetujui'] ? 'Rp ' . number_format(intval($latestHistory['nominal_yang_disetujui']), 0, ',', '.') : 'Rp 0' }}" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="direkturPersentaseBagiHasilDisetujui" class="form-label">Persentase Bagi Hasil yang Disetujui</label>
                                        <input type="text" class="form-control"
                                            id="direkturPersentaseBagiHasilDisetujui" value="{{ isset($latestHistory) && $latestHistory['persentase_bagi_hasil'] ? $latestHistory['persentase_bagi_hasil'] : ($peminjaman['persentase_bagi_hasil'] ?? 2) }}%" disabled>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="direkturTotalBagiHasilDisetujui" class="form-label">Total Bagi Hasil Disetujui</label>
                                        @php
                                            $totalBagiHasilDirektur = 0;
                                            
                                            if (isset($latestHistory) && isset($latestHistory['total_bagi_hasil']) && $latestHistory['total_bagi_hasil'] > 0) {
                                                $totalBagiHasilDirektur = intval($latestHistory['total_bagi_hasil']);
                                            } 
                                            elseif (isset($latestHistory) && isset($latestHistory['nominal_yang_disetujui']) && isset($latestHistory['persentase_bagi_hasil'])) {
                                                $nominal = intval($latestHistory['nominal_yang_disetujui']);
                                                $persentase = floatval($latestHistory['persentase_bagi_hasil']);
                                                if ($nominal > 0 && $persentase > 0) {
                                                    $totalBagiHasilDirektur = $nominal * ($persentase / 100);
                                                }
                                            }
                                            elseif (isset($peminjaman['total_bagi_hasil']) && $peminjaman['total_bagi_hasil'] > 0) {
                                                $totalBagiHasilDirektur = intval($peminjaman['total_bagi_hasil']);
                                            }
                                        @endphp
                                        <input type="text" class="form-control"
                                            id="direkturTotalBagiHasilDisetujui" value="Rp {{ number_format($totalBagiHasilDirektur, 0, ',', '.') }}" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="direkturTanggalPencairan" class="form-label">Tanggal Pencairan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="direkturTanggalPencairan" value="{{ isset($latestHistory) && $latestHistory['tanggal_pencairan'] ? \Carbon\Carbon::parse($latestHistory['tanggal_pencairan'])->format('d/m/Y') : '' }}" disabled>
                                            <span class="input-group-text">
                                                <i class="ti ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="direkturTanggalHarapan" class="form-label">Tanggal Pencairan yang
                                            Diharapkan</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="DD/MM/YYYY"
                                                id="direkturTanggalHarapan" value="{{ $peminjaman['harapan_tanggal_pencairan'] ? \Carbon\Carbon::parse($peminjaman['harapan_tanggal_pencairan'])->format('d/m/Y') : '' }}" disabled>
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
                                <label for="catatanPersetujuanDirektur" class="form-label">Catatan Direktur</label>
                                <textarea name="catatan_persetujuan_direktur" class="form-control" id="catatanPersetujuanDirektur" rows="4"
                                    placeholder="Berikan catatan terkait keputusan Direktur" required></textarea>
                                <div class="invalid-feedback">
                                    Silakan isi catatan terlebih dahulu.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btnTolakDirektur">
                            <i class="ti ti-x me-2"></i>Tolak
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check me-2"></i>Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tolak Debitur (Step 8) -->
    <div class="modal fade" id="modalTolakDebitur" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        Tolak Konfirmasi Debitur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTolakDebitur">
                        <div class="mb-3">
                            <label for="catatanPenolakan" class="form-label">
                                Catatan Penolakan <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="catatanPenolakan" name="catatan_konfirmasi_debitur_ditolak" 
                                      rows="5" placeholder="Masukkan alasan penolakan..." required></textarea>
                            <div class="form-text">Jelaskan alasan penolakan konfirmasi debitur</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Batal
                    </button>
                    <button type="button" class="btn btn-danger" 
                            onclick="approvalWithNote(this)" 
                            data-status="Konfirmasi Ditolak Debitur" 
                            data-form="formTolakDebitur">
                        <i class="fas fa-check me-2"></i>
                        Tolak Konfirmasi
                    </button>
                </div>
            </div>
        </div>
    </div>
