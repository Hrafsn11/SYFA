<div>
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Detail Pengajuan Peminjaman</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home.services') }}" wire:navigate>Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('peminjaman.index') }}" wire:navigate>Peminjaman Dana</a>
                    </li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <span class="badge bg-{{ $status === 'Dana Sudah Dicairkan' ? 'success' : 'primary' }} fs-6">
                {{ $status }}
            </span>
        </div>
    </div>

    {{-- Stepper --}}
    @include('livewire.pengajuan-pinjaman.partials._stepper')

    {{-- Alert Peninjauan --}}
    @if ($this->shouldShowAlertPeninjauan())
        <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
            <i class="fas fa-info-circle me-2"></i>
            Pengajuan Pinjaman Anda sedang kami tinjau. Harap tunggu
            beberapa saat hingga proses verifikasi selesai.
        </div>
    @endif

    {{-- Main Content Card with Tabs --}}
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
                        {{-- Detail Pinjaman Tab --}}
                        <div class="tab-pane fade show active" id="detail-pinjaman" role="tabpanel">
                            <div id="content-default">
                                <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
                                    <h5 class="mb-3 mb-md-4">Detail Pinjaman</h5>
                                    <div class="d-flex gap-2">
                                        @include('livewire.pengajuan-pinjaman.partials._action-buttons')
                                    </div>
                                </div>

                                <hr class="my-3 my-md-4">

                                {{-- Data Perusahaan --}}
                                @include('livewire.pengajuan-pinjaman.partials._data-perusahaan')

                                <hr class="my-3 my-md-4">

                                {{-- Data Peminjaman --}}
                                @include('livewire.pengajuan-pinjaman.partials._data-peminjaman')

                                <hr class="my-3 my-md-4">

                                {{-- Data Invoice/Kontrak --}}
                                @include('livewire.pengajuan-pinjaman.partials._data-invoice-table')
                            </div>
                        </div>

                        {{-- Detail Kontrak Tab --}}
                        <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                            <div class="mb-4">
                                <h5 class="mb-0">Detail Kontrak</h5>
                            </div>
                            <hr class="my-3">
                            
                            @if (!empty($no_kontrak))
                                <div class="row g-3 mb-4">
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div class="mb-0">
                                            <small class="text-light fw-semibold d-block mb-1">Nomor Kontrak</small>
                                            <p class="fw-bold mb-0">{{ $no_kontrak }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary" wire:click="previewKontrak">
                                        <i class="ti ti-eye me-2"></i>
                                        Preview Kontrak
                                    </button>
                                    <button type="button" class="btn btn-primary" wire:click="downloadKontrak">
                                        <i class="ti ti-download me-2"></i>
                                        Download Kontrak
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="ti ti-file-off display-4 text-muted"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">Kontrak Belum Tersedia</h5>
                                    <p class="text-muted mb-0">Kontrak akan digenerate setelah proses persetujuan selesai.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Activity Tab --}}
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            @include('livewire.pengajuan-pinjaman.partials._activity-tab')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Modals --}}
    @include('livewire.pengajuan-pinjaman.components.modal_approval')



    {{-- Notification Handler --}}
    @script
    <script>
        $wire.on('notify', (data) => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: data[0].type,
                    title: data[0].type === 'success' ? 'Berhasil!' : 'Error!',
                    text: data[0].message,
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                alert(data[0].message);
            }
        });

        $wire.on('closeModal', () => {
            window.dispatchEvent(new CustomEvent('close-modal'));
        });
    </script>
    @endscript
</div>
