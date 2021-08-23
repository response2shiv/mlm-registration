<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwilioAuthy extends Model
{
    //https://github.com/twilio/authy-php

    const API_KEY = "02ea82kI27ensx8oOX2yKj13a75xGauu";

    public static function register($email, $mobile, $countryCode)
    {
        $error = 0;
        $msg = "";
        $authyUserId = 0;
        //
        $authy_api = new \Authy\AuthyApi(self::API_KEY);
        $user = $authy_api->registerUser($email, $mobile, $countryCode);
        if ($user->ok()) {
            $authyUserId = $user->id();
        } else {
            $error = 1;
            foreach ($user->errors() as $field => $message) {
                $msg .= $message . "<br/>";
            }
        }
        //
        $res = array();
        $res['error'] = $error;
        $res['msg'] = $msg;
        $res['authy_id'] = $authyUserId;
        //
        return $res;
    }

    public static function sendToken() {
        $authy_api = new \Authy\AuthyApi(self::API_KEY);
        $sent = 0;
        $msg = "";
        $authy_id = session('authy_id');
        if (!empty($authy_id)) {
            $sms = $authy_api->requestSms($authy_id, array("force" => "true"));
            if ($sms->ok()) {
                $sent = 1;
            } else {
                foreach ($sms->errors() as $field => $message) {
                    $msg .= $message;
                }
            }
        }
        //
        $res = array();
        $res['sent'] = $sent;
        $res['msg'] = $msg;
        return $res;
    }

    public static function verifyToken($token) {
        $msg = "";
        $verified = 0;
        $authy_id = session('authy_id');
        //
        $authy_api = new \Authy\AuthyApi(self::API_KEY);
        //
        if (!empty($authy_id)) {
            try {
                $verification = $authy_api->verifyToken($authy_id, $token, array("force" => "true"));
                if ($verification->ok()) {
                    $verified = 1;
                } else {
                    $msg = "Invalid code";
                }
            } catch (\Exception $ex) {
                $msg = "Invalid code.";
            }
        }
        //
        $res = array();
        $res['verified'] = $verified;
        $res['msg'] = $msg;
        //
        return $res;
    }
}
