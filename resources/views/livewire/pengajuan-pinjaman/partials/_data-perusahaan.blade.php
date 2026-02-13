{{-- Data Perusahaan Section - Matching Original Design --}}
<h6 class="text-dark mb-3">Data Perusahaan</h6>
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
        <div class="mb-0">
            <small class="text-light fw-semibold d-block mb-1">Nama Perusahaan</small>
            <p class="fw-bold mb-0">{{ $nama_perusahaan ?? '-' }}</p>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
        <div class="mb-0">
            <small class="text-light fw-semibold d-block mb-1">Nama Bank</small>
            <p class="fw-bold mb-0">{{ $nama_bank ?? '-' }}</p>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
        <div class="mb-0">
            <small class="text-light fw-semibold d-block mb-1">No Rekening</small>
            <p class="fw-bold mb-0">{{ $no_rekening ?? '-' }}</p>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
        <div class="mb-0">
            <small class="text-light fw-semibold d-block mb-1">Lampiran SID</small>
            <p class="fw-bold mb-0">
                @if (!empty($lampiran_sid))
                    <a href="{{ asset('storage/' . $lampiran_sid) }}" target="_blank">Lihat Lampiran</a>
                @else
                    -
                @endif
            </p>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl">
        <div class="mb-0">
            <small class="text-light fw-semibold d-block mb-1">Nilai KOL</small>
            <p class="fw-bold mb-0">{{ $nilai_kol ?? '-' }}</p>
        </div>
    </div>
</div>
