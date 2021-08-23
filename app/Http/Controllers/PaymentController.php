<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use App\Models\PaymentMethodType;
use App\Models\Country;
use App\Models\CurrencyConverter;
use App\Models\OrderConversion;
use App\Models\FakeAccount;
use App\Models\MerchantTransactionTracker;
use App\Helpers\ApiHelper;
use App\Helpers\Billing;
use App\Helpers\UserHelper;
use Carbon\Carbon;
use App\Models\Products;
use DB;
use Illuminate\Http\Request;
use Log;
use Session;
use Validator;
use Config;
use App\Services\UserCreationService;
use App\Services\UserPaymentMethodService;

class PaymentController extends Controller
{
    const TMT_COMPLETED_STATUS = 'complete';
    const MAX_CHECKOUT_ERROR_COUNT = 3;

    private $mode;

    public function __construct()
    {
        $this->mode = \Config::get('const.mode');

        // Disable this for testing, enable when live
        $this->middleware('check_sponsor', ['except' => [
            'callback'
        ]]);

        $this->country = new Country();
    }


    private function validateNewCard($data)
    {
        $validator = Validator::make($data, [
            'credit_card_name' => 'required|max:50',
            'credit_card_number' => 'required',
            'expiry_date' => 'required',
            'cvv' => 'required',
        ]);
        $validator->after(function ($validator) use ($data) {
            $expiryDate = $expiryDate = trim(str_replace(' ', '', $data['expiry_date']));
            $expireDateParts = explode('/', $expiryDate);
            if (!isset($expireDateParts[0])) {
                $validator->errors()->add('expiry_date', __('lang.INVALID_EXPIRY_DATE'));
            } elseif (!isset($expireDateParts[1])) {
                $validator->errors()->add('expiry_date', __('lang.INVALID_EXPIRY_DATE'));
            } else {
                if (strlen(trim($expireDateParts[1])) > 2 || strlen(trim($expireDateParts[1])) <= 1) {
                    $validator->errors()->add('expiry_date', __('lang.INVALID_EXPIRY_DATE'));
                }
            }
        });
        if ($validator->fails()) {
            return ['error' => 1, 'validator' => $validator];
        }
        return ['error' => 0, 'validator' => $validator];
    }

    private function constructAddressInfo($sponsorInfo)
    {
        $primaryAddress = $this->constructPrimaryAddress($sponsorInfo);
        $billingAddress = $this->constructBillingAddress($sponsorInfo);
        $shippingAddress = $this->constructShippingAddress($sponsorInfo);

        return [
            'primary' => $primaryAddress,
            'billing' => $billingAddress,
            'shipping' => $shippingAddress
        ];
    }

    public function createUserAPI(Request $request)
    {
        if (strtolower(Config::get('app.env'))==="prod") {
            $response = [
                'error'     => 1,
                'message'   => 'Unauthorized'
            ];
            return response()->json($response);
            exit(0);
        }
        //standby
        $standByProduct = \App\Models\Products::find(1);
        $productInfo = $standByProduct;

        $faker = new FakeAccount();

        $fakeUser = $faker->getFakeUser($request->sponsor_id);
        $createRecords = new \CreateRecords();
        $userId = $createRecords->create(
            $fakeUser,
            $productInfo,
            $faker->getFakeAddress(),
            $faker->getFakeCreditCard(),
            $faker->getFakeNMIResponse(),
            5,
            11,
            null,
            3244300
        );

        $binary_log     = \App\Models\BinaryPlacementLog::where('user_id', $userId)->first();
        $binary_plan    = \App\Models\BinaryPlanNode::where('user_id', $userId)->first();
        $user           = \App\Models\User::find($userId);
        $response = [
            'error'     => 0,
            'message'   => 'User created',
            'binary_log'=> $binary_log,
            'binary_plan'=> $binary_plan,
            'account'   => $user
        ];
        return response()->json($response);
    }

