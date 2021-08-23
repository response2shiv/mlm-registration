<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use phpDocumentor\Reflection\DocBlock\Serializer;

use GuzzleHttp\Exception\ClientException;

class Billing
{
    private const PROCESS_API_URL = 'v1/api/payment/create-and-process';

    public function __construct()
    {

    }

    public static function iPayTotal($sponsorInfo, $cardInfo, $addressInfo, $userId, $orderTotal, $productInfo, $ticketProduct = null)
    {

        $preOrder = \App\Models\PreOrder::query()->where('userid', $userId)->orderBy('id', 'DESC')->first();

        $baseUrl = env('BILLING_BASE_URL');
        $apiToken = env('BILLING_API_TOKEN');

        $client = new Client();
        $baseUrl = 'https://' . $baseUrl . '/';

        $orderDesc = $productInfo->productdesc;

        if ($ticketProduct != null) {
            $orderDesc .= ' and ' . $ticketProduct->productdesc;
        }

        try {
            $result = $client->post($baseUrl . self::PROCESS_API_URL, [
                'form_params' => [
                    'first_name' => $sponsorInfo['firstname'],
                    'last_name' => $sponsorInfo['lastname'],
                    'card_number' => $cardInfo['credit_card_number'],
                    'expiration_month' => $cardInfo['expiry_date_month'],
                    'expiration_year' => $cardInfo['expiry_date_year'],
                    'cvv' => $cardInfo['cvv'],
                    'ip_address' => static::determineIPAddress(),
                    'order_id' => $preOrder->id,
                    'order_desc' => $orderDesc,
                    'amount' => $orderTotal,
                    'tax' => 0,
                    'shipping' => 0,
                    'currency' => 'USD', // For the moment, we need send USD to the new merchant #session('billing_country')->currency,
                    'address1' =>isset($addressInfo['primary']) ? $addressInfo['primary']['address1'] : null,
                    'city' => isset($addressInfo['primary']) ? $addressInfo['primary']['city'] : null,
                    'state' => isset($addressInfo['primary']) ? $addressInfo['primary']['state'] : null,
                    'zip' => isset($addressInfo['primary']) ? $addressInfo['primary']['postal_code'] : null,
                    'country' => isset($addressInfo['primary']) ? $addressInfo['primary']['country'] : null,
                    'phone' => $sponsorInfo['phone_number'],
                    'email' => strtolower(trim($sponsorInfo['email'])),
                    'shipping_first_name' => $sponsorInfo['firstname'],
                    'shipping_last_name' => $sponsorInfo['lastname'],
                    'shipping_address1' => isset($addressInfo['billing']) ? $addressInfo['billing']['address1'] : $addressInfo['primary']['address1'],
                    'shipping_city' => isset($addressInfo['billing']) ? $addressInfo['billing']['city'] : $addressInfo['primary']['city'],
                    'shipping_state' => isset($addressInfo['billing']) ? $addressInfo['billing']['state'] : $addressInfo['primary']['state'],
                    'shipping_zip' => isset($addressInfo['billing']) ? $addressInfo['billing']['postal_code'] : $addressInfo['primary']['postal_code'],
                    'shipping_country' => isset($addressInfo['billing']) ? $addressInfo['billing']['country'] : $addressInfo['primary']['country'],
                    'shipping_phone' => $sponsorInfo['phone_number'],
                    'shipping_email' => strtolower(trim($sponsorInfo['email'])),
                    '3d_redirect_url' => env('APP_URL').'/payment/callback',
                    'merchant_rotation_id' => env('BILLING_MERCHANT_ROTATION_ID')
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'timeout' => 60
                ]
            ]);

            $responseJson = $result->getBody()->getContents();
            return json_decode($responseJson, true);

        } catch (Exception $e) {
            return $e;
        }
    }

    public static function determineIPAddress()
    {
        $xForwardedFor = request()->header('X-Forwarded-For');
        $ipAddresses = explode(',', $xForwardedFor);

        foreach ($ipAddresses as $ipAddress) {
            $ip = filter_var(trim($ipAddress), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
            if ($ip != false) {
                return $ip;
            }
        }
    }
}
