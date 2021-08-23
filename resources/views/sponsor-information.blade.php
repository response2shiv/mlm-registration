@extends('layout')
@section('title')
ncrease - {!! __('lang.SPONSOR_INFORMATION') !!}
@endsection
@section('back-link')
{{ url('/standby') }}
@endsection
@section('content')
<section class="enrollment-content-wrapper mt-5">
    <div class="wrapper content-wrapper">
        <div class="enrollment-title mt-0 mb-4">{!!__('lang.PERSONAL_INFORMATION')!!}</div>
        <div class="errors-wrapper">
            @if (Session::has('message'))
            <div class="small alert alert-danger">
                {{ Session::get('message') }}
            </div>
            @endif
            @if ($errors->any())
            <div class="small alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="row">
            <div class="mx-auto">
                <form class="info-form" method="POST" action="{{url('sponsor-information')}}" autocomplete="off">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-lg-6 col-12 left-column">
                                <div class="row">
                                    <div class="col-12 form-subtitle">{!! __('lang.ACCOUNT_SETTINGS') !!}</div>
                                    <div class="col-12 mb-2">
                                        <div class="float-container">
                                            <label for="UserNameInput">{!!__('lang.USERNAME')!!} *</label>
                                            <input id="UserNameInput" name="username" type="text" value="{{old('username',session('sponsor_information')['username']??'')}}" maxlength="255" autocomplete="username">
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="float-container">
                                            <label for="PasswordInput">{!!__('lang.PASSWORD')!!} *</label>
                                            <input id="PasswordInput" name="password" type="password" value="{{old('password',session('sponsor_information')['password']??'')}}" maxlength="20" autocomplete="new-password">
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="float-container">
                                            <label for="PasswordConfirmationInput">{!!__('lang.CONFIRM_PASSWORD')!!}
                                                *</label>
                                            <input id="PasswordConfirmationInput" name="password_confirmation" type="password" value="{{old('password_confirmation',session('sponsor_information')['password_confirmation']??'')}}" maxlength="20" autocomplete="new-password">
                                        </div>
                                    </div>
                                    <div class="col-12 form-subtitle">{!! __('lang.YOUR_REPLICATED_WESITE') !!}</div>
                                    <div class="col-12 user-url" style="margin-top: -5px;">
                                        <span class="username-url">{{old('username',session('sponsor_information')['username']??'username')}}</span>.ncrease.com
                                    </div>
                                    <div class="col-12 form-subtitle">{!! __('lang.YOUR_REPLICATED_WESITE') !!}</div>
                                    <div class="user-url" style="margin-top: -5px; padding-bottom: 20px">
                                        <div class="add-co-applicant">
                                            <img src="<?php echo e(url('images/circle-outline.svg')); ?>" alt="store_icons" />
                                            {!! __('lang.ADD_CO_APPLICANT') !!}
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="co_applicant" id="co-applicant" value="{{old('co_applicant',session('sponsor_information')['co_applicant']??'off')}}" />

                                <div class="row co-applicant-info {{old('co-applicant-info',session('sponsor_information')['co_applicant_email']??'')}}">

                                    <div class="col-6 mb-2 pr-2">
                                        <div class="float-container">
                                            <label for="CoApplicantFirstNameInput">{!! __('lang.FIRST_NAME') !!} *</label>
                                            <input id="CoApplicantFirstNameInput" name="co_applicant_first_name" type="text" value="{{old('co_applicant_first_name',session('sponsor_information')['co_applicant_first_name']??'')}}" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2 pl-2">
                                        <div class="float-container">
                                            <label for="CoApplicantLastNameInput">{!! __('lang.LAST_NAME') !!} *</label>
                                            <input id="CoApplicantLastNameInput" name="co_applicant_last_name" type="text" value="{{old('co_applicant_last_name',session('sponsor_information')['co_applicant_last_name']??'')}}" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="float-container">
                                            <label for="CoApplicantEmailInput">{!! __('lang.EMAIL') !!} *</label>
                                            <input id="CoApplicantEmailInput" name="co_applicant_email" type="email" value="{{old('co_applicant_email',session('sponsor_information')['co_applicant_email']??'')}}" minlength="3" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2" style="padding-right: 0;">
                                        <div class="float-container last select-wrapper">
                                            <label for="CoApplicantCountryCodeInput">{!! __('lang.COUNTRY_CODE') !!} *</label>
                                            <select id="CoApplicantCountryCodeInput" name="co_applicant_country_code">
                                                <option value="" {{(old('co_applicant_country_code', session('sponsor_information')['co_applicant_country_code']) == ''?'selected':'')}}>
                                                    ----
                                                </option>
                                                @foreach($dial_code as $d)
                                                <option value="{{$d}}" {{ old('co_applicant_country_code', session('sponsor_information')['co_applicant_country_code']) == $d ? 'selected' : '' }}>{{$d}}</option>
                                                @endforeach
                                            </select>
                                            <i class="fa fa-angle-down arrow-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 mb-2" style="padding-left: 0;">
                                        <div class="float-container last" style="border-left: none;">
                                            <label for="CoApplicantTelInput">Mobile Number *</label>
                                            <input id="CoApplicantTelInput" name="co_applicant_mobile_number" type="text" value="{{old('co_applicant_mobile_number',session('sponsor_information')['co_applicant_mobile_number']??'')}}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 form-subtitle">{!! __('lang.TAX_INFORMATION') !!}</div>
                                    <div class="col-12">
                                        <div class="row pl-3 mb-3">
                                            <div class="col-lg-6 col-md-3 col-6 radio">
                                                <input type="radio" name="tax_information" id="TaxInformation1" value="individual" {{old('tax_information', session('sponsor_information')['tax_information']) != 'business' ? 'checked':''}}>
                                                <label for="TaxInformation1">{!! __('lang.INDIVIDUAL') !!}</label>
                                            </div>
                                            <div class="col-lg-6 col-md-3 col-6 radio">
                                                <input type="radio" name="tax_information" id="TaxInformation2" value="business" {{old('tax_information' , session('sponsor_information')['tax_information']) == 'business' ? 'checked':''}}>
                                                <label for="TaxInformation2">{!! __('lang.BUSINESS') !!}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row tax-info-wrapper {{old('tax_information', session('sponsor_information')['tax_information']) == 'business' ? 'open':''}}">
                                    <div class="col-12 mb-2">
                                        <div class="float-container">
                                            <label for="EinInput">EIN *</label>
                                            <input id="EinInput" name="ein" type="text" value="{{old('ein',session('sponsor_information')['ein'] ?? '')}}" maxlength="255" {{--{{old('tax_information', session('sponsor_information')['tax_information']) != 'business' ? 'disabled':''}}--}}>
                                        </div>
                                    </div>
                                    <div class="col-12 form-subtitle">{!! __('lang.BUSINESS_NAME') !!} ({!! __('lang.IF_APPLICABLE') !!})</div>
                                    <div class="col-12 mb-2">
                                        <div class="float-container">
                                            <label for="BusinessNameInput">{!! __('lang.BUSINESS_NAME') !!}</label>
                                            <input id="BusinessNameInput" name="business_name" type="text" value="{{old('business_name',session('sponsor_information')['business_name']??'')}}" maxlength="255" {{--{{old('tax_information', session('sponsor_information')['tax_information']) != 'business' ? 'disabled':''}}--}}>
                                        </div>
                                    </div>
                                </div>
                                @if(session('billing_country')->merchant == 'ipaytotal')
                                <div class="row">
                                    <div class="col-lg-12 col-12 left-column">
                                        <div class="row">
                                            <div class="col-12 form-subtitle">{!! __('lang.PAYMENT_METHOD') !!}</div>
                                            <div class="col-12 mb-2 payment-method-select-wrap">
                                                <select id="PaymentTypeInput" class="payment-method-select" name="payment_type" required>
                                                    <option disabled>
                                                        --Select--
                                                    </option>
                                                    {{--TODO: Add all payment methods--}}
                                                    <!--<option value="4" title="<?php echo e(url('images/bitpay-icon.png')); ?>">BitPay</option>-->
                                                    <option value="2" title="<?php echo e(url('images/credit-card-logos.jpg')); ?>" selected>{!! __('lang.ADD_NEW_CREDIT_CARD') !!}
                                                    </option>
                                                </select>
                                                <i class="fa fa-angle-down arrow-icon"></i>
                                            </div>
                                            <div class="col-12 credit-card-info-wrap">
                                                <div class="row">
                                                    <div class="col-12 form-subtitle">{!! __('lang.CREDIT_CARD_INFO') !!}</div>
                                                    <div class="col-12 mb-2">
                                                        <div class="float-container">
                                                            <label for="CreditCardNameInput">{!! __('lang.NAME_ON_CARD') !!} *</label>
                                                            <input id="CreditCardNameInput" name="credit_card_name" type="text" value="{{old('credit_card_name',session('sponsor_information')['credit_card_name'])}}" maxlength="100">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <div class="float-container">
                                                            <label for="CreditCardNumberInput">{!! __('lang.CARD_NUMBER') !!} *</label>
                                                            <input id="CreditCardNumberInput" class="cc-number" name="credit_card_number" type="tel" value="{{old('credit_card_number',session('sponsor_information')['credit_card_number'])}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-8 mb-2 pr-2">
                                                        <div class="float-container">
                                                            <label for="ExpiryDateInput">{!! __('lang.EXPIRATION_DATE') !!} (MM/YY) *</label>
                                                            <input id="ExpiryDateInput" class="cc-expires" name="expiry_date" type="text" value="{{old('expiry_date',session('sponsor_information')['expiry_date'])}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mb-2 pl-2">
                                                        <div class="float-container">
                                                            <label for="CVVInput">{!!__('lang.CVV')!!} *</label>
                                                            <input id="CVVInput" class="cc-cvc" name="cvv" type="text" value="{{old('cvv',session('sponsor_information')['cvv'])}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{--<div class="row">--}}
                                        {{--<div class="col-12">--}}
                                        {{--<div class="checkbox mt-3 mb-3 pr-3">--}}
                                        {{--<input type="checkbox" name="save_payment_method"--}}
                                        {{--class="form-check-input" id="savePaymentMethod">--}}
                                        {{--<label class="form-check-label" for="savePaymentMethod">{!! __('lang.SAVE_PAYMENT_METHOD') !!}</label>--}}
                                        {{--</div>--}}
                                        {{--</div>--}}
                                        {{--</div>--}}
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="col-lg-6 col-12 right-column">
                                <div class="row">
                                    <div class="col-12 form-subtitle">{!! __('lang.DATE_OF_BIRTH') !!}</div>
                                    <div class="col-4 mb-2 pr-0">
                                        <div class="float-container select-wrapper">
                                            <label for="BirthMonthInput">{!! __('lang.MONTH') !!} *</label>
                                            <select id="BirthMonthInput" name="birth_month">
                                                <option value="" selected disabled>
                                                    ----
                                                </option>
                                                @foreach(\App\Models\PreEnrollmentSelection::getMonths() as $key => $month)
                                                <option value="{{$key}}" {{ old('birth_month', session('sponsor_information')['birth_month']) == $key ? 'selected':'' }}>
                                                    {{$month}}
                                                </option>
                                                @endforeach
                                            </select>
                                            <i class="fa fa-angle-down arrow-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2 pr-0 pl-0">
                                        <div class="float-container select-wrapper" style="border-left: none; border-right: none;">
                                            <label for="BirthDayInput">{!! __('lang.DAY') !!} *</label>
                                            <select id="BirthDayInput" name="birth_day">
                                                <option value="" selected disabled>
                                                    ----
                                                </option>

                                                @foreach(\App\Models\PreEnrollmentSelection::getDays() as $key => $day)
                                                <option value="{{$day}}" {{ old('birth_day', session('sponsor_information')['birth_month']) == $day ? 'selected':'' }}>
                                                    {{$day}}
                                                </option>
                                                @endforeach


                                            </select>
                                            <i class="fa fa-angle-down arrow-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-4 mb-2 pl-0">
                                        <div class="float-container select-wrapper">
                                            <label for="BirthYearInput">{!! __('lang.YEAR') !!} *</label>
                                            <select id="BirthYearInput" name="birth_year">
                                                <option value="" selected disabled>
                                                    ----
                                                </option>
                                                @php
                                                @endphp
                                                @for($i = (date('Y')-17);$i>=(date('Y')-100);$i--)
                                                <option value="{{$i}}" {{ old('birth_year', session('sponsor_information')['birth_year']) == $i ? 'selected':'' }}>{{$i}}</option>
                                                @endfor
                                            </select>
                                            <i class="fa fa-angle-down arrow-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="row">--}}
                                {{--<div class="col-12 form-subtitle">{!! __('lang.PRIMARY_APPLICANT') !!}</div>--}}
                                {{--<div class="col-12 mb-2">--}}
                                {{--<div class="float-container select-wrapper">--}}
                                {{--<label for="GenderInput">{!! __('lang.GENDER') !!} *</label>--}}
                                {{--<select id="GenderInput" name="gender">--}}
                                {{--<option value="" selected disabled>--}}
                                {{---- Select ----}}
                                {{--</option>--}}
                                {{--<option value="male" {{ old('gender', session('sponsor_information')['gender'])=='male' ?'selected' : '' }}>--}}
                                {{--{!! __('lang.MALE') !!}--}}
                                {{--</option>--}}
                                {{--<option value="female" {{old('gender', session('sponsor_information')['gender'])=='female' ?'selected': '' }}>--}}
                                {{--{!! __('lang.FEMALE') !!}--}}
                                {{--</option>--}}
                                {{--</select>--}}
                                {{--<i class="fa fa-angle-down arrow-icon"></i>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                {{--</div>--}}
                                <div class="row">
                                    <div class="col-12 form-subtitle">{!! __('lang.PRIMARY_ADDRESS') !!}</div>
                                    <div class="col-12 mb-2">
                                        <div class="float-container">
                                            <label for="PrimaryAddressOneInput">{!! __('lang.ADDRESS_LINE') !!} 1 *</label>
                                            <input id="PrimaryAddressOneInput" name="primary_address_line_one" type="text" value="{{old('primary_address_line_one',session('sponsor_information')['primary_address_line_one']??'')}}" maxlength="600">
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <div class="float-container">
                                            <label for="PrimaryAddressTwoInput">{!! __('lang.ADDRESS_LINE') !!} 2</label>
                                            <input id="PrimaryAddressTwoInput" name="primary_address_line_two" type="text" value="{{old('primary_address_line_two',session('sponsor_information')['primary_address_line_two']??'')}}" maxlength="600">
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2 pr-2">
                                        <div class="float-container">
                                            <label for="PrimaryCityInput">{!! __('lang.CITY') !!} *</label>
                                            <input id="PrimaryCityInput" name="primary_city" type="text" value="{{old('primary_city',session('sponsor_information')['primary_city']??'')}}" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2 pl-2">
                                        <div class="float-container">
                                            <label for="PrimaryStateInput">{!! __('lang.STATE_PROVINCE') !!} *</label>
                                            <input id="PrimaryStateInput" name="primary_state" type="text" value="{{old('primary_state',session('sponsor_information')['primary_state']??'')}}" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-6 mb-2 pr-2">
                                        <div class="float-container">
                                            <label for="PrimaryPostalCodeInput">{!! __('lang.POSTAL_CODE') !!} *</label>
                                            <input id="PrimaryPostalCodeInput" name="primary_postal_code" type="text" value="{{old('primary_postal_code',session('sponsor_information')['primary_postal_code']??'')}}" minlength="4" maxlength="8">
                                        </div>
                                    </div>
                                    <div class="col-12 mb-2 pr-2">
                                        <div class="float-container select-wrapper">
                                            <label for="CountryInput">{!! __('lang.COUNTRY') !!} *</label>
                                            <select id="CountryInput" name="primary_country">
                                                <option value="" {{(old('primary_country', session('sponsor_information')['primary_country']) == ''?'selected':'')}}>
                                                    ----
                                                </option>
                                                @foreach($countries as $c)
                                                <option @php if (!empty(old('primary_country')) && old('primary_country')==$c->countrycode) {
                                                    echo 'selected';
                                                    } else if (empty(old('primary_country')) && !empty(session('vitals')['country']) && session('vitals')['country'] == $c->id) {
                                                    echo 'selected';
                                                    }
                                                    @endphp
                                                    value="{{$c->countrycode}}"
                                                    >{{$c->country}}</option>
                                                @endforeach
                                            </select>
                                            <i class="fa fa-angle-down arrow-icon"></i>
                                        </div>
                                    </div>
                                    <div class="row mt-2 mb-2">
                                        <div class="col-lg-7 col-md-5 col-7 question-label">
                                            {!! __('lang.IS_YOUR_BILLING_ADDRESS_THE_SAME') !!}
                                        </div>
                                        <div class="col-5">
                                            <div class="row" style="align-items: center; height: 100%;">
                                                <div class="col-lg-6 col-md-3 col-6 radio" style="text-align: right;">
                                                    <input type="radio" name="is_billing_same" id="BillingSameAsPrimary1" value="yes" {{old('is_billing_same' , session('sponsor_information')['is_billing_same']) != 'no' ? 'checked':''}}>
                                                    <label for="BillingSameAsPrimary1">{!! __('lang.YES') !!}</label>
                                                </div>
                                                <div class="col-lg-6 col-md-3 col-6 radio" style="text-align: right;">
                                                    <input type="radio" name="is_billing_same" id="BillingSameAsPrimary2" value="no" {{old('is_billing_same' , session('sponsor_information')['is_billing_same']) == 'no' ? 'checked':''}}>
                                                    <label for="BillingSameAsPrimary2">{!! __('lang.NO') !!}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row billing-address {{old('is_billing_same', session('sponsor_information')['is_billing_same']) == 'no' ? 'open':''}}">
                                        <div class="col-12 form-subtitle">{!! __('lang.BILLING_ADDRESS') !!}</div>
                                        <div class="col-12 mb-2">
                                            <div class="float-container">
                                                <label for="BillingAddressOneInput">{!! __('lang.ADDRESS_LINE') !!} 1 *</label>
                                                <input id="BillingAddressOneInput" name="billing_address_line_one" type="text" value="{{old('billing_address_line_one',session('sponsor_information')['billing_address_line_one']??'')}}" maxlength="600" {{--{{old('is_billing_same', session('sponsor_information')['is_billing_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="float-container">
                                                <label for="BillingAddressTwoInput">{!! __('lang.ADDRESS_LINE') !!} 2</label>
                                                <input id="BillingAddressTwoInput" name="billing_address_line_two" type="text" value="{{old('billing_address_line_two',session('sponsor_information')['billing_address_line_two']??'')}}" maxlength="600" {{--{{old('is_billing_same', session('sponsor_information')['is_billing_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2 pr-2">
                                            <div class="float-container">
                                                <label for="BillingCityInput">{!! __('lang.CITY') !!} *</label>
                                                <input id="BillingCityInput" name="billing_city" type="text" value="{{old('billing_city',session('sponsor_information')['billing_city']??'')}}" maxlength="255" {{--{{old('is_billing_same', session('sponsor_information')['is_billing_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2 pl-2">
                                            <div class="float-container">
                                                <label for="BillingStateInput">{!! __('lang.STATE_PROVINCE') !!} *</label>
                                                <input id="BillingStateInput" name="billing_state" type="text" value="{{old('billing_state',session('sponsor_information')['billing_state']??'')}}" maxlength="255" {{--{{old('is_billing_same',session('sponsor_information')['is_billing_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        {{--TODO: Auto-formatting the input depending on selected country--}}
                                        <div class="col-6 mb-2 pr-2">
                                            <div class="float-container">
                                                <label for="BillingPostalCodeInput">{!! __('lang.POSTAL_CODE') !!} *</label>
                                                <input id="BillingPostalCodeInput" name="billing_postal_code" type="text" value="{{old('billing_postal_code',session('sponsor_information')['billing_postal_code']??'')}}" minlength="4" maxlength="8" {{--{{old('is_billing_same',session('sponsor_information')['is_billing_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2 pr-2">
                                            <div class="float-container select-wrapper">
                                                <label for="BillingCountryInput">{!! __('lang.COUNTRY') !!} *</label>
                                                <select id="BillingCountryInput" name="billing_country" {{--{{old('is_billing_same', session('sponsor_information')['is_billing_same']) != 'no' ? 'disabled':''}}--}}>
                                                    <option value="" {{(old('billing_country', session('sponsor_information')['billing_country']) == ''?'selected':'')}}>
                                                        ----
                                                    </option>
                                                    @foreach($countries as $c)
                                                    <option value="{{$c->countrycode}}" {{(old('billing_country', session('sponsor_information')['billing_country']) == $c->countrycode ?'selected': ''  )}}>{{$c->country}}</option>
                                                    @endforeach
                                                </select>
                                                <i class="fa fa-angle-down arrow-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2 mb-2">
                                        <div class="col-lg-7 col-md-5 col-7 question-label">
                                            {!! __('lang.IS_YOUR_SHIPPING_ADDRESS_THE_SAME') !!}
                                        </div>
                                        <div class="col-5">
                                            <div class="row" style="align-items: center; height: 100%;">
                                                <div class="col-lg-6 col-md-3 col-6 radio" style="text-align: right;">
                                                    <input type="radio" name="is_shipping_same" id="ShippingSameAsPrimary1" value="yes" {{old('is_shipping_same', session('sponsor_information')['is_shipping_same']) != 'no' ? 'checked':''}}>
                                                    <label for="ShippingSameAsPrimary1">{!! __('lang.YES') !!}</label>
                                                </div>
                                                <div class="col-lg-6 col-md-3 col-6 radio" style="text-align: right;">
                                                    <input type="radio" name="is_shipping_same" id="ShippingSameAsPrimary2" value="no" {{old('is_shipping_same', session('sponsor_information')['is_shipping_same']) == 'no' ? 'checked':''}}>
                                                    <label for="ShippingSameAsPrimary2">{!! __('lang.NO') !!}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row shipping-address {{old('is_shipping_same', session('sponsor_information')['is_shipping_same']) == 'no' ? 'open':''}}">
                                        <div class="col-12 form-subtitle">{!! __('lang.SHIPPING_ADDRESS') !!}</div>
                                        <div class="col-12 mb-2">
                                            <div class="float-container">
                                                <label for="ShippingAddressOneInput">{!! __('lang.ADDRESS_LINE') !!} 1 *</label>
                                                <input id="ShippingAddressOneInput" name="shipping_address_line_one" type="text" value="{{old('shipping_address_line_one',session('sponsor_information')['shipping_address_line_one']??'')}}" maxlength="600" {{--{{old('is_shipping_same', session('sponsor_information')['is_shipping_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="float-container">
                                                <label for="ShippingAddressTwoInput">{!! __('lang.ADDRESS_LINE') !!} 2</label>
                                                <input id="ShippingAddressTwoInput" name="shipping_address_line_two" type="text" value="{{old('shipping_address_line_two',session('sponsor_information')['shipping_address_line_two']??'')}}" maxlength="600" {{--{{old('is_shipping_same', session('sponsor_information')['is_shipping_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2 pr-2">
                                            <div class="float-container">
                                                <label for="ShippingCityInput">{!! __('lang.CITY') !!} *</label>
                                                <input id="ShippingCityInput" name="shipping_city" type="text" value="{{old('shipping_city',session('sponsor_information')['shipping_city']??'')}}" maxlength="255" {{--{{old('is_shipping_same', session('sponsor_information')['is_shipping_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-2 pl-2">
                                            <div class="float-container">
                                                <label for="ShippingStateInput">{!! __('lang.STATE_PROVINCE') !!} *</label>
                                                <input id="ShippingStateInput" name="shipping_state" type="text" value="{{old('shipping_state',session('sponsor_information')['shipping_state']??'')}}" maxlength="255" {{--{{old('is_shipping_same', session('sponsor_information')['is_shipping_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        {{--TODO: Auto-formatting the input depending on selected county--}}
                                        <div class="col-6 mb-2 pr-2">
                                            <div class="float-container">
                                                <label for="ShippingPostalCodeInput">{!! __('lang.POSTAL_CODE') !!} *</label>
                                                <input id="ShippingPostalCodeInput" name="shipping_postal_code" type="text" value="{{old('shipping_postal_code',session('sponsor_information')['shipping_postal_code']??'')}}" minlength="4" maxlength="8" {{--{{old('is_shipping_same', session('sponsor_information')['is_shipping_same']) != 'no' ? 'disabled':''}}--}}>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2 pr-2">
                                            <div class="float-container select-wrapper">
                                                <label for="ShippingCountryInput">{!! __('lang.COUNTRY') !!} *</label>
                                                <select id="ShippingCountryInput" name="shipping_country" {{--{{old('is_shipping_same', session('sponsor_information')['is_shipping_same'] ) != 'no' ? 'disabled':''}}--}}>
                                                    <option value="" {{(old('shipping_country', session('sponsor_information')['shipping_country']) == ''?'selected':'')}}>
                                                        ----
                                                    </option>
                                                    @foreach($countries as $c)
                                                    <option value="{{$c->countrycode}}" {{ old('shipping_country', session('sponsor_information')['shipping_country']) == $c->countrycode ?'selected': '' }}>{{$c->country}}</option>
                                                    @endforeach
                                                </select>
                                                <i class="fa fa-angle-down arrow-icon"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--Vitals Info--}}
                            <input type="hidden" name="country" value="{{session('vitals')['country']}}" />
                            <input type="hidden" name="language" value="{{session('vitals')['language']}}" />
                            <input type="hidden" name="firstname" value="{{session('vitals')['firstname']}}" />
                            <input type="hidden" name="lastname" value="{{session('vitals')['lastname']}}" />
                            <input type="hidden" name="email" value="{{session('vitals')['email']}}" />
                            <input type="hidden" name="country_code" value="{{session('vitals')['country_code']}}" />
                            <input type="hidden" name="product_id" value="1" />
                            <input type="hidden" name="mobile_number" value="{{session('vitals')['mobile_number']}}" />
                            {{--<div class="row">--}}
                            {{--<div class="col-12 col-md-12 form-field text-center mt-3">--}}
                            {{--<div class="form-field-button">--}}
                            {{--@csrf--}}
                            {{--<center><button type="submit" class="input-btn">--}}
                            {{--{!!__('lang.CONTINUE')!!}--}}
                            {{--<i id="loading" class="fa fa-spinner fa-spin" style="display: none"></i>--}}
                            {{--</button></center>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                            <div class="col-12 col-md-12 form-field text-center mt-4">
                                <div class="form-field-button">
                                    <div class="form-field-button">
                                        @csrf
                                        <center><button type="submit" class="input-btn" style="width: 50%">
                                                {!!__('lang.CONTINUE')!!}
                                                <i id="loading" class="fa fa-spinner fa-spin" style="display: none"></i>
                                            </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('custom-js')
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js" integrity="sha384-B1miHxuCmAMGc0405Cm/zSXCc38EP5hNOy2bblqF6yiX01Svinm1mWMwJDdNDBGr" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.js" integrity="sha384-F1bF/FGKNfDfwYyFH6q7sJ50hMoqgL53b0p1HciOITyOzvuSSRzoBlPVe54KtRWB" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.0.1/jquery.payment.min.js" integrity="sha384-AKEcmGRBjvgYcxHEMhGo2Dp+07CoD9RPqK1zAZ9kNkOt6ayBTDyrqJSDC0zcMUZW" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.cc-number').formatCardNumber();
        $('.cc-expires').formatCardExpiry();
        $('.cc-cvc').formatCardCVC();

        $('#payment-form').submit(function() {
            $('#payment-button').html('<i class="fa fa-spinner fa-spin"></i> Processing').prop('disabled', true);
        });
    })
</script>
@endsection