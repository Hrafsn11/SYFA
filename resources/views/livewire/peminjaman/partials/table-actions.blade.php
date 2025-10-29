<div class="d-flex justify-content-center gap-1">
    <a href="{{ route('peminjaman.detail', $id) }}" 
       class="btn btn-sm btn-outline-primary" 
       title="Lihat Detail">
        <i class="ti ti-file-text"></i>
    </a>
    
    <a href="#" 
       class="btn btn-sm btn-outline-warning" 
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>
    
    <button type="button" 
            class="btn btn-sm btn-outline-danger" 
            title="Hapus"
            onclick="confirmDelete({{ $id }})">
        <i class="fas fa-trash"></i>
    </button>
    
    <!-- <a href="#" 
       class="btn btn-sm btn-outline-info" 
       title="Preview Kontrak">
        <i class="fas fa-file-contract"></i>
    </a> -->
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pengajuan pinjaman ini?')) {
        // Add delete logic here
        console.log('Delete pengajuan pinjaman with ID:', id);
    }
}
</script>