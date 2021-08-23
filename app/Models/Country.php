<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'country';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'countrycode',
        'country',
        'is_tier3',
        'is_open',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
    ];


    public static function getStates($country_code)
    {
        // $d['states'] = DB::table('states')
        //     ->select('*')
        //     ->where('country_code', $country_code)
        //     ->orderBy('name', 'asc')
        //     ->get()->toArray();
        $d['states'] = self::where('country_code', $country_code)
            ->orderBy('name')
            ->get()
            ->toArray();
        $v = (string) view('dropdown_states_lists')->with($d);
        return response()->json(['error' => 0, 'v' => $v]);
    }

    public static function getCountryId($country_code)
    {
        return DB::table('country')->where('countrycode', $country_code)->pluck('id');
    }

    public static function getCountries()
    {
        return Country::where('is_open', 1)
            ->orderBy('country', 'asc')
            ->get();
    }
}
