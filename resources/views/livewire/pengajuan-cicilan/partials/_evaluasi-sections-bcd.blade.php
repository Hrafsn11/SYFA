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
                        <th class="text-center" style="width: {{ $isEditable ? '20%' : '15%' }};">
                            {{ $isEditable ? 'Memenuhi? (Ya/Tidak)' : 'Status' }}</th>
                        <th style="width: {{ $isEditable ? '30%' : '25%' }};">
                            {{ $isEditable ? 'Catatan Evaluator' : 'Catatan' }}</th>
                    </tr>
                </thead>
                <tbody id="table-kelayakan-body">
                    @if ($isEditable)
                        @php
                            $kriteriaList = [
                                'Riwayat pembayaran sebelumnya baik (DPD ≤ 30 hari)',
                                'Tidak dalam proses PKPU/kepailitan',
                                'Usaha masih beroperasi dan memiliki buyer/supplier aktif',
                                'Ada rencana pemulihan yang realistis dan terukur',
                                'Tidak ada indikasi fraud atau manipulasi data',
                            ];
                        @endphp
                        @foreach ($kriteriaList as $idx => $kriteria)
                            @php
                                // Get existing data for this kriteria
                                $existingRow = $evaluasi
                                    ? $evaluasi->kelayakanDebitur()->where('kriteria', $kriteria)->first()
                                    : null;
                                $existingStatus = $existingRow->status ?? '';
                                $existingCatatan = $existingRow->catatan ?? '';
                            @endphp
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>{{ $kriteria }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                name="kelayakan_{{ $idx }}" value="Ya"
                                                {{ $existingStatus === 'Ya' ? 'checked' : '' }}>
                                            <label class="form-check-label">Ya</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                name="kelayakan_{{ $idx }}" value="Tidak"
                                                {{ $existingStatus === 'Tidak' ? 'checked' : '' }}>
                                            <label class="form-check-label">Tidak</label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <textarea class="form-control form-control-sm" rows="1" name="catatan_kelayakan_{{ $idx }}"
                                        placeholder="Catatan evaluator">{{ $existingCatatan }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- READ-ONLY --}}
                        @if ($evaluasi)
                            @forelse($evaluasi->kelayakanDebitur()->get() as $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $row->kriteria }}</td>
                                    <td class="text-center">
                                        @if ($row->status === 'Ya')
                                            <span class="badge bg-success">Ya</span>
                                        @else
                                            <span class="badge bg-danger">Tidak</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $row->catatan ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data evaluasi</td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">Evaluasi belum dilakukan</td>
                            </tr>
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
                        <th class="text-center" style="width: 25%;">{{ $isEditable ? 'Pilihan Evaluasi' : 'Evaluasi' }}
                        </th>
                        <th style="width: 30%;">Catatan</th>
                    </tr>
                </thead>
                <tbody id="table-analisa-body">
                    @if ($isEditable)
                        {{-- EDITABLE --}}
                        @php
                            $analisaAspeks = [
                                [
                                    'aspek' => 'Jenis restrukturisasi yang diajukan sesuai kebutuhan',
                                    'options' => ['Sesuai', 'Tidak'],
                                ],
                                [
                                    'aspek' => 'Dampak pada arus kas debitur pasca-restrukturisasi',
                                    'options' => ['Memadai', 'Defisit'],
                                ],
                                [
                                    'aspek' => 'Kemampuan bayar berdasarkan proyeksi arus kas',
                                    'options' => ['Layak', 'Tidak Layak'],
                                ],
                                ['aspek' => 'Risiko moral hazard', 'options' => ['Rendah', 'Sedang', 'Tinggi']],
                            ];
                        @endphp
                        @foreach ($analisaAspeks as $idx => $item)
                            @php
                                $existingAnalisa = $evaluasi
                                    ? $evaluasi->analisaCicilan()->where('aspek', $item['aspek'])->first()
                                    : null;
                                $existingEvaluasi = $existingAnalisa->evaluasi ?? '';
                                $existingCatatan = $existingAnalisa->catatan ?? '';
                            @endphp
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>{{ $item['aspek'] }}</td>
                                <td>
                                    @foreach ($item['options'] as $option)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                name="analisa_{{ $idx }}" value="{{ $option }}"
                                                {{ $existingEvaluasi === $option ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ $option }}</label>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    <textarea class="form-control form-control-sm" rows="1" name="catatan_analisa_{{ $idx }}"
                                        placeholder="Catatan">{{ $existingCatatan }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        {{-- READ-ONLY --}}
                        @if ($evaluasi)
                            @forelse($evaluasi->analisaCicilan()->get() as $row)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $row->aspek }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $row->evaluasi }}</span>
                                    </td>
                                    <td><small class="text-muted">{{ $row->catatan ?: '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada data analisa</td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">Analisa belum dilakukan</td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Section D: Rekomendasi Team Evaluasi --}}
<div class="card mb-4">
    <h5 class="card-header">D. Rekomendasi Team Evaluasi</h5>
    <div class="card-body">
        @if ($isEditable)
            {{-- EDITABLE MODE --}}
            @php
                // Get existing data for pre-fill in edit mode
                $existingRekomendasi = $evaluasi->rekomendasi ?? '';
                $existingJustifikasi = $evaluasi->justifikasi_rekomendasi ?? '';
            @endphp
            <div class="d-flex flex-column">
                <div class="col-12 mb-4">
                    <label for="rekomendasi_analis" class="form-label fw-semibold">
                        Rekomendasi Utama <span class="text-danger">*</span>
                    </label>
                    <select class="form-select select2-rekomendasi" id="rekomendasi_analis" name="rekomendasi_analis"
                        required>
                        <option value="">-- Pilih Rekomendasi --</option>
                        <option value="Setuju" {{ $existingRekomendasi === 'Setuju' ? 'selected' : '' }}>Setuju untuk
                            Direstrukturisasi</option>
                        <option value="Tolak" {{ $existingRekomendasi === 'Tolak' ? 'selected' : '' }}>Tolak
                            Pengajuan</option>
                        <option value="Opsi Lain" {{ $existingRekomendasi === 'Opsi Lain' ? 'selected' : '' }}>
                            Rekomendasi Opsi Lain</option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="justifikasi_rekomendasi" class="form-label fw-semibold">
                        Justifikasi dan Skema Restrukturisasi
                    </label>
                    <textarea class="form-control" id="justifikasi_rekomendasi" name="justifikasi_rekomendasi" rows="4"
                        placeholder="Jelaskan alasan dan pertimbangan rekomendasi...">{{ $existingJustifikasi }}</textarea>
                    <small class="form-text text-muted">Rekomendasi ini akan menjadi dasar keputusan Komite
                        Kredit.</small>
                </div>
            </div>
        @else
            {{-- READ-ONLY MODE --}}
            @if ($evaluasi)
                <div class="d-flex flex-column">
                    <div class="col-md mb-4">
                        <label class="form-label text-muted small mb-1">Rekomendasi</label>
                        <div>
                            @if ($evaluasi->rekomendasi === 'Setuju')
                                <div class="bg-success text-white p-3 rounded">
                                    <span>Setuju untuk Direstrukturisasi</span>
                                </div>
                            @elseif($evaluasi->rekomendasi === 'Tolak')
                                <div class="bg-danger text-white p-3 rounded">
                                    <span>Tolak Pengajuan</span>
                                </div>
                            @else
                                <div class="bg-warning text-dark p-3 rounded">
                                    <span>{{ $evaluasi->rekomendasi }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small mb-1">Justifikasi</label>
                        <div class="bg-light p-3 rounded">
                            {{ $evaluasi->justifikasi_rekomendasi ?: 'Tidak ada justifikasi.' }}
                        </div>
                    </div>
                </div>
            @else
                <p class="text-muted mb-0">Rekomendasi belum diberikan.</p>
            @endif
        @endif
    </div>
</div>
