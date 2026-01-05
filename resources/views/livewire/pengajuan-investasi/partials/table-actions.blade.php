<div class="d-flex justify-content-center align-items-center gap-2">
    <button class="btn btn-sm btn-outline-primary investor-detail-btn" type="button"
        data-id="{{ $id }}" title="Detail">
        <i class="ti ti-file"></i>
    </button>

    @can('investasi.edit')
        @if ($status === 'Draft' || ($status === 'Ditolak' && $current_step == 1))
            <button class="btn btn-sm btn-outline-warning investor-edit-btn" type="button"
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

    @if ($status === 'Selesai')
        <a href="{{ route('pengajuan-investasi.download-sertifikat', $id) }}"
            class="btn btn-sm btn-outline-success" target="_blank"
            title="Download Sertifikat">
            <i class="ti ti-certificate"></i>
        </a>
    @endif
</div>
