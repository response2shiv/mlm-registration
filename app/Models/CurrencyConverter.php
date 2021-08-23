<?php

namespace App\Models;

use App\Http\Controllers\Controller;
use App\Models\OrderConversion;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Log;


class CurrencyConverter
{
    private const CONVERT_API_URL = 'v1/api/currency/convert';

    public function __construct()
    {
        parent::__construct();
    }

    private function getConvertValidator()
    {
        $rules = [
            'amount' => 'required|integer',
            'country' => 'required|size:2|string',
            'locale' => 'required|regex:/^[a-z]{2}_[A-Z]{2}$/'
        ];

        return Validator::make(request()->only(['amount', 'country', 'locale']), $rules);
    }

    public static function convert($baseUrl, $apiToken, $amount, $country, $locale)
    {
        $client = new Client();
        $baseUrl = 'https://' . $baseUrl . '/';



        Log::info("Query", [
            'type' => 'country',
            'amount' => $amount,
            'country' => $country,
            'locale' => $locale
        ]);

        Log::info("Billing URL -> " . $baseUrl);
        try {
            $result = $client->get($baseUrl . self::CONVERT_API_URL, [
                'query' => [
                    'type' => 'country',
                    'amount' => $amount,
                    'country' => $country,
                    'locale' => $locale
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken
                ]
            ]);

            $responseJson = $result->getBody()->getContents();
            return json_decode($responseJson, true);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function convertCurrency($amount, $country = null, $locale = null)
    {

        $baseUrl = env('BILLING_BASE_URL');
        $apiToken = env('BILLING_API_TOKEN');

        $client = new Client();
        $baseUrl = 'https://' . $baseUrl . '/';

        $country = $country ? $country : "USD";
        $locale = $locale ? $locale : "en_US";



        // Log::info("Query",[
        //             'type' => 'country',
        //             'amount' => $amount,
        //             'country' => $country,
        //             'locale' => $locale
        //         ]);

        // Log::info("Billing URL -> ".$baseUrl);
        try {
            $result = $client->get($baseUrl . self::CONVERT_API_URL, [
                'query' => [
                    'amount' => $amount,
                    'currency' => $country,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken
                ]
            ]);

            $responseJson = $result->getBody()->getContents();
            return json_decode($responseJson, true);
        } catch (Exception $e) {
            return null;
        }
    }
}
