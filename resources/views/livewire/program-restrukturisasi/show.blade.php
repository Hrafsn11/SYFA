@php
    use Illuminate\Support\Facades\Storage;
    $pengajuan = $program->pengajuanRestrukturisasi;
    $debitur = $pengajuan?->debitur?->nama ?? ($pengajuan?->nama_perusahaan ?? '-');
    $nomorKontrakPembiayaan = $pengajuan?->nomor_kontrak_pembiayaan ?? '-';

    // Status kontrak
    $isKontrakGenerated = !is_null($program->kontrak_generated_at);
    $statusProgram = $program->status ?? 'Menunggu Generate Kontrak';
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                Detail Program Restrukturisasi
                @if($statusProgram === 'Berjalan')
                    <span class="badge bg-success ms-2">Berjalan</span>
                @elseif($statusProgram === 'Selesai')
                    <span class="badge bg-primary ms-2">Selesai</span>
                @else
                    <span class="badge bg-warning ms-2">Menunggu Generate Kontrak</span>
                @endif
            </h4>
            <p class="text-muted mb-0">Informasi lengkap beserta jadwal angsuran</p>
        </div>
        <div class="d-flex gap-2">
            @if(!$isKontrakGenerated)
                @can('program_restrukturisasi.generate_kontrak')
                    <a href="{{ route('program-restrukturisasi.generate-kontrak', $program->id_program_restrukturisasi) }}"
                        class="btn btn-success">
                        <i class="ti ti-file-check me-1"></i>Generate Kontrak
                    </a>
                @endcan
            @else
                <a href="{{ route('program-restrukturisasi.edit', $program->id_program_restrukturisasi) }}"
                    class="btn btn-outline-warning">
                    <i class="ti ti-pencil me-1"></i>Edit Program
                </a>
            @endif
            <a href="{{ route('program-restrukturisasi.index') }}" class="btn btn-outline-primary">
                <i class="ti ti-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Alert jika kontrak belum di-generate --}}
    @if(!$isKontrakGenerated)
        <div class="alert alert-warning mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-triangle me-2" style="font-size: 1.5rem;"></i>
                <div>
                    <h6 class="alert-heading mb-1">Kontrak Belum Di-generate</h6>
                    <p class="mb-0">
                        Silakan generate kontrak terlebih dahulu untuk mengubah status program menjadi "Berjalan" dan
                        mengaktifkan fitur pembayaran cicilan.
                        <a href="{{ route('program-restrukturisasi.generate-kontrak', $program->id_program_restrukturisasi) }}"
                            class="alert-link">
                            Klik di sini untuk generate kontrak →
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endif

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
                        <small class="text-muted d-block">Nomor Kontrak Pembiayaan Awal</small>
                        <span class="fw-semibold text-heading">{{ $nomorKontrakPembiayaan }}</span>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Metode Perhitungan</small>
                        <span class="badge bg-info">{{ $program->metode_perhitungan ?? '-' }}</span>
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

    {{-- Detail Kontrak Card --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="ti ti-file-certificate me-2"></i>Detail Kontrak Restrukturisasi
            </h5>
            @if($isKontrakGenerated)
                <a href="{{ route('program-restrukturisasi.preview-kontrak', $program->id_program_restrukturisasi) }}"
                    target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-eye me-1"></i>Lihat Kontrak
                </a>
            @endif
        </div>
        <div class="card-body">
            @if($isKontrakGenerated)
                <div class="row g-3">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Nomor Kontrak Restrukturisasi</small>
                        <span class="fw-bold text-primary">{{ $program->nomor_kontrak_restrukturisasi }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Tanggal Kontrak Di-generate</small>
                        <span class="fw-semibold">{{ $program->kontrak_generated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-success">Kontrak Aktif</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Jaminan</small>
                        <span class="fw-semibold">{{ $program->jaminan ?? '-' }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="ti ti-file-off display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">Kontrak Belum Di-generate</h5>
                    <p class="text-muted mb-3">
                        Silakan generate kontrak untuk mengaktifkan program restrukturisasi ini.
                    </p>
                    <a href="{{ route('program-restrukturisasi.generate-kontrak', $program->id_program_restrukturisasi) }}"
                        class="btn btn-success">
                        <i class="ti ti-file-check me-2"></i>Generate Kontrak Sekarang
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Ringkasan Perhitungan</h6>
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
                <span class="fw-bold">Rp {{ number_format($program->total_cicilan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Jadwal Angsuran</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th style="min-width: 150px;">Tanggal Jatuh Tempo</th>
                            @if ($program->metode_perhitungan === 'Efektif (Anuitas)')
                                <th class="text-end" style="min-width: 130px;">Sisa Pinjaman (Rp)</th>
                            @endif
                            <th class="text-end" style="min-width: 120px;">Pokok (Rp)</th>
                            <th class="text-end" style="min-width: 120px;">Margin (Rp)</th>
                            <th class="text-end" style="min-width: 130px;">Total Cicilan (Rp)</th>
                            <th class="text-center" style="min-width: 100px;">Status</th>
                            <th style="min-width: 150px;">Catatan</th>
                            <th class="text-center" style="min-width: 150px;">Bukti Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $index => $item)
                            @php
                                $sisaPinjaman = isset($jadwalWithSisa[$index]['sisa_pinjaman'])
                                    ? $jadwalWithSisa[$index]['sisa_pinjaman']
                                    : null;
                            @endphp
                            <tr class="{{ $item->is_grace_period ? 'table-warning' : '' }}">
                                <td class="text-center">{{ $item->no }}</td>
                                <td>{{ optional($item->tanggal_jatuh_tempo)->format('d/m/Y') ?? '-' }}</td>
                                @if ($program->metode_perhitungan === 'Efektif (Anuitas)')
                                    <td class="text-end">{{ number_format($sisaPinjaman ?? 0, 0, ',', '.') }}</td>
                                @endif
                                <td class="text-end">{{ number_format($item->pokok, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($item->margin, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">{{ number_format($item->total_cicilan, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if ($item->status === 'Lunas')
                                        <span class="badge bg-success">{{ $item->status }}</span>
                                    @elseif($item->status === 'Jatuh Tempo')
                                        <span class="badge bg-danger">{{ $item->status }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->catatan ?? '-' }}</td>
                                <td>
                                    @if (!empty($item->bukti_pembayaran))
                                        <div class="d-flex flex-column gap-1">
                                            <a href="{{ Storage::url($item->bukti_pembayaran) }}" target="_blank"
                                                class="btn btn-sm btn-info">
                                                <i class="ti ti-eye me-1"></i>Lihat Bukti
                                            </a>
                                            @if ($item->tanggal_bayar)
                                                <small class="text-muted">
                                                    Dibayar: {{ optional($item->tanggal_bayar)->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $program->metode_perhitungan === 'Efektif (Anuitas)' ? 9 : 8 }}"
                                    class="text-center text-muted">Belum ada jadwal angsuran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>