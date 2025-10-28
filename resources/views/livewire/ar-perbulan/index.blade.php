@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="col-12">
                <h4 class="fw-bold mb-3">AR Perbulan</h4>

                <div class="mb-3" style="width: 250px;">
                    <div class="input-group input-group-md">
                        <input type="text" class="form-control" placeholder="Select Period" id="flatpickr-tahun-pencarian"
                            name="tahun_pencarian" />
                        <span class="input-group-text cursor-pointer">
                            <i class="ti ti-filter"></i>
                        </span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-history-kol table table-bordered" id="tableHistoryKol">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">nama perusahaan</th>
                                    <th class="text-center">Sisa AR Piutang pokok</th>
                                    <th class="text-center">Sisa Bagi hasil</th>
                                    <th class="text-center">Sisa AR pokok + bagi hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">Techno</td>
                                    <td class="text-center">Rp. 3.640.078</td>
                                    <td class="text-center">Rp. 503.692</td>
                                    <td class="text-center">Rp. 4.143.770</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