    /*
     * /payment/checkout
     * This processes payment after user clicks checkout button
     */
    public function doPayment(Request $request)
    {
        DB::beginTransaction();

        $req = $request;

        # From declined form
        $source = $req->get('source', '');
        $unicryptFromDeclined = $req->get('unicrypt_declined', '');

        $retryDeclinedByUnicrypt = ($source == 'declined' && $unicryptFromDeclined == 'true') ? true : false;

        // dd($retryDeclinedByUnicrypt);

        $orderConversionId = session()->has('order_conversion_id') ? session('order_conversion_id') : null;
        $billing_country = session('billing_country');

        // Log::info("doPayment  ",$req->all());
        // Log::info("order conversion id -> ".session('order_conversion_id'));
        // Log::info("country conversion id -> ".session('country_conversion'));
        // if ($req->new_card == 1 && $req->source == 'declined') {
        //     //new card
        //     $res = $this->validateNewCard($req->all());
        //     if ($res['error'] == 1) {
        //         return redirect('payment/declined')
        //             ->withErrors($res['validator'])
        //             ->withInput();
        //     }
        // } else if ($req->new_card == 1 && $req->source == 'checkout') {
        //     //new card
        //     $res = $this->validateNewCard($req->all());
        //     if ($res['error'] == 1) {
        //         return redirect('payment/checkout')
        //             ->withErrors($res['validator'])
        //             ->withInput();
        //     }
        // }

        $sessionId = session('session_id');
        $sponsorInfo = session('sponsor_information');
        $sponsorInfo['payment_type'] = 1;
        $addressInfo = $this->constructAddressInfo($sponsorInfo);

        $sponsorInfo['sponsor'] = session('sponsor');
        $sponsorInfo['sponsor_username'] = session('sponsor_username');
        $sponsorInfo['email'] = $sponsorInfo['email'];
        $sponsorInfo['phone_number'] = $sponsorInfo['mobile_number'];
        $sponsorEmail = strtolower($sponsorInfo['email']);
        $userAlreadyExists = \App\Models\User::where('email', $sponsorEmail)->count();

        # FIX IT
        if ($userAlreadyExists && $source != 'declined') {
            DB::rollback();
            session()->forget(['vitals', 'sponsor_information', 'product_id', 'ticket_purchase', 'discount_code', 'coupon_code_id', 'discount_amount']);
            return redirect('/vitals')
                ->with('message', __("lang.YOU_HAVE_ALREADY_AN_ACTIVE_ACCOUNT"))
                ->withInput();
        }

        $orderTotal = 0 + \App\Models\Products::getShippingValue();
        $numBoomerangs = 0;
        //standby
        $standByProduct = \App\Models\Products::find(1);
        $productInfo = $standByProduct;
        $orderTotal = $orderTotal + $standByProduct->price;
        $numBoomerangs = $numBoomerangs + $productInfo->num_boomerangs;
        //optional product
        $optionalProduct = null;
        if (!empty(session('product_id'))) {
            $optionalProduct = \App\Models\Products::find(session('product_id'));
            $orderTotal = $orderTotal + $optionalProduct->price;
            $productInfo = $optionalProduct;
            $numBoomerangs = $numBoomerangs + $productInfo->num_boomerangs;
        }

        //ticket product
        $ticketProduct = null;
        if (!empty(session('ticket_purchase'))) {
            $ticketProduct = \App\Models\Products::find(\App\Models\Products::TICKET_PURCHASE_PRODUCT);
            $orderTotal = $orderTotal + $ticketProduct->price;
            $numBoomerangs = $numBoomerangs + $ticketProduct->num_boomerangs;
        }
        $orderId = md5($sponsorEmail . '' . time());
        $orderSubTotal = $orderTotal;

        if (!empty(session('discount_code')) && !empty(session('coupon_code_id')) && !empty(session('discount_amount'))) {
            $orderTotal = $orderTotal - session('discount_amount');
        }

        if ($orderTotal <= 0) {
            $orderTotal = 0;
        }

        if ($billing_country->currency != 'USD' && $orderTotal > 0) {
            $convertObject = CurrencyConverter::convertCurrency(number_format($orderTotal, 2, '', ''), $billing_country->currency, null);
            $orderTotalConvert = substr_replace($convertObject['amount'], '.', -2, 0);
            $orderConversion = new OrderConversion();

            //Saving Order Conversion
            $orderConversion->fill([
                'session_id' => session_id(),
                'original_amount' => number_format($orderTotal, 2, '', ''),
                'original_currency' => "USD",
                'converted_amount' => $convertObject["amount"],
                'converted_currency' => $convertObject['currency'],
                'exchange_rate' => $convertObject['exchange_rate'],
                'expires_at' => now()->addMinutes(30)
            ]);

            $orderConversion->save();
            $orderConversionId = $orderConversion->id;
        } else {
            $orderTotalConvert = $orderTotal;
        }

        if ($billing_country->merchant == 'ipaytotal') {
            $cardnumber = $sponsorInfo['credit_card_number'];
            $cvv = $sponsorInfo['cvv'];
            $expiryDate = trim(str_replace(' ', '', $sponsorInfo['expiry_date']));

            if (strlen($sponsorInfo['expiry_date'])<1 && $orderTotal == 0) {
                $expiryDate = "02/23";
            }

            $expireDateParts = explode('/', $expiryDate);

            # Format the Year from the CC if have 2 digits.
            list($cc_month, $cc_year) = explode('/', $expiryDate);
            $cc_year = (strlen($cc_year) == 2) ? '20'.$cc_year : $cc_year;

            $cardInfo = [
                'credit_card_number' => trim(str_replace(' ', '', $cardnumber)),
                'cvv' => trim(str_replace(' ', '', $cvv)),
                'expiry_date_month' => $cc_month,
                'expiry_date_year' => $cc_year,
                'order_total' => $orderTotal,
                'order_subtotal' => $orderSubTotal,
                'is_save' => null,
            ];

            if ($req->new_card == 1 && !$retryDeclinedByUnicrypt) {
                //if new card added overwrite existing card

                $expiryDate = trim(str_replace(' ', '', $req->expiry_date));
                # Format the Year from the CC if have 2 digits.
                list($cc_month, $cc_year) = explode('/', $expiryDate);
                $cc_year = (strlen($cc_year) == 2) ? '20'.$cc_year : $cc_year;
                $cardInfo = [
                    'credit_card_number' => trim(str_replace(' ', '', $req->credit_card_number)),
                    'cvv' => trim(str_replace(' ', '', $req->cvv)),
                    'expiry_date_month' => $cc_month,
                    'expiry_date_year' => $cc_year,
                    'order_total' => $orderTotal,
                    'order_subtotal' => $orderSubTotal,
                    'is_save' => null,
                ];
            }
        } else {
            $cardInfo = [
                'order_total' => $orderTotal,
                'order_subtotal' => $orderSubTotal,
            ];
        }

        $is_coach = false;
        $is_business = false;
        $is_firstclass = false;

        $primary_coach_merch_limit = null;

        if (isset($optionalProduct)) {
            $is_coach = $optionalProduct["id"] === 2 ? true : false;
            $is_business = $optionalProduct["id"] === 3 ? true : false;
            $is_firstclass = $optionalProduct["id"] === 4 ? true : false;
        }


//         $country_id = \App\Models\Country::getCountryId($addressInfo['billing']["country"]);
//         $defaultMerch = $this->checMerchByCountry($country_id);


//         // check for USA and coach, if US and coach this is subject to merchant rotation
//         if($addressInfo['billing']["country"] === 'US' && $is_coach) {

//             // set the primary level to coach payment_type_country table
//             // and payment_method_type table
//             $primary_level_merch = 'coach_primary';
//             $limit_name_merch = 'limit_coach';

//             // check the primary merch for coach by county
//             $primary_coach_merch = $this->checkPrimaryMerchByLevel($country_id, $primary_level_merch);

//             // check it daily_limit was reached
//             $primary_coach_merch_limit = $this->checkMerchLimit($primary_coach_merch, $orderTotal, $limit_name_merch);

//             if ($primary_coach_merch_limit) {
//                 // limit has been reached
//                 // $paymentType = $defaultMerch;
//                 $paymentType = \App\Models\PaymentMethodType::TYPE_T1_PAYMENTS;

//             } else {
//                 // limit has been not been reached, use pay arc
//                 $paymentType = $primary_coach_merch;
//             }

//         // check for USA and Business, if US and Business this is subject to merchant limit
//         } elseif ($addressInfo['billing']["country"] === 'US' && $is_business) {


//             // set the primary level to business payment_type_country table
//             // and payment_method_type table
//             $primary_level_merch = 'business_primary';
//             $limit_name_merch = 'limit_business_class';

//             // check the primary merch for bussiness by county
//             $primary_business_merch = $this->checkPrimaryMerchByLevel($country_id, $primary_level_merch);

//             // check it daily_limit was reached
//             $primary_business_merch_limit = $this->checkMerchLimit($primary_business_merch, $orderTotal, $limit_name_merch);


//             if ($primary_business_merch_limit) {
//                 // limit has been reached
//                 // $paymentType = $defaultMerch;
//                 $paymentType = \App\Models\PaymentMethodType::TYPE_T1_PAYMENTS;

//             } else {
//                 // limit has been not been reached, use pay arc
//                 $paymentType = $primary_business_merch;
//             }


//         // check for USA and First Class , if US and First Class this is subject to merchant limit
//         } elseif ($addressInfo['billing']["country"] === 'US' && $is_firstclass) {

//             // set the primary level to first class payment_type_country table
//             // and payment_method_type table
//             $primary_level_merch = 'first_class_primary';
//             $limit_name_merch = 'limit_first_class';

//             // check the primary merch for frist class by county
//             $primary_fc_merch =  $this->checkPrimaryMerchByLevel($country_id, $primary_level_merch);

//             // check it daily_limit was reached
//             $primary_fc_merch_limit = $this->checkMerchLimit($primary_fc_merch, $orderTotal, $limit_name_merch);

//             if ($primary_fc_merch_limit) {
//                 // limit has been reached
//                 // $paymentType = $defaultMerch;
//                 $paymentType = \App\Models\PaymentMethodType::TYPE_T1_PAYMENTS;

//             } else {
//                 // limit has been not been reached, use pay arc
//                 $paymentType = $primary_fc_merch;
//             }

//         // Chose the payment method for standby class - no extra packs purchased
//         } else {
//             $countries = \App\Models\Helper::checkTMTAllowPayment(session('sponsor_information')['country']);
        // //            $paymentType = $countries > 0 ? \App\Models\PaymentMethodType::TYPE_T1_PAYMENTS : \App\Models\PaymentMethodType::TYPE_CREDIT_CARD;

//             $primary_level_merch = 'standby_class_primary';
//             // check the primary merch for frist class by county
//             $primary_standby_merch =  $this->checkPrimaryMerchByLevel($country_id, $primary_level_merch);

//             $paymentType = $countries > 0 ? $primary_standby_merch : \App\Models\PaymentMethodType::TYPE_CREDIT_CARD;
//         }

        if (env('SKIP_BILLING', false) === true) {
            $nmiResponse = [
                'response' => ['Token' => '', 'Authorization' => 'DEV']
            ];
        } elseif ($orderTotal > 0) {
            //Complete Payment
            // $nmi = new \NMI();

            // if ((bool)env('FORCE_METRO_US', false) === true && $addressInfo['billing']["country"] == 'US') {
            //     // $paymentType = PaymentMethodType::TYPE_METROPOLITAN;
            //     //Temporary solution to force everyone to go over Payarc
            //     $paymentType = PaymentMethodType::TYPE_PAYARC;
            // }

            #if ((bool)env('FORCE_PAYARC', false) === true && $addressInfo['billing']["country"] == 'US') {
            # All transactions needs use Payarc now
            // if ((bool)env('FORCE_PAYARC', false) === true) {
            //     $paymentType = PaymentMethodType::TYPE_PAYARC;
            // }

            // $nmiResponse = $nmi->doPayment($cardInfo, $addressInfo['billing'], $orderId, $paymentType, $orderConversionId);
            // if (!$nmiResponse['success']) {
            //     $this->handleCheckoutError();
            //     return redirect('/payment/declined')
            //         ->with('message', $nmiResponse['message'])
            //         ->withInput();
            // }

            if ($billing_country->merchant == 'unicrypt' || $retryDeclinedByUnicrypt) {
                $paymentType = PaymentMethodType::TYPE_UNICRYPT;
            } else {
                $paymentType = PaymentMethodType::TYPE_IPAYTOTAL;
            }

            session()->put('paymentType', $paymentType);
            $account_status = 'PENDING APPROVAL';
            $createRecords = new \CreateRecords();

            if (!$retryDeclinedByUnicrypt) {
                $userId = $createRecords->create(
                    $sponsorInfo,
                    $productInfo,
                    $addressInfo,
                    $cardInfo,
                    $nmiResponse=null,
                    $numBoomerangs,
                    $paymentType,
                    $ticketProduct,
                    $orderConversionId,
                    $account_status
                );
            } else {
                $userId = session('user_id');
            }


            $data = [
                'userId'       => $userId,
                'orderTotal'   => $orderTotal,
                'callback_url' => route('thank-you')
            ];

            if ($billing_country->merchant == 'unicrypt' || $retryDeclinedByUnicrypt) {
                DB::commit();

                $response = ApiHelper::request('POST', '/unicrypt/create-invoice', $data);
                $response_data = json_decode($response->getBody());

                $this->addDiscountCode($userId);
                session(['user_id' => $userId, 'orderhash' => $response_data->data->orderhash]);

                if ($response_data->data->redirect_url) {
                    return redirect($response_data->data->redirect_url);
                } else {
                    return redirect('/payment/declined/')->withErrors('Sorry! We received an error from the payment gateway, please try again.');
                }

            } else {

                $response = Billing::iPayTotal($sponsorInfo, $cardInfo, $addressInfo, $userId, $orderTotalConvert, $productInfo, $ticketProduct);
                $this->addDiscountCode($userId);
                // dd($response, $cardInfo, $addressInfo, $userId);

                Log::info("Response ipaytotal", $response);

                if (isset($response['status'])) {

                    if ($response['status'] == 'success') {
                        $merchtracker = new MerchantTransactionTracker();
                        $merchtracker->merchant_id = 6;
                        $merchtracker->pre_order_id = $response['order_id'];
                        $merchtracker->status = 'PAID';
                        $merchtracker->transaction_id = $response['merchant_transaction_id'];
                        $merchtracker->cron_processed = 1;
                        $merchtracker->save();


                        $preOrder = PreOrder::where('id', '=', $response['order_id'])->first();

                        if ($response['merchant_transaction_id']) {
                            $preOrder->orderhash = $response['merchant_transaction_id'];
                            $preOrder->trasnactionid = $response['merchant_transaction_id'];
                        }

                        $userPaymentId = UserPaymentMethodService::createUserPaymentMethod($response, $cardInfo, $addressInfo['billing'], $userId);
                        UserCreationService::makeUserActive($preOrder->userid);
                        $orderId = UserCreationService::copyPreOrderToOrder($preOrder, $userPaymentId);
                        UserCreationService::copyPreOrderItems($preOrder->id, $orderId);
                        // UserCreationService::placeUserInBinaryTree($preOrder->userid);
                        UserCreationService::loungeQueue($preOrder->userid, $sponsorInfo['sponsor']);
                        
                        DB::commit();

                        session(['user_id' => $userId, 'order_id' => $response['order_id'], 'status' => 'success']);
                        return redirect('/thank-you');
                    } elseif ($response['status'] == '3d_redirect') {
                        DB::rollback();

                        $merchtracker = new MerchantTransactionTracker();
                        $merchtracker->merchant_id = 6;
                        $merchtracker->pre_order_id = $response['order_id'];
                        $merchtracker->save();

                        session(['user_id' => $userId, 'order_id' => $response['order_id'], 'status' => '3d_redirect']);
                        return redirect($response['redirect_3ds_url']);
                    } else {
                        DB::rollback();
                        
                        $merchtracker = new MerchantTransactionTracker();
                        $merchtracker->merchant_id = 6;
                        $merchtracker->pre_order_id = $response['order_id'];
                        $merchtracker->status = 'CANCELLED';
                        $merchtracker->transaction_id = $response['merchant_transaction_id'] ?? null;
                        $merchtracker->save();

                        session(['user_id' => $userId, 'orderStatus' => 'fail', 'order_id' => $response['order_id'], 'response_text' => $response['response_text']]);
                        return redirect('/payment/declined/')->withErrors($response['response_text']);
                    }
                } else {
                    DB::rollback();
                    return redirect('/payment/declined/');
                    // return back()->withErrors([$response['errors']]);
                }
            }
        } else {
            $nmiResponse = [
                'response' => ['Token' => '', 'Authorization' => 'COUPON#' . (!empty(session('discount_code')) ? session('discount_code') : '')]
            ];
        }

        if (!isset($userId)) {           
            $createRecords = new \CreateRecords();
            $userId = $createRecords->create(
                $sponsorInfo,
                $productInfo,
                $addressInfo,
                $cardInfo,
                $nmiResponse,
                $numBoomerangs,
                $paymentType='voucher',
                $ticketProduct,
                $orderConversionId,
                null
            );
        }
        $this->addDiscountCode($userId);
        session(['user_id' => $userId]);

        DB::commit();

        return redirect('/thank-you');
    }

