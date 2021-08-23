<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const ACC_STATUS_TERMINATED = "TERMINATED";
    const ACC_STATUS_PENDING_REVIEW = "PENDING REVIEW";
    const ACC_STATUS_PENDING_APPROVAL = "PENDING APPROVAL";
    const ACC_STATUS_APPROVED = "APPROVED";

    const TAX_INDIVIDUAL = 1;
    const TAX_BUSINESS = 2;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'mi',
        'lastname',
        'email',
        'phonenumber',
        'username',
        'refname',
        'distid',
        'usertype',
        'statuscode',
        'sponsorid',
        'legacyid',
        'deleted',
        'mobilenumber',
        'phone_country_code',
        'is_business',
        'business_name',
        'co_applicant_name',
        'country_code',
        'business_name',
        'ssn',
        'fid',
        'founder',
        'password',
        'account_status',
        'email_verified',
        'entered_by',
        'basic_info_updated',
        'remember_token',
        'created_date',
        'created_time',
        'current_product_id',
        'next_subscription_date',
        'original_subscription_date',
        'coundown_expire_on',
        'created_dt',
        'co_applicant_email',
        'co_applicant_country_code',
        'co_applicant_mobile_number',
        'tax_information',
        'ein',
        'sex',
        'language',
        'date_of_birth',
        'subscription_product'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $timestamps = false;

    protected $primaryKey = 'id';


    public static function getSponsor($username)
    {
        $username = strtolower($username);
        return User::where('username', $username)->first();
    }

    public function userAddress()
    {
        return $this->hasOne('App\Models\Addresses', 'userid', 'id')
            ->where('addrtype', '=', '1');
    }

    public static function userExists($email)
    {
        $count = self::where('email', $email)->count();
        if ($count) {
            return true;
        } else {
            return false;
        }
    }

    public static function getSponsorByDistId($distId)
    {
        return User::where('distid', $distId)->first();
    }


    /**
     * @return mixed
     */
    public function getCurrentLeftCarryover()
    {
        $lastCarryover = $this->carryovers->sortBy('bc_history_id')->last();

        return $lastCarryover->left_carryover ?? $this->current_left_carryover;
    }

    /**
     * @return mixed
     */
    public function getCurrentRightCarryover()
    {
        $lastCarryover = $this->carryovers->sortBy('bc_history_id')->last();

        return $lastCarryover->right_carryover ?? $this->current_right_carryover;
    }

    /**
     * @param $id
     */
    public static function activateUser($id)
    {
        User::find($id)->update([
            'account_status' => User::ACC_STATUS_APPROVED
        ]);
    }

    public function carryovers()
    {
        return $this->hasMany('App\Models\BinaryCommissionCarryoverHistory');
    }

}
