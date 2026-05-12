<?php

namespace App\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

class LegacyUser extends Model
{
    protected $connection = 'legacy';
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
