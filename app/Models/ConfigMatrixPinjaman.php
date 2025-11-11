<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigMatrixPinjaman extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'config_matrix_pinjaman';
    protected $primaryKey = 'id_matrix_pinjaman';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nominal',
        'approve_oleh',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];
}
