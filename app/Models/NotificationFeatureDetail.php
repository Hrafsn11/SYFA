<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationFeatureDetail extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'notification_feature_detail';
    protected $primaryKey = 'id_notification_feature_detail';
    public $incrementing = false;

    protected $fillable = [
        'notification_feature_id',
        'role_assigned',
        'message',
    ];

    public function notificationFeature()
    {
        return $this->belongsTo(NotificationFeature::class, 'notification_feature_id', 'id_notification_feature');
    }
}
