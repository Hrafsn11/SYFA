<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CellsProject extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'cells_projects';
    protected $primaryKey = 'id_cells_project';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_project'
    ];
}
