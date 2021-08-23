<?php

namespace App\Http\Controllers;

use App\Models\Addresses;
use App\Models\User;

class StandByController extends Controller
{
    public function __construct()
    {
        $this->middleware('check_sponsor');
    }

    public function index()
    {

        if (!session()->has('vitals_page')) {
            //return redirect('/vitals');
        }

        session_start();
        $sessionId = session_id();
        session(['session_id' => $sessionId]);
        session(['standby_page' => true]);
        session(['standby' => true]);

//        if (!session()->has('vitals_page')) {
//            return redirect('/vitals');
//        }


        $distId = session('sponsor');
        $sponsor = User::getSponsorByDistId($distId);
        $address = Addresses::getAddress($sponsor->id);

        return view('standby')->with([
            'sponsor_name' => $sponsor->firstname . ' ' . $sponsor->lastname,
            'sponsor_city' => (isset($address->city) ? $address->city : ''),
            'sponsor_state' => (isset($address->stateprov) ? $address->stateprov : ''),
            'sponsor_mobile_number' => (isset($sponsor->mobilenumber) ? $sponsor->mobilenumber : ''),
            'sponsor_email' => (isset($sponsor->email) ? $sponsor->email : '')
        ]);
    }
}
