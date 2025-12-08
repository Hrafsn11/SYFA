<div>
    <div class="row">
        <div class="col-12">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">Pengajuan Investasi</h4>
                <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                    id="btnTambahPengajuanInvestasi" data-bs-toggle="modal" data-bs-target="#modalPengajuanInvestasi">
                    <i class="fa-solid fa-plus"></i>
                    Pengajuan Investasi
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <a href="{{ route('sfinlog.pengajuan-investasi.detail', ['id' => 1]) }}" class="text-center fw-bold">detail cek disini</a>
        </div>
    </div>
    
    <div class="modal modal-lg fade" id="modalPengajuanInvestasi">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahPengajuanInvestasiLabel">Tambah Pengajuan Investasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
                        <i class="fas fa-info-circle me-2"></i>
                        Deposito yang masuk setelah tanggal 20, untuk bagi hasil akan dihitung di bulan selanjutnya
                    </div> --}}
                    <form id="formTambahPengajuanInvestasi" novalidate>
                        <input type="hidden" id="editPengajuanInvestasiId" value="">
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label for="nama_investor" class="form-label">Nama Investor <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_investor" name="nama_investor"
                                    placeholder="Nama Investor" required readonly>
                            </div>
                            <div class="col-12 mb-3" id="div-deposito">
                                <label for="project_id" class="form-label">
                                    Pilih Project <span class="text-danger">*</span>
                                </label>
                                <div wire:ignore>
                                    <select id="project_id" class="form-select select2"
                                        data-placeholder="Pilih Project">
                                        <option value=""></option>
                                        <option value="Velocity">Velocity</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_investasi" class="form-label">Tanggal
                                    Investasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control bs-datepicker" placeholder="yyyy-mm-dd"
                                        id="tanggal_investasi" name="tanggal_investasi" required />
                                    <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lama-investasi" class="form-label">Lama Berinvestasi (Bulan) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="lama_investasi"
                                    placeholder="Masukkan lama berinvestasi" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jumlah_investasi" class="form-label">Jumlah Investasi <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control input-rupiah" id="jumlah_investasi"
                                    name="jumlah_investasi" placeholder="Rp 0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bagi_hasil" class="form-label">Bagi Hasil (%)/Tahun <span
                                        class="text-danger">*</span></label>
                                <div wire:ignore>
                                    <select id="bagi_hasil" class="form-select select2"
                                        data-placeholder="Pilih Project">
                                        <option value=""></option>
                                        <option value="12%">12%</option>
                                        <option value="13%">13%</option>
                                        <option value="14%">14%</option>
                                        <option value="15%">15%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="bagi_hasil_keseluruhan" class="form-label">Nominal Bagi Hasil Yang Didapat
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control input-rupiah non-editable"
                                    id="bagi_hasil_keseluruhan" placeholder="Rp 0" required disabled readonly>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary d-none" id="btnHapusFormKerjaInvestor">
                        <i class="ti ti-trash me-1"></i>
                        Hapus Data
                    </button>
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanFormKerjaInvestor">
                        <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.bs-datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto'
        });

        $('#modalPengajuanInvestasi').on('hide.bs.modal', function() {
            $('#formTambahPengajuanInvestasi')[0].reset();
            $('#project_id').val(null).trigger('change');
            $('#bagi_hasil').val(null).trigger('change');
            $('.bs-datepicker').datepicker('clearDates');
        });
    });
</script>
