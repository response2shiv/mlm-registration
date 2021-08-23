<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoungeQueue extends Model
{
    protected $table = 'lounge_queue';

    protected $fillable = [
        'user_id',
        'sponsor_id',
        'is_assigned'
    ];
}
