<div wire:ignore>
    {{-- Header Section --}}
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengajuan Restrukturisasi</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#modalRestrukturisasi" id="btnTambahRestrukturisasi">
                    <i class="fa-solid fa-plus"></i>
                    <span>Ajukan Restrukturisasi</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted text-center py-4">buat datatable nanti, <a href="{{ route('detail-restrukturisasi') }}" class="text-underline">liat contoh detail </a></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Restrukturisasi --}}
    <div class="modal fade" id="modalRestrukturisasi">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
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
                            <form id="formRestrukturisasi" wire:submit="saveData">

                                {{-- Step 1: Data Peminjaman --}}
                                <div id="stepDataPeminjaman" class="content">
                                    <div class="content-header mb-4 px-4 pt-4">
                                        <h6 class="mb-0">Identitas Debitur</h6>
                                        <small>Isi data identitas debitur</small>
                                    </div>
                                    <div class="row g-6 px-4 pb-4">
                                        <div class="col-md-6 form-group">
                                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                                            <input type="text" class="form-control" id="nama_perusahaan"
                                                name="nama_perusahaan" value="Techno Infinity" readonly>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="npwp" class="form-label">NPWP</label>
                                            <input type="number" class="form-control" id="npwp"
                                                name="npwp" value="12345678910" readonly>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="alamat_kantor" class="form-label">Alamat Kantor</label>
                                            <input type="text" class="form-control" id="alamat_kantor"
                                                name="alamat_kantor" value="Bandung, Jawa Barat" readonly>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="no_telepon" class="form-label">Nomor Telepon</label>
                                            <input type="number" class="form-control" id="no_telepon"
                                                name="no_telepon" value="085667788993" readonly>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="nama_pic" class="form-label">Nama PIC</label>
                                            <input type="text" class="form-control" id="nama_pic" name="nama_pic">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="jabatan_pic" class="form-label">Jabatan PIC</label>
                                            <input type="text" class="form-control" id="jabatan_pic" name="jabatan_pic">
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
                                                Pembiayaan<span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="nomor_kontrak_pembiayaan"
                                                wire:model.live="nomor_kontrak_pembiayaan"
                                                data-placeholder="Pilih Nomor Kontrak Pembiayaan">
                                                <option value="">Pilih Nomor Kontrak Pembiayaan</option>
                                                <option value="KONTRAK-001">KONTRAK-001</option>
                                                <option value="KONTRAK-002">KONTRAK-002</option>
                                                <option value="KONTRAK-003">KONTRAK-003</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="tanggal_akad" class="form-label">Tanggal Akad<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control bs-datepicker"
                                                    id="tanggal_akad" name="tanggal_akad"
                                                    wire:model.blur="tanggal_akad" placeholder="DD/MM/YYYY">
                                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 form-group">
                                            <label for="jenis_pembiayaan" class="form-label">Jenis Pembiayaan</label>
                                            <div class="row">
                                                <div class="col-sm mb-md-0 mb-5">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content"
                                                            for="customRadioTemp1">
                                                            <input name="customRadioTemp" class="form-check-input"
                                                                type="radio" value="" id="customRadioTemp1"
                                                                checked />
                                                            <span class="custom-option-header">
                                                                <span class="h6 mb-0">Invoice Financing</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content"
                                                            for="customRadioTemp2">
                                                            <input name="customRadioTemp" class="form-check-input"
                                                                type="radio" value=""
                                                                id="customRadioTemp2" />
                                                            <span class="custom-option-header">
                                                                <span class="h6 mb-0">PO Financing</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content"
                                                            for="customRadioTemp3">
                                                            <input name="customRadioTemp" class="form-check-input"
                                                                type="radio" value=""
                                                                id="customRadioTemp3" />
                                                            <span class="custom-option-header">
                                                                <span class="h6 mb-0">Installment</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-sm">
                                                    <div class="form-check custom-option custom-option-basic">
                                                        <label class="form-check-label custom-option-content"
                                                            for="customRadioTemp4">
                                                            <input name="customRadioTemp" class="form-check-input"
                                                                type="radio" value=""
                                                                id="customRadioTemp4" />
                                                            <span class="custom-option-header">
                                                                <span class="h6 mb-0">Factoring</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="jumlah_plafon_awal" class="form-label">Jumlah Plafon
                                                Awal</label>
                                            <input type="text" class="form-control input-rupiah"
                                                id="jumlah_plafon_awal" placeholder="Rp 0"
                                                wire:model.blur="jumlah_plafon_awal">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="sisa_pokok_yang_belum_dibayar" class="form-label">Sisa Pokok
                                                yang Belum Dibayar<span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control input-rupiah"
                                                id="sisa_pokok_yang_belum_dibayar" placeholder="Rp 0"
                                                wire:model.blur="sisa_pokok_yang_belum_dibayar" readonly>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="tunggakan_pokok" class="form-label">Tunggakan Pokok (jika
                                                ada)</label>
                                            <input type="text" class="form-control input-rupiah"
                                                id="tunggakan_pokok" placeholder="Rp 0" readonly>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="tunggakan_margin" class="form-label">Tunggakan Margin/Bunga
                                                (jika ada)</label>
                                            <input type="text" class="form-control input-rupiah"
                                                id="tunggakan_margin" placeholder="Rp 0" readonly>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="jatuh_tempo_terakhir" class="form-label">Jatuh Tempo Terakhir
                                                <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control bs-datepicker"
                                                    id="jatuh_tempo_terakhir" name="jatuh_tempo_terakhir"
                                                    wire:model.blur="jatuh_tempo_terakhir" placeholder="DD/MM/YYYY" disabled>
                                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="status_saat_ini" class="form-label">Status Saat Ini (DPD)
                                                <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="status_saat_ini"
                                                wire:model.blur="status_saat_ini" placeholder="30 Hari" readonly>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <hr class="my-3">


                                        <div class="col-md-12 form-group">
                                            <label for="alasan_restrukturisasi" class="form-label">Alasan
                                                Restrukturisasi <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="alasan_restrukturisasi" rows="3"
                                                placeholder="Jelaskan alasan mengapa diperlukan restrukturisasi..." wire:model.blur="alasan_restrukturisasi"></textarea>
                                            <div class="invalid-feedback"></div>
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
                                            <label for="permohonan_jenis_restrukturisasi"
                                                class="form-label">Permohonan Jenis Restrukturisasi <span
                                                    class="text-danger">*</span></label>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    name="opsi_restruktur[]" value="suku_bunga"
                                                    id="checkSukuBunga" />
                                                <label class="form-check-label" for="checkSukuBunga">
                                                    Penurunan suku bunga/margin
                                                </label>
                                            </div>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    name="opsi_restruktur[]" value="jangka_waktu"
                                                    id="checkJangkaWaktu" />
                                                <label class="form-check-label" for="checkJangkaWaktu">
                                                    Perpanjangan jangka waktu
                                                </label>
                                            </div>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    name="opsi_restruktur[]" value="tunggakan" id="checkTunggakan" />
                                                <label class="form-check-label" for="checkTunggakan">
                                                    Pengurangan tunggakan pokok/margin
                                                </label>
                                            </div>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    name="opsi_restruktur[]" value="grace_period"
                                                    id="checkGracePeriod" />
                                                <label class="form-check-label" for="checkGracePeriod">
                                                    Masa tenggang (grace period) selama _ bulan
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox"
                                                    name="opsi_restruktur[]" value="penjadwalan"
                                                    id="checkPenjadwalan" />
                                                <label class="form-check-label" for="checkPenjadwalan">
                                                    Penjadwalan ulang cicilan
                                                </label>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="opsi_restruktur[]" value="lainnya" id="checkLainnya" />
                                                    <label class="form-check-label" for="checkLainnya">
                                                        Lainnya:
                                                    </label>
                                                </div>

                                                <input type="text" class="form-control mt-2"
                                                    placeholder="Jelaskan keterangan lainnya..."
                                                    name="keterangan_lainnya" id="inputLainnya" disabled />
                                            </div>

                                            <hr class="my-3">

                                            <div class="col-md-12 form-group">
                                                <label for="rencana_pemulihan_usaha" class="form-label">Rencana
                                                    Pemulihan Usaha (Business Recovery Plan) <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control" id="rencana_pemulihan_usaha" rows="3"
                                                    placeholder="Jelaskan rencana mengenai pemulihan usaha..." wire:model.blur="rencana_pemulihan"></textarea>
                                                <div class="invalid-feedback"></div>
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
                                            <label for="fotocopy_ktp_pic" class="form-label">Fotocopy KTP PIC</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="fotocopy_ktp_pic" name="fotocopy_ktp_pic" />
                                                <label class="input-group-text" for="fotocopy_ktp_pic">Upload</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="fotokopi_npwp_perusahaan" class="form-label">Fotokopi NPWP Perusahaan</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="fotokopi_npwp_perusahaan" name="fotokopi_npwp_perusahaan" />
                                                <label class="input-group-text" for="fotokopi_npwp_perusahaan">Upload</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="laporan_keuangan_terbaru" class="form-label">Laporan Keuangan Terbaru</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="laporan_keuangan_terbaru" name="laporan_keuangan_terbaru" />
                                                <label class="input-group-text" for="laporan_keuangan_terbaru">Upload</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="rekap_arus_kas" class="form-label">Rekap Arus Kas Proyeksi 6-12 bulan</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="rekap_arus_kas" name="rekap_arus_kas" />
                                                <label class="input-group-text" for="rekap_arus_kas">Upload</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="bukti_kondisi_eksternal" class="form-label">Bukti kondisi eksternal</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="bukti_kondisi_eksternal" name="bukti_kondisi_eksternal" />
                                                <label class="input-group-text" for="bukti_kondisi_eksternal">Upload</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="salinan_kontrak_pembiayaan" class="form-label">Salinan kontrak pembiayaan</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="salinan_kontrak_pembiayaan" name="salinan_kontrak_pembiayaan" />
                                                <label class="input-group-text" for="salinan_kontrak_pembiayaan">Upload</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="dokumen_lain" class="form-label">Dokumen Lainnya</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="dokumen_lain" name="dokumen_lain" />
                                                <label class="input-group-text" for="dokumen_lain">Upload</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="tanda_tangan_perusahaan" class="form-label">Tanda tangan Perusahaan</label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="tanda_tangan_perusahaan" name="tanda_tangan_perusahaan" />
                                                <label class="input-group-text" for="tanda_tangan_perusahaan">Upload</label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="tempat" class="form-label">Tempat</label>
                                            <input type="text" class="form-control" id="tempat" name="tempat">
                                        </div>

                                        <div class="col-md-6 form-group">
                                            <label for="tanggal" class="form-label">Tanggal</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control bs-datepicker"
                                                    id="tanggal" name="tanggal"
                                                    wire:model.blur="tanggal" placeholder="DD/MM/YYYY">
                                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="col-12 form-check">
                                            <input type="checkbox" class="form-check-input"
                                                id="persetujuan" name="persetujuan" required />
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
                                            <button type="button" class="btn btn-success"
                                                id="btnSubmitRestrukturisasi">
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
</div>

