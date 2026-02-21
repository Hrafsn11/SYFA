@php
    $dokumenList = [
        [
            'label' => 'KTP PIC',
            'field' => 'dokumen_ktp_pic',
            'accept' => '.pdf,.jpg,.jpeg,.png',
            'hint' => 'PDF, JPG, PNG (Max: 2MB)',
            'required' => true,
        ],
        [
            'label' => 'NPWP Perusahaan',
            'field' => 'dokumen_npwp_perusahaan',
            'accept' => '.pdf,.jpg,.jpeg,.png',
            'hint' => 'PDF, JPG, PNG (Max: 2MB)',
            'required' => true,
        ],
        [
            'label' => 'Laporan Keuangan',
            'field' => 'dokumen_laporan_keuangan',
            'accept' => '.pdf,.xlsx,.xls',
            'hint' => 'PDF, XLSX, XLS (Max: 5MB)',
            'required' => true,
        ],
        [
            'label' => 'Proyeksi Arus Kas',
            'field' => 'dokumen_arus_kas',
            'accept' => '.pdf,.xlsx,.xls',
            'hint' => 'PDF, XLSX, XLS (Max: 5MB)',
            'required' => true,
        ],
        [
            'label' => 'Bukti Kondisi Eksternal',
            'field' => 'dokumen_kondisi_eksternal',
            'accept' => '.pdf,.jpg,.jpeg,.png',
            'hint' => 'PDF, JPG, PNG (Max: 2MB)',
            'required' => true,
        ],
        [
            'label' => 'Kontrak Pembiayaan',
            'field' => 'dokumen_kontrak_pembiayaan',
            'accept' => '.pdf',
            'hint' => 'PDF (Max: 5MB)',
            'required' => true,
        ],
        [
            'label' => 'Dokumen Lainnya',
            'field' => 'dokumen_lainnya',
            'accept' => '.pdf,.jpg,.jpeg,.png,.xlsx,.xls',
            'hint' => 'PDF, JPG, PNG, XLSX, XLS (Max: 5MB)',
            'required' => false,
        ],
        [
            'label' => 'Tanda Tangan Digital',
            'field' => 'dokumen_tanda_tangan',
            'accept' => '.jpg,.jpeg,.png',
            'hint' => 'JPG, PNG (Max: 2MB)',
            'required' => true,
        ],
    ];

    $latestRejection = $histories->where('status', 'Perbaikan Dokumen')->first();

    // Get evaluasi data for catatan
    $evaluasi = \App\Models\EvaluasiPengajuanCicilan::where(
        'id_pengajuan_cicilan',
        $pengajuan->id_pengajuan_cicilan,
    )->first();
@endphp

{{-- Section: Perbaikan Dokumen --}}
<div class="card mb-4 shadow-none">
    <h5 class="card-header">A. Kelengkapan Dokumen</h5>
    <div class="card-body">
        @if (!$isEditDokumenMode)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <div>
                        <strong>Perbaikan Dokumen Diperlukan</strong>
                        <p class="mb-0 small">Pengajuan Anda memerlukan perbaikan dokumen. Silakan klik tombol "Edit Dokumen" untuk mengupload ulang dokumen yang diperlukan.</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($latestRejection && $latestRejection->catatan)
            <div class="alert alert-danger mb-3" role="alert">
                <div class="d-flex align-items-start">
                    <i class="ti ti-message-circle me-2 mt-1"></i>
                    <div>
                        <strong>Catatan dari Evaluator:</strong>
                        <p class="mb-0 mt-1">{{ $latestRejection->catatan }}</p>
                    </div>
                </div>
            </div>
        @endif

        <p class="text-muted">
            Upload ulang dokumen yang diperlukan untuk melengkapi pengajuan restrukturisasi.
            @if ($isEditDokumenMode)
                <span class="text-danger fw-bold">*Pilih file baru untuk dokumen yang ingin diperbarui.</span>
            @endif
        </p>

        @if ($isEditDokumenMode)
            <form id="formUpdateDokumen" enctype="multipart/form-data">
                @csrf
        @endif

        <div class="table-responsive">
            <table class="table table-bordered {{ $isEditDokumenMode ? 'table-hover' : 'table-sm' }}">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: {{ $isEditDokumenMode ? '20%' : '25%' }};">Item Dokumen</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th class="text-center" style="width: 10%;">Lihat</th>
                        <th style="width: {{ $isEditDokumenMode ? '20%' : '50%' }};">Catatan Evaluator</th>
                        @if ($isEditDokumenMode)
                            <th style="width: 35%;">Upload Baru</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dokumenList as $index => $dok)
                        @php
                            $hasDocument = !empty($pengajuan->{$dok['field']});
                            $kelengkapanRow = $evaluasi
                                ? $evaluasi->kelengkapanDokumen()->where('nama_dokumen', $dok['label'])->first()
                                : null;
                            $statusValue = $kelengkapanRow->status ?? '';
                            $catatanValue = $kelengkapanRow->catatan ?? '';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $dok['label'] }}
                                @if ($dok['required'])
                                    <span class="text-danger">*</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($statusValue)
                                    <span class="badge bg-label-{{ $statusValue === 'Ya' ? 'success' : 'danger' }}">
                                        {{ $statusValue }}
                                    </span>
                                @elseif ($hasDocument)
                                    <span class="badge bg-label-success">
                                        <i class="ti ti-check me-1"></i>Uploaded
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">
                                        <i class="ti ti-x me-1"></i>Belum ada
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($hasDocument)
                                    <a href="{{ Storage::url($pengajuan->{$dok['field']}) }}" target="_blank"
                                        class="text-primary">
                                        <i class="ti ti-file-text me-1"></i>Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($catatanValue)
                                    <div class="text-muted small">{{ $catatanValue }}</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            @if ($isEditDokumenMode)
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="file" class="form-control form-control-sm"
                                            id="{{ $dok['field'] }}" name="{{ $dok['field'] }}"
                                            accept="{{ $dok['accept'] }}">
                                    </div>
                                    <small class="text-muted">{{ $dok['hint'] }}</small>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($isEditDokumenMode)
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['edit-dokumen' => null]) }}" class="btn btn-secondary">
                        <i class="ti ti-x me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnSimpanDokumen">
                        <span id="btnSimpanDokumenText"><i class="ti ti-device-floppy me-2"></i>Simpan Dokumen</span>
                        <span class="spinner-border spinner-border-sm d-none" id="btnSimpanDokumenSpinner"></span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