    private function addBoomerang($userId, $numBoomerangs)
    {
        \App\Models\BoomerangInv::create([
            'userid' => $userId,
            'pending_tot' => 0,
            'available_tot' => (int)$numBoomerangs
        ]);
    }


    public function purchaseTicketsRegister(Request $request)
    {
        $purchase = $request->ticket_purchase;
        if ($purchase == 'yes') {
            session(['ticket_purchase' => true]);
            return redirect('/payment/checkout');
        } else {
            session()->forget(['ticket_purchase']);
            return response()->json(['error' => 0, 'url' => url('/payment/checkout')]);
        }
    }

    public function purchaseTickets()
    {
        session()->forget(['ticket_purchase']);

        // Disable this for testing, enable when live
        if (!session()->has('success_2fa') && !session()->has('optional_promotional')) {
            return redirect('/vitals');
        }

        $product = \App\Models\Products::find(\App\Models\Products::TICKET_PURCHASE_PRODUCT);
        return view('purchase-ticket', compact('product'));
    }

    public function cardDeclined()
    {
        $sessionId = session('session_id');

        $countries = $this->country->getCountries();

        return view('card-declined')->with([
            'sessionId' => $sessionId,
            'countries' => $countries,
        ]);
    }

    public function getStates(Request $request)
    {
        return \App\Models\Country::getStates($request->country_code);
    }


