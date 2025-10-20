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
                                    <select name="invoice" id="invoice" class="form-select">
                                        <option value="">Pilih Invoice</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="nominal_invoice">Nominal Invoice</label>
                                    <input type="text" class="form-control" id="nominal_invoice" name="nominal_invoice"
                                        value="Rp.250.000.000" disabled>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="nominal_dibayarkan">Nominal Yang Akan Dibayarkan</label>
                                    <input type="text" class="form-control" id="nominal_dibayarkan"
                                        name="nominal_dibayarkan" value="Rp.250.000.000">
                                </div>
                                <div class="col-md-6">
                                    <label for="bukti_pembayaran">Bukti Pembayaran</label>
                                    <input type="file" class="form-control" id="bukti_pembayaran"
                                        name="bukti_pembayaran">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg">
                            <label for="keterangan">Catatan Lainnya</label>
                            <textarea name="catatan" id="catatan" class="form-control"></textarea>
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
@endsection
