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

            {{-- <!-- Alert Messages -->
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
            @endif --}}

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
            }

            const ajaxPost = (url, data, onSuccess, btnSelector, loadingText) => {
                const $btn = $(btnSelector);
                const originalHtml = $btn.html();

                $.ajax({
                    url,
                    method: 'POST',
                    data: { _token: CSRF, ...data },
                    beforeSend: () => $btn.prop('disabled', true).html(loadingText),
                    success: (res) => res.error ? 
                        Swal.fire('Error!', res.message, 'error') : onSuccess(res),
                    error: (xhr) => Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'),
                    complete: () => $btn.prop('disabled', false).html(originalHtml)
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

            // Approve handler
            window.handleApprove = function(step) {
                pendingRejectStep = step;

                const stepTexts = {
                    2: ['Setujui Evaluasi Restrukturisasi?', 'Pastikan evaluasi kelengkapan dokumen dan kelayakan debitur sudah sesuai. Setelah disetujui, pengajuan akan diteruskan ke CEO SKI untuk persetujuan.'],
                    3: ['Setujui Persetujuan CEO SKI?', 'Pastikan hasil evaluasi sudah sesuai. Setelah disetujui oleh CEO SKI, pengajuan akan diteruskan ke Direktur untuk persetujuan final.'],
                    4: ['Setujui Persetujuan Direktur?', 'Ini adalah persetujuan final. Setelah disetujui oleh Direktur, proses restrukturisasi akan selesai dan status menjadi "Selesai".']
                };

                const [title, description] = stepTexts[step] || ['Apakah Anda yakin menyetujui pengajuan ini?', 'Pastikan semua data dan dokumen sudah sesuai sebelum melanjutkan.'];

                $('#approvalTitle').text(title);
                $('#approvalDescription').text(description);
                new bootstrap.Modal($('#modalApproval')[0]).show();
            };

            // Confirm approval from modal
            $('#btnKonfirmasiSetuju').click(function() {
                bootstrap.Modal.getInstance($('#modalApproval')[0]).hide();
                ajaxPost(
                    `/pengajuan-restrukturisasi/${ID}/decision`,
                    { action: 'approve', step: pendingRejectStep },
                    () => showSuccessReload('Pengajuan berhasil disetujui!'),
                    '#btnKonfirmasiSetuju',
                    '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...'
                );
            });

            // Open reject modal from approval modal
            $('#btnBukaPenolakan').click(function() {
                bootstrap.Modal.getInstance($('#modalApproval')[0]).hide();

                const rejectWarnings = {
                    2: ['Penolakan di Tahap Evaluasi', 'Jika Anda menolak di tahap ini, pengajuan akan dikembalikan ke pemohon untuk diperbaiki. Status akan berubah menjadi "Perbaikan Dokumen" dan pemohon dapat mengedit ulang pengajuan.'],
                    3: ['Penolakan oleh CEO SKI', 'Jika ditolak di tahap ini, pengajuan akan dikembalikan ke tahap evaluasi (Step 2) untuk dievaluasi ulang. Status akan berubah menjadi "Perlu Evaluasi Ulang".'],
                    4: ['Penolakan oleh Direktur', 'PERHATIAN: Penolakan di tahap ini bersifat final! Pengajuan akan masuk ke Step 5 (Selesai) dengan status "Ditolak" dan tidak dapat diproses kembali.']
                };

                const [title, text] = rejectWarnings[pendingRejectStep] || ['Perhatian!', 'Penolakan pengajuan akan dicatat dalam sistem.'];

                $('#rejectWarningTitle').text(title);
                $('#rejectWarningText').text(text);
                $('#rejectNote').val('');
                $('#formReject').removeClass('was-validated');

                setTimeout(() => new bootstrap.Modal($('#modalReject')[0]).show(), 300);
            });

            // Submit rejection
            $('#formReject').submit(function(e) {
                e.preventDefault();
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                const $spinner = $('#rejectSpinner');
                const $btn = $(this).find('button[type="submit"]');
                const originalHtml = $btn.html();

                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');

                $.ajax({
                    url: `/pengajuan-restrukturisasi/${ID}/decision`,
                    method: 'POST',
                    data: { _token: CSRF, action: 'reject', step: pendingRejectStep, note: $('#rejectNote').val() },
                    success: (res) => res.error ? 
                        Swal.fire('Error!', res.message, 'error') :
                        (bootstrap.Modal.getInstance($('#modalReject')[0]).hide(), 
                         Swal.fire('Pengajuan Ditolak', 'Pengajuan telah ditolak', 'info').then(() => location.reload())),
                    error: (xhr) => Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'),
                    complete: () => {
                        $btn.prop('disabled', false).html(originalHtml);
                        $spinner.addClass('d-none');
                    }
                });
            });

            updateUI();

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
                const collectData = (selector, mapper) => Array.from(document.querySelectorAll(selector)).map(mapper);
                
                const collectKelengkapan = () => collectData('#table-kelengkapan-body tr', tr => ({
                    nama_dokumen: tr.children[1]?.textContent.trim() || '',
                    status: tr.querySelector('input[type="radio"]:checked')?.value || null,
                    catatan: tr.querySelector('textarea[name^="catatan_kelengkapan"]')?.value?.trim() || ''
                }));

                const collectKelayakan = () => collectData('#table-kelayakan-body tr', tr => ({
                    kriteria: tr.children[1]?.textContent.trim() || '',
                    status: tr.querySelector('input[type="radio"]:checked')?.value || null,
                    catatan: tr.querySelector('textarea[name^="catatan_kelayakan"]')?.value?.trim() || ''
                }));

                const collectAnalisa = () => collectData('#table-analisa-body tr', tr => ({
                    aspek: tr.children[1]?.textContent.trim() || '',
                    evaluasi: tr.querySelector('input[type="radio"]:checked')?.value || null,
                    catatan: tr.querySelector('textarea[name^="catatan_analisa"]')?.value?.trim() || ''
                }));

                const collectCommittee = () => collectData('#committee-container .approval-row', row => ({
                    nama_anggota: row.querySelector('input[name$="[nama_anggota]"]')?.value || '',
                    jabatan: row.querySelector('input[name$="[jabatan]"]')?.value || '',
                    tanggal_persetujuan: row.querySelector('input[name$="[tanggal_persetujuan]"]')?.value || '',
                    ttd_digital: row.querySelector('input[name$="[ttd_digital]"]')?.files[0] || null
                }));

                // Validate
                const validateEvaluasi = (kelengkapan, kelayakan, analisa) => [
                    ...kelengkapan.map((row, idx) => !row.status ? `Kelengkapan dokumen baris ${idx + 1}: Status belum dipilih` : null),
                    ...kelayakan.map((row, idx) => !row.status ? `Kelayakan kriteria ${idx + 1}: Status belum dipilih` : null),
                    ...analisa.map((row, idx) => !row.evaluasi ? `Analisa aspek ${idx + 1}: Evaluasi belum dipilih` : null)
                ].filter(Boolean);

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
                    if (errors.length) return Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', html: '<ul class="text-start">' + errors.map(e => `<li>${e}</li>`).join('') + '</ul>' });
                    if (!rekomendasi) return Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Rekomendasi harus dipilih' });
                    if (!committee.length || !committee[0].nama_anggota) return Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Minimal 1 anggota komite harus diisi' });

                    // Prepare FormData
                    const formData = new FormData();
                    Object.entries({ _token: CSRF, kelengkapan: JSON.stringify(kelengkapan), kelayakan: JSON.stringify(kelayakan), analisa: JSON.stringify(analisa), rekomendasi, justifikasi_rekomendasi: justifikasi })
                        .forEach(([key, val]) => formData.append(key, val));

                    committee.forEach((c, idx) => {
                        ['nama_anggota', 'jabatan', 'tanggal_persetujuan'].forEach(field => 
                            formData.append(`persetujuan_komite[${idx}][${field}]`, c[field]));
                        if (c.ttd_digital) formData.append(`persetujuan_komite[${idx}][ttd_digital]`, c.ttd_digital);
                    });

                    const $btn = $(this);
                    const originalHtml = $btn.html();

                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

                    $.ajax({
                        url: `/pengajuan-restrukturisasi/${ID}/evaluasi`,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: (res) => res.error ? 
                            Swal.fire('Error!', res.message, 'error') : 
                            Swal.fire('Berhasil!', res.message || 'Evaluasi berhasil disimpan!', 'success').then(() => location.reload()),
                        error: (xhr) => Swal.fire('Error!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error'),
                        complete: () => $btn.prop('disabled', false).html(originalHtml)
                    });
                });
            @endif
        });
    </script>
@endsection
