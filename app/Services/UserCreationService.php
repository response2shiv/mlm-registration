<?php

namespace App\Services;

use App\Models\User;
use App\Models\Orders;
use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\LoungeQueue;
use Illuminate\Support\Facades\DB;

class UserCreationService
{

    public static function makeUserActive($userId)
    {
        $user = User::where('id', '=', $userId)->first();

        $user->update([
            'account_status' => User::ACC_STATUS_APPROVED
        ]);
    }


    public static function placeUserInBinaryTree($userId)
    {
        try {
            $direction = session('binary_placement_direction');
            \CreateRecords::placeToBinaryTree($userId, $direction);
        } catch (\Exception $ex) {
            \App\Models\BinaryPlacementLog::insert(['user_id' => $userId, 'error' => $ex->getMessage()]);
        } catch (\Throwable $ex) {
            \App\Models\BinaryPlacementLog::insert(['user_id' => $userId, 'error' => $ex->getMessage()]);
        }
    }

    public static function loungeQueue($userId, $sponsor)
    {
        $sponsor = User::getSponsorByDistId($sponsor);

        LoungeQueue::create([
            'user_id' => $userId,
            'sponsor_id' => $sponsor->id,
            'is_assigned' => $sponsor->binary_placement == 'auto' ? true : false,
        ]);
    }

    /**
     * @param PreOrder $preOrder
     *
     * @return int
     */
    public static function copyPreOrderToOrder($preOrder, $userPaymentId = null)
    {
        $preOrder->orderhash = request()->order_id;
        $preOrder->save();

        $preOrderData = $preOrder->toArray();
        unset($preOrderData['id']);
        //$preOrderData['trasnactionid'] = request()->order_id;

        $order = new Orders($preOrderData);
        $order->user_payment_methods_id = $userPaymentId;
        $order->save();

        return $order->id;
    }

    public static function copyPreOrderItems($preOrderId, $orderId)
    {
        $preOrderItems = DB::table('pre_order_items')
            ->where('orderid', '=', $preOrderId)
            ->get()
            ->toArray();

        foreach ($preOrderItems as &$preOrderItem) {
            $preOrderItem = (array)$preOrderItem;
            $preOrderItem['orderid'] = $orderId;
            unset($preOrderItem['id']);
        }

        DB::table('orderItem')->insert($preOrderItems);
    }
}
