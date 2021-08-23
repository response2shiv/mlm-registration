<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
    const TYPE_BILLING = 1;
    const TYPE_SHIPPING = 2;
    const TYPE_REGISTRATION = 3;

    protected $table = 'addresses';

    protected $fillable = [
        'userid',
        'addrtype',
        'primary',
        'apt',
        'address1',
        'address2',
        'city',
        'stateprov',
        'stateprov_abbrev',
        'postalcode',
        'countrycode',
        'countrycode',
        'is_save'
    ];

    protected $primaryKey = 'id';

    public $timestamps = false;

    public static function getAddress($userId){
        return self::where('userid',$userId)
            ->first();
    }
}
