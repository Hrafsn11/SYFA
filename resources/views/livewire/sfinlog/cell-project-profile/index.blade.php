<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white overflow-hidden"
                style="background: linear-gradient(135deg, #13ABAB 0%, #0D7A7A 100%);">
                <div class="card-body position-relative py-5">
                    <div class="row align-items-center">
                        <div class="col-lg-7">
                            <div class="px-3">
                                <span class="bg-white rounded-pill mb-3 px-3 py-2 d-inline-flex align-items-center"
                                    style="color: #13ABAB; width: fit-content;">
                                    <i class="ti ti-sparkles me-1"></i> Investasi
                                </span>
                                <h1 class="display-5 fw-bold text-white mb-3">
                                    Investasi Mudah di <br>
                                    <span>Cells Project Syifa</span>
                                </h1>
                                <p class="lead mb-4 opacity-90">
                                    Temukan proyek unggulan dengan imbal hasil kompetitif. <br>
                                    Terverifikasi, transparan, dan dikelola profesional.
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    @php
                                        $isSFinlog = \App\Helpers\ModuleHelper::isSFinlog();
                                        $pengajuanInvestasiRoute = $isSFinlog 
                                            ? route('sfinlog.pengajuan-investasi.index') 
                                            : route('pengajuan-investasi.index');
                                    @endphp
                                    <a href="{{ $pengajuanInvestasiRoute }}"
                                        class="btn btn-lg btn-light fw-semibold px-4" style="color: #0D7A7A;">
                                        Mulai Berinvestasi <i class="ti ti-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 d-none d-lg-block">
                            <div class="d-flex justify-content-center align-items-center">
                                <div class="position-relative">
                                    <img src="{{ asset('assets/img/Investment-data-bro.svg') }}"
                                        alt="Investment Illustration" class="img-fluid"
                                        style="max-width: 380px; height: auto; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.15));">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="decorative-circles">
                        <div class="position-absolute"
                            style="width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.08); top: -50px; right: -50px;">
                        </div>
                        <div class="position-absolute"
                            style="width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05); bottom: -30px; left: 100px;">
                        </div>
                        <div class="position-absolute"
                            style="width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.03); top: 50%; right: 30%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>


        <div class="row g-4">
            @forelse($cellsProjects as $cells)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            {{-- Image Header Section --}}
                            <div class="rounded text-center mb-4 overflow-hidden" style="height: 150px;">
                                @if ($cells->profile_pict)
                                    <img src="{{ asset('storage/' . $cells->profile_pict) }}"
                                        alt="{{ $cells->nama_cells_bisnis }}" class="w-100 h-100 rounded"
                                        style="object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100 rounded"
                                        style="background: linear-gradient(135deg, #13ABAB 0%, #0D7A7A 100%);">
                                        <i class="ti ti-building-store text-white" style="font-size: 64px;"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Title & Description --}}
                            <h5 class="mb-2 fw-bold">{{ $cells->nama_cells_bisnis }}</h5>
                            <p class="small text-muted mb-4">{{ $cells->deskripsi_bidang }}</p>

                            {{-- Info Sections --}}
                            <div class="row mb-4 g-3">
                                <div class="col-6">
                                    <div class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="ti ti-user ti-28px"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 small fw-semibold">{{ Str::limit($cells->nama_pic, 12) }}
                                            </h6>
                                            <small class="text-muted">PIC</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-info">
                                                <i class="ti ti-folder ti-28px"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 small fw-semibold">{{ $cells->projects->count() }} Project
                                            </h6>
                                            <small class="text-muted">Tersedia</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Location Info --}}
                            <div class="d-flex align-items-center text-muted mb-4">
                                <i class="ti ti-map-pin me-2"></i>
                                <small>{{ Str::limit($cells->alamat, 40) }}</small>
                            </div>

                            {{-- Projects Tags --}}
                            @if ($cells->projects->count() > 0)
                                <div class="mb-4">
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($cells->projects->take(3) as $project)
                                            <span
                                                class="badge bg-light text-dark border">{{ $project->nama_project }}</span>
                                        @endforeach
                                        @if ($cells->projects->count() > 3)
                                            <span class="badge bg-secondary">+{{ $cells->projects->count() - 3 }}
                                                lainnya</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Action Button --}}
                            {{-- <a href="{{ route('sfinlog.pengajuan-investasi.index') }}" class="btn btn-primary w-100">
                                <i class="ti ti-arrow-right me-1"></i> Investasi Sekarang
                            </a> --}}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="ti ti-building-store text-muted mb-3" style="font-size: 48px;"></i>
                            <h5 class="text-muted">Belum ada Cells Project</h5>
                            <p class="text-muted mb-0">Cells Project akan ditampilkan di sini</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
