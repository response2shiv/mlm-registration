@extends('layout')
@section('title')
    ncrease - {!! __('lang.CARD_DECLINED_TITLE') !!}
@endsection
@section('content')
    <section class="enrollment-content-wrapper">
        <div class="wrapper content-wrapper">
            <div class="enrollment-title">
                {!! __('lang.THE_CARD_YOU_ARE_USING_CANNOT_BE') !!}
            </div>
            <div class="errors-wrapper mb-5" style="max-width: 1100px;">
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
            <div class="enrollment-container mb-5 pl-3 pr-3">
                <form method="POST" action="{{url('/payment/declined')}}" id="payment-form"
                      style="max-width: 970px; margin: 0 auto;">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 col-12 left-column">
                            <div class="row">
                                <div class="col-12 form-subtitle">{!! __('lang.PAYMENT_METHOD') !!}</div>
                                <div class="col-12 mb-2 payment-method-select-wrap">
                                    <select id="PaymentTypeInput" class="payment-method-select" name="payment_method"
                                            required>
                                        <option disabled>
                                            --Select--
                                        </option>
                                        <option
                                            value="2"
                                            title="<?php echo e(url('images/credit-card-logos.jpg')); ?>">Credit Card
                                        </option>
                                    </select>
                                    <i class="fa fa-angle-down arrow-icon"></i>
                                </div>
                                <div class="col-12 credit-card-info-wrap">
                                    <div class="row" style="margin-top: 10px">
                                        <div class="col-12 form-subtitle">{!! __('lang.CREDIT_CARD_INFO') !!}</div>
                                        <div class="col-12 mb-2">
                                            <div class="float-container">
                                                <label for="CreditCardNameInput">{!! __('lang.NAME_ON_CARD') !!} *</label>
                                                <input id="CreditCardNameInput" name="credit_card_name" type="text"
                                                       value="{{old('credit_card_name')}}" maxlength="100" required>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="float-container">
                                                <label for="CreditCardNumberInput">{!! __('lang.CARD_NUMBER') !!} *</label>
                                                <input id="CreditCardNumberInput" class="cc-number"
                                                       name="credit_card_number" type="tel"
                                                       value="{{old('credit_card_number')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-8 mb-2 pr-2">
                                            <div class="float-container">
                                                <label for="ExpiryDateInput">{{__('lang.EXPIRATION_DATE')}}
                                                    (MM/YY) *</label>
                                                <input id="ExpiryDateInput" class="cc-expires" name="expiry_date"
                                                       type="text" value="{{old('expiry_date')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2 pl-2">
                                            <div class="float-container">
                                                <label for="CVVInput">{!!__('lang.CVV')!!} *</label>
                                                <input id="CVVInput" class="cc-cvc" name="cvv" type="text"
                                                       value="{{old('cvv')}}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="row">--}}
                                {{--<div class="col-12">--}}
                                    {{--<div class="checkbox mt-3 mb-3 pr-3">--}}
                                        {{--<input type="checkbox" name="save_payment_method" class="form-check-input"--}}
                                               {{--id="savePaymentMethod">--}}
                                        {{--<label class="form-check-label" for="savePaymentMethod">{!! __('lang.SAVE_PAYMENT_METHOD') !!}</label>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        </div>
                        <div class="col-lg-6 col-12 right-column">
                            <div class="row billing-address">
                                <div class="col-12 form-subtitle">{!! __('lang.BILLING_ADDRESS') !!}</div>
                                <div class="col-12 mb-2">
                                    <div class="float-container">
                                        <label for="BillingAddressOneInput">{!! __('lang.ADDRESS_LINE') !!} 1 *</label>
                                        <input id="BillingAddressOneInput" name="billing_address_line_one" type="text"
                                               value="{{old('billing_address_line_one',session('sponsor_information')['billing_address_line_one']??'')}}"
                                               required maxlength="600">
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="float-container">
                                        <label for="BillingAddressTwoInput">{!! __('lang.ADDRESS_LINE') !!} 2</label>
                                        <input id="BillingAddressTwoInput" name="billing_address_line_two" type="text"
                                               value="{{old('billing_address_line_two',session('sponsor_information')['billing_address_line_two']??'')}}"
                                               maxlength="600">
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <div class="float-container select-wrapper">
                                        <label for="BillingCountry"
                                               style="color: #666667;font-size: 15px;">{!! __('lang.COUNTRY') !!} *</label>
                                        <select id="BillingCountry" required
                                                name="billing_country">
                                            <option value=""
                                                {{(old('billing_country') == ''?'selected':'')}}>
                                                ----
                                            </option>
                                            @foreach($countries as $c)--}}
                                            <option
                                                {{(old('billing_country') == $c->countrycode ?'selected': ''  )}} value="{{$c->countrycode}}">{{$c->country}}</option>
                                            @endforeach
                                        </select>
                                        <input id="BillingAddressTwoInput" name="billing_address_line_two" type="text"
                                               value="{{old('billing_address_line_two',session('sponsor_information')['billing_address_line_two']??'')}}"
                                               maxlength="600">
                                    </div>
                                </div>

                                <div class="col-6 mb-2 pr-2">
                                    <div class="float-container">
                                        <label for="BillingCityInput">{!! __('lang.CITY') !!} *</label>
                                        <input id="BillingCityInput" name="billing_city" type="text"
                                               value="{{old('billing_city',session('sponsor_information')['billing_city']??'')}}"
                                               required maxlength="255">
                                    </div>
                                </div>
                                <div class="col-6 mb-2 pl-2">
                                    <div class="float-container">
                                        <label for="BillingStateInput">{!! __('lang.STATE_PROVINCE') !!} *</label>
                                        <input id="BillingStateInput" name="billing_state" type="text"
                                               value="{{old('billing_state',session('sponsor_information')['billing_state']??'')}}"
                                               required maxlength="255">
                                    </div>
                                </div>
                                <div class="col-6 mb-2 pr-2">
                                    <div class="float-container">
                                        <label for="BillingPostalCodeInput">{!! __('lang.POSTAL_CODE') !!} *</label>
                                        <input id="BillingPostalCodeInput" name="billing_postal_code" type="text"
                                               value="{{old('billing_postal_code',session('sponsor_information')['billing_postal_code']??'')}}"
                                               required minlength="4" maxlength="8">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12 col-md-12 form-field text-center mt-3">
                            <div class="form-field-button">
                                <input type="hidden" name="session_id" value="{{$sessionId}}"/>
                                <input type="hidden" name="new_card" value="1"/>
                                <input type="hidden" name="source" value="declined"/>
                                <input type="hidden" name="unicrypt_declined" id="unicrypt_declined" value="false">

                                <button type="submit" class="input-btn" id="payment-button">
                                    {!! __('lang.CHECKOUT_NEW_CARD') !!}
                                </button> <br />

                                <!-- MODAL -->
                                <div class="modal fade bd-example-modal-lg" id="loading" tabindex="-1" data-backdrop="static">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content" style="width: 450px; color: white;">
                                            <h3 style="color: white;">{!! __('lang.MODAL_WAITING_MESSAGE') !!}</h3> <br />
                                            <span class="fa fa-spinner fa-spin fa-5x"></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- MODAL -->

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('custom-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.0.1/jquery.payment.min.js"
            integrity="sha384-AKEcmGRBjvgYcxHEMhGo2Dp+07CoD9RPqK1zAZ9kNkOt6ayBTDyrqJSDC0zcMUZW"
            crossorigin="anonymous"></script>
    <script type="text/javascript">

        function modal() {
           $('#loading').modal('show');
        }

        $(document).ready(function () {
            $('.cc-number').formatCardNumber();
            $('.cc-expires').formatCardExpiry();
            $('.cc-cvc').formatCardCVC();

            $('#payment-form').submit(function () {
                modal();
            });

            $('#payment-button').click(function () {
                $('#unicrypt_declined').val('false');
                $('#payment-button').html('<i class="fa fa-spinner fa-spin"></i> Processing').prop('disabled', true);
                $('#payment-form').submit();
            });
        })
    </script>
@endsection
