<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CurrencyConverter;
use App\Models\DiscountCoupon;
use App\Models\iDecide;
use App\Models\IPayOut;
use App\Models\OrderConversion;
use App\Models\Orders;
use App\Models\Products;
use App\Models\SaveOn;
use App\Models\User;
use App\Services\MailChimp as MailChimpService;
use App\Services\Twilio;
use CreateRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use NMI;
use SendMail;

class AdminController extends Controller
{
    private function getAddNewEnrollmentValidator()
    {
        $rules = [
            'distid' => 'required|unique:users,distid',
            'username' => 'required|max:255|regex:/[a-zA-Z][a-zA-Z0-9]+/|unique:users,username',
            'default_password' => 'required',
            'sponsorid' => 'required|exists:users,distid',
            'enrollment_date' => 'required|date',
            'email' => 'required|email|unique:users,email',

            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'date_of_birth' => 'required|date',
            'subscription_product' => 'required|integer|exists:products,id',
            'subscription_package' => 'required|integer|exists:products,id',

            'business_name' => 'sometimes|max:255',
            'phonenumber' => 'required|max:255',
            'mobilenumber' => 'sometimes|max:255',

            'address1' => 'required|max:255',
            'apt' => 'sometimes|max:50',
            'countrycode' => 'required|max:2',
            'city'=> 'required|max:255',
            'stateprov' => 'max:50',
            'postalcode' => 'required|max:10',

            'entered_by' => 'required|integer|exists:users,id',
            'credit_card_name' => 'sometimes',
            'credit_card_number' => 'sometimes|integer',
            'expiration_date' => 'sometimes',
            'cvv' => 'sometimes|between:2,4',
            'voucher_code' => 'sometimes|alphanum|size:6|exists:discount_coupon,code'
        ];

        $messages = [
            'username.required' => 'Username is required',
            'username.unique' => 'Username is in use. Please choose another.',
            'default_password.required' => 'Default password is required',
            'sponsorid.required' => 'Sponsor is required',
            'enrollment_date.required' => 'Enrollment date is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already in use',

            'firstname.required' => 'First name is required',
            'lastname.required' => 'Last name is required',

            'phonenumber.required' => 'Phone number is required',

            'address1.required' => 'Address 1 is required',
            'city.required' => 'City is required',
            'stateprov.required' => 'State/Province is required',
            'postalcode.required' => 'Postal Code is required',

            'credit_card_name.required' => 'Cardholder Name is required',
            'credit_card_number.required' => 'Credit card number is required',
            'cvv.required' => 'CVV is required',
            'cvv.max' => 'CVV cannot exceed 4 characters',
            'expiration_date.required' => 'Expiration date is required',
            'expiration_date.date' => 'Invalid expiration date',
            'voucher_code.alphanum' => 'Voucher code is invalid',
            'voucher_code.size' => 'Voucher code is invalid',
            'voucher_code.exists' => 'Voucher code is invalid'
        ];

        return Validator::make(request()->post(), $rules, $messages);
    }

    private function calculateTotal($standByProductInfo, $productInfo, $ticketProduct)
    {
        // Standby can't buy an event ticket, and has no special logic
        if ($productInfo->id == 1) {
            return $productInfo->price;
        }

        $total = $standByProductInfo->price + $productInfo->price;

        if ($ticketProduct) {
            $total += $ticketProduct->price;
        }

        return $total;
    }

    private function doCreditCardPayment($cardInfo, $addressInfo, $orderId, $paymentType, $orderConversionId = null)
    {
        //Complete Payment
        $nmi = new NMI();

        $nmiResponse = $nmi->doPayment($cardInfo, $addressInfo['billing'], $orderId, $paymentType, $orderConversionId);

        return array($nmiResponse, $orderConversionId);
    }


    private function doVoucherPayment($userId, $voucherCode, $total)
    {
        $coupon = DiscountCoupon::query()
            ->where('code', '=', $voucherCode)
            ->where('is_used', '=', 0)
            ->where('is_active', '=', 1)
            ->first();

        if (!$coupon) {
            return false;
        }

        if ($total > $coupon->discount_amount) {
            return false;
        }

        $coupon->fill([
            'is_used' => 1,
            'used_by' => $userId,
            'is_active' => 0
        ]);

        $coupon->save();

        $order = Orders::where('userid', $userId)
                        ->orderBy('id', 'desc')
                        ->first();

        if (!empty($order)) {
            Orders::where('id', $order->id)
                ->update(['coupon_code' => $coupon->id]);
        }

        return true;
    }

