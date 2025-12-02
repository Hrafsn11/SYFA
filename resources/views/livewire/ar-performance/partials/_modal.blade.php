{{-- Bootstrap Modal for Transaction Details --}}
<div class="modal fade" id="modalDetailTransaksi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="8%">No</th>
                                <th class="text-center">No Kontrak</th>
                                <th class="text-center">No Invoice</th>
                                <th class="text-end">Nilai Pembayaran</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Tgl Bayar</th>
                                <th class="text-center">Keterlambatan</th>
                            </tr>
                        </thead>
                        <tbody id="tableDetailTransaksi">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="spinner-border spinner-border-sm me-2"></div>
                                    Memuat data..
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    'use strict';

    $(document).ready(function() {
        const $modal = $('#modalDetailTransaksi');
        const $tbody = $('#tableDetailTransaksi');
        
        // Initialize Select2 for Filters
        function initSelect2() {
            // Destroy existing instances first
            if ($('#filterBulan').hasClass('select2-hidden-accessible')) {
                $('#filterBulan').select2('destroy');
            }
            if ($('#filterTahun').hasClass('select2-hidden-accessible')) {
                $('#filterTahun').select2('destroy');
            }

            // Initialize filterBulan with fixed width (tidak auto-resize)
            $('#filterBulan').select2({
                placeholder: 'Pilih Bulan',
                minimumResultsForSearch: Infinity,
                width: 'resolve', // Use the width of the original element
                allowClear: true,
                dropdownAutoWidth: false
            });

            // Initialize filterTahun with fixed width
            $('#filterTahun').select2({
                placeholder: 'Pilih Tahun',
                minimumResultsForSearch: Infinity,
                width: 'resolve',
                dropdownAutoWidth: false
            });
            
            // Force set width setelah initialize untuk memastikan konsisten
            setTimeout(function() {
                $('#filterBulan').next('.select2-container').css({
                    'width': '180px',
                    'min-width': '180px',
                    'max-width': '180px'
                });
                $('#filterTahun').next('.select2-container').css({
                    'width': '150px',
                    'min-width': '150px',
                    'max-width': '150px'
                });
            }, 10);
        }

        // Init on page load
        initSelect2();
        updateExportLink();

        // Update export PDF link when filters change
        function updateExportLink() {
            const tahun = @this.tahun;
            const bulan = @this.bulan || '';
            let url = "{{ route('ar-performance.export-pdf') }}";
            url += '?tahun=' + tahun;
            if (bulan) {
                url += '&bulan=' + bulan;
            }
            $('#btnExportPDF').attr('href', url);
        }

        $('#filterBulan').on('change', function(e) {
            const bulan = $(this).val();
            @this.set('bulan', bulan || null);
            setTimeout(updateExportLink, 100);
        });

        $('#filterTahun').on('change', function(e) {
            const tahun = $(this).val();
            @this.set('tahun', tahun);
            setTimeout(updateExportLink, 100);
        });

        let isSelectChanging = false;
        
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                if (!isSelectChanging) {
                    if ($('#filterBulan').hasClass('select2-hidden-accessible')) {
                        $('#filterBulan').select2('destroy');
                    }
                    if ($('#filterTahun').hasClass('select2-hidden-accessible')) {
                        $('#filterTahun').select2('destroy');
                    }
                    initSelect2();
                    
                    $('#filterBulan').val(@this.bulan || '').trigger('change.select2');
                    $('#filterTahun').val(@this.tahun).trigger('change.select2');
                    
                    // Update export link after Livewire update
                    updateExportLink();
                }
            });
        });

        $('#filterBulan, #filterTahun').on('select2:selecting', function() {
            isSelectChanging = true;
        });
        
        $('#filterBulan, #filterTahun').on('select2:select', function() {
            setTimeout(() => { isSelectChanging = false; }, 100);
        });

        $(document).on('click', '.view-transactions', function(e) {
            e.preventDefault();

            const debiturId = $(this).data('debitur-id');
            const debiturName = $(this).data('debitur-name');
            const category = $(this).data('category');

            const categoryLabels = {
                'belum_jatuh_tempo': 'Belum Jatuh Tempo',
                'del_1_30': 'DEL (1-30)',
                'del_31_60': 'DEL (31-60)',
                'del_61_90': 'DEL (61-90)',
                'npl_91_179': 'NPL (91-179)',
                'writeoff_180': 'WriteOff (>180)'
            };

            $('#modalTitle').text(`${debiturName} - ${categoryLabels[category]}`);

            $tbody.html(`
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="spinner-border spinner-border-sm me-2"></div>
                        Memuat data...
                    </td>
                </tr>
            `);

            $modal.modal('show');

            const tahun = @this.tahun;
            const bulan = @this.bulan;

            $.ajax({
                url: "{{ route('ar-performance.transactions') }}",
                method: 'GET',
                data: {
                    debitur_id: debiturId,
                    category: category,
                    tahun: tahun,
                    bulan: bulan
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '';
                        response.data.forEach((item, index) => {
                            const dueDate = formatDate(item.due_date);
                            const paymentDate = formatDate(item.tanggal_pembayaran);
                            const daysLate = item.hari_keterlambatan;

                            const keterlambatan = daysLate === 0 
                                ? '<span class="badge bg-success">On-time</span>' 
                                : `<span class="badge bg-danger">${daysLate} hari</span>`;

                            html += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td class="text-center">${item.nomor_kontrak || '-'}</td>
                                    <td class="text-center">${item.nomor_invoice || '-'}</td>
                                    <td class="text-end">Rp ${formatRupiah(item.nilai)}</td>
                                    <td class="text-center">${dueDate}</td>
                                    <td class="text-center">${paymentDate}</td>
                                    <td class="text-center">${keterlambatan}</td>
                                </tr>
                            `;
                        });
                        $tbody.html(html);
                    } else {
                        $tbody.html(`
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    Tidak ada data transaksi
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading transactions:', xhr);
                    $tbody.html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger py-3">
                                Gagal memuat data transaksi
                            </td>
                        </tr>
                    `);
                }
            });
        });

        // Helper functions
        function formatRupiah(angka) {
            if (!angka) return '0';
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('show-alert', (event) => {
                const data = event[0] || event;
                const type = data.type || 'success';
                const message = data.message || 'Success!';
                
                Swal.fire({
                    icon: type,
                    title: type === 'success' ? 'Berhasil!' : 'Perhatian!',
                    text: message,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: `btn btn-${type}`
                    }
                });
            });
        });
    });
</script>
@endpush
