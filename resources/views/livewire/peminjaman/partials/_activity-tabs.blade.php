<div class="tab-pane fade" id="activity" role="tabpanel">
    <div class="mb-4">
        <h5 class="mb-0">Aktivitas Terakhir</h5>
    </div>

    <hr class="my-3">

    <!-- Empty state untuk step 1 -->
    <div id="activity-empty" class="text-center py-5">
        <div class="mb-3">
            <i class="ti ti-clipboard-list display-4 text-muted"></i>
        </div>
        <h5 class="text-muted mb-2">Belum Ada Aktivitas</h5>
        <p class="text-muted mb-0">Aktivitas akan muncul setelah proses validasi
            dimulai.
        </p>
    </div>

    <!-- Timeline Container - hanya muncul dari step 2 -->
    <div class="d-none" id="timeline-container">
        <!-- Step 2: Validasi Dokumen -->
        <div class="activity-item d-none mb-4" id="activity-step-2">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-warning"><i
                                        class="ti ti-report-search"></i></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Validasi Dokumen</h6>
                            <p class="text-muted mb-0 small">Pengajuan sedang dalam
                                proses
                                validasi.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <small class="text-muted" id="date-step-2">-</small>
                </div>
                <div class="col-6 col-md-3 text-end"></div>
            </div>
        </div>

        <!-- Step 4: Persetujuan Debitur -->
        <div class="activity-item d-none mt-3 mb-4" id="activity-step-3">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-primary"><i
                                        class="ti ti-file-text"></i></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Draft: Persetujuan Debitur <i class="ti ti-arrow-right mx-1"></i> Pengajuan
                                Disetujui</h6>
                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <small class="text-muted" id="date-step-4">-</small>
                </div>
                <div class="col-6 col-md-3 text-end">
                    <button type="button" class="btn btn-icon btn-sm btn-label-primary" id="btnEditPencairan"
                        title="Edit">
                        <i class="ti ti-edit"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 5: Validasi Direktur SKI -->
        <div class="activity-item d-none mt-3 mb-4" id="activity-step-4">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-primary"><i
                                        class="ti ti-file-text"></i></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Draft: Validasi Direktur SKI <i class="ti ti-arrow-right mx-1"></i>
                                Pengajuan
                                Disetujui
                            </h6>
                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <small class="text-muted" id="date-step-5">-</small>
                </div>
                <div class="col-6 col-md-3 text-end">
                    <button type="button" class="btn btn-icon btn-sm btn-label-primary" id="btnEditPencairan"
                        title="Edit">
                        <i class="ti ti-edit"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 6: Validasi Direktur -->
        <div class="activity-item d-none mt-3 mb-4" id="activity-step-5">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-primary"><i
                                        class="ti ti-file-text"></i></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Draft: Validasi Direktur <i class="ti ti-arrow-right mx-1"></i> Pengajuan
                                Disetujui
                            </h6>
                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <small class="text-muted" id="date-step-6">-</small>
                </div>
                <div class="col-6 col-md-3 text-end">
                    <button type="button" class="btn btn-icon btn-sm btn-label-primary" id="btnEditPencairan"
                        title="Edit">
                        <i class="ti ti-edit"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 7: Generate Kontrak -->
        <div class="activity-item d-none mt-3 mb-4" id="activity-step-6">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-primary"><i
                                        class="ti ti-file-text"></i></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Draft: Generate Kontrak <i class="ti ti-arrow-right mx-1"></i> Pengajuan
                                Disetujui</h6>
                            <p class="text-muted mb-0 small">Pengajuan telah terkirim.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <small class="text-muted" id="date-step-7">-</small>
                </div>
                <div class="col-6 col-md-3 text-end">
                    <button type="button" class="btn btn-icon btn-sm btn-label-primary" id="btnPreviewKontrak"
                        title="Edit">
                        <i class="ti ti-file-text"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 8: Upload Dokumen -->
        <div class="activity-item d-none mt-3 mb-4" id="activity-step-7">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-warning"><i
                                        class="ti ti-upload"></i></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Upload Dokumen <i class="ti ti-arrow-right mx-1"></i> Pengajuan
                                Disetujui</h6>
                            <p class="text-muted mb-0 small">Bukti Pengiriman telah
                                terkirim.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <small class="text-muted" id="date-step-8">-</small>
                </div>
                <div class="col-6 col-md-3 text-end">
                    <button type="button" class="btn btn-icon btn-sm btn-label-success" id="btnUploadDokumen"
                        title="Upload Dokumen">
                        <i class="ti ti-upload"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 9: Selesai -->
        <div class="activity-item d-none mt-3 mb-4" id="activity-step-8">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-success"><i
                                        class="ti ti-circle-check"></i></span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">Selesai</h6>
                            <p class="text-muted mb-0 small">Proses pengajuan pinjaman
                                telah selesai.</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 text-center">
                    <small class="text-muted" id="date-step-9">-</small>
                </div>
                <div class="col-6 col-md-3 text-end"></div>
            </div>
        </div>
    </div>
</div>