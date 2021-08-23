<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoomerangHistory extends Model
{
    protected $table = 'boomerang_history';

    protected $fillable = [
        'id',
        'user_id',
        'opening_boomerangs',
        'closing_boomerangs',
        'num_boomerangs',
        'remark',
        'created_at'
    ];

    protected $primaryKey = 'id';

    public $timestamps = false;
}