    public function addNewEnrollment()
    {
        $validator = $this->getAddNewEnrollmentValidator();

        if ($validator->fails()) {
            return response()->json([
                'error' => 1,
                'msg' => $this->generateErrorMessageFromValidator($validator)
            ])->setStatusCode(400);
        }

        list($sponsorInfo, $addressInfo, $cardInfo, $paymentType) = $this->createFieldsFromRequest();

        $orderId = md5($sponsorInfo['email'] . '' . time());

        $ticketProductId = filter_var(request()->post('ticket_product_id'), FILTER_VALIDATE_INT);
        $ticketProduct = $ticketProductId ? Products::find($ticketProductId) : null;
        $standByProductInfo = Products::find(1);
        $productInfo = Products::find(request()->post('subscription_package'));

        $total = 0;

        if ($paymentType != 'comp') {
            $total = $this->calculateTotal($standByProductInfo, $productInfo, $ticketProduct);
        }

        $cardInfo['order_subtotal'] = $total;
        $cardInfo['order_total'] = $total;

        $orderConversionId = null;

        $nmiResponse = [
            'success' => true,
            'order_id' => $orderId,
            'response' => [
                'Token' => mt_rand(),
                'Authorization' => 'Admin'
            ]
        ];

        if (!in_array($paymentType, ['comp', 'voucher']) && $total > 0) {

            $orderConversionId = null;

            $country = $addressInfo['billing']['country'];
            if ($country != 'US') {
                $conversionResult = CurrencyConverter::convertCurrency($total * 100, $country);

                if (!$conversionResult) {
                    return response()->json([
                       'success' => false,
                       'error' => 1,
                       'msg' => 'Currency conversion error'
                    ]);
                }

                $orderConversion = new OrderConversion();

                $orderConversion->fill([
                    'original_amount' => $total * 100,
                    'original_currency' => 'USD',
                    'converted_amount' => $conversionResult['amount'],
                    'converted_currency' => $conversionResult['currency'],
                    'exchange_rate' => $conversionResult['exchange_rate'],
                    'display_amount' => $conversionResult['display_amount'],
                    'expires_at' => now()->addMinutes(30)
                ]);

                $orderConversion->save();

                $orderConversionId = $orderConversion->id;
            }

            list($nmiResponse, $orderConversionId) = $this->doCreditCardPayment(
                $cardInfo,
                $addressInfo,
                $orderId,
                $paymentType,
                $orderConversionId
            );

            if (!$nmiResponse['success']) {
                return response()->json([
                    'error' => 1,
                    'msg' => $nmiResponse['message']
                ]);
            }
        }

        $numBoomerangs = $standByProductInfo->num_boomerangs;

        if ($productInfo->id > 1) {
            $numBoomerangs += $productInfo->num_boomerangs;

            if ($ticketProduct) {
                $numBoomerangs += $ticketProduct->num_boomerangs;
            }
        }

        $createRecords = new CreateRecords();

        $userId = $createRecords->create(
            $sponsorInfo,
            $productInfo,
            $addressInfo,
            $cardInfo,
            $nmiResponse,
            $numBoomerangs,
            $paymentType,
            $ticketProduct,
            $orderConversionId
        );

        CreateRecords::placeToBinaryTree($userId);

        if ($paymentType == 'voucher') {
            $voucherCode = request()->post('voucher_code');
            $success = $this->doVoucherPayment($userId, $voucherCode, $total);

            if (!$success) {
                return response()->json([
                    'error' => 1,
                    'msg' => 'Voucher code is invalid (2)'
                ]);
            }
        }

        $user = User::find($userId);

        if (in_array(strtolower(env('APP_ENV')), ['prod', 'production'])) {
            $this->performExtraActions($user, $productInfo, $sponsorInfo, $addressInfo);
        }

        return response()->json([
           'error' => 0,
           'message' => 'User created succesfully (Dist ID: ' . $user->distid . ')'
        ]);
    }

    private function performExtraActions($user, $productInfo, $sponsorInfo, $addressInfo)
    {
       if (request()->post('subscribe') === 'on') {
            // add subscribe to master audience
            (new MailChimpService(env('MAILCHIMP_MASTER_AUDIENCE_ID'), $productInfo->id))
                ->buildMasterAudienceData($user)
                ->subscribe();

            // add subscriber to sponsor audience
            (new MailChimpService(env('MAILCHIMP_SPONSOR_AUDIENCE_ID'), $productInfo->id))
                ->buildSponsorAudienceData($user)
                ->subscribe();
        }

        if (request()->post('activateIDecide') === 'on') {
            iDecide::createUser($user->id, $sponsorInfo);
        }

        if (request()->post('activateSor') === 'on') {
            SaveOn::SORCreateUser($user->id, $productInfo->id, $addressInfo);
        }

        if (request()->post('activateiPayout') === 'on') {
            IPayOut::createiPayoutUser($user);
        }

        // notifiy the sponsor and distributor
        if (request()->post('sendSponsorEmail') == 'on') {
            sendMail::sendDistributorRegistrationMail(request()->post('sponsorid'), $user->id);
            Twilio::sendEnrollmentSuccessMessage(request()->post('sponsorid'), $user->id);
        }
    }

