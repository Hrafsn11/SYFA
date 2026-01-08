<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterDebiturDanInvestor extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'master_debitur_dan_investor';

    protected $primaryKey = 'id_debitur';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'id_kol',
        'nama',
        'alamat',
        'email',
        'npa',
        'no_telepon',
        'status',
        'deposito',
        'nama_ceo',
        'nama_bank',
        'no_rek',
        'npwp',
        'flagging',
        'flagging_investor',
        'tanda_tangan',
    ];

    protected $casts = [
        'flagging' => 'string',
        'flagging_investor' => 'string',
        'status' => 'string',
        'deposito' => 'string',
    ];

    public function kol()
    {
        return $this->belongsTo(MasterKol::class, 'id_kol', 'id_kol');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function peminjamanFinlog()
    {
        return $this->hasMany(PeminjamanFinlog::class, 'id_debitur', 'id_debitur');
    }

    public function Debitur($query)
    {
        return $query->where('flagging', 'tidak');
    }

    public function Investor($query)
    {
        return $query->where('flagging', 'ya');
    }

    /**
     * Check if investor is registered in a specific platform type
     * 
     * @param string $type sfinance, sfinlog, or both
     * @return bool
     */
    public function isInvestorType(string $type): bool
    {
        if (!$this->flagging_investor) {
            return false;
        }

        $types = array_map('trim', explode(',', $this->flagging_investor));
        return in_array($type, $types);
    }

    /**
     * Get investor types as array
     * 
     * @return array
     */
    public function getInvestorTypes(): array
    {
        if (!$this->flagging_investor) {
            return [];
        }

        return array_map('trim', explode(',', $this->flagging_investor));
    }
}
