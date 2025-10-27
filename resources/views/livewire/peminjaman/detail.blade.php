@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold mb-4">
                Detail Pengajuan Peminjaman
            </h4>


            <!-- AStepper -->
            <div class="stepper-container mb-4">
                <div class="stepper-wrapper">

                    <div class="stepper-item completed" data-step="1">
                        <div class="stepper-node">
                        </div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 1</div>
                            <div class="step-name">Pengajuan Pinjaman</div>
                        </div>
                    </div>

                    <div class="stepper-item active" data-step="2">
                        <div class="stepper-node">
                        </div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 2</div>
                            <div class="step-name">Validasi Dokumen</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="3">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 3</div>
                            <div class="step-name">Dokumen Tervalidasi</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="4">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 4</div>
                            <div class="step-name">Persetujuan Debitur</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="5">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 5</div>
                            <div class="step-name">validasi CEO SKI</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="6">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 6</div>
                            <div class="step-name">Validasi Direktur</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="7">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 7</div>
                            <div class="step-name">Generate Kontrak</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="8">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 8</div>
                            <div class="step-name">Upload Dokumen Transfer</div>
                        </div>
                    </div>

                    <div class="stepper-item" data-step="9">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 9</div>
                            <div class="step-name">Selesai</div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
                <i class="fas fa-info-circle me-2"></i>
                Pengajuan Pinjaman Anda sedang kami tinjau. Harap tunggu
                beberapa saat hingga proses verifikasi selesai.
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-0">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#detail-pinjaman" role="tab" aria-selected="true">
                                            <i class="ti ti-wallet me-2"></i>
                                            <span class="d-none d-sm-inline">Detail Pinjaman</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#detail-kontrak" role="tab" aria-selected="false">
                                            <i class="ti ti-report-money me-2"></i>
                                            <span class="d-none d-sm-inline">Detail Kontrak</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button" class="nav-link" data-bs-toggle="tab"
                                            data-bs-target="#activity" role="tab" aria-selected="false">
                                            <i class="ti ti-activity me-2"></i>
                                            <span class="d-none d-sm-inline">Activity</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Detail Pinjaman Tab -->
                                <div class="tab-pane fade show active" id="detail-pinjaman" role="tabpanel">
                                    <!-- Konten Default (Step 1-6, 8-9) -->
                                    <div id="content-default">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
                                            <h5 class="mb-3 mb-md-4">Detail Pinjaman</h5>
                                            <button type="button" class="btn btn-primary d-none"
                                                id="btnSetujuiPeminjaman">
                                                <i class="fas fa-check me-2"></i>
                                                Setujui Peminjaman
                                            </button>
                                        </div>

                                        <hr class="my-3 my-md-4">

                                        <!-- Data Perusahaan -->
                                        <h6 class="text-dark mb-3">Data Perusahaan</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nama
                                                        Perusahaan</small>
                                                    <p class="fw-bold mb-0">{{ $peminjaman['nama_perusahaan'] ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nama Bank</small>
                                                    <p class="fw-bold mb-0">{{ $peminjaman['nama_bank'] ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">No Rekening</small>
                                                    <p class="fw-bold mb-0">{{ $peminjaman['no_rekening'] ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Lampiran SID</small>
                                                    <p class="fw-bold mb-0">
                                                        @if (!empty($peminjaman['lampiran_sid']))
                                                            <a href="{{ asset('storage/' . $peminjaman['lampiran_sid']) }}"
                                                                target="_blank">Lihat Lampiran</a>
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nilai KOL</small>
                                                    <p class="fw-bold mb-0">{{ $peminjaman['nilai_kol'] ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-3 my-md-4">

                                        <!-- Data Peminjaman -->
                                        <h6 class="text-dark mb-3">Data Peminjaman</h6>
                                        <div class="row g-3 mb-4">
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                <div class="mb-0">
                                                    <small class="text-light fw-semibold d-block mb-1">Nominal
                                                        Pinjaman</small>
                                                    <p class="mb-0 text-success fw-semibold">Rp.
                                                        {{ number_format($peminjaman['nominal_pinjaman'] ?? ($peminjaman['total_pinjaman'] ?? 0), 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                            @if (($peminjaman['jenis_pembiayaan'] ?? '') === 'Installment')
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Tenor
                                                            Pembayaran</small>
                                                        <p class="fw-bold mb-0">
                                                            {{ $peminjaman['tenor_pembayaran'] ? $peminjaman['tenor_pembayaran'] . ' Bulan' : '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Persentase Bagi
                                                            Hasil</small>
                                                        @php
                                                            $p = $peminjaman['persentase_bagi_hasil'] ?? null;
                                                            $p_display = '-';
                                                            if ($p !== null && $p !== '') {
                                                                $p_display =
                                                                    rtrim(
                                                                        rtrim(sprintf('%.4f', (float) $p), '0'),
                                                                        '.',
                                                                    ) . '%';
                                                            }
                                                        @endphp
                                                        <p class="fw-bold mb-0">{{ $p_display }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">PPS</small>
                                                        <p class="fw-bold mb-0">Rp.
                                                            {{ number_format($peminjaman['pps'] ?? 0, 0, ',', '.') }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">S
                                                            Finance</small>
                                                        <p class="fw-bold mb-0">Rp.
                                                            {{ number_format($peminjaman['sfinance'] ?? 0, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Pembayaran
                                                            Total</small>
                                                        <p class="mb-0 text-warning fw-semibold">Rp.
                                                            {{ number_format($peminjaman['pembayaran_total'] ?? 0, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Yang harus
                                                            dibayarkan / bulan</small>
                                                        <p class="mb-0 text-warning fw-semibold">Rp.
                                                            {{ number_format($peminjaman['yang_harus_dibayarkan'] ?? 0, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Harapan Tanggal
                                                            Pencairan</small>
                                                        <p class="fw-bold mb-0">
                                                            {{ !empty($peminjaman['harapan_tanggal_pencairan'])
                                                                ? \Carbon\Carbon::parse($peminjaman['harapan_tanggal_pencairan'])->translatedFormat('j F Y')
                                                                : '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Persentase Bagi
                                                            Hasil</small>
                                                        <p class="fw-bold mb-0">2%</p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Jenis
                                                            Pembiayaan</small>
                                                        <p class="fw-bold mb-0">
                                                            {{ $peminjaman['jenis_pembiayaan'] ?? 'Invoice Financing' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Rencana Tanggal
                                                            Bayar</small>
                                                        <p class="fw-bold mb-0">
                                                            {{ !empty($peminjaman['rencana_tgl_pembayaran']) ? \Carbon\Carbon::parse($peminjaman['rencana_tgl_pembayaran'])->translatedFormat('j F Y') : '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                                    <div class="mb-0">
                                                        <small class="text-light fw-semibold d-block mb-1">Pembayaran
                                                            Total</small>
                                                        <p class="mb-0 text-warning fw-semibold">Rp.
                                                            {{ number_format($peminjaman['pembayaran_total'] ?? ($peminjaman['total_pembayaran'] ?? 0), 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <hr class="my-3 my-md-4">

                                        <!-- Data Invoicing -->
                                        @if (!empty($installment_data) && ($peminjaman['jenis_pembiayaan'] ?? '') === 'Installment')
                                            <h6 class="text-muted mb-3">Data Installment</h6>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>NO. INVOICE</th>
                                                            <th>NAMA CLIENT</th>
                                                            <th>NILAI KONTRAK</th>
                                                            <th>INVOICE DATE</th>
                                                            <th>NAMA BARANG</th>
                                                            <th>DOKUMEN INVOICE</th>
                                                            <th>DOKUMEN LAINNYA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                        @forelse($installment_data as $idx => $inst)
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>{{ $inst['no_invoice'] ?? '-' }}</td>
                                                                <td>{{ $inst['nama_client'] ?? '-' }}</td>
                                                                <td>Rp.
                                                                    {{ number_format($inst['nilai_invoice'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td>{{ $inst['invoice_date'] ?? '-' }}</td>
                                                                <td>{{ $inst['nama_barang'] ?? '-' }}</td>
                                                                <td>
                                                                    @if (!empty($inst['dokumen_invoice']))
                                                                        <a href="{{ asset('storage/' . $inst['dokumen_invoice']) }}"
                                                                            target="_blank">{{ basename($inst['dokumen_invoice']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!empty($inst['dokumen_lainnya']))
                                                                        <a href="{{ asset('storage/' . $inst['dokumen_lainnya']) }}"
                                                                            target="_blank">{{ basename($inst['dokumen_lainnya']) }}</a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8" class="text-center">Tidak ada data
                                                                    Installment</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif(!empty($po_financing_data))
                                            <h6 class="text-muted mb-3">Data PO Financing</h6>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>NO. KONTRAK</th>
                                                            <th>NILAI INVOICE</th>
                                                            <th>NAMA CLIENT</th>
                                                            <th>NILAI PINJAMAN</th>
                                                            <th>NILAI BAGI HASIL</th>
                                                            <th>DOKUMEN KONTRAK</th>
                                                            <th>DOKUMEN SO</th>
                                                            <th>DOKUMEN BAST</th>
                                                            <th>DOKUMEN LAINNYA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                        @forelse($po_financing_data as $idx => $po)
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>{{ $po['no_kontrak'] ?? '-' }}</td>
                                                                <td>Rp.
                                                                    {{ number_format($po['nilai_invoice'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td>{{ $po['nama_client'] ?? '-' }}</td>
                                                                <td>Rp.
                                                                    {{ number_format($po['nilai_pinjaman'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td>Rp.
                                                                    {{ number_format($po['nilai_bagi_hasil'] ?? 0, 0, ',', '.') }}
                                                                </td>

                                                                <td>
                                                                    @if (!empty($po['dokumen_kontrak']))
                                                                        <a href="{{ asset('storage/' . $po['dokumen_kontrak']) }}"
                                                                            target="_blank">{{ basename($po['dokumen_kontrak']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!empty($po['dokumen_so']))
                                                                        <a href="{{ asset('storage/' . $po['dokumen_so']) }}"
                                                                            target="_blank">{{ basename($po['dokumen_so']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!empty($po['dokumen_bast']))
                                                                        <a href="{{ asset('storage/' . $po['dokumen_bast']) }}"
                                                                            target="_blank">{{ basename($po['dokumen_bast']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!empty($po['dokumen_lainnya']))
                                                                        <a href="{{ asset('storage/' . $po['dokumen_lainnya']) }}"
                                                                            target="_blank">{{ basename($po['dokumen_lainnya']) }}</a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="9" class="text-center">Tidak ada data PO
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif(!empty($factoring_data) || (($peminjaman['jenis_pembiayaan'] ?? '') === 'Factoring'))
                                            <h6 class="text-muted mb-3">Data Factoring</h6>
                                            <div class="table-responsive text-nowrap">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>NO</th>
                                                            <th>NO. KONTRAK</th>
                                                            <th>NAMA CLIENT</th>
                                                            <th>NILAI INVOICE</th>
                                                            <th>NILAI PINJAMAN</th>
                                                            <th>NILAI BAGI HASIL</th>
                                                            <th>KONTRAK DATE</th>
                                                            <th>DUE DATE</th>
                                                            <th>DOKUMEN INVOICE</th>
                                                            <th>DOKUMEN KONTRAK</th>
                                                            <th>DOKUMEN SO</th>
                                                            <th>DOKUMEN BAST</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                        @forelse($factoring_data as $idx => $fact)
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>{{ $fact['no_kontrak'] ?? '-' }}</td>
                                                                <td>{{ $fact['nama_client'] ?? '-' }}</td>
                                                                <td>Rp. {{ number_format($fact['nilai_invoice'] ?? 0, 0, ',', '.') }}</td>
                                                                <td>Rp. {{ number_format($fact['nilai_pinjaman'] ?? 0, 0, ',', '.') }}</td>
                                                                <td>Rp. {{ number_format($fact['nilai_bagi_hasil'] ?? 0, 0, ',', '.') }}</td>
                                                                <td>{{ $fact['kontrak_date'] ?? '-' }}</td>
                                                                <td>{{ $fact['due_date'] ?? '-' }}</td>
                                                                <td>
                                                                    @if(!empty($fact['dokumen_invoice']))
                                                                        <a href="{{ asset('storage/' . $fact['dokumen_invoice']) }}" target="_blank">{{ basename($fact['dokumen_invoice']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(!empty($fact['dokumen_kontrak']))
                                                                        <a href="{{ asset('storage/' . $fact['dokumen_kontrak']) }}" target="_blank">{{ basename($fact['dokumen_kontrak']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(!empty($fact['dokumen_so']))
                                                                        <a href="{{ asset('storage/' . $fact['dokumen_so']) }}" target="_blank">{{ basename($fact['dokumen_so']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if(!empty($fact['dokumen_bast']))
                                                                        <a href="{{ asset('storage/' . $fact['dokumen_bast']) }}" target="_blank">{{ basename($fact['dokumen_bast']) }}</a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="12" class="text-center">Tidak ada data Factoring</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <h6 class="text-muted mb-3">Data Invoicing</h6>

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
                                                            <th>DOKUMEN INVOICE <span class="text-danger">*</span></th>
                                                            <th>DOKUMEN KONTRAK</th>
                                                            <th>DOKUMEN SO</th>
                                                            <th>DOKUMEN BAST</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="table-border-bottom-0">
                                                        @forelse($invoice_financing_data as $idx => $inv)
                                                            <tr>
                                                                <td>{{ $idx + 1 }}</td>
                                                                <td>{{ $inv['no_invoice'] }}</td>
                                                                <td>{{ $inv['nama_client'] }}</td>
                                                                <td>Rp.
                                                                    {{ number_format($inv['nilai_invoice'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td>Rp.
                                                                    {{ number_format($inv['nilai_pinjaman'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td>Rp.
                                                                    {{ number_format($inv['nilai_bagi_hasil'] ?? 0, 0, ',', '.') }}
                                                                </td>
                                                                <td>{{ $inv['invoice_date'] }}</td>
                                                                <td>{{ $inv['due_date'] }}</td>
                                                                <td>
                                                                    @if (!empty($inv['dokumen_invoice']))
                                                                        <a href="{{ asset('storage/' . $inv['dokumen_invoice']) }}"
                                                                            target="_blank">{{ basename($inv['dokumen_invoice']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!empty($inv['dokumen_kontrak']))
                                                                        <a href="{{ asset('storage/' . $inv['dokumen_kontrak']) }}"
                                                                            target="_blank">{{ basename($inv['dokumen_kontrak']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!empty($inv['dokumen_so']))
                                                                        <a href="{{ asset('storage/' . $inv['dokumen_so']) }}"
                                                                            target="_blank">{{ basename($inv['dokumen_so']) }}</a>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!empty($inv['dokumen_bast']))
                                                                        <a href="{{ asset('storage/' . $inv['dokumen_bast']) }}"
                                                                            target="_blank">{{ basename($inv['dokumen_bast']) }}</a>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="12" class="text-center">Tidak ada invoice
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- End Konten Default -->
                                </div>

                                <!-- Detail Kontrak Tab -->
                                <!-- Detail Kontrak Tab -->
                                <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                                    <!-- Konten Default (Before Step 7) -->
                                    <div id="kontrak-default">
                                        <div class="text-center py-5">
                                            <i class="far fa-file-alt fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Detail Kontrak</h5>
                                            <p class="text-muted">Konten detail kontrak akan ditampilkan di sini.</p>
                                        </div>
                                    </div>

                                    <!-- Konten Step 7: Generate Kontrak -->
                                    <div id="kontrak-step7" class="d-none">
                                        <h5 class="mb-4">Generate Kontrak Peminjaman</h5>
                                        <form action="" id="formGenerateKontrak">
                                            <div class="col-lg mb-3">
                                                <label for="jenis_pembiayaan" class="form-label">Jenis
                                                    Pembiayaan</label>
                                                <input type="text" class="form-control" id="jenis_pembiayaan"
                                                    name="jenis_pembiayaan" value="Invoice & Project Financing" required
                                                    disabled>
                                            </div>

                                            <div class="card border-1 shadow-none mb-3">
                                                <div class="card-body">
                                                    <div class="col-lg mb-3">
                                                        <label for="nama_perusahaan" class="form-label">Nama
                                                            Perusahaan</label>
                                                        <input type="text" class="form-control" id="nama_perusahaan"
                                                            name="nama_perusahaan" value="Techno Infinity" required
                                                            disabled>
                                                    </div>

                                                    <div class="col-lg mb-3">
                                                        <label for="nama_pimpinan" class="form-label">
                                                            Nama Pimpinan
                                                        </label>
                                                        <input type="text" class="form-control" id="nama_pimpinan"
                                                            name="nama_pimpinan" value="Cahyo" required disabled>
                                                    </div>

                                                    <div class="col-lg mb-3">
                                                        <label for="alamat" class="form-label">
                                                            Alamat Perusahaan
                                                        </label>
                                                        <input type="text" class="form-control" id="alamat"
                                                            name="alamat"
                                                            value="Gd. Permata Kuningan Lantai 17 Unit 07 Jl. Kuningan Mulia"
                                                            required disabled>
                                                    </div>

                                                    <div class="col-lg mb-3">
                                                        <label for="tujuan" class="form-label">
                                                            Tujuan Pembiayaan
                                                        </label>
                                                        <input type="text" class="form-control" id="tujuan"
                                                            name="tujuan" value="Kebutuhan Gaji Operasional/Umum Sept"
                                                            required disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-2">
                                                    <label for="nilai_pembiayaan">Nilai Pembiayaan</label>
                                                    <input type="text" class="form-control" id="nilai_pembiayaan"
                                                        name="nilai_pembiayaan" value="Rp.250.000.000" disabled>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="hutang_pokok">Hutang Pokok</label>
                                                    <input type="text" class="form-control" id="hutang_pokok"
                                                        name="hutang_pokok" value="Rp.250.000.000" disabled>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6 mb-2">
                                                    <label for="tenor">Tenor Pembiayaan</label>
                                                    <input type="text" class="form-control" id="tenor"
                                                        name="tenor" value="1 Bulan" disabled>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="biaya_admin">Biaya Administrasi</label>
                                                    <input type="text" class="form-control" id="biaya_admin"
                                                        name="biaya_admin" value="Rp.0.00" disabled>
                                                </div>
                                            </div>

                                            <div class="col-lg mb-3">
                                                <label for="nisbah" class="form-label">Bagi Hasil (Nisbah)</label>
                                                <input type="text" class="form-control" id="nisbah" name="nisbah"
                                                    value="2% flat / bulan" required disabled>
                                            </div>

                                            <div class="col-lg mb-3">
                                                <label for="denda_keterlambatan" class="form-label">
                                                    Denda Keterlambatan
                                                </label>
                                                <input type="text" class="form-control" id="denda_keterlambatan"
                                                    name="denda_keterlambatan"
                                                    value="2% dari jumlah yang belum dibayarkan untuk periode pembayaran tersebut"
                                                    required disabled>
                                            </div>

                                            <div class="col-lg mb-3">
                                                <label for="jaminan" class="form-label">
                                                    Jaminan
                                                </label>
                                                <input type="text" class="form-control" id="jaminan" name="jaminan"
                                                    value="Invoice & Project Financing" required disabled>
                                            </div>

                                            <div class="col-lg mb-3">
                                                <label for="ttd_debitur" class="form-label">
                                                    Tanda Tangan Debitur
                                                </label>
                                                <input type="file" class="form-control" id="ttd_debitur" required>
                                                <div class="invalid-feedback">
                                                    Silakan pilih file untuk diupload.
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary" id="btnSimpanKontrak">
                                                    <span class="spinner-border spinner-border-sm me-2 d-none"
                                                        id="btnSimpanKontrakSpinner"></span>
                                                    Simpan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- End Konten Step 7 -->
                                </div>

                                <!-- Activity Tab -->
                                @include('livewire.peminjaman.partials._activity-tabs')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Modal -->
    @include('livewire.peminjaman.partials._modal-detail-peminjaman')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- STATE MANAGEMENT ---
            const state = {
                currentStep: 1,
                totalSteps: 9,
                pencairanData: {
                    nominalDisetujui: '',
                    tanggalPencairan: '',
                    catatan: ''
                },
            };

            // --- DOM ELEMENT CACHE ---
            const dom = {
                stepper: document.querySelector('.stepper-wrapper'),
                alertPeninjauan: document.getElementById('alertPeninjauan'),
                activityTab: document.querySelector('[data-bs-target="#activity"]'),
                detailKontrakTab: document.querySelector('[data-bs-target="#detail-kontrak"]'),
                timeline: {
                    container: document.getElementById('timeline-container'),
                    empty: document.getElementById('activity-empty'),
                    items: document.querySelectorAll('.activity-item'),
                },
                buttons: {
                    setujuiPeminjaman: document.getElementById('btnSetujuiPeminjaman'),
                    konfirmasiSetuju: document.getElementById('btnKonfirmasiSetuju'),
                    tolakPinjaman: document.getElementById('btnTolakPinjaman'),
                    editPencairan: document.querySelectorAll(
                        '#btnEditPencairan'), // Menggunakan querySelectorAll
                    uploadDokumen: document.getElementById('btnUploadDokumen'),
                },
                forms: {
                    pencairan: document.getElementById('formPencairanDana'),
                    review: document.getElementById('formHasilReview'),
                    edit: document.getElementById('formEditPencairan'),
                    upload: document.getElementById('formUploadDokumen'),
                },
                modals: {
                    persetujuan: new bootstrap.Modal(document.getElementById('modalPersetujuanPinjaman')),
                    pencairan: new bootstrap.Modal(document.getElementById('modalPencairanDana')),
                    review: new bootstrap.Modal(document.getElementById('modalHasilReview')),
                    edit: new bootstrap.Modal(document.getElementById('modalEditPencairan')),
                    upload: new bootstrap.Modal(document.getElementById('modalUploadDokumen')),
                },
                inputs: {
                    nominalPengajuan: document.getElementById('nominalPengajuan'),
                    nominalDisetujui: document.getElementById('nominalDisetujui'),
                    tanggalPencairan: document.getElementById('flatpickr-tanggal-pencairan'),
                    tanggalHarapan: document.getElementById('flatpickr-tanggal-harapan'),
                    catatanLainnya: document.getElementById('catatanLainnya'),
                    editNominalPengajuan: document.getElementById('editNominalPengajuan'),
                    editNominalDisetujui: document.getElementById('editNominalDisetujui'),
                    editTanggalPencairan: document.getElementById('editTanggalPencairan'),
                    editTanggalHarapan: document.getElementById('editTanggalHarapan'),
                    editCatatanLainnya: document.getElementById('editCatatanLainnya'),
                }
            };

            // --- HELPERS ---
            const getFormattedDate = () => new Date().toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            const toggleDisplay = (element, show) => element?.classList.toggle('d-none', !show);
            const resetForm = (form) => {
                form?.reset();
                form?.classList.remove('was-validated');
            };
            const switchModal = (hideModal, showModal, onShow) => {
                const hideModalEl = document.getElementById(hideModal._element.id);
                const showModalEl = document.getElementById(showModal._element.id);

                const handleHidden = () => {
                    onShow?.();
                    showModal.show();
                    showModalEl.addEventListener('shown.bs.modal', function() {
                        initCleaveRupiah(); // Re-initialize cleave on new modal
                    }, {
                        once: true
                    });

                    hideModalEl.removeEventListener('hidden.bs.modal', handleHidden);
                };

                hideModalEl.addEventListener('hidden.bs.modal', handleHidden);
                hideModal.hide();
            };

            // --- INITIALIZATION ---
            const initFlatpickr = () => {
                if (dom.inputs.tanggalPencairan?._flatpickr) dom.inputs.tanggalPencairan._flatpickr.destroy();
                flatpickr(dom.inputs.tanggalPencairan, {
                    monthSelectorType: 'static',
                    dateFormat: 'd/m/Y',
                    altInput: true,
                    altFormat: 'j F Y',
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            };

            // --- UI UPDATES ---
            const updateStepper = () => {
                document.querySelectorAll('.stepper-item').forEach((item, index) => {
                    const step = index + 1;
                    item.classList.remove('completed', 'active');
                    
                    if (step < state.currentStep) {
                        item.classList.add('completed');
                    } else if (step === state.currentStep) {
                        item.classList.add('active');
                    }
                });
                toggleDisplay(dom.buttons.setujuiPeminjaman, state.currentStep === 2);
                dom.alertPeninjauan.style.display = state.currentStep >= 2 ? 'none' : 'block';
                updateDetailKontrakContent(); // Update konten tab Detail Kontrak
                updateActivityTimeline();
            };

            const updateDetailKontrakContent = () => {
                const kontrakDefault = document.getElementById('kontrak-default');
                const kontrakStep7 = document.getElementById('kontrak-step7');

                if (state.currentStep === 7) {
                    // Step 7: Tampilkan form Generate Kontrak
                    toggleDisplay(kontrakDefault, false);
                    toggleDisplay(kontrakStep7, true);
                } else {
                    // Step lainnya: Tampilkan konten default
                    toggleDisplay(kontrakDefault, true);
                    toggleDisplay(kontrakStep7, false);
                }
            };

            const updateActivityTimeline = () => {
                const showTimeline = state.currentStep >= 2;
                toggleDisplay(dom.timeline.empty, !showTimeline);
                toggleDisplay(dom.timeline.container, showTimeline);

                if (!showTimeline) return;

                const currentDate = getFormattedDate();
                dom.timeline.items.forEach((item, index) => {
                    const step = index + 2; // Timeline items start from step 2
                    const shouldShow = step <= state.currentStep;
                    toggleDisplay(item, shouldShow);
                    if (shouldShow) {
                        const dateEl = item.querySelector(`#date-step-${step}`);
                        if (dateEl) dateEl.textContent = currentDate;
                    }
                });
            };

            const goToStep = (step) => {
                if (step >= 1 && step <= state.totalSteps) {
                    state.currentStep = step;
                    updateStepper();
                }
            };

            const switchToActivityTab = () => {
                new bootstrap.Tab(dom.activityTab).show();
            };

            const switchToDetailKontrakTab = () => {
                new bootstrap.Tab(dom.detailKontrakTab).show();
            };


            // --- EVENT HANDLERS ---
            const handleStepperClick = (e) => {
                const target = e.target.closest('.stepper-item');
                if (target) {
                    const step = parseInt(target.dataset.step);
                    goToStep(step);

                    // Auto switch ke tab Detail Kontrak jika step 7
                    if (step === 7) {
                        switchToDetailKontrakTab();
                    }
                }
            };

            const handlePencairanSubmit = (e) => {
                e.preventDefault();
                if (!dom.forms.pencairan.checkValidity()) {
                    e.stopPropagation();
                    dom.forms.pencairan.classList.add('was-validated');
                    return;
                }
                Object.assign(state.pencairanData, {
                    nominalDisetujui: dom.inputs.nominalDisetujui.value,
                    tanggalPencairan: dom.inputs.tanggalPencairan.value,
                    catatan: dom.inputs.catatanLainnya.value.trim(),
                });
                dom.modals.pencairan.hide();
                resetForm(dom.forms.pencairan);
                goToStep(4);
                switchToActivityTab();
            };

            const handleReviewSubmit = (e) => {
                e.preventDefault();
                if (!dom.forms.review.checkValidity()) {
                    e.stopPropagation();
                    dom.forms.review.classList.add('was-validated');
                    return;
                }
                dom.modals.review.hide();
                resetForm(dom.forms.review);
                goToStep(1);
            };

            const handleEditPencairanShow = () => {
                dom.inputs.editNominalPengajuan.value = '300000000';
                dom.inputs.editTanggalHarapan.value = '24/08/2024';
                dom.inputs.editNominalDisetujui.value = state.pencairanData.nominalDisetujui;
                dom.inputs.editTanggalPencairan.value = state.pencairanData.tanggalPencairan;
                dom.inputs.editCatatanLainnya.value = state.pencairanData.catatan;
                dom.modals.edit.show();
            };

            const handleEditPencairanSubmit = (e) => {
                e.preventDefault();
                state.pencairanData.catatan = dom.inputs.editCatatanLainnya.value.trim();
                dom.modals.edit.hide();
            };

            const handleUploadSubmit = (e) => {
                e.preventDefault();
                if (!dom.forms.upload.checkValidity()) {
                    e.stopPropagation();
                    dom.forms.upload.classList.add('was-validated');
                    return;
                }
                // Implement upload logic here
                console.log('File to upload:', document.getElementById('fileUpload').files[0]);

                dom.modals.upload.hide();
                resetForm(dom.forms.upload);
                goToStep(9);
                switchToActivityTab();
            };

            const handleGenerateKontrakSubmit = (e) => {
                e.preventDefault();

                const form = e.target;
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }

                const btnSimpan = document.getElementById('btnSimpanKontrak');
                const spinner = document.getElementById('btnSimpanKontrakSpinner');
                const originalText = btnSimpan.innerHTML;

                // Show loading
                btnSimpan.disabled = true;
                spinner.classList.remove('d-none');

                // Simulasi proses generate kontrak (ganti dengan AJAX call sebenarnya)
                setTimeout(() => {
                    btnSimpan.disabled = false;
                    spinner.classList.add('d-none');

                    // Reset form
                    form.classList.remove('was-validated');

                    // Success - pindah ke step 8
                    goToStep(8);
                    switchToActivityTab();
                }, 2000);
            };

            const handleBatalKontrak = () => {
                const form = document.getElementById('formGenerateKontrak');
                form.classList.remove('was-validated');
                // Kembali ke step sebelumnya atau tetap di step 7
            };

            const handlePreviewKontrak = () => {
                // Get peminjaman ID from current page
                const peminjamanId = {{ $peminjaman['id'] ?? 1 }};

                // Open preview in new tab
                window.open(`/peminjaman/${peminjamanId}/preview-kontrak`, '_blank');
            };


            dom.stepper.addEventListener('click', handleStepperClick);
            dom.buttons.setujuiPeminjaman?.addEventListener('click', () => dom.modals.persetujuan.show());
            dom.buttons.konfirmasiSetuju?.addEventListener('click', () => {
                switchModal(dom.modals.persetujuan, dom.modals.pencairan, () => {
                    resetForm(dom.forms.pencairan);
                    dom.inputs.nominalPengajuan.value = '300000000';
                    dom.inputs.tanggalHarapan.value = '24/08/2024';
                    initFlatpickr();
                });
            });
            dom.buttons.tolakPinjaman?.addEventListener('click', () => {
                switchModal(dom.modals.persetujuan, dom.modals.review, () => resetForm(dom.forms.review));
            });
            dom.buttons.uploadDokumen?.addEventListener('click', () => dom.modals.upload.show());

            dom.buttons.editPencairan.forEach(btn => {
                btn.addEventListener('click', handleEditPencairanShow);
            });

            dom.forms.pencairan?.addEventListener('submit', handlePencairanSubmit);
            dom.forms.review?.addEventListener('submit', handleReviewSubmit);
            dom.forms.edit?.addEventListener('submit', handleEditPencairanSubmit);
            dom.forms.upload?.addEventListener('submit', handleUploadSubmit);

            // Event listener untuk form Generate Kontrak di Step 7
            const formGenerateKontrak = document.getElementById('formGenerateKontrak');
            formGenerateKontrak?.addEventListener('submit', handleGenerateKontrakSubmit);

            const btnBatalKontrak = document.getElementById('btnBatalKontrak');
            btnBatalKontrak?.addEventListener('click', handleBatalKontrak);

            // Event listener untuk button Preview Kontrak di Activity Tab
            const btnPreviewKontrak = document.getElementById('btnPreviewKontrak');
            btnPreviewKontrak?.addEventListener('click', handlePreviewKontrak);

            updateStepper();
            initCleaveRupiah();

        });
    </script>
@endsection