    public function applyCoupon(Request $request)
    {
        session()->forget(['coupon_code_id', 'discount_amount', 'discount_code', 'order_conversion_id']);
        $product_id = (!empty(Session::get('product_id')) ? Session::get('product_id') : '');
        $standby = \App\Models\Products::find(1);
        $product = [];
        if (!empty($product_id)) {
            $product = \App\Models\Products::find($product_id);
        }
        $discountCode = $request->coupon_code;
        $discountRec = \App\Models\DiscountCoupon::where('code', trim($discountCode))->where('is_used', '0')->first();
        $d['product'] = $product;
        $ticket_purchase = session('ticket_purchase');
        $ticket_product = [];
        $ticket_display = '';
        if ($ticket_purchase) {
            $ticket_product = \App\Models\Products::find(\App\Models\Products::TICKET_PURCHASE_PRODUCT);
            $d['ticket_product'] = $ticket_product;
            //Converting the currency on ticket price
            $convertObject = CurrencyConverter::convertCurrency(number_format($ticket_product->price, 2, '', ''), session('country_conversion'), null);
            $d['ticket_display'] = $convertObject['display_amount'];
        }
        $total = (!empty($product) ? $product->price : 0) + $standby->price + (!empty($ticket_product) ? $ticket_product->price : 0);
        if ($total <= 0) {
            $total = 0;
        }
        $d['discount'] = 0;
        $d['sub_total'] = 0;
        $d['total'] = $total;
        $d['standby'] = $standby;
        if (empty($discountRec)) {
            $d['voucher_code_valid'] = 0;
            $v = (string)view('discount_payment')->with($d);
            return response()->json(['error' => 1, 'valid' => 0, 'msg' =>  __('lang.INVALID_VOUCHER_CODE'), 'v' => $v]);
        }
        $discount = $discountRec->discount_amount;
        $total = ((!empty($product) ? $product->price : 0) + $standby->price + (!empty($ticket_product) ? $ticket_product->price : 0)) - $discount;
        $sub_total = (!empty($product) ? $product->price : 0) + $standby->price + (!empty($ticket_product) ? $ticket_product->price : 0);
        if ($total <= 0) {
            $total = 0;
        }
        $d['voucher_code_valid'] = 1;
        $d['discount'] = $discount;
        $d['sub_total'] = $sub_total;
        $d['total'] = $total;
        $d['country_conversion'] = (!empty(session('country_conversion'))?session('country_conversion'):'USD');
        $d['products_conversion'] = Products::getSubscriptionProductsWithConversion(session('country_conversion'));


        //Converting the currency
        if ($total<=0) {
            $total_to_convert = 0;
        } else {
            $total_to_convert = number_format($total, 2, '', '');
        }
        $convertObject = CurrencyConverter::convertCurrency($total_to_convert, session('country_conversion'), null);
        $orderConversion = new OrderConversion();

        session(['order_conversion_id' => $orderConversion->id]);
        $d['total_conversion'] = $convertObject['display_amount'];

        //Saving Order Conversion
        $orderConversion->fill([
            'session_id' => session_id(),
            'original_amount' => number_format($total, 2, '', ''),
            'original_currency' => "USD",
            'converted_amount' => $convertObject["amount"],
            'converted_currency' => $convertObject['currency'],
            'exchange_rate' => $convertObject['exchange_rate'],
            'expires_at' => now()->addMinutes(30)
        ]);

        $orderConversion->save();
        session([
            'discount_code' => $discountCode,
            'coupon_code_id' => $discountRec->id,
            'discount_amount' => $discount,
            'order_conversion_id' => $orderConversion->id
        ]);
        $v = (string)view('discount_payment')->with($d);
        return response()->json(['error' => 0, 'total' => $total, 'v' => $v, 'valid' => 1]);
    }

