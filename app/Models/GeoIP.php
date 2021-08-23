<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class GeoIP extends Model
{
    private $guzzleClient;
    private $enpoint;
    private $apikey;

    public function __construct()
    {
        $this->guzzleClient = new Client();
        $this->endpoint = "https://ip-geo-location.p.rapidapi.com";
        $this->apikey   = "9f65053febmshbb55509705f18adp1e99cajsn5e2e0654a78a";
    }

    public function getCountryFromIP($ip){
        
        $baseUrl = $this->endpoint . '/ip/'.$ip;

        try {
            $result = $this->guzzleClient->get($baseUrl, [
                'query' => [
                    'format' => 'json'
                ],
                'headers' => [
                    'x-rapidapi-host' => 'ip-geo-location.p.rapidapi.com',
                    'x-rapidapi-key' => '9f65053febmshbb55509705f18adp1e99cajsn5e2e0654a78a',
                    'useQueryString' => true
                ]
            ]);

            $responseJson = $result->getBody()->getContents();
            $response = json_decode($responseJson, true);
            if(isset($response['country']['code'])){
                return $response['country']['code'];
            }else{
                return "US";
            }            
        } catch (Exception $e) {
            return "US";
        }
    }
}
