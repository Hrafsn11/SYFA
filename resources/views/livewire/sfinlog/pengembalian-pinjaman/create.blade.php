<div>
    {{-- Header Section --}}
    <div class="mb-4">
        <a wire:navigate.hover href="{{ route('sfinlog.pengembalian-pinjaman.index') }}"
            class="btn btn-outline-primary mb-3">
            <i class="fa-solid fa-arrow-left me-2"></i>
            Kembali
        </a>
        <h4 class="fw-bold">Menu Pengembalian Peminjaman Finlog</h4>
    </div>

    {{-- Main Form Card --}}
    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="store">
                {{-- Nama Perusahaan --}}
                <div class="mb-3">
                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                    <input type="text" class="form-control" id="nama_perusahaan" value="{{ $nama_perusahaan }}"
                        readonly>
                </div>

                {{-- Kode Peminjaman --}}
                <div class="mb-3" wire:key="select2-container-{{ $currentUserId ?? 'guest' }}">
                    <label for="kode_peminjaman" class="form-label">
                        Kode Peminjaman <span class="text-danger">*</span>
                    </label>
                    {{-- Hidden input to preserve value --}}
                    <input type="hidden" wire:model="id_pinjaman_finlog" id="hidden_id_pinjaman_finlog">
                    <div wire:ignore>
                        <livewire:components.select2 :list_data="$peminjamanList" value_name="id" value_label="text"
                            data_placeholder="Pilih Kode Peminjaman" model_name="id_pinjaman_finlog" :value="$id_pinjaman_finlog"
                            :key="'select2-peminjaman-finlog-v2'" />
                    </div>
                    @error('id_pinjaman_finlog')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror

                </div>

                {{-- Peminjaman Details Card - Use static key to prevent recreation --}}
                <div class="card border shadow-none mb-4" wire:key="peminjaman-detail-card">
                    <div class="card-body">
                        {{-- Row 1: Cells Bisnis & Nama Project --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Cells Bisnis</label>
                                <input type="text" class="form-control" value="{{ $cells_bisnis }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Project</label>
                                <input type="text" class="form-control" value="{{ $nama_project }}" readonly>
                            </div>
                        </div>

                        {{-- Row 2: Tanggal, TOP, Jatuh Tempo --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tanggal Pencairan</label>
                                <input type="text" class="form-control" value="{{ $tanggal_pencairan }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">TOP (Term of Payment)</label>
                                <input type="text" class="form-control" value="{{ $top }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jatuh Tempo</label>
                                <input type="text" class="form-control" value="{{ $jatuh_tempo }}" readonly>
                            </div>
                        </div>

                        {{-- Row 3: Nilai Awal Pinjaman --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Nilai Pokok Awal</label>
                                <input type="text" class="form-control"
                                    value="Rp {{ number_format($nilai_pinjaman, 0, ',', '.') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bagi Hasil Awal</label>
                                <input type="text" class="form-control"
                                    value="Rp {{ number_format($nilai_bagi_hasil, 0, ',', '.') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total Awal</label>
                                <input type="text" class="form-control"
                                    value="Rp {{ number_format($total_pinjaman, 0, ',', '.') }}" readonly>
                            </div>
                        </div>

                        {{-- Row 4: Keterlambatan (jika ada) --}}
                        @if ($jumlah_minggu_keterlambatan > 0)
                            <div class="alert alert-warning mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-alert-triangle me-2 fs-4"></i>
                                    <div>
                                        <strong>Pinjaman Terlambat!</strong>
                                        <span class="ms-2">{{ $jumlah_minggu_keterlambatan }} minggu
                                            keterlambatan</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- List Pengembalian Invoice Table --}}
                        <div class="card shadow-none border mb-3" wire:key="pengembalian-table-container">
                            <div class="card-header">
                                <h5 class="card-title mb-0">List Pengembalian Invoice</h5>
                            </div>
                            <div class="table-responsive">
                                @error('pengembalian_list')
                                    <div class="alert alert-danger m-3">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th>Nominal Yang Dibayarkan</th>
                                            <th>Bukti Pembayaran</th>
                                            <th width="10%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pengembalian_list as $index => $item)
                                            <tr wire:key="pengembalian-row-{{ $index }}">
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                                                <td>
                                                    @if ($item['bukti_file'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                                        <a href="{{ $item['bukti_file']->temporaryUrl() }}"
                                                            target="_blank" class="btn btn-sm btn-outline-info">
                                                            <i class="ti ti-eye me-1"></i> Lihat
                                                        </a>
                                                    @elseif(is_string($item['bukti_file']))
                                                        <a href="{{ Storage::url($item['bukti_file']) }}"
                                                            target="_blank" class="btn btn-sm btn-outline-info">
                                                            <i class="ti ti-eye me-1"></i> Lihat
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
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

                        {{-- Button Tambah --}}
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#modal-pengembalian-invoice">
                            <i class="fa-solid fa-plus me-1"></i>
                            Tambah
                        </button>
                    </div>
                </div>

                {{-- Sisa Yang Harus Dibayar --}}
                <div class="card border shadow-none mb-3">
                    <div class="card-body pt-2">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Sisa Pokok</label>
                                <input type="text" class="form-control fw-bold"
                                    value="Rp {{ number_format($sisa_utang, 0, ',', '.') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Sisa Bagi Hasil @if ($jumlah_minggu_keterlambatan > 0)
                                        <span class="text-danger">(+Denda)</span>
                                    @endif
                                </label>
                                <input type="text" class="form-control fw-bold"
                                    value="Rp {{ number_format($sisa_bagi_hasil, 0, ',', '.') }}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Total Sisa</label>
                                <input type="text" class="form-control fw-bold text-primary"
                                    value="Rp {{ number_format($sisa_utang + $sisa_bagi_hasil, 0, ',', '.') }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan Lainnya</label>
                    <textarea wire:model="catatan" id="catatan" class="form-control" placeholder="Masukkan Catatan" rows="3"></textarea>
                </div>

                {{-- Submit Button --}}
                <div class="d-flex justify-content-end">
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

    {{-- Modal Pengembalian Invoice --}}
    @include('livewire.sfinlog.pengembalian-pinjaman.partials.modal')
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize modal reference
            const modalEl = document.getElementById('modal-pengembalian-invoice');
            let bsModal = null;

            if (modalEl) {
                bsModal = new bootstrap.Modal(modalEl);

                // Reset form fields when modal is closed
                modalEl.addEventListener('hidden.bs.modal', () => {
                    const fileInput = document.getElementById('file-upload-bukti');
                    if (fileInput) fileInput.value = '';

                    // Reset Livewire properties
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('reset-modal-fields');
                    }
                });
            }

            // Listen: Alert Events
            Livewire.on('alert', (data) => {
                const eventData = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: eventData.icon,
                    title: eventData.icon === 'error' ? 'Error!' : (eventData.icon === 'success' ?
                        'Berhasil!' : 'Perhatian'),
                    html: eventData.html,
                    timer: eventData.icon === 'success' ? 2500 : undefined,
                    showConfirmButton: eventData.icon !== 'success'
                });
            });

            // Listen: Close Modal Event
            Livewire.on('close-pengembalian-modal', () => {
                console.log('Closing modal...');
                if (modalEl) {
                    // Try using Bootstrap's modal instance
                    const existingModal = bootstrap.Modal.getInstance(modalEl);
                    if (existingModal) {
                        existingModal.hide();
                    } else if (bsModal) {
                        bsModal.hide();
                    } else {
                        // Fallback: force close using jQuery or vanilla JS
                        modalEl.classList.remove('show');
                        modalEl.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                    }

                    // Reset file input
                    const fileInput = document.getElementById('file-upload-bukti');
                    if (fileInput) fileInput.value = '';
                }
            });
        });
    </script>
@endpush
