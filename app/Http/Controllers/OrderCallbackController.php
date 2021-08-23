<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\DiscountCoupon;
use App\Models\Orders;
use App\Models\PreOrder;
use App\Models\User;
use App\Models\MerchantTransactionTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use App\Services\UserCreationService;

class OrderCallbackController extends Controller
{
    public function callback()
    {

        Log::info("Request ",request()->all());

        $preOrderId = filter_var(request()->sulte_apt_no, FILTER_VALIDATE_INT);
        $status = request()->status;

        $merchtracker = MerchantTransactionTracker::where('pre_order_id', request()->sulte_apt_no)->first();
        $merchtracker->transaction_id = request()->order_id;
        $merchtracker->status = $status == 'fail' ? 'UNPAID' : 'PAID';
        $merchtracker->save();

        if ($preOrderId === false) {
            session()->flush();
            return redirect('/enrollment/sponsor');
        }

        $preOrder = PreOrder::query()->find($preOrderId);

        if (!$preOrder) {
            session()->flush();
            return redirect('/enrollment/sponsor');
        }

        if ($status == 'success') {
            UserCreationService::makeUserActive($preOrder->userid);
            $orderId = UserCreationService::copyPreOrderToOrder($preOrder);
            UserCreationService::copyPreOrderItems($preOrder->id, $orderId);
            UserCreationService::placeUserInBinaryTree($preOrder->userid);

            $merchtracker->cron_processed = 1;
            $merchtracker->save();

            session(['status' => 'success']);

            return redirect('/thank-you');
        } else if ($status == 'fail') {
            $this->deleteUserAndAssociated($preOrder->userid);
            return redirect('/payment/declined');
        } else {
            return redirect('/enrollment/sponsor');
        }
    }

    private function deleteUserAndAssociated($userId)
    {
        DB::table('users')
            ->where('id', '=', $userId)
            ->update([
                'firstname' => null,
                'lastname' => null,
                'username' => null,
                'email' => null,
                'phonenumber' => null,
                'mobilenumber' => null,
                'usertype' => 5
        ]);

        $tablesByUserIdAlias = [
            'addresses' => 'userid',
            'payment_methods' => 'userID',
            'boomerang_inv' => 'userid',
            'bc_carryover_history' => 'user_id',
            'user_activity_history' => 'user_id'
        ];

        foreach ($tablesByUserIdAlias as $table=>$userIdAlias) {
            DB::table($table)
                ->where($userIdAlias, '=', $userId)
                ->delete();
        }

        $preOrders = DB::table('pre_orders')
            ->where('userid', '=', $userId)
            ->get();

        foreach ($preOrders as $preOrder) {

            if (stripos($preOrder->trasnactionid, 'COUPON#') !== false) {
                $couponCode = substr($preOrder->transactionid, 7);
                DiscountCoupon::where('code', '=', $couponCode)->update([
                    'is_used' => 0,
                    'used_by' => null
                ]);
            }

            DB::table('pre_order_items')->where('orderid', '=', $preOrder->id)->get();
            DB::table('order_conversions')->where('pre_order_id', '=', $preOrder->id)->delete();
        }

    }
}
