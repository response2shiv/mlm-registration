<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodType extends Model
{

    public $timestamps = false;
    CONST TYPE_CREDIT_CARD = 9;
    CONST TYPE_ADMIN = 2;
    CONST TYPE_E_WALET = 3;
    CONST TYPE_BITPAY = 4;
    CONST TYPE_COUPON_CODE = 7;
    CONST TYPE_TRUST_MY_TRAVEL = 8;
    CONST TYPE_T1_PAYMENTS = 9;
    CONST TYPE_PAYARC = 11;  //Note 10 was skiped for consistency with myibuumerang repo
    CONST TYPE_METROPOLITAN = 12;
    CONST TYPE_UNICRYPT = 13;
    CONST TYPE_IPAYTOTAL = 14;

    public static function getPaymentMethodTypeByOrder($order)
    {
        $paymentMethod = DB::table('payment_methods')
            ->where('id', $order->payment_methods_id)
            ->first();

        if (!$paymentMethod) {
            return false;
        }

        return DB::table('payment_method_type')
            ->where('id', $paymentMethod->pay_method_type)
            ->first();
    }

    public static function getEnrollmentPaymentMethods()
    {
        $paymentMethodTypeId = array(
            PaymentMethodType::TYPE_CREDIT_CARD,
            PaymentMethodType::TYPE_BITPAY,
        );

        return $paymentMethod = DB::table('payment_method_type')
            ->whereIn('id', $paymentMethodTypeId)
            ->get();
    }

    public static function getTMTPaymentType()
    {
        return  DB::table('payment_method_type')
            ->whereIn('id', [
//                PaymentMethodType::TYPE_TRUST_MY_TRAVEL,
                PaymentMethodType::TYPE_BITPAY,
                PaymentMethodType::TYPE_T1_PAYMENTS,
            ])->get();
    }

}
