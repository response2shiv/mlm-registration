<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantTransactionTracker extends Model
{
    protected $table = "merchant_transaction_tracker";

    public $fillable = [
        'merchant_id',
        'pre_order_id',
        'transaction_id',
        'status',
        'cron_processed',
        'created_at',
        'updated_at'
    ];
}
