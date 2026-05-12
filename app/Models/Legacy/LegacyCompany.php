<?php

namespace App\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

class LegacyCompany extends Model
{
    protected $connection = 'legacy';
    protected $table = 'firma';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
