@extends('layout')
@section('title')
    ncrease - Review Order & Payment
@endsection
@section('content')
    <section class="enrollment-content-wrapper">
        <div class="wrapper content-wrapper">
            <div class="enrollment-title">{!! __('lang.SELECT PAYMENT METHOD') !!}</div>
            <div class="errors-wrapper mb-5" style="max-width: 1100px;">
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
                <form method="POST" action="{{'/payment/checkout'}}" id="payment-form" class="optional-purchase-form">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-5">
                            <div class="order-review-wrap">
                                <div id="productCheckOut">
                                    <div class="row order-review-header" style="padding-left: 20px">
                                        <div class="col-9">
                                            {!! __('lang.PACK') !!}
                                        </div>
                                        <div class="col-3 text-center">
                                            {!! __('lang.PRICE') !!}
                                        </div>
                                    </div>
                                    <div class="order-pack-full-wrap">
                                        @if($standby)
                                            <div class="row order-pack-non-color-wrap">
                                                <div class="col-sm-8 col-8">
                                                    <div class="order-pack-name"
                                                         style="text-transform: uppercase"> {{$standby->productname}}</div>
                                                    <div
                                                        class="order-pack-notes">{!! __('lang.REQUIRED_ENROLLMENT_PACK') !!}</div>
                                                </div>
                                                <div class="col-sm-4 col-4 order-pack-price">
                                                    ${{number_format($standby->price,2)}}
                                                    @if($country_conversion != "USD")
                                                     / {{$products_conversion[1]['conversion']['display_amount']}}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if($product)
                                            <div class="row order-pack-non-color-wrap">
                                                <div class="col-sm-8 col-8">
                                                    <div class="order-pack-name"
                                                         style="text-transform: uppercase">{{$product->productname}}</div>
                                                    <div
                                                        class="order-pack-notes">{!! __('lang.OPTIONAL_PROMOTIONAL_ENROLLMENT_PACK') !!}</div>
                                                </div>
                                                <div class="col-sm-4 col-4 order-pack-price">
                                                    ${{number_format($product->price,2)}}
                                                    @if($country_conversion != "USD")
                                                     / {{$products_conversion[$product->id]['conversion']['display_amount']}}
                                                    @endif
                                                </div>
                                                <div class="change-pack-btn">
                                                    <a href="{{ url('optional-promotional') }}">{!! __('lang.CHANGE_PACK') !!}</a>
                                                </div>
                                            </div>
                                            <div class="row order-pack-non-color-wrap">
                                                <div class="col-sm-8 col-8">
                                                    <div class="order-pack-name"
                                                         style="text-transform: uppercase">Shipping charges</div>
                                                    <div
                                                        class="order-pack-notes"></div>
                                                </div>
                                                <div class="col-sm-4 col-4 order-pack-price">
                                                    ${{number_format(\App\Models\Products::getShippingValue(),2)}}
                                                    @if($country_conversion != "USD")
                                                     / {{$shipping_conversion}}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        @if(!empty($ticket_product))
                                            <div class="row order-pack-non-color-wrap">
                                                <div class="col-sm-8 col-8">
                                                    <div class="order-pack-name"
                                                         style="text-transform: uppercase">{{$ticket_product->productname}}</div>
                                                    <div
                                                        class="order-pack-notes">{!! __('lang.SPECIAL_ENROLLMENT_PRICE') !!}</div>
                                                </div>
                                                <div class="col-sm-4 col-4 order-pack-price">
                                                    ${{number_format($ticket_product->price,2)}}
                                                    @if($country_conversion != "USD")
                                                     / {{$ticket_display}}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    {{--<div class="row mt-3">--}}
                                    {{--<div class="col-sm-9 col-6">--}}
                                    {{--<div>{!! __('lang.SUB_TOTAL') !!}</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-sm-3 col-6 order-pack-price">--}}
                                    {{--${{ number_format($sub_total,2)  }}--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="row mt-3">--}}
                                    {{--<div class="col-sm-9 col-6">--}}
                                    {{--<div style="text-transform: uppercase">{!! __('lang.DISCOUNT') !!}</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-sm-3 col-6 order-pack-price" id="">--}}
                                    {{--${{ number_format($discount,2)  }}--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    <hr>
                                    <div class="row mt-3">
                                        <div class="col-sm-7 col-7" style="font-size: 25px">
                                            <div>{!! __('lang.TOTAL') !!}</div>
                                        </div>
                                        <div class="col-sm-5 col-5 order-pack-price" id="" style="font-size: 25px">
                                            @php
                                                $price = explode(".",number_format($total,2));
                                            @endphp

                                            ${{$price[0]}}<sup>.{{$price[1]}}</sup>
                                            @if($country_conversion != "USD")
                                                / {{$total_conversion}}
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                                <div class="row voucher-input-wrap">
                                    <div class="col-8 pl-0">
                                        <div class="float-container">
                                            <label for="VoucherCodeInput">{!! __('lang.VOUCHER_CODE') !!}</label>
                                            <input id="coupon-code" name="voucher_code" type="tel" value="{{old('voucher_code')}}">
                                        </div>
                                    </div>
                                    <div class="col-4 form-field text-center mt-0 mb-0 pr-0">
                                        <div class="form-field-button">
                                            <button type="button" class="input-btn" id="apply-coupon">
                                                {!! __('lang.APPLY') !!}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="checkbox mt-3 mb-3 pr-3">
                                        <input type="checkbox" name="updates_subscribe" class="form-check-input" id="monthlyFee" required>
                                          @php
                                             $productID = session('product_id'); //get product id
                                             $tierPrice ='$99.95';
                                             if($productID == Null){
                                               $tierPrice = '$49.95'; // standby pack
                                             }
                                             //if coach pack is chosen(productID==2) and country is tier3 price is 49.95
                                             if($productID == 2 && (int)request()->session()->get('user_tier') === 1){
                                               $tierPrice = '$49.95';
                                             }
                                        @endphp

                                        <label class="form-check-label" for="monthlyFee">{!! __('lang.I_UNDERSTAND_THAT_A_RECURRING_MONTHLY',['tier_price'=>$tierPrice] ) !!}</label>
                                    </div>
                                </div>
                                <div class="row order-review-notes pr-3">
                                    <p>{!! __('lang.YOUR_SELECTED_PAYMENT_METHOD_FOR_THIS_SUBSCRIPTION') !!}</p>
                                </div>
                                <div class="row">
                                    <div class="checkbox mt-3 mb-3 pr-3">
                                        <input type="checkbox" name="updates_subscribe" class="form-check-input"
                                               id="monthlyFeex" required>
                                        <label class="form-check-label" for="monthlyFeex">
                                            {!! __('lang.I_HAVE_READ_AND_AGREE_TO_ALL') !!}
                                        </label>
                                        {{-- <p style="padding-left: 15px; margin-top: 10px">
                                            <a href="#" data-toggle="modal"
                                               data-target="#terms-and-condition">{!! __('lang.TERMS_CONDITIONS') !!}</a>
                                            <a href="#"
                                                                                   style="float: right">{!! __('lang.VIRTUAL_OFFICE_AGREEMENT') !!}</a>
                                        </p> --}}
                                        <p style="padding-left: 15px;">
                                            <a href="#" data-toggle="modal"
                                               data-target="#policies-and-procedures">{!! __('lang.POLICIES_PROCEDURES') !!}</a>
                                            <a href="#" style="float: right" data-toggle="modal"
                                               data-target="#privacy-policy">{!! __('lang.PRIVACY_POLICY') !!}</a>
                                        </p>
                                        <p style="padding-left: 15px;">
                                            <a href="#">{!! __('lang.ALLOW_MY_SPONSOR_TO_CONTACT_ME') !!}</a>
                                        </p>
                                        <p style="padding-left: 15px;">
                                            <a href="#">{!! __('lang.ALLOW_NOTIFICATIONS_MARKETING') !!}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 pl-0 pr-0" id="paymentMethod" hidden>
                            <div class="row optional-purchase-payment-wrap">
                                <div class="col-12 form-subtitle">{!! __('lang.PAYMENT_METHOD') !!}{{(old('payment_method') == 't1'?'selected':'')}}</div>
                                <div class="col-12 mb-2 payment-method-select-wrap">
                                    <select id="PaymentTypeInput" class="payment-method-select" name="payment_type" required>
                                        <option disabled>
                                            --Select--
                                        </option>
                                        <option value="1" title="<?php echo e(url('images/credit-card-logos.jpg')); ?>"
                                                selected>{{$card_number}}</option>
                                        <option value="2" {{(old('payment_type') == 2?'selected':'')}}
                                        {{(old('payment_method') == 't1'?'selected':'')}} title="<?php echo e(url('images/credit-card-logos.jpg')); ?>">
                                            Add New Card
                                        </option>
                                    </select>
                                    <i class="fa fa-angle-down arrow-icon"></i>
                                </div>
                                <div class="col-12 credit-card-info-wrap">
                                    <div class="row">
                                        <div class="col-12 form-subtitle" style="margin-top: 20px">{!! __('lang.CREDIT_CARD_INFO') !!}
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="float-container">
                                                <label for="CreditCardNameInput">{!! __('lang.NAME_ON_CARD') !!} *</label>
                                                <input id="CreditCardNameInput" name="credit_card_name" type="text" value="{{old('credit_card_name')}}" maxlength="100" required>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="float-container">
                                                <label for="CreditCardNumberInput">{!! __('lang.CARD_NUMBER') !!} *</label>
                                                <input id="CreditCardNumberInput" class="cc-number" name="credit_card_number" type="tel" value="{{old('credit_card_number')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-8 mb-2 pr-2">
                                            <div class="float-container">
                                                <label for="ExpiryDateInput">{!! __('lang.EXPIRATION_DATE') !!} (MM/YY)
                                                    *</label>
                                                <input id="ExpiryDateInput" class="cc-expires" name="expiry_date" type="text" value="{{old('expiry_date')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-4 mb-2 pl-2">
                                            <div class="float-container">
                                                <label for="CVVInput">{!!__('lang.CVV')!!} *</label>
                                                <input id="CVVInput" class="cc-cvc" name="cvv" type="text" value="{{old('cvv')}}" required>
                                            </div>
                                        </div>
                                        {{--<div class="col-12 mb-2">--}}
                                            {{--<div class="checkbox mt-3 mb-3 pr-3">--}}
                                                {{--<input type="checkbox" name="save_payment_method" class="form-check-input"--}}
                                                       {{--id="savePaymentMethod">--}}
                                                {{--<label class="form-check-label" for="savePaymentMethod">{!! __('lang.SAVE_PAYMENT_METHOD') !!}</label>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                        <input name="new_card" type="hidden" value="1">
                                        <input name="source" type="hidden" value="checkout">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12 form-field text-center mt-3">
                            <div class="form-field-button">
                                <input name="payment_method" type="hidden"
                                       value="{{\App\Models\PaymentMethodType::TYPE_T1_PAYMENTS}}">
                                <input type="hidden" name="session_id" value="{{$sessionId}}"/>
                                <button type="submit" class="input-btn" id="payment-button">
                                    {!! __('lang.CHECKOUT') !!}
                                </button>
                                <button type="submit" class="input-btn" id="payment-credit-card">
                                    <p id="payment-credit-card-text">{!! __('CHECKOUT WITH CREDIT CARD') !!}</p>
                                </button>
                                <div class="row mt-4 justify-content-center">
                                    <img class="align-self-center" src="{{asset('images/cclogos.png')}}" />
                                </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.0.1/jquery.payment.min.js" integrity="sha384-AKEcmGRBjvgYcxHEMhGo2Dp+07CoD9RPqK1zAZ9kNkOt6ayBTDyrqJSDC0zcMUZW" crossorigin="anonymous"></script>
    <script type="text/javascript">

        function modal() {
           $('#loading').modal('show');
        }


        function validateRequiredFields() {
            if (($('#monthlyFee:checked').length > 0) && ($('#monthlyFeex:checked').length > 0)) {
                return true;
            }

            return false;
        }

        $(document).ready(function () {

            $("#payment-button").hide();
            $('.cc-number').formatCardNumber();
            $('.cc-expires').formatCardExpiry();
            $('.cc-cvc').formatCardCVC();

            $('#payment-form').submit(function () {
                $('#payment-button').html('<i class="fa fa-spinner fa-spin"></i> Processing').prop('disabled', true);
                modal();
            });

            $('#apply-coupon').click(function () {
                var button = $(this);
                button.html('Apply').prop('disabled', true);
                $("#coupon-code-result").text("");
                var _token = $('meta[name="csrf-token"]').attr('content');
                button.html('<i class="fa fa-spinner fa-spin"></i> Wait').prop('disabled', true);
                $.ajax({
                    url: '/payment/apply-coupon',
                    type: 'POST',
                    data: '_token=' + _token + '&coupon_code=' + $('#coupon-code').val(),
                    success: function (data) {

                        if (data.error == 0) {
                            $("#productCheckOut").html(data.v);
                            if (data.valid) {
                                if (data.total == 0) {
                                    $(".payment_type_error").text("");
                                    //$("#paymentMethod").hide();
                                    $("#payment-credit-card").hide();
                                    $("#payment-button").show();
                                }else{
                                    // $("#paymentMethod").show();
                                    $("#payment-button").hide();
                                    $("#payment-credit-card").show();
                                }
                            }else{
                                $("#paymentMethod").show();
                            }
                        } else {
                            $("#coupon-code").val("");
                            $("#productCheckOut").html(data.v);
                            $("#coupon-code-result").text(data.msg);
                            $("#paymentMethod").show();
                        }
                        button.html('Apply').prop('disabled', false);
                    }
                });
            });

            $('#payment-credit-card').click(function() {
                if (validateRequiredFields()) {
                    $("#payment-credit-card-text").text("PLEASE WAIT...");
                    modal();
                }
            });
        })
    </script>
@endsection
