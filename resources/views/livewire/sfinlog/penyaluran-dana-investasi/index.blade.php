@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <h4 class="fw-bold">Penyaluran Dana Investasi - SFinlog</h4>

            <div class="content-wrapper">
                <div class="card">
                    <div class="card-datatable">
                        <livewire:sfinlog.penyaluran-dana-investasi-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

