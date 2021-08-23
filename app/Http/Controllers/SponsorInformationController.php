<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\BillingCountry;
use App\TwilioAuthy;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Log;
class SponsorInformationController extends Controller
{

    public function __construct()
    {
        $this->middleware('check_sponsor');
        $this->country = new Country();
    }

    public function index($userId = '')
    {
        session_start();

        if (!session()->has('optional_promotional')) {
            return redirect('/optional_promotional');
        }
        if (session()->has('success_2fa') && session('success_2fa') != 1) {
            return redirect('/vitals');
        }

        if (!session()->has('standby')) {
            return redirect('/standby');
        }

        $data['countries'] = $this->country->getCountries();

        $dial_code = json_decode(file_get_contents("json/dial_code.json"), true);
        $dial_code = array_unique($dial_code);
        asort($dial_code);
        $data['dial_code'] = array_filter($dial_code);
        $data['sessionId'] = session_id();

        // show 2fa modal
        $show2FAmodal = 0;
        if (session()->has('show_2fa_modal')) {
            session(['resent_2fa_count' => 0]);
            session(['failed_2fa_count' => 0]);
            $show2FAmodal = 1;
        }
        $data['show_2fa_modal'] = $show2FAmodal;
        return view('sponsor-information')->with($data);
    }

    public function vitalsView($userId = '')
    {
        session()->forget(['sponsor_information', 'optional_promotional']);
        if (!session()->has('sponsor_search_page')) {
            return redirect('/enrollment/sponsor');
        }

        $data['countries'] = $this->country->getCountries();

        $dial_code = json_decode(file_get_contents("json/dial_code.json"), true);
        $dial_code = array_unique($dial_code);
        asort($dial_code);
        $data['dial_code'] = array_filter($dial_code);
        // show 2fa modal
        $show2FAmodal = 0;
        if (session()->has('show_2fa_modal')) {
            session(['resent_2fa_count' => 0]);
            session(['failed_2fa_count' => 0]);
            $show2FAmodal = 1;
        }
        $data['show_2fa_modal'] = $show2FAmodal;
        return view('vitals')->with($data);
    }

    public function vitalsSubmit(Request $request)
    {
        $skip2Factor = env('SKIP_2FA') == 1 ? true : false;

        $data = $request->all();
        $rules = [
            'country' => 'required',
            'language' => 'required',
            'firstname' => 'required|max:50',
            'lastname' => 'nullable|max:50',
            'email' => 'required|email|unique:users|max:50',
            'country_code' => 'required',
            'mobile_number' => 'required|unique:users,mobilenumber'
        ];

        #if (!$skip2Factor) {
        #    $rules['mobile_number'] = 'required|unique:users,mobilenumber';
        #}

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect('vitals')
                ->withErrors($validator)
                ->withInput();
        }


         $countryName = DB::table('country')->where('id', $data['country'])->value('country');   //get country name based on country id


        $userTier = DB::table('country')->where('country', $countryName)->value('is_tier3'); //get user tier based on country
        $request->session()->put('user_tier', $userTier);  //store user tier in session variable

        $billingCountry = BillingCountry::where('name', $countryName)->first();
        session()->put('billing_country', $billingCountry);
        // dd(session('billing_country'));
        if ($skip2Factor === true) {
            session(['vitals' => $request->except('_token')]);
            session(['vitals_page' => true]);
            session(['failed_2fa_count' => 0]);
            session(['success_2fa' => 1]);

            \App\Models\Marketing::manageMarketingData();
            return response()->redirectTo('/optional-promotional');
        }

        $authyRes = TwilioAuthy::register($data['email'], $data['mobile_number'], $data['country_code']);
        if ($authyRes['error'] == 1) {
            return redirect('vitals')
                ->withErrors(['mobile_number' => __('lang.INVALID_MOBILE_NUMBER')])
                ->withInput();
        }
        session(['vitals' => $request->except('_token')]);
        session(['authy_id' => $authyRes['authy_id']]);

//        if (!session('marketing_id'))
//            session(['marketing_id' => false]);
//        \App\Models\Marketing::manageMarketingData();

