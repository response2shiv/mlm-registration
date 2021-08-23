<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\GeoIP;
use Session;
use Log;
class OptionalPromotionalController extends Controller
{

    public function __construct()
    {
        $this->middleware('check_sponsor');
    }

    public function index()
    {
        if (!session()->has('success_2fa')) {
             return redirect('/vitals');
        }
        session(['optional_promotional' => true, 'standby' => true]);
        session()->forget(['product_id', 'ticket_purchase', 'sponsor_information', 'products_conversion', 'country_conversion']);
        if (session()->has('vitals') && session('vitals')['country'] == '71') {
            $d = array("premium_fc_available" => true);
        }else{
            $d = array("premium_fc_available" => false);
        }

        // $clientIP = \Request::ip();
        // Log::info("Client IP ".$clientIP);

        // $geoip = new GeoIP();
        // $country = $geoip->getCountryFromIP($clientIP);
        // Log::info("Result from country is ",[$country]);

        $country = trim(session('billing_country')->currency);

        session()->put(['country_conversion' => $country]);
        $d['products']  = Products::getSubscriptionProductsWithConversion($country);
        $d['country']   = $country;

        return view('optional-promotional-faroe')->with($d);
    }

    public function setProduct($id)
    {
        session(['product_id' => $id]);
        return redirect('/sponsor-information');
    }

}
