@push('scripts')
<script>
    $(document).ready(function() {
        const alert = (icon, html, title = icon === 'error' ? 'Error!' : icon === 'success' ? 'Berhasil!' : 'Perhatian') => 
            Swal.fire({ 
                icon, 
                title, 
                [icon === 'error' || icon === 'warning' ? 'html' : 'text']: html, 
                ...(icon === 'success' && { timer: 2000, showConfirmButton: false }) 
            });

        Livewire.on('alert', (event) => {
            const { icon, title, text } = event;
            alert(icon, text, title);
        });

        // Livewire refresh listener
        Livewire.on('refreshData', () => {
            @this.call('refreshData');
        });

        // Step 1: Submit Pengajuan
        $('#btnSubmitPengajuan').click(function() {
            $('#modalSubmitPengajuan').modal('show');
        });

        $('#btnKonfirmasiSubmit').click(function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
            
            @this.call('submitPengajuan').then(() => {
                $(document.activeElement).blur();
                $('#modalSubmitPengajuan').modal('hide');
                btn.prop('disabled', false).html('<i class="ti ti-send me-1"></i>Ya, Submit');
            }).catch((error) => {
                btn.prop('disabled', false).html('<i class="ti ti-send me-1"></i>Ya, Submit');
                console.error('Error:', error);
            });
        });

        // Step 2: Validasi Investment Officer
        $('#btnValidasiIO').click(function() {
            $('#modalValidasiIO').modal('show');
        });

        $('#formValidasiIO').submit(function(e) {
            e.preventDefault();
            
            const bagiHasil = $('#bagi_hasil_disetujui').val();
            const catatan = $('#catatan_validasi_io').val();
            
            if (!bagiHasil || parseFloat(bagiHasil) <= 0) {
                alert('error', 'Bagi hasil disetujui wajib diisi dan harus lebih dari 0');
                return;
            }

            // Disable submit button to prevent double submission
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

            @this.set('bagi_hasil_disetujui', bagiHasil);
            @this.set('catatan', catatan);
            
            // Wait for Livewire to finish, then close modal
            @this.call('validasiIOApprove').then(() => {
                // Blur focus before hiding modal to prevent ARIA-hidden error
                $(document.activeElement).blur();
                $('#modalValidasiIO').modal('hide');
                
                // Reset form and button
                $('#formValidasiIO')[0].reset();
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Setujui');
            }).catch((error) => {
                // Re-enable button on error
                submitBtn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Setujui');
                console.error('Error:', error);
            });
        });

        $('#btnTolakIO').click(function() {
            // Blur focus before hiding
            $(document.activeElement).blur();
            $('#modalValidasiIO').modal('hide');
            setTimeout(() => $('#modalAlasanPenolakanIO').modal('show'), 300);
        });

        $('#formAlasanPenolakanIO').submit(function(e) {
            e.preventDefault();
            
            const catatan = $('#alasan_penolakan_io').val();
            
            if (!catatan || catatan.trim() === '') {
                alert('error', 'Alasan penolakan wajib diisi');
                return;
            }

            // Disable submit button
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

            @this.set('catatan_penolakan', catatan);
            
            @this.call('validasiIOReject').then(() => {
                $(document.activeElement).blur();
                $('#modalAlasanPenolakanIO').modal('hide');
                
                $('#formAlasanPenolakanIO')[0].reset();
                submitBtn.prop('disabled', false).html('<i class="ti ti-x me-1"></i>Konfirmasi Penolakan');
            }).catch((error) => {
                submitBtn.prop('disabled', false).html('<i class="ti ti-x me-1"></i>Konfirmasi Penolakan');
                console.error('Error:', error);
            });
        });

        // Step 3: Persetujuan Debitur
        $('#btnPersetujuanDebitur').click(function() {
            $('#modalPersetujuanDebitur').modal('show');
        });

        $('#btnKonfirmasiDebitur').click(function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
            
            @this.call('persetujuanDebiturApprove').then(() => {
                $(document.activeElement).blur();
                $('#modalPersetujuanDebitur').modal('hide');
                btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Ya, Setuju');
            }).catch((error) => {
                btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Ya, Setuju');
                console.error('Error:', error);
            });
        });

        $('#btnTolakDebitur').click(function() {
            $(document.activeElement).blur();
            $('#modalPersetujuanDebitur').modal('hide');
            setTimeout(() => $('#modalAlasanPenolakanDebitur').modal('show'), 300);
        });

        $('#formAlasanPenolakanDebitur').submit(function(e) {
            e.preventDefault();
            
            const catatan = $('#alasan_penolakan_debitur').val();
            
            if (!catatan || catatan.trim() === '') {
                alert('error', 'Alasan penolakan wajib diisi');
                return;
            }

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

            @this.set('catatan_penolakan', catatan);
            
            @this.call('persetujuanDebiturReject').then(() => {
                $(document.activeElement).blur();
                $('#modalAlasanPenolakanDebitur').modal('hide');
                
                $('#formAlasanPenolakanDebitur')[0].reset();
                submitBtn.prop('disabled', false).html('<i class="ti ti-x me-1"></i>Konfirmasi Penolakan');
            }).catch((error) => {
                submitBtn.prop('disabled', false).html('<i class="ti ti-x me-1"></i>Konfirmasi Penolakan');
                console.error('Error:', error);
            });
        });

        // Step 4: Persetujuan SKI Finance
        $('#btnPersetujuanSKIFinance').click(function() {
            $('#modalPersetujuanSKIFinance').modal('show');
        });

        $('#btnKonfirmasiSKIFinance').click(function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
            
            @this.call('persetujuanSKIFinanceApprove').then(() => {
                $(document.activeElement).blur();
                $('#modalPersetujuanSKIFinance').modal('hide');
                btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Ya, Setuju');
            }).catch((error) => {
                btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Ya, Setuju');
                console.error('Error:', error);
            });
        });

        $('#btnTolakSKIFinance').click(function() {
            $(document.activeElement).blur();
            $('#modalPersetujuanSKIFinance').modal('hide');
            setTimeout(() => $('#modalAlasanPenolakanSKIFinance').modal('show'), 300);
        });

        $('#formAlasanPenolakanSKIFinance').submit(function(e) {
            e.preventDefault();
            
            const catatan = $('#alasan_penolakan_ski').val();
            
            if (!catatan || catatan.trim() === '') {
                alert('error', 'Alasan penolakan wajib diisi');
                return;
            }

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

            @this.set('catatan_penolakan', catatan);
            
            @this.call('persetujuanSKIFinanceReject').then(() => {
                $(document.activeElement).blur();
                $('#modalAlasanPenolakanSKIFinance').modal('hide');
                
                $('#formAlasanPenolakanSKIFinance')[0].reset();
                submitBtn.prop('disabled', false).html('<i class="ti ti-x me-1"></i>Konfirmasi Penolakan');
            }).catch((error) => {
                submitBtn.prop('disabled', false).html('<i class="ti ti-x me-1"></i>Konfirmasi Penolakan');
                console.error('Error:', error);
            });
        });

        // Step 5: Persetujuan CEO Finlog
        $('#btnPersetujuanCEOFinlog').click(function() {
            $('#modalPersetujuanCEOFinlog').modal('show');
        });

        $('#btnKonfirmasiCEOFinlog').click(function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');
            
            @this.call('persetujuanCEOApprove').then(() => {
                $(document.activeElement).blur();
                $('#modalPersetujuanCEOFinlog').modal('hide');
                btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Ya, Setuju');
            }).catch((error) => {
                btn.prop('disabled', false).html('<i class="ti ti-check me-1"></i>Ya, Setuju');
                console.error('Error:', error);
            });
        });

        $('#btnTolakCEOFinlog').click(function() {
            $(document.activeElement).blur();
            $('#modalPersetujuanCEOFinlog').modal('hide');
            setTimeout(() => $('#modalAlasanPenolakanCEOFinlog').modal('show'), 300);
        });

        $('#formAlasanPenolakanCEOFinlog').submit(function(e) {
            e.preventDefault();
            
            const catatan = $('#alasan_penolakan_ceo').val();
            
            if (!catatan || catatan.trim() === '') {
                alert('error', 'Alasan penolakan wajib diisi');
                return;
            }

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

            @this.set('catatan_penolakan', catatan);
            
            @this.call('persetujuanCEOReject').then(() => {
                $(document.activeElement).blur();
                $('#modalAlasanPenolakanCEOFinlog').modal('hide');
                
                $('#formAlasanPenolakanCEOFinlog')[0].reset();
                submitBtn.prop('disabled', false).html('<i class="ti ti-x me-1"></i>Konfirmasi Penolakan');
            }).catch((error) => {
                submitBtn.prop('disabled', false).html('<i class="ti ti-x me-1"></i>Konfirmasi Penolakan');
                console.error('Error:', error);
            });
        });

        // Step 6: Generate Kontrak
        // Format biaya administrasi with Cleave.js
        if ($('#biaya_administrasi').length) {
            new Cleave('#biaya_administrasi', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                prefix: 'Rp ',
                rawValueTrimPrefix: true
            });
        }

        $('#btnGenerateKontrak').click(function(e) {
            e.preventDefault();
            
            const nomorKontrak = $('#nomor_kontrak').val();
            const biayaAdministrasi = $('#biaya_administrasi').val().replace(/\D/g, '');
            const jaminan = $('#jaminan').val();
            
            if (!nomorKontrak || nomorKontrak.trim() === '') {
                alert('error', 'Nomor kontrak wajib diisi');
                return;
            }
            
            if (!biayaAdministrasi || parseFloat(biayaAdministrasi) <= 0) {
                alert('error', 'Biaya administrasi wajib diisi');
                return;
            }
            
            if (!jaminan || jaminan.trim() === '') {
                alert('error', 'Jaminan wajib diisi');
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Memproses...');

            @this.set('nomor_kontrak', nomorKontrak);
            @this.set('biaya_administrasi', biayaAdministrasi);
            @this.set('jaminan', jaminan);
            
            @this.call('generateKontrak').then(() => {
                $('#formGenerateKontrak')[0].reset();
                btn.prop('disabled', false).html('<i class="ti ti-file-text me-1"></i>Generate Kontrak');
            }).catch((error) => {
                btn.prop('disabled', false).html('<i class="ti ti-file-text me-1"></i>Generate Kontrak');
                console.error('Error:', error);
            });
        });

        // Step 7: Upload Bukti Transfer
        $('#btnUploadBuktiTransfer').click(function() {
            $('#modalUploadBuktiTransfer').modal('show');
        });

        $('#formUploadBuktiTransfer').submit(function(e) {
            e.preventDefault();
            
            const fileInput = $('#file_bukti_transfer')[0];
            const file = fileInput.files[0];
            
            if (!file) {
                alert('error', 'Silakan pilih file terlebih dahulu');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('error', 'Ukuran file maksimal 2MB');
                return;
            }

            // Upload file using Livewire
            @this.upload('bukti_transfer', file, (uploadedFilename) => {
                // File uploaded successfully
                @this.call('uploadBuktiTransfer');
                $('#modalUploadBuktiTransfer').modal('hide');
            }, (error) => {
                // Error callback
                alert('error', 'Terjadi kesalahan saat upload file');
            }, (event) => {
                // Progress callback
                console.log('Upload progress: ' + event.detail.progress + '%');
            });
        });

        // Preview Bukti Transfer
        window.previewBuktiTransfer = function(url) {
            const fileExt = url.split('.').pop().toLowerCase();
            
            if (fileExt === 'pdf') {
                $('#previewPdf').attr('src', url).removeClass('d-none');
                $('#previewImage').addClass('d-none');
            } else {
                $('#previewImage').attr('src', url).removeClass('d-none');
                $('#previewPdf').addClass('d-none');
            }
            
            $('#downloadBukti').attr('href', url);
            $('#modalPreviewBukti').modal('show');
        };
    });
</script>
@endpush
