<?php

namespace App\Services;

use App\Models\Addresses;
use App\Models\User;

class MailChimp
{
    private const MAILCHIMP_SUBSCRIBE_URL = "https://us20.api.mailchimp.com/3.0/lists/%s/members";

    protected $audienceId;
    protected $productId;

    /** @var array $data */
    protected $data;

    public function __construct(string $audienceId, $productId)
    {
        $this->audienceId = $audienceId;
        $this->productId = $productId;

    }

    public function subscribe() {
        $response = (new \GuzzleHttp\Client())->post(sprintf(self::MAILCHIMP_SUBSCRIBE_URL, $this->audienceId), [
            'auth' => ['apiKey', env('MAILCHIMP_API_KEY')],
            'json' => $this->data,
            'http_errors' => false
        ]);

        $statusCode = $response->getStatusCode();

        // Any 200 status code is good, otherwise we assume something went wrong
        return $statusCode >= 200 && $statusCode < 300;
    }

    /**
     * @param User $user
     * @return MailChimp
     */
    public function buildMasterAudienceData($user)
    {
        $this->data = [
            'email_address' => $user->email,
            'email_type' => 'html',
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $user->firstname,
                'LNAME' => $user->lastname,
                'USERNAME' => $user->username,
                'PHONE' => $user->phonenumber,
                'CITY' => $user->userAddress->city,
                'STATEPROV' => $user->userAddress->stateprov,
                'COUNTRY' => $user->userAddress->countrycode,
                'ZIP' => $user->userAddress->postalcode,
                'TSA_NUMBER' => $user->distid,
                'PRODID' => $this->productId,
                'FOUNDER' => $user->founder === 1 ? 'Yes' : 'No'
            ],
            'ip_signup' => request()->ip(),
            'timestamp_signup' => date('c')
        ];

        return $this;
    }

    /**
     * @param User $enrollee
     * @param Addresses $address
     * @return MailChimp
     */
    public function buildSponsorAudienceData($enrollee)
    {
        /** @var User $sponsor */
        $sponsor = User::where('distid', $enrollee->sponsorid)->first();

        $this->data = [
            'email_address' => $sponsor->email,
            'email_type' => 'html',
            'status' => 'subscribed',
            'merge_fields' => [
                'EMAIL' => $sponsor->email,
                'SDISTID' => $sponsor->distid,
                'SFNAME' => $sponsor->firstname,
                'SLNAME' => $sponsor->lastname,
                'SPHONE' => $sponsor->phonenumber,
                'SCCODE' => $sponsor->country_code,
                'SPCODE' => $sponsor->userAddress->postalcode,
                'EDISTID' => $enrollee->distid,
                'EFNAME' => $enrollee->firstname,
                'ELNAME' => $enrollee->lastname,
                'EEMAIL' => $enrollee->email,
                'EPHONE' => $enrollee->phonenumber,
                'ECCODE' => $enrollee->country_code,
                'EPCODE' => $enrollee->userAddress->postalcode,
            ],
            'ip_signup' => request()->ip(),
            'timestamp_signup' => date('c')
        ];

        return $this;
    }
}
