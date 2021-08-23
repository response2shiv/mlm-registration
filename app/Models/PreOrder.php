<?php

namespace App\Models;

use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Util;
use App\Helpers\CurrencyConverter;
use App\Models\OrderConversion;
use Illuminate\Support\Facades\Log;

class PreOrder extends Model
{

    protected $table = "pre_orders";
    public $timestamps = false;

    const ORDER_ACTIVE = 1;
    const ORDER_STATUS_REFUND = 6;
    const ORDER_STATUS_PARTIAL_REFUND = 9;
    const ORDER_STATUS_REFUNDED = 10;
    const ORDER_STATUS_PARTIALLY_REFUNDED = 11;


    public $fillable = [
        'userid',
        'statuscode',
        'ordersubtotal',
        'ordertax',
        'ordertotal',
        'orderbv',
        'orderqv',
        'ordercv',
        'trasnactionid',
        'updated_at',
        'created_at',
        'payment_methods_id',
        'shipping_address_id',
        'inv_id',
        'created_date',
        'created_time',
        'processed',
        'coupon_code',
        'order_refund_ref',
        'created_dt',
        'orderqc',
        'orderac'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userid');
    }


    public function preOrderItems()
    {
        return $this->hasMany('App\Models\PreOrderItem', 'orderid', 'id');
    }

    /**
     * @param $userId
     * @param $subtotal
     * @param $orderTotal
     * @param $orderBV
     * @param $orderQV
     * @param $orderCV
     * @param $transactionId
     * @param $paymentMethodId
     * @param $shippingAddressId
     * @param $invId
     * @param string $createdDate
     * @param string $discountCode
     * @param null $orderStatus
     * @param null $order_refund_ref
     * @param int $orderQC
     * @param int $orderAC
     * @return mixed
     */
    public static function addNew(
        $userId,
        $subtotal,
        $orderTotal,
        $orderBV,
        $orderQV,
        $orderCV,
        $transactionId,
        $paymentMethodId,
        $shippingAddressId,
        $invId,
        $createdDate = '',
        $discountCode = '',
        $orderStatus = null,
        $order_refund_ref = null,
        $orderQC = 0,
        $orderAC = 0,
        $isTSBOrder = null
    ) {
        $rec = new PreOrder();
        $rec->userid = $userId;
        $rec->statuscode = (empty($orderStatus) ? 1 : $orderStatus);
        $rec->ordersubtotal = $subtotal;
        $rec->ordertotal = $orderTotal;
        $rec->orderbv = $orderBV;
        $rec->orderqv = $orderQV;
        $rec->ordercv = $orderCV;
        $rec->orderqc = $orderQC;
        $rec->orderac = $orderAC;
        $rec->trasnactionid = $transactionId;
        $rec->payment_methods_id = $paymentMethodId;
        $rec->shipping_address_id = $shippingAddressId;
        $rec->inv_id = $invId;
        $rec->coupon_code = $discountCode;
        $rec->order_refund_ref = $order_refund_ref;
        if (!$isTSBOrder) {
            if (!empty($createdDate)) {
                $rec->created_date = $createdDate;
                $rec->created_dt = $createdDate . " " . Util::getCurrentTime();
            } else {
                $rec->created_date = Util::getCurrentDate();
                $rec->created_dt = Util::getCurrentDateTime();
            }
            $rec->created_time = Util::getCurrentTime();
        } else {
            $rec->created_date = date("Y-m-d", strtotime($createdDate));
            $rec->created_dt = $createdDate;
            $rec->created_time = date("h:i:s", strtotime($createdDate));
        }
        $rec->save();
        return $rec->id;
    }

    public static function updateRec($orderId, $rec, $req)
    {
        $createdDt = $req->created_date . " " . Utill::getCurrentTime();
        $r = PreOrder::find($orderId);
        $r->ordertotal = $req->ordertotal;
        $r->ordersubtotal = $req->ordersubtotal;
        $r->orderbv = $req->orderbv;
        $r->orderqv = $req->orderqv;
        $r->ordercv = $req->ordercv;
        $r->orderqc = $req->orderqc;
        $r->orderac = $req->orderac;
        $r->created_date = $req->created_date;
        $r->created_dt = $createdDt;
        $r->save();
        //
        UpdateHistory::orderUpdate($orderId, $rec, $req);

        DB::table('orderItem')
            ->where('orderid', $orderId)
            ->update(['created_dt' => $createdDt]);
    }

    public static function getById($id)
    {
        return DB::table('orders')
            ->where('id', $id)
            ->first();
    }

    /**
     * @param $id
     * @return Order|
     */
    public static function getActiveOrder($id)
    {
        return PreOrder::query()
            ->where('id', $id)
            ->whereIn('statuscode', [self::ORDER_ACTIVE, self::ORDER_STATUS_PARTIALLY_REFUNDED])
            ->where('order_refund_ref', null)
            ->first();
    }

