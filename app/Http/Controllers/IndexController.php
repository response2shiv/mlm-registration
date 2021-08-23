<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Addresses;
use Illuminate\Http\Request;
use \App\Models\User;
use Session;

class IndexController extends Controller
{

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        if ($request->has('c')) {
            session(['c' => strtolower($request->input('c'))]);
            return redirect('/');
        }

        if (!is_null($request->sponsor)) {
            $sponsorUsername = strtolower(trim($request->input('sponsor')));
        } else if (session()->has('sponsor')) {
            $sponsorUsername = strtolower(trim(session('sponsor')));
        } else {
            return redirect('enrollment/sponsor');
        }

        if ($request->has('direction') && in_array(strtoupper($request->get('direction')), ['L', 'R'])) {
            session([
                'binary_placement_direction' => request()->get('direction')
            ]);
        }

        $sponsorDetail = User::getSponsor($sponsorUsername);
        if (!$sponsorDetail) {
            return redirect('enrollment/sponsor');
        }

        //$distId = session('sponsor');
        $sponsor = User::getSponsorByDistId($sponsorDetail->distid);
        $address = Addresses::getAddress($sponsor->id);

        session([
            'sponsor_username' => $sponsorUsername,
            'sponsor' => $sponsorDetail->distid,
            'sponsor_name' => (isset($sponsor->firstname)?$sponsor->firstname:'') . ' ' . (isset($sponsor->lastname)?$sponsor->lastname:''),
            'sponsor_city' => (isset($address->city)?$address->city:''),
            'sponsor_state' => (isset($address->stateprov)?$address->stateprov:''),
            'sponsor_mobile_number' => (isset($sponsor->mobilenumber)?$sponsor->mobilenumber:''),
            'sponsor_email' => (isset($sponsor->email)?$sponsor->email:'')
        ]);

        session(['sponsor_search_page' => true]);
        return redirect('vitals');
    }
}