        TwilioAuthy::sendToken();
        session()->forget(['success_2fa']);
        return redirect('vitals')
            ->with(['show_2fa_modal' => 1]);
    }

    public function getSponsorInformation(Request $request)
    {
        $data = $request->all();
        // Log::info("Information received",$data);
        $data['email'] = strtolower(trim($request->email));
        $data['username'] = strtolower(trim($data['username']));
        $username = $data['username'];
        $validator = Validator::make($data, [
            'username' => 'required|unique:users|regex:/^[a-zA-Z0-9]+$/i|min:6|max:20',
            'password' => 'required|confirmed|min:6',
            'co_applicant_first_name' => 'nullable|max:100',
            'co_applicant_last_name' => 'nullable|max:100',
            'co_applicant_email' => 'nullable|max:100',
            'co_applicant_country_code' => 'nullable|max:10',
            'co_applicant_mobile_number' => 'nullable|max:20',
            'tax_information' => 'required',
            'ein' => 'required_if:tax_information,' . User::TAX_BUSINESS,
            'business_name' => 'required_if:tax_information,' . User::TAX_BUSINESS,
            'birth_month' => 'required',
            'birth_day' => 'required',
            'birth_year' => 'required',
//            'gender' => 'required',
            'primary_address_line_one' => 'required|max:100',
            'primary_address_line_two' => 'nullable|max:100',
            'primary_city' => 'required|max:100',
            'primary_state' => 'required|max:50',
            'primary_postal_code' => 'required|max:10',
            'primary_country' => 'required|max:100',
            'billing_address_line_one' => 'required_if:is_billing_same,no|max:100',
            'billing_address_line_two' => 'nullable|max:100',
            'billing_city' => 'required_if:is_billing_same,no|max:100',
            'billing_state' => 'required_if:is_billing_same,no|max:50',
            'billing_postal_code' => 'required_if:is_billing_same,no|max:10',
            'billing_country' => 'required_if:is_billing_same,no|max:100',
            'shipping_address_line_one' => 'required_if:is_shipping_same,no|max:100',
            'shipping_address_line_two' => 'nullable|max:100',
            'shipping_city' => 'required_if:is_shipping_same,no|max:100',
            'shipping_state' => 'required_if:is_shipping_same,no|max:50',
            'shipping_postal_code' => 'required_if:is_shipping_same,no|max:10',
            'shipping_country' => 'required_if:is_shipping_same,no|max:100',
            // 'payment_type' => 'required',
            // 'credit_card_name' => 'required|max:50',
            // 'credit_card_number' => 'required',
            // 'expiry_date' => 'required',
            // 'cvv' => 'required'
        ]);



        $validator->after(function ($validator) use ($username, $data) {
            if ($username == 'join' || $username == 'home') {
                $validator->errors()->add('username', __('lang.USERNAME_ALREADY_TAKEN'));
            }
            $dob = 0;
            if (!isset($data['birth_day'])) {
                $validator->errors()->add('birth_year', __('lang.INVALID_AGE'));
            } else if (!isset($data['birth_month'])) {
                $validator->errors()->add('birth_year', __('lang.INVALID_AGE'));
            } else if (!isset($data['birth_year'])) {
                $validator->errors()->add('birth_year', __('lang.INVALID_AGE'));
            } else {
                $dob = $data['birth_day'] . "-" . $data['birth_month'] . "-" . $data['birth_year'];
            }
            if (!$this->validateAge($dob)) {
                $validator->errors()->add('birth_year', __('lang.INVALID_AGE'));
            }

            // $expiryDate = $expiryDate = trim(str_replace(' ', '', $data['expiry_date']));
            // $expireDateParts = explode('/', $expiryDate);
            // if (!isset($expireDateParts[0])) {
            //     $validator->errors()->add('expiry_date', __('lang.INVALID_EXPIRY_DATE'));
            // } else if (!isset($expireDateParts[1])) {
            //     $validator->errors()->add('expiry_date', __('lang.INVALID_EXPIRY_DATE'));
            // } else {
            //     if (strlen(trim($expireDateParts[1])) > 2 || strlen(trim($expireDateParts[1])) <= 1) {
            //         $validator->errors()->add('expiry_date', __('lang.INVALID_EXPIRY_DATE'));
            //     }
            // }
        });
        if ($validator->fails()) {
            return redirect('sponsor-information')
                ->withErrors($validator)
                ->withInput();
        } else {

            session()->forget(['country_conversion']);
            if($data['is_billing_same']=="yes"){
                $billingCountry = BillingCountry::where('code', $data['primary_country'])->first();

                session(['country_conversion' => $billingCountry->currency]);
            }else{
                $billingCountry = BillingCountry::where('code', $data['billing_country'])->first();
                session(['country_conversion' => $billingCountry->currency]);
            }
            session(['sponsor_information' => $request->except('_token')]);

            // Ticket offer for users that purchase packs
            // if(session()->has('product_id')){
            //     return redirect('payment/tickets');
            // }

            return redirect('payment/checkout');
        }
    }

    private function validateAge($birthday, $age = 18)
    {
        // $birthday can be UNIX_TIMESTAMP or just a string-date.
        if (is_string($birthday)) {
            $birthday = strtotime($birthday);
        }

        // check
        // 31536000 is the number of seconds in a 365 days year.
        if (time() - $birthday < $age * 31536000) {
            return false;
        }
        return true;
    }
    public function submitTFA()
    {
        $req = request();
        $code = $req->c;
        if ($code == "")
            return response()->json(['error' => '1', 'msg' => __('lang.PLEASE_ENTER_THE_CODE')]);
        //
        $res = TwilioAuthy::verifyToken($code);
        if ($res['verified'] == 0) {
            $failedCount = session('failed_2fa_count', 0);
            session(['failed_2fa_count' => ++$failedCount]);
            return response()->json(['error' => '1', 'failed_count' => $failedCount, 'msg' => $res['msg']]);
        } else {
            session(['vitals_page' => true]);
            session(['failed_2fa_count' => 0]);
            session(['success_2fa' => 1]);
//            if (!session('marketing_id'))
//                session(['marketing_id' => false]);
            \App\Models\Marketing::manageMarketingData();
            return response()->json(['error' => '0', 'failed_count' => 0, 'url' => url('/optional-promotional')]);
        }
    }

    public function resendTFA()
    {
        $res = TwilioAuthy::sendToken();
        //
        if ($res['sent']) {
            session(['resent_2fa_count' => 0]);
            return response()->json(['error' => '0', 'resent_count' => 0, 'msg' => __('lang.WE_HAVE_RE-SENT_THE_VERIFICATION_CODE')]);
        } else {
            $resentCount = session('resent_2fa_count', 0);
            session(['resent_2fa_count' => ++$resentCount]);
            return response()->json(['error' => '1', 'resent_count' => $resentCount, 'msg' => $res['msg']]);
        }
    }

    public function getPhoneCode(Request $request)
    {
        $code = $request->code;
        $dial_code = json_decode(file_get_contents("json/dial_code.json"), true);
//        $dial_code = array_unique($dial_code);
        asort($dial_code);

        $select_code = $rec = DB::table('country')
            ->select('*')
            ->where('id', $code)
            ->first();
        return response()->json(['error' => 0, 'select_code' => (!empty($select_code) ? (!empty($dial_code[$select_code->countrycode]) ? $dial_code[$select_code->countrycode] : '') : '')]);
    }

}
