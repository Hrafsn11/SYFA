@php
    // Status checks
    $currentStatus = $pengajuan->status ?? '';
    $isPerluEvaluasiUlang = $currentStatus === 'Perlu Evaluasi Ulang';

    $isEditModeRequested = request()->query('edit') === 'true';

    $isEditable =
        isset($pengajuan) &&
        ($pengajuan->current_step ?? 1) == 2 &&
        auth()->user()->can('pengajuan_cicilan.validasi_dokumen') &&
        (!$evaluasi || ($isPerluEvaluasiUlang && $isEditModeRequested));

    $showEditButton = $isPerluEvaluasiUlang && !$isEditModeRequested && $evaluasi;

    $dokumenList = [
        ['label' => 'KTP PIC', 'field' => 'dokumen_ktp_pic'],
        ['label' => 'NPWP Perusahaan', 'field' => 'dokumen_npwp_perusahaan'],
        ['label' => 'Laporan Keuangan', 'field' => 'dokumen_laporan_keuangan'],
        ['label' => 'Proyeksi Arus Kas', 'field' => 'dokumen_arus_kas'],
        ['label' => 'Bukti Kondisi Eksternal', 'field' => 'dokumen_kondisi_eksternal'],
        ['label' => 'Kontrak Pembiayaan', 'field' => 'dokumen_kontrak_pembiayaan'],
        ['label' => 'Dokumen Lainnya', 'field' => 'dokumen_lainnya'],
        ['label' => 'Tanda Tangan Digital', 'field' => 'dokumen_tanda_tangan'],
    ];
@endphp

{{-- Section A: Kelengkapan Dokumen --}}
<div class="card mb-4">
    <h5 class="card-header">A. Kelengkapan Dokumen</h5>
    <div class="card-body">
        @if (!$isEditable && $evaluasi)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-info-circle me-2"></i>
                    <div>
                        <strong>Evaluasi telah disimpan</strong>
                        <p class="mb-0 small">Data evaluasi ini sudah tersimpan dan tidak dapat diubah lagi. Silakan
                            lanjutkan ke tahap persetujuan.</p>
                    </div>
                </div>
            </div>
        @endif
        <p class="text-muted">
            Verifikasi bahwa setiap dokumen yang dipersyaratkan sudah lengkap dan sah.
            @if ($isEditable)
                <span class="text-danger fw-bold">*Harap isi kolom Catatan jika memilih 'Tidak'.</span>
            @endif
        </p>
        <div class="table-responsive">
            <table class="table table-bordered {{ $isEditable ? 'table-hover' : 'table-sm' }}">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 35%;">Item Dokumen</th>
                        <th class="text-center" style="width: 15%;">Tautan/Status Upload</th>
                        @if ($isEditable)
                            <th class="text-center" style="width: 20%;">Verifikasi Kelengkapan</th>
                            <th style="width: 25%;">Catatan Evaluator</th>
                        @else
                            <th class="text-center" style="width: 15%;">Status</th>
                            <th style="width: 30%;">Catatan</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="table-kelengkapan-body">
                    @foreach ($dokumenList as $index => $dok)
                        @php
                            $kelengkapanRow = $evaluasi
                                ? $evaluasi->kelengkapanDokumen()->where('nama_dokumen', $dok['label'])->first()
                                : null;
                            $statusValue = $kelengkapanRow->status ?? '';
                            $catatanValue = $kelengkapanRow->catatan ?? '';
                            $hasDocument = !empty($pengajuan->{$dok['field']});
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $dok['label'] }}</td>
                            <td class="text-center">
                                @if ($hasDocument)
                                    <a href="{{ Storage::url($pengajuan->{$dok['field']}) }}" target="_blank"
                                        class="text-success">
                                        <i class="ti ti-file-text me-1"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($isEditable)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                            name="kelengkapan_{{ $index }}" value="Ya"
                                            {{ $statusValue === 'Ya' ? 'checked' : '' }}>
                                        <label class="form-check-label">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                            name="kelengkapan_{{ $index }}" value="Tidak"
                                            {{ $statusValue === 'Tidak' ? 'checked' : '' }}>
                                        <label class="form-check-label">Tidak</label>
                                    </div>
                                @else
                                    @if ($statusValue)
                                        <span class="badge bg-label-{{ $statusValue === 'Ya' ? 'success' : 'danger' }}">
                                            {{ $statusValue }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($isEditable)
                                    <textarea class="form-control form-control-sm" rows="1" name="catatan_kelengkapan_{{ $index }}"
                                        placeholder="Catatan evaluator...">{{ $catatanValue }}</textarea>
                                @else
                                    <div class="text-muted small">{{ $catatanValue ?: '-' }}</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('livewire.pengajuan-cicilan.partials._evaluasi-sections-bcd')

{{-- Section E: Persetujuan Komite Kredit --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">E. Persetujuan Komite Kredit</h5>
        @if ($isEditable)
            <button type="button" class="btn btn-primary" onclick="addCommitteeRow()">
                <i class="ti ti-plus me-1"></i>
                Tambah Anggota
            </button>
        @endif
    </div>
    <div class="card-body">
        @if ($isEditable)
            <p class="text-muted mb-4">Tambahkan anggota Komite Kredit yang menyetujui restrukturisasi ini.</p>
            <div id="committee-container">
            </div>
        @else
            {{-- READ-ONLY MODE --}}
            @if ($evaluasi)
                @php
                    $komite = $evaluasi->persetujuanKomite()->get();
                @endphp
                @if ($komite->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th style="width: 25%;">Nama Anggota</th>
                                    <th style="width: 25%;">Jabatan</th>
                                    <th class="text-center" style="width: 20%;">Tanggal Persetujuan</th>
                                    <th class="text-center" style="width: 25%;">TTD Digital</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($komite as $index => $k)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>{{ $k->nama_anggota }}</td>
                                        <td>{{ $k->jabatan }}</td>
                                        <td class="text-center">
                                            {{ $k->tanggal_persetujuan ? \Carbon\Carbon::parse($k->tanggal_persetujuan)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            @if ($k->ttd_digital)
                                                <a href="{{ Storage::url($k->ttd_digital) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    Lihat TTD
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Tidak ada data komite kredit.</p>
                @endif
            @else
                <p class="text-muted mb-0">Data komite kredit belum tersedia.</p>
            @endif
        @endif
    </div>
</div>

{{-- Save Button --}}
@if ($isEditable)
    <div class="d-flex justify-content-end gap-2">
        @if ($isPerluEvaluasiUlang)
            <a href="{{ request()->url() }}" class="btn btn-outline-secondary">
                Batal
            </a>
        @endif
        @can('pengajuan_cicilan.validasi_dokumen')
            <button type="button" class="btn btn-success" id="btn-save-evaluasi">
                Simpan Evaluasi
            </button>
        @endcan
    </div>
@endif
