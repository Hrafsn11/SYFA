<style>
    
    .jenis-pembiayaan-radio:disabled + label,
    .jenis-pembiayaan-radio:disabled ~ .custom-option-content {
        opacity: 0.6;
        pointer-events: none !important;
        cursor: not-allowed !important;
    }
    
    .custom-option.disabled {
        opacity: 0.6;
        pointer-events: none !important;
        cursor: not-allowed !important;
        background-color: #f5f5f9 !important;
    }
    
    .file-upload-wrapper {
        position: relative;
    }
    
    .file-upload-wrapper .invalid-feedback {
        display: block;
        margin-top: 0.25rem;
    }
</style>

<div class="modal fade" id="modalRestrukturisasi">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="modalRestrukturisasiTitle">Pengajuan Restrukturisasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <div class="bs-stepper wizard-numbered" id="wizardRestrukturisasi">
                    <div class="bs-stepper-header border-bottom mb-0 px-4 py-3">
                        <div class="step" data-target="#stepDataPeminjaman">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">1</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Identitas Debitur</span>
                                    <span class="bs-stepper-subtitle">Data Perusahaan & PIC</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="ti ti-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#stepRestrukturisasi">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">2</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Data Pembiayaan</span>
                                    <span class="bs-stepper-subtitle">Kontrak & Alasan Pengajuan</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="ti ti-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#stepDokumen">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">3</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Permohonan Restrukturisasi</span>
                                    <span class="bs-stepper-subtitle">Jenis & Rencana Pemulihan</span>
                                </span>
                            </button>
                        </div>
                        <div class="line">
                            <i class="ti ti-chevron-right"></i>
                        </div>
                        <div class="step" data-target="#stepReview">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle">4</span>
                                <span class="bs-stepper-label">
                                    <span class="bs-stepper-title">Dokumen Pendukung</span>
                                    <span class="bs-stepper-subtitle">Upload & Persetujuan</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Stepper Content --}}
                    <div class="bs-stepper-content">
                        <form id="formRestrukturisasi" method="POST" enctype="multipart/form-data"
                            onsubmit="return false;">
                            @csrf
                            <input type="hidden" name="id_debitur" value="{{ $debitur->id_debitur ?? '' }}">
                            <input type="hidden" name="nama_perusahaan" value="{{ $debitur->nama ?? '' }}">
                            <input type="hidden" name="npwp" value="{{ $debitur->npwp ?? '' }}">
                            <input type="hidden" name="alamat_kantor" value="{{ $debitur->alamat ?? '' }}">
                            <input type="hidden" name="nomor_telepon" value="{{ $debitur->no_telepon ?? '' }}">

                            {{-- Step 1: Data Peminjaman --}}
                            <div id="stepDataPeminjaman" class="content">
                                <div class="content-header mb-4 px-4 pt-4">
                                    <h6 class="mb-0">Identitas Debitur</h6>
                                    <small>Isi data identitas debitur</small>
                                </div>
                                <div class="row g-6 px-4 pb-4">
                                    <div class="col-md-6 form-group">
                                        <label for="nama_perusahaan" class="form-label">Nama Perusahaan <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('nama_perusahaan') is-invalid @enderror"
                                            id="nama_perusahaan" value="{{ $debitur->nama ?? '' }}" readonly>
                                        @error('nama_perusahaan')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="npwp" class="form-label">NPWP</label>
                                        <input type="text" class="form-control @error('npwp') is-invalid @enderror"
                                            id="npwp" value="{{ $debitur->npwp ?? '' }}" readonly>
                                        @error('npwp')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="alamat_kantor" class="form-label">Alamat Kantor</label>
                                        <input type="text"
                                            class="form-control @error('alamat_kantor') is-invalid @enderror"
                                            id="alamat_kantor" value="{{ $debitur->alamat ?? '' }}" readonly>
                                        @error('alamat_kantor')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                                        <input type="text"
                                            class="form-control @error('nomor_telepon') is-invalid @enderror"
                                            id="nomor_telepon" value="{{ $debitur->no_telepon ?? '' }}" readonly>
                                        @error('nomor_telepon')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="nama_pic" class="form-label">Nama PIC <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('nama_pic') is-invalid @enderror"
                                            id="nama_pic" name="nama_pic" placeholder="Masukkan nama PIC">
                                        @error('nama_pic')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="jabatan_pic" class="form-label">Jabatan PIC <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('jabatan_pic') is-invalid @enderror"
                                            id="jabatan_pic" name="jabatan_pic" placeholder="Masukkan jabatan PIC">
                                        @error('jabatan_pic')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-label-secondary" disabled>
                                            <i class="ti ti-arrow-left me-1"></i>
                                            <span class="align-middle">Sebelumnya</span>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-next">
                                            <span class="align-middle me-1">Selanjutnya</span>
                                            <i class="ti ti-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 2: Detail Restrukturisasi --}}
                            <div id="stepRestrukturisasi" class="content">
                                <div class="content-header mb-4 px-4 pt-4">
                                    <h6 class="mb-0">Data Pembiayaan & Alasan Pengajuan</h6>
                                    <small>Isi data pembiayaan dan alasan pengajuan restrukturisasi</small>
                                </div>
                                <div class="row g-6 px-4 pb-4">
                                    <div class="col-md-6 form-group">
                                        <label for="nomor_kontrak_pembiayaan" class="form-label">Nomor Kontrak
                                            Pembiayaan <span class="text-danger">*</span>
                                        </label>

                                        <input type="hidden" name="id_pengajuan_peminjaman"
                                            id="id_pengajuan_peminjaman" value="">
                                        <input type="hidden" name="nomor_kontrak_pembiayaan"
                                            id="nomor_kontrak_pembiayaan_value" value="">

                                        <select
                                            class="form-control select2 @error('nomor_kontrak_pembiayaan') is-invalid @enderror"
                                            id="nomor_kontrak_pembiayaan"
                                            data-placeholder="Pilih Nomor Kontrak Pembiayaan">
                                            <option value="">Pilih Nomor Kontrak Pembiayaan</option>
                                            @foreach ($peminjamanList as $peminjaman)
                                                <option value="{{ $peminjaman->id_pengajuan_peminjaman }}">
                                                    {{ $peminjaman->nomor_peminjaman }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('nomor_kontrak_pembiayaan')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                        @error('id_pengajuan_peminjaman')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="tanggal_akad" class="form-label">Tanggal Akad <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('tanggal_akad') is-invalid @enderror"
                                                id="tanggal_akad" name="tanggal_akad" placeholder="yyyy-mm-dd">
                                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        </div>
                                        @error('tanggal_akad')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 form-group">
                                        <label class="form-label">Jenis Pembiayaan <span
                                                class="text-danger">*</span></label>
                                        <div class="row" id="jenis-pembiayaan-wrapper">
                                            @foreach (['Invoice Financing', 'PO Financing', 'Installment', 'Factoring'] as $jenis)
                                                <div class="col-sm mb-md-0 mb-2">
                                                    <div class="form-check custom-option custom-option-basic disabled" 
                                                         id="option_{{ str_replace(' ', '_', strtolower($jenis)) }}">
                                                        <label class="form-check-label custom-option-content"
                                                            for="jenis_{{ str_replace(' ', '_', strtolower($jenis)) }}">
                                                            <input name="jenis_pembiayaan_radio"
                                                                class="form-check-input jenis-pembiayaan-radio"
                                                                type="radio" value="{{ $jenis }}"
                                                                id="jenis_{{ str_replace(' ', '_', strtolower($jenis)) }}"
                                                                disabled />
                                                            <span class="custom-option-header">
                                                                <span class="h6 mb-0">{{ $jenis }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="text-muted">Jenis pembiayaan akan terisi otomatis saat Anda
                                            memilih Nomor Kontrak Pembiayaan</small>
                                        @error('jenis_pembiayaan')
                                            <span class="text-danger d-block mt-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="jumlah_plafon_awal" class="form-label">Jumlah Plafon Awal</label>
                                        <input type="text"
                                            class="form-control @error('jumlah_plafon_awal') is-invalid @enderror"
                                            id="jumlah_plafon_awal" placeholder="Rp 0" readonly>
                                        @error('jumlah_plafon_awal')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="sisa_pokok_belum_dibayar" class="form-label">Sisa Pokok yang Belum
                                            Dibayar <span class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('sisa_pokok_belum_dibayar') is-invalid @enderror"
                                            id="sisa_pokok_belum_dibayar" placeholder="Rp 0" readonly>
                                        @error('sisa_pokok_belum_dibayar')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="tunggakan_margin_bunga" class="form-label">Tunggakan Margin/Bunga</label>
                                        <input type="text"
                                            class="form-control @error('tunggakan_margin_bunga') is-invalid @enderror"
                                            id="tunggakan_margin_bunga" name="tunggakan_margin_bunga"
                                            placeholder="Rp 0" readonly style="background-color: #f8f9fa;">
                                        @error('tunggakan_margin_bunga')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="jatuh_tempo_terakhir" class="form-label">Jatuh Tempo
                                            Terakhir</label>
                                        <input type="text"
                                            class="form-control @error('jatuh_tempo_terakhir') is-invalid @enderror"
                                            id="jatuh_tempo_terakhir" name="jatuh_tempo_terakhir"
                                            placeholder="-" readonly style="background-color: #f8f9fa;">
                                        <input type="hidden" id="jatuh_tempo_terakhir_value" name="jatuh_tempo_terakhir_value">
                                        @error('jatuh_tempo_terakhir')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 form-group">
                                        <label for="status_dpd" class="form-label">Status Saat Ini (DPD)</label>
                                        <div class="input-group">
                                            <input type="number"
                                                class="form-control @error('status_dpd') is-invalid @enderror"
                                                id="status_dpd" name="status_dpd" 
                                                placeholder="0 Hari"
                                                readonly
                                                style="background-color: #f5f5f9;">
                                            <span class="input-group-text">Hari</span>
                                        </div>
                                        @error('status_dpd')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <hr class="my-3">

                                    <div class="col-md-12 form-group">
                                        <label for="alasan_restrukturisasi" class="form-label">Alasan Restrukturisasi
                                            <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('alasan_restrukturisasi') is-invalid @enderror" id="alasan_restrukturisasi"
                                            name="alasan_restrukturisasi" rows="3" placeholder="Jelaskan alasan mengapa diperlukan restrukturisasi..."></textarea>
                                        @error('alasan_restrukturisasi')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-label-secondary btn-prev">
                                            <i class="ti ti-arrow-left me-1"></i>
                                            <span class="align-middle">Sebelumnya</span>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-next">
                                            <span class="align-middle me-1">Selanjutnya</span>
                                            <i class="ti ti-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 3: Dokumen Pendukung --}}
                            <div id="stepDokumen" class="content">
                                <div class="content-header mb-4 px-4 pt-4">
                                    <h6 class="mb-0">Permohonan Jenis & Rencana Pemulihan Usaha</h6>
                                    <small>Isi jenis permohonan dan rencana pemulihan usaha</small>
                                </div>
                                <div class="row g-6 px-4 pb-4">
                                    <div class="col-12">
                                        <label class="form-label">Permohonan Jenis Restrukturisasi <span
                                                class="text-danger">*</span></label>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                name="jenis_restrukturisasi[]" value="Penurunan suku bunga/margin"
                                                id="checkSukuBunga" />
                                            <label class="form-check-label" for="checkSukuBunga">
                                                Penurunan suku bunga/margin
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                name="jenis_restrukturisasi[]" value="Perpanjangan jangka waktu"
                                                id="checkJangkaWaktu" />
                                            <label class="form-check-label" for="checkJangkaWaktu">
                                                Perpanjangan jangka waktu
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                name="jenis_restrukturisasi[]"
                                                value="Pengurangan tunggakan pokok/margin" id="checkTunggakan" />
                                            <label class="form-check-label" for="checkTunggakan">
                                                Pengurangan tunggakan pokok/margin
                                            </label>
                                        </div>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                name="jenis_restrukturisasi[]" value="Masa tenggang (grace period)"
                                                id="checkGracePeriod" />
                                            <label class="form-check-label" for="checkGracePeriod">
                                                Masa tenggang (grace period) selama _ bulan
                                            </label>
                                        </div>

                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox"
                                                name="jenis_restrukturisasi[]" value="Penjadwalan ulang cicilan"
                                                id="checkPenjadwalan" />
                                            <label class="form-check-label" for="checkPenjadwalan">
                                                Penjadwalan ulang cicilan
                                            </label>
                                        </div>

                                        <div class="mb-3">
                                            <label for="inputLainnya" class="form-label">Keterangan Tambahan (Opsional)</label>
                                            <input type="text"
                                                class="form-control @error('jenis_restrukturisasi_lainnya') is-invalid @enderror"
                                                placeholder="Jelaskan keterangan tambahan jika ada..."
                                                name="jenis_restrukturisasi_lainnya" id="inputLainnya" />
                                            @error('jenis_restrukturisasi_lainnya')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        @error('jenis_restrukturisasi')
                                            <span class="text-danger d-block mb-3">{{ $message }}</span>
                                        @enderror

                                        <hr class="my-3">

                                        <div class="col-md-12 form-group">
                                            <label for="rencana_pemulihan_usaha" class="form-label">Rencana Pemulihan
                                                Usaha (Business Recovery Plan) <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control @error('rencana_pemulihan_usaha') is-invalid @enderror" id="rencana_pemulihan_usaha"
                                                name="rencana_pemulihan_usaha" rows="3" placeholder="Jelaskan rencana mengenai pemulihan usaha..."></textarea>
                                            @error('rencana_pemulihan_usaha')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col-12 d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-label-secondary btn-prev">
                                            <i class="ti ti-arrow-left me-1"></i>
                                            <span class="align-middle">Sebelumnya</span>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-next">
                                            <span class="align-middle me-1">Selanjutnya</span>
                                            <i class="ti ti-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 4: Review & Submit --}}
                            <div id="stepReview" class="content">
                                <div class="content-header mb-4 px-4 pt-4">
                                    <h6 class="mb-0">Dokumen Pendukung</h6>
                                    <small>Upload dokumen pendukung yang diperlukan</small>
                                </div>
                                <div class="row g-6 px-4 pb-4">

                                    <div class="col-md-6 form-group">
                                        <label for="dokumen_ktp_pic" class="form-label">
                                            Fotocopy KTP PIC <span class="text-danger">*</span>
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('dokumen_ktp_pic') is-invalid @enderror"
                                                    id="dokumen_ktp_pic" name="dokumen_ktp_pic" 
                                                    accept=".pdf,.jpg,.jpeg,.png" />
                                                <label class="input-group-text" for="dokumen_ktp_pic">Upload</label>
                                            </div>
                                            @error('dokumen_ktp_pic')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="dokumen_npwp_perusahaan" class="form-label">
                                            Fotokopi NPWP Perusahaan <span class="text-danger">*</span>
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('dokumen_npwp_perusahaan') is-invalid @enderror"
                                                    id="dokumen_npwp_perusahaan" name="dokumen_npwp_perusahaan" 
                                                    accept=".pdf,.jpg,.jpeg,.png" />
                                                <label class="input-group-text"
                                                    for="dokumen_npwp_perusahaan">Upload</label>
                                            </div>
                                            @error('dokumen_npwp_perusahaan')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="dokumen_laporan_keuangan" class="form-label">Laporan Keuangan
                                            Terbaru <span class="text-danger">*</span>
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('dokumen_laporan_keuangan') is-invalid @enderror"
                                                    id="dokumen_laporan_keuangan" name="dokumen_laporan_keuangan" 
                                                    accept=".pdf,.xlsx,.xls" />
                                                <label class="input-group-text"
                                                    for="dokumen_laporan_keuangan">Upload</label>
                                            </div>
                                            @error('dokumen_laporan_keuangan')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: PDF, XLSX, XLS (Max: 5MB)</small>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="dokumen_arus_kas" class="form-label">Rekap Arus Kas Proyeksi 6-12
                                            bulan <span class="text-danger">*</span>
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('dokumen_arus_kas') is-invalid @enderror"
                                                    id="dokumen_arus_kas" name="dokumen_arus_kas" 
                                                    accept=".pdf,.xlsx,.xls" />
                                                <label class="input-group-text" for="dokumen_arus_kas">Upload</label>
                                            </div>
                                            @error('dokumen_arus_kas')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: PDF, XLSX, XLS (Max: 5MB)</small>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="dokumen_kondisi_eksternal" class="form-label">Bukti kondisi
                                            eksternal <span class="text-danger">*</span>
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('dokumen_kondisi_eksternal') is-invalid @enderror"
                                                    id="dokumen_kondisi_eksternal" name="dokumen_kondisi_eksternal" 
                                                    accept=".pdf,.jpg,.jpeg,.png" />
                                                <label class="input-group-text"
                                                    for="dokumen_kondisi_eksternal">Upload</label>
                                            </div>
                                            @error('dokumen_kondisi_eksternal')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="dokumen_kontrak_pembiayaan" class="form-label">Salinan kontrak
                                            pembiayaan <span class="text-danger">*</span>
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('dokumen_kontrak_pembiayaan') is-invalid @enderror"
                                                    id="dokumen_kontrak_pembiayaan" name="dokumen_kontrak_pembiayaan" 
                                                    accept=".pdf" />
                                                <label class="input-group-text"
                                                    for="dokumen_kontrak_pembiayaan">Upload</label>
                                            </div>
                                            @error('dokumen_kontrak_pembiayaan')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: PDF (Max: 5MB)</small>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="dokumen_lainnya" class="form-label">Dokumen Lainnya</label>
                                        <div class="file-upload-wrapper">
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('dokumen_lainnya') is-invalid @enderror"
                                                    id="dokumen_lainnya" name="dokumen_lainnya" 
                                                    accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls" />
                                                <label class="input-group-text" for="dokumen_lainnya">Upload</label>
                                            </div>
                                            @error('dokumen_lainnya')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: PDF, JPG, PNG, XLSX, XLS (Max: 5MB)</small>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="dokumen_tanda_tangan" class="form-label">Tanda tangan
                                            Perusahaan <span class="text-danger">*</span>
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('dokumen_tanda_tangan') is-invalid @enderror"
                                                    id="dokumen_tanda_tangan" name="dokumen_tanda_tangan" 
                                                    accept=".jpg,.jpeg,.png" />
                                                <label class="input-group-text" for="dokumen_tanda_tangan">Upload</label>
                                            </div>
                                            @error('dokumen_tanda_tangan')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="tempat" class="form-label">Tempat</label>
                                        <input type="text"
                                            class="form-control @error('tempat') is-invalid @enderror" id="tempat"
                                            name="tempat" placeholder="Masukkan tempat">
                                        @error('tempat')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <div class="input-group">
                                            <input type="text"
                                                class="form-control @error('tanggal') is-invalid @enderror"
                                                id="tanggal" name="tanggal" placeholder="yyyy-mm-dd">
                                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                        </div>
                                        @error('tanggal')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-12 form-check">
                                        <input type="checkbox" class="form-check-input" id="persetujuan" required />
                                        <label class="form-check-label" for="persetujuan">
                                            Saya menyatakan bahwa informasi yang saya berikan dalam formulir ini
                                            adalah benar dan lengkap. Saya memahami bahwa pemberian restrukturisasi
                                            tunduk pada evaluasi dan keputusan Perusahaan Pembiayaan sesuai
                                            kebijakan internal dan ketentuan OJK.
                                        </label>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-label-secondary btn-prev">
                                            <i class="ti ti-arrow-left me-1"></i>
                                            <span class="align-middle">Sebelumnya</span>
                                        </button>
                                        <button type="button" class="btn btn-success" id="btnSubmitRestrukturisasi">
                                            <i class="ti ti-check me-1"></i>
                                            <span class="align-middle">Submit Pengajuan</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
