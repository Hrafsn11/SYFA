<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ConfigMatrixPinjaman;
use Illuminate\Support\Facades\Validator;

class ConfigMatrixPinjamanController extends Controller
{
    public function index()
    {
        return view('livewire.config-matrix-pinjaman.index');
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nominal' => 'required|numeric|min:0',
            'approve_oleh' => 'required|string|max:255'
        ]);
        
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $m = ConfigMatrixPinjaman::create([
            'nominal' => $request->input('nominal'),
            'approve_oleh' => $request->input('approve_oleh'),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function edit($id)
    {
        $m = ConfigMatrixPinjaman::where('id_matrix_pinjaman', $id)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id_matrix_pinjaman' => $m->id_matrix_pinjaman,
                'nominal' => $m->nominal,
                'approve_oleh' => $m->approve_oleh
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $m = ConfigMatrixPinjaman::where('id_matrix_pinjaman', $id)->firstOrFail();
        
        $v = Validator::make($request->all(), [
            'nominal' => 'required|numeric|min:0',
            'approve_oleh' => 'required|string|max:255'
        ]);
        
        if ($v->fails()) {
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $m->update([
            'nominal' => $request->input('nominal'),
            'approve_oleh' => $request->input('approve_oleh'),
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function destroy($id)
    {
        $m = ConfigMatrixPinjaman::where('id_matrix_pinjaman', $id)->firstOrFail();
        $m->delete();
        
        return response()->json([
            'success' => true,
        ]);
    }
}
