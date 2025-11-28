<div>
    <div class="row col-12">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Pengajuan Restrukturisasi / Detail Pengajuan /</span>
            Evaluasi Pengajuan
        </h4>

        <!-- Stepper -->
        <div class="stepper-container mb-4">
            <div class="stepper-wrapper">

                <div class="stepper-item" data-step="1">
                    <div class="stepper-node">
                    </div>
                    <div class="stepper-content">
                        <div class="step-label">STEP 1</div>
                        <div class="step-name">Pengajuan Restrukturisasi</div>
                    </div>
                </div>

                <div class="stepper-item" data-step="2">
                    <div class="stepper-node">
                    </div>
                    <div class="stepper-content">
                        <div class="step-label">STEP 2</div>
                        <div class="step-name">Validasi Dokumen</div>
                    </div>
                </div>

                <div class="stepper-item" data-step="3">
                    <div class="stepper-node"></div>
                    <div class="stepper-content">
                        <div class="step-label">STEP 3</div>
                        <div class="step-name">Persetujuan CEO</div>
                    </div>
                </div>

                <div class="stepper-item" data-step="4">
                    <div class="stepper-node"></div>
                    <div class="stepper-content">
                        <div class="step-label">STEP 4</div>
                        <div class="step-name">Validasi Direktur</div>
                    </div>
                </div>

                <div class="stepper-item" data-step="5">
                    <div class="stepper-node"></div>
                    <div class="stepper-content">
                        <div class="step-label">STEP 5</div>
                        <div class="step-name">Selesai</div>
                    </div>
                </div>



            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <h5 class="card-header d-flex justify-content-between align-items-center">
                    <span>Ringkasan Data Debitur & Pengajuan</span>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="approval(this)"
                            data-status="Submit Dokumen">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit Pengajuan
                        </button>

                    </div>
                </h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    <tr>
                                        <td class="text-nowrap" style="width: 35%;"><strong>Nama Perusahaan:</strong>
                                        </td>
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
                                                <span
                                                    class="badge bg-label-primary">{{ $pengajuan->jenis_pembiayaan }}</span>
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
                                        <td class="text-nowrap"><strong>Tunggakan Pokok:</strong></td>
                                        <td
                                            class="{{ $pengajuan->tunggakan_pokok > 0 ? 'text-danger fw-semibold' : '' }}">
                                            {{ $pengajuan->tunggakan_pokok ? 'Rp ' . number_format($pengajuan->tunggakan_pokok, 0, ',', '.') : 'Rp 0' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap"><strong>Tunggakan Margin/Bunga:</strong></td>
                                        <td
                                            class="{{ $pengajuan->tunggakan_margin_bunga > 0 ? 'text-danger fw-semibold' : '' }}">
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
                                        <td class="text-warning">{{ $pengajuan->status_dpd ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-nowrap"><strong>Tanggal Pengajuan:</strong></td>
                                        <td>{{ $pengajuan->created_at ? $pengajuan->created_at->format('d/m/Y H:i') : '-' }}
                                        </td>
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
                            @if ($pengajuan->jenis_restrukturisasi && count($pengajuan->jenis_restrukturisasi) > 0)
                                <div class="alert alert-info py-3" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="ti ti-list-check me-2 mt-1" style="font-size: 1.2rem;"></i>
                                        <div class="flex-grow-1">
                                            <p class="mb-2 fw-semibold">Jenis Restrukturisasi yang Diajukan:</p>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($pengajuan->jenis_restrukturisasi as $index => $jenis)
                                                    @php
                                                        // Determine badge color based on type
                                                        $badgeColor = 'info';
                                                        if (stripos($jenis, 'penurunan') !== false) {
                                                            $badgeColor = 'success';
                                                        } elseif (stripos($jenis, 'perpanjangan') !== false) {
                                                            $badgeColor = 'primary';
                                                        } elseif (stripos($jenis, 'pengurangan') !== false) {
                                                            $badgeColor = 'warning';
                                                        } elseif (stripos($jenis, 'masa tenggang') !== false || stripos($jenis, 'grace') !== false) {
                                                            $badgeColor = 'info';
                                                        } elseif (stripos($jenis, 'penjadwalan') !== false) {
                                                            $badgeColor = 'secondary';
                                                        } elseif (stripos($jenis, 'lainnya') !== false) {
                                                            $badgeColor = 'dark';
                                                        }
                                                    @endphp
                                                    <span class="badge bg-label-{{ $badgeColor }} px-3 py-2" style="font-size: 0.875rem;">
                                                        <i class="ti ti-circle-check me-1"></i>{{ $jenis }}
                                                    </span>
                                                @endforeach
                                            </div>
                                            
                                            @if ($pengajuan->jenis_restrukturisasi_lainnya)
                                                <div class="mt-3 p-2 bg-light rounded">
                                                    <small class="d-block">
                                                        <i class="ti ti-info-circle me-1 text-primary"></i>
                                                        <strong>Keterangan Lainnya:</strong>
                                                    </small>
                                                    <small class="text-muted ms-4">{{ $pengajuan->jenis_restrukturisasi_lainnya }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-secondary py-2" role="alert">
                                    <small class="text-muted">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Belum ada jenis restrukturisasi yang dipilih
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">A. Kelengkapan Dokumen</h5>
                <div class="card-body">
                    <p class="text-muted">Verifikasi bahwa setiap dokumen yang dipersyaratkan sudah lengkap dan sah.
                        <span class="text-danger fw-bold">*Harap isi kolom Catatan jika memilih 'Tidak'.</span>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th style="width: 35%;">Item Dokumen</th>
                                    <th class="text-center" style="width: 15%;">Tautan/Status Upload</th>
                                    <th class="text-center" style="width: 20%;">Verifikasi Kelengkapan</th>
                                    <th style="width: 25%;">Catatan Evaluator</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                                <a href="{{ Storage::url($pengajuan->{$dok['field']}) }}"
                                                    target="_blank" class="text-success">
                                                    <i class="ti ti-file-text me-1"></i> Lihat Dokumen
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                    name="kelengkapan_{{ $index + 1 }}"
                                                    id="dok_{{ $index + 1 }}_ya" value="Ya">
                                                <label class="form-check-label"
                                                    for="dok_{{ $index + 1 }}_ya">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                    name="kelengkapan_{{ $index + 1 }}"
                                                    id="dok_{{ $index + 1 }}_tidak" value="Tidak">
                                                <label class="form-check-label"
                                                    for="dok_{{ $index + 1 }}_tidak">Tidak</label>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" rows="1" name="catatan_{{ $index + 1 }}"
                                                placeholder="Catatan"></textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">B. Kelayakan Debitur</h5>
                <div class="card-body">
                    <p class="text-muted">Evaluasi kriteria kelayakan debitur untuk mendapatkan restrukturisasi.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th style="width: 45%;">Kriteria</th>
                                    <th class="text-center" style="width: 20%;">Memenuhi? (Ya/Tidak)</th>
                                    <th style="width: 30%;">Skor / Catatan Evaluator</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Riwayat pembayaran sebelumnya baik (DPD &le; 30 hari)</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_1"
                                                    id="layak_1_ya" value="Ya">
                                                <label class="form-check-label" for="layak_1_ya">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_1"
                                                    id="layak_1_tidak" value="Tidak">
                                                <label class="form-check-label" for="layak_1_tidak">Tidak</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1" placeholder="Misal: DPD 60 hari pada 6 bulan lalu."></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Tidak dalam proses PKPU/kepailitan</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_2"
                                                    id="layak_2_ya" value="Ya">
                                                <label class="form-check-label" for="layak_2_ya">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_2"
                                                    id="layak_2_tidak" value="Tidak">
                                                <label class="form-check-label" for="layak_2_tidak">Tidak</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1" placeholder="Catatan"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>Usaha masih beroperasi dan memiliki *buyer/supplier* aktif</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_3"
                                                    id="layak_3_ya" value="Ya">
                                                <label class="form-check-label" for="layak_3_ya">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_3"
                                                    id="layak_3_tidak" value="Tidak">
                                                <label class="form-check-label" for="layak_3_tidak">Tidak</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1"
                                            placeholder="Catatan (Misal: Verifikasi via kunjungan lapangan)"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td>Ada rencana pemulihan yang realistis dan terukur</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_4"
                                                    id="layak_4_ya" value="Ya">
                                                <label class="form-check-label" for="layak_4_ya">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_4"
                                                    id="layak_4_tidak" value="Tidak">
                                                <label class="form-check-label" for="layak_4_tidak">Tidak</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1"
                                            placeholder="Catatan (Merujuk ke Rencana Pemulihan di bagian atas)"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td>Tidak ada indikasi *fraud* atau manipulasi data</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_5"
                                                    id="layak_5_ya" value="Ya">
                                                <label class="form-check-label" for="layak_5_ya">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="kelayakan_5"
                                                    id="layak_5_tidak" value="Tidak">
                                                <label class="form-check-label" for="layak_5_tidak">Tidak</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1"
                                            placeholder="Catatan (Hasil verifikasi lapangan/audit)"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">C. Analisa Restrukturisasi</h5>
                <div class="card-body">
                    <p class="text-muted">Analisis dampak restrukturisasi yang diajukan dan evaluasi risiko.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th style="width: 40%;">Aspek Evaluasi</th>
                                    <th class="text-center" style="width: 25%;">Pilihan Evaluasi</th>
                                    <th style="width: 30%;">Catatan Evaluator / Justifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>Jenis restrukturisasi yang diajukan:
                                        <br><small class="text-primary">[Opsi yang dipilih Debitur, misal:
                                            Perpanjangan
                                            Jangka Waktu]</small>
                                    </td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_1"
                                                id="analisa_1_sesuai" value="Sesuai">
                                            <label class="form-check-label" for="analisa_1_sesuai">Sesuai
                                                Kebutuhan</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_1"
                                                id="analisa_1_tidak" value="Tidak">
                                            <label class="form-check-label" for="analisa_1_tidak">Tidak
                                                Proporsional</label>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1"
                                            placeholder="Misal: Perpanjangan 6 bulan dianggap cukup untuk pemulihan."></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Dampak pada arus kas debitur pasca-restrukturisasi</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_2"
                                                id="analisa_2_memadai" value="Memadai">
                                            <label class="form-check-label" for="analisa_2_memadai">Memadai (Cash
                                                Flow
                                                Positif)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_2"
                                                id="analisa_2_defisit" value="Defisit">
                                            <label class="form-check-label" for="analisa_2_defisit">Masih
                                                Defisit</label>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1"
                                            placeholder="Berdasarkan Proyeksi Arus Kas setelah skema baru."></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>Kemampuan bayar berdasarkan proyeksi arus kas</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_3"
                                                id="analisa_3_layak" value="Layak">
                                            <label class="form-check-label" for="analisa_3_layak">Layak</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_3"
                                                id="analisa_3_tidak" value="Tidak">
                                            <label class="form-check-label" for="analisa_3_tidak">Tidak
                                                Layak</label>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1"
                                            placeholder="Debt Service Coverage Ratio (DSCR) setelah restrukturisasi."></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td>Dampak pada kualitas aset & PPAP (Pencadangan)</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_4"
                                                id="analisa_4_tidak_signifikan" value="Tidak Signifikan">
                                            <label class="form-check-label" for="analisa_4_tidak_signifikan">Tidak
                                                Signifikan</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_4"
                                                id="analisa_4_perlu_penyesuaian" value="Perlu Penyesuaian">
                                            <label class="form-check-label" for="analisa_4_perlu_penyesuaian">Perlu
                                                Penyesuaian Klasifikasi</label>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1" placeholder="Catatan mengenai kolektibilitas baru."></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center">5</td>
                                    <td>Risiko *moral hazard*</td>
                                    <td>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_5"
                                                id="analisa_5_rendah" value="Rendah">
                                            <label class="form-check-label" for="analisa_5_rendah">Rendah</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_5"
                                                id="analisa_5_sedang" value="Sedang">
                                            <label class="form-check-label" for="analisa_5_sedang">Sedang</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="analisa_5"
                                                id="analisa_5_tinggi" value="Tinggi">
                                            <label class="form-check-label" for="analisa_5_tinggi">Tinggi</label>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control form-control-sm" rows="1"
                                            placeholder="Wajib diisi. Justifikasi risiko dan mitigasinya."></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">D. Rekomendasi Team Evaluasi</h5>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="rekomendasi_analis" class="form-label">Rekomendasi Utama (Persetujuan /
                            Penolakan):</label>
                        <select class="form-select" id="rekomendasi_analis" name="rekomendasi_analis" required>
                            <option value="" disabled selected>Pilih Opsi</option>
                            <option value="Setuju">Setuju untuk Direstrukturisasi</option>
                            <option value="Tolak">Tolak Pengajuan</option>
                            <option value="Opsi Lain">Rekomendasi Opsi Restrukturisasi Lain</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="justifikasi_rekomendasi" class="form-label">Justifikasi dan Skema
                            Restrukturisasi
                            yang Direkomendasikan:</label>
                        <textarea class="form-control" id="justifikasi_rekomendasi" name="justifikasi_rekomendasi" rows="4"
                            placeholder="Jelaskan alasan rekomendasi (misalnya: Debitur layak, namun tenor yang disetujui hanya 12 bulan dengan Grace Period 3 bulan), serta mitigasi risiko yang diusulkan."></textarea>
                        <small class="form-text text-muted">Rekomendasi ini akan menjadi dasar keputusan Komite
                            Kredit.</small>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">E. Persetujuan Komite Kredit</h5>
                <div class="card-body">
                    <p class="text-muted">Tambahkan anggota Komite Kredit dan catat persetujuan mereka.</p>

                    <div id="committee-approval-container">

                        <div class="approval-row row g-3 p-3 mb-3 rounded-3">
                            <div class="col-md-3">
                                <label class="form-label small">Nama Anggota Komite</label>
                                <input type="text" class="form-control" name="committee[0][name]"
                                    placeholder="Nama Lengkap" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Jabatan</label>
                                <input type="text" class="form-control" name="committee[0][position]"
                                    placeholder="Misal: Kepala Divisi Kredit" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small">Tanggal Persetujuan</label>
                                <input type="date" class="form-control" name="committee[0][date]" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Upload TTD Digital</label>
                                <input type="file" class="form-control" name="committee[0][signature]"
                                    accept=".png,.jpg,.jpeg" required>
                            </div>
                            <div class="col-md-1 d-flex align-items-end justify-content-center">
                                <button type="button" class="btn btn-icon btn-outline-danger btn-sm d-none"
                                    onclick="removeApprovalRow(this)" title="Hapus Anggota">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="add-approval-button">
                        <i class="tf-icons bx bx-plus me-1"></i> Tambah Anggota Komite
                    </button>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Simpan
                Evaluasi</button>
        </div>
    </div>
</div>

<script>
    let approvalCount = 1;
    document.getElementById('add-approval-button').addEventListener('click', function() {
        const container = document.getElementById('committee-approval-container');
        const newRow = document.createElement('div');
        newRow.className = 'approval-row row border-bottom pb-3 mb-3';
        newRow.innerHTML = `
            <div class="col-md-3 mb-3">
                <label class="form-label">Nama Anggota Komite</label>
                <input type="text" class="form-control" name="committee[${approvalCount}][name]" placeholder="Nama Lengkap" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Jabatan</label>
                <input type="text" class="form-control" name="committee[${approvalCount}][position]" placeholder="Misal: Anggota Komite" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Tanggal Persetujuan</label>
                <input type="date" class="form-control" name="committee[${approvalCount}][date]" required>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label">Upload TTD Digital</label>
                <input type="file" class="form-control" name="committee[${approvalCount}][signature]" accept=".png,.jpg,.jpeg" required>
            </div>
            <div class="col-md-1 d-flex align-items-center justify-content-end">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeApprovalRow(this)">Hapus</button>
            </div>
        `;
        container.appendChild(newRow);
        approvalCount++;
    });

    function removeApprovalRow(button) {
        button.closest('.approval-row').remove();
    }
</script>
