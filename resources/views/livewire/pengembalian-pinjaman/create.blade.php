@extends('layouts.app')

@section('content')
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
                <form action="#">
                    <div class="row">
                        <div class="col-lg mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan"
                                value="Techno Infinity" required disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg mb-3">
                            <label for="kode_peminjaman" class="form-label">Kode Peminjaman</label>
                            <select class="form-control select2" name="kode_peminjaman" id="kode_peminjaman" data-placeholder="Pilih Peminjaman">
                                <option value="">Pilih Peminjaman</option>
                            </select>
                        </div>
                    </div>
                    <div class="card border-1 shadow-none mb-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="total_pinjaman">Total Pinjaman</label>
                                    <input type="text" class="form-control" id="total_pinjaman" name="total_pinjaman"
                                        value="Rp.250.000.000" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="total_bagi_hasil">Total Bagi Hasil</label>
                                    <input type="text" class="form-control" id="total_bagi_hasil" name="total_bagi_hasil"
                                        value="2% (Rp.50.000.000)" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="invoice">Invoice Yang Akan Dibayar</label>
                                    <select name="invoice" id="invoice" class="form-control select2" data-placeholder="Pilih Invoice">
                                        <option value="">Pilih Invoice</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="nominal_invoice">Nominal Invoice</label>
                                    <input type="text" class="form-control" id="nominal_invoice" name="nominal_invoice"
                                        value="Rp.250.000.000" disabled>
                                </div>
                            </div>
                            @include('livewire.pengembalian-pinjaman.partials._pengembalian-table')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg mb-3">
                            <label for="sisa_utang" class="form-label">Sisa Utang</label>
                            <input type="text" class="form-control" id="sisa_utang" name="sisa_utang"
                                value="Rp. 50.000.000" required disabled>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg">
                            <label for="keterangan">Catatan Lainnya</label>
                            <textarea name="catatan" id="catatan" class="form-control" placeholder="Masukkan Catatan"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">
                            Simpan Data
                            <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('livewire.pengembalian-pinjaman.partials._modal-tambah-pengembalian-invoice')
@endsection

@push('scripts')
<script>
    let modalInstance = $('#modalPengembalian');
    $(document).ready(function() {

        initCleaveRupiah();

        $('#btnTambahPengembalian').on('click', function() {
            openModal();
        });
    });

    function openModal() {
        
        $('.modal-form-content').hide();

        $('.modal-form-content input[type="text"]').val('');
        $('.modal-form-content input[type="file"]').val('');

        $('#modalTitle').text('Tambah Pengembalian Invoice');

        setTimeout(function() {
            initCleaveRupiah(); 
        }, 100);

        modalInstance.modal('show');

        console.log(modalInstance);
        
    }
</script>
@endpush
