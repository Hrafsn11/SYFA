<div>
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
            <form action="">
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
                        <select name="kode_peminjaman" id="kode_peminjaman" class="form-control">
                            <option value="">Pilih Peminjaman</option>
                        </select>
                    </div>
                </div>
                <div class="card border-1 shadow-none mb-4">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="cells_bisnis" class="form-label">Cells Bisnis</label>
                                <input type="text" class="form-control" id="cells_bisnis_display" readonly>
                                <input type="hidden" wire:model="cells_bisnis">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_project" class="form-label">Nama Project</label>
                                <input type="text" class="form-control" id="nama_project_display" readonly>
                                <input type="hidden" wire:model="nama_project">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-3">
                                <label for="tanggal_pencairan" class="form-label">Tanggal Pencairan</label>
                                <input type="text" class="form-control" id="tanggal_pencairan_display" readonly>
                                <input type="hidden" wire:model="tanggal_pencairan">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="top" class="form-label">TOP</label>
                                <input type="text" class="form-control" id="top_display" readonly>
                                <input type="hidden" wire:model="top">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="rencana_tanggal_pengembalian" class="form-label">Rencana Tanggal
                                    Pengembalian</label>
                                <input type="text" class="form-control" id="rencana_tanggal_pengembalian_display"
                                    readonly>
                                <input type="hidden" wire:model="rencana_tanggal_pengembalian">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="nilai_pinjaman" class="form-label">Nilai Peminjaman</label>
                                <input type="text" class="form-control" id="nilai_pinjaman_display" readonly>
                                <input type="hidden" wire:model="nilai_pinjaman">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nilai_bagi_hasil" class="form-label">Nilai Bagi Hasil</label>
                                <input type="text" class="form-control" id="nilai_bagi_hasil_display" readonly>
                                <input type="hidden" wire:model="nilai_bagi_hasil">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="total_pinjaman" class="form-label">Total Pinjaman</label>
                                <input type="text" class="form-control" id="total_pinjaman_display" readonly>
                                <input type="hidden" wire:model="total_pinjaman">
                            </div>
                        </div>

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

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary wave-effect mb-3"
                            id="btnTambahPengembalian">
                            <i class="fa-solid fa-plus me-1"></i>
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sisa_utang" class="form-label">Sisa Bayar Pokok</label>
                        <input type="text" class="form-control" id="sisa_utang_display" readonly>
                        <input type="hidden" wire:model="sisa_utang">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sisa_bagi_hasil" class="form-label">Sisa Bagi Hasil</label>
                        <input type="text" class="form-control" id="sisa_bagi_hasil_display" readonly>
                        <input type="hidden" wire:model="sisa_bagi_hasil">
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
</div>
