<div class="container-xxl flex-grow-1 container-p-y" wire:ignore.self>
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $pageTitle ?? 'Program Restrukturisasi' }}</h4>
            <p class="text-muted">{{ $pageSubtitle ?? 'Kelola program restrukturisasi berdasarkan pengajuan yang telah disetujui' }}</p>
        </div>
    </div>

    {{-- Alert Container (Backup) --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="simpan">
                {{-- INFO DEBITUR --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">Informasi Debitur</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pilih Pengajuan Restrukturisasi <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('id_pengajuan_restrukturisasi') is-invalid @enderror" 
                            wire:model.live="id_pengajuan_restrukturisasi"
                            @if($isEdit) disabled @endif>
                            <option value="">-- Pilih Pengajuan --</option>
                            @foreach($approvedRestrukturisasi as $item)
                                <option value="{{ $item->id_pengajuan_restrukturisasi }}">
                                    {{ $item->debitur ? $item->debitur->nama : $item->nama_perusahaan }} - {{ $item->nomor_kontrak_pembiayaan }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mengambil data sesuai dengan debitur yang Login</small>
                        @error('id_pengajuan_restrukturisasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Debitur</label>
                        <input type="text" class="form-control" wire:model.live="nama_debitur" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Kontrak</label>
                        <input type="text" class="form-control" wire:model.live="nomor_kontrak" readonly>
                    </div>
                </div>

                <hr class="my-4">

                {{-- PARAMETER --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">Parameter Perhitungan</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Metode Perhitungan Plafon Pembiayaan <span class="text-danger">*</span></label>
                        <select class="form-select @error('metode_perhitungan') is-invalid @enderror" wire:model.live="metode_perhitungan">
                            <option value="Flat">Metode Flat</option>
                            <option value="Anuitas">Metode Anuitas</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Plafon Pembiayaan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('plafon_pembiayaan') is-invalid @enderror" 
                            wire:model.live="plafon_pembiayaan" step="0.01" min="0" readonly
                            @if($isEdit) readonly style="background-color: #f5f5f9;" @endif>
                        <small class="text-muted">Nominal sisa pokok</small>
                        @error('plafon_pembiayaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Suku Bunga Per Tahun (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('suku_bunga_per_tahun') is-invalid @enderror" 
                            wire:model.live="suku_bunga_per_tahun" step="0.01" min="0" max="100">
                        @error('suku_bunga_per_tahun') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jangka Waktu Total (Bulan) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('jangka_waktu_total') is-invalid @enderror" 
                            wire:model.live="jangka_waktu_total" min="1">
                        @error('jangka_waktu_total') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Masa Tenggang (Bulan) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('masa_tenggang') is-invalid @enderror" 
                            wire:model.live="masa_tenggang" min="0">
                        <small class="text-muted">Hanya bayar margin selama masa tenggang</small>
                        @error('masa_tenggang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai Cicilan <span class="text-danger">*</span></label>
                        <div wire:ignore>
                            <div class="input-group">
                                <input type="text" class="form-control @error('tanggal_mulai_cicilan') is-invalid @enderror" 
                                    id="tgl_mulai_cicilan" placeholder="yyyy-mm-dd" autocomplete="off"
                                    value="{{ $tanggal_mulai_cicilan }}">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                        </div>
                        @error('tanggal_mulai_cicilan') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <button type="button" class="btn btn-primary" wire:click="hitungJadwalAngsuran" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="hitungJadwalAngsuran">
                                <i class="ti ti-calculator me-1"></i>Hitung Jadwal Angsuran
                            </span>
                            <span wire:loading wire:target="hitungJadwalAngsuran">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Menghitung...
                            </span>
                        </button>
                    </div>
                </div>

                {{-- TABEL JADWAL --}}
                @if($show_jadwal && count($jadwal_angsuran) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">Tabel Jadwal Angsuran</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Jatuh Tempo</th>
                                        @if($metode_perhitungan === 'Anuitas')<th>Sisa Pinjaman (Rp)</th>@endif
                                        <th>Pokok (Rp)</th>
                                        <th>Margin (Rp)</th>
                                        <th>Total Cicilan (Rp)</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwal_angsuran as $item)
                                    <tr class="{{ $item['is_grace_period'] ? 'table-warning' : '' }}">
                                        <td>{{ $item['no'] }}</td>
                                        <td>{{ $item['tanggal_jatuh_tempo'] }}</td>
                                        @if($metode_perhitungan === 'Anuitas')<td class="text-end">Rp {{ number_format($item['sisa_pinjaman'] ?? 0, 0, ',', '.') }}</td>@endif
                                        <td class="text-end">{{ number_format($item['pokok'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($item['margin'], 0, ',', '.') }}</td>
                                        <td class="text-end"><strong>{{ number_format($item['total_cicilan'], 0, ',', '.') }}</strong></td>
                                        <td>{{ $item['catatan'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="2">Total</th>
                                        @if($metode_perhitungan === 'Anuitas')<th></th> @endif
                                        <th class="text-end">{{ number_format($total_pokok, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($total_margin, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($total_cicilan, 0, ',', '.') }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card bg-lighter">
                                    <div class="card-body">
                                        <h6 class="card-title">Ringkasan Metode Flat</h6>
                                        <ul class="list-unstyled mb-0">
                                            <li>Total Margin: Rp {{ number_format($total_margin, 0, ',', '.') }}</li>
                                            <li>Total Pokok: Rp {{ number_format($total_pokok, 0, ',', '.') }}</li>
                                            <li><strong>Total Dibayar: Rp {{ number_format($total_cicilan, 0, ',', '.') }}</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- TOMBOL SIMPAN --}}
                <div class="row mt-4">
                    <div class="col-12">
                        @if($show_jadwal && count($jadwal_angsuran) > 0)
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="simpan">
                                    <i class="ti ti-device-floppy me-1"></i>{{ $submitLabel ?? 'Simpan Program Restrukturisasi' }}
                                </span>
                                <span wire:loading wire:target="simpan">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Menyimpan...
                                </span>
                            </button>
                        @else
                            <div class="alert alert-warning d-inline-block mb-0 py-2 px-3">
                                <i class="ti ti-alert-circle me-1"></i>
                                <strong>Perhatian:</strong> Silakan klik tombol <b>"Hitung Jadwal Angsuran"</b> di atas untuk melanjutkan penyimpanan.
                            </div>
                        @endif

                        <a href="{{ route('program-restrukturisasi.index') }}" class="btn btn-secondary ms-2">
                            <i class="ti ti-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
        
        // SweetAlert Modal Listener
        Livewire.on('swal:modal', (data) => {
            const options = data[0]; 
            
            Swal.fire({
                icon: options.type,
                title: options.title,
                text: options.text,
                showCancelButton: false, 
                showDenyButton: false,
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed && options.redirect_url) {
                    window.location.href = options.redirect_url;
                }
            });
        });
    });
</script>
@endpush