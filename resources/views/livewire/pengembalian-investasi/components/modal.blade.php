{{-- Modal Tambah/Edit Penyaluran Deposito --}}
<div class="modal fade" id="modalPengembalianInvestasi">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPengembalianInvestasiLabel">Tambah Pengembalian Investasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPengembalianInvestasi">
                <div class="modal-body">
                    <div class="row">
                        <!-- No Kontrak -->
                        <div class="col-12 mb-3 form-group">
                            <label for="id_pengajuan_investasi" class="form-label">
                                Pilih No Kontrak <span class="text-danger">*</span>
                            </label>
                            <select id="id_pengajuan_investasi" class="form-select select2"
                                wire:model="id_pengajuan_investasi" data-placeholder="Pilih No Kontrak">
                                <option value=""></option>
                                <option value="">Adam Ganteng</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Nominal yang Investasi -->
                        <div class="col-12 mb-3 form-group">
                            <label for="nominal_investasi" class="form-label">
                                Nominal Investasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nominal_investasi"
                                placeholder="Nanti keisi (contoh: 10000000)" autocomplete="off" readonly>
                            <input type="hidden" id="nominal_raw" wire:model="nominal_investasi">
                            <small class="text-muted" id="nilai-investasi-info"></small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mb-3 form-group">
                            <label for="lama_investasi" class="form-label">
                                Lama Investasi <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="lama_investasi"
                                    placeholder="Pilih tanggal" wire:model="lama_investasi" readonly>
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mb-3 form-group">
                            <label for="nominal_investasi" class="form-label">
                                Bagi Hasil <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nominal_investasi"
                                placeholder="Nanti keisi (contoh: 10000000)" autocomplete="off" readonly>
                            <input type="hidden" id="nominal_raw" wire:model="nominal_investasi">
                            <small class="text-muted" id="nilai-investasi-info"></small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="nominal_investasi" class="form-label">
                                Dana Pokok Yang dibayarkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nominal_investasi"
                                placeholder="Nanti keisi (contoh: 10000000)" autocomplete="off">
                            <input type="hidden" id="nominal_raw" wire:model="nominal_investasi">
                            <small class="text-muted" id="nilai-investasi-info"></small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="nominal_investasi" class="form-label">
                                Sisa Pokok Yang belum dibayarkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nominal_investasi"
                                placeholder="Nanti keisi (contoh: 10000000)" autocomplete="off" readonly>
                            <input type="hidden" id="nominal_raw" wire:model="nominal_investasi">
                            <small class="text-muted" id="nilai-investasi-info"></small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="nominal_investasi" class="form-label">
                                Bagi Hasil Yang dibayarkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nominal_investasi"
                                placeholder="Nanti keisi (contoh: 10000000)" autocomplete="off">
                            <input type="hidden" id="nominal_raw" wire:model="nominal_investasi">
                            <small class="text-muted" id="nilai-investasi-info"></small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3 form-group">
                            <label for="nominal_investasi" class="form-label">
                                Bagi Hasil Yang belum dibayarkan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nominal_investasi"
                                placeholder="Nanti keisi (contoh: 10000000)" autocomplete="off" readonly>
                            <input type="hidden" id="nominal_raw" wire:model="nominal_investasi">
                            <small class="text-muted" id="nilai-investasi-info"></small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-12 mb-3 form-group">
                            <label for="bukti_tf" class="form-label">Bukti Transfer</label>
                            <input type="file" class="form-control" id="bukti_tf" wire:model="bukti_tf">
                            <div class="invalid-feedback"></div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnHapusData" style="display: none;">
                        <i class="ti ti-trash me-1"></i> Hapus Data
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm me-2" wire:loading
                            wire:target="saveData"></span>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
