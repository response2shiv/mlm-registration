<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;

class DiscountCoupon extends Model
{
    protected $table = 'discount_coupon';

    protected $fillable = [
        'code',
        'is_used',
        'created_at',
        'used_by',
        'discount_amount',
        'is_active'
    ];

    public $timestamps = false;

    public static function getCouponCode($couponCode)
    {
        return \App\Models\DiscountCoupon::where('code', $couponCode)
            ->where('is_active', 1)
            ->where('is_used', 0)->first();
    }

    public static function getDiscountAmount($code)
    {
        $rec = DB::table('discount_coupon')
            ->select('discount_amount')
            ->where('code', $code)
            ->where('is_used', 0)
            ->where('is_active', 1)
            ->first();
        if (empty($rec))
            return 0;
        else
            return $rec->discount_amount;
    }


}
