<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLogs extends Model
{
    public $timestamps = false;

    protected $table = 'api_logs';

    protected $fillable = [
        'user_id',
        'api',
        'endpoint',
        'request',
        'response'
    ];

    public static function logRequest($userId, $api, $endPoint, $postData)
    {
        return ApiLogs::create([
            'user_id' => $userId,
            'api' => $api,
            'endpoint' => $endPoint,
            'request' => json_encode($postData)
        ]);
    }

    public static function logResponse($id, $response)
    {
        ApiLogs::where('id', $id)->update(['response' => $response]);
    }

}
