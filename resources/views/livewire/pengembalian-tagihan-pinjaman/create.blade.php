<div>
    <div>
        <a href="{{ route('pengembalian.index') }}" class="btn btn-outline-primary mb-4">
            <i class="fa-solid fa-arrow-left me-2"></i>
            Kembali
        </a>
        <h4 class="fw-bold">
            Menu Pengembalian Peminjaman
        </h4>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-lg mb-3">
                        <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="nama_perusahaan" wire:model="nama_perusahaan"
                            readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg mb-3">
                        <label for="kode_peminjaman" class="form-label">Kode Peminjaman <span
                                class="text-danger">*</span></label>
                        <livewire:components.select2 :list_data="$pengajuanPeminjaman" model_name="kode_peminjaman"
                            value_name="id_pengajuan_peminjaman" value_label="nomor_peminjaman"
                            data_placeholder="Pilih Peminjaman" :value="$kode_peminjaman" />
                        @error('kode_peminjaman')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card border-1 shadow-none mb-4">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label for="total_pinjaman">Total Pinjaman</label>
                                <input type="text" class="form-control" id="total_pinjaman"
                                    value="{{ $total_pinjaman ? 'Rp ' . number_format($total_pinjaman, 0, ',', '.') : '' }}"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="total_bagi_hasil">
                                    Total Bunga
                                    @if ($isLate && $bagiHasilTambahan > 0)
                                        <span class="badge bg-warning ms-1">Disesuaikan</span>
                                    @endif
                                </label>
                                <input type="text" class="form-control {{ $isLate ? 'border-warning' : '' }}"
                                    id="total_bagi_hasil"
                                    value="{{ $total_bagi_hasil ? 'Rp ' . number_format($total_bagi_hasil, 0, ',', '.') : '' }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label for="tanggal_pencairan">Tanggal Pencairan</label>
                                <input type="text" class="form-control" id="tanggal_pencairan"
                                    value="{{ $tanggal_pencairan }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="lama_pemakaian">Lama Pemakaian (Hari)</label>
                                <input type="text" class="form-control" id="lama_pemakaian"
                                    value="{{ $lama_pemakaian ?? 0 }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label for="invoice_dibayarkan">
                                    @if ($jenisPembiayaan === 'Invoice Financing')
                                        Invoice Yang Akan Dibayar
                                    @elseif(in_array($jenisPembiayaan, ['PO Financing', 'Factoring']))
                                        Kontrak Yang Akan Dibayar
                                    @else
                                        Yang Akan Dibayar
                                    @endif
                                    <span class="text-danger">*</span>
                                </label>
                                @if ($jenisPembiayaan === 'Installment')
                                    <input type="text" class="form-control" value="{{ $bulan_pembayaran ?? '' }}"
                                        readonly>
                                @else
                                    <livewire:components.select2 :key="'invoice-select-' . $kode_peminjaman" :list_data="collect($availableInvoices)"
                                        model_name="invoice_dibayarkan" value_name="label" value_label="label"
                                        :data_placeholder="$jenisPembiayaan === 'Invoice Financing' ? 'Pilih Invoice' : 'Pilih Kontrak'" :value="$invoice_dibayarkan" />
                                @endif
                                @error('invoice_dibayarkan')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="nominal_invoice">
                                    @if ($jenisPembiayaan === 'Invoice Financing')
                                        Nominal Invoice
                                    @elseif(in_array($jenisPembiayaan, ['PO Financing', 'Factoring']))
                                        Nominal Kontrak
                                    @else
                                        Nominal
                                    @endif
                                </label>
                                <input type="text" class="form-control" id="nominal_invoice"
                                    value="{{ $nominal_invoice ? 'Rp ' . number_format($nominal_invoice, 0, ',', '.') : '' }}"
                                    readonly>
                            </div>
                        </div>

                        {{-- Tanggal Jatuh Tempo --}}
                        @if ($tanggal_jatuh_tempo)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-{{ $isLate ? 'danger' : 'info' }} py-2 mb-0">
                                        <i class="fa-solid fa-calendar-clock me-2"></i>
                                        <strong>Tanggal Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($tanggal_jatuh_tempo)->format('d F Y') }}
                                        @if ($isLate)
                                            <span class="badge bg-danger ms-2">Sudah Jatuh Tempo</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Khusus Installment -->
                        @if ($jenisPembiayaan === 'Installment')
                            {{-- Info Tenor --}}
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="alert alert-primary py-2 mb-0">
                                        <i class="fa-solid fa-calendar-check me-2"></i>
                                        <strong>{{ $infoTenor ?? 'Menghitung tenor...' }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="bulan_pembayaran">Pembayaran Tenor</label>
                                    <input type="text" class="form-control" id="bulan_pembayaran"
                                        value="{{ $bulan_pembayaran ?? '' }}" readonly>
                                    @error('bulan_pembayaran')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="yang_harus_dibayarkan">Yang Harus Dibayar Tenor Ini</label>
                                    <input type="text" class="form-control" id="yang_harus_dibayarkan"
                                        value="{{ $yang_harus_dibayarkan ? 'Rp ' . number_format($yang_harus_dibayarkan, 0, ',', '.') : '' }}"
                                        readonly>
                                </div>
                            </div>
                        @endif

                        <!-- Pengembalian Invoice Table -->
                        <div class="card shadow-none border mb-4 financing-table" id="pengembalianInvoicetable">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Tabel Pengembalian Invoice</h5>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>Nominal yang akan dibayarkan</th>
                                            <th>Bukti Pembayaran</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0" id="pengembalianTableBody">
                                        @if (count($pengembalian_invoices) > 0)
                                            @foreach ($pengembalian_invoices as $index => $invoice)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>Rp {{ number_format($invoice['nominal'], 0, ',', '.') }}</td>
                                                    <td>
                                                        @if (isset($invoice['file_name']))
                                                            <span
                                                                class="badge bg-success">{{ $invoice['file_name'] }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">Belum upload</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button"
                                                            class="btn btn-sm btn-warning btn-edit-pengembalian"
                                                            data-idx="{{ $index }}">
                                                            <i class="ti ti-edit"></i>
                                                        </button>
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger btn-remove-pengembalian"
                                                            data-idx="{{ $index }}">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr id="emptyRow">
                                                <td colspan="4" class="text-center text-muted">Belum ada data
                                                    pengembalian</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary wave-effect mb-3"
                            id="btnTambahPengembalian">
                            <i class="fa-solid fa-plus me-1"></i>
                            Tambah
                        </button>
                        @error('pengembalian_invoices')
                            <span class="text-danger small d-block mb-3">{{ $message }}</span>
                        @enderror

                        {{-- Sisa Bayar Section --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sisa_utang" class="form-label">Sisa Bayar Pokok</label>
                                <input type="text" class="form-control"
                                    value="{{ $sisa_utang ? 'Rp ' . number_format($sisa_utang, 0, ',', '.') : 'Rp 0' }}"
                                    readonly>
                                <input type="hidden" wire:model="sisa_utang">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sisa_bagi_hasil" class="form-label">
                                    Sisa Bunga
                                    @if ($isLate && $bagiHasilTambahan > 0)
                                        <span class="badge bg-warning ms-1">Disesuaikan</span>
                                    @endif
                                </label>
                                <input type="text" class="form-control {{ $isLate ? 'border-warning' : '' }}"
                                    value="{{ $sisa_bagi_hasil ? 'Rp ' . number_format($sisa_bagi_hasil, 0, ',', '.') : 'Rp 0' }}"
                                    readonly>
                                <input type="hidden" wire:model="sisa_bagi_hasil">
                            </div>
                        </div>

                        {{-- Late Payment Alert - Show near Sisa fields if payment is late --}}
                        @if ($isLate && $bulanKeterlambatan > 0)
                            <div class="alert alert-warning border-warning mb-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-2">
                                            <i class="ti ti-clock-exclamation me-1"></i>
                                            Pembayaran Terlambat {{ $bulanKeterlambatan }} Bulan
                                        </h6>
                                        <p class="mb-2 small">
                                            Karena pembayaran melewati jatuh tempo
                                            <strong>({{ $selectedDueDate ? \Carbon\Carbon::parse($selectedDueDate)->format('d M Y') : '-' }})</strong>,
                                            bunga disesuaikan berdasarkan sisa pokok.
                                        </p>
                                        <hr class="my-2">
                                        <div class="row small">
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Bunga Awal:</span>
                                                    <span>Rp {{ number_format($bagiHasilAwal, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Persentase Bunga:</span>
                                                    <span>{{ number_format($persentaseBagiHasil, 2) }}%</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Sisa Pokok:</span>
                                                    <span>Rp {{ number_format($total_pinjaman, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Keterlambatan:</span>
                                                    <span class="text-warning fw-semibold">{{ $bulanKeterlambatan }}
                                                        Bulan</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-muted">Bunga Tambahan:</span>
                                                    <span class="text-danger fw-semibold">+ Rp
                                                        {{ number_format($bagiHasilTambahan, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-semibold">Total Bunga:</span>
                                                    <span class="fw-bold text-warning">Rp
                                                        {{ number_format($totalBagiHasilDisesuaikan, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2 p-2 bg-light rounded small">
                                            <strong>Rumus:</strong> Sisa Pokok ×
                                            {{ number_format($persentaseBagiHasil, 2) }}% × {{ $bulanKeterlambatan }}
                                            bulan =
                                            <span class="text-danger">Rp
                                                {{ number_format($bagiHasilTambahan, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('livewire.pengembalian-pinjaman.partials._modal-tambah-pengembalian-invoice')

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                const modal = new bootstrap.Modal(document.getElementById('modal-pengembalian-invoice'));
                const elements = {
                    modalTitle: document.getElementById('modal-pengembalian-invoice').querySelector('.modal-title'),
                    nominalInput: document.getElementById('nominal_yang_dibayarkan'),
                    fileInput: document.getElementById('bukti_pembayaran_invoice'),
                    saveButton: document.getElementById('btnSavePengembalian'),
                    fileInfoDiv: document.getElementById('currentFileInfo'),
                    fileNameSpan: document.getElementById('currentFileName')
                };
                let editingIndex = null;

                const formatCurrency = (value) => 'Rp ' + (parseInt(value) || 0).toLocaleString('id-ID');

                const resetButton = () => {
                    elements.saveButton.disabled = false;
                    elements.saveButton.innerHTML = 'Simpan <i class="ti ti-check ms-1"></i>';
                };

                const attachSelect2Listeners = () => {
                    const select2Fields = ['kode_peminjaman', 'invoice_dibayarkan', 'bulan_pembayaran'];

                    select2Fields.forEach(field => {
                        // Cari elemen select, baik by ID maupun class select2 standard
                        const $el = $(`#${field}`);
                        if ($el.length) {
                            $el.off('select2:select.custom').on('select2:select.custom', function(e) {
                                const val = $(this).val();
                                @this.set(field, val);
                            });

                            $el.off('select2:clear.custom').on('select2:clear.custom', function(e) {
                                @this.set(field, null);
                            });
                        }
                    });
                };

                // Init pertama kali
                attachSelect2Listeners();

                // Re-init setiap kali Livewire selesai update DOM
                Livewire.hook('morph.updated', () => {
                    setTimeout(attachSelect2Listeners, 100);
                });

                // Re-init khusus saat data invoice di-refresh dari server (via dispatch di PHP)
                @this.on('init-select2-invoice', () => {
                    setTimeout(attachSelect2Listeners, 200);
                });

                @this.on('alert', (event) => {
                    const data = event[0] || event;
                    showSweetAlert({
                        icon: data.type === 'success' ? 'success' : 'error',
                        title: data.type === 'success' ? 'Berhasil' : 'Error',
                        text: data.message
                    });
                });

                @this.on('closeInvoiceModal', () => {
                    modal.hide();
                    if (elements.nominalInput._cleaveInstance) {
                        elements.nominalInput._cleaveInstance.setRawValue('');
                    }
                });

                document.getElementById('btnTambahPengembalian').addEventListener('click', () => {
                    editingIndex = null;
                    elements.modalTitle.innerText = 'Tambah Pengembalian Invoice';
                    elements.nominalInput.value = '';
                    elements.fileInput.value = '';
                    modal.show();
                });

                document.addEventListener('click', (e) => {
                    const editBtn = e.target.closest('.btn-edit-pengembalian');
                    const deleteBtn = e.target.closest('.btn-remove-pengembalian');

                    if (editBtn) {
                        editingIndex = editBtn.dataset.idx;
                        const invoiceData = @this.get('pengembalian_invoices')[editingIndex];

                        if (!invoiceData) return;

                        elements.modalTitle.innerText = 'Edit Pengembalian Invoice';

                        setTimeout(() => {
                            if (elements.nominalInput._cleaveInstance) {
                                elements.nominalInput._cleaveInstance.setRawValue(invoiceData.nominal ||
                                    0);
                            } else {
                                elements.nominalInput.value = formatCurrency(invoiceData.nominal);
                            }

                            if (invoiceData.file_name) {
                                elements.fileNameSpan.textContent = invoiceData.file_name;
                                elements.fileInfoDiv.style.display = 'block';
                            } else {
                                elements.fileInfoDiv.style.display = 'none';
                            }
                        }, 100);

                        modal.show();
                    }

                    if (deleteBtn) {
                        Swal.fire({
                            title: 'Yakin ingin menghapus?',
                            text: "Data yang dihapus tidak dapat dikembalikan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) @this.call('deleteInvoice', deleteBtn.dataset.idx);
                        });
                    }
                });

                elements.saveButton.addEventListener('click', () => {
                    const nominal = elements.nominalInput._cleaveInstance ?
                        elements.nominalInput._cleaveInstance.getRawValue() :
                        parseInt(elements.nominalInput.value.replace(/[^0-9]/g, '')) || 0;

                    if (!nominal) {
                        Swal.fire('Error', 'Nominal harus diisi!', 'error');
                        return;
                    }

                    elements.saveButton.disabled = true;
                    elements.saveButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                    const formData = {
                        nominal: parseInt(nominal),
                        editingIndex
                    };
                    const file = elements.fileInput.files[0];

                    if (file) {
                        const maxSize = 2048 * 1024;
                        const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];

                        if (file.size > maxSize) {
                            Swal.fire('Error', 'Ukuran file maksimal 2MB!', 'error');
                            resetButton();
                            return;
                        }

                        if (!allowedTypes.includes(file.type)) {
                            Swal.fire('Error', 'Format file harus PDF, PNG, JPG, atau JPEG!', 'error');
                            resetButton();
                            return;
                        }

                        @this.upload('modalFile', file,
                            () => @this.call('saveInvoice', formData).finally(resetButton),
                            () => {
                                Swal.fire('Error', 'Gagal mengupload file!', 'error');
                                resetButton();
                            }
                        );
                    } else {
                        @this.call('saveInvoice', formData).finally(resetButton);
                    }
                });

                document.getElementById('modal-pengembalian-invoice').addEventListener('hidden.bs.modal', () => {
                    editingIndex = null;
                    resetButton();

                    if (elements.nominalInput._cleaveInstance) {
                        elements.nominalInput._cleaveInstance.setRawValue('');
                    } else {
                        elements.nominalInput.value = '';
                    }

                    elements.fileInput.value = '';
                    elements.fileInfoDiv.style.display = 'none';
                    @this.set('modalFile', null);
                });
            });
        </script>
    @endpush
</div>
