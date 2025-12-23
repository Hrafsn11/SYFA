<?php

namespace App\Models;

use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Create a default team for the user
            $user->ownedTeams()->save(
                $user->ownedTeams()->make([
                    'name' => $user->name . "'s Team",
                    'personal_team' => true,
                ])
            );
        });
    }

    /**
     * Get the debitur/investor record associated with the user.
     */
    public function debitur()
    {
        return $this->hasOne(MasterDebiturDanInvestor::class, 'user_id', 'id');
    }

    /**
     * Alias for debitur() relation - for better semantic naming.
     */
    public function debiturInvestor()
    {
        return $this->debitur();
    }

    public function notifs()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id')->where('status_hide', 'unhide');
    }

    public function unread_notifs()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id')->where('status', 'unread')->where('status_hide', 'unhide');
    }
}
