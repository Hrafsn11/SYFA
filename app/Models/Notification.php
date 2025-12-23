<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'notifications';
    protected $primaryKey = 'id_notification';
    public $incrementing = false;

    protected $fillable = [
        'type',
        'content',
        'link',
        'status',
        'status_hide',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
