<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmtPayment extends Model
{
    const URL = 'https://tmtprotects.com/ibuumerang';
    const USER_NAME = 'ibuumerangadmin';
    const PASSWORD = 'oH:3Z?>~3#54cQh4H6Pi,/kPe.AB=Z}=';

    private $token;

    private $refreshToken;

    private $sites = [];

    private $channels = [];

    private $bookingList = [];

    private $booking;

    /**
     * TmtPayment constructor.
     */
    public function __construct()
    {
        $this->setTokens();
    }

    /**
     * @return mixed
     */
    public function setTokens()
    {
        $options = array(
            'url' => self::URL . "/wp-json/jwt-auth/v1/token",
            'postFields' => "{\n  \"username\": \"" . self::USER_NAME . "\",\n  \"password\": \"" . self::PASSWORD . "\"\n}",
            'method' => 'POST',
            'headers' => array(
                "Content-Type: application/json"
            ),
        );
        $curlResponse = $this->getCurlResponse($options);


        if (isset($curlResponse['token'])) {

            $this->token = $curlResponse['token'];
            $this->refreshToken = $curlResponse['refresh_token'];
        }
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function setNewRefreshToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::URL . "/wp-json/jwt-auth/v1/token/refresh",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->refreshToken
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        }

        $responseData = json_decode($response, true);

        if (isset($responseData['refreshToken'])) {
            $this->refreshToken = $responseData['refreshToken'];
        }
    }

    public function getListSites()
    {
        $options = array(
            'url' => self::URL . "/wp-json/tmt/v2/sites",
            'method' => 'GET',
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ),
        );
        $curlResponse = $this->getCurlResponse($options);

        if (count($curlResponse)) {
            $this->sites = $curlResponse;
        }

        return $this->sites;
    }

    public function getChannelsList()
    {
        $options = array(
            'url' => self::URL . "/wp-json/tmt/v2/channels/",
            'method' => 'GET',
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ),
        );
        $curlResponse = $this->getCurlResponse($options);

        if ($curlResponse) {
            $this->channels = $curlResponse;
        }
        return $this->channels;
    }

    public function updateSites()
    {
        $siteId = isset($this->sites[0]['id']) ? $this->sites[0]['id'] : null;

        $options = array(
            'url' => self::URL . "/wp-json/tmt/v2/sites/" . $siteId,
            'postFields' => "{\n\t\"permitted_urls\": [ \"http://enrollment.local\"]\n}",
            'method' => 'PUT',
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ),
        );
        $this->getCurlResponse($options);
    }

    public function getBookingList()
    {
        $options = array(
            'url' => self::URL . "/wp-json/tmt/v2/bookings",
            'method' => 'GET',
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ),
        );
        $curlResponse = $this->getCurlResponse($options);

        if ($curlResponse) {
            $this->bookingList = $curlResponse;
        }

        return $this->bookingList;
    }

    public function createBooking($data)
    {
        $bookingData['content'] = isset($data['content']) ? $data['content'] : '';
        $bookingData['firstname'] = isset($data['firstname']) ? $data['firstname'] : '';
        $bookingData['surname'] = isset($data['surname']) ? $data['surname'] : '';
        $bookingData['email'] = isset($data['email']) ? $data['email'] : '';
        $bookingData['date'] = date('Y-m-d');
        $bookingData['pax'] = isset($data['pax']) ? $data['pax'] : '';
        $bookingData['reference'] = isset($data['reference']) ? $data['reference'] : '';
        $bookingData['total'] = isset($data['total']) ? $data['total'] : '';
        $bookingData['channels'] = isset($this->channels[0]['id']) ? $this->channels[0]['id'] : '';
        $bookingData['currencies'] = isset($this->channels[0]['currencies']) ? $this->channels[0]['currencies'] : '';
        $bookingData['country'] = isset($data['country']) ? $data['country'] : '';
        $options = array(
            'url' => self::URL . "/wp-json/tmt/v2/bookings",
            'postFields' => "{\n    \"channels\": " . $bookingData['channels'] . ",\n" .
                "\"content\": \"" . $bookingData['content'] . "\",\n" .
                "\"firstname\": \"" . $bookingData['firstname'] . "\",\n" .
                "\"surname\": \"" . $bookingData['surname'] . "\",\n " .
                "\"email\": \"" . $bookingData['email'] . "\",\n" .
                "\"date\": \"" . $bookingData['date'] . "\",\n" .
                "\"pax\": " . $bookingData['pax'] . ",\n" .
                "\"reference\": \"" . $bookingData['reference'] . "\",\n" .
                "\"total\": " . $bookingData['total'] . ",\n" .
                "\"currencies\": \"" . $bookingData['currencies'] . "\",\n" .
                "\"countries\": \"" . $bookingData['country'] . "\"\n}",
            'method' => 'POST',
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ),
        );

        $curlResponse = $this->getCurlResponse($options);

        if ($curlResponse) {
            $this->booking = $curlResponse;
        }
        return $this->booking;
    }

    public function getBookingById($id)
    {

    }

    private function getCurlResponse($options = [])
    {
        $curl = curl_init();
        $url = isset($options['url']) ? $options['url'] : '';
        $method = isset($options['method']) ? $options['method'] : '';
        $postFields = isset($options['postFields']) ? $options['postFields'] : '';
        $headers = isset($options['headers']) ? $options['headers'] : [];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => 'CURL_HTTP_VERSION_1_1',
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => $headers
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        }

        return json_decode($response, true);

    }


}