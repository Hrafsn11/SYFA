<div class="d-flex justify-content-center gap-1" id="action-row-{{ $id }}">
    <a href="{{ route('peminjaman.detail', $id) }}" 
       wire:navigate
       class="btn btn-sm btn-outline-primary action-btn" 
       title="Lihat Detail" 
       data-action="detail">
        <i class="ti ti-file-text"></i>
    </a>
    
    @can('peminjaman_dana.edit')
        {{-- Menggunakan route Livewire untuk edit --}}
        <a href="{{ route('peminjaman.edit', $id) }}" 
        wire:navigate.hover
        class="btn btn-sm btn-outline-warning action-btn edit-btn" 
        title="Edit"
        data-action="edit"
        data-status="{{ $status }}">
            <i class="fas fa-edit"></i>
        </a> 
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