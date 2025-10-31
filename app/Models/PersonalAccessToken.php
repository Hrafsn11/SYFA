<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasUlids;

    protected $table = 'personal_access_tokens';
    protected $primaryKey = 'id';

    protected $keyType = 'string';
    public $incrementing = false;
}
