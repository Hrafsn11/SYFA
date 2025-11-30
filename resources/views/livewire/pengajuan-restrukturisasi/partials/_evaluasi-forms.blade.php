@php
    $isEditable = isset($pengajuan) && $pengajuan->current_step == 2;
    $evaluasi = null;
    if (isset($pengajuan) && $pengajuan->current_step >= 3) {
        $evaluasi = \App\Models\EvaluasiPengajuanRestrukturisasi::where('id_pengajuan_restrukturisasi', $pengajuan->id_pengajuan_restrukturisasi)->first();
    }
@endphp

{{-- Section A: Kelengkapan Dokumen --}}
<div class="card mb-4">
    <h5 class="card-header">A. Kelengkapan Dokumen</h5>
    <div class="card-body">
        <p class="text-muted">
            Verifikasi bahwa setiap dokumen yang dipersyaratkan sudah lengkap dan sah.
            @if($isEditable)
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
                        @if($isEditable)
                            <th class="text-center" style="width: 20%;">Verifikasi Kelengkapan</th>
                            <th style="width: 25%;">Catatan Evaluator</th>
                        @else
                            <th class="text-center" style="width: 15%;">Status</th>
                            <th style="width: 30%;">Catatan</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="table-kelengkapan-body">
                    @php
                        $dokumen = [
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
                    @foreach ($dokumen as $index => $dok)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $dok['label'] }}</td>
                            <td class="text-center">
                                @if ($pengajuan->{$dok['field']})
                                    <a href="{{ Storage::url($pengajuan->{$dok['field']}) }}" target="_blank"
                                        class="text-success">
                                        <i class="ti ti-file-text me-1"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $kelengkapanRow = $evaluasi ? $evaluasi->kelengkapanDokumen()->where('nama_dokumen', $dok['label'])->first() : null;
                                    $statusValue = $kelengkapanRow->status ?? '';
                                @endphp
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                        name="kelengkapan_{{ $index }}" value="Ya"
                                        {{ $statusValue === 'Ya' ? 'checked' : '' }}
                                        {{ !$isEditable ? 'disabled' : '' }}>
                                    <label class="form-check-label">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                        name="kelengkapan_{{ $index }}" value="Tidak"
                                        {{ $statusValue === 'Tidak' ? 'checked' : '' }}
                                        {{ !$isEditable ? 'disabled' : '' }}>
                                    <label class="form-check-label">Tidak</label>
                                </div>
                            </td>
                            <td>
                                @php
                                    $catatanValue = $kelengkapanRow->catatan ?? '';
                                @endphp
                                <textarea class="form-control form-control-sm" rows="1" 
                                    name="catatan_kelengkapan_{{ $index }}"
                                    placeholder="Catatan"
                                    {{ !$isEditable ? 'readonly' : '' }}>{{ $catatanValue }}</textarea>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('livewire.pengajuan-restrukturisasi.partials._evaluasi-sections-bcd')

{{-- Section E: Komite (Pure HTML + JavaScript, NO Livewire) --}}
<div class="card mb-4">
    <h5 class="card-header">E. Persetujuan Komite Kredit</h5>
    <div class="card-body">
        <p class="text-muted">Tambahkan anggota Komite Kredit dan catat persetujuan mereka.</p>
        <div id="committee-container">
            @if(!$isEditable && $evaluasi)
                @php
                    $komite = $evaluasi->persetujuanKomite()->get();
                @endphp
                @foreach($komite as $k)
                <div class="approval-row row g-3 p-3 mb-3 border rounded">
                    <div class="col-md-3">
                        <label class="form-label small">Nama Anggota</label>
                        <input type="text" class="form-control" value="{{ $k->nama_anggota }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Jabatan</label>
                        <input type="text" class="form-control" value="{{ $k->jabatan }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Tanggal</label>
                        <input type="text" class="form-control" value="{{ $k->tanggal_persetujuan ? \Carbon\Carbon::parse($k->tanggal_persetujuan)->format('d M Y') : '-' }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">TTD Digital</label>
                        @if($k->ttd_digital)
                            <a href="{{ Storage::url($k->ttd_digital) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                <i class="ti ti-eye me-1"></i> Lihat TTD
                            </a>
                        @else
                            <input type="text" class="form-control" value="-" readonly>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        @if($isEditable)
            <button type="button" class="btn btn-primary" onclick="addCommitteeRow()">
                <i class="bx bx-plus me-1"></i> Tambah Anggota Komite
            </button>
        @endif
    </div>
</div>

{{-- Save Button --}}
@if($isEditable)
    <div class="d-flex gap-2 mb-4">
        @can('pengajuan_restrukturisasi.validasi_dokumen')
            <button type="button" class="btn btn-success" id="btn-save-evaluasi">
                <i class="fas fa-save me-2"></i>
                Simpan Evaluasi
            </button>
        @endcan
    </div>

@endif