@push('scripts')
    <script>
        let wizardRestrukturisasi;

        $(document).ready(function() {
            initWizardRestrukturisasi();
        });

        function initWizardRestrukturisasi() {
            const wizardElement = document.getElementById('wizardRestrukturisasi');
            if (wizardElement) {
                wizardRestrukturisasi = new Stepper(wizardElement, {
                    linear: false,
                    animation: true
                });

                wizardElement.addEventListener('shown.bs-stepper', function(event) {
                    if (event.detail.indexStep === 3) {
                        populateReviewData();
                    }
                });
            }
        }

        function validateCurrentStep() {
            return true;
        }

        function populateReviewData() {
            $('#review_kode_peminjaman').text($('#nomor_kontrak_pembiayaan option:selected').text() || '-');
            $('#review_nama_perusahaan').text($('#nama_perusahaan').val() || '-');
            $('#review_alasan').text($('#alasan_restrukturisasi').val() || '-');

            const jenisText = $('input[name="customRadioTemp"]:checked').next('.custom-option-header').find('.h6').text();
            $('#review_jenis').text(jenisText || '-');

            const dokumenList = [];
            const fileInputs = [{
                    id: 'fotocopy_ktp_pic',
                    label: 'Fotocopy KTP PIC'
                },
                {
                    id: 'fotokopi_npwp_perusahaan',
                    label: 'Fotokopi NPWP Perusahaan'
                },
                {
                    id: 'laporan_keuangan_terbaru',
                    label: 'Laporan Keuangan Terbaru'
                },
                {
                    id: 'rekap_arus_kas',
                    label: 'Rekap Arus Kas'
                }
            ];

            fileInputs.forEach(function(file) {
                const input = $(`#${file.id}`)[0];
                if (input && input.files.length > 0) {
                    dokumenList.push(
                        `<li><i class="ti ti-file-check text-success me-2"></i>${file.label}: ${input.files[0].name}</li>`
                    );
                }
            });

            if (dokumenList.length > 0) {
                $('#review_dokumen').html(dokumenList.join(''));
            } else {
                $('#review_dokumen').html('<li class="text-muted">Belum ada dokumen yang diupload</li>');
            }
        }

        function handleSubmit() {
            sweetAlertConfirm({
                title: 'Konfirmasi Pengajuan',
                text: 'Apakah Anda yakin ingin mengajukan restrukturisasi ini?',
                icon: 'warning',
                confirmButtonText: 'Ya, Ajukan',
                cancelButtonText: 'Batal',
            }, () => {
                // TODO: Integrate with Livewire
                // @this.saveData("route.name", {"data": formData, "callback": "afterAction"});

                showSweetAlert({
                    title: 'Berhasil!',
                    text: 'Pengajuan restrukturisasi berhasil disubmit',
                    icon: 'success'
                }).then(() => {
                    $('#modalRestrukturisasi').modal('hide');
                });
            });
        }

        function resetWizard() {
            // $('#formRestrukturisasi')[0].reset();

            if (wizardRestrukturisasi) {
                // wizardRestrukturisasi.reset();
                wizardRestrukturisasi.to(1);
            }
        }

        // Livewire callback function
        function afterAction(payload) {
            // Refresh datatable jika ada
            // Livewire.dispatch('refreshRestrukturisasiTable');
            $('#modalRestrukturisasi').modal('hide');
        }

        // Event handlers menggunakan event delegation
        $(document).on('click', '.btn-next', function() {
            if (validateCurrentStep()) {
                wizardRestrukturisasi.next();
            }
        });

        $(document).on('click', '.btn-prev', function() {
            wizardRestrukturisasi.previous();
        });

        $(document).on('click', '#btnSubmitRestrukturisasi', function(e) {
            e.preventDefault();
            handleSubmit();
        });

        $('#modalRestrukturisasi').on('shown.bs.modal', function() {
            initAllComponents();

            if (typeof window.initCleaveRupiah === 'function') {
                window.initCleaveRupiah();
            }

            // Initialize Bootstrap Datepicker
            $('.bs-datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom auto'
            });
        });

        $('#modalRestrukturisasi').on('hide.bs.modal', function() {
            resetWizard();
        });

        // Enable/disable "Lainnya" input based on checkbox
        $(document).on('change', '#checkLainnya', function() {
            $('#inputLainnya').prop('disabled', !this.checked);
        });
    </script>
@endpush
