<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\PreEnrollmentSelection
 *
 * @property int $id
 * @property int $userId
 * @property int $productId
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $idecide_user
 * @property int|null $saveon_user
 * @property int|null $is_processed
 * @property int|null $is_process_success
 * @property string|null $process_msg
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereIdecideUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereIsProcessSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereIsProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereProcessMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereSaveonUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PreEnrollmentSelection whereUserId($value)
 */
	class PreEnrollmentSelection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SOR
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $api_log
 * @property int|null $platform_id
 * @property string|null $platform_name
 * @property string|null $platform_tier
 * @property int|null $sor_user_id
 * @property int|null $product_id
 * @property string|null $sor_password
 * @property string|null $token
 * @property int|null $status
 * @property int|null $old_sor_user_id
 * @property string|null $note
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereApiLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereOldSorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR wherePlatformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR wherePlatformName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR wherePlatformTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereSorPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereSorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SOR whereUserId($value)
 */
	class SOR extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property string|null $firstname
 * @property string|null $mi
 * @property string|null $lastname
 * @property string|null $email
 * @property string|null $phonenumber
 * @property string|null $username
 * @property string|null $refname
 * @property string|null $distid
 * @property string|null $updated_at
 * @property string|null $created_at
 * @property int|null $usertype
 * @property int|null $statuscode
 * @property string|null $sponsorid
 * @property string|null $legacyid
 * @property int|null $deleted
 * @property string|null $mobilenumber
 * @property int|null $is_business
 * @property string|null $business_name
 * @property string|null $ssn
 * @property string|null $fid
 * @property int|null $founder
 * @property string|null $password
 * @property string|null $account_status
 * @property int|null $email_verified
 * @property int|null $entered_by
 * @property int|null $basic_info_updated
 * @property string|null $remember_token
 * @property int $id
 * @property string|null $default_password
 * @property string|null $created_date
 * @property string|null $created_time
 * @property int|null $current_product_id
 * @property int|null $is_tv_user
 * @property float|null $available_balance
 * @property float|null $estimated_balance
 * @property string|null $payap_mobile
 * @property int|null $admin_role
 * @property int|null $current_month_qv
 * @property int|null $current_month_rank
 * @property string|null $co_applicant_name
 * @property string|null $country_code
 * @property string|null $display_name
 * @property string|null $recognition_name
 * @property string|null $phone_country_code
 * @property string|null $original_subscription_date
 * @property int|null $subscription_payment_method_id
 * @property string|null $next_subscription_date
 * @property int|null $gflag
 * @property string|null $remarks
 * @property int|null $payment_fail_count
 * @property int|null $subscription_attempts
 * @property int|null $sync_with_mailgun
 * @property int|null $is_sites_deactivate
 * @property int|null $is_cron_fail
 * @property int $current_month_pqv
 * @property string|null $created_dt
 * @property float $current_left_carryover
 * @property float $current_right_carryover
 * @property int $current_month_tsa
 * @property string|null $coundown_expire_on
 * @property string|null $binary_placement
 * @property string|null $beneficiary
 * @property int|null $secondary_auth_enabled
 * @property int|null $authy_id
 * @property int|null $is_active
 * @property int|null $current_month_cv
 * @property int|null $binary_q_l
 * @property int|null $binary_q_r
 * @property int $is_activate
 * @property string|null $subscription_remarks
 * @property int $is_bc_active
 * @property int|null $level
 * @property int|null $subscription_product
 * @property string|null $co_applicant_email
 * @property string|null $co_applicant_country_code
 * @property string|null $co_applicant_mobile_number
 * @property string|null $tax_information
 * @property string|null $ein
 * @property string|null $language
 * @property string|null $date_of_birth
 * @property string|null $sex
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BinaryCommissionCarryoverHistory[] $carryovers
 * @property-read int|null $carryovers_count
 * @property-read \App\Models\Addresses $userAddress
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAdminRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAuthyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAvailableBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBasicInfoUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBeneficiary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBinaryPlacement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBinaryQL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBinaryQR($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCoApplicantCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCoApplicantEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCoApplicantMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCoApplicantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCoundownExpireOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedDt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentLeftCarryover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentMonthCv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentMonthPqv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentMonthQv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentMonthRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentMonthTsa($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCurrentRightCarryover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDefaultPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDistid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEnteredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEstimatedBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFounder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereGflag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsActivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsBcActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsBusiness($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsCronFail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsSitesDeactivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsTvUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLegacyid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereMi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereMobilenumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereNextSubscriptionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereOriginalSubscriptionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePayapMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePaymentFailCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhoneCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhonenumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRecognitionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRefname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSecondaryAuthEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSponsorid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStatuscode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSubscriptionAttempts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSubscriptionPaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSubscriptionProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSubscriptionRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSyncWithMailgun($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTaxInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsertype($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentMethods
 *
 * @property int|null $userID
 * @property int|null $primary
 * @property int|null $deleted
 * @property string|null $token
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $cvv
 * @property string|null $expMonth
 * @property string|null $expYear
 * @property string|null $firstname
 * @property string|null $lastname
 * @property int|null $bill_addr_id
 * @property int|null $pay_method_type
 * @property int|null $is_subscription
 * @property int|null $is_deleted
 * @property string|null $is_save
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereBillAddrId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereCvv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereExpMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereExpYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereIsSave($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereIsSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods wherePayMethodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethods whereUserID($value)
 */
	class PaymentMethods extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TmtPayment
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TmtPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TmtPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TmtPayment query()
 */
	class TmtPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SaveOn
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $api_log
 * @property int|null $platform_id
 * @property string|null $platform_name
 * @property string|null $platform_tier
 * @property int|null $sor_user_id
 * @property int|null $product_id
 * @property string|null $sor_password
 * @property string|null $token
 * @property int|null $status
 * @property int|null $old_sor_user_id
 * @property string|null $note
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereApiLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereOldSorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn wherePlatformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn wherePlatformName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn wherePlatformTier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereSorPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereSorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SaveOn whereUserId($value)
 */
	class SaveOn extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BoomerangHistory
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $opening_boomerangs
 * @property int|null $closing_boomerangs
 * @property string|null $remark
 * @property string|null $created_at
 * @property int|null $num_boomerangs
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory whereClosingBoomerangs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory whereNumBoomerangs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory whereOpeningBoomerangs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangHistory whereUserId($value)
 */
	class BoomerangHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DiscountCoupon
 *
 * @property int $id
 * @property string|null $code
 * @property int|null $is_used
 * @property string|null $created_at
 * @property int|null $used_by
 * @property float|null $discount_amount
 * @property int|null $is_active
 * @property int|null $generated_for
 * @property int|null $product_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereGeneratedFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereIsUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DiscountCoupon whereUsedBy($value)
 */
	class DiscountCoupon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ApiLogs
 *
 * @property int $id
 * @property int $user_id
 * @property string $api
 * @property string $endpoint
 * @property string|null $request
 * @property string|null $response
 * @property string $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs whereApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs whereEndpoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs whereRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApiLogs whereUserId($value)
 */
	class ApiLogs extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Products
 *
 * @property int|null $id
 * @property string|null $productname
 * @property int|null $producttype
 * @property string|null $productdesc
 * @property string|null $productdesc2
 * @property int|null $isautoship
 * @property int|null $statuscode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $udated_at
 * @property float|null $price
 * @property float|null $price_as
 * @property float|null $price2
 * @property float|null $price3
 * @property string|null $sku
 * @property string|null $itemcode
 * @property int|null $bv
 * @property int|null $cv
 * @property int|null $qv
 * @property int|null $num_boomerangs
 * @property int|null $sponsor_boomerangs
 * @property float|null $qc
 * @property float|null $ac
 * @property int $is_enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereBv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereCv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereIsautoship($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereItemcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereNumBoomerangs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products wherePrice2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products wherePrice3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products wherePriceAs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereProductdesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereProductdesc2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereProductname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereProducttype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereQc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereQv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereSponsorBoomerangs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereStatuscode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Products whereUdatedAt($value)
 */
	class Products extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BinaryPlacementLog
 *
 * @property int $id
 * @property int $user_id
 * @property string $error
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BinaryPlacementLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BinaryPlacementLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BinaryPlacementLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BinaryPlacementLog whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BinaryPlacementLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BinaryPlacementLog whereUserId($value)
 */
	class BinaryPlacementLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\IPayOut
 *
 * @property int $id
 * @property int $user_id
 * @property int $transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IPayOut newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IPayOut newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IPayOut query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IPayOut whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IPayOut whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IPayOut whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IPayOut whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IPayOut whereUserId($value)
 */
	class IPayOut extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderItem
 *
 * @property int|null $orderid
 * @property int|null $productid
 * @property int|null $quantity
 * @property float|null $itemprice
 * @property int|null $bv
 * @property int|null $qv
 * @property int|null $cv
 * @property int $id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $created_date
 * @property string|null $created_time
 * @property int|null $discount_coupon
 * @property int|null $discount_voucher_id
 * @property string|null $created_dt
 * @property int|null $qc
 * @property int|null $ac
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereAc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereBv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCreatedDt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCreatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereCv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereDiscountCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereDiscountVoucherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereItemprice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereOrderid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereProductid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereQc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereQv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderItem whereUpdatedAt($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Orders
 *
 * @property int|null $userid
 * @property int|null $statuscode
 * @property float|null $ordersubtotal
 * @property float|null $ordertax
 * @property float|null $ordertotal
 * @property int|null $orderbv
 * @property int|null $orderqv
 * @property int|null $ordercv
 * @property string|null $trasnactionid
 * @property string|null $updated_at
 * @property string|null $created_at
 * @property int|null $payment_methods_id
 * @property int|null $shipping_address_id
 * @property int $id
 * @property int|null $inv_id
 * @property string|null $created_date
 * @property string|null $created_time
 * @property bool|null $processed
 * @property string|null $coupon_code
 * @property int|null $order_refund_ref
 * @property string|null $created_dt
 * @property int|null $orderqc
 * @property int|null $orderac
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderItem[] $orderItems
 * @property-read int|null $order_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereCouponCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereCreatedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereCreatedDt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereCreatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereInvId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrderRefundRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrderac($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrderbv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrdercv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrderqc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrderqv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrdersubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrdertax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereOrdertotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders wherePaymentMethodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereShippingAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereStatuscode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereTrasnactionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Orders whereUserid($value)
 */
	class Orders extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Addresses
 *
 * @property int|null $userid
 * @property string|null $addrtype
 * @property int|null $primary
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $stateprov
 * @property string|null $stateprov_abbrev
 * @property string|null $postalcode
 * @property string|null $countrycode
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int $id
 * @property string|null $apt
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereAddress1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereAddrtype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereApt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereCountrycode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses wherePostalcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereStateprov($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereStateprovAbbrev($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Addresses whereUserid($value)
 */
	class Addresses extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentMethodType
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PaymentMethodType query()
 */
	class PaymentMethodType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Helper
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Helper newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Helper newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Helper query()
 */
	class Helper extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Marketing
 *
 * @property int $id
 * @property string|null $sponsor_username
 * @property string|null $sponsor
 * @property string|null $sponsor_name
 * @property string|null $sponsor_city
 * @property string|null $sponsor_state
 * @property string|null $sponsor_mobile_number
 * @property string|null $sponsor_email
 * @property string|null $country
 * @property string|null $language
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $country_code
 * @property string|null $mobile_number
 * @property string|null $updates_subscribe
 * @property string|null $authy_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $marketing_agreed
 * @property int $fa_approved
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereAuthyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereFaApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereMarketingAgreed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereSponsor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereSponsorCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereSponsorEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereSponsorMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereSponsorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereSponsorState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereSponsorUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Marketing whereUpdatesSubscribe($value)
 */
	class Marketing extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductType
 *
 * @property int|null $id
 * @property string|null $typedesc
 * @property int|null $statuscode
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductType whereStatuscode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProductType whereTypedesc($value)
 */
	class ProductType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Country
 *
 * @property string|null $countrycode
 * @property string|null $country
 * @property int $id
 * @property int|null $is_tier3
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereCountrycode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Country whereIsTier3($value)
 */
	class Country extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BoomerangInv
 *
 * @property int|null $userid
 * @property int|null $pending_tot
 * @property int|null $available_tot
 * @property int $id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangInv newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangInv newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangInv query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangInv whereAvailableTot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangInv whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangInv wherePendingTot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BoomerangInv whereUserid($value)
 */
	class BoomerangInv extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\iDecide
 *
 * @property int $id
 * @property int $api_log
 * @property int $user_id
 * @property int|null $idecide_user_id
 * @property string|null $password
 * @property string|null $login_url
 * @property int|null $is_updated_business_number
 * @property int|null $generated_integration_id
 * @property int|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide whereApiLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide whereGeneratedIntegrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide whereIdecideUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide whereIsUpdatedBusinessNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide whereLoginUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\iDecide whereUserId($value)
 */
	class iDecide extends \Eloquent {}
}

namespace App{
/**
 * App\TwilioAuthy
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TwilioAuthy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TwilioAuthy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\TwilioAuthy query()
 */
	class TwilioAuthy extends \Eloquent {}
}