    public static function getByUser($id)
    {
        Log::info('User ID received was ' . $id);
        $orders = DB::table('orders')
            ->selectRaw('*, orders.id as id_order ')
            ->leftjoin('order_conversions', 'orders.id', '=', 'order_conversions.order_id')
            ->where('userid', $id)
            ->where('trasnactionid', 'not like', '%AMB%')
            ->where('trasnactionid', 'not like', '%SOR%')
            // ->orWhereNull ('trasnactionid')
            ->orWhereRaw(DB::raw("userid = " . $id . " AND trasnactionid is NULL"))
            ->orderBy('created_date', 'desc')
            ->get();
        return $orders;
    }

    public static function getInvoiceByUser($id)
    {
        Log::info('User ID received was ' . $id);
        $orders = DB::table('orders')
            ->selectRaw('*,orders.id as id_order')
            ->leftjoin('orderItem', 'orders.id', '=', 'orderItem.orderid')
            ->leftjoin('order_conversions', 'orders.id', '=', 'order_conversions.order_id')
            ->where('orderItem.productid', '<>', Product::ID_TRAVEL_SAVING_BONUS)
            ->where('userid', $id)
            ->Where(function ($sq) use ($id) {
                $sq->Where('trasnactionid', 'not like', '%AMB%')
                ->orWhere('trasnactionid', 'not like', '%SOR%')
                ->orWhereNull('trasnactionid');
            })
            ->orderBy('orders.created_dt', 'desc')
            ->get();
        return $orders;
    }

    public static function getUserOrder($id)
    {
        return DB::table('orders')
            ->where('id', $id)
            ->where('userid', Auth::user()->id)
            ->first();
    }

    public static function getThisMonthOrderQV($userId)
    {
        $monthAgo = date('Y-m-d', strtotime("-1 Months"));

        $alwaysActiveUsers = [
            'A1357703',
            'A1637504',
            'TSA9846698',
            'TSA3564970',
            'TSA9714195',
            'TSA8905585',
            'TSA2593082',
            'TSA0707550',
            'TSA9834283',
            'TSA5138270',
            'TSA8715163',
            'TSA3516402',
            'TSA8192292',
            'TSA0002566',
            'TSA9856404'
        ];

        //If user is always active, his battery should always be at 100%
        if (in_array(Auth::user()->distid, $alwaysActiveUsers)) {
            return 100;
        }

        $rec = DB::table('orders')
            ->selectRaw('sum(orderqv) as qv')
            ->where('userid', $userId)
            ->whereDate('created_dt', '>=', $monthAgo)
            ->first();
        return $rec->qv;
    }

    public static function getOrdersByTsaDateRange($fromDate, $toDate, $type, $distid)
    {
        $userId = \App\User::getByDistId($distid);
        return DB::table('orders')
            ->join('orderItem', 'orders.id', '=', 'orderItem.orderid')
            ->join('products', 'orderItem.productid', '=', 'products.id')
            ->join('producttype', 'products.producttype', '=', 'producttype.id')
            ->select('orders.id', 'orders.created_dt', 'products.productname', 'products.price', 'producttype.typedesc')
            ->whereIn('producttype.typedesc', $type)
            ->where('orders.userid', $userId->id)
            ->whereDate('orders.created_dt', '>=', $fromDate)
            ->whereDate('orders.created_dt', '<=', $toDate)
            ->get();
    }

    public static function orderWithTransactionIdExists($transactionID)
    {
        return static::query()->where('trasnactionid', $transactionID)->count() > 0;
    }

    public function isRefunded()
    {
        $isRefunded = true;
        foreach ($this->preOrderItems as $orderItem) {
            if (!$orderItem->is_refunded) {
                $isRefunded = false;
                break;
            }
        }

        return $isRefunded;
    }

    public function getStandByOrderItem()
    {
        return PreOrderItem::query()
            ->where('orderid', $this->id)
            ->where('productid', Product::ID_STANDBY_CLASS)
            ->first();
    }

    public function isPurchasedByVoucher()
    {
        return !empty($this->trasnactionid) && strpos($this->trasnactionid, 'COUPON#') !== false;
    }

    /**
     * @return bool
     */
    public function isVoucherPurchaseOrder()
    {
        if ($this->preOrderItems->count() !== 1) {
            return false;
        }

        $orderItem = $this->preOrderItems[0];
        if (!$orderItem->getActiveDiscountCoupon()) {
            return false;
        }

        return true;
    }

    /**
     * @return DiscountCoupon
     */
    public function getAssociatedDiscountCoupon()
    {
        if ($this->preOrderItems->count() !== 1) {
            return null;
        }

        $orderItem = $this->preOrderItems[0];
        if (!$discountCoupon = $orderItem->getActiveDiscountCoupon()) {
            return null;
        }

        return $discountCoupon;
    }

