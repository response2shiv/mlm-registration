<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SOR extends Model
{

    protected $table = 'sor_tokens';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'api_log',
        'platform_id',
        'platform_name',
        'platform_tier',
        'sor_password',
        'token',
    ];

    public $timestamps = false;

    public static function createUser($userId, $productInfo)
    {
        $user = User::find($userId);
        if (isset($user->userAddress->address1))
            $address = $user->userAddress->address1;
        else
            $address = NULL;

        if (isset($user->userAddress->city))
            $city = $user->userAddress->city;
        else
            $city = NULL;


        if (isset($user->userAddress->countrycode))
            $countrycode = $user->userAddress->countrycode;
        else
            $countrycode = NULL;

        $password = \GeneratePassword::generate();
        $postData = array(
            'Email' => strtolower($user->email),
            'Password' => $password,
            'FirstName' => $user->firstname,
            'LastName' => $user->lastname,
            'Address' => $address,
            'City' => $city,
            'TwoLetterCountryCode' => $countrycode,
            'Phone' => $user->phonenumber,
            'ContractNumber' => $user->distid,
            'UserAccountTypeID' => 9
        );

        $logId = ApiLogs::logRequest($userId, 'SOR-F', 'clubmembership/createdefault', $postData)->id;
        $level = Products::getProductLevel($productInfo->id);

        //create user
        $saveOnAPI = new \SaveOn($level, 'json');
        try {
            $jsonBody = $saveOnAPI->post('clubmembership/createdefault', $postData, true);
        } catch (\Exception $exception) {
            $jsonBody = json_encode($exception->getMessage());
            //$jsonBody = (string)$exception->getResponse()->getBody(true);
        }

        $jsonBody = json_encode(json_decode($jsonBody));
        ApiLogs::logResponse($logId, $jsonBody);

        $response = json_decode($jsonBody);

        if (isset($response->ResultType) && $response->ResultType != 'error' && isset($response->Account->UserId)) {
            \App\Models\SOR::insert([
                'api_log' => $logId,
                'user_id' => $userId,
                'product_id' => $productInfo->id,
                'sor_user_id' => (string)$response->Account->UserId,
                'sor_password' => $password,
                'status' => 1
            ]);

            $numberOfBoomerangs = !empty($productInfo->num_boomerangs) ? (int)$productInfo->num_boomerangs : 0;
            $sponsorBoomerangs = !empty($productInfo->sponsor_boomerangs) ? (int)$productInfo->sponsor_boomerangs : 0;

            \App\Models\BoomerangInv::create([
                'userid' => $userId,
                'pending_tot' => 0,
                'available_tot' => $numberOfBoomerangs
            ]);

            $sponsor = User::getSponsorByDistId($user->sponsorid);
            if ($sponsor) {
                BoomerangInv::addToInventory($sponsor->id, $sponsorBoomerangs);
            }
        }
    }
}
