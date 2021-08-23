<?php

namespace App\Http\Controllers;

use App\Models\Addresses;
use App\Models\User;
use Illuminate\Http\Request;

class SponsorLinkController extends Controller
{

    public function index($username, $direction) {
        session()->forget(['vitals_page', 'standby', 'sponsor-information', 'optional-promotional', "sponsor-search"]);

        $regex = '/^[a-zA-Z0-9]+$/';

        if (!preg_match($regex, $username)) {
            return redirect('enrollment/sponsor');
        }

        if (!in_array(strtoupper($direction), ['L', 'R'])) {
            return redirect('enrollment/sponsor');
        }

        return redirect('/?sponsor=' . $username . '&direction=' . $direction);
    }

}
