<?php

namespace App\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

class LegacyTicket extends Model
{
    protected $connection = 'legacy';
    protected $table = 'tickets';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
