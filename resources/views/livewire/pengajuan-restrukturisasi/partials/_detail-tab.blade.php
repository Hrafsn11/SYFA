<div class="tab-pane fade show active" id="detail-restrukturisasi" role="tabpanel">
    <div class="d-flex justify-content-between items-center mt-4 mb-3 mb-md-4 flex-wrap gap-2">
        <h5 class="mb-3 mb-md-4">Detail Pinjaman</h5>
        <div class="d-flex gap-2">
            @can('pengajuan_restrukturisasi.ajukan_restrukturisasi')
                @php
                    $isDraft = ($pengajuan->status ?? '') === 'Draft';
                    $isPerbaikanDokumen = ($pengajuan->status ?? '') === 'Perbaikan Dokumen';
                    $currentStep = $pengajuan->current_step ?? 1;
                @endphp
                @if($isDraft || ($isPerbaikanDokumen && $currentStep == 1))
                    <button type="button" class="btn btn-success" onclick="submitPengajuan()">
                        <i class="fas fa-paper-plane me-2"></i>
                        {{ $isPerbaikanDokumen ? 'Submit Ulang Pengajuan' : 'Submit Pengajuan' }}
                    </button>
                @endif
            @endcan
            @can('pengajuan_restrukturisasi.validasi_dokumen')
                @if(isset($pengajuan) && ($pengajuan->current_step ?? 1) == 2 && !in_array($pengajuan->status ?? '', ['Draft','Ditolak']))
                    <button type="button" class="btn btn-primary btn-approve" id="btnSetujuiPeminjaman" onclick="handleApprove(2)">
                        <i class="fas fa-check me-2"></i>
                        Setujui Restrukturisasi
                    </button>
                @endif
            @endcan
            @can('pengajuan_restrukturisasi.persetujuan_ceo_ski')
                @if(isset($pengajuan) && ($pengajuan->current_step ?? 1) == 3 && !in_array($pengajuan->status ?? '', ['Draft','Ditolak']))
                    <button type="button" class="btn btn-warning btn-approve" id="btnPersetujuanCEO" onclick="handleApprove(3)">
                        <i class="fas fa-crown me-2"></i>
                        Setujui Restrukturisasi
                    </button>
                @endif
            @endcan
            @can('pengajuan_restrukturisasi.persetujuan_direktur')
                @if(isset($pengajuan) && ($pengajuan->current_step ?? 1) == 4 && !in_array($pengajuan->status ?? '', ['Draft','Ditolak']))
                    <button type="button" class="btn btn-info btn-approve" id="btnPersetujuanDirektur" onclick="handleApprove(4)">
                        <i class="fas fa-briefcase me-2"></i>
                        Setujui Restrukturisasi
                    </button>
                @endif
            @endcan
        </div>
    </div>

    <hr class="my-3 my-md-4">

    <div class="row">
        <div class="col-md-6">
            <table class="table table-borderless table-sm">
                <tbody>
                    <tr>
                        <td class="text-nowrap" style="width: 35%;"><strong>Nama Perusahaan:</strong></td>
                        <td>{{ $pengajuan->nama_perusahaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>NPWP:</strong></td>
                        <td>{{ $pengajuan->npwp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Nama PIC:</strong></td>
                        <td>{{ $pengajuan->nama_pic ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Jabatan PIC:</strong></td>
                        <td>{{ $pengajuan->jabatan_pic ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Kontrak Pembiayaan:</strong></td>
                        <td>{{ $pengajuan->nomor_kontrak_pembiayaan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Jenis Pembiayaan:</strong></td>
                        <td>
                            @if ($pengajuan->jenis_pembiayaan)
                                <span class="badge bg-label-primary">{{ $pengajuan->jenis_pembiayaan }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-borderless table-sm">
                <tbody>
                    <tr>
                        <td class="text-nowrap" style="width: 40%;"><strong>Plafon Awal:</strong></td>
                        <td>{{ $pengajuan->jumlah_plafon_awal ? 'Rp ' . number_format($pengajuan->jumlah_plafon_awal, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Sisa Pokok Belum Bayar:</strong></td>
                        <td class="fw-semibold">
                            {{ $pengajuan->sisa_pokok_belum_dibayar ? 'Rp ' . number_format($pengajuan->sisa_pokok_belum_dibayar, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Tunggakan Margin/Bunga:</strong></td>
                        <td class="{{ $pengajuan->tunggakan_margin_bunga > 0 ? 'text-danger fw-semibold' : '' }}">
                            {{ $pengajuan->tunggakan_margin_bunga ? 'Rp ' . number_format($pengajuan->tunggakan_margin_bunga, 0, ',', '.') : 'Rp 0' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Jatuh Tempo Terakhir:</strong></td>
                        <td>{{ $pengajuan->jatuh_tempo_terakhir ? \Carbon\Carbon::parse($pengajuan->jatuh_tempo_terakhir)->format('d/m/Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Status Saat Ini (DPD):</strong></td>
                        <td>
                            @if($pengajuan->status_dpd !== null)
                                @if($pengajuan->status_dpd > 0)
                                    <span class="badge bg-label-{{ $pengajuan->status_dpd > 90 ? 'danger' : ($pengajuan->status_dpd > 30 ? 'warning' : 'info') }}">
                                        {{ $pengajuan->status_dpd }} Hari
                                    </span>
                                @else
                                    <span class="badge bg-label-success">0 Hari (Lancar)</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-nowrap"><strong>Tanggal Pengajuan:</strong></td>
                        <td>{{ $pengajuan->created_at ? $pengajuan->created_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-12 mt-3">
            <div class="alert alert-secondary py-2" role="alert">
                <p class="mb-1"><strong>Alasan Restrukturisasi:</strong></p>
                <small>{{ $pengajuan->alasan_restrukturisasi ?? 'Tidak ada keterangan' }}</small>
            </div>
            <div class="alert alert-secondary py-2" role="alert">
                <p class="mb-1"><strong>Rencana Pemulihan Usaha:</strong></p>
                <small>{{ $pengajuan->rencana_pemulihan_usaha ?? 'Tidak ada keterangan' }}</small>
            </div>
            <div class="alert alert-secondary py-2" role="alert">
                <p class="mb-2"><strong>Jenis Restrukturisasi yang Diajukan:</strong></p>
                @if ($pengajuan->jenis_restrukturisasi && count($pengajuan->jenis_restrukturisasi) > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($pengajuan->jenis_restrukturisasi as $jenis)
                            @php
                                $badgeColor = 'primary';
                                if (stripos($jenis, 'penurunan') !== false) {
                                    $badgeColor = 'success';
                                } elseif (stripos($jenis, 'perpanjangan') !== false) {
                                    $badgeColor = 'info';
                                } elseif (stripos($jenis, 'pengurangan') !== false) {
                                    $badgeColor = 'warning';
                                } elseif (stripos($jenis, 'lainnya') !== false) {
                                    $badgeColor = 'secondary';
                                }
                            @endphp
                            <span class="badge bg-label-{{ $badgeColor }}">{{ $jenis }}</span>
                        @endforeach
                    </div>
                @else
                    <small class="text-muted">Belum ada jenis restrukturisasi yang dipilih</small>
                @endif
            </div>
        </div>
    </div>

    @if(isset($pengajuan) && $pengajuan->current_step >= 2)
        @include('livewire.pengajuan-restrukturisasi.partials._evaluasi-forms')
    @endif
</div>
