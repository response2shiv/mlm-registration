<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IPayOut extends Model
{
    protected $table = 'ipayout_user';
    protected $fillable = ['user_id', 'transaction_id'];
    const ITEM_DESCRIPTION = 'Fund deposit from https://ncrease.com/';


    public static function createiPayoutUser($user)
    {
        try {
            $primary_address = \App\Models\Addresses::where('userid', $user->id)
                ->where('addrtype', \App\Models\Addresses::TYPE_REGISTRATION)
                ->first();
            $params = array(
                'fn' => 'eWallet_RegisterUser',
                'UserName' => $user->username,
                'FirstName' => $user->firstname,
                'LastName' => $user->lastname,
                'CompanyName' => '',
                'Address1' => (isset($primary_address->address1) ? $primary_address->address1 : ''),
                'Address2' => (isset($primary_address->address2) ? $primary_address->address2 : ''),
                'City' => (isset($primary_address->city) ? $primary_address->city : ''),
                'State' => (isset($primary_address->stateprov) ? $primary_address->stateprov : ''),
                'ZipCode' => (isset($primary_address->postalcode) ? $primary_address->postalcode : ''),
                'Country2xFormat' => (isset($primary_address->countrycode) ? $primary_address->countrycode : ''),
                'PhoneNumber' => (isset($user->phonenumber) ? $user->phonenumber : ''),
                'CellPhoneNumber' => '',
                'EmailAddress' => $user->email,
                'SSN' => '',
                'CompanyTaxID' => '',
                'GovernmentID' => '',
                'MilitaryID' => '',
                'PassportNumber' => '',
                'DriversLicense' => '',
                'DateOfBirth' => '',
                'WebsitePassword' => 'password',
                'DefaultCurrency' => 'USD'
            );
            $response = \App\Models\IPayOut::curl($params);
            if ($response['error'] == 1) {
                //error
                $logId = \App\Models\ApiLogs::logRequest($user->id, 'IPayout - add user', '', $params);
                \App\Models\ApiLogs::logResponse($logId->id, $response['response']->response->m_Text);
                return ['error' => 1, 'data' => [], 'msg' => $response['response']->response->m_Text];
            } else {
                //success
                $rec = \App\Models\IPayOut::addUser($user->id, $response['response']->response->TransactionRefID);
                return ['error' => 0, 'data' => $rec, 'msg' => 'iPayout account setup successfully'];
            }
        } catch (\Exception $exception) {
            return ['error' => 1, 'msg' => $exception->getMessage()];
        }
    }

    public static function curl($requestPayload)
    {
        $mode = \Config::get('const.ipayout.mode');
        $requestPayload['MerchantGUID'] = \Config::get('const.ipayout.' . $mode . '.MerchantGUID');
        $requestPayload['MerchantPassword'] = \Config::get('const.ipayout.' . $mode . '.MerchantPassword');
        $request = json_encode($requestPayload);
        try {
            $ch = curl_init(\Config::get('const.ipayout.' . $mode . '.eWalletAPIURL'));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $ips_response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($ips_response);
            return ['error' => 0, 'response' => $response];
        } catch (\Exception $ex) {
            return ['error' => 1, 'response' => ['m_Text' => $ex->getMessage()]];
        }
    }

    public static function addUser($userId, $transactionRefId)
    {
        $hasRec = \App\Models\IPayOut::getIPayoutByUserId($userId);
        if (empty($hasRec)) {
            $rec = new IPayOut();
            $rec->user_id = $userId;
            $rec->transaction_id = $transactionRefId;
            $rec->save();
            return $rec->id;
        }
        return $hasRec;
    }

    public static function getIPayoutByUserId($userId)
    {
        return IPayOut::where('user_id', $userId)->first();
    }
}
