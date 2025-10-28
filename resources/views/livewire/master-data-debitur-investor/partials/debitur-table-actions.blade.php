<div class="d-flex justify-content-center align-items-center gap-2">
    {{-- Button Edit --}}
    <button class="btn btn-sm btn-icon btn-text-primary rounded-pill waves-effect debitur-edit-btn" 
            type="button"
            data-id="{{ $id }}" 
            title="Edit">
        <i class="ti ti-edit"></i>
    </button>

    <a href="{{ route('master-data.debitur-investor.history-kol', $id) }}" 
       class="btn btn-sm btn-icon btn-text-info rounded-pill waves-effect" 
       title="Lihat History KOL">
        <i class="ti ti-history"></i>
    </a>

    {{-- Button Toggle Status --}}
    @if($status === 'active')
        <button class="btn btn-sm btn-icon btn-text-danger rounded-pill waves-effect debitur-toggle-status-btn" 
                type="button"
                data-id="{{ $id }}"
                data-status="{{ $status }}"
                title="Nonaktifkan">
            <i class="ti ti-circle-x"></i>
        </button>
    @else
        <button class="btn btn-sm btn-icon btn-text-success rounded-pill waves-effect debitur-toggle-status-btn" 
                type="button"
                data-id="{{ $id }}"
                data-status="{{ $status }}"
                title="Aktifkan">
            <i class="ti ti-circle-check"></i>
        </button>
    @endif
</div>
