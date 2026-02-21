@section('title', 'Penyesuaian Cicilan')

<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Penyesuaian Cicilan</h4>
            <p class="text-muted">Daftar penyesuaian cicilan yang telah dibuat</p>
        </div>
        @can('program_restrukturisasi.add')
            <a href="{{ route('program-restrukturisasi.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Tambah Penyesuaian Cicilan
            </a>
        @endcan
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <livewire:program-restrukturisasi.table />
        </div>
    </div>
</div>
