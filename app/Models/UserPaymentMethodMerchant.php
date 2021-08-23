<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPaymentMethodMerchant extends Model
{
    protected $table = 'user_payment_method_merchants';
    protected $fillable = [
        'user_payment_method_id',
        'merchant_id'
    ];
}