    private function createFieldsFromRequest()
    {
        $sponsorInfo = $this->createSponsorInfo();
        $cardInfo = $this->createCardInfo();
        $addressInfo = $this->createAddressInfo($sponsorInfo);
        $paymentType = request()->post('payment_method_type');

        return array($sponsorInfo, $addressInfo, $cardInfo, $paymentType);
    }

    private function createBillingAddress($sponsorInfo)
    {
        $billingInfo = [];
        $billingInfo['apt'] = $sponsorInfo['billing_apt'];
        $billingInfo['address1'] = $sponsorInfo['billing_address_line_one'];
        $billingInfo['address2'] = $sponsorInfo['billing_address_line_two'];
        $billingInfo['city'] = $sponsorInfo['billing_city'];
        $billingInfo['state'] = $sponsorInfo['billing_state'];
        $billingInfo['postal_code'] = $sponsorInfo['billing_postal_code'];
        $billingInfo['country'] = $sponsorInfo['billing_country'];
        $billingInfo['billing_first_name'] = $sponsorInfo['firstname'];
        $billingInfo['billing_last_name'] = $sponsorInfo['lastname'];
        $billingInfo['payment_type'] = (isset($sponsorInfo['payment_type']) ? $sponsorInfo['payment_type'] : 1);
        return $billingInfo;
    }

    private function createAddressInfo($sponsorInfo)
    {
        $primaryAddress = $this->createAddress($sponsorInfo);
        $billingAddress = request()->post('billingSame') == 'on' ?
            $this->createAddress($sponsorInfo) :
            $this->createBillingAddress($sponsorInfo);

        $shippingAddress = $this->createAddress($sponsorInfo);

        return [
            'primary' => $primaryAddress,
            'billing' => $billingAddress,
            'shipping' => $shippingAddress
        ];
    }

    private function createAddress($sponsorInfo)
    {
        $billingInfo = [];
        $billingInfo['apt'] = $sponsorInfo['primary_apt'];
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

    private function createSponsorInfo()
    {
        $sponsorInfo = [
            'username' => request()->post('username'),
            'password' => Str::random(),
            'distid' => request()->post('distid'),
            'sponsor' => request()->post('sponsorid'),
            'default_password' => request()->post('default_password'),
            'email' => request()->post('email'),
            'firstname' => request()->post('firstname'),
            'lastname' => request()->post('lastname'),
            'language' => request()->post('language'),
            'business_name' => request()->post('business_name'),
            'phone_number' => request()->post('phonenumber'),
            'mobile_number' => request()->post('mobilenumber'),
            'ein' => request()->post('ein'),
            'country' =>  Country::whereCountrycode(request()->post('countrycode'))->first()->id,
            'country_code' => request()->post('countrycode'),
            'tax_information' => request()->post('business_name') ? 2 : 1,
            'primary_address_line_one' => request()->post('address1'),
            'primary_address_line_two' => request()->post('address2'),
            'primary_apt' => request()->post('apt'),
            'primary_city' => request()->post('city'),
            'primary_state' =>  request()->post('stateprov'),
            'primary_postal_code' =>  request()->post('postalcode'),
            'primary_country' =>  request()->post('countrycode'),
            'billing_address_line_one' => request()->post('billing_address1'),
            'billing_address_line_two' => request()->post('billing_address2'),
            'billing_apt' => request()->post('billing_apt'),
            'billing_city' => request()->post('billing_city'),
            'billing_state' =>  request()->post('billing_stateprov'),
            'billing_postal_code' =>  request()->post('billing_postalcode'),
            'billing_country' =>  request()->post('billing_countrycode'),
            'payment_type' => request()->post('payment_type'),
            'credit_card_name' => request()->post('credit_card_name'),
            'credit_card_number' => request()->post('credit_card_number'),
            'expiry_date' => request()->post('expiration_date'),
            'cvv' => request()->post('cvv'),
            'subscription_start_date' => request()->post('subscription_start_date')
        ];

        $dob = Carbon::createFromFormat('Y-m-d', request()->post('date_of_birth'));

        $sponsorInfo['birth_day'] = $dob->day;
        $sponsorInfo['birth_month'] = $dob->month;
        $sponsorInfo['birth_year'] = $dob->year;

        return $sponsorInfo;
    }

    private function createCardInfo()
    {
        if (empty(request()->post('credit_card_number'))) {
            return [];
        }

        $expirationDate = request()->post('expiration_date');
        $expireDateParts = explode('/', $expirationDate);

        return [
            'credit_card_number' => trim(str_replace(' ', '', request()->post('credit_card_number'))),
            'cvv' => trim(str_replace(' ', '', request()->post('cvv'))),
            'expiry_date_month' => $expireDateParts[0],
            'expiry_date_year' => $expireDateParts[1],
            'is_save' => 1
        ];
    }
}
