<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterKaryawanSki;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MasterKaryawanSkiController extends Controller
{
    public function index()
    {
        $karyawan = MasterKaryawanSki::orderBy('created_at', 'desc')->get();
        return view('livewire.master-karyawan-ski.index', compact('karyawan'));
    }

    public function show($id)
    {
        try {
            $karyawan = MasterKaryawanSki::with('user')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_karyawan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create User Account (untuk login)
            $user = User::create([
                'name' => $request->nama_karyawan,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Create Karyawan record
            $karyawan = MasterKaryawanSki::create([
                'user_id' => $user->id,
                'nama_karyawan' => $request->nama_karyawan,
                'jabatan' => $request->jabatan,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'status' => 'Active',
            ]);

            $karyawan->load('user');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan dan akun pengguna dibuat',
                'data' => $karyawan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $karyawan = MasterKaryawanSki::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_karyawan' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $karyawan->user_id,
            'role' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update user account if exists
            if ($karyawan->user_id) {
                $user = User::find($karyawan->user_id);
                if ($user) {
                    $user->name = $request->nama_karyawan;
                    $user->email = $request->email;
                    
                    // Update password only if provided
                    if ($request->filled('password')) {
                        $user->password = Hash::make($request->password);
                    }
                    
                    $user->save();
                }
            }

            // Update karyawan data
            $data = [
                'nama_karyawan' => $request->nama_karyawan,
                'jabatan' => $request->jabatan,
                'email' => $request->email,
                'role' => $request->role,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $karyawan->update($data);
            $karyawan->refresh();
            $karyawan->load('user');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diupdate',
                'data' => $karyawan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $karyawan = MasterKaryawanSki::findOrFail($id);
            $karyawan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $karyawan = MasterKaryawanSki::findOrFail($id);
            $karyawan->status = $karyawan->status === 'Active' ? 'Non Active' : 'Active';
            $karyawan->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah',
                'data' => $karyawan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}
