<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\EmailVerificationNotification;
use App\Notifications\ForgotPasswordNotification;
use App\Notifications\WelcomeNotification;
use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User
 *
 * @property string $id
 * @property string $full_name
 * @property string $username
 * @property string|null $avatar
 * @property string $email
 * @property string $password
 * @property string $organisation_id
 * @property string $bio
 * @property string $status
 * @property string $password
 * @property string $otp
 * @property bool $email_notifications_enabled
 * @property bool $push_notifications_enabled
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $last_login
 * @property \Illuminate\Support\Carbon|null $otp_expires_at
 * @property \Illuminate\Support\Carbon|null $otp_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUlids;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }



    public function hasWorkspace(): bool
    {
        return $this->organisation()->exists();
    }

    public function sendEmailVerificationNotification()
    {
        $this->update([
            'otp' => generateRandom(6),
            'otp_expires_at' => now()->addMinutes(30),
        ]);
        $this->notify(new EmailVerificationNotification());
    }

    public function verifyEmail(): void
    {
        $this->update([
            'email_verified_at' => now(),
            'otp' => null,
            'otp_expires_at' => null,
        ]);
        $this->notify(new WelcomeNotification());
    }

    public function updateLastLogin()
    {
        $this->update([
            'last_login' => now(),
        ]);
    }

    public function generatePassswordResetToken()
    {
        $this->update([
            'reset_token' => generateRandom(32),
            'reset_token_expires_at' => now()->addMinutes(30),
        ]);
        $this->notify(new ForgotPasswordNotification());
    }
}
