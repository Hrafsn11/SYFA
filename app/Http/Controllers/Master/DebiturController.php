<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\MasterDebitur;
use App\Models\Master\MasterKol;
use App\Models\Master\MasterSumberPendanaanEksternal;

class DebiturController extends Controller
{
    public function index()
    {
        $debiturs = MasterDebitur::with(['kol','sumberPendanaan'])->paginate(20);
        return view('livewire.master-data-debitur-investor.index', compact('debiturs'));
    }

    // public function create()
    // {
    //     $kols = MasterKol::all();
    //     $sumber = MasterSumberPendanaanEksternal::all();
    //     return view('master.debitur.create', compact('kols','sumber'));
    // }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'id_kol' => 'required|exists:master_kol,id_kol',
    //         'id_instansi' => 'nullable|exists:master_sumber_pendanaan_eksternal,id_instansi',
    //         'nama_debitur' => 'required|string|max:255',
    //         'alamat' => 'nullable|string|max:255',
    //         'email' => 'nullable|email|max:255',
    //         'nama_ceo' => 'nullable|string|max:255',
    //         'nama_bank' => 'nullable|string|max:255',
    //         'no_rek' => 'nullable|string|max:100',
    //     ]);

    //     MasterDebitur::create($data);

    //     return redirect()->route('master.debitur.index')->with('success','Debitur dibuat');
    // }

    // public function show($id)
    // {
    //     $debitur = MasterDebitur::with(['kol','sumberPendanaan'])->findOrFail($id);
    //     return view('master.debitur.show', compact('debitur'));
    // }

    // public function edit($id)
    // {
    //     $debitur = MasterDebitur::findOrFail($id);
    //     $kols = MasterKol::all();
    //     $sumber = MasterSumberPendanaanEksternal::all();
    //     return view('master.debitur.edit', compact('debitur','kols','sumber'));
    // }

    // public function update(Request $request, $id)
    // {
    //     $debitur = MasterDebitur::findOrFail($id);
    //     $data = $request->validate([
    //         'id_kol' => 'required|exists:master_kol,id_kol',
    //         'id_instansi' => 'nullable|exists:master_sumber_pendanaan_eksternal,id_instansi',
    //         'nama_debitur' => 'required|string|max:255',
    //         'alamat' => 'nullable|string|max:255',
    //         'email' => 'nullable|email|max:255',
    //         'nama_ceo' => 'nullable|string|max:255',
    //         'nama_bank' => 'nullable|string|max:255',
    //         'no_rek' => 'nullable|string|max:100',
    //     ]);

    //     $debitur->update($data);

    //     return redirect()->route('master.debitur.index')->with('success','Debitur diperbarui');
    // }

    // public function destroy($id)
    // {
    //     $debitur = MasterDebitur::findOrFail($id);
    //     $debitur->delete();
    //     return redirect()->route('master.debitur.index')->with('success','Debitur dihapus');
    // }
}
