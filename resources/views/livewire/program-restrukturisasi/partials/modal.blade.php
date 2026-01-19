<div>
    {{-- Modal Upload Bukti Pembayaran --}}
    @if ($isEdit)
        <div class="modal fade" id="modalUploadBukti" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEditingFile ? 'Edit Bukti Pembayaran' : 'Upload Bukti Pembayaran' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            wire:click="closeUploadModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($selectedAngsuranNo)
                            <div class="mb-3 p-3 bg-light rounded">
                                <p class="mb-1"><strong>Angsuran Bulan:</strong> {{ $selectedAngsuranNo }}</p>
                                @if (isset($jadwal_angsuran[$selectedAngsuranIndex]))
                                    @php
                                        $selectedAngsuran = $jadwal_angsuran[$selectedAngsuranIndex];
                                    @endphp
                                    <p class="mb-1"><strong>Tanggal Jatuh Tempo:</strong>
                                        {{ $selectedAngsuran['tanggal_jatuh_tempo'] }}</p>
                                    <p class="mb-0"><strong>Total Cicilan:</strong> Rp
                                        {{ number_format($selectedAngsuran['total_cicilan'], 0, ',', '.') }}</p>
                                @endif
                            </div>
                        @endif

                        @if ($isEditingFile)
                            <div class="alert alert-warning py-2 mb-3">
                                <i class="ti ti-alert-triangle me-1"></i>
                                <small>File bukti pembayaran lama akan diganti dengan file baru.</small>
                            </div>
                        @endif

                        <div class="mb-3" x-data="{
                            rawValue: @entangle('uploadNominalBayar'),
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
                            @if (isset($jadwal_angsuran[$selectedAngsuranIndex]))
                                <small class="text-muted">
                                    Total cicilan yang harus dibayar: Rp
                                    {{ number_format($jadwal_angsuran[$selectedAngsuranIndex]['total_cicilan'], 0, ',', '.') }}
                                </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label
                                class="form-label">{{ $isEditingFile ? 'Pilih File Baru' : 'Pilih File Bukti Pembayaran' }}
                                <span class="text-danger">*</span></label>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            wire:click="closeUploadModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="submitUploadBukti"
                            wire:loading.attr="disabled" wire:target="submitUploadBukti">
                            <span wire:loading.remove wire:target="submitUploadBukti">
                                <i class="ti ti-upload me-1"></i>Upload
                            </span>
                            <span wire:loading wire:target="submitUploadBukti">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Mengupload...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Pembayaran --}}
    @if ($isEdit)
        <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-check-square me-2"></i>Konfirmasi Pembayaran
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            wire:click="closeKonfirmasiModal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($selectedKonfirmasiNo)
                            <div class="alert alert-info mb-3">
                                <i class="ti ti-info-circle me-1"></i>
                                Anda akan mengkonfirmasi pembayaran untuk angsuran berikut:
                            </div>

                            @if (isset($jadwal_angsuran[$selectedKonfirmasiIndex]))
                                @php
                                    $konfirmasiAngsuran = $jadwal_angsuran[$selectedKonfirmasiIndex];
                                @endphp
                                <div class="card shadow-none border mb-3">
                                    <div class="card-body py-3">
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td class="fw-bold" width="40%">Angsuran Bulan</td>
                                                <td>: {{ $konfirmasiAngsuran['no'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Tanggal Jatuh Tempo</td>
                                                <td>: {{ $konfirmasiAngsuran['tanggal_jatuh_tempo'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Total Cicilan</td>
                                                <td>: Rp
                                                    {{ number_format($konfirmasiAngsuran['total_cicilan'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Nominal Dibayar</td>
                                                <td>: Rp
                                                    {{ number_format($konfirmasiAngsuran['nominal_bayar'] ?? 0, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold">Tanggal Bayar</td>
                                                <td>:
                                                    {{ isset($konfirmasiAngsuran['tanggal_bayar']) ? \Carbon\Carbon::parse($konfirmasiAngsuran['tanggal_bayar'])->format('d/m/Y') : '-' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if (!empty($konfirmasiAngsuran['bukti_pembayaran']))
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Bukti Pembayaran:</label>
                                        <div class="text-center p-3 w-full">
                                            <a href="{{ Storage::url($konfirmasiAngsuran['bukti_pembayaran']) }}"
                                                target="_blank" class="btn btn-info text-white w-100">
                                                <i class="ti ti-external-link me-1"></i>Lihat Bukti Pembayaran
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" wire:click="openTolakModal">
                            <i class="ti ti-x me-1"></i>Tolak Pembayaran
                        </button>
                        <button type="button" class="btn btn-success" wire:click="submitKonfirmasi"
                            wire:loading.attr="disabled" wire:target="submitKonfirmasi">
                            <span wire:loading.remove wire:target="submitKonfirmasi">
                                <i class="ti ti-check me-1"></i>Konfirmasi Lunas
                            </span>
                            <span wire:loading wire:target="submitKonfirmasi">
                                <span class="spinner-border spinner-border-sm me-1"></span>Memproses...
                            </span>
                        </button>
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
                        <div class="alert alert-warning mb-3">
                            <i class="ti ti-info-circle me-1"></i>
                            Apakah Anda yakin ingin menolak bukti pembayaran ini?
                            Bukti pembayaran akan dihapus dan user harus mengupload ulang.
                        </div>

                        {{-- Field Catatan untuk alasan penolakan --}}
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span
                                    class="text-muted">(opsional)</span></label>
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
