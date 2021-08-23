<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'userid',
        'statuscode',
        'ordersubtotal',
        'ordertax',
        'ordertotal',
        'orderbv',
        'orderqv',
        'ordercv',
        'trasnactionid',
        'payment_type_id',
        'payment_methods_id',
        'shipping_address_id',
        'inv_id',
        'created_date',
        'created_time',
        'created_dt'
    ];

    protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * @return HasMany
     */
    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItem', 'orderid', 'id');
    }

    public function currency()
    {
        return $this->hasOne('App\Models\OrderConversion');
    }
}