    /**
     * Get a list of the orders so we can calculate the world series
     */
    public static function getWorldSeriesOrders($start_date, $end_date)
    {
        DB::enableQueryLog();
        return DB::table('orders')
            ->select(
                'orders.id as order_id',
                'orders.created_dt',
                'orders.statuscode',
                'users.id as userid',
                'users.username',
                'users.sponsorid',
                'users.sponsorid',
                'users.sponsorid',
                'oi.productid',
                'oi.orderid',
                'oi.id as order_item_id',
                'products.id as product_id',
                'products.productname',
                'products.bv'
            )
            ->join('orderItem as oi', 'orders.id', '=', 'oi.orderid')
            ->join('users', 'orders.userid', '=', 'users.id')
            ->join('products', 'products.id', '=', 'oi.productid')
            ->whereIn('oi.productid', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 38])
            ->whereBetween('orders.created_date', [$start_date, $end_date])
            ->orderBy('orders.created_dt')
            ->get();
        Log::info("Query -> " . DB::getQueryLog());
    }

    /**
     * Get a list of the orders so we can calculate the world series
     */
    public static function getWorldSeriesOrdersByUser($start_date, $end_date)
    {

        //DB::enableQueryLog();

        Log::info("Start ".$start_date." - End ".$end_date);
        $orders = DB::table('orders')
            ->select(
                'users.id as user_id',
                'users.username',
                'users.distid',
                'users.created_dt',
                'orders.id as order_id'
                // 'orderItem.id as order_item_id'
            )
            ->join('users', 'orders.userid', '=', 'users.id')
            // ->join('orderItem', 'orderItem.orderid', '=', 'orders.id')
            ->whereBetween('orders.created_dt', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            // ->whereIn('orderItem.productid', [1,2,3,4,5,6,7,8,9,10,38])
            ->whereIn('orders.statuscode', [1, 10])
            ->orderBy('orders.id')
            ->get();

            /*
        $orders = DB::table('users')
            ->select(
                'users.id as user_id',
                'users.username',
                'users.distid',
                'users.created_dt',
                'orders.id as order_id'
                // 'orderItem.id as order_item_id'
            )
            ->join('orders', 'orders.userid', '=', 'users.id')
            // ->join('orderItem', 'orderItem.orderid', '=', 'orders.id')
            ->whereBetween('users.created_dt', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->whereBetween('orders.created_dt', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            // ->whereIn('orderItem.productid', [1,2,3,4,5,6,7,8,9,10,38])
            ->whereIn('orders.statuscode', [1, 10])
            ->orderBy('orders.id')
            ;//->get();
            */

        //Log::info("Query log -> ",DB::getQueryLog());

        return $orders;
    }

    public static function createOrderForBuumerang($orderId, $product)
    {

        $user = Auth::user();
        $bv = $product->bv * $product->quantity;
        $qv = $product->qv * $product->quantity;
        $cv = $product->cv * $product->quantity;

        PreOrderItem::addNew($orderId, $product->id, $product->quantity, $product->price, $bv, $qv, $cv);
        Log::info("Order for Buumerangs: " . $product->producttype);

        BoomerangInv::addToInventory($user->id, $product->product->num_boomerangs * $product->quantity);
    }

    public static function createOrderForDonation($orderId, $product)
    {

        $bv = $product->bv * $product->quantity;
        $qv = $product->qv * $product->quantity;
        $cv = $product->cv * $product->quantity;

        PreOrderItem::addNew($orderId, $product->id, $product->quantity, $product->sub_total, $bv, $qv, $cv);
        Log::info("Order for Donation: " . $product->producttype);
    }

    public static function createOrderForUpgrade($orderId, $product)
    {
        $user = Auth::user();

        $bv = $product->bv * $product->quantity;
        $qv = $product->qv * $product->quantity;
        $cv = $product->cv * $product->quantity;

        PreOrderItem::addNew($orderId, $product->id, $product->quantity, $product->sub_total, $bv, $qv, $cv);

        User::setCurrentProductId($user->id, $product->id);

        Helper::afterPaymentSuccess($product->id, $product->num_boomerangs, null, $user->id);

        if (!empty($sesData) && isset($user->currentProductId) && $user->currentProductId == Product::ID_STANDBY_CLASS) {
            $cOrder = PreOrder::getById($orderId);
            $oCreatedDate = date('d', strtotime($cOrder->created_date));
            if ($oCreatedDate >= 25) {
                $sDate = strtotime(date("Y-m-25", strtotime($cOrder->created_date)) . " +1 month");
                $sDate = date("Y-m-d", $sDate);
            } else {
                $sDate = strtotime(date("Y-m-d", strtotime($cOrder->created_date)) . " +1 month");
                $sDate = date("Y-m-d", $sDate);
            }
            User::where('id', $user->id)->update(['next_subscription_date' => $sDate, 'original_subscription_date' => $sDate]);
        }
        Log::info("Order for Upgrade: " . $product->producttype);
    }

    public static function createOrderForTicket($orderId, $product)
    {

        $bv = $product->bv * $product->quantity;
        $qv = $product->qv * $product->quantity;
        $cv = $product->cv * $product->quantity;


        PreOrderItem::addNew($orderId, $product->id, $product->quantity, $product->sub_total, $bv, $qv, $cv);
        Log::info("Order for ticket: " . $product->producttype);
    }

    public static function createOrderForMembership($orderId,$product)
    {

        Log::info("Create order for membership: ".$product->producttype);
    }

}
