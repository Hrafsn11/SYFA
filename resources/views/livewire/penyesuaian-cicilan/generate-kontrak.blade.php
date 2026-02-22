@php
    use Carbon\Carbon;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Generate Kontrak Restrukturisasi</h4>
            <p class="text-muted mb-0">Lengkapi data dan generate kontrak untuk memulai program restrukturisasi</p>
        </div>
        <div>
            <a href="{{ route('penyesuaian-cicilan.show', $program->id_penyesuaian_cicilan) }}"
                class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Alert Info --}}
    <div class="alert alert-info mb-4" role="alert">
        <div class="d-flex">
            <i class="ti ti-info-circle me-2" style="font-size: 1.5rem;"></i>
            <div>
                <h6 class="alert-heading mb-1">Informasi Penting</h6>
                <p class="mb-0">Setelah kontrak di-generate, status program akan berubah menjadi
                    <strong>"Berjalan"</strong> dan jadwal angsuran dapat mulai diisi.</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Data Kontrak --}}
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-file-text me-2"></i>Data Kontrak Restrukturisasi
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Nomor Kontrak Preview --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nomor Kontrak (Preview)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-warning text-dark">
                                    <i class="ti ti-file-certificate"></i>
                                </span>
                                <input type="text" class="form-control bg-light fw-bold"
                                    value="{{ $previewNomorKontrak }}" readonly>
                            </div>
                            <small class="text-warning">
                                <i class="ti ti-alert-circle me-1"></i>Preview - akan di-generate saat disimpan
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nomor Kontrak Pembiayaan Awal</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $dataKontrak['nomor_kontrak_pembiayaan'] }}" readonly>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Section 1: Jenis Restrukturisasi --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">1. Jenis Restrukturisasi</label>
                        <div class="d-flex flex-wrap gap-2">
                            @if(is_array($dataKontrak['jenis_restrukturisasi']) && count($dataKontrak['jenis_restrukturisasi']) > 0)
                                @foreach($dataKontrak['jenis_restrukturisasi'] as $jenis)
                                    <span class="badge bg-primary">{{ $jenis }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>

                    {{-- Section 2: Debitur --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">2. Debitur</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">a. Nama Perusahaan</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $dataKontrak['nama_perusahaan'] }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">b. Nama Pimpinan</label>
                                <input type="text" class="form-control bg-light"
                                    value="{{ $dataKontrak['nama_pimpinan'] }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small">c. Alamat Perusahaan</label>
                                <textarea class="form-control bg-light" rows="2"
                                    readonly>{{ $dataKontrak['alamat_perusahaan'] }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Tujuan Restrukturisasi --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">3. Tujuan Restrukturisasi</label>
                        <textarea class="form-control bg-light" rows="2"
                            readonly>{{ $dataKontrak['tujuan_restrukturisasi'] }}</textarea>
                    </div>

                    {{-- Section 4-11: Data Finansial --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">4. Nilai Plafon Awal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control bg-light"
                                    value="{{ number_format($dataKontrak['nilai_plafon_awal'], 0, ',', '.') }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">5. Nilai Plafon Pembiayaan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control bg-light"
                                    value="{{ number_format($dataKontrak['nilai_plafon_pembiayaan'], 0, ',', '.') }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">6. Metode Perhitungan</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $dataKontrak['metode_perhitungan'] ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">7. Jangka Waktu</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light"
                                    value="{{ $dataKontrak['jangka_waktu'] }}" readonly>
                                <span class="input-group-text">bulan</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">8. Tanggal Mulai Cicilan</label>
                            <input type="text" class="form-control bg-light"
                                value="{{ $dataKontrak['tanggal_mulai_cicilan'] ? Carbon::parse($dataKontrak['tanggal_mulai_cicilan'])->format('d F Y') : '-' }}"
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">9. Bagi Hasil / Suku Bunga</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light"
                                    value="{{ number_format($dataKontrak['suku_bunga'], 2) }}" readonly>
                                <span class="input-group-text">% per tahun</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">10. Masa Tenggang</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light"
                                    value="{{ $dataKontrak['masa_tenggang'] }}" readonly>
                                <span class="input-group-text">bulan</span>
                            </div>
                        </div>

                        {{-- Section 11: Jaminan (Editable) --}}
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                11. Jaminan <span class="text-danger">*</span>
                            </label>
                            <textarea wire:model="jaminan" class="form-control @error('jaminan') is-invalid @enderror"
                                rows="2" placeholder="Masukkan jaminan yang disepakati..."></textarea>
                            @error('jaminan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Input jaminan yang disepakati untuk kontrak</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Ringkasan & Actions --}}
        <div class="col-lg-4">
            {{-- Ringkasan Perhitungan --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ti ti-calculator me-2"></i>Ringkasan Perhitungan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">Total Pokok</span>
                        <span class="fw-semibold">Rp {{ number_format($program->total_pokok, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <span class="text-muted">Total Margin</span>
                        <span class="fw-semibold">Rp {{ number_format($program->total_margin, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Dibayar</span>
                        <span class="fw-bold text-primary">Rp
                            {{ number_format($program->total_cicilan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ti ti-settings me-2"></i>Aksi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        {{-- Preview Kontrak --}}
                        <a href="{{ route('penyesuaian-cicilan.preview-kontrak', $program->id_penyesuaian_cicilan) }}"
                            target="_blank" class="btn btn-outline-primary">
                            <i class="ti ti-eye me-2"></i>Preview Kontrak
                        </a>

                        {{-- Generate Kontrak --}}
                        <button type="button" wire:click="generateKontrak" wire:loading.attr="disabled"
                            class="btn btn-success btn-lg">
                            <span wire:loading.remove wire:target="generateKontrak">
                                <i class="ti ti-file-check me-2"></i>Generate Kontrak
                            </span>
                            <span wire:loading wire:target="generateKontrak">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Memproses...
                            </span>
                        </button>

                        <small class="text-muted text-center">
                            <i class="ti ti-info-circle me-1"></i>
                            Pastikan semua data sudah benar sebelum generate kontrak
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Listen for SweetAlert events
        window.addEventListener('swal:modal', event => {
            Swal.fire({
                icon: event.detail.type || event.detail[0]?.type,
                title: event.detail.title || event.detail[0]?.title,
                text: event.detail.text || event.detail[0]?.text,
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    const redirectUrl = event.detail.redirect_url || event.detail[0]?.redirect_url;
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    }
                }
            });
        });
    </script>
@endpush