<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    public static function checkTMTAllowPayment($countryId)
    {
        return count(DB::table('payment_type_country')
            ->where('country_id', '=', $countryId)
            ->get());
    }
}
