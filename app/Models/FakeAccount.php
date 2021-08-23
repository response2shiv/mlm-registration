<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class FakeAccount extends Model
{
    //

    /**
     * Get sponsor information
     */
    public function getFakeUser($sponsor_id)
    {
        $sponsor = User::where('distid',$sponsor_id)->first();
        return [
            "username" => "wiqytev".\Carbon\Carbon::now()->timestamp,
            "password" => "1234567",
            "password_confirmation" => "1234567",
            "co_applicant" => "off",
            "co_applicant_first_name" => null,
            "co_applicant_last_name" => null,
            "co_applicant_email" => null,
            "co_applicant_country_code" => null,
            "co_applicant_mobile_number" => null,
            "tax_information" => "individual",
            "ein" => "",
            "business_name" => "Ralph Golden",
            "payment_type" => 1,
            "credit_card_name" => "Britanni Mcmahon",
            "credit_card_number" => "4111 1111 1111 1111",
            "expiry_date" => "04 / 22",
            "cvv" => "445",
            "birth_month" => "07",
            "birth_day" => "11",
            "birth_year" => "1980",
            "primary_address_line_one" => "716 Old Road",
            "primary_address_line_two" => null,
            "primary_city" => "Frisco",
            "primary_state" => "TX",
            "primary_postal_code" => "75035",
            "primary_country" => "US",
            "is_billing_same" => "yes",
            "billing_address_line_one" => null,
            "billing_address_line_two" => null,
            "billing_city" => null,
            "billing_state" => null,
            "billing_postal_code" => null,
            "billing_country" => null,
            "is_shipping_same" => "yes",
            "shipping_address_line_one" => null,
            "shipping_address_line_two" => null,
            "shipping_city" => null,
            "shipping_state" => null,
            "shipping_postal_code" => null,
            "shipping_country" => null,
            "country" => "1",
            "language" => "en",
            "firstname" => "Winifred",
            "lastname" => "Kelley",
            "email" => "wiqytev".\Carbon\Carbon::now()->timestamp."@gmail.com",
            "country_code" => "+1",
            "product_id" => "1",
            "mobile_number" => "9729263166",
            "sponsor" => $sponsor_id,
            "sponsor_username" => $sponsor->username,
            "phone_number" => "9729263166"
        ];
    }

    /**
     * Get address information
     */
    public function getFakeAddress()
    {
        return [
            "primary" => [
                "apt" => "",
                "address1" => "716 Old Road",
                "address2" => null,
                "city" => "Frisco",
                "state" => "TX",
                "postal_code" => "75035",
                "country" => "US",
                "billing_first_name" => "Winifred",
                "billing_last_name" => "Kelley",
                "payment_type" => 1
            ],
            "billing" => [
                "apt" => "",
                "address1" => "716 Old Road",
                "address2" => null,
                "city" => "Frisco",
                "state" => "TX",
                "postal_code" => "75035",
                "country" => "US",
                "billing_first_name" => "Winifred",
                "billing_last_name" => "Kelley",
                "payment_type" => 1
            ],
            "shipping" => [
                "apt" => "",
                "address1" => "716 Old Road",
                "address2" => null,
                "city" => "Frisco",
                "state" => "TX",
                "postal_code" => "75035",
                "country" => "US",
                "billing_first_name" => "Winifred",
                "billing_last_name" => "Kelley",
                "payment_type" => 1
            ]
        ];
    }

    /**
     * Get Card information
     */
    public function getFakeCreditCard()
    {
        return [
            "credit_card_number" => "4111111111111111",
            "cvv" => "445",
            "expiry_date_month" => "04",
            "expiry_date_year" => "2022",
            "order_total" => 0,
            "order_subtotal" => 49.95,
            "is_save" => null
        ];
    }

    /**
     * Get NMI Response information
     */
    public function getFakeNMIResponse()
    {
        return [
            "response" => [
                "Token" => "",
                "Authorization" => "COUPON#CFR9W6"
            ]
        ];
    }
}