    public function removeCoupon()
    {
        session()->forget(['coupon_code_id', 'discount_amount']);
    }

    private function addDiscountCode($userId)
    {
        if (session()->has('coupon_code_id') && session()->has('discount_amount') && !empty(session('coupon_code_id')) && !empty(session('discount_amount'))) {
            $couponCodeId = session('coupon_code_id');
            \App\Models\DiscountCoupon::find($couponCodeId)->update([
                'is_used' => 1,
                'used_by' => $userId,
                'is_active' => 0
            ]);
            $order = \App\Models\PreOrder::select('*')->where('userid', $userId)->orderBy('id', 'desc')->first();
            if (!empty($order)) {
                \App\Models\PreOrder::where('id', $order->id)->update(['coupon_code' => $couponCodeId]);
            }
        }
    }

    public function doCheckoutPaymentConfirm()
    {
        // Log::info("doCheckoutPaymentConfirm Session -> ",session('products_conversion'));
        if (!session()->has('success_2fa')) {
            return redirect('vitals');
        }

        if (!session()->has('sponsor_information')) {
            return redirect('sponsor-information');
        }
        session()->forget(['discount_code', 'coupon_code_id', 'discount_amount']);
        $ticket_purchase = session('ticket_purchase');
        $productId = (!empty(session('product_id')) ? session('product_id') : '');
        $standby = \App\Models\Products::find(1);
        $product = [];
        if (!empty($productId)) {
            $product = \App\Models\Products::find($productId);
        }
        session_start();
        $sessionId = session_id();
        session(['session_id' => $sessionId]);
        $countries = $this->checkTMTAllowPayment(session('sponsor_information')['country']);
        $paymentType = $countries > 0 ? PaymentMethodType::getTMTPaymentType() : PaymentMethodType::getEnrollmentPaymentMethods();
        $countries = $rec = DB::table('country')
            ->select('*')
            ->whereNotIn('countrycode', ["SO", "KR", "KP", "IR", "IQ", "SY", "CU", "IN"])
            ->orderBy('country', 'asc')
            ->get();
        $ticket_product = '';
        $ticket_display = '';
        if ($ticket_purchase) {
            $ticket_product = \App\Models\Products::find(\App\Models\Products::TICKET_PURCHASE_PRODUCT);

            //Converting the currency on ticket price
            $convertObject = CurrencyConverter::convertCurrency(number_format($ticket_product->price, 2, '', ''), session('country_conversion'), null);
            $ticket_display = $convertObject['display_amount'];
        }
        $card_number = '';
        // $sponsor = session()->get('sponsor_information');
        // if (!empty($sponsor['credit_card_number'])) {
        //     $card_number = substr($sponsor['credit_card_number'], -4);
        // }
        $discount = 0;
        $sub_total = (!empty($standby->price) ? $standby->price : 0) + (!empty($product->price) ? $product->price : 0) + (!empty($ticket_product) ? $ticket_product->price : 0) + \App\Models\Products::getShippingValue();
        $total = $sub_total - $discount;

        //Converting the currency
        $convertObject = CurrencyConverter::convertCurrency(number_format($total, 2, '', ''), session('country_conversion'), null);

        $orderConversion = new OrderConversion();

        //Saving Order Conversion
        $orderConversion->fill([
            'session_id' => session_id(),
            'original_amount' => number_format($total, 2, '', ''),
            'original_currency' => "USD",
            'converted_amount' => $convertObject["amount"],
            'converted_currency' => $convertObject['currency'],
            'exchange_rate' => $convertObject['exchange_rate'],
            'expires_at' => now()->addMinutes(30)
        ]);
        session(['order_conversion_id' => $orderConversion->id]);


        $convertObject = CurrencyConverter::convertCurrency(number_format(\App\Models\Products::getShippingValue(),2,'',''), session('country_conversion'), null);
        session(['shipping_conversion' => $convertObject['display_amount']]);

        ////// End of conversion ///////

        return view('checkout-cart-item-confirm')->with([
            'discount' => 0,
            'standby' => $standby,
            'product' => $product,
            'sessionId' => $sessionId,
            'paymentMethods' => $paymentType,
            'countries' => $countries,
            'ticket_product' => $ticket_product,
            'ticket_display' => $ticket_display,
            'discount' => $discount,
            'sub_total' => $sub_total,
            'total' => $total,
            'total_conversion' => $convertObject['display_amount'],
            'country_conversion' => session('country_conversion'),
            'products_conversion' => Products::getSubscriptionProductsWithConversion(session('country_conversion')),
            'shipping_conversion' => session('shipping_conversion'),
            'card_number' => __('lang.CREDIT_CARD_ENDING_IN') . ' ' . $card_number,
        ]);
    }

