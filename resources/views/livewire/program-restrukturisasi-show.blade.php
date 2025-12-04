@php
    $pengajuan = $program->pengajuanRestrukturisasi;
    $debitur = $pengajuan?->debitur?->nama ?? $pengajuan?->nama_perusahaan ?? '-';
    $nomorKontrak = $pengajuan?->nomor_kontrak_pembiayaan ?? '-';
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
<div>
            <h4 class="fw-bold mb-1">Detail Program Restrukturisasi</h4>
            <p class="text-muted mb-0">Informasi lengkap beserta jadwal angsuran</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('program-restrukturisasi.edit', $program->id_program_restrukturisasi) }}"
                class="btn btn-warning">
                <i class="ti ti-pencil me-1"></i>Edit Program
            </a>
            <a href="{{ route('program-restrukturisasi.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Debitur</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Nama Debitur</small>
                        <span class="fw-semibold text-heading">{{ $debitur }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Nomor Kontrak</small>
                        <span class="fw-semibold text-heading">{{ $nomorKontrak }}</span>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Metode Perhitungan</small>
                        <span class="badge bg-info">{{ $program->metode_perhitungan }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Parameter Program</h5>
                </div>
                <div class="card-body row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Plafon Pembiayaan</small>
                        <p class="fw-semibold mb-0">Rp {{ number_format($program->plafon_pembiayaan, 0, ',', '.') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Suku Bunga</small>
                        <p class="fw-semibold mb-0">{{ number_format($program->suku_bunga_per_tahun, 2) }}%</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Jangka Waktu</small>
                        <p class="fw-semibold mb-0">{{ $program->jangka_waktu_total }} bulan</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Masa Tenggang</small>
                        <p class="fw-semibold mb-0">{{ $program->masa_tenggang }} bulan</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Tanggal Mulai Cicilan</small>
                        <p class="fw-semibold mb-0">
                            {{ optional($program->tanggal_mulai_cicilan)->format('d/m/Y') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card bg-label-info text-center">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Total Pokok</small>
                    <h4 class="mb-0">Rp {{ number_format($program->total_pokok, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card bg-label-warning text-center">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Total Margin</small>
                    <h4 class="mb-0">Rp {{ number_format($program->total_margin, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card bg-label-success text-center">
                <div class="card-body">
                    <small class="text-muted d-block mb-1">Total Cicilan</small>
                    <h4 class="mb-0">Rp {{ number_format($program->total_cicilan, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Jadwal Angsuran</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Pokok (Rp)</th>
                            <th>Margin (Rp)</th>
                            <th>Total Cicilan (Rp)</th>
                            <th>Status / Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $item)
                            <tr class="{{ $item->is_grace_period ? 'table-warning' : '' }}">
                                <td>{{ $item->no }}</td>
                                <td>{{ optional($item->tanggal_jatuh_tempo)->format('d/m/Y') ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($item->pokok, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->margin, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($item->total_cicilan, 0, ',', '.') }}</td>
                                <td>
                                    <div>{{ $item->catatan ?? '-' }}</div>
                                    <small class="text-muted">{{ $item->status }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada jadwal angsuran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
