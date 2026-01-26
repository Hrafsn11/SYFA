<div>
    {{-- Modal Upload Bukti Pembayaran --}}
    @if ($isEdit)
        <div class="modal fade" id="modalUploadBukti" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if ($viewOnlyMode)
                                <i class="ti ti-history me-2"></i>Riwayat Pembayaran
                            @else
                                <i class="ti ti-upload me-2"></i>Upload Bukti Pembayaran
                            @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            wire:click="closeUploadModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($selectedAngsuranNo && isset($jadwal_angsuran[$selectedAngsuranIndex]))
                            @php
                                $selectedAngsuran = $jadwal_angsuran[$selectedAngsuranIndex];
                                $totalCicilan = $selectedAngsuran['total_cicilan'] ?? 0;
                                $totalTerbayar = $selectedAngsuran['total_terbayar'] ?? 0;
                                $sisaPembayaran = $selectedAngsuran['sisa_pembayaran'] ?? ($totalCicilan - $totalTerbayar);
                                $progressPercent = $totalCicilan > 0 ? min(100, ($totalTerbayar / $totalCicilan) * 100) : 0;
                            @endphp
                            
                            
                            {{-- Info Angsuran - Minimal --}}
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="50%"><strong>Angsuran Bulan:</strong> {{ $selectedAngsuranNo }}</td>
                                    <td><strong>Total Cicilan:</strong> Rp {{ number_format($totalCicilan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Jatuh Tempo:</strong> {{ $selectedAngsuran['tanggal_jatuh_tempo'] }}</td>
                                    <td><strong>Sudah Dibayar:</strong> Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Status:</strong> 
                                        <span class="badge {{ $selectedAngsuran['status'] === 'Lunas' ? 'bg-success' : ($selectedAngsuran['status'] === 'Dibayar Sebagian' ? 'bg-warning' : 'bg-secondary') }}">
                                            {{ $selectedAngsuran['status'] }}
                                        </span>
                                    </td>
                                    <td><strong>Sisa Pembayaran:</strong> Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                            
                            <hr class="my-3">

                            @if (!$viewOnlyMode)
                                {{-- Form Upload - hanya tampil jika bukan view only mode --}}
                                <div class="mb-3" x-data="{
                                    rawValue: @entangle('uploadNominalBayar'),
                                    maxValue: {{ $maxNominalBayar ?? $sisaPembayaran }},
                                    formattedValue: '',
                                    formatNumber(num) {
                                        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    },
                                    parseNumber(str) {
                                        return parseInt(str.replace(/\./g, '')) || 0;
                                    },
                                    init() {
                                        this.formattedValue = this.formatNumber(this.rawValue);
                                        this.$watch('rawValue', (value) => {
                                            this.formattedValue = this.formatNumber(value);
                                        });
                                    }
                                }">
                                    <label class="form-label">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('uploadNominalBayar') is-invalid @enderror"
                                        x-model="formattedValue"
                                        @input="rawValue = parseNumber($event.target.value); formattedValue = formatNumber(rawValue)"
                                        @blur="formattedValue = formatNumber(rawValue)"
                                        placeholder="Masukkan nominal pembayaran">
                                    @error('uploadNominalBayar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">Maksimal: Rp {{ number_format($maxNominalBayar ?? $sisaPembayaran, 0, ',', '.') }}</small>
                                        <button type="button" class="btn btn-link btn-sm p-0" 
                                            @click="rawValue = maxValue; formattedValue = formatNumber(maxValue)">
                                            Isi Maksimal
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Pilih File Bukti Pembayaran <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('uploadFile') is-invalid @enderror"
                                        accept="image/*,.pdf" wire:model="uploadFile" wire:loading.attr="disabled">
                                    @error('uploadFile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Format: JPG, PNG, atau PDF. Maksimal 2MB</small>

                                    <div wire:loading wire:target="uploadFile" class="mt-2">
                                        <div class="alert alert-info py-2">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            <small>Mengunggah file ke server, harap tunggu...</small>
                                        </div>
                                    </div>

                                    <div wire:loading.remove wire:target="uploadFile">
                                        @if ($uploadFile)
                                            <div class="mt-2">
                                                <div class="alert alert-success py-2">
                                                    <i class="ti ti-check me-1"></i>
                                                    <small>File siap:
                                                        <strong>{{ is_string($uploadFile) ? $uploadFile : $uploadFile->getClientOriginalName() }}</strong></small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Riwayat Pembayaran --}}
                            @if (!empty($riwayatPembayaran))
                                <div class="mt-4">
                                    <h6 class="mb-2"><i class="ti ti-history me-1"></i>Riwayat Pembayaran</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th class="text-end">Nominal</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Bukti</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($riwayatPembayaran as $riwayat)
                                                    <tr>
                                                        <td>{{ $riwayat['created_at'] }}</td>
                                                        <td class="text-end">Rp {{ number_format($riwayat['nominal_bayar'], 0, ',', '.') }}</td>
                                                        <td class="text-center">
                                                            @if ($riwayat['status'] === 'Dikonfirmasi')
                                                                <span class="badge bg-success">Dikonfirmasi</span>
                                                            @elseif ($riwayat['status'] === 'Tertunda')
                                                                <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                                            @else
                                                                <span class="badge bg-danger">Ditolak</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if (!empty($riwayat['bukti_pembayaran']))
                                                                <a href="{{ Storage::url($riwayat['bukti_pembayaran']) }}" 
                                                                    target="_blank" 
                                                                    class="btn btn-sm btn-outline-primary">
                                                                    <i class="ti ti-eye"></i> Lihat
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
                                </div>
                            @else
                                @if ($viewOnlyMode)
                                    <div class="alert alert-warning">
                                        <i class="ti ti-alert-triangle me-1"></i>
                                        Belum ada riwayat pembayaran untuk angsuran ini.
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="closeUploadModal">
                            {{ $viewOnlyMode ? 'Tutup' : 'Batal' }}
                        </button>
                        @if (!$viewOnlyMode)
                            <button type="button" class="btn btn-primary" wire:click="submitUploadBukti"
                                wire:loading.attr="disabled" wire:target="submitUploadBukti">
                                <span wire:loading.remove wire:target="submitUploadBukti">
                                    <i class="ti ti-upload me-1"></i>Upload Pembayaran
                                </span>
                                <span wire:loading wire:target="submitUploadBukti">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Mengupload...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Pembayaran --}}
    @if ($isEdit)
        <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-check-square me-2"></i>Konfirmasi Pembayaran
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            wire:click="closeKonfirmasiModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($selectedKonfirmasiNo && isset($jadwal_angsuran[$selectedKonfirmasiIndex]))
                            @php
                                $konfirmasiAngsuran = $jadwal_angsuran[$selectedKonfirmasiIndex];
                                $totalCicilan = $konfirmasiAngsuran['total_cicilan'] ?? 0;
                                $totalTerbayar = $konfirmasiAngsuran['total_terbayar'] ?? 0;
                                $sisaPembayaran = $konfirmasiAngsuran['sisa_pembayaran'] ?? ($totalCicilan - $totalTerbayar);
                            @endphp

                            
                            <p class="mb-3">
                                <i class="ti ti-info-circle me-1"></i>
                                Konfirmasi pembayaran untuk <strong>Angsuran Bulan {{ $selectedKonfirmasiNo }}</strong>
                            </p>

                            {{-- Info Ringkasan - Minimal --}}
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="33%"><small class="text-muted">Total Cicilan</small><br><strong>Rp {{ number_format($totalCicilan, 0, ',', '.') }}</strong></td>
                                    <td width="33%"><small class="text-muted">Sudah Dikonfirmasi</small><br><strong>Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</strong></td>
                                    <td width="33%"><small class="text-muted">Sisa Pembayaran</small><br><strong>Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}</strong></td>
                                </tr>
                            </table>
                            
                            <hr class="my-3">

                            {{-- Daftar Pembayaran Tertunda --}}
                            @if (!empty($riwayatPembayaran))
                                <h6 class="mb-2"><i class="ti ti-clock me-1"></i>Pembayaran Menunggu Konfirmasi</h6>
                                <div class="list-group mb-3">
                                    @foreach ($riwayatPembayaran as $index => $riwayat)
                                        <div class="list-group-item list-group-item-action {{ $selectedRiwayatId === $riwayat['id'] ? 'bg-light' : '' }}"
                                            wire:click="selectRiwayat('{{ $riwayat['id'] }}')"
                                            style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <input type="radio" class="form-check-input me-2" 
                                                            {{ $selectedRiwayatId === $riwayat['id'] ? 'checked' : '' }}>
                                                        <div>
                                                            <strong>Rp {{ number_format($riwayat['nominal_bayar'], 0, ',', '.') }}</strong>
                                                            <small class="d-block text-muted">
                                                                Diupload: {{ $riwayat['created_at'] }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if (!empty($riwayat['bukti_pembayaran']))
                                                    <a href="{{ Storage::url($riwayat['bukti_pembayaran']) }}" 
                                                        target="_blank" 
                                                        class="btn btn-sm btn-outline-secondary"
                                                        onclick="event.stopPropagation();">
                                                        <i class="ti ti-eye me-1"></i>Lihat Bukti
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Info Total Jika Dikonfirmasi - Simplified --}}
                                @if ($selectedRiwayat)
                                    @php
                                        $nominalTerpilih = $selectedRiwayat['nominal_bayar'] ?? 0;
                                        $totalSetelahKonfirmasi = $totalTerbayar + $nominalTerpilih;
                                        $sisaSetelahKonfirmasi = $totalCicilan - $totalSetelahKonfirmasi;
                                        $akanLunas = $totalSetelahKonfirmasi >= $totalCicilan;
                                    @endphp
                                    <div class="alert {{ $akanLunas ? 'alert-light border border-success text-success' : 'alert-light border' }} mb-3">
                                        @if ($akanLunas)
                                            <strong><i class="ti ti-check me-1"></i>Jika dikonfirmasi, angsuran ini akan LUNAS!</strong>
                                            <br><small class="text-success">Total: Rp {{ number_format($totalSetelahKonfirmasi, 0, ',', '.') }} ≥ Rp {{ number_format($totalCicilan, 0, ',', '.') }}</small>
                                        @else
                                            <strong>Total setelah konfirmasi: Rp {{ number_format($totalSetelahKonfirmasi, 0, ',', '.') }}</strong>
                                            <br><small class="text-muted">Sisa yang harus dibayar: Rp {{ number_format($sisaSetelahKonfirmasi, 0, ',', '.') }}</small>
                                        @endif
                                    </div>
                                @endif

                                {{-- Catatan Konfirmasi --}}
                                <div class="mb-3">
                                    <label class="form-label">Catatan Konfirmasi <span class="text-muted">(opsional)</span></label>
                                    <textarea class="form-control" wire:model="catatanKonfirmasi" rows="2"
                                        placeholder="Catatan tambahan untuk konfirmasi..."></textarea>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    Tidak ada pembayaran yang menunggu konfirmasi.
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if (!empty($riwayatPembayaran) && $selectedRiwayat)
                            @php
                                $nominalTerpilih = $selectedRiwayat['nominal_bayar'] ?? 0;
                                $totalSetelahKonfirmasi = ($konfirmasiAngsuran['total_terbayar'] ?? 0) + $nominalTerpilih;
                                $akanLunas = $totalSetelahKonfirmasi >= ($konfirmasiAngsuran['total_cicilan'] ?? 0);
                            @endphp
                            
                            <button type="button" class="btn btn-outline-danger" wire:click="openTolakModal">
                                <i class="ti ti-x me-1"></i>Tolak
                            </button>
                            
                            <button type="button" class="btn btn-success" wire:click="submitKonfirmasi"
                                wire:loading.attr="disabled" wire:target="submitKonfirmasi">
                                <span wire:loading.remove wire:target="submitKonfirmasi">
                                    <i class="ti ti-check me-1"></i>Konfirmasi
                                    @if ($akanLunas)
                                        <small class="opacity-75">(akan Lunas)</small>
                                    @endif
                                </span>
                                <span wire:loading wire:target="submitKonfirmasi">
                                    <span class="spinner-border spinner-border-sm me-1"></span>Memproses...
                                </span>
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                wire:click="closeKonfirmasiModal">Tutup</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Penolakan --}}
    @if ($isEdit)
        <div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger">
                            <i class="ti ti-alert-triangle me-2"></i>Tolak Pembayaran
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            wire:click="closeTolakModal"></button>
                    </div>
                    <div class="modal-body pt-2">
                        @if ($selectedRiwayat)
                            <div class="alert alert-danger mb-3">
                                <strong>Pembayaran yang akan ditolak:</strong><br>
                                Nominal: <strong>Rp {{ number_format($selectedRiwayat['nominal_bayar'] ?? 0, 0, ',', '.') }}</strong><br>
                                Tanggal: {{ $selectedRiwayat['tanggal_bayar'] ?? '-' }}
                            </div>
                        @endif

                        <div class="alert alert-warning mb-3">
                            <i class="ti ti-info-circle me-1"></i>
                            Apakah Anda yakin ingin menolak bukti pembayaran ini?
                            Bukti pembayaran akan dihapus dan user harus mengupload ulang.
                        </div>

                        {{-- Field Catatan untuk alasan penolakan --}}
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-muted">(opsional)</span></label>
                            <textarea class="form-control" wire:model="catatanKonfirmasi" rows="3"
                                placeholder="Contoh: Bukti pembayaran tidak jelas, nominal tidak sesuai, dll"></textarea>
                            <small class="text-muted">Catatan ini akan disimpan sebagai alasan penolakan.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="closeTolakModal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="tolakPembayaran"
                            wire:loading.attr="disabled" wire:target="tolakPembayaran">
                            <span wire:loading.remove wire:target="tolakPembayaran">
                                <i class="ti ti-x me-1"></i>Ya, Tolak Pembayaran
                            </span>
                            <span wire:loading wire:target="tolakPembayaran">
                                <span class="spinner-border spinner-border-sm me-1"></span>Memproses...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
