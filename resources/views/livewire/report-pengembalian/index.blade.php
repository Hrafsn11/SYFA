@extends('layouts.app')

@section('content')
    <div>
        <div class="row">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold py-3 mb-4">Report Pengembalian</h4>
            </div>

            <div class="card">
                <div class="card-datatable table-responsive">
                    <table class="datatables-history-kol table table-bordered" id="tableHistoryKol">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">no invoice</th>
                                <th class="text-center">Invoice date</th>
                                <th class="text-center">Due date</th>
                                <th class="text-center">hari keterlambatan</th>
                                <th class="text-center">total bulan pemakaian</th>
                                <th class="text-center">nilai total pengembalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td class="text-center">12345678</td>
                                <td class="text-center">15 Maret 2025</td>
                                <td class="text-center">20 Maret 2025</td>
                                <td class="text-center">?</td>
                                <td class="text-center">1 Bulan</td>
                                <td class="text-center">Rp. 50.000.000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
