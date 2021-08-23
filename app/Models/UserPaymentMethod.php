<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\UserPaymentAddress;
use App\Models\UserPaymentMethodMerchant;
// use App\Models\UserPaymentMethod;
use App\Models\PreOrder;
use Log;

class UserPaymentMethod extends Model
{

    protected $fillable = [
        'user_id',
        'user_payment_address_id',
        'first_name',
        'last_name',
        'card_token',
        'is_primary',
        'active',
        'cvv',
        'expiration_month',
        'expiration_year',
        'pay_method_type'
    ];

    public function paymentAddress()
    {
        return $this->hasOne(UserPaymentAddress::class);
    }

    public static function convertAndSave($cardInfo, $addressInfo)
    {

        $card = UserPaymentMethod::where('card_token', $cardInfo['token'])->first();

        try {
            $payment_address = [
                'address1'     => $addressInfo['address1'],
                'address2'     => $addressInfo['address2'],
                'city'         => $addressInfo['city'],
                'state'        => $addressInfo['state'],
                'zipcode'      => $addressInfo['zip'],
                'country_code' => $addressInfo['country_code']
            ];

            $UserPaymentAddressId = UserPaymentAddress::create($payment_address);

            $user_payment_method = [
                'user_id' => \Auth::user()->id,
                'user_payment_address_id' => $UserPaymentAddressId,
                'first_name'       => $addressInfo['first_name'],
                'last_name'        => $addressInfo['last_name'],
                'card_token'       => $cardInfo['token'],
                'is_primary'       => 1,
                'active'           => 1,
                'expiration_month' => str_pad($cardInfo['expiration_month'], 2, '0', STR_PAD_LEFT),
                'expiration_year'  => $cardInfo['expiration_year'],
                'pay_method_type'  => \App\Models\PaymentMethodType::TYPE_IPAYTOTAL
            ];

            $user_payment_method_id = UserPaymentMethod::create($user_payment_method)->id;

            $user_payment_method_merchant = [
                'user_payment_method_id' => $user_payment_method_id,
                'merchant_id' => 2
            ];

            UserPaymentMethodMerchant::create($user_payment_method_merchant)->id;
        } catch (\Exception $e) {

            return null;
        }
    }
}
