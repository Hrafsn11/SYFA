<div>
    @php
        $currentStep = $peminjaman->current_step ?? 1;
        $status = $peminjaman->status ?? 'Draft';
    @endphp

    <div class="row">
        <div class="col-lg">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0">Detail Pengajuan Peminjaman Finlog</h4>
                </div>
            </div>

            <!-- Stepper -->
            <div class="stepper-container mb-4">
                <div class="stepper-wrapper">

                    <div class="stepper-item {{ $currentStep >= 1 ? 'completed' : '' }} {{ $currentStep == 1 ? 'active' : '' }}"
                        data-step="1">
                        <div class="stepper-node">
                        </div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 1</div>
                            <div class="step-name">Pengajuan Peminjaman</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 2 ? 'completed' : '' }} {{ $currentStep == 2 ? 'active' : '' }}"
                        data-step="2">
                        <div class="stepper-node">
                        </div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 2</div>
                            <div class="step-name">Validasi Investment Officer</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 3 ? 'completed' : '' }} {{ $currentStep == 3 ? 'active' : '' }}"
                        data-step="3">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 3</div>
                            <div class="step-name">Persetujuan Debitur</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 4 ? 'completed' : '' }} {{ $currentStep == 4 ? 'active' : '' }}"
                        data-step="4">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 4</div>
                            <div class="step-name">Persetujuan SKI Finance</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 5 ? 'completed' : '' }} {{ $currentStep == 5 ? 'active' : '' }}"
                        data-step="5">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 5</div>
                            <div class="step-name">Persetujuan CEO SKI Finlog</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 6 ? 'completed' : '' }} {{ $currentStep == 6 ? 'active' : '' }}"
                        data-step="6">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 6</div>
                            <div class="step-name">Generate Kontrak</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 7 ? 'completed' : '' }} {{ $currentStep == 7 ? 'active' : '' }}"
                        data-step="7">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 7</div>
                            <div class="step-name">Upload Bukti Transfer</div>
                        </div>
                    </div>

                    <div class="stepper-item {{ $currentStep >= 8 ? 'completed' : '' }} {{ $currentStep == 8 ? 'active' : '' }}"
                        data-step="8">
                        <div class="stepper-node"></div>
                        <div class="stepper-content">
                            <div class="step-label">STEP 8</div>
                            <div class="step-name">Selesai</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card">
                <div class="card-header p-0">
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#detail-investasi" role="tab" aria-selected="true">
                                    <i class="ti ti-wallet me-2"></i>
                                    <span class="d-none d-sm-inline">Detail Peminjaman</span>
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
                                <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#activity"
                                    role="tab" aria-selected="false">
                                    <i class="ti ti-activity me-2"></i>
                                    <span class="d-none d-sm-inline">Activity</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- Detail Investasi Tab -->
                        <div class="tab-pane fade show active" id="detail-investasi" role="tabpanel">
                            @include('livewire.sfinlog.peminjaman.partials.detail-tab')
                        </div>

                        <!-- Detail Kontrak Tab -->
                        <div class="tab-pane fade" id="detail-kontrak" role="tabpanel">
                            @include('livewire.sfinlog.peminjaman.partials.kontrak-tab')
                        </div>

                        <!-- Activity Tab -->
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            @include('livewire.sfinlog.peminjaman.partials.activity-tab')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('livewire.sfinlog.peminjaman.partials.modal')
</div>

@include('livewire.sfinlog.peminjaman.partials.scripts')
