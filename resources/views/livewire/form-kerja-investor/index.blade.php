@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Kerja Investor</h4>
                    <button type="button" class="btn btn-primary d-flex justify-content-center align-items-center gap-3"
                        id="btnTambahFormKerjaInvestor">
                        <i class="fa-solid fa-plus"></i>
                        Pengajuan Investor
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-history-kol table table-bordered" id="tableHistoryKol">
                    <thead>
                        <tr class="">
                            <th class="text-center">No</th>
                            <th>Nama Investor</th>
                            <th class="text-center">Jenis Deposito</th>
                            <th class="text-center">Tanggal Investasi</th>
                            <th class="text-center">Lama Investasi</th>
                            <th class="text-center">Jumlah Investasi</th>
                            <th class="text-center">Bagi Hasil</th>
                            <th class="text-center">Nominal Bagi Hasil Keseluruhan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formKerjaInvestor as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $item->nama_investor }}</td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $item->deposito === 'reguler' ? 'primary' : 'info' }}">
                                        {{ ucfirst($item->deposito) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{ $item->tanggal_pembayaran ? \Carbon\Carbon::parse($item->tanggal_pembayaran)->format('d F Y') : '-' }}
                                </td>
                                <td class="text-center">{{ $item->lama_investasi ?? '-' }} Bulan</td>
                                <td class="text-center">Rp {{ number_format($item->jumlah_investasi, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->bagi_hasil }}%</td>
                                <td class="text-center">Rp {{ number_format($item->bagi_hasil_keseluruhan, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($item->status === 'pending')
                                        <span class="badge bg-label-warning">Pending</span>
                                    @elseif($item->status === 'approved')
                                        <span class="badge bg-label-success">Disetujui</span>
                                    @elseif($item->status === 'rejected')
                                        <span class="badge bg-label-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-label-info">Selesai</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <a href="{{ route('form-kerja-investor.show', $item->id_form_kerja_investor) }}" 
                                           class="btn btn-sm btn-icon btn-text-info" title="Detail">
                                            <i class="ti ti-file-text"></i>
                                        </a>
                                        <button class="btn btn-sm btn-icon btn-text-danger btnHapus" 
                                                data-id="{{ $item->id_form_kerja_investor }}" title="Hapus">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="ti ti-inbox display-6 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada data pengajuan investasi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal modal-lg fade" id="modalFormKerjaInvestor" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahFormKerjaInvestorLabel">Tambah Form Kerja Investor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form id="formTambahFormKerjaInvestor" novalidate>
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label for="nama_investor" class="form-label">Nama Investor <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control non-editable" id="nama_investor"
                                        name="nama_investor" placeholder="Nama Investor" required disabled readonly>
                                </div>
                                <div class="col-12 mb-3" id="div-deposito">
                                    <label class="form-label">Deposito <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deposito"
                                                id="deposito_reguler" value="reguler" required disabled>
                                            <label class="form-check-label" for="deposito_reguler">
                                                Reguler
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="deposito"
                                                id="deposito_khusus" value="khusus" required disabled>
                                            <label class="form-check-label" for="deposito_khusus">
                                                Khusus
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="flatpickr-tanggal-pembayaran" class="form-label">Tanggal Investasi</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control rounded-start flatpickr-date"
                                            placeholder="DD/MM/YYYY" id="flatpickr-tanggal-pembayaran"
                                            name="tanggal_pembayaran" />
                                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lama-investasi" class="form-label">Lama Berinvestasi (Bulan)</label>
                                    <input type="number" class="form-control" id="lama_investasi"
                                        placeholder="Masukkan lama berinvestasi" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah_investasi" class="form-label">Jumlah Investasi <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control input-rupiah" id="jumlah_investasi"
                                        name="jumlah_investasi" placeholder="Rp 0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bagi_hasil" class="form-label">Bagi Hasil (%)</label>
                                    <input type="text" class="form-control" id="bagi_hasil"
                                        placeholder="Masukan bagi hasil" required>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label for="bagi_hasil_keseluruhan" class="form-label">Nominal Bagi Hasil Keseluruhan
                                        <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control input-rupiah" id="bagi_hasil_keseluruhan"
                                        placeholder="Rp 0" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hapus Data</button>
                        <button type="button" class="btn btn-primary" id="btnSimpanFormKerjaInvestor">
                            <span class="spinner-border spinner-border-sm me-2 d-none" id="btnSimpanSpinner"></span>
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const $modal = $('#modalFormKerjaInvestor');
            const $form = $('#formTambahFormKerjaInvestor');

            // Initialize Cleave.js untuk format rupiah
            if (typeof window.initCleaveRupiah === 'function') {
                window.initCleaveRupiah();
            }

            // Initialize Flatpickr
            const flatpickrDate = $('#flatpickr-tanggal-pembayaran');
            if (flatpickrDate.length && !flatpickrDate[0]._flatpickr) {
                flatpickrDate.flatpickr({
                    monthSelectorType: 'static',
                    dateFormat: 'Y-m-d', // Format untuk submit ke backend
                    altInput: true,
                    altFormat: 'j F Y', // Format tampilan user-friendly
                    locale: {
                        firstDayOfWeek: 1
                    }
                });
            }

            // Handle Modal Open
            $('#btnTambahFormKerjaInvestor').on('click', function() {
                // Check if investor data exists
                const namaInvestor = '{{ optional($investor)->nama_debitur ?? '' }}';
                const depositoInvestor = '{{ optional($investor)->deposito ?? '' }}';
                
                if (!namaInvestor || !depositoInvestor) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Investor Tidak Ditemukan',
                        html: 'Anda belum terdaftar sebagai investor.<br>Silakan hubungi admin untuk mendaftar sebagai investor.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                $form[0].reset();
                $form.removeClass('was-validated');

                // Set nama investor (disabled)
                $('#nama_investor').val(namaInvestor);

                // Set and check deposito radio (disabled)
                $('input[name="deposito"]').prop('checked', false).prop('disabled', true);
                
                if (depositoInvestor) {
                    const depositoValue = depositoInvestor.toLowerCase();
                    $(`input[name="deposito"][value="${depositoValue}"]`).prop('checked', true);
                    
                    // Trigger deposito logic
                    if (depositoValue === 'reguler') {
                        $('#bagi_hasil').val('10').prop('readonly', true).addClass('non-editable');
                        $('#bagi_hasil_keseluruhan').prop('readonly', true).addClass('non-editable');
                    } else if (depositoValue === 'khusus') {
                        $('#bagi_hasil').val('').prop('readonly', false).removeClass('non-editable');
                        $('#bagi_hasil_keseluruhan').prop('readonly', false).removeClass('non-editable');
                    }
                }

                // Show modal
                $modal.modal('show');

                // Reinitialize Cleave after modal shown
                setTimeout(function() {
                    if (typeof window.initCleaveRupiah === 'function') {
                        window.initCleaveRupiah();
                    }
                }, 100);
            });

            // Handle Deposito Radio Change (disabled by default, but keep for reference)
            $('input[name="deposito"]').on('change', function() {
                const selectedDeposito = $(this).val();

                if (selectedDeposito === 'reguler') {
                    // Deposito Reguler: Bagi Hasil auto 10% (disabled)
                    $('#bagi_hasil').val('10').prop('readonly', true).addClass('non-editable');

                    // Enable auto-calculate for Nominal Bagi Hasil Keseluruhan
                    $('#bagi_hasil_keseluruhan').prop('readonly', true).addClass('non-editable');

                    // Trigger calculation
                    calculateNominalBagiHasil();

                } else if (selectedDeposito === 'khusus') {
                    // Deposito Khusus: Manual input (enabled)
                    $('#bagi_hasil').val('').prop('readonly', false).removeClass('non-editable');
                    $('#bagi_hasil_keseluruhan').prop('readonly', false).removeClass('non-editable');

                    // Clear Nominal Bagi Hasil Keseluruhan
                    window.setCleaveValue(document.getElementById('bagi_hasil_keseluruhan'), 'Rp 0');
                }
            });

            // Handle Jumlah Investasi Input Change (for auto-calculate in Reguler mode)
            $('#jumlah_investasi').on('input', function() {
                const selectedDeposito = $('input[name="deposito"]:checked').val();
                if (selectedDeposito === 'reguler') {
                    calculateNominalBagiHasil();
                }
            });

            // Function to calculate Nominal Bagi Hasil Keseluruhan (Reguler mode only)
            function calculateNominalBagiHasil() {
                const selectedDeposito = $('input[name="deposito"]:checked').val();

                if (selectedDeposito !== 'reguler') return;

                // Get raw value from Cleave.js
                const jumlahInvestasiRaw = window.getCleaveRawValue(document.getElementById('jumlah_investasi')) ||
                    0;
                const bagiHasilPercent = parseFloat($('#bagi_hasil').val()) || 0;

                // Calculate: Jumlah Investasi * (Bagi Hasil / 100)
                const nominalBagiHasil = Math.round(jumlahInvestasiRaw * (bagiHasilPercent / 100));

                // Set calculated value
                window.setCleaveValue(
                    document.getElementById('bagi_hasil_keseluruhan'),
                    'Rp ' + nominalBagiHasil.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                );
            }

            // Handle Save Button
            $('#btnSimpanFormKerjaInvestor').on('click', function() {
                if (!$form[0].checkValidity()) {
                    $form.addClass('was-validated');
                    return;
                }

                // Validate deposito selection
                if (!$('input[name="deposito"]:checked').val()) {
                    alert('Pilih jenis deposito terlebih dahulu');
                    return;
                }

                // Temporarily enable deposito radio for form submission
                $('input[name="deposito"]').prop('disabled', false);

                // Get form data
                const formData = {
                    nama_investor: $('#nama_investor').val(),
                    deposito: $('input[name="deposito"]:checked').val(),
                    tanggal_pembayaran: $('#flatpickr-tanggal-pembayaran').val(),
                    lama_investasi: $('#lama_investasi').val(),
                    jumlah_investasi: window.getCleaveRawValue(document.getElementById(
                        'jumlah_investasi')) || 0,
                    bagi_hasil: $('#bagi_hasil').val(),
                    bagi_hasil_keseluruhan: window.getCleaveRawValue(document.getElementById(
                        'bagi_hasil_keseluruhan')) || 0,
                    _token: '{{ csrf_token() }}'
                };

                // Re-disable deposito radio after getting value
                $('input[name="deposito"]').prop('disabled', true);

                // Show loading
                $('#btnSimpanSpinner').removeClass('d-none');
                $('#btnSimpanFormKerjaInvestor').prop('disabled', true);

                // Debug: Log form data before sending
                console.log('Form Data:', formData);

                // AJAX save
                $.ajax({
                    url: '{{ route('form-kerja-investor.store') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $modal.modal('hide');
                            
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Reload page to show new data
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        // Debug: Log full error response
                        console.error('Error Response:', xhr.responseJSON);
                        
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                        
                        // Show validation errors if available
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('\n');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: errorMessage.replace(/\n/g, '<br>')
                        });
                    },
                    complete: function() {
                        $('#btnSimpanSpinner').addClass('d-none');
                        $('#btnSimpanFormKerjaInvestor').prop('disabled', false);
                    }
                });
            });

            // Handle Delete Button
            $(document).on('click', '.btnHapus', function() {
                const id = $(this).data('id');
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX delete
                        $.ajax({
                            url: `/form-kerja-investor/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
