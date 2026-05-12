<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyUser extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'qr_token',
    ];
}
