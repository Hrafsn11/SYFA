<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKaryawanSki extends Model
{
    use HasFactory;

    protected $table = 'master_karyawan_ski';

    protected $fillable = [
        'user_id',
        'nama_karyawan',
        'jabatan',
        'email',
        'role',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the user that owns the karyawan.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
