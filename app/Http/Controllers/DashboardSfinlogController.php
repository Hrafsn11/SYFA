<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PembiayaanSFinlogService;

class DashboardSfinlogController extends Controller
{
    public function index(Request $request)
    {
        $data = PembiayaanSFinlogService::getDashboardData();
        return response()->json($data);
    }
}
