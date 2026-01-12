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
        'login_attempts',
        'locked_at',
    ];

    /**
     * Max login attempts before account gets locked.
     */
    public const MAX_LOGIN_ATTEMPTS = 3;

    /**
     * Roles that cannot be locked.
     */
    protected static $unlockableRoles = ['super-admin', 'admin'];

    /**
     * Check if user account can be locked (not admin/super-admin).
     */
    public function isLockable(): bool
    {
        foreach (self::$unlockableRoles as $role) {
            if ($this->hasRole($role)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the user's associated debitur/investor account is locked.
     */
    public function isAccountLocked(): bool
    {
        $debitur = $this->debitur;
        if ($debitur) {
            return $debitur->status === 'locked';
        }
        return false;
    }

    /**
     * Check if the user's associated debitur/investor account is non-active.
     */
    public function isAccountNonActive(): bool
    {
        $debitur = $this->debitur;
        if ($debitur) {
            return $debitur->status === 'non active';
        }
        return false;
    }

    /**
     * Increment login attempts.
     */
    public function incrementLoginAttempts(): int
    {
        $this->login_attempts++;
        $this->save();
        return $this->login_attempts;
    }

    /**
     * Reset login attempts to zero.
     */
    public function resetLoginAttempts(): void
    {
        $this->login_attempts = 0;
        $this->locked_at = null;
        $this->save();
    }

    /**
     * Lock the account by updating debitur/investor status.
     */
    public function lockAccount(): bool
    {
        $debitur = $this->debitur;
        if ($debitur) {
            $debitur->status = 'locked';
            $debitur->save();
            
            $this->locked_at = now();
            $this->save();
            
            return true;
        }
        return false;
    }

    /**
     * Unlock the account by resetting status and login attempts.
     */
    public function unlockAccount(): bool
    {
        $debitur = $this->debitur;
        if ($debitur && $debitur->status === 'locked') {
            $debitur->status = 'active';
            $debitur->save();
            
            $this->resetLoginAttempts();
            
            return true;
        }
        return false;
    }

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
        'locked_at' => 'datetime',
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
