{{-- Data Peminjaman Section - Matching Original Design --}}
<h6 class="text-dark mb-3">Data Peminjaman</h6>
<div class="row g-3 mb-4">
    {{-- Nominal Pinjaman / Total Nominal Yang Dialihkan --}}
    <div class="col-12 col-sm-6 col-md-4 col-lg-4">
        <div class="mb-0">
            <small class="text-light fw-semibold d-block mb-1">
                @if ($jenis_pembiayaan === 'Factoring')
                    Total Nominal Yang Dialihkan
                @else
                    Nominal Pinjaman
                @endif
            </small>
            <p class="mb-0 text-success fw-semibold">Rp.
                @if ($jenis_pembiayaan === 'Factoring')
                    {{ number_format($latestHistory->nominal_yang_disetujui ?? ($nominal_pinjaman ?? 0), 0, ',', '.') }}
                @else
                    {{ number_format($latestHistory->nominal_yang_disetujui ?? ($nominal_pinjaman ?? 0), 0, ',', '.') }}
                @endif
            </p>
        </div>
    </div>
    
    @if ($jenis_pembiayaan === 'Installment')
        {{-- Installment Specific Fields --}}
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Tenor Pembayaran</small>
                <p class="fw-bold mb-0">
                    {{ $tenor_pembayaran ? $tenor_pembayaran . ' Bulan' : '-' }}
                </p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Persentase Bunga</small>
                @php
                    $p = $persentase_bunga ?? null;
                    $p_display = '-';
                    if ($p !== null && $p !== '') {
                        $p_display = rtrim(rtrim(sprintf('%.4f', (float) $p), '0'), '.') . '%';
                    }
                @endphp
                <p class="fw-bold mb-0">{{ $p_display }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">PPS</small>
                <p class="fw-bold mb-0">Rp. {{ number_format($pps ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">S Finance</small>
                <p class="fw-bold mb-0">Rp. {{ number_format($s_finance ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Pembayaran Total</small>
                <p class="mb-0 text-warning fw-semibold">Rp. {{ number_format($pembayaran_total ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Yang harus dibayarkan / bulan</small>
                <p class="mb-0 text-warning fw-semibold">Rp. {{ number_format($yang_harus_dibayarkan ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    @else
        {{-- Non-Installment Fields (Invoice Financing, PO Financing, Factoring) --}}
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Harapan Tanggal Pencairan</small>
                <p class="fw-bold mb-0">
                    {{ !empty($harapan_tanggal_pencairan) ? \Carbon\Carbon::parse($harapan_tanggal_pencairan)->translatedFormat('j F Y') : '-' }}
                </p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Persentase Bunga</small>
                @php
                    $p = $persentase_bunga ?? null;
                    $p_display = '-';
                    if ($p !== null && $p !== '') {
                        $p_display = rtrim(rtrim(sprintf('%.4f', (float) $p), '0'), '.') . '%';
                    }
                @endphp
                <p class="fw-bold mb-0">{{ $p_display }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Jenis Pembiayaan</small>
                <p class="fw-bold mb-0">{{ $jenis_pembiayaan ?? 'Invoice Financing' }}</p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Rencana Tanggal Bayar</small>
                <p class="fw-bold mb-0">
                    {{ !empty($rencana_tgl_pembayaran) ? \Carbon\Carbon::parse($rencana_tgl_pembayaran)->translatedFormat('j F Y') : '-' }}
                </p>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
            <div class="mb-0">
                <small class="text-light fw-semibold d-block mb-1">Pembayaran Total</small>
                <p class="mb-0 text-warning fw-semibold">Rp.
                    {{ number_format($pembayaran_total ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>
    @endif
</div>
