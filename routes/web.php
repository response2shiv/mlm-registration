<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
$domain = Request::getHost();
// Exclude external hosts;
if ($domain === env('PAYAP_SUBDOMAIN')) {
    Route::get('/{any}', 'RedirectController@helpDesk')->where('any', '.*');
} else if ($domain === env('ROOT_DOMAIN')) {
    Route::get('/{any}', 'RedirectController@home')->where('any', '.*');
}
Route::get('lng/{locale}', function ($local) {
    \Session::put('local', $local);
    return redirect()->back();
});

Route::get('terms/smartship_terms_conditions', function() {
    return Storage::disk('s3')->download('terms/smartship_terms_conditions.pdf');    
})->name('terms.conditions');

Route::get('/', 'IndexController@index');
//sponsor search
Route::get('/enrollment/sponsor', 'SponsorSearchController@index');
Route::get('/enrollment/sponsor/{username}/{direction}', 'SponsorLinkController@index');
Route::post('/enrollment/sponsor/search', 'SponsorSearchController@search');
//optional packs
Route::get('/optional-promotional', 'OptionalPromotionalController@index');
Route::get('/optional-promotional/{id}', 'OptionalPromotionalController@setProduct');
//sponsor and billing information
Route::get('/sponsor-information/{id?}', 'SponsorInformationController@index');
Route::post('/sponsor-information', 'SponsorInformationController@getSponsorInformation');
//vital page & phone verification
Route::get('/vitals/{id?}', 'SponsorInformationController@vitalsView');
Route::post('/vitals', 'SponsorInformationController@vitalsSubmit');
Route::post('/sub-tfa', 'SponsorInformationController@submitTFA');
Route::post('/resend-tfa', 'SponsorInformationController@resendTFA');
//card declined payment
Route::get('/payment/declined', 'PaymentController@cardDeclined');
Route::post('/payment/declined', 'PaymentController@doPayment');
//voucher code
Route::post('/payment/apply-coupon', 'PaymentController@applyCoupon');
//get states
Route::post('/get-states', 'PaymentController@getStates')->name('get-states');
//checkout payment
Route::get('/payment/checkout', 'PaymentController@doCheckoutPaymentConfirm');
Route::post('/payment/checkout', 'PaymentController@doPayment');
//ticket checkout
 Route::get('/payment/tickets', 'PaymentController@purchaseTickets');
 Route::post('/payment/tickets', 'PaymentController@purchaseTicketsRegister');
//phone code
Route::post('/phone-code','SponsorInformationController@getPhoneCode');
//thank you page
Route::get('/thank-you', 'ThankYouController@index')->name('thank-you');
Route::get('/currency/convert', 'CurrencyController@convertPassthrough');
Route::get('/payment/status', 'OrderStatusController@index');
Route::get('/payment/callback', 'OrderCallbackController@callback');

Route::get('/maintenance', function(){
    return view('errors.maintenance');
})->name('maintenance');