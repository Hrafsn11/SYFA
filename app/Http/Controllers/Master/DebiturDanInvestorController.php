<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterDebiturDanInvestor;
use App\Models\MasterKol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DebiturDanInvestorController extends Controller
{
    public function index()
    {
        $kol = MasterKol::orderBy('id_kol', 'asc')->get();
        $banks = [
            'BCA','BSI','Mandiri','BRI','BNI','Danamon','Permata Bank','OCBC','Panin Bank','UOB Indonesia','CIMB Niaga'
        ];
        return view('livewire.master-data-debitur-investor.index', compact('kol','banks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kol' => 'required|exists:master_kol,id_kol',
            'nama_debitur' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'no_telepon' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:active,non active',
            'deposito' => 'nullable|string|in:reguler,khusus',
            'nama_ceo' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|in:BCA,BSI,Mandiri,BRI,BNI,Danamon,Permata Bank,OCBC,Panin Bank,UOB Indonesia,CIMB Niaga',
            'no_rek' => 'nullable|string|max:100',
            'flagging' => 'nullable|string|in:ya,tidak'
        ]);

        try {
            DB::beginTransaction();

            // Create User Account
            $user = User::create([
                'name' => $validated['nama_debitur'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Create Debitur/Investor record
            $validated['user_id'] = $user->id;
            unset($validated['password']); // Remove password from debitur data
            
            $debitur = MasterDebiturDanInvestor::create($validated);
            $debitur->load('kol', 'user');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Debitur berhasil ditambahkan dan akun pengguna dibuat',
                'data' => $debitur->toArray()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan debitur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->with('kol')->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $debitur->toArray()
        ]);
    }

    public function update(Request $request, $id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();
        
        $validated = $request->validate([
            'id_kol' => 'required|exists:master_kol,id_kol',
            'nama_debitur' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'email' => 'required|email|max:255|unique:users,email,' . $debitur->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'no_telepon' => 'nullable|string|max:20',
            'status' => 'nullable|string|in:active,non active',
            'deposito' => 'nullable|string|in:reguler,khusus',
            'nama_ceo' => 'nullable|string|max:255',
            'nama_bank' => 'nullable|string|in:BCA,BSI,Mandiri,BRI,BNI,Danamon,Permata Bank,OCBC,Panin Bank,UOB Indonesia,CIMB Niaga',
            'no_rek' => 'nullable|string|max:100',
            'flagging' => 'nullable|string|in:ya,tidak'
        ]);

        try {
            DB::beginTransaction();

            // Update user if exists
            if ($debitur->user_id) {
                $user = User::find($debitur->user_id);
                if ($user) {
                    $user->name = $validated['nama_debitur'];
                    $user->email = $validated['email'];
                    
                    // Update password only if provided
                    if (!empty($validated['password'])) {
                        $user->password = Hash::make($validated['password']);
                    }
                    
                    $user->save();
                }
            }

            unset($validated['password']); // Remove password from debitur data
            $debitur->update($validated);
            $debitur->refresh();
            $debitur->load('kol', 'user');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Debitur berhasil diupdate',
                'data' => $debitur->toArray()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate debitur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();
        $debitur->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Debitur berhasil dihapus'
        ]);
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
}
