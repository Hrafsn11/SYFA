<div class="d-flex gap-2 justify-content-center">
    @php
        // Check if this is a rejection status that requires resubmission
        $isRejectedAtStep1 = $current_step == 1 && str_contains($status, 'Ditolak');
        $canEdit = $status === 'Draft' || $isRejectedAtStep1;
    @endphp

    @can('pengajuan_investasi_finlog.view')
        <a href="{{ route('sfinlog.pengajuan-investasi.detail', ['id' => $id]) }}"
            class="btn btn-sm btn-icon btn-text-info rounded-pill waves-effect investor-detail-btn" title="Lihat Detail">
            <i class="ti ti-file-text"></i>
        </a>
    @endcan

    @can('pengajuan_investasi_finlog.edit')
        @if ($canEdit)
            <button class="btn btn-sm btn-icon btn-text-warning rounded-pill waves-effect investor-edit-btn" type="button"
                data-id="{{ $id }}" title="Edit">
                <i class="ti ti-edit"></i>
            </button>
        @else
            <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect" type="button" disabled
                title="Tidak dapat edit (status: {{ $status }})">
                <i class="ti ti-edit"></i>
            </button>
        @endif
    @endcan

    {{-- Re-Submit Button - only shows when rejected and at step 1 --}}
    @if ($isRejectedAtStep1)
        <button class="btn btn-sm btn-icon btn-text-success rounded-pill waves-effect investor-resubmit-btn"
            type="button" data-id="{{ $id }}" title="Re-Submit Pengajuan">
            <i class="ti ti-send"></i>
        </button>
    @endif

    @if ($status === 'Selesai')
        <a href="{{ route('sfinlog.pengajuan-investasi.download-sertifikat', ['id' => $id]) }}"
            class="btn btn-sm btn-icon btn-text-success rounded-pill waves-effect" target="_blank"
            title="Download Sertifikat">
            <i class="ti ti-certificate"></i>
        </a>
    @endif
</div>
