<?php

use App\Facades\BinaryPlanManager;
use App\Facades\HoldingTank;
use App\Helpers\ApiHelper;
use App\Models\Addresses;
use App\Models\BinaryPlanNode;
use App\Models\BoomerangInv;
use App\Models\OrderConversion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateRecords
{
    public function __construct()
    {
    }

    public function create(
        $sponsorInfo,
        $productInfo,
        $addressInfo,
        $cardInfo = null,
        $nmiResponse,
        $numBoomerangs,
        $paymentType = null,
        $ticketProduct = null,
        $orderConversionId,
        $account_status = null
    ) {
        $userId = $this->createUser($sponsorInfo, $productInfo, $addressInfo, $account_status);
        $addressId = $this->createAddress($userId, $addressInfo['billing'], Addresses::TYPE_BILLING, 1);
        // $paymentMethodId = $this->createPaymentMethod($userId, $addressId, $addressInfo['billing'], $cardInfo, $nmiResponse, $paymentType);
        $this->createEwalletPaymentMethod($userId);
        $orderId = $this->createOrder($userId, $nmiResponse, $paymentType, $sponsorInfo);

        $this->addBoomerangs($userId, $numBoomerangs);

        if ($orderConversionId) {
            OrderConversion::setOrderId($orderConversionId, $orderId);
        }

        session(['order_id' => $orderId]);
        // session(['existing_payment_method_id' => NU]);

        $this->createPreOrderItem($orderId, $productInfo, $cardInfo, $ticketProduct);

        if ($paymentType == 'voucher') {
            try {
                $direction = session('binary_placement_direction');
                $this->placeToBinaryTree($userId, $direction);
            } catch (\Exception $ex) {
                \App\Models\BinaryPlacementLog::insert(['user_id' => $userId, 'error' => $ex->getMessage()]);
            } catch (\Throwable $ex) {
                \App\Models\BinaryPlacementLog::insert(['user_id' => $userId, 'error' => $ex->getMessage()]);
            }
        }

        # Apply the first event (enrollment) to World Series
        # when month is Jun, Jul, Aug.
        // if (in_array(date('m'), ['06', '07', '08'])) {
        //     $this->joinWorldSeries($userId, $orderId);
        // }

        return $userId;
    }

    private function joinWorldSeries($userId, $orderId)
    {
        $data = [
            'user_id'    => $userId,
            'order_id'   => $orderId,
            'event_type' => 'enrollment'
        ];

        $response = ApiHelper::request('POST', '/signup-world-series', $data);
        $response_data = json_decode($response->getBody());

        return $response_data;
    }

    private function createUser($sponsorInfo, $productInfo, $addressInfo, $account_status)
    {
        $current_date = date("Y-m-d");

        if (isset($sponsorInfo['subscription_start_date'])) {
            $next_billing_date = $sponsorInfo['subscription_start_date'];
        } else {
            if ($current_date >= date("Y-m-25")) {
                $next_billing_date = strtotime(date("Y-m-25", strtotime($current_date)) . " +1 month");
            } else {
                $next_billing_date = strtotime(date("Y-m-d", strtotime($current_date)) . " +1 month");
            }

            $next_billing_date = date("Y-m-d", $next_billing_date);
        }


        $countDownExpireOn = date('Y-m-d', strtotime(date('Y-m-d') . '+28 days'));

        $countryCode = DB::table('country')->where('id', $sponsorInfo['country'])->first();

        $user = [
            'firstname' => $sponsorInfo['firstname'],
            'lastname' => $sponsorInfo['lastname'],
            'co_applicant_name' => (isset($sponsorInfo['co_applicant_first_name']) ? $sponsorInfo['co_applicant_first_name'] : '') . " " . (isset($sponsorInfo['co_applicant_last_name']) ? $sponsorInfo['co_applicant_last_name'] : ''),
            'co_applicant_country_code' => (isset($sponsorInfo['co_applicant_country_code']) ? $sponsorInfo['co_applicant_country_code'] : ''),
            'co_applicant_mobile_number' => (isset($sponsorInfo['co_applicant_mobile_number']) ? $sponsorInfo['co_applicant_mobile_number'] : ''),
            'co_applicant_email' => (isset($sponsorInfo['co_applicant_email']) ? $sponsorInfo['co_applicant_email'] : ''),
            'tax_information' => $sponsorInfo['tax_information'],
            'language' => $sponsorInfo['language'],
            'ein' => $sponsorInfo['ein'],
            'date_of_birth' => date("Y-m-d", strtotime($sponsorInfo['birth_day'] . "-" . $sponsorInfo['birth_month'] . "-" . $sponsorInfo['birth_year'])),
            //            'date_of_birth' => null,
            'business_name' => $sponsorInfo['business_name'],
            'country_code' => (!empty($countryCode) ? $countryCode->countrycode : ''),
            'email' => strtolower(trim($sponsorInfo['email'])),
            'phonenumber' => $sponsorInfo['phone_number'],
            'username' => strtolower(trim($sponsorInfo['username'])),
            'refname' => strtolower(trim($sponsorInfo['username'])),
            'sex' => null,
            //            'sex' => strtolower(trim($sponsorInfo['gender'])),
            'account_status' => is_null($account_status) ? 'APPROVED' : $account_status,
            'usertype' => 2,
            'statuscode' => 1,
            'sponsorid' => $sponsorInfo['sponsor'],
            'mobilenumber' => $sponsorInfo['mobile_number'],
            'phone_country_code' => $sponsorInfo['country_code'],
            'password' => bcrypt($sponsorInfo['password']),
            'current_product_id' => $productInfo->id,
            'subscription_product' => $this->determineSubscriptionProduct($productInfo->id, $countryCode),
            'created_date' => $current_date,
            'created_time' => date('H:i:s'),
            'original_subscription_date' => $next_billing_date,
            'next_subscription_date' => $next_billing_date,
            'coundown_expire_on' => $countDownExpireOn,
            'created_dt' => date('Y-m-d H:i:s')
        ];
        $userId = \App\Models\User::create($user)->id;

        $distId = null;

        if (isset($sponsorInfo['distid'])) {
            $distId = $sponsorInfo['distid'];
        } else {
            $tsa = new \TSA();
            $distId = $tsa->generate($userId);
        }

        \App\Models\User::find($userId)->update(['distid' => $distId]);
        $this->createAddress($userId, $addressInfo['primary'], Addresses::TYPE_REGISTRATION, 1);
        $this->createAddress($userId, $addressInfo['shipping'], Addresses::TYPE_SHIPPING, 1);
        return $userId;
    }

    private function createAddress($userId, $billingInfo, $type = Addresses::TYPE_BILLING, $primary = 1)
    {
        $address = [
            'userid' => $userId,
            'addrtype' => $type,
            'apt' => isset($billingInfo['apt']) ? $billingInfo['apt'] : null,
            'address1' => isset($billingInfo['address1']) ? $billingInfo['address1'] : null,
            'address2' => isset($billingInfo['address2']) ? $billingInfo['address2'] : null,
            'city' => isset($billingInfo['city']) ? $billingInfo['city'] : null,
            'stateprov' => isset($billingInfo['state']) ? $billingInfo['state'] : null,
            'postalcode' => isset($billingInfo['postal_code']) ? $billingInfo['postal_code'] : null,
            'countrycode' => isset($billingInfo['country']) ? $billingInfo['country'] : null,
            'primary' => 1,
        ];
        return \App\Models\Addresses::create($address)->id;
    }

    private function createOrder($userId, $nmiResponse, $paymentTypeId)
    {
        if ($paymentTypeId == 'comp') {
            $paymentTypeId = \App\Models\PaymentMethodType::TYPE_ADMIN;
        } else if ($paymentTypeId == 'voucher') {
            $paymentTypeId = \App\Models\PaymentMethodType::TYPE_COUPON_CODE;
        }

        $order = [
            'userid' => $userId,
            'statuscode' => 1,
            'trasnactionid' => $nmiResponse['response']['Authorization'],
            'payment_methods_id' => NULL,
            'payment_type_id' => $paymentTypeId,
            'created_date' => date('Y-m-d'),
            'created_time' => date('H:i:s'),
            'is_distributed' => false,
            'created_dt' => date('Y-m-d H:i:s'),
        ];

        if ($paymentTypeId == \App\Models\PaymentMethodType::TYPE_UNICRYPT || $paymentTypeId == \App\Models\PaymentMethodType::TYPE_IPAYTOTAL) {
            $orderId = \App\Models\PreOrder::create($order)->id;
        } else {
            $orderId = \App\Models\Orders::create($order)->id;
        }
        return $orderId;
    }

    private function createPreOrderItem($orderId, $product, $cardInfo, $ticketProduct)
    {
        $preOrder = \App\Models\PreOrder::find($orderId);

        if ($product->id == 1) {
            $standBy = \App\Models\Products::find($product->id);
            $orderItem = [
                'orderid' => $orderId,
                'productid' => 1,
                'quantity' => 1,
                'itemprice' => (string)$standBy->price,
                'bv' => !empty($standBy->bv) ? (string)$standBy->bv : 0,
                'qv' => !empty($standBy->qv) ? (string)$standBy->qv : 0,
                'cv' => !empty($standBy->cv) ? (string)$standBy->cv : 0,
                'created_date' => date('Y-m-d'),
                'created_time' => date('H:i:s'),
                'created_dt' => date('Y-m-d H:i:s')
            ];
            if (!$preOrder) {
                \App\Models\OrderItem::create($orderItem);
                \App\Models\Orders::find($orderId)->update([
                    'ordersubtotal' => $cardInfo['order_subtotal'],
                    'ordertotal' => $cardInfo['order_total'],
                    'orderbv' => !empty($standBy->bv) ? $standBy->bv : 0,
                    'orderqv' => !empty($standBy->qv) ? $standBy->qv : 0,
                    'ordercv' => !empty($standBy->cv) ? $standBy->cv : 0,
                ]);
            } else {
                \App\Models\PreOrderItem::create($orderItem);
                \App\Models\PreOrder::find($orderId)->update([
                    'ordersubtotal' => $cardInfo['order_subtotal'],
                    'ordertotal' => $cardInfo['order_total'],
                    'orderbv' => !empty($standBy->bv) ? $standBy->bv : 0,
                    'orderqv' => !empty($standBy->qv) ? $standBy->qv : 0,
                    'ordercv' => !empty($standBy->cv) ? $standBy->cv : 0,
                ]);
            }
        } else {
            $standBy = \App\Models\Products::find(1);
            $orderItem = [
                'orderid' => $orderId,
                'productid' => 1,
                'quantity' => 1,
                'itemprice' => (string)$standBy->price,
                'bv' => !empty($standBy->bv) ? (string)$standBy->bv : 0,
                'qv' => !empty($standBy->qv) ? (string)$standBy->qv : 0,
                'cv' => !empty($standBy->cv) ? (string)$standBy->cv : 0,
                'created_date' => date('Y-m-d'),
                'created_time' => date('H:i:s'),
                'created_dt' => date('Y-m-d H:i:s')
            ];
            if (!$preOrder) {
                \App\Models\OrderItem::create($orderItem);
                $productInfo = \App\Models\Products::find($product->id);
                $orderItem = [
                    'orderid' => $orderId,
                    'productid' => $productInfo->id,
                    'quantity' => 1,
                    'itemprice' => (string)$productInfo->price,
                    'bv' => !empty($productInfo->bv) ? (string)$productInfo->bv : 0,
                    'qv' => !empty($productInfo->qv) ? (string)$productInfo->qv : 0,
                    'cv' => !empty($productInfo->cv) ? (string)$productInfo->cv : 0,
                    'created_date' => date('Y-m-d'),
                    'created_time' => date('H:i:s'),
                    'created_dt' => date('Y-m-d H:i:s')
                ];
                \App\Models\OrderItem::create($orderItem);
            } else {
                \App\Models\PreOrderItem::create($orderItem);
                $productInfo = \App\Models\Products::find($product->id);
                $orderItem = [
                    'orderid' => $orderId,
                    'productid' => $productInfo->id,
                    'quantity' => 1,
                    'itemprice' => (string)$productInfo->price,
                    'bv' => !empty($productInfo->bv) ? (string)$productInfo->bv : 0,
                    'qv' => !empty($productInfo->qv) ? (string)$productInfo->qv : 0,
                    'cv' => !empty($productInfo->cv) ? (string)$productInfo->cv : 0,
                    'created_date' => date('Y-m-d'),
                    'created_time' => date('H:i:s'),
                    'created_dt' => date('Y-m-d H:i:s')
                ];
                \App\Models\PreOrderItem::create($orderItem);
            }

            if (!empty($ticketProduct)) {
                $orderItem = [
                    'orderid' => $orderId,
                    'productid' => $ticketProduct->id,
                    'quantity' => 1,
                    'itemprice' => $ticketProduct->price,
                    'bv' => !empty($ticketProduct->bv) ? (string)$ticketProduct->bv : 0,
                    'qv' => !empty($ticketProduct->qv) ? (string)$ticketProduct->qv : 0,
                    'cv' => !empty($ticketProduct->cv) ? (string)$ticketProduct->cv : 0,
                    'created_date' => date('Y-m-d'),
                    'created_time' => date('H:i:s'),
                    'created_dt' => date('Y-m-d H:i:s')
                ];

                if (!$preOrder) {
                    \App\Models\OrderItem::create($orderItem);
                } else {
                    \App\Models\PreOrderItem::create($orderItem);
                }
            }
            if (!$preOrder) {
                \App\Models\Orders::find($orderId)->update([
                    'ordersubtotal' => $cardInfo['order_subtotal'],
                    'ordertotal' => $cardInfo['order_total'],
                    'orderbv' => ((int)$productInfo->bv + (int)$standBy->bv + (int)(!empty($ticketProduct) ? $ticketProduct->bv : 0)),
                    'orderqv' => ((int)$productInfo->qv + (int)$standBy->qv + (int)(!empty($ticketProduct) ? $ticketProduct->qv : 0)),
                    'ordercv' => ((int)$productInfo->cv + (int)$standBy->cv + (int)(!empty($ticketProduct) ? $ticketProduct->cv : 0)),
                ]);
            } else {
                \App\Models\PreOrder::find($orderId)->update([
                    'ordersubtotal' => $cardInfo['order_subtotal'],
                    'ordertotal' => $cardInfo['order_total'],
                    'orderbv' => ((int)$productInfo->bv + (int)$standBy->bv + (int)(!empty($ticketProduct) ? $ticketProduct->bv : 0)),
                    'orderqv' => ((int)$productInfo->qv + (int)$standBy->qv + (int)(!empty($ticketProduct) ? $ticketProduct->qv : 0)),
                    'ordercv' => ((int)$productInfo->cv + (int)$standBy->cv + (int)(!empty($ticketProduct) ? $ticketProduct->cv : 0)),
                ]);
            }
        }
    }

    // private function createPaymentMethod($userId, $addressId, $billingInfo, $cardInfo, $nmiResponse, $type = null)
    // {
    //     if (in_array($type, ['comp', 'voucher'])) {
    //         $type = \App\Models\PaymentMethodType::TYPE_ADMIN;
    //     }

    //     // set default values
    //     $paymentMethod = [
    //         'userID' => $userId,
    //         'firstname' => !empty($billingInfo['billing_first_name']) ? $billingInfo['billing_first_name'] : NULL,
    //         'lastname' => !empty($billingInfo['billing_last_name']) ? $billingInfo['billing_last_name'] : NULL,
    //         'primary' => 1,
    //         'token' => $nmiResponse['response']['Token'],
    //         'bill_addr_id' => $addressId,
    //         'pay_method_type' => (empty($type) ? \App\Models\PaymentMethodType::TYPE_T1_PAYMENTS : $type),
    //         'is_save' => 1
    //     ];

    //     // if ($this->isCreditPaymentType($type)) {
    //     //     $paymentMethod['cvv'] = $cardInfo['cvv'];
    //     //     $paymentMethod['expMonth'] = $cardInfo['expiry_date_month'];
    //     //     $paymentMethod['expYear'] = $cardInfo['expiry_date_year'];
    //     // }

    //     return \App\Models\PaymentMethods::create($paymentMethod)->id;
    // }

    private function isCreditPaymentType($type)
    {
        return in_array($type, [
            \App\Models\PaymentMethodType::TYPE_CREDIT_CARD,
            \App\Models\PaymentMethodType::TYPE_T1_PAYMENTS,
            \App\Models\PaymentMethodType::TYPE_PAYARC,
            \App\Models\PaymentMethodType::TYPE_METROPOLITAN
        ]);
    }

    /**
     * @param $userId
     * @param null $direction
     * @throws Exception
     */
    public static function placeToBinaryTree($userId, $direction = null)
    {
        $newUser = User::where('id', $userId)->get();

        if (!$newUser[0]) {
            throw new Exception('Invalid user');
        }

        $inBinary = DB::table('binary_plan')
            ->where('user_id', $newUser[0]->id)
            ->count();

        if ($inBinary > 0) {
            throw new Exception('This user already placed in the binary tree');
        }

        /** @var User $sponsor */
        $sponsor = User::where('distid', $newUser[0]->sponsorid)->first();
        $targetNode = BinaryPlanNode::where('user_id', $sponsor->id)->first();

        if (!$targetNode) {
            throw new Exception('Sponsor is not in tree');
        }

        if (!isset($direction)) {
            if (!$sponsor->binary_placement) {
                $direction = $targetNode->direction == "R" ? "right" : "left";
            } else {
                $direction = $sponsor->binary_placement;
            }

            if ($direction == "greater" || $direction == "lesser") {
                $currentLeftAmount = static::getCurrentLeftAmount($targetNode);
                $totalLeft = $currentLeftAmount + $sponsor->getCurrentLeftCarryover();

                $currentRightAmount = static::getCurrentRightAmount($targetNode);
                $totalRight = $currentRightAmount + $sponsor->getCurrentRightCarryover();
                if ($totalLeft == $totalRight) {
                    $direction = $targetNode->direction == "R" ? "right" : "left";
                }
                if ($direction == "greater") {
                    if ($totalLeft > $totalRight) {
                        $direction = "left";
                    } else {
                        $direction = "right";
                    }
                } else if ($direction == "lesser") {
                    if ($totalLeft > $totalRight) {
                        $direction = "right";
                    } else {
                        $direction = "left";
                    }
                }
            }

            if ($direction == BinaryPlanManager::DIRECTION_AUTO) {
                $lastEnrolled = static::getLastEnrolledUser($sponsor, $newUser[0]);

                $direction = $targetNode->direction;

                if ($lastEnrolled) {
                    $node = BinaryPlanManager::getNodeByAgentTsa($lastEnrolled);

                    if ($node) {
                        $direction = $node->direction === trim(BinaryPlanNode::DIRECTION_LEFT)
                            ? BinaryPlanManager::DIRECTION_RIGHT
                            : BinaryPlanManager::DIRECTION_LEFT;
                    } else {
                        //If can not find tree yet, check the sponsor's direction
                        $direction = $targetNode->direction === trim(BinaryPlanNode::DIRECTION_LEFT)
                            ? BinaryPlanManager::DIRECTION_RIGHT
                            : BinaryPlanManager::DIRECTION_LEFT;
                    }
                } else {
                    $direction = $targetNode->direction === trim(BinaryPlanNode::DIRECTION_LEFT)
                        ? BinaryPlanManager::DIRECTION_RIGHT
                        : BinaryPlanManager::DIRECTION_LEFT;
                }
            }
        } else {
            switch (strtoupper($direction)) {
                case 'L':
                    $direction = BinaryPlanManager::DIRECTION_LEFT;
                    break;
                case 'R':
                    $direction = BinaryPlanManager::DIRECTION_RIGHT;
                    break;
                default:
                    //If can not find tree yet, check the sponsor's direction
                    $direction = $targetNode->direction === trim(BinaryPlanNode::DIRECTION_LEFT)
                        ? BinaryPlanManager::DIRECTION_LEFT
                        : BinaryPlanManager::DIRECTION_RIGHT;
                    break;
            }
        }

        HoldingTank::placeAgentsToBinaryViewer($targetNode, $newUser, $direction);
    }

    /**
     * @param $targetNode
     * @return int
     */
    private static function getCurrentLeftAmount($targetNode)
    {
        $mondayDate = date('Y-m-d', strtotime('monday this week'));
        $leftLeg = BinaryPlanManager::getLeftLeg($targetNode);
        $currentLeftAmount = 0;
        if ($leftLeg) {
            $currentLeftAmount = BinaryPlanManager::getNodeTotal($leftLeg, $mondayDate);
        }
        return $currentLeftAmount;
    }

    /**
     * @param $targetNode
     * @return int
     */
    private static function getCurrentRightAmount($targetNode)
    {
        $mondayDate = date('Y-m-d', strtotime('monday this week'));
        $rightLeg = BinaryPlanManager::getRightLeg($targetNode);
        $currentRightAmount = 0;
        if ($rightLeg) {
            $currentRightAmount = BinaryPlanManager::getNodeTotal($rightLeg, $mondayDate);
        }
        return $currentRightAmount;
    }

    /**
     * @param $sponsor
     * @param $currentUser
     * @return string
     */
    private static function getLastEnrolledUser($sponsor, $currentUser)
    {
        return DB::table('users')
            ->select('distid')
            ->where('users.sponsorid', $sponsor->distid)
            ->where('users.distid', '<>', $currentUser->distid)
            ->orderBy('created_dt', 'DESC')
            ->pluck('distid')
            ->first();
    }

    /**
     * @param $productId
     * @param $countryCode
     * @return int
     */
    private function determineSubscriptionProduct($productId, $countryCode)
    {
        if ($productId == 1) {
            return 33;
        }

        if ($productId == 2 && $countryCode->is_tier3 === 1) {
            return 26;
        }

        return 11;
    }

    private function createEwalletPaymentMethod($userId)
    {
        $paymentMethod = [
            'userID' => $userId,
            'pay_method_type' => \App\Models\PaymentMethodType::TYPE_E_WALET,
            'created_at' => now(),
            'updated_at' => now()
        ];

        return \App\Models\PaymentMethods::create($paymentMethod)->id;
    }


    private function addBoomerangs($userId, $numBoomerangs)
    {
        BoomerangInv::create([
            'userid' => $userId,
            'pending_tot' => 0,
            'available_tot' => (int)$numBoomerangs
        ]);
    }
}
