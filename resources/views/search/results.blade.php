{{-- @extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Hasil Pencarian</h5>
                <span class="text-muted">{{ $total }} hasil untuk: <strong>{{ $q ?: '-' }}</strong></span>
            </div>
            <div class="card-body">
                <form action="{{ route('search') }}" method="get" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Cari..." />
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>

                @php
                    $groups = [
                        'pages' => 'Pages',
                        'debitur' => 'Master Debitur & Investor',
                        'pengajuan_peminjaman' => 'Pengajuan Peminjaman',
                        'pengajuan_investasi' => 'Pengajuan Investasi',
                        'sfinlog_peminjaman' => 'SFinlog • Peminjaman',
                    ];
                @endphp

                @if($total === 0)
                    <div class="text-center text-muted py-5">Tidak ada hasil yang cocok.</div>
                @else
                    @foreach($groups as $key => $label)
                        @php $items = $results[$key] ?? []; @endphp
                        @if(!empty($items))
                            <h6 class="text-uppercase text-muted mt-4 mb-2">{{ $label }}</h6>
                            <ul class="list-group mb-3">
                                @foreach($items as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{ $item['url'] }}" class="fw-medium text-body">{{ $item['title'] }}</a>
                                            @if(!empty($item['subtitle']))
                                                <div class="small text-muted">{{ $item['subtitle'] }}</div>
                                            @endif
                                        </div>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ $item['url'] }}">Buka</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection --}}
