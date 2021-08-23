<?php

namespace App\Library\TMTService;

class TMTPayment
{
    private $url;

    private $userName;

    private $password;

    private $token;

    private $refreshToken;

    private $sites = [];

    public $channelList = [];

    public $bookingList = [];

    public $booking;

    private $permitedUrls;

    public $transaction;

    public $channel;

    /**
     * TMTPayment constructor.
     */
    public function __construct()
    {
        $this->url = env('TMT_URL');
        $this->userName = env('TMT_USERNAME');
        $this->password = env('TMT_PASSWORD');
        $this->setTokens();
        $this->getSiteList();
        $this->setSitePermission();
        $this->getChannel();
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getUrl()
    {
        return $this->url;
    }

    private function setTokens()
    {
        $options = array(
            'url' => $this->url . "/wp-json/jwt-auth/v1/token",
            'postFields' => "{\n  \"username\": \"" . $this->userName . "\",\n  \"password\": \"" . $this->password . "\"\n}",
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

    private function setTokenRefresh()
    {
        $options = array(
            'url' => $this->url . "/wp-json/jwt-auth/v1/token/refresh",
            'method' => 'POST',
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->refreshToken
            ),
        );
        $curlResponse = $this->getCurlResponse($options);

        if (isset($curlResponse['token'])) {

            $this->token = $curlResponse['token'];
            $this->refreshToken = $curlResponse['refresh_token'];
        }
    }

    private function getSiteList()
    {
        $options = array(
            'url' => $this->url . "/wp-json/tmt/v2/sites",
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

    private function setSitePermission()
    {
        $url = parse_url(rtrim(env('APP_URL'), '/'));
        $host = $url['host'];

        $permittedUrls[] = 'http://' . $host;
        $permittedUrls[] = 'https://' . $host;

        if (isset($this->sites[0])) {
            foreach ($this->sites[0]['permitted_urls'] as $url) {
                $permittedUrls[] = $url;
            }
        }
        $permittedUrlsString = '';
        $permittedUrls = array_unique($permittedUrls);
        foreach ($permittedUrls as $url) {
            $lastSymbol = $url != end($permittedUrls)? ',' : null;
            $permittedUrlsString = $permittedUrlsString . "\"" . $url . "\"".$lastSymbol."";
        }
        $options = array(
            'url' => $this->url . "/wp-json/tmt/v2/sites/" . $this->sites[0]['id'],
            'method' => 'PUT',
            'postFields' => "{\n\t\"permitted_urls\": [" . $permittedUrlsString . "]\n}",
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ),
        );
        $curlResponse = $this->getCurlResponse($options);

        if ($curlResponse) {
            $this->permitedUrls = $curlResponse['permitted_urls'];
        }

    }

    public function getChannelList()
    {
        $options = array(
            'url' => $this->url . "/wp-json/tmt/v2/channels/",
            'method' => 'GET',
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ),
        );
        $curlResponse = $this->getCurlResponse($options);
        if ($curlResponse) {
            foreach ($curlResponse as $channel) {
                $this->channelList[] = new TMTChanel($channel['id'], $channel['name'], $channel['currencies']);
            }
        }
    }

    public function getChannel()
    {
        $options = array(
            'url' => $this->url . "/wp-json/tmt/v2/channels/" . env('TMT_CHANNEL_ID'),
            'method' => 'GET',
            'headers' => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ),
        );
        $curlResponse = $this->getCurlResponse($options);
        if ($curlResponse) {
            $this->channel = new TMTChanel($curlResponse['id'], $curlResponse['name'], $curlResponse['currencies']);
        }
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
        $bookingData['channels'] = isset($this->channel) ? $this->channel->getId() : '';
        $bookingData['currencies'] = isset($this->channel) ? $this->channel->getCurrencies() : '';
        $bookingData['country'] = isset($data['country']) ? $data['country'] : '';
        $options = array(
            'url' => $this->url . "/wp-json/tmt/v2/bookings",
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
                "Authorization: Bearer " . $this->getToken()
            ),
        );

        $curlResponse = $this->getCurlResponse($options);

        if (isset($curlResponse['id'])) {
            $this->booking = new TMTBooking();
            $this->booking->setId($curlResponse['id']);
            $this->booking->setContent($curlResponse['id']);
            $this->booking->setFirstName($curlResponse['firstname']);
            $this->booking->setSurname($curlResponse['surname']);
            $this->booking->setEmail($curlResponse['email']);
            $this->booking->setDate($curlResponse['date']);
            $this->booking->setPax($curlResponse['pax']);
            $this->booking->setReference($curlResponse['reference']);
            $this->booking->setTotal($curlResponse['total']);
            $this->booking->setChannelId($curlResponse['channels']);
            $this->booking->setCountries($curlResponse['countries']);
            $this->booking->setCurrencies($curlResponse['currencies']);
        }

        return $this->booking;
    }

    public function createTransaction()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . "/wp-json/tmt/v2/transactions",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\n\t\"payee_name\": \"Bill Smith\",\n\t\"payee_email\": \"bill.smith@example.org\",\n\t\"countries\": \"GB\",\n    \"channels\": 2,\n    \"bookings\": [\n    \t{\n    \t\t\"id\": " . $this->booking->getId() . ",\n    \t\t\"currencies\": \"USD\",\n    \t\t\"total\": 1000\n    \t}\n\t],\n    \"currencies\": \"USD\",\n    \"total\": 1000,\n    \"psp\": \"test_psp\",\n    \"transaction_types\": \"purchase\",\n    \"payment_methods\": \"credit-card\",\n    \"token\": \"FINALTOKEN123\",\n    \"ip_address\": \"192.12.12.12\",\n    \"bin_number\": \"411111\"\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->getToken()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            $this->transaction = json_decode($response, true);

            return $this->transaction;
        }
    }

    function getCurlResponse($options = [])
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
            return false;
        }

        return json_decode($response, true);

    }
}