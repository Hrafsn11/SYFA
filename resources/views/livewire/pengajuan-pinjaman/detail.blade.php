<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Detail Pengajuan Peminjaman</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home.services') }}" wire:navigate>Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('peminjaman.index') }}" wire:navigate>Peminjaman Dana</a>
                    </li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <span class="p-3 rounded text-white bg-{{ $status === 'Dana Sudah Dicairkan' ? 'success' : 'primary' }} fs-6">
                {{ $status }}
            </span>
        </div>
    </div>

    {{-- Stepper --}}
    @include('livewire.pengajuan-pinjaman.partials._stepper')

    {{-- Alert Peninjauan --}}
    @if ($this->shouldShowAlertPeninjauan())
    <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
        <i class="fas fa-info-circle me-2"></i>
        Pengajuan Pinjaman Anda sedang kami tinjau. Harap tunggu
        beberapa saat hingga proses verifikasi selesai.
    </div>
    @endif

    {{-- Main Content Card with Tabs --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-0">
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#detail-pinjaman" role="tab" aria-selected="true">
                                    <i class="ti ti-wallet me-2"></i>
                                    <span class="d-none d-sm-inline">Detail Pinjaman</span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#detail-kontrak" role="tab" aria-selected="false">
                                    <i class="ti ti-report-money me-2"></i>
                                    <span class="d-none d-sm-inline">Detail Kontrak</span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#activity" role="tab" aria-selected="false">
                                    <i class="ti ti-activity me-2"></i>
                                    <span class="d-none d-sm-inline">Activity</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        {{-- Detail Pinjaman Tab --}}
                        <div class="tab-pane fade show active" id="detail-pinjaman" role="tabpanel">
                            <div id="content-default">
                                <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
                                    <h5 class="mb-3 mb-md-4">Detail Pinjaman</h5>
                                    <div class="d-flex gap-2">
                                        @include('livewire.pengajuan-pinjaman.partials._action-buttons')
                                    </div>
                                </div>

                                <hr class="my-3 my-md-4">

                                {{-- Data Perusahaan --}}
                                @include('livewire.pengajuan-pinjaman.partials._data-perusahaan')

                                <hr class="my-3 my-md-4">

                                {{-- Data Peminjaman --}}
                                @include('livewire.pengajuan-pinjaman.partials._data-peminjaman')

                                <hr class="my-3 my-md-4">

                                {{-- Data Invoice/Kontrak --}}
                                @include('livewire.pengajuan-pinjaman.partials._data-invoice-table')

                                {{-- Upload Dokumen Transfer (Step 7) --}}
                                @if ($currentStep == 7)
                                @can('peminjaman_dana.upload_dokumen_transfer')
                                <div class="mt-4">
                                    <hr class="my-4">
                                    <h6 class="text-dark mb-3">
                                        <i class="ti ti-upload me-2"></i>
                                        Upload Dokumen Transfer
                                    </h6>

                                    <div class="card border shadow-none">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="dokumenTransfer" class="form-label">
                                                    Dokumen Transfer <span class="text-danger">*</span>
                                                </label>
                                                <input type="file" class="form-control"
                                                    id="dokumenTransfer"
                                                    wire:model="dokumen_transfer"
                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                <div class="form-text">Format: PDF, JPG, PNG (Max: 2MB)</div>
                                                @error('dokumen_transfer') <span class="text-danger small">{{ $message }}</span> @enderror

                                                {{-- Upload progress --}}
                                                <div wire:loading wire:target="dokumen_transfer" class="mt-2">
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                                    </div>
                                                    <small class="text-muted">Mengupload file...</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endcan
                                @endif
                            </div>
                        </div>

                        {{-- Detail Kontrak Tab --}}
                        <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                            @if ($currentStep == 6 && empty($no_kontrak))
                            {{-- Step 6: Form Generate Kontrak --}}
                            <h5 class="mb-4">Generate Kontrak Peminjaman</h5>

                            <div class="col-lg mb-3">
                                <label for="no_kontrak" class="form-label">No Kontrak</label>
                                <input type="text" class="form-control" id="no_kontrak"
                                    value="{{ $no_kontrak ?? ($preview_no_kontrak ?? '') }}"
                                    placeholder="No Kontrak akan di-generate otomatis" readonly>
                                <small class="text-muted">
                                    <i class="ti ti-info-circle"></i>
                                    @if (empty($no_kontrak) && !empty($preview_no_kontrak))
                                    Preview nomor kontrak. Akan disimpan saat Anda klik "Simpan"
                                    @else
                                    Nomor kontrak otomatis dari sistem
                                    @endif
                                </small>
                            </div>

                            <div class="col-lg mb-3">
                                <label for="jenis_pembiayaan_kontrak" class="form-label">Jenis Pembiayaan</label>
                                <input type="text" class="form-control" id="jenis_pembiayaan_kontrak"
                                    value="{{ $jenis_pembiayaan ?? '' }}" disabled>
                            </div>

                            {{-- Card Data Perusahaan --}}
                            <div class="card border-1 shadow-none mb-3">
                                <div class="card-body">
                                    <div class="col-lg mb-3">
                                        <label class="form-label">Nama Perusahaan</label>
                                        <input type="text" class="form-control"
                                            value="{{ $nama_perusahaan ?? 'N/A' }}" disabled>
                                    </div>
                                    <div class="col-lg mb-3">
                                        <label class="form-label">Nama Pimpinan</label>
                                        <input type="text" class="form-control"
                                            value="{{ $nama_ceo ?? 'N/A' }}" disabled>
                                    </div>
                                    <div class="col-lg mb-3">
                                        <label class="form-label">Alamat Perusahaan</label>
                                        <input type="text" class="form-control"
                                            value="{{ $alamat ?? 'N/A' }}" disabled>
                                    </div>
                                    <div class="col-lg mb-3">
                                        <label class="form-label">Tujuan Pembiayaan</label>
                                        <input type="text" class="form-control"
                                            value="{{ $tujuan_pembiayaan ?? 'N/A' }}" disabled>
                                    </div>
                                </div>
                            </div>

                            @php
                            $nilaiPembiayaan = $nominal_yang_disetujui ?? $nominal_pinjaman ?? 0;
                            @endphp

                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Nilai Pembiayaan</label>
                                    <input type="text" class="form-control"
                                        value="Rp {{ number_format($nilaiPembiayaan, 0, ',', '.') }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hutang Pokok</label>
                                    <input type="text" class="form-control"
                                        value="Rp {{ number_format($nilaiPembiayaan, 0, ',', '.') }}" disabled>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">Tenor Pembiayaan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $tenor_pembayaran ?? 1 }} Bulan" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Biaya Administrasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                        id="input_biaya_administrasi"
                                        placeholder="Rp 0"
                                        autocomplete="off">
                                    <input type="hidden" wire:model="biaya_administrasi" id="hidden_biaya_administrasi">
                                    @error('biaya_administrasi') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-lg mb-3">
                                <label class="form-label">Bunga (Nisbah)</label>
                                <input type="text" class="form-control"
                                    value="{{ $persentase_bunga ?? 2 }}% flat / bulan" disabled>
                            </div>

                            <div class="col-lg mb-3">
                                <label class="form-label">Denda Keterlambatan</label>
                                <input type="text" class="form-control"
                                    value="2% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut" disabled>
                            </div>

                            <div class="col-lg mb-3">
                                <label class="form-label">Jaminan</label>
                                <input type="text" class="form-control"
                                    value="{{ $jenis_pembiayaan ?? '' }}" disabled>
                            </div>

                            <div class="col-lg mb-3">
                                <label class="form-label">Tanda Tangan Debitur</label>
                                @if ($tanda_tangan)
                                <div class="border rounded p-3 bg-light">
                                    <img src="{{ asset('storage/' . $tanda_tangan) }}"
                                        alt="Tanda Tangan Debitur" class="img-fluid"
                                        style="max-height: 150px; max-width: 100%;">
                                    <small class="text-muted d-block mt-2">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Tanda tangan dari data master debitur
                                    </small>
                                </div>
                                @else
                                <div class="border rounded p-3 text-center bg-light">
                                    <i class="ti ti-signature text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0">Tanda tangan debitur tidak tersedia</p>
                                    <small class="text-muted">Silakan update tanda tangan di data master debitur</small>
                                </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-primary"
                                    wire:click="previewKontrak">
                                    <i class="ti ti-eye me-2"></i>
                                    Preview Kontrak
                                </button>
                                @can('peminjaman_dana.generate_kontrak')
                                <button type="button" class="btn btn-primary"
                                    wire:click="generateKontrak"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="generateKontrak">
                                        <i class="ti ti-device-floppy me-2"></i>Simpan
                                    </span>
                                    <span wire:loading wire:target="generateKontrak">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                                    </span>
                                </button>
                                @endcan
                            </div>

                            @elseif (!empty($no_kontrak))
                            {{-- Kontrak sudah ada: Info + Preview/Download --}}
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Detail Kontrak</h5>
                                <span class="badge bg-label-success">
                                    <i class="ti ti-check me-1"></i> Kontrak Tersedia
                                </span>
                            </div>
                            <hr class="my-3">

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <small class="text-muted fw-semibold d-block mb-1">Nomor Kontrak</small>
                                        <p class="fw-bold mb-0">{{ $no_kontrak }}</p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <small class="text-muted fw-semibold d-block mb-1">Jenis Pembiayaan</small>
                                        <p class="mb-0">{{ $jenis_pembiayaan ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <small class="text-muted fw-semibold d-block mb-1">Nilai Pembiayaan</small>
                                        <p class="fw-bold text-primary mb-0">
                                            Rp {{ number_format($nominal_yang_disetujui ?? $nominal_pinjaman ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <small class="text-muted fw-semibold d-block mb-1">Tenor</small>
                                        <p class="mb-0">{{ $tenor_pembayaran ?? 1 }} Bulan</p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <small class="text-muted fw-semibold d-block mb-1">Biaya Administrasi</small>
                                        <p class="mb-0">Rp {{ number_format($pengajuan->biaya_administrasi ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="mb-3">
                                        <small class="text-muted fw-semibold d-block mb-1">Nisbah (Bunga)</small>
                                        <p class="mb-0">{{ $persentase_bunga ?? 2 }}% flat / bulan</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary"
                                    wire:click="previewKontrak"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="previewKontrak">
                                        <i class="ti ti-eye me-2"></i> Preview Kontrak
                                    </span>
                                    <span wire:loading wire:target="previewKontrak">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Memuat...
                                    </span>
                                </button>
                                <button type="button" class="btn btn-primary"
                                    wire:click="downloadKontrak"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="downloadKontrak">
                                        <i class="ti ti-download me-2"></i> Download Kontrak
                                    </span>
                                    <span wire:loading wire:target="downloadKontrak">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Generating PDF...
                                    </span>
                                </button>
                            </div>
                            @else
                            {{-- Belum sampai Step 6: Placeholder --}}
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="ti ti-file-off display-4 text-muted"></i>
                                </div>
                                <h5 class="text-muted mb-2">Kontrak Belum Tersedia</h5>
                                <p class="text-muted mb-0">Kontrak akan digenerate setelah proses persetujuan selesai.</p>
                            </div>
                            @endif
                        </div>

                        {{-- Activity Tab --}}
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            @include('livewire.pengajuan-pinjaman.partials._activity-tab')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Modals --}}
    @include('livewire.pengajuan-pinjaman.components.modal_approval')



    {{-- Notification Handler --}}
    @script
    <script>
        $wire.on('notify', (data) => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: data[0].type,
                    title: data[0].type === 'success' ? 'Berhasil!' : 'Error!',
                    text: data[0].message,
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                alert(data[0].message);
            }
        });

        $wire.on('closeModal', () => {
            window.dispatchEvent(new CustomEvent('close-all-modals'));
        });

        // Initialize Cleave.js for biaya_administrasi
        function initBiayaAdminInput() {
            const currencyInput = document.getElementById('input_biaya_administrasi');
            const hiddenCurrency = document.getElementById('hidden_biaya_administrasi');

            if (currencyInput && typeof Cleave !== 'undefined') {
                if (currencyInput._cleaveInstance) {
                    currencyInput._cleaveInstance.destroy();
                }

                const cleaveInstance = new Cleave(currencyInput, {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalScale: 0,
                    prefix: 'Rp ',
                    rawValueTrimPrefix: true,
                    noImmediatePrefix: false
                });

                currencyInput._cleaveInstance = cleaveInstance;

                currencyInput.addEventListener('input', function() {
                    if (hiddenCurrency) {
                        const rawValue = cleaveInstance.getRawValue();
                        hiddenCurrency.value = rawValue;
                        hiddenCurrency.dispatchEvent(new Event('input', {
                            bubbles: true
                        }));
                    }
                });
            }
        }

        // Init on page load and after Livewire updates
        initBiayaAdminInput();

        Livewire.hook('morph.updated', () => {
            setTimeout(initBiayaAdminInput, 100);
        });
    </script>
    @endscript
</div>