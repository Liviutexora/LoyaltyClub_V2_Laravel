<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyUser extends Model
{
    protected $connection = 'legacy';
    protected $table = 'user';
    public $timestamps = false;
    protected $fillable = [
        'id', 'name', 'email', // adaugă alte câmpuri relevante dacă este nevoie
    ];
}
