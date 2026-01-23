<div class="container-xxl flex-grow-1 container-p-y" wire:ignore.self>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $pageTitle ?? 'Program Restrukturisasi' }}</h4>
            <p class="text-muted">
                {{ $pageSubtitle ?? 'Kelola program restrukturisasi berdasarkan pengajuan yang telah disetujui' }}</p>
        </div>
        <a href="{{ route('program-restrukturisasi.index') }}" class="btn btn-outline-primary">
            <i class="ti ti-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="simpan">
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">Informasi Debitur</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pilih Pengajuan Restrukturisasi <span
                                class="text-danger">*</span></label>
                        <select class="form-select" disabled style="background-color: #f5f5f9;">
                            <option value="{{ $id_pengajuan_restrukturisasi }}" selected>
                                {{ $nomor_kontrak }} - {{ $nama_debitur }}
                            </option>
                        </select>
                        <small class="text-muted">Pengajuan tidak dapat diubah setelah program dibuat</small>
                        @error('id_pengajuan_restrukturisasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Debitur</label>
                        <input type="text" class="form-control" wire:model="nama_debitur" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Kontrak</label>
                        <input type="text" class="form-control" wire:model="nomor_kontrak" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plafon Pembiayaan (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control"
                            value="Rp {{ number_format($plafon_pembiayaan, 0, ',', '.') }}" readonly
                            style="background-color: #f5f5f9;">
                        <small class="text-muted">Nominal sisa pokok</small>
                        @error('plafon_pembiayaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                @can('program_restrukturisasi.edit_parameter')
                    @if (($program->status ?? '') !== 'Lunas')
                    @if($specialCase)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Parameter Perhitungan</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Metode Perhitungan Plafon Pembiayaan <span
                                    class="text-danger">*</span></label>
                            {{-- In edit mode, just show a disabled select --}}
                            <select class="form-select" disabled style="background-color: #f5f5f9;">
                                <option value="{{ $metode_perhitungan }}" selected>
                                    Metode {{ $metode_perhitungan }}
                                </option>
                            </select>
                            <small class="text-muted">Metode perhitungan tidak dapat diubah setelah program
                                dibuat</small>
                            @error('metode_perhitungan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Suku Bunga Per Tahun (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('suku_bunga_per_tahun') is-invalid @enderror"
                                wire:model.live="suku_bunga_per_tahun" step="0.01" min="0" max="100">
                            @error('suku_bunga_per_tahun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jangka Waktu Total (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jangka_waktu_total') is-invalid @enderror"
                                wire:model.live="jangka_waktu_total" min="1">
                            @error('jangka_waktu_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Masa Tenggang (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('masa_tenggang') is-invalid @enderror"
                                wire:model.live="masa_tenggang" min="0">
                            <small class="text-muted">Hanya bayar margin selama masa tenggang</small>
                            @error('masa_tenggang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Mulai Cicilan <span class="text-danger">*</span></label>
                            <div wire:ignore>
                                <div class="input-group">
                                    <input type="text"
                                        class="form-control @error('tanggal_mulai_cicilan') is-invalid @enderror"
                                        id="tgl_mulai_cicilan" placeholder="yyyy-mm-dd" autocomplete="off"
                                        value="{{ $tanggal_mulai_cicilan }}">
                                    <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Metode Perhitungan Plafon Pembiayaan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" disabled style="background-color: #f5f5f9;">
                                    <option value="{{ $metode_perhitungan }}" selected>
                                        Metode {{ $metode_perhitungan }}
                                    </option>
                                </select>
                                <small class="text-muted">Metode perhitungan tidak dapat diubah setelah program
                                    dibuat</small>
                                @error('metode_perhitungan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Suku Bunga Per Tahun (%) <span
                                        class="text-danger">*</span></label>
                                <input type="number"
                                    class="form-control @error('suku_bunga_per_tahun') is-invalid @enderror"
                                    wire:model.live="suku_bunga_per_tahun" step="0.01" min="0" max="100">
                                @error('suku_bunga_per_tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jangka Waktu Total (Bulan) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('jangka_waktu_total') is-invalid @enderror"
                                    wire:model.live="jangka_waktu_total" min="1">
                                @error('jangka_waktu_total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Masa Tenggang (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('masa_tenggang') is-invalid @enderror"
                                    wire:model.live="masa_tenggang" min="0">
                                <small class="text-muted">Hanya bayar margin selama masa tenggang</small>
                                @error('masa_tenggang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal Mulai Cicilan <span class="text-danger">*</span></label>
                                <div wire:ignore>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control @error('tanggal_mulai_cicilan') is-invalid @enderror"
                                            id="tgl_mulai_cicilan" placeholder="yyyy-mm-dd" autocomplete="off"
                                            value="{{ $tanggal_mulai_cicilan }}">
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    </div>
                                </div>
                                @error('tanggal_mulai_cicilan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <button type="button" class="btn btn-primary" wire:click="hitungJadwalAngsuran"
                                    wire:loading.attr="disabled" @if (!$this->canCalculate) disabled @endif>
                                    <span wire:loading.remove wire:target="hitungJadwalAngsuran"
                                        class="d-flex align-items-center">
                                        <i class="ti ti-calculator me-1"></i>Hitung Jadwal Angsuran
                                    </span>
                                    <span wire:loading wire:target="hitungJadwalAngsuran">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Menghitung...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    @else

                    {{-- PARAMETER --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Parameter Perhitungan</h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jangka Waktu Total (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jangka_waktu_total') is-invalid @enderror"
                                wire:model.live="jangka_waktu_total" min="1">
                            @error('jangka_waktu_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominal yang Disetujui <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('nominal_yg_disetujui') is-invalid @enderror"
                                wire:model.live="nominal_yg_disetujui" min="1">
                            @error('nominal_yg_disetujui')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <button type="button" class="btn btn-primary" wire:click="hitungJadwalAngsuran"
                                wire:loading.attr="disabled" @if (!$this->canCalculate) disabled @endif>
                                <span wire:loading.remove wire:target="hitungJadwalAngsuran"
                                    class="d-flex align-items-center">
                                    <i class="ti ti-calculator me-1"></i>Hitung Jadwal Angsuran
                                </span>
                                <span wire:loading wire:target="hitungJadwalAngsuran">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Menghitung...
                                </span>
                            </button>
                        </div>
                    </div>

                    @endif
                    @endif
                @endcan

                {{-- TABEL JADWAL --}}
                @if ($show_jadwal && count($jadwal_angsuran) > 0)

                    @if ($specialCase)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Tabel Jadwal Angsuran</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 60px;">No</th>
                                            <th style="min-width: 150px;">Tanggal Jatuh Tempo</th>
                                            @if ($metode_perhitungan === 'Efektif (Anuitas)')
                                                <th class="text-end" style="min-width: 130px;">Sisa Pinjaman (Rp)
                                                </th>
                                            @endif
                                            <th class="text-end" style="min-width: 120px;">Pokok (Rp)</th>
                                            <th class="text-end" style="min-width: 120px;">Margin (Rp)</th>
                                            <th class="text-end" style="min-width: 130px;">Total Cicilan (Rp)</th>
                                            <th class="text-center" style="min-width: 100px;">Status</th>
                                            <th style="min-width: 150px;">Catatan</th>
                                            @if ($isEdit)
                                                <th class="text-center" style="min-width: 120px;">Bukti Pembayaran
                                                </th>
                                                <th class="text-center" style="min-width: 100px;">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jadwal_angsuran as $index => $item)
                                            <tr class="{{ $item['is_grace_period'] ? 'table-warning' : '' }}">
                                                <td class="text-center">{{ $item['no'] }}</td>
                                                <td>{{ $item['tanggal_jatuh_tempo'] }}</td>
                                                @if ($metode_perhitungan === 'Efektif (Anuitas)')
                                                    <td class="text-end">
                                                        {{ number_format($item['sisa_pinjaman'] ?? 0, 0, ',', '.') }}
                                                    </td>
                                                @endif
                                                <td class="text-end">
                                                    {{ number_format($item['pokok'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-end">
                                                    {{ number_format($item['margin'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-end fw-semibold">
                                                    {{ number_format($item['total_cicilan'], 0, ',', '.') }}</td>
                                                <td>
                                                    @if (isset($item['status']))
                                                        @if ($item['status'] === 'Lunas')
                                                            <span
                                                                class="badge bg-success">{{ $item['status'] }}</span>
                                                        @elseif($item['status'] === 'Jatuh Tempo')
                                                            <span class="badge bg-danger">{{ $item['status'] }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-secondary">{{ $item['status'] }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">Belum Jatuh Tempo</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item['catatan'] }}</td>
                                                @if ($isEdit)
                                                    {{-- Kolom Bukti Pembayaran --}}
                                                    <td class="text-center">
                                                        @if (!empty($item['bukti_pembayaran']))
                                                            <div class="d-flex flex-column align-items-center gap-1">
                                                                <a href="{{ Storage::url($item['bukti_pembayaran']) }}"
                                                                    target="_blank"
                                                                    class="btn btn-sm btn-info text-white">
                                                                    <i class="ti ti-eye me-1"></i>Lihat
                                                                </a>
                                                                <small class="text-muted">
                                                                    {{ isset($item['tanggal_bayar']) ? \Carbon\Carbon::parse($item['tanggal_bayar'])->format('d/m/Y') : '' }}
                                                                </small>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    {{-- Kolom Aksi --}}
                                                    <td class="text-center">
                                                        @php
                                                            // Cek apakah bisa upload (angsuran sebelumnya sudah lunas)
                                                            $canUpload = true;
                                                            $previousNo = null;
                                                            if ($item['no'] > 1) {
                                                                $previousIndex = $index - 1;
                                                                if (isset($jadwal_angsuran[$previousIndex])) {
                                                                    $previous = $jadwal_angsuran[$previousIndex];
                                                                    $previousNo = $previous['no'];
                                                                    // Check if previous has status and is not Lunas
                                                                    $previousStatus =
                                                                        $previous['status'] ?? 'Belum Jatuh Tempo';
                                                                    if (
                                                                        $previousStatus !== 'Lunas' ||
                                                                        empty($previous['bukti_pembayaran'] ?? null)
                                                                    ) {
                                                                        $canUpload = false;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        @can('program_restrukturisasi.upload')
                                                            @if (!empty($item['bukti_pembayaran']))
                                                                {{-- Tombol Edit untuk file yang sudah ada --}}
                                                                <button type="button"
                                                                    class="btn btn-sm btn-warning text-white mb-1"
                                                                    wire:click="openUploadModal({{ $index }})">
                                                                    <i class="ti ti-pencil me-1"></i>Edit
                                                                </button>
                                                            @else
                                                                <div>
                                                                    <button type="button" class="btn btn-sm btn-primary"
                                                                        wire:click="openUploadModal({{ $index }})"
                                                                        @if (!$canUpload) disabled @endif>
                                                                        <i class="ti ti-upload me-1"></i>Upload
                                                                    </button>
                                                                    @if (!$canUpload)
                                                                        <small class="text-danger d-block mt-1">
                                                                            Bayar bulan {{ $previousNo }} dulu
                                                                        </small>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endcan

                                                        {{-- Tombol Konfirmasi untuk Admin (hanya jika status Tertunda) --}}
                                                        @can('program_restrukturisasi.konfirmasi')
                                                            @if ($item['status'] === 'Tertunda')
                                                                <button type="button"
                                                                    class="btn btn-sm btn-success text-white"
                                                                    wire:click="openKonfirmasiModal({{ $index }})">
                                                                    <i class="ti ti-check me-1"></i>Konfirmasi
                                                                </button>
                                                            @endif
                                                        @endcan
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">Total</th>
                                            @if ($metode_perhitungan === 'Efektif (Anuitas)')
                                                <th></th>
                                            @endif
                                            <th class="text-end">{{ number_format($total_pokok, 0, ',', '.') }}
                                            </th>
                                            <th class="text-end">{{ number_format($total_margin, 0, ',', '.') }}
                                            </th>
                                            <th class="text-end">{{ number_format($total_cicilan, 0, ',', '.') }}
                                            </th>
                                            <th></th>
                                            <th></th>
                                            @if ($isEdit)
                                                <th></th>
                                                <th></th>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card shadow-none">
                                        <div class="card-header">
                                            <h6 class="mb-0">Ringkasan Pembayaran</h6>
                                        </div>
                                        <div class="card-body">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                                <span class="text-muted">Total Seluruh Cicilan</span>
                                                <span class="fw-semibold">Rp
                                                    {{ number_format($total_cicilan, 0, ',', '.') }}</span>
                                            </div>
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                                <span class="text-muted">Total Sudah Dibayar (Lunas)</span>
                                                <span class="fw-semibold text-success">Rp
                                                    {{ number_format($program->total_terbayar ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Sisa Pembayaran</span>
                                                @php
                                                    $sisaPembayaran = $total_cicilan - ($program->total_terbayar ?? 0);
                                                @endphp
                                                <span
                                                    class="fw-bold {{ $sisaPembayaran > 0 ? 'text-danger' : 'text-success' }}">
                                                    Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            {{-- Alert jika program sudah lunas --}}
                                            @if (($program->status ?? '') === 'Lunas')
                                                <div class="alert alert-success mt-4 mb-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-circle-check fs-3 me-3"></i>
                                                        <div>
                                                            <h6 class="alert-heading mb-1">Selamat! Program Sudah
                                                                Lunas
                                                            </h6>
                                                            <p class="mb-0">Seluruh cicilan program
                                                                restrukturisasi
                                                                ini telah dilunasi.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @else 

                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">Tabel Jadwal Angsuran</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 60px;">No</th>
                                            <th class="text-end" style="min-width: 120px;">Jumlah Pembayaran (Rp)</th>
                                            <th class="text-center">Status</th>
                                            @if ($isEdit)
                                                <th class="text-center" style="min-width: 150px;">Bukti Pembayaran</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jadwal_angsuran as $index => $item)
                                            <tr>
                                                <td class="text-center">{{ $item['no'] }}</td>
                                                <td class="text-end">{{ number_format($item['pokok'], 0, ',', '.') }}</td>
                                                <td class="text-center">
                                                    @if (isset($item['status']))
                                                        @if ($item['status'] === 'Lunas')
                                                            <span
                                                                class="badge bg-success">{{ $item['status'] }}</span>
                                                        @elseif($item['status'] === 'Jatuh Tempo')
                                                            <span class="badge bg-danger">{{ $item['status'] }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-secondary">{{ $item['status'] }}</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">Belum Jatuh Tempo</span>
                                                    @endif
                                                </td>
                                                @if ($isEdit)
                                                    <td>
                                                        @if (!empty($item['bukti_pembayaran']))
                                                            <div class="d-flex flex-column gap-1">
                                                                <a href="{{ Storage::url($item['bukti_pembayaran']) }}"
                                                                    target="_blank" class="btn btn-sm btn-info">
                                                                    <i class="ti ti-eye me-1"></i>Lihat Bukti
                                                                </a>
                                                                <small class="text-muted">
                                                                    {{ isset($item['tanggal_bayar']) ? \Carbon\Carbon::parse($item['tanggal_bayar'])->format('d/m/Y') : '' }}
                                                                </small>
                                                            </div>
                                                        @else
                                                            @php
                                                                // Cek apakah bisa upload (angsuran sebelumnya sudah lunas)
                                                                $canUpload = true;
                                                                $previousNo = null;
                                                                if ($item['no'] > 1) {
                                                                    $previousIndex = $index - 1;
                                                                    if (isset($jadwal_angsuran[$previousIndex])) {
                                                                        $previous = $jadwal_angsuran[$previousIndex];
                                                                        $previousNo = $previous['no'];
                                                                        // Check if previous has status and is not Lunas
                                                                        $previousStatus =
                                                                            $previous['status'] ?? 'Belum Jatuh Tempo';
                                                                        if (
                                                                            $previousStatus !== 'Lunas' ||
                                                                            empty($previous['bukti_pembayaran'] ?? null)
                                                                        ) {
                                                                            $canUpload = false;
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            <div>
                                                                <button type="button" class="btn btn-sm btn-primary"
                                                                    wire:click="openUploadModal({{ $index }})"
                                                                    @if (!$canUpload) disabled @endif>
                                                                    <i class="ti ti-upload me-1"></i>Upload Bukti
                                                                </button>
                                                                @if (!$canUpload)
                                                                    <small class="text-danger d-block mt-1">
                                                                        Bayar angsuran bulan {{ $previousNo }}
                                                                        terlebih dahulu
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th class="text-end">{{ number_format($total_pokok, 0, ',', '.') }}</th>
                                            @if ($isEdit)
                                                <th></th>
                                            @endif
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card shadow-none">
                                        <div class="card-header">
                                            <h6 class="mb-0">Ringkasan Perhitungan</h6>
                                        </div>
                                        <div class="card-body">
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                                <span class="text-muted">Total Pokok</span>
                                                <span class="fw-semibold">Rp
                                                    {{ number_format($total_pokok, 0, ',', '.') }}</span>
                                            </div>
                                            <div
                                                class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                                <span class="text-muted">Total Margin</span>
                                                <span class="fw-semibold">Rp
                                                    {{ number_format($total_margin, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Total Dibayar</span>
                                                <span class="fw-bold">Rp
                                                    {{ number_format($total_cicilan, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @endif

                @endif

                {{-- TOMBOL SIMPAN --}}
                @can('program_restrukturisasi.edit_parameter')
                    @if (($program->status ?? '') !== 'Lunas')
                        <div class="row mt-4">
                            <div class="col-12">
                                @if ($show_jadwal && count($jadwal_angsuran) > 0)
                                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="simpan" class="d-flex align-items-center">
                                            <i class="ti ti-device-floppy me-1">
                                            </i>
                                            {{ $submitLabel ?? 'Simpan Restrukturisasi' }}
                                        </span>
                                        <span wire:loading wire:target="simpan">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                            Menyimpan...
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif
                @endcan
            </form>
        </div>
    </div>

    @include('livewire.program-restrukturisasi.partials.modal')
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Init Datepicker
            $('#tgl_mulai_cicilan').datepicker({
                format: 'yyyy-mm-dd',
                todayHighlight: true,
                autoclose: true,
                orientation: 'bottom auto',
                startDate: 'today'
            }).on('changeDate', function(e) {
                var date = e.format(0, "yyyy-mm-dd");
                @this.set('tanggal_mulai_cicilan', date);
            });
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('swal:modal', (data) => {
                const options = data[0];

                Swal.fire({
                    icon: options.type,
                    title: options.title,
                    text: options.text,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed && options.redirect_url) {
                        window.location.href = options.redirect_url;
                    }

                    // Auto close modal when upload success
                    if (options.type === 'success' && options.text.includes(
                            'Bukti pembayaran berhasil')) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'modalUploadBukti'));
                        if (modal) {
                            modal.hide();
                        }
                    }
                });
            });

            // Buka modal ketika event dipanggil
            Livewire.on('open-upload-modal', () => {
                const modalElement = document.getElementById('modalUploadBukti');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();

                    // Reset form ketika modal ditutup
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        @this.closeUploadModal();
                    });
                }
            });

            // Buka modal konfirmasi
            let isSwitchingToTolak = false;

            Livewire.on('open-konfirmasi-modal', () => {
                isSwitchingToTolak = false;
                const modalElement = document.getElementById('modalKonfirmasi');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();

                    modalElement.addEventListener('hidden.bs.modal', function() {
                        if (!isSwitchingToTolak) {
                            @this.closeKonfirmasiModal();
                        }
                    }, {
                        once: true
                    });
                }
            });

            // Switch dari modal konfirmasi ke modal tolak
            Livewire.on('switch-to-tolak-modal', () => {
                isSwitchingToTolak = true;

                // Tutup modal konfirmasi dulu
                const konfirmasiModal = document.getElementById('modalKonfirmasi');
                if (konfirmasiModal) {
                    const bsKonfirmasiModal = bootstrap.Modal.getInstance(konfirmasiModal);
                    if (bsKonfirmasiModal) {
                        bsKonfirmasiModal.hide();
                    }
                }

                // Tunggu modal konfirmasi tertutup, lalu buka modal tolak
                setTimeout(() => {
                    const tolakModal = document.getElementById('modalTolak');
                    if (tolakModal) {
                        const modal = new bootstrap.Modal(tolakModal);
                        modal.show();

                        // Reset ketika modal ditutup
                        tolakModal.addEventListener('hidden.bs.modal', function() {
                            @this
                                .closeKonfirmasiModal();
                        }, {
                            once: true
                        });
                    }
                }, 300);
            });

            // Tutup modal konfirmasi via Livewire event
            Livewire.on('close-konfirmasi-modal', () => {
                const modalElement = document.getElementById('modalKonfirmasi');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });

            // Tutup modal tolak via Livewire event
            Livewire.on('close-tolak-modal', () => {
                const modalElement = document.getElementById('modalTolak');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });
        });
    </script>
@endpush