    private function checkTMTAllowPayment($countryId)
    {
        return count(DB::table('payment_type_country')
            ->where('country_id', '=', $countryId)
            ->get());
    }

    public static function checMerchByCountry($countryId)
    {
        return DB::table('payment_type_country')
            ->where('country_id', '=', $countryId)
            ->value('payment_type');
    }

    public static function checkPrimaryMerchByLevel($countryId, $level)
    {
        return DB::table('payment_type_country')
            ->where('country_id', '=', $countryId)
            ->value($level);
    }

    public static function checkMerchLimit($paymentTypeId, $orderTotal, $limit_col_name)
    {
        // carbon get today's date
        $today = Carbon::now()->format('Y-m-d');

        // for testing only
        //$today = Carbon::tomorrow()->format('Y-m-d');


        $merchCoachLimit = DB::table('payment_method_type')
            ->where(['id' => $paymentTypeId])
            ->pluck($limit_col_name);
        $merchCoachLimit = intval($merchCoachLimit[0]);


        // DEAL WITH NULL AND ZERO VALUES
        if ($merchCoachLimit == 0) {
            return false;
        } else {
            $merchTotal = DB::table('orders')
                ->where(['created_date' => $today, 'payment_type_id' => $paymentTypeId])
                ->sum('ordertotal');

            // Note no rows returns 0 so code works on empty sets.
            // if the total amount spent on this processor plus the current cart total is more than
            // the coach limit, return true the limit has been reached
            return (intval($merchTotal + $orderTotal) > $merchCoachLimit) ? true : false;
        }
    }

