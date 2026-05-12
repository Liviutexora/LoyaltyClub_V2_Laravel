<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'legacy_user_id',
    ];

    /**
     * Boot the model and add automatic qr_token generation.
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->qr_token)) {
                do {
                    $token = 'LCV2_' . \Illuminate\Support\Str::random(32);
                } while (self::where('qr_token', $token)->exists());
                $user->qr_token = $token;
            }
        });
    }


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
}
