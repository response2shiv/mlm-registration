<?php

class  TokenEx
{
    private $apiKey;
    private $tokenExId;
    private $serviceURL;
    private $headers;


    public function __construct()
    {
        $mode = Config::get('const.token_ex.mode');

        $this->serviceURL = Config::get('const.token_ex.' . $mode . '.service_url');
        $this->apiKey = Config::get('const.token_ex.' . $mode . '.api_key');
        $this->tokenExId = Config::get('const.token_ex.' . $mode . '.id');

        $this->headers = array(
            'Content-Type' => 'application/json'
        );
    }

    public function tokenize($endPoint, $data)
    {

        $postData ["APIKey"] = $this->apiKey;
        $postData ["TokenExID"] = $this->tokenExId;
        $postData ["Data"] = $data;
        $postData ["TokenScheme"] = 1;
        $client = new \GuzzleHttp\Client(["base_uri" => $this->serviceURL . 'TokenServices.svc/REST/']);
        $options = [
            'headers' => $this->headers,
            'json' => $postData
        ];
        $response = $client->post($endPoint, $options);
        return (string)$response->getBody();
    }


    public function detokenize($endPoint, $token)
    {
        $postData ["APIKey"] = $this->aoiKey;
        $postData ["TokenExID"] = $this->tokenExId;
        $postData ["Token"] = $token;
        $client = new \GuzzleHttp\Client(["base_uri" => $this->serviceURL . 'TokenServices.svc/REST/']);
        $options = [
            'headers' => $this->headers,
            'json' => $postData
        ];
        $response = $client->post($endPoint, $options);
        return (string)$response->getBody();
    }


    public function processTransactionAndTokenize($endPoint, $postData)
    {
        $postData ["APIKey"] = $this->apiKey;
        $postData ["TokenExID"] = $this->tokenExId;
        $postData ["TokenScheme"] = 3;
        $client = new \GuzzleHttp\Client(["base_uri" => $this->serviceURL . '/PaymentServices.svc/REST/']);
        $options = [
            'headers' => $this->headers,
            'json' => $postData
        ];
        $response = $client->post($endPoint, $options);
        return (string)$response->getBody();
    }

    public function getKountHashValue($endPoint, $token)
    {
        $postData = array(
            'TokenExID' => $this->tokenExId,
            'APIKey' => $this->apiKey,
            'Token' => $token,
        );

        $client = new \GuzzleHttp\Client(["base_uri" => $this->serviceURL . '/FraudServices.svc/REST/']);
        $options = [
            'headers' => $this->headers,
            'json' => $postData
        ];
        $response = $client->post($endPoint, $options);
        return (string)$response->getBody();
    }


    public function getKountHashValueAndTokenize($endPoint, $data)
    {
        $postData = array(
            'TokenExID' => $this->tokenExId,
            'APIKey' => $this->apiKey,
            'Data' => $data,
            'Encrypted' => false,
            'TokenScheme' => 1
        );

        $client = new \GuzzleHttp\Client(["base_uri" => $this->service_url . '/FraudServices.svc/REST/']);
        $options = [
            'headers' => $this->headers,
            'json' => $postData
        ];

        $response = $client->post($endPoint, $options);
        return (string)$response->getBody();
    }
}