<div x-data="{ wireId: @js($this->getId()) }">
    <div>
        <a wire:navigate.hover href="{{ route('sfinlog.pengembalian-pinjaman.index') }}"
            class="btn btn-outline-primary mb-4">
            <i class="fa-solid fa-arrow-left me-2"></i>
            Kembali
        </a>
        <h4 class="fw-bold">
            Menu Pengembalian Peminjaman Finlog
        </h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="store">
                <div class="row">
                    <div class="col-lg mb-3">
                        <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="nama_perusahaan" value="{{ $nama_perusahaan }}"
                            readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg mb-3">
                        <label for="kode_peminjaman" class="form-label">Kode Peminjaman <span
                                class="text-danger">*</span></label>
                        <livewire:components.select2 
                            :list_data="$peminjamanList"
                            value_name="id"
                            value_label="text"
                            data_placeholder="Pilih Kode Peminjaman"
                            model_name="id_peminjaman_finlog"
                            :value="$id_peminjaman_finlog"
                            :key="'select2-peminjaman-' . now()->timestamp"
                        />
                        @error('id_peminjaman_finlog') 
                            <small class="text-danger">{{ $message }}</small> 
                        @enderror
                    </div>
                </div>
                <div class="card border-1 shadow-none mb-4" wire:key="peminjaman-detail-{{ $id_peminjaman_finlog }}">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="cells_bisnis" class="form-label">Cells Bisnis</label>
                                <input type="text" class="form-control" value="{{ $cells_bisnis }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_project" class="form-label">Nama Project</label>
                                <input type="text" class="form-control" value="{{ $nama_project }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_pencairan" class="form-label">Tanggal Pencairan</label>
                                <input type="text" class="form-control" value="{{ $tanggal_pencairan }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="top" class="form-label">TOP</label>
                                <input type="text" class="form-control" value="{{ $top }}" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jatuh_tempo" class="form-label">Jatuh Tempo</label>
                                <input type="text" class="form-control" value="{{ $jatuh_tempo }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="nilai_pinjaman" class="form-label">Nilai Pinjaman</label>
                                <input type="text" class="form-control" 
                                    value="Rp {{ number_format($nilai_pinjaman, 0, ',', '.') }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nilai_bagi_hasil" class="form-label">Bagi Hasil</label>
                                <input type="text" class="form-control" 
                                    value="Rp {{ number_format($nilai_bagi_hasil, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="total_pinjaman" class="form-label">Total Pinjaman</label>
                                <input type="text" class="form-control" 
                                    value="Rp {{ number_format($total_pinjaman, 0, ',', '.') }}" readonly>
                            </div>
                        </div>

                        <div class="card shadow-none border mb-4 financing-table" id="pengembalianInvoicetable">
                            <div class="card-header">
                                <h5 class="card-title mb-0">List Pengembalian Invoice</h5>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Nominal Yang Dibayarkan</th>
                                            <th>Bukti Pembayaran</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pengembalianList as $index => $item)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ Storage::url($item['bukti_file']) }}" 
                                                        target="_blank" class="btn btn-sm btn-outline-info">
                                                        <i class="ti ti-eye me-1"></i> Lihat
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                        wire:click="removePengembalian({{ $index }})"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus pengembalian ini?">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    Belum ada pengembalian invoice
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary wave-effect mb-3"
                            data-bs-toggle="modal" data-bs-target="#modal-pengembalian-invoice">
                            <i class="fa-solid fa-plus me-1"></i>
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sisa_utang" class="form-label">Sisa Bayar Pokok</label>
                        <input type="text" class="form-control" 
                            value="Rp {{ number_format($sisa_utang, 0, ',', '.') }}" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sisa_bagi_hasil" class="form-label">Sisa Bagi Hasil</label>
                        <input type="text" class="form-control" 
                            value="Rp {{ number_format($sisa_bagi_hasil, 0, ',', '.') }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg">
                        <label for="catatan">Catatan Lainnya</label>
                        <textarea wire:model="catatan" id="catatan" class="form-control" placeholder="Masukkan Catatan" rows="3"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="ti ti-device-floppy me-1"></i> Simpan Data
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1" role="status"
                                aria-hidden="true"></span>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pengembalian Invoice -->
    @include('livewire.sfinlog.pengembalian-pinjaman.partials.modal')
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen untuk event close modal
        Livewire.on('close-pengembalian-modal', () => {
            $('#modal-pengembalian-invoice').modal('hide');
        });
    });
</script>
@endpush
