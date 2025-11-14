@php
    $isActive = $is_active === 'active';
@endphp

<div class="d-flex justify-content-center gap-1" id="action-row-{{ $id }}">
    <a href="{{ route('peminjaman.detail', $id) }}" 
       class="btn btn-sm btn-outline-primary action-btn" 
       title="Lihat Detail"
       data-action="detail"
       @if(!$isActive) style="pointer-events: none; opacity: 0.5;" @endif>
        <i class="ti ti-file-text"></i>
    </a>
    
    @can('peminjaman_dana.edit')
        <a href="{{ route('peminjaman.edit', $id) }}" 
        class="btn btn-sm btn-outline-warning action-btn edit-btn" 
        title="Edit"
        data-action="edit"
        data-status="{{ $status }}"
        @if(!$isActive) style="pointer-events: none; opacity: 0.5;" @endif>
            <i class="fas fa-edit"></i>
        </a> 
    @endcan

    @can('peminjaman_dana.active/non_active')
        <button class="btn btn-sm btn-icon {{ $isActive ? 'btn-text-danger' : 'btn-text-success' }} rounded-pill waves-effect pengajuan-toggle-status-btn" 
                type="button"
                data-id="{{ $id }}"
                data-active="{{ $isActive ? 'true' : 'false' }}"
                title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}">
            <i class="{{ $isActive ? 'ti ti-circle-x' : 'ti ti-circle-check' }}"></i>
        </button>
    @endcan
    
    
    {{-- <button type="button" 
            class="btn btn-sm btn-outline-danger" 
            title="Hapus"
            onclick="confirmDelete({{ $id }})">
        <i class="fas fa-trash"></i>
    </button> --}}
    
    <!-- <a href="#" 
       class="btn btn-sm btn-outline-info" 
       title="Preview Kontrak">
        <i class="fas fa-file-contract"></i>
    </a> -->
</div>