    public static function checkAllMerchLimit($paymentTypeId, $orderTotal)
    {
        // carbon get today's date
        $today = Carbon::now()->format('Y-m-d');

        $merchFirstClassLimit = DB::table('payment_method_type')
            ->where(['id' => $paymentTypeId])
            ->pluck('limit_business_class');
        $merchFirstClassLimit = intval($merchFirstClassLimit[0]);

        $merchTotal = DB::table('orders')
            ->where(['created_date' => $today, 'payment_type_id' => $paymentTypeId])
            ->sum('ordertotal');

        // if the total amount spent on this processor plus the current cart total is more than
        // the coach limit, return true the limit has been reached
        return (intval($merchTotal + $orderTotal) > $merchFirstClassLimit) ? true : false;
    }


    /*
     * Construct distributors addresses
     */

    private function constructShippingAddress($sponsorInfo)
    {
        $billingInfo = [];
        if ($sponsorInfo['is_shipping_same'] == 'yes') {
            $billingInfo['apt'] = '';
            $billingInfo['address1'] = $sponsorInfo['primary_address_line_one'];
            $billingInfo['address2'] = $sponsorInfo['primary_address_line_two'];
            $billingInfo['city'] = $sponsorInfo['primary_city'];
            $billingInfo['state'] = $sponsorInfo['primary_state'];
            $billingInfo['postal_code'] = $sponsorInfo['primary_postal_code'];
            $billingInfo['country'] = $sponsorInfo['primary_country'];
            $billingInfo['billing_first_name'] = $sponsorInfo['firstname'];
            $billingInfo['billing_last_name'] = $sponsorInfo['lastname'];
            $billingInfo['payment_type'] = (isset($sponsorInfo['payment_type']) ? $sponsorInfo['payment_type'] : 1);
        } else {
            $billingInfo['apt'] = '';
            $billingInfo['address1'] = $sponsorInfo['shipping_address_line_one'];
            $billingInfo['address2'] = $sponsorInfo['shipping_address_line_two'];
            $billingInfo['city'] = $sponsorInfo['shipping_city'];
            $billingInfo['state'] = $sponsorInfo['shipping_state'];
            $billingInfo['postal_code'] = $sponsorInfo['shipping_postal_code'];
            $billingInfo['country'] = $sponsorInfo['shipping_country'];
            $billingInfo['billing_first_name'] = $sponsorInfo['firstname'];
            $billingInfo['billing_last_name'] = $sponsorInfo['lastname'];
            $billingInfo['payment_type'] = (isset($sponsorInfo['payment_type']) ? $sponsorInfo['payment_type'] : 1);
        }
        return $billingInfo;
    }

