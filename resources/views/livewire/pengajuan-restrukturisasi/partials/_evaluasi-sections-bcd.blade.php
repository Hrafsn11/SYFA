{{-- Section B: Kelayakan Debitur --}}
<div class="card mb-4">
    <h5 class="card-header">B. Kelayakan Debitur</h5>
    <div class="card-body">
        <p class="text-muted">Evaluasi kriteria kelayakan debitur untuk mendapatkan restrukturisasi.</p>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: {{ $isEditable ? '45%' : '55%' }};">Kriteria</th>
                        <th class="text-center" style="width: {{ $isEditable ? '20%' : '15%' }};">{{ $isEditable ? 'Memenuhi? (Ya/Tidak)' : 'Status' }}</th>
                        <th style="width: {{ $isEditable ? '30%' : '25%' }};">{{ $isEditable ? 'Catatan Evaluator' : 'Catatan' }}</th>
                    </tr>
                </thead>
                <tbody id="table-kelayakan-body">
                    @if($isEditable)
                        @foreach([
                            'Riwayat pembayaran sebelumnya baik (DPD ≤ 30 hari)',
                            'Tidak dalam proses PKPU/kepailitan',
                            'Usaha masih beroperasi dan memiliki buyer/supplier aktif',
                            'Ada rencana pemulihan yang realistis dan terukur',
                            'Tidak ada indikasi fraud atau manipulasi data'
                        ] as $idx => $kriteria)
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>{{ $kriteria }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="kelayakan_{{ $idx }}" value="Ya">
                                        <label class="form-check-label">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="kelayakan_{{ $idx }}" value="Tidak">
                                        <label class="form-check-label">Tidak</label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control form-control-sm" rows="1" 
                                    name="catatan_kelayakan_{{ $idx }}"
                                    placeholder="Catatan evaluator"></textarea>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        {{-- READ-ONLY --}}
                        @if($evaluasi)
                            @forelse($evaluasi->kelayakanDebitur()->get() as $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $row->kriteria }}</td>
                                    <td class="text-center">
                                        @if($row->status === 'Ya')
                                            <span class="badge bg-success">Ya</span>
                                        @else
                                            <span class="badge bg-danger">Tidak</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $row->catatan ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data evaluasi</td></tr>
                            @endforelse
                        @else
                            <tr><td colspan="4" class="text-center text-muted">Evaluasi belum dilakukan</td></tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Section C: Analisa Restrukturisasi --}}
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
                        <th class="text-center" style="width: 25%;">{{ $isEditable ? 'Pilihan Evaluasi' : 'Evaluasi' }}</th>
                        <th style="width: 30%;">Catatan</th>
                    </tr>
                </thead>
                <tbody id="table-analisa-body">
                    @if($isEditable)
                        {{-- EDITABLE --}}
                        <tr>
                            <td class="text-center">1</td>
                            <td>Jenis restrukturisasi yang diajukan sesuai kebutuhan</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_0" value="Sesuai">
                                    <label class="form-check-label">Sesuai</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_0" value="Tidak">
                                    <label class="form-check-label">Tidak</label>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control form-control-sm" rows="1" 
                                    name="catatan_analisa_0"
                                    placeholder="Catatan"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td>Dampak pada arus kas debitur pasca-restrukturisasi</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_1" value="Memadai">
                                    <label class="form-check-label">Memadai</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_1" value="Defisit">
                                    <label class="form-check-label">Defisit</label>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control form-control-sm" rows="1" 
                                    name="catatan_analisa_1"
                                    placeholder="Catatan"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td>Kemampuan bayar berdasarkan proyeksi arus kas</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_2" value="Layak">
                                    <label class="form-check-label">Layak</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_2" value="Tidak Layak">
                                    <label class="form-check-label">Tidak Layak</label>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control form-control-sm" rows="1" 
                                    name="catatan_analisa_2"
                                    placeholder="Catatan"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">4</td>
                            <td>Risiko moral hazard</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_3" value="Rendah">
                                    <label class="form-check-label">Rendah</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_3" value="Sedang">
                                    <label class="form-check-label">Sedang</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="analisa_3" value="Tinggi">
                                    <label class="form-check-label">Tinggi</label>
                                </div>
                            </td>
                            <td>
                                <textarea class="form-control form-control-sm" rows="1" 
                                    name="catatan_analisa_3"
                                    placeholder="Catatan"></textarea>
                            </td>
                        </tr>
                    @else
                        {{-- READ-ONLY --}}
                        @if($evaluasi)
                            @forelse($evaluasi->analisaRestrukturisasi()->get() as $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $row->aspek }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $row->evaluasi }}</span>
                                    </td>
                                    <td><small class="text-muted">{{ $row->catatan ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data analisa</td></tr>
                            @endforelse
                        @else
                            <tr><td colspan="4" class="text-center text-muted">Analisa belum dilakukan</td></tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Section D: Rekomendasi --}}
<div class="card mb-4">
    <h5 class="card-header">D. Rekomendasi Team Evaluasi</h5>
    <div class="card-body">
        @if($isEditable)
            {{-- EDITABLE --}}
            <div class="mb-3">
                <label for="rekomendasi_analis" class="form-label">Rekomendasi Utama:</label>
                <select class="form-select" id="rekomendasi_analis" name="rekomendasi_analis" required>
                    <option value="" disabled selected>Pilih Opsi</option>
                    <option value="Setuju">Setuju untuk Direstrukturisasi</option>
                    <option value="Tolak">Tolak Pengajuan</option>
                    <option value="Opsi Lain">Rekomendasi Opsi Restrukturisasi Lain</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="justifikasi_rekomendasi" class="form-label">Justifikasi dan Skema Restrukturisasi:</label>
                <textarea class="form-control" id="justifikasi_rekomendasi" name="justifikasi_rekomendasi" 
                    rows="4" placeholder="Jelaskan alasan rekomendasi..."></textarea>
                <small class="form-text text-muted">Rekomendasi ini akan menjadi dasar keputusan Komite Kredit.</small>
            </div>
        @else
            {{-- READ-ONLY --}}
            @if($evaluasi)
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted d-block mb-1">Rekomendasi:</small>
                        <p class="fw-bold mb-3">
                            @if($evaluasi->rekomendasi === 'Setuju')
                                <span class="badge bg-success fs-6">Setuju untuk Direstrukturisasi</span>
                            @elseif($evaluasi->rekomendasi === 'Tolak')
                                <span class="badge bg-danger fs-6">Tolak Pengajuan</span>
                            @else
                                <span class="badge bg-warning fs-6">{{ $evaluasi->rekomendasi }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-9">
                        <small class="text-muted d-block mb-1">Justifikasi:</small>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $evaluasi->justifikasi_rekomendasi ?: 'Tidak ada justifikasi.' }}</p>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-muted mb-0">Rekomendasi belum diberikan.</p>
            @endif
        @endif
    </div>
</div>
