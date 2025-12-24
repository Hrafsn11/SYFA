{{-- Modal Tambah Pengembalian Investasi --}}
<div class="modal fade" id="modalPengembalianInvestasi" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPengembalianInvestasiLabel">Tambah Pengembalian Investasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
            </div>
            <form id="formPengembalianInvestasi"
                wire:submit="{{ $urlAction['store_pengembalian_investasi'] ?? 'saveData' }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- No Kontrak -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_pengajuan_investasi" class="form-label">
                                Pilih No Kontrak <span class="text-danger">*</span>
                            </label>
                            <div wire:ignore>
                                <select id="id_pengajuan_investasi"
                                    class="form-select select2 @error('id_pengajuan_investasi') is-invalid @enderror"
                                    data-placeholder="Pilih No Kontrak">
                                    <option value=""></option>
                                    @foreach ($pengajuanInvestasi as $item)
                                        <option value="{{ $item->id_pengajuan_investasi }}">
                                            {{ $item->nomor_kontrak }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('id_pengajuan_investasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <div wire:loading wire:target="loadDataKontrak" class="alert alert-info py-2">
                                <small><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</small>
                            </div>
                            @error('id_pengajuan_investasi')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Readonly Info Fields -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nominal Investasi</label>
                            <input type="text" class="form-control"
                                value="Rp {{ number_format($nominal_investasi ?? 0, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Lama Investasi</label>
                            <input type="text" class="form-control" value="{{ $lama_investasi ?? 0 }} Bulan"
                                readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bagi Hasil Total</label>
                            <input type="text" class="form-control"
                                value="Rp {{ number_format($bagi_hasil_total ?? 0, 0, ',', '.') }}" readonly>
                        </div>

                        @if ($jumlah_transaksi > 0)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Pokok Sudah Dikembalikan</label>
                                <input type="text" class="form-control text-success fw-bold"
                                    value="Rp {{ number_format($total_pokok_dikembalikan ?? 0, 0, ',', '.') }}"
                                    readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Bagi Hasil Sudah Dikembalikan</label>
                                <input type="text" class="form-control text-success fw-bold"
                                    value="Rp {{ number_format($total_bagi_hasil_dikembalikan ?? 0, 0, ',', '.') }}"
                                    readonly>
                            </div>
                        @endif

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sisa Pokok</label>
                            <input type="text" class="form-control text-warning fw-bold"
                                value="Rp {{ number_format($sisa_pokok ?? 0, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sisa Bagi Hasil</label>
                            <input type="text" class="form-control text-warning fw-bold"
                                value="Rp {{ number_format($sisa_bagi_hasil ?? 0, 0, ',', '.') }}" readonly>
                        </div>

                        @if ($id_pengajuan_investasi)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dana di Perusahaan</label>
                                <input type="text" class="form-control text-danger fw-bold"
                                    value="Rp {{ number_format($sisa_dana_di_perusahaan ?? 0, 0, ',', '.') }}"
                                    readonly>
                                <small class="text-muted">Dana yang disalurkan ke perusahaan, menunggu pembayaran
                                    kembali</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dana Tersedia</label>
                                <input type="text" class="form-control text-success fw-bold"
                                    value="Rp {{ number_format($dana_tersedia ?? 0, 0, ',', '.') }}" readonly>
                                <small class="text-muted">Dana yang bisa dikembalikan ke investor saat ini</small>
                            </div>
                        @endif

                        <div class="col-md-6 mb-3 form-group">
                            <label for="dana_pokok_dibayar" class="form-label">
                                Dana Pokok Yang Dibayarkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('dana_pokok_dibayar') is-invalid @enderror"
                                id="dana_pokok_dibayar" placeholder="Ketik angka saja (contoh: 10000000)"
                                autocomplete="off">
                            <input type="hidden" id="dana_pokok_raw" wire:model="dana_pokok_dibayar">
                            @if ($id_pengajuan_investasi && $dana_tersedia !== null)
                                <small class="text-muted">Maksimal: Rp
                                    {{ number_format($dana_tersedia, 0, ',', '.') }}</small>
                            @endif
                            @error('dana_pokok_dibayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="bagi_hasil_dibayar" class="form-label">
                                Bagi Hasil Yang Dibayarkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('bagi_hasil_dibayar') is-invalid @enderror"
                                id="bagi_hasil_dibayar" placeholder="Ketik angka saja (contoh: 1000000)"
                                autocomplete="off">
                            <input type="hidden" id="bagi_hasil_raw" wire:model="bagi_hasil_dibayar">
                            @if ($id_pengajuan_investasi && $sisa_bagi_hasil !== null)
                                <small class="text-muted">Maksimal: Rp
                                    {{ number_format($sisa_bagi_hasil, 0, ',', '.') }}</small>
                            @endif
                            @error('bagi_hasil_dibayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="tanggal_pengembalian" class="form-label">
                                Tanggal Pengembalian <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('tanggal_pengembalian') is-invalid @enderror"
                                id="tanggal_pengembalian" wire:model="tanggal_pengembalian"
                                placeholder="Pilih tanggal">
                            @error('tanggal_pengembalian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="bukti_transfer" class="form-label">
                                Bukti Transfer <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('bukti_transfer') is-invalid @enderror"
                                id="bukti_transfer" wire:model="bukti_transfer" accept=".pdf,.jpg,.jpeg,.png">
                            <div wire:loading wire:target="bukti_transfer" class="mt-1">
                                <small class="text-primary"><span
                                        class="spinner-border spinner-border-sm me-1"></span>Uploading...</small>
                            </div>
                            @error('bukti_transfer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="resetForm">
                        <i class="ti ti-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                        wire:target="saveData">
                        <span wire:loading.remove wire:target="saveData">
                            <i class="ti ti-device-floppy me-1"></i> Simpan
                        </span>
                        <span wire:loading wire:target="saveData">
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