    private function constructBillingAddress($sponsorInfo)
    {
        $billingInfo = [];
        if ($sponsorInfo['is_billing_same'] == 'yes') {
            $billingInfo['apt'] = '';
            $billingInfo['address1'] = $sponsorInfo['primary_address_line_one'];
            $billingInfo['address2'] = $sponsorInfo['primary_address_line_two'];
            $billingInfo['city'] = $sponsorInfo['primary_city'];
            $billingInfo['state'] = $sponsorInfo['primary_state'];
            $billingInfo['postal_code'] = $sponsorInfo['primary_postal_code'];
            $billingInfo['country'] = $sponsorInfo['primary_country'];
            $billingInfo['billing_first_name'] = $sponsorInfo['firstname'];
            $billingInfo['billing_last_name'] = $sponsorInfo['lastname'];
            $billingInfo['payment_type'] = (isset($sponsorInfo['payment_type']) ? $sponsorInfo['payment_type'] : 1);
        } else {
            $billingInfo['apt'] = '';
            $billingInfo['address1'] = $sponsorInfo['billing_address_line_one'];
            $billingInfo['address2'] = $sponsorInfo['billing_address_line_two'];
            $billingInfo['city'] = $sponsorInfo['billing_city'];
            $billingInfo['state'] = $sponsorInfo['billing_state'];
            $billingInfo['postal_code'] = $sponsorInfo['billing_postal_code'];
            $billingInfo['country'] = $sponsorInfo['billing_country'];
            $billingInfo['billing_first_name'] = $sponsorInfo['firstname'];
            $billingInfo['billing_last_name'] = $sponsorInfo['lastname'];
            $billingInfo['payment_type'] = (isset($sponsorInfo['payment_type']) ? $sponsorInfo['payment_type'] : 1);
        }
        return $billingInfo;
    }

    private function constructPrimaryAddress($sponsorInfo)
    {
        $billingInfo = [];
        $billingInfo['apt'] = '';
        $billingInfo['address1'] = $sponsorInfo['primary_address_line_one'];
        $billingInfo['address2'] = $sponsorInfo['primary_address_line_two'];
        $billingInfo['city'] = $sponsorInfo['primary_city'];
        $billingInfo['state'] = $sponsorInfo['primary_state'];
        $billingInfo['postal_code'] = $sponsorInfo['primary_postal_code'];
        $billingInfo['country'] = $sponsorInfo['primary_country'];
        $billingInfo['billing_first_name'] = $sponsorInfo['firstname'];
        $billingInfo['billing_last_name'] = $sponsorInfo['lastname'];
        $billingInfo['payment_type'] = (isset($sponsorInfo['payment_type']) ? $sponsorInfo['payment_type'] : 1);
        return $billingInfo;
    }

    private function handleCheckoutError()
    {
        $errorCount = (int)session('checkout_error_count') + 1;
        if ($errorCount >= self::MAX_CHECKOUT_ERROR_COUNT) {
            session()->flush();
            return redirect('/enrollment/sponsor');
        }

        session()->put('checkout_error_count', $errorCount);
    }
}
