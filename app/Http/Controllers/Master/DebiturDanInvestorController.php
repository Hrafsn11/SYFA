<?php

namespace App\Http\Controllers\Master;

use App\Helpers\Response;
use App\Models\User;
use App\Models\MasterKol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\MasterDebiturDanInvestor;
use App\Http\Requests\DebiturDanInvestorRequest;

class DebiturDanInvestorController extends Controller
{
    // public function index()
    // {
    //     $kol = MasterKol::orderBy('id_kol', 'asc')->get();
    //     $banks = [
    //         'BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'
    //     ];
    //     return view('livewire.master-data-debitur-investor.index', compact('kol','banks'));
    // }

    public function store(DebiturDanInvestorRequest $request)
    {
        // dd('masuk');
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $file = null;
            if ($validated['flagging'] == 'tidak') {
                // Handle file upload
                if ($request->tanda_tangan) {
                    $file = Storage::disk('public')->put('tanda_tangan', $request->tanda_tangan);
                }
                $validated['tanda_tangan'] = $file;
            }

            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $validated['user_id'] = $user->id;
            unset($validated['password'], $validated['password_confirmation']);
            
            $debitur = MasterDebiturDanInvestor::create($validated);
            $debitur->load('kol', 'user');

            DB::commit();

            return Response::success(null, 'Debitur berhasil ditambahkan dan akun pengguna dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function edit($id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->with('kol')->firstOrFail();
        if ($debitur->flagging == 'ya') {
            $result = [
                'nama' => $debitur->nama,
                'deposito' => $debitur->deposito,
                'email' => $debitur->email,
                'no_telepon' => $debitur->no_telepon,
                'nama_bank' => $debitur->nama_bank,
                'no_rek' => $debitur->no_rek
            ];
        } else {
            $result = [
                'nama' => $debitur->nama,
                'nama_ceo' => $debitur->nama_ceo,
                'alamat' => $debitur->nama_ceo,
                'email' => $debitur->email,
                'no_telepon' => $debitur->no_telepon,
                'nama_bank' => $debitur->nama_bank,
                'no_rek' => $debitur->no_rek,
                'id_kol' => $debitur->id_kol,
                // 'tanda_tangan' => $debitur->tanda_tangan
            ];
        }

        $result['id'] = $debitur->id_debitur;
        $result['flagging'] = $debitur->flagging;
        
        return Response::success($result, 'Debitur berhasil ditemukan');
    }

    public function update(DebiturDanInvestorRequest $request, $id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            if ($debitur->flagging == 'tidak') {
                $file = $debitur->tanda_tangan;
                if (Storage::disk('public')->exists($debitur->tanda_tangan)) {
                    Storage::disk('public')->delete($debitur->tanda_tangan);
                }
    
                // Handle file upload
                if ($request->tanda_tangan) {
                    $file = Storage::disk('public')->put('tanda_tangan', $request->tanda_tangan);
                }
                $validated['tanda_tangan'] = $file;
            }

            // Update user if exists
            if ($debitur->user_id) {
                $user = User::find($debitur->user_id);
                if ($user) {
                    $user->name = $validated['nama'];
                    $user->email = $validated['email'];
                    
                    // Update password only if provided
                    if (!empty($validated['password'])) {
                        $user->password = Hash::make($validated['password']);
                    }
                    
                    $user->save();
                }
            }

            unset($validated['password'], $validated['password_confirmation']); // Remove password from debitur data
            $debitur->update($validated);

            DB::commit();
            return Response::success(null, 'Debitur berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }

    public function destroy($id)
    {
        try {
            $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();
            if (Storage::disk('public')->exists($debitur->tanda_tangan)) {
                Storage::disk('public')->delete($debitur->tanda_tangan);
            }
            $debitur->delete();

            return Response::success(null, 'Debitur berhasil dihapus');
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
    }

    public function toggleStatus($id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();
        
        $newStatus = $debitur->status === 'active' ? 'non active' : 'active';
        $debitur->update(['status' => $newStatus]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah menjadi ' . ucfirst($newStatus),
            'status' => $newStatus
        ]);
    }

    public function historyKol($id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->with('kol')->firstOrFail();
        
        return view('livewire.kol-history.index', [
            'debitur' => $debitur
        ]);
    }

    public function deleteSignature($id)
    {
        try {
            $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();
            
            // Delete file if exists
            if ($debitur->tanda_tangan && Storage::disk('public')->exists($debitur->tanda_tangan)) {
                Storage::disk('public')->delete($debitur->tanda_tangan);
            }
            
            // Update database
            $debitur->update(['tanda_tangan' => null]);
            
            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tanda tangan: ' . $e->getMessage()
            ], 500);
        }
    }
}
