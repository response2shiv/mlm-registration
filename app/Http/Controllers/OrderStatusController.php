<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index()
    {

        // return response()->json([
        //     'status' => 'PAID'
        // ]);

        $orderHashRegex = '/^MERC-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/';

        if (!request()->has('orderhash') || !preg_match($orderHashRegex, request()->orderhash)) {
            return 400;
        }

        $params['orderhash'] = request()->orderhash;
        $response = ApiHelper::request('POST', '/unicrypt/status', $params);
        $jsonResponse = json_decode($response->getBody());

        $orderStatus = 'UNPAID';

        if ($jsonResponse) {
            $orderStatus = $jsonResponse->data->status;
        }

        return response()->json([
            'status' => $orderStatus
        ]);
    }
}
