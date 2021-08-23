<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPaymentMethod;
use App\Models\UserPaymentAddress;
use App\Models\UserPaymentMethodMerchant;
use Illuminate\Support\Facades\DB;

class UserPaymentMethodService
{

    public static function createUserPaymentMethod($billingResponse, $cardInfo, $addressInfo, $userId)
    {

        try {
            $payment_address = [
                'address1'     => $addressInfo['address1'],
                'address2'     => $addressInfo['address2'],
                'city'         => $addressInfo['city'],
                'state'        => $addressInfo['state'],
                'zipcode'      => $addressInfo['postal_code'],
                'country_code' => $addressInfo['country']
            ];

            $UserPaymentAddressId = UserPaymentAddress::create($payment_address);

            $user_payment_method = [
                'user_id' => $userId,
                'user_payment_address_id' => $UserPaymentAddressId->id,
                'first_name'       => $addressInfo['billing_first_name'],
                'last_name'        => $addressInfo['billing_last_name'],
                'card_token'       => $billingResponse['token'],
                'is_primary'       => 1,
                'active'           => 1,
                'expiration_month' => str_pad($cardInfo['expiry_date_month'], 2, '0', STR_PAD_LEFT),
                'expiration_year'  => $cardInfo['expiry_date_year'],
                'pay_method_type'  => \App\Models\PaymentMethodType::TYPE_IPAYTOTAL
            ];

            $user_payment_method_id = UserPaymentMethod::create($user_payment_method)->id;

            $user_payment_method_merchant = [
                'user_payment_method_id' => $user_payment_method_id,
                'merchant_id' => 2
            ];

            UserPaymentMethodMerchant::create($user_payment_method_merchant)->id;

            return $user_payment_method_id;
            
        } catch (\Exception $e) {

            return null;
        }
    }
}
