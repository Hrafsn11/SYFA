@extends('layouts.app')

@section('content')
    <div>
        <div>
            <a href="{{ route('pengembalian.index') }}" class="btn btn-outline-primary mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Kembali
            </a>
            <h4 class="fw-bold">
                Menu Pengembalian Peminjaman
            </h4>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('pengembalian.store') }}" method="POST" id="formPengembalian"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-lg mb-3">
                            <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan"
                                value="{{ $namaPerusahaan }}" readonly required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg mb-3">
                            <label for="kode_peminjaman" class="form-label">Kode Peminjaman</label>
                            <select class="form-control select2" name="kode_peminjaman" id="kode_peminjaman"
                                data-placeholder="Pilih Peminjaman" required>
                                <option value="">Pilih Peminjaman</option>
                                @foreach ($pengajuanPeminjaman as $item)
                                    <option value="{{ $item->id_pengajuan_peminjaman }}"
                                        data-total-pinjaman="{{ $item->total_pinjaman }}"
                                        data-total-bagi-hasil="{{ $item->total_bagi_hasil }}"
                                        data-tanggal-pencairan="{{ $item->harapan_tanggal_pencairan }}"
                                        data-jenis-pembiayaan="{{ $item->jenis_pembiayaan }}"
                                        data-invoices="{{ json_encode($item->invoices_json) }}"
                                        data-tenor-pembayaran="{{ $item->tenor_pembayaran_value ?? 0 }}"
                                        data-yang-harus-dibayarkan="{{ $item->yang_harus_dibayarkan_value ?? 0 }}"
                                        data-tanggal-pencairan-real="{{ $item->tanggal_pencairan_real ?? $item->harapan_tanggal_pencairan }}">
                                        {{ $item->nomor_peminjaman }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card border-1 shadow-none mb-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="total_pinjaman">Total Pinjaman</label>
                                    <input type="text" class="form-control" id="total_pinjaman" name="total_pinjaman"
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="total_bagi_hasil">Total Bagi Hasil</label>
                                    <input type="text" class="form-control" id="total_bagi_hasil" name="total_bagi_hasil"
                                        readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="tanggal_pencairan">Tanggal Pencairan</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="tanggal_pencairan"
                                            name="tanggal_pencairan" readonly>
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="lama_pemakaian">Lama Pemakaian</label>
                                    <input type="text" class="form-control" id="lama_pemakaian" name="lama_pemakaian"
                                        readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label for="invoice" id="labelInvoice">Invoice Yang Akan Dibayar</label>
                                    <select name="invoice" id="invoice" class="form-control select2"
                                        data-placeholder="Pilih Invoice">
                                        <option value="">Pilih Invoice</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="nominal_invoice" id="labelNominalInvoice">Nominal Invoice</label>
                                    <input type="text" class="form-control" id="nominal_invoice" name="nominal_invoice"
                                        readonly>
                                </div>
                            </div>

                            <!-- Khusus Installment -->
                            <div class="row mb-3" id="installmentFields" style="display: none;">
                                <div class="col-md-6 mb-2">
                                    <label for="bulan_pembayaran">Bulan Pembayaran</label>
                                    <select name="bulan_pembayaran" id="bulan_pembayaran" class="form-control select2"
                                        data-placeholder="Pilih Bulan">
                                        <option value="">Pilih Bulan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="yang_harus_dibayarkan">Yang Harus Dibayar Bulan Ini</label>
                                    <input type="text" class="form-control" id="yang_harus_dibayarkan" name="yang_harus_dibayarkan" readonly>
                                </div>
                            </div>

                            @include('livewire.pengembalian-pinjaman.partials._pengembalian-table')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sisa_utang" class="form-label">Sisa Bayar Pokok</label>
                            <input type="text" class="form-control" id="sisa_utang" name="sisa_utang" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sisa_bagi_hasil" class="form-label">Sisa Bagi Hasil</label>
                            <input type="text" class="form-control" id="sisa_bagi_hasil" name="sisa_bagi_hasil"
                                readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg">
                            <label for="catatan">Catatan Lainnya</label>
                            <textarea name="catatan" id="catatan" class="form-control" placeholder="Masukkan Catatan"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            Simpan Data
                            <i class="ti ti-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('livewire.pengembalian-pinjaman.partials._modal-tambah-pengembalian-invoice')
@endsection

@push('scripts')
    <script>
        const state = {
            pengembalianData: [],
            editingIndex: null,
            totalPinjaman: 0,
            totalBagiHasil: 0,
            lamaPemakaianHari: 0,
            nominalInvoiceTerpilih: 0,
            nomorInvoiceTerpilih: '',
            currentJenisPembiayaan: '',
            tenorPembayaran: 0,
            yangHarusDibayarkanPerBulan: 0,
            tanggalPencairanReal: ''
        };

        $(document).ready(function() {
            initCleaveRupiah();
            
            $('#kode_peminjaman').on('change', handlePeminjamanChange);
            $('#invoice').on('change', handleInvoiceChange);
            $('#bulan_pembayaran').on('change', handleBulanPembayaranChange);
            $('#btnTambahPengembalian').on('click', openModal);
            $('#btnSimpanPengembalianInvoice').on('click', savePengembalian);
            $('#formPengembalian').on('submit', handleFormSubmit);
            $(document).on('click', '.btn-edit-pengembalian', handleEdit);
            $(document).on('click', '.btn-remove-pengembalian', handleDelete);
        });

        function handlePeminjamanChange() {
            const selected = $(this).find(':selected');
            if (!selected.val()) return resetForm();

            const data = {
                totalPinjaman: parseFloat(selected.data('total-pinjaman')),
                totalBagiHasil: parseFloat(selected.data('total-bagi-hasil')),
                tanggalPencairan: selected.data('tanggal-pencairan'),
                jenisPembiayaan: selected.data('jenis-pembiayaan'),
                invoices: selected.data('invoices'),
                tenorPembayaran: parseInt(selected.data('tenor-pembayaran')) || 0,
                yangHarusDibayarkan: parseFloat(selected.data('yang-harus-dibayarkan')) || 0,
                tanggalPencairanReal: selected.data('tanggal-pencairan-real') || selected.data('tanggal-pencairan')
            };

            Object.assign(state, {
                totalPinjaman: data.totalPinjaman,
                totalBagiHasil: data.totalBagiHasil,
                currentJenisPembiayaan: data.jenisPembiayaan,
                tenorPembayaran: data.tenorPembayaran,
                yangHarusDibayarkanPerBulan: data.yangHarusDibayarkan,
                tanggalPencairanReal: data.tanggalPencairanReal
            });

            fillFormData(data);
            updateLabels(data.jenisPembiayaan);
            toggleInstallmentFields(data.jenisPembiayaan);
            if (data.jenisPembiayaan === 'Installment') populateBulanPembayaran(data.tenorPembayaran);
            calculateSisa();
        }

        function updateLabels(jenisPembiayaan) {
            const labels = {
                'Invoice Financing': { invoice: 'Invoice Yang Akan Dibayar', nominal: 'Nominal Invoice' },
                'PO Financing': { invoice: 'Kontrak Yang Akan Dibayar', nominal: 'Nominal Kontrak' },
                'Factoring': { invoice: 'Kontrak Yang Akan Dibayar', nominal: 'Nominal Kontrak' },
                'Installment': { invoice: 'Invoice (Referensi)', nominal: 'Nominal Invoice' }
            };
            
            const label = labels[jenisPembiayaan] || { invoice: 'Yang Akan Dibayar', nominal: 'Nominal' };
            $('#labelInvoice').text(label.invoice);
            $('#labelNominalInvoice').text(label.nominal);
        }
        
        function toggleInstallmentFields(jenisPembiayaan) {
            const isInstallment = jenisPembiayaan === 'Installment';
            $('#installmentFields').toggle(isInstallment);
            
            if (isInstallment) {
                $('#invoice').closest('.row').find('.col-md-6').first().find('label')
                    .append(' <small class="text-muted">(Opsional)</small>');
            } else {
                $('#bulan_pembayaran, #yang_harus_dibayarkan').val('');
            }
        }
        
        function populateBulanPembayaran(tenor) {
            const $select = $('#bulan_pembayaran');
            $select.empty().append('<option value="">Pilih Bulan</option>');
            
            if (tenor > 0) {
                const bulanDibayar = [];
                for (let i = 1; i <= tenor; i++) {
                    const bulanLabel = `Bulan ke-${i}`;
                    if (!bulanDibayar.includes(bulanLabel)) {
                        $select.append(`<option value="${bulanLabel}">${bulanLabel}</option>`);
                    }
                }
            }
            
            initSelect2($select[0]);
        }
        
        function handleBulanPembayaranChange() {
            const bulanLabel = $(this).val();
            if (!bulanLabel) {
                $('#yang_harus_dibayarkan').val('');
                Object.assign(state, { nominalInvoiceTerpilih: 0, nomorInvoiceTerpilih: '' });
                return;
            }
            
            const bulanKe = parseInt(bulanLabel.replace('Bulan ke-', ''));
            let nominalBulanIni = state.yangHarusDibayarkanPerBulan;
            if (bulanKe === 1) nominalBulanIni += state.totalBagiHasil;
            
            nominalBulanIni = Math.round(nominalBulanIni);
            $('#yang_harus_dibayarkan').val(formatRupiah(nominalBulanIni));
            Object.assign(state, { nominalInvoiceTerpilih: nominalBulanIni, nomorInvoiceTerpilih: bulanLabel });
        }

        function fillFormData(data) {
            $('#total_pinjaman').val(formatRupiah(data.totalPinjaman));
            $('#total_bagi_hasil').val(formatRupiah(data.totalBagiHasil));
            
            // Untuk Installment, gunakan tanggal_pencairan_real dari history
            const tanggalPencairan = data.jenisPembiayaan === 'Installment' ? data.tanggalPencairanReal : data.tanggalPencairan;
            $('#tanggal_pencairan').val(formatDate(tanggalPencairan));
            
            // Untuk Installment, lama pemakaian = tenor_pembayaran (dalam bulan), bukan hitung dari tanggal
            if (data.jenisPembiayaan === 'Installment') {
                $('#lama_pemakaian').val(data.tenorPembayaran + ' Bulan');
                lamaPemakaianHari = data.tenorPembayaran * 30; // Konversi bulan ke hari (approx)
            } else {
                $('#lama_pemakaian').val(calculateDuration(tanggalPencairan));
            }

            populateInvoiceSelect(data.invoices);
        }

        function populateInvoiceSelect(invoices) {
            const $select = $('#invoice');
            const isKontrak = ['PO Financing', 'Factoring'].includes(state.currentJenisPembiayaan);
            const placeholder = isKontrak ? 'Pilih Kontrak' : 'Pilih Invoice';
            
            $select.empty().append(`<option value="">${placeholder}</option>`);

            if (invoices?.length > 0) {
                invoices.forEach(invoice => {
                    $select.append(`<option value="${invoice.id}" 
                        data-nilai-invoice="${invoice.nilai}"
                        data-nilai-asli="${invoice.nilai_asli || invoice.nilai}"
                        data-sudah-dibayar="${invoice.sudah_dibayar || 0}">
                        ${invoice.label}
                    </option>`);
                });
            } else {
                const jenisLabel = isKontrak ? 'kontrak' : 'invoice';
                $select.append(`<option value="" disabled>Semua ${jenisLabel} sudah lunas</option>`);
            }

            initSelect2($select[0]);
        }

        function handleInvoiceChange() {
            const selected = $(this).find(':selected');
            const nilai = parseFloat(selected.data('nilai-invoice')) || 0;

            Object.assign(state, { 
                nominalInvoiceTerpilih: nilai, 
                nomorInvoiceTerpilih: selected.text().trim() 
            });

            $('#nominal_invoice').val(nilai > 0 ? formatRupiah(nilai) : '');
        }

        function openModal() {
            state.editingIndex = null;
            $('#modalTitle').text('Tambah Pengembalian Invoice');
            $('#nominal_yang_dibayarkan, #bukti_pembayaran').val('');
            $('#currentFileInfo').hide();
            setTimeout(initCleaveRupiah, 100);
            $('#modalPengembalian').modal('show');
        }

        function savePengembalian() {
            const nominalInput = $('#nominal_yang_dibayarkan').val();
            const fileInput = $('#bukti_pembayaran')[0];

            if (!nominalInput || nominalInput.trim() === '' || nominalInput === 'Rp 0') {
                return showSweetAlert({ icon: 'warning', title: 'Perhatian', text: 'Nominal yang dibayarkan harus diisi' });
            }

            if (!fileInput.files[0] && state.editingIndex === null) {
                return showSweetAlert({ icon: 'warning', title: 'Perhatian', text: 'Bukti pembayaran harus diupload' });
            }

            const nominal = parseFloat(nominalInput.replace(/[^0-9]/g, ''));

            if (nominal <= 0 || isNaN(nominal)) {
                return showSweetAlert({ icon: 'warning', title: 'Perhatian', text: 'Nominal yang dibayarkan tidak valid' });
            }

            if (nominal > state.nominalInvoiceTerpilih) {
                return showSweetAlert({
                    icon: 'error',
                    title: 'Nominal Melebihi Sisa',
                    text: `Pembayaran tidak boleh lebih dari sisa nominal ${formatRupiah(state.nominalInvoiceTerpilih)}`
                });
            }
            
            if (state.currentJenisPembiayaan === 'Installment' && Math.round(nominal) !== Math.round(state.nominalInvoiceTerpilih)) {
                return showSweetAlert({
                    icon: 'error',
                    title: 'Nominal Tidak Sesuai',
                    text: `Untuk pembayaran Installment, Anda harus membayar TEPAT ${formatRupiah(state.nominalInvoiceTerpilih)}`
                });
            }

            const file = fileInput.files[0];
            const data = {
                nominal,
                fileName: file?.name || state.pengembalianData[state.editingIndex]?.fileName || '',
                file: file || state.pengembalianData[state.editingIndex]?.file || null
            };

            if (state.editingIndex !== null) {
                state.pengembalianData[state.editingIndex] = data;
            } else {
                state.pengembalianData.push(data);
            }

            renderTable();
            calculateSisa();
            $('#modalPengembalian').modal('hide');
        }

        function renderTable() {
            const tbody = $('#pengembalianTableBody');
            tbody.empty();

            if (state.pengembalianData.length === 0) {
                return tbody.append('<tr><td colspan="4" class="text-center text-muted">Belum ada data pengembalian</td></tr>');
            }

            state.pengembalianData.forEach((item, index) => {
                tbody.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${formatRupiah(item.nominal)}</td>
                        <td>${item.fileName}</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary btn-edit-pengembalian" data-idx="${index}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-danger btn-remove-pengembalian" data-idx="${index}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                `);
            });
        }

        function handleEdit(e) {
            e.preventDefault();
            state.editingIndex = $(this).data('idx');
            const data = state.pengembalianData[state.editingIndex];

            $('#modalTitle').text('Edit Pengembalian Invoice');
            $('#nominal_yang_dibayarkan').val(data.nominal);
            $('#bukti_pembayaran').val('');
            
            if (data.fileName) {
                $('#currentFileName').text(data.fileName);
                $('#currentFileInfo').show();
            } else {
                $('#currentFileInfo').hide();
            }

            setTimeout(() => {
                if (window.cleaveNominal) window.cleaveNominal.destroy();
                initCleaveRupiah();
            }, 100);

            $('#modalPengembalian').modal('show');
        }

        function handleDelete(e) {
            e.preventDefault();
            sweetAlertConfirm({
                title: 'Hapus Data?',
                text: 'Yakin ingin menghapus data ini?'
            }, () => {
                state.pengembalianData.splice($(e.target).closest('a').data('idx'), 1);
                renderTable();
                calculateSisa();
            });
        }

        function calculateSisa() {
            const totalDibayar = state.pengembalianData.reduce((sum, item) => sum + item.nominal, 0);
            let sisaBagiHasil = state.totalBagiHasil;
            let sisaBayarPokok = state.totalPinjaman;

            if (totalDibayar >= state.totalBagiHasil) {
                sisaBagiHasil = 0;
                sisaBayarPokok = state.totalPinjaman - (totalDibayar - state.totalBagiHasil);
            } else {
                sisaBagiHasil = state.totalBagiHasil - totalDibayar;
            }

            sisaBayarPokok = Math.max(0, sisaBayarPokok);
            $('#sisa_bagi_hasil').val(formatRupiah(sisaBagiHasil));
            $('#sisa_utang').val(formatRupiah(sisaBayarPokok));
        }

        function calculateDuration(startDate) {
            const start = new Date(startDate);
            const now = new Date();
            const diffTime = Math.abs(now - start);
            state.lamaPemakaianHari = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            let years = now.getFullYear() - start.getFullYear();
            let months = now.getMonth() - start.getMonth();
            let days = now.getDate() - start.getDate();

            if (days < 0) {
                months--;
                days += new Date(now.getFullYear(), now.getMonth(), 0).getDate();
            }
            if (months < 0) {
                years--;
                months += 12;
            }

            const parts = [];
            if (years > 0) parts.push(`${years} Tahun`);
            if (months > 0) parts.push(`${months} Bulan`);
            if (days > 0) parts.push(`${days} Hari`);

            return parts.length > 0 ? parts.join(' ') : '0 Hari';
        }

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function resetForm() {
            $('#total_pinjaman, #total_bagi_hasil, #tanggal_pencairan, #lama_pemakaian, #nominal_invoice, #bulan_pembayaran, #yang_harus_dibayarkan, #sisa_utang, #sisa_bagi_hasil').val('');
            $('#invoice').empty().append('<option value="">Pilih Invoice</option>');
            $('#labelInvoice').text('Invoice Yang Akan Dibayar');
            $('#labelNominalInvoice').text('Nominal Invoice');
            $('#installmentFields').hide();

            Object.assign(state, {
                pengembalianData: [],
                totalPinjaman: 0,
                totalBagiHasil: 0,
                lamaPemakaianHari: 0,
                nominalInvoiceTerpilih: 0,
                nomorInvoiceTerpilih: '',
                currentJenisPembiayaan: '',
                tenorPembayaran: 0,
                yangHarusDibayarkanPerBulan: 0,
                tanggalPencairanReal: ''
            });

            renderTable();
        }

        function handleFormSubmit(e) {
            e.preventDefault();

            const kodePeminjaman = $('#kode_peminjaman').val();
            if (!kodePeminjaman) {
                return showSweetAlert({ icon: 'warning', title: 'Perhatian', text: 'Kode peminjaman harus dipilih' });
            }

            if (state.pengembalianData.length === 0) {
                return showSweetAlert({ icon: 'warning', title: 'Perhatian', text: 'Data pengembalian invoice harus diisi minimal 1 item' });
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('kode_peminjaman', kodePeminjaman);
            formData.append('nama_perusahaan', $('#nama_perusahaan').val());
            formData.append('total_pinjaman', state.totalPinjaman);
            formData.append('total_bagi_hasil', state.totalBagiHasil);
            formData.append('tanggal_pencairan', $('#tanggal_pencairan').val());
            formData.append('lama_pemakaian', state.lamaPemakaianHari);
            formData.append('nominal_invoice', state.nominalInvoiceTerpilih);
            formData.append('invoice_dibayarkan', state.nomorInvoiceTerpilih);
            formData.append('sisa_utang', $('#sisa_utang').val().replace(/[^0-9]/g, ''));
            formData.append('sisa_bagi_hasil', $('#sisa_bagi_hasil').val().replace(/[^0-9]/g, ''));
            formData.append('catatan', $('#catatan').val() || '');
            
            if (state.currentJenisPembiayaan === 'Installment') {
                formData.append('bulan_pembayaran', $('#bulan_pembayaran').val());
                formData.append('yang_harus_dibayarkan', state.yangHarusDibayarkanPerBulan);
            }

            state.pengembalianData.forEach((item, index) => {
                formData.append(`pengembalian_invoices[${index}][nominal]`, item.nominal);
                if (item.file) formData.append(`pengembalian_invoices[${index}][file]`, item.file);
            });

            $.ajax({
                url: '{{ route('pengembalian.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: () => {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                },
                success: (response) => {
                    if (response.error === false || response.success === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Data berhasil disimpan',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            if (typeof Livewire !== 'undefined') {
                                Livewire.dispatch('refreshPengembalianPeminjamanTable');
                            }
                            window.location.href = response.data?.redirect || response.redirect || '{{ route('pengembalian.index') }}';
                        });
                    } else {
                        showSweetAlert({ icon: 'warning', title: 'Perhatian', text: response.message || 'Terjadi kesalahan' });
                    }
                },
                error: (xhr) => {
                    Swal.close();
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';

                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        errorMessage = '<ul class="text-start">' + 
                            Object.values(xhr.responseJSON.errors).map(err => `<li>${err[0]}</li>`).join('') + 
                            '</ul>';
                    } else if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 0) {
                        errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                    }

                    showSweetAlert({ icon: 'error', title: 'Error', text: errorMessage });
                }
            });

            return false;
        }

        function initCleaveRupiah() {
            if (window.cleaveNominal) window.cleaveNominal.destroy();
            window.cleaveNominal = new Cleave('#nominal_yang_dibayarkan', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalScale: 0,
                prefix: 'Rp ',
                rawValueTrimPrefix: true,
                noImmediatePrefix: false
            });
        }
    </script>
@endpush
