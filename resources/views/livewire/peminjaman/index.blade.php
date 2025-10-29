@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold">Menu Pengajuan Peminjaman</h4>
                    <a href="{{ route('ajukanpeminjaman') }}"
                        class="btn btn-primary d-flex justify-center align-items-center gap-3">
                        <i class="fa-solid fa-plus"></i>
                        Ajukan Peminjaman
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <livewire:pengajuan-pinjaman-table />
            </div>
        </div>
    </div>
@endsection
