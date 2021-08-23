<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class Marketing extends Model
{
    protected $table='marketing_leads';

    public static function constructSessionArray($session_data)
    {
        if (!empty($session_data) && isset($session_data['vitals']) && isset($session_data['vitals'])) {
            $sponsorInfo = $session_data['vitals'];
            foreach ($sponsorInfo as $key => $info)
                $session_data[$key] = $info;
            unset($session_data['vitals']);
        }
        return $session_data;
    }

    public static function manageMarketingData()
    {
        $session_data = session()->all();
        $session_data = self::constructSessionArray($session_data);
        $marketingId = self::manageData($session_data);
        session(['marketing_id' => $marketingId]);
    }

    public static function manageData($sessionData)
    {
//        if (!isset($sessionData['marketing_id']) || !$sessionData['marketing_id'])
//            $marketing = new Marketing();
//        else
//            $marketing = Marketing::find($sessionData['marketing_id']);
        $marketing = new Marketing();
        $marketing->sponsor_username = (isset($sessionData['sponsor_username'])) ? $sessionData['sponsor_username'] : '';
        $marketing->sponsor = (isset($sessionData['sponsor'])) ? $sessionData['sponsor'] : '';
        $marketing->sponsor_name = (isset($sessionData['sponsor_name'])) ? $sessionData['sponsor_name'] : '';
        $marketing->sponsor_city = (isset($sessionData['sponsor_city'])) ? $sessionData['sponsor_city'] : '';
        $marketing->sponsor_state = (isset($sessionData['sponsor_state'])) ? $sessionData['sponsor_state'] : '';
        $marketing->sponsor_mobile_number = (isset($sessionData['sponsor_mobile_number'])) ? $sessionData['sponsor_mobile_number'] : '';
        $marketing->sponsor_email = (isset($sessionData['sponsor_email'])) ? $sessionData['sponsor_email'] : '';
        $marketing->country = (isset($sessionData['country'])) ? $sessionData['country'] : '';
        $marketing->language = (isset($sessionData['language'])) ? $sessionData['language'] : '';
        $marketing->first_name = (isset($sessionData['firstname'])) ? $sessionData['firstname'] : '';
        $marketing->last_name = (isset($sessionData['lastname'])) ? $sessionData['lastname'] : '';
        $marketing->email = (isset($sessionData['email'])) ? $sessionData['email'] : '';
        $marketing->country_code = (isset($sessionData['country_code'])) ? $sessionData['country_code'] : '';
        $marketing->mobile_number = (isset($sessionData['mobile_number'])) ? $sessionData['mobile_number'] : '';
        $marketing->updates_subscribe = (isset($sessionData['updates_subscribe'])) ? $sessionData['updates_subscribe'] : '';
        $marketing->authy_id = (isset($sessionData['authy_id'])) ? $sessionData['authy_id'] : '';
        $marketing->marketing_agreed = (isset($sessionData['updates_subscribe'])) ? 1 : 0;
        $marketing->fa_approved = (isset($sessionData['success_2fa'])) ? $sessionData['success_2fa'] : 0;
        $marketing->save();
        return $marketing->id;
    }
}
