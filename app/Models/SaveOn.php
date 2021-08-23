<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class SaveOn extends Model {

    public $timestamps = false;
    protected $table = 'sor_tokens';
    const USER_DISABLE_CHANGE_NOTE = 'The user has cancelled';

    public static function disableUser($productId, $email, $contractNumber, $userId)
    {
        $endPoint = config('api_endpoints.SORDeactivatedUser');
        $saveOnAPI = new \SOR($productId);
        $postData = array(
            "Email" => $email,
            "ContractNumber" => $contractNumber,
            "ChangeNote" => \App\SaveOn::USER_DISABLE_CHANGE_NOTE,
        );
        $logId = \App\Helper::logApiRequests($userId, 'SOR - user token', $endPoint, $postData);
        try {
            $responseBody = $saveOnAPI->_post($endPoint, $postData, false);
        } catch (\Exception $exception) {
            $responseBody = (string)$exception->getResponse()->getBody(true);
        }
        \App\Helper::logApiResponse($logId->id, $responseBody);
        if ($responseBody != '"Member not found."') {
            $token = str_replace('"', '', $responseBody);
            \App\SaveOn::where('user_id', $userId)->update(['api_log' => $logId->id, 'token' => $token]);
            return array('status' => 'success', 'token' => $token);
        } else {
            return array('status' => 'error', 'msg' => str_replace('"', '', $responseBody));
        }

    }

    public static function SORUserToken($userId, $email, $password, $createCustomerResponse, $productId) {
        $endPoint = config('api_endpoints.SORGetLoginToken');
        $saveOnAPI = new \SOR($productId);
        $postData = array(
            "Email" => $email,
            "Password" => $password
        );
        $logId = \App\Helper::logApiRequests($userId, 'SOR - user token', $endPoint, $postData);
        try {
            $responseBody = $saveOnAPI->_post($endPoint, $postData, false);
        } catch (\Exception $exception) {
            $responseBody = (string) $exception->getResponse()->getBody(true);
        }
        \App\Helper::logApiResponse($logId->id, $responseBody);
        if ($responseBody != '"Member not found."') {
            $token = str_replace('"', '', $responseBody);
            \App\SaveOn::where('user_id', $userId)->update(['api_log' => $logId->id, 'token' => $token]);
            return array('status' => 'success', 'token' => $token);
        } else {
            return array('status' => 'error', 'msg' => str_replace('"', '', $responseBody));
        }
    }

    public static function SORCreateUserWithToken($userId, $productId) {
        $user = User::find($userId);
        $endPoint = config('api_endpoints.SORCreateUser');
        $password = \App\Helper::randomPassword();
        $postData = array(
            'Email' => $user->email,
            'Password' => $password,
            'FirstName' => $user->firstname,
            'LastName' => $user->lastname,
            'Address' => $user->userAddress->address1,
            'City' => $user->userAddress->city,
            'TwoLetterCountryCode' => $user->userAddress->countrycode,
            'Phone' => $user->phonenumber,
            'ContractNumber' => $user->distid,
            'UserAccountTypeID' => config('api_endpoints.UserAccountTypeID')
        );
        $logId = \App\Helper::logApiRequests($userId, 'SOR - with token', $endPoint, $postData);
        //create user
        $saveOnAPI = new \SOR($productId);
        try {
            $jsonBody = $saveOnAPI->_post($endPoint, $postData, true);
        } catch (\Exception $exception) {
            $jsonBody = (string) $exception->getResponse()->getBody(true);
        }
        \App\Helper::logApiResponse($logId->id, $jsonBody);
        $response = json_decode($jsonBody);
        if ($response->ResultType != 'error') {
            \App\SaveOn::insert(['api_log' => $logId->id, 'user_id' => $user->id, 'product_id' => $productId, 'sor_user_id' => $response->Account->UserId, 'sor_password' => $password]);
            return self::SORUserToken($user->id, $user->email, $password, $response, $productId);
        } else {
            return array('status' => 'error', 'msg' => $response->Message);
        }
    }

    public static function transfer($userId, $sorUserId, $transferToProductId) {
        $user = User::find($userId);
        $endPoint = config('api_endpoints.SORUserTransfer');
        $postData = array(
            'SORContractNumber' => $user->distid,
            'SORMemberID' => $sorUserId,
        );
        $logId = \App\Helper::logApiRequests($userId, 'SOR - transfer', $endPoint, $postData);
        $saveOnAPI = new \SOR($transferToProductId);
        try {
            $jsonBody = $saveOnAPI->_postTransferRequest($endPoint, $postData, true);
        } catch (\Exception $exception) {
            $jsonBody = (string) $exception->getResponse()->getBody(true);
        }
        $response = json_decode($jsonBody);
        \App\Helper::logApiResponse($logId->id, $jsonBody);
        return array('response' => $response, 'request' => $postData);
    }

    public static function SORCreateUser($userId, $productId, $userAddress) {
        $user = User::find($userId);
        $endPoint = config('api_endpoints.SORCreateUser');
        $password = \App\Helper::randomPassword();
        $postData = array(
            'Email' => $user->email,
            'Password' => $password,
            'FirstName' => $user->firstname,
            'LastName' => $user->lastname,
            'Address' => $userAddress->address1,
            'City' => $userAddress->city,
            'TwoLetterCountryCode' => $userAddress->countrycode,
            'Phone' => $user->phonenumber,
            'ContractNumber' => $user->distid,
            'UserAccountTypeID' => config('api_endpoints.UserAccountTypeID')
        );
        //create user
        $saveOnAPI = new \SOR($productId);
        try {
            $jsonBody = $saveOnAPI->_post($endPoint, $postData, true);
        } catch (\Exception $exception) {
            $jsonBody = (string) $exception->getResponse()->getBody(true);
        }
        $response = json_decode($jsonBody);
        return array('response' => $response, 'request' => $postData);
    }

    /** FOR CUSTOMERS, START * */
    public static function SORCreateUserWithToken_customers($firstName, $lastName, $email, $mobile, $password, $referringUserSORID, $boomerangCode) {
        $endPoint = config('api_endpoints.SORCreateUser');
        $postData = array(
            'Email' => $email,
            'Password' => $password,
            'FirstName' => $firstName,
            'LastName' => $lastName,
            'Phone' => $mobile,
            'ReferringUserId' => $referringUserSORID,
            'UserAccountTypeID' => 5
        );
        $logId = \App\Helper::logApiRequests(0, 'SOR - customer - '.$boomerangCode, $endPoint, $postData);
        //create user
        $saveOnAPI = new \SOR('boomerang');
        try {
            $jsonBody = $saveOnAPI->_post($endPoint, $postData, true);
        } catch (\Exception $exception) {
            $jsonBody = (string) $exception->getResponse()->getBody(true);
        }
        \App\Helper::logApiResponse($logId->id, $jsonBody);
        $response = json_decode($jsonBody);
        if (isset($response->Account) && isset($response->Account->UserId)) {
            \App\SaveOn::insert(['api_log' => $logId->id, 'user_id' => 0, 'product_id' => 0, 'sor_user_id' => $response->Account->UserId, 'sor_password' => $password]);
            return array('status' => 'success');
        } else {
            return array('status' => 'error', 'msg' => $response->Message);
        }
    }

    /** FOR CUSTOMERS, END * */
    public static function getSORUserId($userId) {
        $rec = DB::table('sor_tokens')
                ->select('sor_user_id')
                ->where('user_id', $userId)
                ->first();
        if (empty($rec))
            return null;
        else
            return $rec->sor_user_id;
    }

    public static function getSORUserInfo($userId) {
        $rec = DB::table('sor_tokens')
                ->select('sor_user_id', 'product_id')
                ->where('user_id', $userId)
                ->first();
        return $rec;
    }


    public static function getPackageName($productId) {
        if ($productId == 1) {
            // standby
            return "iGo4Less0";
        } else if ($productId == 2) {
            // coach
            return "iGo4less1";
        } else if ($productId == 3) {
            // businss
            return "iGo4less2";
        } else if ($productId == 4) {
            // first class
            return "iGo4less3";
        } else if ($productId == 'boomerang') {
            // customer
            return "iGo4lessBoom";
        }
    }

}
