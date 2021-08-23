<?php

class SaveOn
{

    private $serviceURL;
    private $username;
    private $secret;
    private $type;

    public function __construct($level, $type)
    {
        $this->type = $type;

        $mode = Config::get('const.save_on.mode');

        $this->serviceURL = Config::get('const.save_on.' . $mode . '.service_url');
        $this->username = Config::get('const.save_on.' . $mode . '.' . $level . '.login');
        $this->secret = Config::get('const.save_on.' . $mode . '.' . $level . '.password');
    }

    public function post($endPoint, $postData, $version)
    {
        $headers = array(
            'Content-Type' => 'application/' . $this->type,
            "x-saveon-secret" => $this->secret,
            "x-saveon-username" => $this->username
        );

        $client = new \GuzzleHttp\Client();
        $options = [
            'headers' => $headers,
            'json' => $postData
        ];

        if ($version) {
            $response = $client->post($this->serviceURL . 'v2/' . $endPoint, $options);
        } else {
            $response = $client->post($this->serviceURL . $endPoint, $options);
        }

        return (string)$response->getBody();
    }


}
