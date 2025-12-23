<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationFeature extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'notification_feature';
    protected $primaryKey = 'id_notification_feature';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'module',
    ];

    public function notificationFeatureDetails()
    {
        return $this->hasMany(NotificationFeatureDetail::class, 'notification_feature_id', 'id_notification_feature');
    }
}
