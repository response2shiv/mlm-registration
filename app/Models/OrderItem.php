<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'orderItem';

    protected $fillable = [
        'orderid',
        'productid',
        'quantity',
        'itemprice',
        'bv',
        'qv',
        'cv',
        'created_date',
        'created_time',
        'created_dt'
    ];

    protected $primaryKey = 'id';

    public $timestamps = false;
}
