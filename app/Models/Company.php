<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'legacy_id',
        'token',
        'name',
        'email',
        'company_loyalty_percent',
    ];

    protected static function booted(): void
    {
        static::creating(function ($company) {
            if (empty($company->token)) {
                do {
                    $token = 'LCV2_' . \Illuminate\Support\Str::random(32);
                } while (self::where('token', $token)->exists());

                $company->token = $token;
            }
        });
    }
}
