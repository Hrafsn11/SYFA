
<div>
    <div>
        <a wire:navigate.hover href="{{ route('peminjaman.index') }}" class="btn btn-outline-primary mb-4">
            <i class="tf-icons ti ti-arrow-left me-1"></i>
            Kembali
        </a>
        <h4 class="fw-bold">{{ $title }}</h4>
    </div>
    <div class="card">
        <div class="card-body">
            <form wire:submit="{{ isset($id) ? $urlAction['update'] : $urlAction['store'] }}">
                <div class="row">
                    <div class="col-lg mb-3">
                        <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                        <input type="text" class="form-control" id="nama_perusahaan" wire:model="nama_perusahaan" readonly disabled>
                    </div>
                </div>
                {{-- Sumber Pembiayaan dihilangkan karena sudah di-hardcode ke Internal di backend --}}
                <input type="hidden" wire:model="sumber_pembiayaan" value="Internal">
    
                <div class="card border-1 mb-3 shadow-none">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-lg-3 col-sm-12 mb-3 form-group">
                                <label for="selectBank" class="form-label">Nama Bank</label>
                                <input type="text" class="form-control" placeholder="Masukkan Nama Bank" wire:model="nama_bank" readonly disabled>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3 form-group">
                                <label for="no_rekening" class="form-label">No. Rekening</label>
                                <input type="text" class="form-control" id="no_rekening" wire:model="no_rekening" placeholder="Masukkan No. Rekening" readonly disabled>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-5 mb-3 form-group" wire:ignore>
                                <label for="nama_rekening" class="form-label">Nama Rekening</label>
                                <input type="text" class="form-control" id="nama_rekening" wire:model.blur="nama_rekening" placeholder="Masukkan Nama Rekening">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
    
                        {{-- khusus Invoice Financing & PO Financing --}}
                        <div class="row mb-3" id="rowLampiranSID">
                            <div class="col-md-6 form-group">
                                <div class="d-flex justify-content-between">
                                    <label for="lampiran_sid" class="form-label">Lampiran SID</label>
                                    @if (isset($lampiran_sid_current))
                                    <a href="{{ getFileUrl($lampiran_sid_current) }}" target="_blank"><small>Current File</small></a>
                                    @endif
                                </div>
                                <input class="form-control" type="file" id="lampiran_sid" wire:model.blur="lampiran_sid" wire:ignore>
                                <div class="invalid-feedback" wire:ignore></div>
                                <small class="form-text mb-3">Maximum upload file size: 2 MB. (Type File: pdf, docx, xls, png, rar, zip)</small>
                            </div>
                            <div class="col-md-6 form-group" wire:ignore>
                                <label for="nilai_kol" class="form-label">Nilai KOL</label>
                                <input type="text" class="form-control" id="nilai_kol" wire:model="nilai_kol" placeholder="Nilai KOL" readonly disabled>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12 mt-3 form-group" wire:ignore>
                                <label for="tujuan_pembiayaan" class="form-label">Tujuan Pembiayaan</label>
                                <input type="text" class="form-control" id="tujuan_pembiayaan" wire:model.blur="tujuan_pembiayaan" placeholder="Tujuan Pembiayaan"/>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        {{-- end --}}
    
                        <div class="row" wire:block-when-change-state="jenis_pembiayaan">
                            <div class="col-md-12 form-group" wire:ignore>
                                <label class="form-label mb-2">Jenis Pembiayaan</label>
                                <div class="d-flex">
                                    <div class="form-check me-3">
                                        <input wire:model.change="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio" type="radio" value="Invoice Financing" id="invoice_financing">
                                        <label class="form-check-label" for="invoice_financing">Invoice Financing</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input wire:model.change="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio" type="radio" value="PO Financing" id="po_financing">
                                        <label class="form-check-label" for="po_financing">PO Financing</label>
                                    </div>
                                    <div class="form-check me-3">
                                        <input wire:model.change="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio" type="radio" value="Installment" id="installment">
                                        <label class="form-check-label" for="installment">Installment</label>
                                    </div>
                                    <div class="form-check">
                                        <input wire:model.change="jenis_pembiayaan" class="form-check-input jenis-pembiayaan-radio" type="radio" value="Factoring" id="factoring">
                                        <label class="form-check-label" for="factoring">Factoring</label>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Table Invoice/Kontrak -->
                <div wire:block-when-change-state="jenis_pembiayaan|id_instansi">
                    @include('livewire.pengajuan-pinjaman.components.table_create')
                </div>
    
                <div class="card border-1 mb-4 shadow-none">
                    <div class="card-body">
                        @if ($jenis_pembiayaan == 'Installment')
                        <!-- Form khusus untuk Installment -->
                        <div id="formInstallment">
                            <div class="row mb-3">
                                <div class="col-md-6 form-group">
                                    <label for="nominal_pinjaman" class="form-label">Total Pinjaman</label>
                                    <input type="text" class="form-control input-rupiah" id="nominal_pinjaman" wire:model="nominal_pinjaman" placeholder="Rp 0" readonly disabled>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 form-group" wire:ignore>
                                    <label for="tenor_pembayaran" class="form-label">Tenor Pembayaran</label>
                                    <livewire:components.select2 
                                        :list_data="$list_tenor_pembayaran"
                                        value_name="value"
                                        value_label="label"
                                        data_placeholder="Pilih Tenor Pembayaran"
                                        model_name="tenor_pembayaran"
                                        :value="$tenor_pembayaran"
                                        :allow_clear="true"
                                        :tags="false"
                                    />
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
    
                            <div class="row mb-3">
                                <div class="col-md-4 form-group">
                                    <label for="pps_debit" class="form-label">Persentase Bagi Hasil (Debit Cost)</label>
                                    <input type="text" class="form-control" id="pps_debit" wire:model="pps_debit" readonly disabled>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="pps_percentage" class="form-label">PPS</label>
                                    <input type="text" class="form-control" id="pps_percentage" wire:model="pps_percentage" readonly disabled>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="s_finance" class="form-label">S Finance</label>
                                    <input type="text" class="form-control" id="s_finance" wire:model="s_finance" readonly disabled>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="total_pembayaran_installment" class="form-label">Total Pembayaran
                                        <i class="ti ti-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Total yang harus dibayarkan"></i>
                                    </label>
                                    <input type="text" class="form-control bg-light" id="total_pembayaran_installment" wier:model="total_pembayaran_installment" readonly disabled>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="bayar_per_bulan" class="form-label">Yang harus dibayarkan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light" id="bayar_per_bulan" wire:model="bayar_per_bulan" readonly disabled>
                                        <span class="input-group-text">/Bulan</span>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        {{-- end form khusus installment --}}
                        @else
                        <!-- Form untuk selain Invoice Financing && PO Financing -->
                        <div id="formNonInstallment">
                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="total_pinjaman" class="form-label" id="labelTotalPinjaman">{{ $jenis_pembiayaan == 'Factoring' ? 'Total Nominal Yang Dialihkan' : 'Total Pinjaman' }}</label>
                                    <input type="text" class="form-control input-rupiah non-editable" id="total_pinjaman" wire:model="total_pinjaman" placeholder="Rp 0" readonly disabled>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 form-group mb-3" wire:ignore>
                                    <label for="harapan_tanggal_pencairan" class="form-label">Harapan Tanggal Pencairan</label>
                                    <livewire:components.datepicker-bootstrap 
                                        model_name="harapan_tanggal_pencairan"
                                        :value="$harapan_tanggal_pencairan"
                                        data_placeholder="DD/MM/YYYY"
                                        format="dd/mm/yyyy"
                                        :autoclose="true" 
                                        :today_highlight="true"
                                        wire:key="create_harapan_tanggal_pencairan"
                                    />
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label for="total_bagi_hasil" class="form-label">Total Bagi Hasil</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control input-rupiah non-editable" id="total_bagi_hasil" wire:model="total_bagi_hasil" placeholder="2%" readonly disabled>
                                        <span class="input-group-text">/Bulan</span>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 form-group mb-3" wire:ignore>
                                    <label for="rencana_tgl_pembayaran" class="form-label">Rencana Tanggal Pembayaran</label>
                                    <livewire:components.datepicker-bootstrap 
                                        model_name="rencana_tgl_pembayaran"
                                        :value="$rencana_tgl_pembayaran"
                                        data_placeholder="DD/MM/YYYY"
                                        format="dd/mm/yyyy"
                                        :autoclose="true"
                                        :today_highlight="true"
                                        wire:key="create_rencana_tgl_pembayaran"
                                    />
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="pembayaran_total" class="form-label">Pembayaran Total</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control input-rupiah non-editable" id="pembayaran_total" wire:model="pembayaran_total" placeholder="" readonly disabled>
                                        <span class="input-group-text">/Bulan</span>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
    
                <div class="row mb-3">
                    <div class="col-md-12 form-group" wire:ignore>
                        <label for="catatan_lainnya" class="form-label">Catatan Lainnya</label>
                        <textarea class="form-control" id="catatan_lainnya" wire:model.blur="catatan_lainnya" rows="3" placeholder="Masukkan Catatan"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
    
                <div class="d-flex justify-content-end gap-2">
                    <a wire:navigate.hover href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-x me-1"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-{{ isset($id) ? 'device-floppy' : 'check' }} me-1"></i>
                        <span class="align-middle">{{ isset($id) ? 'Update' : 'Simpan' }} Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <livewire:pengajuan-pinjaman.invoice-form 
        :$jenis_pembiayaan 
        :$pengajuan 
        :$sumber_pembiayaan 
        :$id_instansi 
        wire:key="invoice-form-{{ $jenis_pembiayaan }}-{{ $sumber_pembiayaan }}-{{ $id_instansi }}" 
    />
</div>

@push('scripts')
<script>
    function afterAction(payload) {
        // Redirect sudah dihandle di Livewire component (afterSave method)
    }
</script>
@endpush
