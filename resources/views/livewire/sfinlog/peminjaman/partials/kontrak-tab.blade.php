<div id="content-kontrak">

    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2 mt-3">
        <h5 class="mb-0">Detail Kontrak</h5>
    </div>

    <hr class="my-3 my-md-4">

    @if($currentStep >= 6 && $peminjaman->nomor_kontrak)
        <h6 class="text-dark mb-3">Informasi Kontrak</h6>
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nomor Kontrak</small>
                    <p class="fw-bold mb-0">{{ $peminjaman->nomor_kontrak }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Jenis Pembiayaan</small>
                    <p class="fw-bold mb-0">Pembiayaan Project</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nama Debitur</small>
                    <p class="fw-bold mb-0">{{ $peminjaman->debitur->nama ?? '-' }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nilai Pembiayaan</small>
                    <p class="fw-bold mb-0">Rp {{ number_format($peminjaman->nilai_pinjaman ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Biaya Administrasi</small>
                    <p class="fw-bold mb-0">Rp {{ number_format($peminjaman->biaya_administrasi ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Status Kontrak</small>
                    <span class="badge bg-success">Aktif</span>
                </div>
            </div>
        </div>

        <hr class="my-3 my-md-4">

        <!-- Button Preview Kontrak -->
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('sfinlog.peminjaman.show-kontrak', $peminjaman->id_peminjaman_finlog) }}" 
               target="_blank" 
               class="btn btn-info">
                <i class="ti ti-eye me-1"></i>
                Preview Kontrak
            </a>
        </div>

    @elseif($currentStep == 6 && !$peminjaman->nomor_kontrak)
        <form id="formGenerateKontrak">
            <div class="row g-3">
                <!-- Nomor Kontrak -->
                <div class="col-12">
                    <label for="nomor_kontrak" class="form-label">Nomor Kontrak <span class="text-danger">*</span></label>
                    <input type="text" id="nomor_kontrak" name="nomor_kontrak" class="form-control"
                        placeholder="Masukkan nomor kontrak">
                </div>

                <!-- Jenis Pembiayaan -->
                <div class="col-12">
                    <label for="jenis_pembiayaan" class="form-label">Jenis Pembiayaan</label>
                    <input type="text" id="jenis_pembiayaan" name="jenis_pembiayaan" class="form-control"
                        value="Pembiayaan Project" readonly>
                </div>

                <!-- Data Principal -->
                <div class="col-md-6">
                    <label for="cells_project" class="form-label">Nama Principal</label>
                    <input type="text" id="cells_project" name="cells_project" class="form-control"
                        value="{{ $peminjaman->cellsProject->nama_project ?? '' }}" readonly>
                </div>

                <div class="col-md-6">
                    <label for="nama_pic" class="form-label">Nama PIC</label>
                    <input type="text" id="nama_pic" name="nama_pic" class="form-control"
                        value="{{ $peminjaman->cellsProject->nama_pic ?? '' }}" readonly>
                </div>

                <!-- Data Perusahaan -->
                <div class="col-md-6">
                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                    <input type="text" id="nama_perusahaan" name="nama_perusahaan" class="form-control"
                        value="{{ $peminjaman->debitur->nama ?? '' }}" readonly>
                </div>

                <div class="col-md-6">
                    <label for="nama_ceo" class="form-label">Nama CEO</label>
                    <input type="text" id="nama_ceo" name="nama_ceo" class="form-control"
                        value="{{ $peminjaman->debitur->nama_ceo ?? '' }}" readonly>
                </div>

                <div class="col-12">
                    <label for="alamat_perusahaan" class="form-label">Alamat Perusahaan</label>
                    <textarea id="alamat_perusahaan" name="alamat_perusahaan" class="form-control" rows="2" readonly>{{ $peminjaman->debitur->alamat ?? '' }}</textarea>
                </div>

                <!-- Detail Pembiayaan -->
                <div class="col-md-6">
                    <label for="tujuan_pembiayaan" class="form-label">Tujuan Pembiayaan</label>
                    <input type="text" id="tujuan_pembiayaan" name="tujuan_pembiayaan" class="form-control"
                        value="{{ $peminjaman->nama_project ?? '' }}" readonly>
                </div>

                <div class="col-md-6">
                    <label for="tenor_pembiayaan" class="form-label">Tenor Pembiayaan</label>
                    <input type="text" id="tenor_pembiayaan" name="tenor_pembiayaan" class="form-control"
                        value="{{ $peminjaman->durasi_project ?? '' }} Bulan" readonly>
                </div>

                <!-- Biaya dan Bagi Hasil -->
                <div class="col-md-6">
                    <label for="biaya_administrasi" class="form-label">Biaya Administrasi <span class="text-danger">*</span></label>
                    <input type="text" id="biaya_administrasi" name="biaya_administrasi" class="form-control"
                        placeholder="Masukkan biaya administrasi">
                </div>

                <div class="col-md-6">
                    <label for="bagi_hasil" class="form-label">Bagi Hasil</label>
                    <input type="text" id="bagi_hasil" name="bagi_hasil" class="form-control"
                        value="Rp {{ number_format($peminjaman->nilai_bagi_hasil ?? 0, 0, ',', '.') }}" readonly>
                </div>

                <!-- Jaminan -->
                <div class="col-12">
                    <label for="jaminan" class="form-label">Jaminan <span class="text-danger">*</span></label>
                    <textarea id="jaminan" name="jaminan" class="form-control" rows="3" 
                        placeholder="Masukkan deskripsi jaminan"></textarea>
                </div>
            </div>

            <hr class="my-3 my-md-4">

            <!-- Action Button -->
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-primary" id="btnGenerateKontrak">
                    <i class="ti ti-file-text me-1"></i>
                    Generate Kontrak
                </button>
            </div>
        </form>

    @else
        <div class="alert alert-info mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="ti ti-info-circle me-2" style="font-size: 20px;"></i>
                <div>
                    <h6 class="alert-heading mb-2">Kontrak Belum Tersedia</h6>
                    <p class="mb-0">Kontrak akan tersedia setelah proses persetujuan selesai (Step 6). Saat ini masih dalam tahap:</p>
                    <ul class="mb-0 mt-2">
                        @if($currentStep == 1)
                            <li>Pengajuan Peminjaman - <span>{{ $status }}</span></li>
                        @elseif($currentStep == 2)
                            <li>Validasi Investment Officer - <span>{{ $status }}</span></li>
                        @elseif($currentStep == 3)
                            <li>Persetujuan Debitur - <span>{{ $status }}</span></li>
                        @elseif($currentStep == 4)
                            <li>Persetujuan SKI Finance - <span>{{ $status }}</span></li>
                        @elseif($currentStep == 5)
                            <li>Persetujuan CEO SKI Finlog - <span>{{ $status }}</span></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Preview Data yang akan masuk ke Kontrak -->
        <h6 class="text-dark mb-3">Preview Data Pembiayaan</h6>
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nama Debitur</small>
                    <p class="fw-bold mb-0">{{ $peminjaman->debitur->nama ?? '-' }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nama Project</small>
                    <p class="fw-bold mb-0">{{ $peminjaman->nama_project ?? '-' }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Nilai Pembiayaan</small>
                    <p class="fw-bold mb-0">Rp {{ number_format($peminjaman->nilai_pinjaman ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Durasi Project</small>
                    <p class="fw-bold mb-0">{{ $peminjaman->durasi_project ?? 0 }} Bulan</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Bagi Hasil</small>
                    <p class="fw-bold mb-0">Rp {{ number_format($peminjaman->nilai_bagi_hasil ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="mb-0">
                    <small class="text-light fw-semibold d-block mb-1">Total Pembiayaan</small>
                    <p class="fw-bold mb-0">Rp {{ number_format($peminjaman->total_pinjaman ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    @endif

</div>
