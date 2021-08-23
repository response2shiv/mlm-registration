<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Twilio\Rest\Client;

class Twilio extends Model {

    /**
     * @param $mobile
     * @param $message
     * @return array
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public static function sendSMS($mobile, $message) {
        $sid   = 'AC9e6cb008b3daf41f0ac81e0698117fd7';
        $token = '6e2f6471dbf99ef91bf9ad7b1aace792';
        $from  = '+13212332212';

        // init the twilio client
        $twilio = new Client($sid, $token);
        try {
            $response = $twilio->messages->create($mobile, [
                'from' => $from,
                'body' => $message
                ]
            );

            return [
                'status' => 'success',
                'sid' => (string) $response->sid,
                'sms_status' => (string) $response->status,
                'msg' => ''
            ];
        } catch (\Exception $ex) {
            return ['status' => 'error', 'msg' => $ex->getMessage()];
        }
    }

    /**
     * @param string $sponsorTsa
     * @param string $userId
     */
    public static function sendEnrollmentSuccessMessage($sponsorTsa, $userId)
    {
        try {
            if (!$sponsor = \App\Models\User::where('distid', $sponsorTsa)->first()) {
                throw new \Exception('Unable to find sponsor with TSA #' . $sponsorTsa);
            }
            if (!$distributor = \App\Models\User::where('id', $userId)->first()) {
                throw new \Exception('Unable to find user with id #' . $userId);
            }

            $phoneNumber = $sponsor->mobile_number;
            if (empty($phoneNumber)) {
                $phoneNumber = $sponsor->phonenumber;
            }
            if (empty($phoneNumber)) {
                throw new \Exception('Unable to find the mobile number for sponsor with user id #' . $sponsor->id);
            }

            // create message
            $message = "You have a new team member \r\n";
            $message .= "$distributor->firstname $distributor->lastname \r\n";
            $message .= "$distributor->email \r\n";

            if (!empty($distributor->phonenumber)) {
                $message .= "Tel: $distributor->phonenumber \r\n";
            }
            if (!empty($distributor->mobile_number)) {
                $message .= "Mobile: $distributor->mobile_number \r\n";
            }

            self::sendSMS($phoneNumber, $message);
        } catch (\Exception $ex) {
            \Log::critical('Unable to send sms for user #' . $distributor->id . 'Error' . $ex->getMessage());
        }
    }
}
