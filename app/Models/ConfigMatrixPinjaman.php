<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigMatrixPinjaman extends Model
{
    use HasFactory;

    protected $table = 'config_matrix_pinjaman';
    protected $primaryKey = 'id_matrix_pinjaman';

    protected $fillable = [
        'nominal',
        'approve_oleh',
    ];
}
