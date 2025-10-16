<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\MasterDebiturDanInvestor;
use App\Models\Master\MasterKol;

class DebiturDanInvestorController extends Controller
{
    public function index()
    {
        $debiturs = MasterDebiturDanInvestor::with('kol')->get();
        $data = $debiturs->map(function($d){
            $raw = $d->flagging ?? 'tidak';
            return [
                'id' => $d->id_debitur,
                'nama_perusahaan' => $d->nama_debitur,
                // display 'Investor' when flagging is 'ya', otherwise '-'
                'Flagging' => $raw === 'ya' ? 'Investor' : '-',
                'flagging_raw' => $raw,
                'nama_ceo' => $d->nama_ceo,
                'alamat_perusahaan' => $d->alamat,
                'email' => $d->email,
                'kol_perusahaan' => optional($d->kol)->kol,
                'kol_id' => $d->id_kol,
                'nama_bank' => $d->nama_bank,
                'no_rek' => $d->no_rek,
            ];
        })->toArray();

        $kol = MasterKol::orderBy('id_kol','asc')->get()->map(function($k){
            return ['id' => $k->id_kol, 'kol' => $k->kol];
        })->toArray();

        return view('livewire.master-data-debitur-investor.index', compact('data','kol'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kol_perusahaan' => 'required|exists:master_kol,id_kol',
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'nama_ceo' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'no_rek' => 'nullable|string|max:100',
              'flagging' => 'nullable|string|in:ya,tidak'
        ]);

        $debitur = MasterDebiturDanInvestor::create([
            'id_kol' => $validated['kol_perusahaan'],
            'nama_debitur' => $validated['nama_perusahaan'],
            'alamat' => $validated['alamat_perusahaan'] ?? null,
            'email' => $validated['email'] ?? null,
            'nama_ceo' => $validated['nama_ceo'] ?? null,
            'nama_bank' => $validated['nama_bank'] ?? null,
            'no_rek' => $validated['no_rek'] ?? null,
              'flagging' => $validated['flagging'] ?? 'tidak',
        ]);

        $kol = MasterKol::find($debitur->id_kol);
        $raw = $debitur->flagging ?? 'tidak';
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $debitur->id_debitur,
                'nama_perusahaan' => $debitur->nama_debitur,
                // display 'Investor' when flagging is 'ya', otherwise '-'
                'Flagging' => $raw === 'ya' ? 'Investor' : '-',
                'flagging_raw' => $raw,
                'nama_ceo' => $debitur->nama_ceo,
                'alamat_perusahaan' => $debitur->alamat,
                'email' => $debitur->email,
                'kol_perusahaan' => optional($kol)->kol,
                'kol_id' => $debitur->id_kol,
                'nama_bank' => $debitur->nama_bank,
                'no_rek' => $debitur->no_rek,
            ]
        ]);
    }

    public function edit($id)
    {
        $d = MasterDebiturDanInvestor::with('kol')->findOrFail($id);
        $raw = $d->flagging ?? 'tidak';
        return response()->json([
            'id' => $d->id_debitur,
            'nama_perusahaan' => $d->nama_debitur,
            // display 'Investor' when flagging is 'ya', otherwise '-'
            'Flagging' => $raw === 'ya' ? 'Investor' : '-',
            'flagging_raw' => $raw,
            'nama_ceo' => $d->nama_ceo,
            'alamat_perusahaan' => $d->alamat,
            'email' => $d->email,
            'kol_perusahaan' => $d->id_kol,
            'nama_bank' => $d->nama_bank,
            'no_rek' => $d->no_rek,
        ]);
    }

    public function update(Request $request, $id)
    {
        $debitur = MasterDebiturDanInvestor::findOrFail($id);
        $validated = $request->validate([
            'kol_perusahaan' => 'required|exists:master_kol,id_kol',
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_perusahaan' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'nama_ceo' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|max:255',
            'no_rek' => 'nullable|string|max:100',
            'flagging' => 'nullable|string|in:ya,tidak'
        ]);

        $debitur->update([
            'id_kol' => $validated['kol_perusahaan'],
            'nama_debitur' => $validated['nama_perusahaan'],
            'alamat' => $validated['alamat_perusahaan'] ?? null,
            'email' => $validated['email'] ?? null,
            'nama_ceo' => $validated['nama_ceo'] ?? null,
            'nama_bank' => $validated['nama_bank'] ?? null,
            'no_rek' => $validated['no_rek'] ?? null,
            'flagging' => $validated['flagging'] ?? 'tidak',
        ]);

        $kol = MasterKol::find($debitur->id_kol);
        $raw = $debitur->flagging ?? 'tidak';
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $debitur->id_debitur,
                'nama_perusahaan' => $debitur->nama_debitur,
                'Flagging' => $raw === 'ya' ? 'Investor' : '-',
                'flagging_raw' => $raw,
                'nama_ceo' => $debitur->nama_ceo,
                'alamat_perusahaan' => $debitur->alamat,
                'email' => $debitur->email,
                'kol_perusahaan' => optional($kol)->kol,
                'kol_id' => $debitur->id_kol,
                'nama_bank' => $debitur->nama_bank,
                'no_rek' => $debitur->no_rek,
            ]
        ]);
    }

    public function destroy($id)
    {
        $debitur = MasterDebiturDanInvestor::findOrFail($id);
        $debitur->delete();
        return response()->json(['success' => true]);
    }
}
