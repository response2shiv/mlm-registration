<?php

namespace App\Http\Controllers;

use App\Models\Addresses;
use Illuminate\Http\Request;

class SponsorSearchController extends Controller {

    public function __construct() {

    }

    public function index() {
        session()->forget(['vitals_page', 'standby', 'sponsor-information', 'optional-promotional', "sponsor-search"]);
        return view('sponsor-search');
    }

    public function search(Request $request) {
        $action = $request->input('action');

        if ($action == 'searchByName') {
            return $this->searchByName($request);
        } else if ($action == 'searchByUserName') {
            return $this->searchByUsername($request);
        } else {
            return response()->json([
                        'status' => 1,
                        'list' => '',
                        'count' => 0
            ]);
        }
    }

    private function searchByName($request) {

        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');

        $user = \App\Models\User::where('firstname', 'ilike', $firstName)
                ->where('lastname', 'ilike', $lastName)
                ->whereNotIn('distid', ['TSA5138270', 'TSA9834283', 'TSA0707550'])
                ->whereNotIn('account_status', [\App\Models\User::ACC_STATUS_TERMINATED, \App\Models\User::ACC_STATUS_PENDING_REVIEW, \App\Models\User::ACC_STATUS_PENDING_APPROVAL])
                ->get();
        $sponsors = [];
        foreach ($user as $u) {
            $address = Addresses::getAddress($u->id);
            $sponsors[] = [
                'firstname' => $u->firstname,
                'lastname' => $u->lastname,
                'url' => env('APP_URL') . '?sponsor=' . $u->username,
                'avatarUrl' => url('/images/avatar.png'),
                'city' => $address ? $address->city : '',
                'stateabbr' => $address ? $address->stateprov : '',
                'countrycode' => $address ? $address->countrycode : '',
            ];
        }

        return response()->json([
                    'status' => 1,
                    'sponsors' => $sponsors,
                    'count' => $user->count()
        ]);
    }

    private function searchByUsername($request) {
        $username = $request->input('username');
        $user = \App\Models\User::where('username', 'ilike', $username)
                ->whereNotIn('distid', ['TSA5138270', 'TSA9834283', 'TSA0707550'])
                ->whereNotIn('account_status', [\App\Models\User::ACC_STATUS_TERMINATED, \App\Models\User::ACC_STATUS_PENDING_REVIEW, \App\Models\User::ACC_STATUS_PENDING_APPROVAL])
                ->get();

        $sponsors = [];
        foreach ($user as $u) {
            $address = Addresses::getAddress($u->id);

            $sponsors[] = [
                'firstname' => $u->firstname,
                'lastname' => $u->lastname,
                'url' => env('APP_URL') . '?sponsor=' . $u->username,
                'avatarUrl' => url('/images/avatar.png'),
                'city' => $address ? $address->city : '',
                'stateabbr' => $address ? $address->stateprov : '',
                'countrycode' => $address ? $address->countrycode : '',
            ];
        }

        return response()->json([
                    'status' => 1,
                    'sponsors' => $sponsors,
                    'count' => $user->count()
        ]);
    }

}
