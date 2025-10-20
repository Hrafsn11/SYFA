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
        $items = ConfigMatrixPinjaman::orderBy('id_matrix_pinjaman','asc')->get();
        // convert to array with numeric order instead of using id; format nominal for rupiah (no decimals)
        $data = $items->map(function($it){
            return [
                'id' => $it->id_matrix_pinjaman,
                // Indonesian style thousands separator with no decimals
                'nominal' => number_format($it->nominal, 0, ',', '.'),
                'approve_oleh' => $it->approve_oleh ?? '-',
            ];
        })->toArray();

        return view('livewire.config-matrix-pinjaman.index', ['data' => $data]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nominal' => 'required|numeric',
            'approve_oleh' => 'nullable|string|max:255'
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);

        $m = ConfigMatrixPinjaman::create([
            'nominal' => $request->input('nominal'),
            'approve_oleh' => $request->input('approve_oleh'),
        ]);

        return response()->json(['success'=>true,'data'=>[
            'id' => $m->id_matrix_pinjaman,
            'nominal' => number_format($m->nominal,0,',','.'),
            'approve_oleh' => $m->approve_oleh ?? '-'
        ]]);
    }

    public function edit($id)
    {
        $m = ConfigMatrixPinjaman::findOrFail($id);
    return response()->json(['id'=>$m->id_matrix_pinjaman,'nominal'=>number_format($m->nominal,0,',','.'),'approve_oleh'=>$m->approve_oleh]);
    }

    public function update(Request $request, $id)
    {
        $m = ConfigMatrixPinjaman::findOrFail($id);
        $v = Validator::make($request->all(), [
            'nominal' => 'required|numeric',
            'approve_oleh' => 'nullable|string|max:255'
        ]);
        if ($v->fails()) return response()->json(['success'=>false,'errors'=>$v->errors()], 422);

        $m->update([
            'nominal' => $request->input('nominal'),
            'approve_oleh' => $request->input('approve_oleh'),
        ]);

    return response()->json(['success'=>true,'data'=>['id'=>$m->id_matrix_pinjaman,'nominal'=>number_format($m->nominal,0,',','.'),'approve_oleh'=>$m->approve_oleh ?? '-']]);
    }

    public function destroy($id)
    {
        $m = ConfigMatrixPinjaman::findOrFail($id);
        $m->delete();
        return response()->json(['success'=>true]);
    }
}
