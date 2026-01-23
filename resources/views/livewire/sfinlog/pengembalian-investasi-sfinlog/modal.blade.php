{{-- Modal Tambah Pengembalian Investasi SFinlog --}}
<div class="modal fade" id="modalPengembalianInvestasiSfinlog" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengembalian Investasi - SFinlog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
            </div>
            <form id="formPengembalianInvestasiFinlog"
                wire:submit="{{ $urlAction['store_pengembalian_investasi_finlog'] ?? 'saveData' }}">
                <div class="modal-body">
                    <div class="row">
                        <!-- No Kontrak -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_pengajuan_investasi_finlog" class="form-label">
                                Pilih No Kontrak <span class="text-danger">*</span>
                            </label>
                            <div wire:ignore>
                                <select id="id_pengajuan_investasi_finlog"
                                    class="form-select select2 @error('id_pengajuan_investasi_finlog') is-invalid @enderror"
                                    data-placeholder="Pilih No Kontrak">
                                    <option value=""></option>
                                    @foreach ($pengajuanInvestasi as $item)
                                        <option value="{{ $item->id_pengajuan_investasi_finlog }}">
                                            {{ $item->nomor_kontrak }} - {{ $item->nama_investor }} -
                                            Rp {{ number_format($item->nominal_investasi, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('id_pengajuan_investasi_finlog')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <div wire:loading wire:target="loadDataKontrak" class="alert alert-info py-2">
                                <small><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</small>
                            </div>
                            @error('id_pengajuan_investasi_finlog')
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
                            <input type="text" class="form-control" value="{{ $lama_investasi ?? 0 }} Bulan" readonly>
                        </div>
                        @php
                            $sisaBagiHasilInfo = $sisa_bagi_hasil_db ?? 0;
                        @endphp
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sisa Bagi Hasil</label>
                            <input type="text" class="form-control text-warning fw-bold"
                                value="Rp {{ number_format($sisaBagiHasilInfo, 0, ',', '.') }}" readonly>
                            @if(($bagi_hasil_total ?? 0) > 0)
                                <small class="text-muted">
                                    Total Bagi Hasil: Rp {{ number_format($bagi_hasil_total ?? 0, 0, ',', '.') }}
                                </small>
                            @endif
                        </div>

                        {{-- Penyaluran Deposito Info --}}
                        @if($total_dana_disalurkan > 0 || $jumlah_transaksi > 0)
                            <div class="col-12 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-2">
                                        <h6 class="card-title mb-2">
                                            <i class="ti ti-report-money me-1"></i>
                                            Informasi Penyaluran Dana ke Perusahaan
                                        </h6>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Total Dana Disalurkan</small>
                                                <strong class="text-primary">Rp
                                                    {{ number_format($total_dana_disalurkan ?? 0, 0, ',', '.') }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Total Bagi Hasil Sudah
                                                    Dikembalikan</small>
                                                <strong class="text-success">Rp
                                                    {{ number_format($total_bagi_hasil_dikembalikan ?? 0, 0, ',', '.') }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Sisa Dana di Perusahaan</small>
                                                <strong class="text-warning">Rp
                                                    {{ number_format($sisa_dana_di_perusahaan ?? 0, 0, ',', '.') }}</strong>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Dana Pokok Tersedia untuk
                                                    Dikembalikan</small>
                                                <strong class="text-info">Rp
                                                    {{ number_format($dana_pokok_tersedia ?? 0, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                        @if($sisa_dana_di_perusahaan > 0)
                                            <div class="alert alert-warning mt-2 mb-0 py-1">
                                                <small>
                                                    <i class="ti ti-alert-triangle me-1"></i>
                                                    <strong>Perhatian:</strong> Masih ada Rp
                                                    {{ number_format($sisa_dana_di_perusahaan, 0, ',', '.') }} yang belum
                                                    dikembalikan dari perusahaan.
                                                    Dana pokok yang bisa dikembalikan ke investor maksimal <strong>Rp
                                                        {{ number_format($dana_pokok_tersedia, 0, ',', '.') }}</strong>
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($bulan_berjalan > 0)
                            <div class="col-12 mb-3">
                                <div class="alert alert-info mb-0">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <strong>Bulan Berjalan: {{ $bulan_berjalan }} / {{ $lama_investasi ?? 0 }}</strong>
                                    @if($is_bulan_terakhir)
                                        <span class="badge bg-success ms-2">Bulan Terakhir</span>
                                    @endif
                                    @if($info_periode)
                                        <br><small>{{ $info_periode }}</small>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($jumlah_transaksi > 0)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Pokok Sudah Dikembalikan</label>
                                <input type="text" class="form-control text-success fw-bold"
                                    value="Rp {{ number_format($total_pokok_dikembalikan ?? 0, 0, ',', '.') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Bagi Hasil Sudah Dikembalikan</label>
                                <input type="text" class="form-control text-success fw-bold"
                                    value="Rp {{ number_format($total_bagi_hasil_dikembalikan ?? 0, 0, ',', '.') }}"
                                    readonly>
                            </div>
                        @endif

                        @php
                            $sisaBagiHasil = $sisa_bagi_hasil_db ?? 0;
                            $canPayBagiHasil = $sisaBagiHasil > 0 && ($bisa_bayar_bagi_hasil ?? false);
                        @endphp
                        <div class="col-md-6 mb-3 form-group">
                            <label for="bagi_hasil_dibayar_finlog" class="form-label">
                                Bagi Hasil Yang Dibayarkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('bagi_hasil_dibayar') is-invalid @enderror"
                                id="bagi_hasil_dibayar_finlog" placeholder="Ketik angka saja (contoh: 1000000)"
                                autocomplete="off" @if(!$canPayBagiHasil) disabled @endif>
                            <input type="hidden" id="bagi_hasil_raw_finlog" wire:model="bagi_hasil_dibayar">
                            @error('bagi_hasil_dibayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(!$canPayBagiHasil)
                                @if($sisaBagiHasil <= 0)
                                    <small class="text-success">
                                        Bagi Hasil sudah lunas, tidak perlu diisi lagi.
                                    </small>
                                @else
                                    <small class="text-warning">
                                        Bagi Hasil hanya dapat dibayarkan di bulan genap (bulan ke-2, 4, 6, dst) atau bulan
                                        terakhir.
                                    </small>
                                @endif
                            @endif
                        </div>

                        @php
                            $sisaPokokInfo = $sisa_pokok_db ?? 0;
                            $canPayPokok = ($bisa_bayar_pokok ?? false) && $sisaPokokInfo > 0;
                        @endphp

                        <div class="col-md-6 mb-3 form-group">
                            <label for="dana_pokok_dibayar_finlog" class="form-label">
                                Dana Pokok Yang Dibayarkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('dana_pokok_dibayar') is-invalid @enderror"
                                id="dana_pokok_dibayar_finlog" placeholder="Ketik angka saja (contoh: 10000000)"
                                autocomplete="off" @if(!$canPayPokok) disabled @endif>
                            <input type="hidden" id="dana_pokok_raw_finlog" wire:model="dana_pokok_dibayar">
                            @error('dana_pokok_dibayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(!$canPayPokok)
                                @if(!($is_bulan_terakhir ?? false))
                                    <small class="text-warning">
                                        Dana pokok hanya dapat dibayarkan di bulan terakhir investasi (bulan
                                        ke-{{ $lama_investasi ?? 0 }}).
                                    </small>
                                @elseif($sisaBagiHasil > 0)
                                    <small class="text-danger">
                                        Dana pokok baru bisa dibayarkan jika Bagi Hasil sudah lunas.
                                    </small>
                                @elseif($sisaPokokInfo <= 0)
                                    <small class="text-success">
                                        Dana pokok sudah lunas, tidak perlu diisi lagi.
                                    </small>
                                @endif
                            @endif
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="tanggal_pengembalian_finlog" class="form-label">
                                Tanggal Pengembalian <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('tanggal_pengembalian') is-invalid @enderror"
                                id="tanggal_pengembalian_finlog" wire:model="tanggal_pengembalian"
                                placeholder="Pilih tanggal">
                            @error('tanggal_pengembalian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="bukti_transfer_finlog" class="form-label">
                                Bukti Transfer <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('bukti_transfer') is-invalid @enderror"
                                id="bukti_transfer_finlog" wire:model="bukti_transfer" accept=".pdf,.jpg,.jpeg,.png">
                            <div wire:loading wire:target="bukti_transfer" class="mt-1">
                                <small class="text-primary">
                                    <span class="spinner-border spinner-border-sm me-1"></span>Uploading...
                                </small>
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
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="saveData">
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