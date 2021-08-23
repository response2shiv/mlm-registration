<?php

class  iDecideInteractive
{
    private $apiUser;
    private $apiKey;
    private $serviceUrl;
    private $live;

    public function __construct()
    {
        $mode = Config::get('const.idecide.mode');

        $this->apiUser = Config::get('const.idecide.' . $mode . '.api_user');
        $this->apiKey = Config::get('const.idecide.' . $mode . '.api_key');
        $this->serviceUrl = Config::get('const.idecide.service_url');
    }

    public function get($endPoint)
    {
        $headers = array(
            'Content-Type' => 'application/json'
        );
        $query["apiUser"] = $this->apiUser;
        $query["apiKey"] = $this->apiKey;

        $client = new \GuzzleHttp\Client();
        $options = [
            'headers' => $headers,
            'query' => http_build_query($query)
        ];
        $response = $client->get($this->serviceUrl . $this->live . $endPoint, $options);
        return (string)$response->getBody();
    }

    public function post($postData, $endPoint)
    {
        $headers = array(
            'Content-Type' => 'application/json'
        );
        $query["apiUser"] = $this->apiUser;
        $query["apiKey"] = $this->apiKey;
        $client = new \GuzzleHttp\Client();
        $options = [
            'headers' => $headers,
            'query' => http_build_query($query),
            'json' => $postData
        ];

        $response = $client->post($this->serviceUrl . $endPoint, $options);
        return (string)$response->getBody();
    }

}
