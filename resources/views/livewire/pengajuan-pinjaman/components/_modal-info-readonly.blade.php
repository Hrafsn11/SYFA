{{-- Modal Info Readonly - Data yang ditampilkan di modal approval --}}
<div class="card border mb-3 shadow-none">
    <div class="card-body">
        {{-- Deviasi --}}
        <div class="row mb-3">
            <div class="col-12">
                <label class="form-label text-muted small">Deviasi</label>
                <div class="d-flex gap-3 align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" 
                               {{ $latestHistory?->deviasi === 'ya' ? 'checked' : '' }} disabled>
                        <label class="form-check-label">
                            <i class="ti ti-check-circle text-success me-1"></i> Ya
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" 
                               {{ $latestHistory?->deviasi === 'tidak' ? 'checked' : '' }} disabled>
                        <label class="form-check-label">
                            <i class="ti ti-x-circle text-danger me-1"></i> Tidak
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Nominal --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-muted small">Nominal Pengajuan</label>
                <input type="text" class="form-control" 
                       value="{{ $this->formatRupiah($nominal_pinjaman) }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Nominal Disetujui</label>
                <input type="text" class="form-control" 
                       value="{{ $this->formatRupiah($latestHistory?->nominal_yang_disetujui ?? $nominal_yang_disetujui) }}" disabled>
            </div>
        </div>
        
        {{-- Bunga --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label text-muted small">Persentase Bunga</label>
                <input type="text" class="form-control" 
                       value="{{ $latestHistory?->persentase_bunga ?? $persentase_bunga ?? 2 }}%" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Total Bunga</label>
                @php
                    $totalBagiHasilCalc = $latestHistory?->total_bunga 
                        ?? $total_bunga 
                        ?? (($latestHistory?->nominal_yang_disetujui ?? $nominal_yang_disetujui ?? 0) * (($latestHistory?->persentase_bunga ?? $persentase_bunga ?? 2) / 100));
                @endphp
                <input type="text" class="form-control" 
                       value="{{ $this->formatRupiah($totalBagiHasilCalc) }}" disabled>
            </div>
        </div>
        
        {{-- Tanggal --}}
        <div class="row">
            <div class="col-md-6">
                <label class="form-label text-muted small">Tanggal Pencairan</label>
                <div class="input-group">
                    <input type="text" class="form-control" 
                           value="{{ $this->formatTanggal($latestHistory?->tanggal_pencairan ?? $tanggal_pencairan) }}" disabled>
                    <span class="input-group-text">
                        <i class="ti ti-calendar"></i>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Tanggal Pencairan yang Diharapkan</label>
                <div class="input-group">
                    <input type="text" class="form-control" 
                           value="{{ $this->formatTanggal($harapan_tanggal_pencairan) }}" disabled>
                    <span class="input-group-text">
                        <i class="ti ti-calendar"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
