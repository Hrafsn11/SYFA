@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <h4 class="fw-bold mb-4">Detail Pengajuan Restrukturisasi</h4>

            <!-- Stepper -->
            <div class="stepper-container mb-4">
                <div class="stepper-wrapper">
                    @foreach (['Pengajuan Restrukturisasi', 'Evaluasi Dokumen', 'Persetujuan CEO', 'Persetujuan Direktur', 'Selesai'] as $i => $name)
                        <div class="stepper-item" data-step="{{ $i + 1 }}">
                            <div class="stepper-node"></div>
                            <div class="stepper-content">
                                <div class="step-label">STEP {{ $i + 1 }}</div>
                                <div class="step-name">{{ $name }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Alert Messages -->
            @if ($restrukturisasi['status'] === 'Draft')
                <div class="alert alert-info mb-4" role="alert" id="alertDraft">
                    <i class="fas fa-info-circle me-2"></i>
                    Pengajuan restrukturisasi masih dalam status <strong>Draft</strong>. Silakan klik tombol <strong>"Submit
                        Pengajuan"</strong> untuk melanjutkan proses evaluasi.
                </div>
            @elseif($restrukturisasi['status'] === 'Perbaikan Dokumen')
                <div class="alert alert-warning mb-4" role="alert" id="alertPerbaikan">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Pengajuan restrukturisasi Anda <strong>memerlukan perbaikan dokumen</strong>. Pengajuan ditolak pada tahap evaluasi. 
                    Silakan perbaiki data/dokumen yang diperlukan, lalu klik <strong>"Submit Ulang Pengajuan"</strong> untuk diproses kembali.
                    <br><small class="mt-2 d-block">Cek tab <strong>Activity</strong> untuk melihat alasan penolakan.</small>
                </div>
            @elseif($restrukturisasi['status'] === 'Ditolak')
                <div class="alert alert-danger mb-4" role="alert" id="alertDitolak">
                    <i class="fas fa-times-circle me-2"></i>
                    Pengajuan restrukturisasi Anda <strong>Ditolak</strong>. Proses restrukturisasi telah berakhir dan tidak dapat diajukan kembali.
                    <br><small class="mt-2 d-block">Cek tab <strong>Activity</strong> untuk melihat alasan penolakan.</small>
                </div>
            @else
                <div class="alert alert-warning mb-4" role="alert" id="alertPeninjauan">
                    <i class="fas fa-info-circle me-2"></i>
                    Pengajuan Restrukturisasi sedang kami tinjau. Harap tunggu beberapa saat hingga proses verifikasi
                    selesai.
                </div>
            @endif

            <!-- Tabs -->
            <div class="card mb-4">
                <div class="card-header p-0">
                    <div class="nav-align-top">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active" data-bs-toggle="tab"
                                    data-bs-target="#detail-restrukturisasi" role="tab">
                                    <i class="ti ti-file-text me-2"></i>
                                    <span class="d-none d-sm-inline">Detail</span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link" data-bs-toggle="tab"
                                    data-bs-target="#activity-restrukturisasi" role="tab">
                                    <i class="ti ti-activity me-2"></i>
                                    <span class="d-none d-sm-inline">Activity</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content p-0">
                        <!-- Detail Tab -->
                        <div class="tab-pane fade show active" id="detail-restrukturisasi" role="tabpanel">
                            @include('livewire.pengajuan-restrukturisasi.partials._detail-tab')
                        </div>

                        <!-- Activity Tab -->
                        <div class="tab-pane fade" id="activity-restrukturisasi" role="tabpanel">
                            @include('livewire.pengajuan-restrukturisasi.partials._activity-tab')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Approval --}}
    <div class="modal fade" id="modalApproval" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-2" id="approvalTitle">Apakah Anda yakin menyetujui pengajuan restrukturisasi ini?</h5>
                    <p class="mb-0 text-muted" id="approvalDescription">
                        Pastikan semua data dan dokumen sudah sesuai sebelum melanjutkan ke tahap berikutnya.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btnKonfirmasiSetuju">
                        <i class="fas fa-check me-2"></i>
                        Ya, Setujui
                    </button>
                    <button type="button" class="btn btn-danger" id="btnBukaPenolakan">
                        <i class="fas fa-times me-2"></i>
                        Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Penolakan --}}
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Penolakan Pengajuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formReject">
                    <div class="modal-body">
                        {{-- <div class="alert alert-warning mb-3" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong id="rejectWarningTitle">Perhatian!</strong>
                        <p class="mb-0 mt-2" id="rejectWarningText"></p>
                    </div> --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejectNote" rows="5" placeholder="Jelaskan alasan penolakan secara detail..."
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-arrow-left me-2"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="rejectSpinner"></span>
                            <span id="rejectBtnText">
                                Konfirmasi Penolakan
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ID = '{{ $restrukturisasi['id'] }}';
            const STATUS = '{{ $restrukturisasi['status'] }}';
            const STEP = {{ $restrukturisasi['current_step'] }};
            const CSRF = '{{ csrf_token() }}';
            let pendingRejectStep = null;

            // Update stepper UI
            function updateUI() {
                document.querySelectorAll('.stepper-item').forEach((el, idx) => {
                    const step = idx + 1;
                    el.classList.toggle('completed', step < STEP);
                    el.classList.toggle('active', step === STEP);
                });

                const isDraft = STATUS === 'Draft';
                const isDitolak = STATUS === 'Ditolak';

                // Show/hide alerts
                const alertDraft = document.getElementById('alertDraft');
                const alertDitolak = document.getElementById('alertDitolak');
                const alertPeninjauan = document.getElementById('alertPeninjauan');

                if (alertDraft) alertDraft.style.display = isDraft ? 'block' : 'none';
                if (alertDitolak) alertDitolak.style.display = isDitolak ? 'block' : 'none';
                if (alertPeninjauan) alertPeninjauan.style.display = (!isDraft && !isDitolak) ? 'block' : 'none';
            }

            const ajaxPost = (url, data, onSuccess, btnSelector, loadingText) => {
                const $btn = $(btnSelector);
                const originalHtml = $btn.html();

                $.ajax({
                    url,
                    method: 'POST',
                    data: {
                        _token: CSRF,
                        ...data
                    },
                    beforeSend: () => $btn.prop('disabled', true).html(loadingText),
                    success: (res) => {
                        if (res.error) {
                            Swal.fire('Error!', res.message, 'error');
                            $btn.prop('disabled', false).html(originalHtml);
                        } else {
                            onSuccess(res);
                        }
                    },
                    error: (xhr) => {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan',
                            'error');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            };

            const showSuccessReload = (msg) => Swal.fire('Berhasil!', msg, 'success').then(() => location.reload());

            // Submit pengajuan (Draft -> Step 2)
            window.submitPengajuan = function() {
                ajaxPost(
                    `/pengajuan-restrukturisasi/${ID}/decision`, {
                        action: 'approve',
                        step: 1
                    },
                    () => showSuccessReload('Pengajuan berhasil disubmit!'),
                    '#btnSubmitPengajuan',
                    '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...'
                );
            };

            // Approve handler - Show modal approval first
            window.handleApprove = function(step) {
                pendingRejectStep = step; // Store current step for reject option

                // Customize modal text based on step
                const stepTexts = {
                    2: {
                        title: 'Setujui Evaluasi Restrukturisasi?',
                        description: 'Pastikan evaluasi kelengkapan dokumen dan kelayakan debitur sudah sesuai. Setelah disetujui, pengajuan akan diteruskan ke CEO SKI untuk persetujuan.'
                    },
                    3: {
                        title: 'Setujui Persetujuan CEO SKI?',
                        description: 'Pastikan hasil evaluasi sudah sesuai. Setelah disetujui oleh CEO SKI, pengajuan akan diteruskan ke Direktur untuk persetujuan final.'
                    },
                    4: {
                        title: 'Setujui Persetujuan Direktur?',
                        description: 'Ini adalah persetujuan final. Setelah disetujui oleh Direktur, proses restrukturisasi akan selesai dan status menjadi "Selesai".'
                    }
                };

                const stepText = stepTexts[step] || {
                    title: 'Apakah Anda yakin menyetujui pengajuan ini?',
                    description: 'Pastikan semua data dan dokumen sudah sesuai sebelum melanjutkan.'
                };

                $('#approvalTitle').text(stepText.title);
                $('#approvalDescription').text(stepText.description);

                // Show approval modal
                const modalApproval = new bootstrap.Modal($('#modalApproval')[0]);
                modalApproval.show();
            };

            // Confirm approval from modal
            $('#btnKonfirmasiSetuju').click(function() {
                const step = pendingRejectStep;
                bootstrap.Modal.getInstance($('#modalApproval')[0]).hide();

                ajaxPost(
                    `/pengajuan-restrukturisasi/${ID}/decision`, {
                        action: 'approve',
                        step: step
                    },
                    () => showSuccessReload('Pengajuan berhasil disetujui!'),
                    '#btnKonfirmasiSetuju',
                    '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...'
                );
            });

            // Open reject modal from approval modal
            $('#btnBukaPenolakan').click(function() {
                const step = pendingRejectStep;

                // Hide approval modal first
                bootstrap.Modal.getInstance($('#modalApproval')[0]).hide();

                // Customize rejection warning based on step
                const rejectWarnings = {
                    2: {
                        title: 'Penolakan di Tahap Evaluasi',
                        text: 'Jika Anda menolak di tahap ini, pengajuan akan dikembalikan ke pemohon untuk diperbaiki. Status akan berubah menjadi "Perbaikan Dokumen" dan pemohon dapat mengedit ulang pengajuan.'
                    },
                    3: {
                        title: 'Penolakan oleh CEO SKI',
                        text: 'Jika ditolak di tahap ini, pengajuan akan dikembalikan ke tahap evaluasi (Step 2) untuk dievaluasi ulang. Status akan berubah menjadi "Perlu Evaluasi Ulang".'
                    },
                    4: {
                        title: 'Penolakan oleh Direktur',
                        text: 'PERHATIAN: Penolakan di tahap ini bersifat final! Pengajuan akan masuk ke Step 5 (Selesai) dengan status "Ditolak" dan tidak dapat diproses kembali.'
                    }
                };

                const warning = rejectWarnings[step] || {
                    title: 'Perhatian!',
                    text: 'Penolakan pengajuan akan dicatat dalam sistem.'
                };

                $('#rejectWarningTitle').text(warning.title);
                $('#rejectWarningText').text(warning.text);
                $('#rejectNote').val('');
                $('#formReject').removeClass('was-validated');

                // Show reject modal after a small delay
                setTimeout(() => {
                    const modalReject = new bootstrap.Modal($('#modalReject')[0]);
                    modalReject.show();
                }, 300);
            });

            // Submit rejection
            $('#formReject').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                const note = $('#rejectNote').val();
                const $spinner = $('#rejectSpinner');
                const $btn = $(this).find('button[type="submit"]');
                const originalHtml = $btn.html();

                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');

                $.ajax({
                    url: `/pengajuan-restrukturisasi/${ID}/decision`,
                    method: 'POST',
                    data: {
                        _token: CSRF,
                        action: 'reject',
                        step: pendingRejectStep,
                        note: note
                    },
                    success: (res) => {
                        if (!res.error) {
                            bootstrap.Modal.getInstance($('#modalReject')[0]).hide();
                            Swal.fire('Pengajuan Ditolak', 'Pengajuan telah ditolak', 'info')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error!', res.message, 'error');
                        }
                    },
                    error: (xhr) => {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan',
                            'error');
                    },
                    complete: () => {
                        $btn.prop('disabled', false).html(originalHtml);
                        $spinner.addClass('d-none');
                    }
                });
            });

            updateUI();

            // ===== EVALUASI FORM SCRIPT (Committee + Save) =====
            @if (isset($pengajuan) && $pengajuan->current_step == 2)
                let committeeIndex = 0;

                // Add committee row function
                window.addCommitteeRow = function() {
                    const html = `
            <div class="approval-row row g-3 p-3 mb-3 border rounded" data-index="${committeeIndex}">
                <div class="col-md-3">
                    <label class="form-label small">Nama Anggota</label>
                    <input type="text" class="form-control" name="committee[${committeeIndex}][nama_anggota]" 
                        placeholder="Nama Lengkap" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Jabatan</label>
                    <input type="text" class="form-control" name="committee[${committeeIndex}][jabatan]" 
                        placeholder="Jabatan" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Tanggal</label>
                    <input type="date" class="form-control" name="committee[${committeeIndex}][tanggal_persetujuan]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Upload TTD</label>
                    <input type="file" class="form-control" name="committee[${committeeIndex}][ttd_digital]" accept=".png,.jpg,.jpeg">
                </div>
                <div class="col-md-1 d-flex align-items-end justify-content-center">
                    ${committeeIndex > 0 ? `<button type="button" class="btn btn-icon btn-outline-danger btn-sm" onclick="removeCommitteeRow(${committeeIndex})"><i class="ti ti-trash"></i></button>` : ''}
                </div>
            </div>
        `;
                    $('#committee-container').append(html);
                    committeeIndex++;
                };

                // Remove committee row
                window.removeCommitteeRow = function(index) {
                    $(`.approval-row[data-index="${index}"]`).remove();
                };

                // Initialize first committee row
                addCommitteeRow();

                // Collect data functions
                function collectKelengkapan() {
                    const rows = document.querySelectorAll('#table-kelengkapan-body tr');
                    return Array.from(rows).map((tr, idx) => ({
                        nama_dokumen: tr.children[1]?.textContent.trim() || '',
                        status: tr.querySelector('input[type="radio"]:checked')?.value || null,
                        catatan: tr.querySelector('textarea[name^="catatan_kelengkapan"]')?.value
                        .trim() || ''
                    }));
                }

                function collectKelayakan() {
                    const rows = document.querySelectorAll('#table-kelayakan-body tr');
                    return Array.from(rows).map((tr, idx) => ({
                        kriteria: tr.children[1]?.textContent.trim() || '',
                        status: tr.querySelector('input[type="radio"]:checked')?.value || null,
                        catatan: tr.querySelector('textarea[name^="catatan_kelayakan"]')?.value
                        .trim() || ''
                    }));
                }

                function collectAnalisa() {
                    const rows = document.querySelectorAll('#table-analisa-body tr');
                    return Array.from(rows).map((tr, idx) => ({
                        aspek: tr.children[1]?.textContent.trim() || '',
                        evaluasi: tr.querySelector('input[type="radio"]:checked')?.value || null,
                        catatan: tr.querySelector('textarea[name^="catatan_analisa"]')?.value.trim() ||
                            ''
                    }));
                }

                function collectCommittee() {
                    const rows = document.querySelectorAll('#committee-container .approval-row');
                    return Array.from(rows).map((row, idx) => ({
                        nama_anggota: row.querySelector('input[name$="[nama_anggota]"]')?.value || '',
                        jabatan: row.querySelector('input[name$="[jabatan]"]')?.value || '',
                        tanggal_persetujuan: row.querySelector('input[name$="[tanggal_persetujuan]"]')
                            ?.value || '',
                        ttd_digital: row.querySelector('input[name$="[ttd_digital]"]')?.files[0] || null
                    }));
                }

                // Validate
                function validateEvaluasi(kelengkapan, kelayakan, analisa) {
                    const errors = [];

                    kelengkapan.forEach((row, idx) => {
                        if (!row.status) errors.push(
                            `Kelengkapan dokumen baris ${idx + 1}: Status belum dipilih`);
                    });

                    kelayakan.forEach((row, idx) => {
                        if (!row.status) errors.push(`Kelayakan kriteria ${idx + 1}: Status belum dipilih`);
                    });

                    analisa.forEach((row, idx) => {
                        if (!row.evaluasi) errors.push(`Analisa aspek ${idx + 1}: Evaluasi belum dipilih`);
                    });

                    return errors;
                }

                // Save evaluasi
                $('#btn-save-evaluasi').click(function() {
                    const kelengkapan = collectKelengkapan();
                    const kelayakan = collectKelayakan();
                    const analisa = collectAnalisa();
                    const committee = collectCommittee();
                    const rekomendasi = document.getElementById('rekomendasi_analis')?.value || '';
                    const justifikasi = document.getElementById('justifikasi_rekomendasi')?.value || '';

                    // Validate
                    const errors = validateEvaluasi(kelengkapan, kelayakan, analisa);
                    if (errors.length > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Belum Lengkap',
                            html: '<ul class="text-start">' + errors.map(e => `<li>${e}</li>`).join(
                                '') + '</ul>',
                        });
                        return;
                    }

                    if (!rekomendasi) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Rekomendasi harus dipilih'
                        });
                        return;
                    }

                    // Validate committee
                    if (committee.length === 0 || !committee[0].nama_anggota) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian',
                            text: 'Minimal 1 anggota komite harus diisi'
                        });
                        return;
                    }

                    // Prepare FormData for file upload
                    const formData = new FormData();
                    formData.append('_token', CSRF);
                    formData.append('kelengkapan', JSON.stringify(kelengkapan));
                    formData.append('kelayakan', JSON.stringify(kelayakan));
                    formData.append('analisa', JSON.stringify(analisa));
                    formData.append('rekomendasi', rekomendasi);
                    formData.append('justifikasi_rekomendasi', justifikasi);

                    // Add committee data with files
                    committee.forEach((c, idx) => {
                        formData.append(`persetujuan_komite[${idx}][nama_anggota]`, c.nama_anggota);
                        formData.append(`persetujuan_komite[${idx}][jabatan]`, c.jabatan);
                        formData.append(`persetujuan_komite[${idx}][tanggal_persetujuan]`, c
                            .tanggal_persetujuan);
                        if (c.ttd_digital) {
                            formData.append(`persetujuan_komite[${idx}][ttd_digital]`, c
                                .ttd_digital);
                        }
                    });

                    const $btn = $(this);
                    const originalHtml = $btn.html();

                    $btn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

                    $.ajax({
                        url: `/pengajuan-restrukturisasi/${ID}/evaluasi`,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: (res) => {
                            if (!res.error) {
                                Swal.fire('Berhasil!', res.message ||
                                        'Evaluasi berhasil disimpan!', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error!', res.message, 'error');
                                $btn.prop('disabled', false).html(originalHtml);
                            }
                        },
                        error: (xhr) => {
                            Swal.fire('Error!', xhr.responseJSON?.message ||
                                'Terjadi kesalahan', 'error');
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                });
            @endif
        });
    </script>
@endsection
