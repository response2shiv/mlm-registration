<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class iDecide extends Model
{
    protected $table = 'idecide_users';

    public static function createUser($userId, $sponsorInfo, $integrationId = 0)
    {
        $user = \App\Models\User::find($userId);

        $postData = array(
            'emailAddress' => strtolower($user->email),
            'password' => $sponsorInfo['password'],
            'firstName' => $user->firstname,
            'lastName' => $user->lastname,
            'integrationId' => !empty($integrationId) ? $integrationId : $userId,
            'businessNumber' => $sponsorInfo['username'],
            'sendWelcomeEmail' => true
        );


        $logId = ApiLogs::logRequest($userId, 'iDecide-F', 'users/create', $postData)->id;

        $iDecideInteractive = new \iDecideInteractive();
        try {
            $responseJson = $iDecideInteractive->post($postData, 'users/create');
        } catch (\Exception $exception) {
            $responseJson = (string)$exception->getResponse()->getBody(true);
        }

        $responseJson = json_encode(json_decode($responseJson));
        ApiLogs::logResponse($logId, $responseJson);

        $response = json_decode($responseJson);

        if (empty($response->errors) && isset($response->userId)) {
            iDecide::insert([
                'api_log' => $logId,
                'user_id' => $userId,
                'idecide_user_id' => $response->userId,
                'password' => $sponsorInfo['password'],
                'login_url' => $response->loginUrl,
                'generated_integration_id' => $integrationId,
                'status' => 1
            ]);

            return true;
        } else if (isset($response->errors) && (string)$response->errors[0] == 'INTEGRATION_ID_USED') {

            $integrationId = mt_rand(10000000, 99999999);

            while (self::integrationIdUsed($integrationId)) {
                $integrationId = mt_rand(10000000, 99999999);
            }

            self::createUser($userId, $sponsorInfo, $integrationId);
        }
    }

    private static function integrationIdUsed($integrationId)
    {

        $integrationIdUsed = \App\Models\iDecide::where('generated_integration_id', $integrationId)->count();

        if ($integrationIdUsed) {
            return true;
        }

        return false;
    }

}
