<?php

namespace App\Http\Controllers;

use App\Helpers\ApiHelper;
use App\Models\PaymentMethodType;
use App\Models\MerchantTransactionTracker;
use App\Services\MailChimp as MailChimpService;
use App\Services\Twilio;
use SendMail;

class ThankYouController extends Controller
{
    private $mode;
    private const STANDBY_PRODUCT_ID = 1;

    public function __construct()
    {
        $this->mode = \Config::get('const.mode');
    }

    public function index()
    {

        // $pendingStatuses = [
        //     'UNPAID',
        //     'PARTIAL',
        //     'PROCESSING',
        //     'PENDING'
        // ];

        // $cancelledStatuses = [
        //     'ERROR',
        //     'CANCELLED',
        //     'EXPIRED',
        //     'DECLINED'
        // ];

        // return view('thank-you')->with([
        //     'username' =>  '',
        //     'tsa' =>  '',
        //     'orderHash' => 'MERC-DMR3-AVRI-TZA0-YMHM-UGCY',
        //     'orderStatus' => 'PROCESSING',
        //     'pendingStatuses' => $pendingStatuses,
        //     'cancelledStatuses' => $cancelledStatuses
        // ]);

        if (!session()->has('user_id')) {
            return redirect('/');
        }

        $userId    = session('user_id');
        $user      = \App\Models\User::query()->find($userId);
        $productId = session()->has('product_id') ? session()->get('product_id') : self::STANDBY_PRODUCT_ID;

        if (session('paymentType') == PaymentMethodType::TYPE_UNICRYPT) {
            $orderHash = session('orderhash');

            $response = ApiHelper::request('GET', '/merchant/status', ['transaction_id' => $orderHash]);
            $response_data = json_decode($response->getBody());

            $merchtracker = new MerchantTransactionTracker();
            $merchtracker->merchant_id = 5;
            $merchtracker->transaction_id = $response_data->data->transaction_id;
            $merchtracker->pre_order_id = $response_data->data->pre_order_id;
            $merchtracker->status = $response_data->data->status;
            $merchtracker->save();

            $orderStatus = $response_data->data->status;

        } elseif (session('paymentType') == PaymentMethodType::TYPE_IPAYTOTAL) {
            if (session('status') == '3d_redirect') {
                $response = ApiHelper::request('GET', '/merchant/status', ['transaction_id' => request()->route()->parameters['order_id']]);
                $response_data = json_decode($response->getBody());

                $merchtracker = MerchantTransactionTracker::where('pre_order_id', session('order_id'))->first();
                $merchtracker->transaction_id = $response_data->data->transaction_id;
                $merchtracker->status = $response_data->data->status;
                $merchtracker->save();

                $orderStatus = $response_data->data->status;
            } elseif (session('status') == 'success') {
                $orderStatus = 'PAID';
            } else {
                $orderStatus = 'UNPAID';
                $cancelledStatuses = 'DECLINED';
                $responseText = session('response_text');
            }
        } else {
            $orderHash = '';
            $orderStatus = 'PAID';
        }
        $pendingStatuses = [
            'UNPAID',
            'PARTIAL',
            'PROCESSING',
            'PENDING'
        ];

        $cancelledStatuses = [
            'ERROR',
            'CANCELLED',
            'EXPIRED',
            'DECLINED'
        ];

        if (session()->has('thankYouCompleted')) {
            if (in_array($orderStatus, $cancelledStatuses) || $orderStatus == 'PAID') {
                session()->flush();
            }

            return view('thank-you')->with([
                'username' => $user ? $user->username : '',
                'tsa' => $user ? $user->distid : '',
                'orderHash' => $orderHash ?? '',
                'orderStatus' => $orderStatus ?? '',
                'pendingStatuses' => $pendingStatuses,
                'cancelledStatuses' => $cancelledStatuses,
                'responseText' => $responseText ?? '',
                'sulte_apt_no' => $sulte_apt_no ?? '',
                'apiKey' => $apiKey ?? '',
                'order_id' => $order_id ?? ''
            ]);
        }

        // // add subscribe to master audience
        // (new MailChimpService(env('MAILCHIMP_MASTER_AUDIENCE_ID'), $productId))
        //     ->buildMasterAudienceData($user)
        //     ->subscribe();

        // add subscriber to sponsor audience
        // (new MailChimpService(env('MAILCHIMP_SPONSOR_AUDIENCE_ID'), $productId))
        //     ->buildSponsorAudienceData($user)
        //     ->subscribe();

        // \App\Models\IPayOut::createiPayoutUser($user);

        // notifiy the sponsor and distributor
        sendMail::sendDistributorRegistrationMail(session('sponsor'), $userId);
        Twilio::sendEnrollmentSuccessMessage(session('sponsor'), $userId);
    

        session(['thankYouCompleted' => true]);

        if (in_array($orderStatus, $cancelledStatuses) || $orderStatus == 'PAID') {
            session()->flush();
        }

        return view('thank-you')->with([
            'username' => $user ? $user->username : '',
            'tsa' => $user ? $user->distid : '',
            'orderStatus' => $orderStatus ?? '',
            'orderHash' => $orderHash ?? '',
            'pendingStatuses' => $pendingStatuses,
            'cancelledStatuses' => $cancelledStatuses,
            'responseText' => $responseText ?? '',
            'sulte_apt_no' => $sulte_apt_no ?? '',
            'apiKey' => $apiKey ?? '',
            'order_id' => $order_id ?? ''
        ]);
    }
}
