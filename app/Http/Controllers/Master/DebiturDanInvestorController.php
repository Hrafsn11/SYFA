<?php

namespace App\Http\Controllers\Master;

use App\Helpers\Response;
use App\Models\Role;
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
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Handle file upload for both debitur and investor
            $file = null;
            if ($request->tanda_tangan) {
                $file = Storage::disk('public')->put('tanda_tangan', $request->tanda_tangan);
            }
            $validated['tanda_tangan'] = $file;

            if (isset($validated['kode_perusahaan'])) {
                $validated['kode_perusahaan'] = strtoupper($validated['kode_perusahaan']);
            }

            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $roleName = $validated['flagging'] === 'ya' ? 'Investor' : 'Debitur';

            $role = Role::firstOrCreate([
                'name' => $roleName,
                'restriction' => 0
            ]);

            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }

            $validated['user_id'] = $user->id;

            // Remove password keys safely before creating debitur
            if (isset($validated['password'])) {
                unset($validated['password']);
            }
            if (isset($validated['password_confirmation'])) {
                unset($validated['password_confirmation']);
            }

            $debitur = MasterDebiturDanInvestor::create($validated);
            $debitur->load('kol', 'user');

            DB::commit();

            $message = $roleName === 'Investor'
                ? 'Investor berhasil ditambahkan dan akun pengguna dibuat'
                : 'Debitur berhasil ditambahkan dan akun pengguna dibuat';

            return Response::success(null, $message);
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
                'alamat' => $debitur->alamat,
                'email' => $debitur->email,
                'no_telepon' => $debitur->no_telepon,
                'nama_bank' => $debitur->nama_bank,
                'no_rek' => $debitur->no_rek
            ];
        } else {
            $result = [
                'nama' => $debitur->nama,
                'kode_perusahaan' => $debitur->kode_perusahaan,
                'nama_ceo' => $debitur->nama_ceo,
                'alamat' => $debitur->alamat,
                'email' => $debitur->email,
                'no_telepon' => $debitur->no_telepon,
                'nama_bank' => $debitur->nama_bank,
                'no_rek' => $debitur->no_rek,
                'id_kol' => $debitur->id_kol,
                'npwp' => $debitur->npwp,
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

            // Handle file upload for both debitur and investor
            $file = $debitur->tanda_tangan;
            if ($request->tanda_tangan) {
                // Delete old file if exists
                if ($file && Storage::disk('public')->exists($debitur->tanda_tangan)) {
                    Storage::disk('public')->delete($debitur->tanda_tangan);
                }

                $file = Storage::disk('public')->put('tanda_tangan', $request->tanda_tangan);
                $validated['tanda_tangan'] = $file;
            }

            if (isset($validated['kode_perusahaan'])) {
                $validated['kode_perusahaan'] = strtoupper($validated['kode_perusahaan']);
            }

            // Update user if exists
            if ($debitur->user_id) {
                $user = User::find($debitur->user_id);
                if ($user) {
                    $user->name = $validated['nama'];
                    $user->email = $validated['email'];

                    // Update password only if provided
                    if (isset($validated['password']) && !empty($validated['password'])) {
                        $user->password = Hash::make($validated['password']);
                    }

                    $oldFlagging = $debitur->flagging;
                    $newFlagging = $validated['flagging'];

                    if ($oldFlagging !== $newFlagging) {
                        $oldRoleName = $oldFlagging === 'ya' ? 'Investor' : 'Debitur';
                        $newRoleName = $newFlagging === 'ya' ? 'Investor' : 'Debitur';

                        if ($user->hasRole($oldRoleName)) {
                            $user->removeRole($oldRoleName);
                        }

                        if (!$user->hasRole($newRoleName)) {
                            $user->assignRole($newRoleName);
                        }
                    }

                    $user->save();
                }
            }

            // Remove password keys safely from debitur data
            if (isset($validated['password'])) {
                unset($validated['password']);
            }
            if (isset($validated['password_confirmation'])) {
                unset($validated['password_confirmation']);
            }

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
        try {
            $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();

            $newStatus = $debitur->status === 'active' ? 'non active' : 'active';
            $debitur->update(['status' => $newStatus]);

            return Response::success([
                'status' => $newStatus
            ], 'Status berhasil diubah menjadi ' . ucfirst($newStatus));
        } catch (\Exception $e) {
            return Response::errorCatch($e);
        }
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

    /**
     * Unlock a locked account
     */
    public function unlock($id)
    {
        try {
            $debitur = MasterDebiturDanInvestor::where('id_debitur', $id)->firstOrFail();

            if ($debitur->status !== 'locked') {
                return Response::error('Akun tidak dalam status terkunci');
            }

            DB::beginTransaction();

            // Update debitur status
            $debitur->update(['status' => 'active']);

            // Reset user login attempts if user exists
            if ($debitur->user_id) {
                $user = User::find($debitur->user_id);
                if ($user) {
                    $user->resetLoginAttempts();
                }
            }

            DB::commit();

            return Response::success([
                'status' => 'active'
            ], 'Akun berhasil dibuka kuncinya');
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::errorCatch($e);
        }
    }
}
