<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethods extends Model
{

    protected $table = 'payment_methods';

    protected $fillable = [
        'userID',
        'primary',
        'token',
        'expMonth',
        'expYear',
        'cvv',
        'firstname',
        'lastname',
        'bill_addr_id',
        'pay_method_type',
        'is_save'
    ];

    protected $primaryKey = 'id';
}


