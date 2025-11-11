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
                <form action="{{ route('pengembalian.store') }}" method="POST" id="formPengembalian" enctype="multipart/form-data">
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
                                            data-invoices="{{ json_encode($item->invoices_json) }}">
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
                                            name="tanggal_pencairan" value="01-01-2024" disabled>
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
                                    <label for="invoice">Invoice Yang Akan Dibayar</label>
                                    <select name="invoice" id="invoice" class="form-control select2"
                                        data-placeholder="Pilih Invoice">
                                        <option value="">Pilih Invoice</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="nominal_invoice">Nominal Invoice</label>
                                    <input type="text" class="form-control" id="nominal_invoice" name="nominal_invoice"
                                    readonly>
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
                            <input type="text" class="form-control" id="sisa_bagi_hasil" name="sisa_bagi_hasil" readonly>
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
        let modalInstance = $('#modalPengembalian');
        let pengembalianData = [];
        let editingIndex = null;
        let totalPinjaman = 0;
        let totalBagiHasil = 0;
        let lamaPemakaianHari = 0;
        let nominalInvoiceTerpilih = 0;
        let nomorInvoiceTerpilih = ''; // Nomor invoice yang dipilih user

        $(document).ready(function() {
            initCleaveRupiah();
            initEventListeners();
        });

        function initEventListeners() {
            $('#kode_peminjaman').on('change', handlePeminjamanChange);
            $('#invoice').on('change', handleInvoiceChange);
            $('#btnTambahPengembalian').on('click', openModal);
            $('#btnSimpanPengembalianInvoice').on('click', savePengembalian);
            $('#modalPengembalian').on('hidden.bs.modal', resetModal);
            $('#formPengembalian').on('submit', handleFormSubmit);
            
            $(document).on('click', '.btn-edit-pengembalian', handleEdit);
            $(document).on('click', '.btn-remove-pengembalian', handleDelete);
            $('#bukti_pembayaran').on('change', handleFileInputChange);
        }

        function handlePeminjamanChange() {
            const selected = $(this).find(':selected');
            
            if (!selected.val()) {
                resetForm();
                return;
            }

            const data = {
                totalPinjaman: parseFloat(selected.data('total-pinjaman')),
                totalBagiHasil: parseFloat(selected.data('total-bagi-hasil')),
                tanggalPencairan: selected.data('tanggal-pencairan'),
                invoices: selected.data('invoices')
            };

            totalPinjaman = data.totalPinjaman;
            totalBagiHasil = data.totalBagiHasil;

            fillFormData(data);
            calculateSisa();
        }

        function fillFormData(data) {
            $('#total_pinjaman').val(formatRupiah(data.totalPinjaman));
            $('#total_bagi_hasil').val(formatRupiah(data.totalBagiHasil));
            $('#tanggal_pencairan').val(formatDate(data.tanggalPencairan));
            $('#lama_pemakaian').val(calculateDuration(data.tanggalPencairan));

            populateInvoiceSelect(data.invoices);
        }

        function populateInvoiceSelect(invoices) {
            const $select = $('#invoice');
            $select.empty().append('<option value="">Pilih Invoice</option>');

            if (invoices && invoices.length > 0) {
                invoices.forEach(invoice => {
                    const val = invoice.id ?? invoice.no_invoice;
                    const nilai = invoice.nilai_invoice ?? 
                                 (invoice.nilai_pinjaman && invoice.nilai_bagi_hasil ? 
                                  (invoice.nilai_pinjaman + invoice.nilai_bagi_hasil) : null);
                    
                    $select.append(`<option value="${val}" data-nilai-invoice="${nilai}">${invoice.no_invoice}</option>`);
                });
            }

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            $select.select2({ placeholder: 'Pilih Invoice' });
        }

        function handleInvoiceChange() {
            const selected = $(this).find(':selected');
            const nilai = parseFloat(selected.data('nilai-invoice')) || 0;
            const noInvoice = selected.text().trim();
            
            nominalInvoiceTerpilih = nilai;
            nomorInvoiceTerpilih = noInvoice; // Simpan nomor invoice
            
            if (nilai > 0) {
                $('#nominal_invoice').val(formatRupiah(nilai));
            } else {
                $('#nominal_invoice').val('');
            }
        }

        function openModal() {
            editingIndex = null;
            $('#modalTitle').text('Tambah Pengembalian Invoice');
            $('#nominal_yang_dibayarkan').val('');
            $('#bukti_pembayaran').val('');
            $('#currentFileInfo').hide();
            
            clearFileInputDisplay();
            
            setTimeout(initCleaveRupiah, 100);
            modalInstance.modal('show');
        }

        function resetModal() {
            editingIndex = null;
            $('#nominal_yang_dibayarkan').val('');
            $('#bukti_pembayaran').val('');
            $('#currentFileInfo').hide();
            
            clearFileInputDisplay();
            
            if (window.cleaveNominal) {
                window.cleaveNominal.destroy();
            }
            setTimeout(initCleaveRupiah, 100);
        }

        function savePengembalian() {
            const nominalInput = $('#nominal_yang_dibayarkan').val();
            const fileInput = $('#bukti_pembayaran')[0];

            if (!nominalInput || nominalInput.trim() === '' || nominalInput === 'Rp 0') {
                alert('Nominal yang dibayarkan harus diisi');
                return;
            }

            if (!fileInput.files[0] && editingIndex === null) {
                alert('Bukti pembayaran harus diupload');
                return;
            }

            const nominal = parseFloat(nominalInput.replace(/[^0-9]/g, ''));
            
            if (nominal <= 0 || isNaN(nominal)) {
                alert('Nominal yang dibayarkan tidak valid');
                return;
            }

            const file = fileInput.files[0];
            const data = {
                nominal: nominal,
                fileName: file ? file.name : (editingIndex !== null ? pengembalianData[editingIndex].fileName : ''),
                file: file ? file : (editingIndex !== null ? pengembalianData[editingIndex].file : null)
            };

            if (editingIndex !== null) {
                pengembalianData[editingIndex] = data;
            } else {
                pengembalianData.push(data);
            }

            renderTable();
            calculateSisa();
            modalInstance.modal('hide');
        }

        function handleFileInputChange() {
            const fileName = $(this).val().split('\\').pop();
            
            if (fileName) {
                $(this).next('.text-muted').text('File dipilih: ' + fileName);
            } else {
                $(this).next('.text-muted').text('Maximum upload file size: 2 MB. (Type File: pdf, png, jpg)');
            }
        }

        function clearFileInputDisplay() {
            $('#bukti_pembayaran').val('');
            $('#bukti_pembayaran').next('.text-muted').text('Maximum upload file size: 2 MB. (Type File: pdf, png, jpg)');
        }

        function renderTable() {
            const tbody = $('#pengembalianTableBody');
            tbody.empty();

            if (pengembalianData.length === 0) {
                tbody.append('<tr id="emptyRow"><td colspan="4" class="text-center text-muted">Belum ada data pengembalian</td></tr>');
                return;
            }

            pengembalianData.forEach((item, index) => {
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
            
            editingIndex = $(this).data('idx');
            const data = pengembalianData[editingIndex];

            $('#modalTitle').text('Edit Pengembalian Invoice');
            $('#bukti_pembayaran').val('');
            
            clearFileInputDisplay();
            
            if (data.fileName) {
                $('#currentFileName').text(data.fileName);
                $('#currentFileInfo').show();
            } else {
                $('#currentFileInfo').hide();
            }
            
            $('#nominal_yang_dibayarkan').val(data.nominal);
            
            setTimeout(function() {
                if (window.cleaveNominal) {
                    window.cleaveNominal.destroy();
                }
                
                window.cleaveNominal = new Cleave('#nominal_yang_dibayarkan', {
                    numeral: true,
                    numeralThousandsGroupStyle: 'thousand',
                    numeralDecimalScale: 0,
                    prefix: 'Rp ',
                    rawValueTrimPrefix: true,
                    noImmediatePrefix: false
                });
            }, 100);
            
            modalInstance.modal('show');
        }

        function handleDelete(e) {
            e.preventDefault();
            
            if (confirm('Yakin ingin menghapus data ini?')) {
                const index = $(this).data('idx');
                pengembalianData.splice(index, 1);
                renderTable();
                calculateSisa();
            }
        }

        function calculateSisa() {
            const totalDibayar = pengembalianData.reduce((sum, item) => sum + item.nominal, 0);
            
            let sisaBagiHasil = totalBagiHasil;
            let sisaBayarPokok = totalPinjaman;

            if (totalDibayar >= totalBagiHasil) {
                sisaBagiHasil = 0;
                const sisaPembayaran = totalDibayar - totalBagiHasil;
                sisaBayarPokok = totalPinjaman - sisaPembayaran;
            } else {
                sisaBagiHasil = totalBagiHasil - totalDibayar;
            }

            sisaBayarPokok = Math.max(0, sisaBayarPokok);

            $('#sisa_bagi_hasil').val(formatRupiah(sisaBagiHasil));
            $('#sisa_utang').val(formatRupiah(sisaBayarPokok));
        }

        function calculateDuration(startDate) {
            const start = new Date(startDate);
            const now = new Date();
            
            // Hitung total hari untuk database
            const diffTime = Math.abs(now - start);
            lamaPemakaianHari = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            let years = now.getFullYear() - start.getFullYear();
            let months = now.getMonth() - start.getMonth();
            let days = now.getDate() - start.getDate();

            if (days < 0) {
                months--;
                const lastMonth = new Date(now.getFullYear(), now.getMonth(), 0);
                days += lastMonth.getDate();
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
            $('#total_pinjaman, #total_bagi_hasil, #tanggal_pencairan, #lama_pemakaian, #nominal_invoice').val('');
            $('#invoice').empty().append('<option value="">Pilih Invoice</option>');
            $('#sisa_utang, #sisa_bagi_hasil').val('');
            
            pengembalianData = [];
            totalPinjaman = 0;
            totalBagiHasil = 0;
            lamaPemakaianHari = 0;
            nominalInvoiceTerpilih = 0;
            nomorInvoiceTerpilih = '';
            
            renderTable();
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            
            const kodePeminjaman = $('#kode_peminjaman').val();

            if (!kodePeminjaman) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Kode peminjaman harus dipilih'
                });
                return false;
            }

            if (pengembalianData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Data pengembalian invoice harus diisi minimal 1 item'
                });
                return false;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('kode_peminjaman', kodePeminjaman);
            formData.append('nama_perusahaan', $('#nama_perusahaan').val());
            formData.append('total_pinjaman', totalPinjaman);
            formData.append('total_bagi_hasil', totalBagiHasil);
            formData.append('tanggal_pencairan', $('#tanggal_pencairan').val());
            formData.append('lama_pemakaian', lamaPemakaianHari);
            formData.append('nominal_invoice', nominalInvoiceTerpilih);
            formData.append('invoice_dibayarkan', nomorInvoiceTerpilih); // Nomor invoice yang dipilih
            formData.append('sisa_utang', $('#sisa_utang').val().replace(/[^0-9]/g, ''));
            formData.append('sisa_bagi_hasil', $('#sisa_bagi_hasil').val().replace(/[^0-9]/g, ''));
            formData.append('catatan', $('#catatan').val() || '');
            
            pengembalianData.forEach((item, index) => {
                formData.append(`pengembalian_invoices[${index}][nominal]`, item.nominal);
                if (item.file) {
                    formData.append(`pengembalian_invoices[${index}][file]`, item.file);
                }
            });

            $.ajax({
                url: '{{ route("pengembalian.store") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        let errorList = '<ul class="text-left">';
                        Object.keys(errors).forEach(key => {
                            errorList += `<li>${errors[key][0]}</li>`;
                        });
                        errorList += '</ul>';
                        errorMessage = errorList;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMessage
                    });
                }
            });

            return false;
        }
    </script>
@endpush
