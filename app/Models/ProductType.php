<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model {

    protected $table = "producttype";

    const TYPE_ENROLLMENT = 1;
    const TYPE_UPGRADE = 2;
    const TYPE_MEMBERSHIP = 3;
    const TYPE_BOOMERANG = 4;
    const TYPE_PRE_PAID_CODES = 5;
    const TYPE_TICKET = 6;
    const TYPE_DONATION = 7;

}
