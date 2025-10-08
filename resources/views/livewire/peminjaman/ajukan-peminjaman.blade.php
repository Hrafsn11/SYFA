@section('title', 'Menu Pengajuan Peminjaman')

<div>
    <div>
        <a href="{{ route('peminjaman') }}" class="btn btn-outline-primary mb-4">
            <i class="tf-icons ti ti-arrow-left me-1"></i>
            Kembali
        </a>
        <h4 class="fw-bold">Menu Pengajuan Peminjaman</h4>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg mb-3">
                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                    <input type="text" class="form-control" id="nama_perusahaan" value="Techno Infinity" disabled>
                </div>
            </div>
            <div class="card border-1 mb-3 shadow-none">
                <div class="card-body ">
                    <div class="col-md-12">
                        <label class="form-label">Sumber Pembiayaan</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input name="sumber_pembiayaan" class="form-check-input" type="radio"
                                    value="Eksternal" id="sumber_eksternal" wire:model="sumber_pembiayaan">
                                <label class="form-check-label" for="sumber_eksternal">
                                    Eksternal
                                </label>
                            </div>
                            <div class="form-check">
                                <input name="sumber_pembiayaan" class="form-check-input" type="radio" value="Internal"
                                    id="sumber_internal" wire:model="sumber_pembiayaan">
                                <label class="form-check-label" for="sumber_internal">
                                    Internal
                                </label>
                            </div>
                        </div>
                        <div class="mb-4 rounded-">
                            <select class="form-select" id="exampleFormControlSelect1"
                                aria-label="Default select example">
                                <option selected>Pilih Sumber Pembiayaan</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-1 mb-3 shadow-none">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-3 col-sm-1 mb-6">
                            <label for="exampleFormControlSelect1" class="form-label">Nama Bank</label>
                            <select class="form-select" id="exampleFormControlSelect1"
                                aria-label="Default select example">
                                <option selected>Pilih Bank</option>
                                <option value="1">BCA</option>
                                <option value="2">Mandiri</option>
                                <option value="3">BNI</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="no_rekening" class="form-label">No. Rekening</label>
                            <input type="text" class="form-control" id="no_rekening"
                                placeholder="Masukkan No. Rekening">
                        </div>
                        <div class="col-md-5">
                            <label for="nama_rekening" class="form-label">Nama Rekening</label>
                            <input type="text" class="form-control" id="nama_rekening"
                                placeholder="Masukkan Nama Rekening">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="lampiran_sid" class="form-label">Lampiran SID</label>
                            <input class="form-control" type="file" id="lampiran_sid">
                            <div class="form-text">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar,
                                zip)
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="nilai_kol" class="form-label">Nilai KOL</label>
                            <input type="text" class="form-control" id="nilai_kol" placeholder="Nilai KOL" disabled>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Jenis Pembiayaan</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Invoice Financing" id="invoice_financing"
                                        wire:model="jenis_pembiayaan">
                                    <label class="form-check-label" for="invoice_financing">
                                        Invoice Financing
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="PO Financing" id="po_financing" wire:model="jenis_pembiayaan">
                                    <label class="form-check-label" for="po_financing">
                                        PO Financing
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Installment" id="installment" wire:model="jenis_pembiayaan">
                                    <label class="form-check-label" for="installment">
                                        Installment
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input name="jenis_pembiayaan" class="form-check-input" type="radio"
                                        value="Factoring" id="factoring" wire:model="jenis_pembiayaan">
                                    <label class="form-check-label" for="factoring">
                                        Factoring
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-none border mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Invoice Penjamin</h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>NO. INVOICE</th>
                                <th>NAMA CLIENT</th>
                                <th>NILAI INVOICE</th>
                                <th>NILAI PINJAMAN</th>
                                <th>NILAI BAGI HASIL</th>
                                <th>INVOICE DATE</th>
                                <th>DUE DATE</th>
                                <th>DOKUMEN INVOICE *</th>
                                <th>DOKUMEN KONTRAK</th>
                                <th>DOKUMEN SO</th>
                                <th>DOKUMEN BAST</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($invoices as $index => $invoice)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $invoice['no_invoice'] }}</td>
                                    <td>{{ $invoice['nama_client'] }}</td>
                                    <td>Rp. {{ number_format((int) $invoice['nilai_invoice'], 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format((int) $invoice['nilai_pinjaman'], 0, ',', '.') }}</td>
                                    <td>Rp. {{ number_format((int) $invoice['nilai_bagi_hasil'], 0, ',', '.') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($invoice['invoice_date'])->format('d F Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($invoice['due_date'])->format('d F Y') }}</td>
                                    <td><a href="#">{{ $invoice['dokumen_invoice'] }}</a></td>
                                    <td><a href="#">{{ $invoice['dokumen_kontrak'] }}</a></td>
                                    <td><a href="#">{{ $invoice['dokumen_so'] }}</a></td>
                                    <td><a href="#">{{ $invoice['dokumen_bast'] }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-outline-primary wave-effect">
                        <i class="fa-solid fa-plus me-1"></i>
                        Tambah
                    </button>
                </div>
            </div>

            <div class="row justify-content-end mb-3">
                <div class="col-md-6 text-end">
                    <span class="badge bg-label-secondary">H+30 dari harapan tanggal pencairan</span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="total_pinjaman" class="form-label">Total Pinjaman</label>
                    <input type="text" class="form-control" id="total_pinjaman" value="RP. 9.000.000" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="harapan_tanggal_pencairan" class="form-label">Harapan Tanggal Pencairan</label>
                    <input class="form-control" type="date" value="2021-06-18" id="html5-date-input" />
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="total_bagi_hasil" class="form-label">Total Bagi Hasil</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="total_bagi_hasil" value="2% (Rp. 180.000)"
                            disabled>
                        <span class="input-group-text">/Bulan</span>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="rencana_tanggal_pembayaran" class="form-label">Rencana Tanggal Pembayaran <i
                            class="tf-icons ti ti-info-circle data-bs-toggle="tooltip" title="Info"></i></label>
                    <input class="form-control" type="date" value="2021-06-18" id="html5-date-input" />

                </div>
                <div class="col-md-4 mb-3">
                    <label for="pembayaran_total" class="form-label">Pembayaran Total</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="pembayaran_total" value="Rp. 9.180.000"
                            disabled>
                        <span class="input-group-text">/Bulan</span>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="catatan_lainnya" class="form-label">Catatan Lainnya</label>
                    <textarea class="form-control" id="catatan_lainnya" rows="3" placeholder="Masukkan Catatan"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <span class="align-middle">Simpan Data</span>
                    <i class="tf-icons ti ti-arrow-right ms-1"></i>
                </button>
            </div>

        </div>
    </div>
</div>
