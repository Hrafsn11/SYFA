{{-- Action Buttons - Matching Original Design --}}
@can('peminjaman_dana.pengajuan_peminjaman')
    @if ($currentStep == 1)
        <button type="button" class="btn btn-success" wire:click="submitDokumen"
                wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="submitDokumen">
                <i class="fas fa-paper-plane me-2"></i>
                Submit Pengajuan
            </span>
            <span wire:loading wire:target="submitDokumen">
                <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
            </span>
        </button>
    @endif
@endcan

@can('peminjaman_dana.validasi_dokumen')
    @if ($currentStep == 2)
        <button type="button" class="btn btn-primary" 
                @click="$dispatch('open-modal', { modal: 'modal-validasi-dokumen' })">
            <i class="fas fa-check me-2"></i>
            Setujui Peminjaman
        </button>
        <button type="button" class="btn btn-danger"
                @click="$dispatch('open-modal', { modal: 'modal-tolak-validasi' })">
            <i class="fas fa-times me-2"></i>
            Tolak
        </button>
    @endif
@endcan

@can('peminjaman_dana.persetujuan_debitur')
    @if ($currentStep == 3)
        <button type="button" class="btn btn-success"
                @click="$dispatch('open-modal', { modal: 'modal-persetujuan-debitur' })">
            <i class="fas fa-user-check me-2"></i>
            Setujui
        </button>
        <button type="button" class="btn btn-danger"
                @click="$dispatch('open-modal', { modal: 'modal-tolak-debitur' })">
            <i class="fas fa-times me-2"></i>
            Tolak
        </button>
    @endif
@endcan

@can('peminjaman_dana.validasi_ceo_ski')
    @if ($currentStep == 4)
        <button type="button" class="btn btn-warning"
                @click="$dispatch('open-modal', { modal: 'modal-persetujuan-ceo' })">
            <i class="fas fa-crown me-2"></i>
            Setujui
        </button>
        <button type="button" class="btn btn-danger"
                @click="$dispatch('open-modal', { modal: 'modal-tolak-ceo' })">
            <i class="fas fa-times me-2"></i>
            Tolak
        </button>
    @endif
@endcan

@can('peminjaman_dana.validasi_direktur')
    @if ($currentStep == 5)
        <button type="button" class="btn btn-info"
                @click="$dispatch('open-modal', { modal: 'modal-persetujuan-direktur' })">
            <i class="fas fa-briefcase me-2"></i>
            Setujui
        </button>
        <button type="button" class="btn btn-danger"
                @click="$dispatch('open-modal', { modal: 'modal-tolak-direktur' })">
            <i class="fas fa-times me-2"></i>
            Tolak
        </button>
    @endif
@endcan

@can('peminjaman_dana.konfirmasi_debitur')
    @if ($currentStep == 8)
        <button type="button" class="btn btn-success" wire:click="konfirmasiDebiturTerima"
                wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="konfirmasiDebiturTerima">
                <i class="fas fa-check me-2"></i>
                Terima
            </span>
            <span wire:loading wire:target="konfirmasiDebiturTerima">
                <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
            </span>
        </button>
        <button type="button" class="btn btn-danger"
                @click="$dispatch('open-modal', { modal: 'modal-tolak-konfirmasi' })">
            <i class="fas fa-times me-2"></i>
            Tolak
        </button>
    @endif
@endcan
