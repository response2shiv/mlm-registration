<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderConversion extends Model
{
    protected $table = "order_conversions";

    public $fillable = [
        'order_id',
        'original_amount',
        'original_currency',
        'converted_amount',
        'converted_currency',
        'exchange_rate',
        'created_at',
        'updated_at',
        'expires_at'
    ];

    public $dates = [
        'expires_at'
    ];

    public function order()
    {
        return $this->belongsTo('App\Orders');
    }

    public static function setOrderId($orderConversionId, $orderId)
    {
        $preOrder = \App\Models\PreOrder::find($orderId);

        $orderConversion = static::query()->find($orderConversionId);

        if($orderConversion){
            if($preOrder){
                $orderConversion->pre_order_id = $orderId;
            }else{
                $orderConversion->order_id = $orderId;
            }
            
            $orderConversion->save();
        }        
    }
}
