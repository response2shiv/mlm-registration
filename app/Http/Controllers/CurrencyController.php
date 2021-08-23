<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderConversion;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    private const CONVERT_API_URL = 'v1/api/currency/convert';
    private $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    private function getConvertValidator()
    {
        $rules = [
            'amount' => 'required|integer',
            'country' => 'required|string|size:2'
        ];

        return Validator::make(request()->only(['amount', 'country']), $rules);
    }

    private function convert($baseUrl, $apiToken, $amount, $country, $locale)
    {
        $baseUrl = 'https://' . $baseUrl . '/';

        try {
            $result = $this->guzzleClient->get($baseUrl . self::CONVERT_API_URL, [
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

    private function determineLocale()
    {
        $localePreferences = explode(",",Request::server('HTTP_ACCEPT_LANGUAGE', 'en_US,'));

        return $localePreferences[0];
    }

    private function determineCountry()
    {
        return Request::server('GEOIP_COUNTRY_CODE', 'US');
    }

    public function convertPassthrough()
    {
        $baseUrl = env('BILLING_BASE_URL') ;
        $apiToken = env('BILLING_API_TOKEN');

        if (!$baseUrl || !$apiToken) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'server' => 'An internal error has occurred'
                ],
                'amount' => -1,
                'display_amount' => 'N/A'
            ])->setStatusCode(500);
        }

        $validator = $this->getConvertValidator();

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray(),
                'amount' => -1,
                'display_amount' => 'N/A'
            ])->setStatusCode(400);
        }

        $amount = request()->get('amount');
        $country = request()->get('country');
        $locale = $this->determineLocale();

        $response = $this->convert($baseUrl, $apiToken, $amount, $country, $locale);

        // JSON is null or issue with
        if (!$response) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'server' => 'An internal error has occurred'
                ],
                'amount' => -1,
                'display_amount' => 'N/A'
            ])->setStatusCode(500);
        }

        if ($response['success'] !== 1) {
            $errors = $response['errors'];

            return response()->json([
                'success' => 0,
                'errors' => $errors,
                'amount' => -1,
                'display_amount' => 'N/A'
            ])->setStatusCode(400);
        }

        $orderConversion = new OrderConversion();

        $orderConversion->fill([
            'original_amount' => $amount,
            'original_currency' => 'USD',
            'converted_amount' => $response['amount'],
            'converted_currency' => $response['currency'],
            'exchange_rate' => $response['exchange_rate'],
            'expires_at' => now()->addMinutes(30)
        ]);

        $orderConversion->save();

        $response['order_conversion_id'] = $orderConversion->id;
        $response['expiration'] = $orderConversion->expires_at->timestamp;

        return response()->json($response);
    }

}





