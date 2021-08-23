<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use phpDocumentor\Reflection\DocBlock\Serializer;

use GuzzleHttp\Exception\ClientException;

class ApiHelper
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * ApiHelper constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Makes a post request.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $form_params
     * @param array $custom_headers
     * @return \Exception|\Psr\Http\Message\ResponseInterface
     */
    public static function request(string $method, string $endpoint, array $form_params, array $custom_headers = [])
    {
        $self = new self();

        $url = self::buildUrl($endpoint);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . session()->get('token')
        ];

        if (count($custom_headers) > 0) {
            $headers = $custom_headers;
        }

        if (strtoupper($method) === 'GET') {
            $query_string = implode('/', $form_params);
            $url = $url . '/' . $query_string;
        }

        try {
            $response = $self->client->request($method, $url, [
                'headers' => $headers,
                'json' => $form_params
            ]);

        } catch (Exception $e) {
            abort(403, 'Unauthorized action. Token expired!');
        }

        return $response;
    }

    /**
     * Builds a string with the api host and the endpoint.
     *
     * @param string $endpoint
     * @return string
     */
    protected static function buildUrl(string $endpoint): string
    {
        return config('api.host') . config('api.uri') . $endpoint;
    }
}
