<div>
    <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2 mt-3">
        <h5 class="mb-3 mb-md-4">Detail Peminjaman</h5>
        <div class="d-flex gap-2">
            {{-- Step 1: Draft - Button Submit Pengajuan --}}
            @if ($currentStep == 1 && $status == 'Draft')
                <button type="button" class="btn btn-success" id="btnSubmitPengajuan">
                    <i class="ti ti-send me-2"></i>
                    Submit Pengajuan
                </button>
            @endif

            {{-- Step 2: Validasi Investment Officer --}}
            @if ($currentStep == 2 && $status == 'Menunggu Persetujuan')
                <button type="button" class="btn btn-primary" id="btnValidasiIO">
                    <i class="ti ti-check me-2"></i>
                    Validasi Investment Officer
                </button>
            @endif

            {{-- Step 3: Persetujuan Debitur --}}
            @if ($currentStep == 3 && $status == 'Menunggu Persetujuan')
                <button type="button" class="btn btn-primary" id="btnPersetujuanDebitur">
                    <i class="ti ti-check me-2"></i>
                    Konfirmasi Debitur
                </button>
            @endif

            {{-- Step 4: Persetujuan SKI Finance --}}
            @if ($currentStep == 4 && $status == 'Menunggu Persetujuan')
                <button type="button" class="btn btn-primary" id="btnPersetujuanSKIFinance">
                    <i class="ti ti-check me-2"></i>
                    Persetujuan SKI Finance
                </button>
            @endif

            {{-- Step 5: Persetujuan CEO SKI Finlog --}}
            @if ($currentStep == 5 && $status == 'Menunggu Persetujuan')
                <button type="button" class="btn btn-primary" id="btnPersetujuanCEOFinlog">
                    <i class="ti ti-check me-2"></i>
                    Persetujuan CEO Finlog
                </button>
            @endif

            {{-- Step 6: Generate Kontrak - Button ada di Tab Kontrak --}}

            {{-- Step 7: Upload Bukti Transfer --}}
            @if ($currentStep == 7 && $status == 'Dicairkan')
                <button type="button" class="btn btn-success" id="btnUploadBuktiTransfer">
                    <i class="ti ti-upload me-2"></i>
                    Upload Bukti Transfer
                </button>
            @endif

            {{-- Status Ditolak --}}
            @if ($status == 'Ditolak')
                <span class="badge bg-danger fs-6">
                    <i class="ti ti-x me-1"></i>
                    {{ $status }}
                </span>
            @endif

            {{-- Status Selesai --}}
            @if ($currentStep == 8 || $status == 'Selesai')
                <span class="badge bg-success fs-6">
                    <i class="ti ti-check me-1"></i>
                    Proses Selesai
                </span>
            @endif
        </div>
    </div>

    <hr class="my-3 my-md-4">

    <!-- Data Peminjaman -->
    <h6 class="text-dark mb-3">Data Peminjaman Finlog</h6>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nomor Peminjaman</small>
                <p class="fw-bold mb-0">{{ $peminjaman->nomor_peminjaman ?? '-' }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nama Debitur</small>
                <p class="fw-bold mb-0">{{ $peminjaman->debitur->nama ?? '-' }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nama Project</small>
                <p class="fw-bold mb-0">{{ $peminjaman->nama_project ?? '-' }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Project Cells</small>
                <p class="fw-bold mb-0">{{ $peminjaman->cellsProject->nama_cells_bisnis ?? '-' }}</p>
            </div>
        </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Durasi Project (Bulan)</small>
                <p class="fw-bold mb-0">{{ $peminjaman->durasi_project ?? 0 }} Bulan</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Durasi Project (Hari)</small>
                <p class="fw-bold mb-0">{{ $peminjaman->durasi_project_hari ?? 0 }} Hari</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">NIB Perusahaan</small>
                <p class="fw-bold mb-0">{{ $peminjaman->nib_perusahaan ?? '-' }}</p>
            </div>
        </div>
    </div>

    <hr class="my-3 my-md-4">

    <!-- Detail Pinjaman -->
    <h6 class="text-dark mb-3">Detail Pinjaman</h6>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nilai Pinjaman</small>
                <p class="fw-bold mb-0">Rp {{ number_format($peminjaman->nilai_pinjaman ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Persentase Bagi Hasil</small>
                <p class="fw-bold mb-0">{{ $peminjaman->presentase_bagi_hasil ?? 0 }}%</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Nilai Bagi Hasil</small>
                <p class="fw-bold mb-0">Rp {{ number_format($peminjaman->nilai_bagi_hasil ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Total Pinjaman</small>
                <p class="fw-bold mb-0">Rp {{ number_format($peminjaman->total_pinjaman ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <hr class="my-3 my-md-4">

    <!-- Jadwal Pencairan & Pengembalian -->
    <h6 class="text-dark mb-3">Jadwal Pencairan & Pengembalian</h6>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Harapan Tanggal Pencairan</small>
                <p class="fw-bold mb-0">
                    {{ $peminjaman->harapan_tanggal_pencairan ? \Carbon\Carbon::parse($peminjaman->harapan_tanggal_pencairan)->format('d F Y') : '-' }}
                </p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">TOP (Term of Payment)</small>
                <p class="fw-bold mb-0">{{ $peminjaman->top ?? 0 }} Hari</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Rencana Tanggal Pengembalian</small>
                <p class="fw-bold mb-0">
                    {{ $peminjaman->rencana_tgl_pengembalian ? \Carbon\Carbon::parse($peminjaman->rencana_tgl_pengembalian)->format('d F Y') : '-' }}
                </p>
            </div>
        </div>
    </div>

    <hr class="my-3 my-md-4">

    <!-- Dokumen Persyaratan -->
    <h6 class="text-dark mb-3">Dokumen Persyaratan</h6>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Dokumen Mitra</small>
                @if ($peminjaman->dokumen_mitra)
                    <a href="{{ asset('storage/' . $peminjaman->dokumen_mitra) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye me-1"></i> Lihat
                    </a>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Form New Customer</small>
                @if ($peminjaman->form_new_customer)
                    <a href="{{ asset('storage/' . $peminjaman->form_new_customer) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye me-1"></i> Lihat
                    </a>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Dokumen Kerja Sama</small>
                @if ($peminjaman->dokumen_kerja_sama)
                    <a href="{{ asset('storage/' . $peminjaman->dokumen_kerja_sama) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye me-1"></i> Lihat
                    </a>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Dokumen NPA</small>
                @if ($peminjaman->dokumen_npa)
                    <a href="{{ asset('storage/' . $peminjaman->dokumen_npa) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye me-1"></i> Lihat
                    </a>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Akta Perusahaan</small>
                @if ($peminjaman->akta_perusahaan)
                    <a href="{{ asset('storage/' . $peminjaman->akta_perusahaan) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye me-1"></i> Lihat
                    </a>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">KTP Owner</small>
                @if ($peminjaman->ktp_owner)
                    <a href="{{ asset('storage/' . $peminjaman->ktp_owner) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye me-1"></i> Lihat
                    </a>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">KTP PIC</small>
                @if ($peminjaman->ktp_pic)
                    <a href="{{ asset('storage/' . $peminjaman->ktp_pic) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye me-1"></i> Lihat
                    </a>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Surat Izin Usaha</small>
                @if ($peminjaman->surat_izin_usaha)
                    <a href="{{ asset('storage/' . $peminjaman->surat_izin_usaha) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-eye me-1"></i> Lihat
                    </a>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>
    </div>

    <hr class="my-3 my-md-4">

    <!-- Catatan -->
    <h6 class="text-dark mb-3">Catatan</h6>
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Catatan</small>
                <p class="mb-0">{{ $peminjaman->catatan ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
