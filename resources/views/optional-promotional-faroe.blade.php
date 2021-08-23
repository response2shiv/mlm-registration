@extends('template.layout-op')

@section('title')
    ncrease - {!! __('lang.OPTIONAL_PROMOTIONAL_ENROLLMENT_PACKS_TITLE') !!}
@endsection
@section('back-link')
   {{ url('/standby') }}
@endsection
@section('custom-css')
    <style type="text/css">
        body {
            background-color: white !important;
        }
    </style>
@endsection
@section('content')
    <div class="select-country">
        <div class="margin">

            <p class="leading-text text-center">
                {!! __('lang.STANDBY_IS_THE_ENROLLMENT_KIT_THAT_EVERYONE_MUST_HAVE') !!}
            </p>

        </div>
        @php
             $standby_price =  number_format($products[1]['price'],2);
             $standby_price = explode(".",$standby_price);

             $basic_price = number_format($products[2]['price'],2);
             $basic_price = explode(".",$basic_price);

             $visionary_price = number_format($products[3]['price'],2);
             $visionary_price = explode(".",$visionary_price);

             $fx_price = number_format($products[10]['price'],2);
             $fx_price = explode(".",$fx_price);
        @endphp

        <div id="fdw-pricing-table">
            <div class="left-part">
                {{--  <h5 class="title-s">{!! __('lang.STANDARD_ENROLLMENT_KIT') !!}</h5>  --}}
                <div class="plan plan0">
                        <div class="header">ncrease ISBO</div>
                        <div class="price">
                            <span style="font-size: 18px;">$</span>{{$standby_price[0]}}.<sup>{{$standby_price[1]}}</sup> <sup style="font-size: 8px;">per month</sup>
                            
                        </div>
                        {{--  @if($country != "US")
                            <div class="conversion_price">{{$products[1]['conversion']['display_amount']}}</div>
                        @endif  --}}
                    <ul>
                        <li>- 
                            CRM
                        </li>
                        <li>-
                            Advanced Communication Tools
                        </li>
                        <li>-
                            Advanced Business Management <br> and Tracking Tools
                        </li>
                        <li>-
                            Understanding your Business <br> Compliance Education and Training
                        </li>
                        <li>
                            - Emotional Intelligence Education <br> and Certification
                        </li>
                    </ul>
                        {{--  <a class="btn-a" href="{{url('sponsor-information')}}">
                            SELECT ISBO
                        </a>  --}}
                </div>
            </div>
            <div class="mid-part">
                <h4 class="title-s">PLUS</h4>
            </div>
            <div class="right-part">
                <ul>
                <li>
                        <div class="plan plan4">
                                <div class="header">FX Pack</div>
                                <div class="price"><span style="font-size: 18px;">$</span>{{$fx_price[0]}}.<sup>{{$fx_price[1]}} </sup></div>
                                        {{-- @if($country != "US")
                                            <div class="conversion_price">{{$products[16]['conversion']['display_amount']}}</div>
                                        @endif --}}
                            <ul class="text-center">
                                <li>
                                    - HFX
                                </li>
                                <li>
                                    - Live Trading Sessions
                                </li>
                                <li>
                                    -Trade signals
                                </li>
                                <li>
                                    - Trade Ideas
                                </li>
                                <li>
                                    - Wave Analyzer
                                </li>
                                <li>
                                    - And much more…!
                                </li>
                            </ul>
                            <a class="btn-a" href="{{url('optional-promotional/10')}}">SELECT FX</a>
                        </div>
                     </li>
                    <li>
                        <div class="plan plan1">
                                <div class="header">BASIC PACK</div>
                                <div class="price"><span style="font-size: 18px;">$</span>{{$basic_price[0]}}.<sup>{{$basic_price[1]}} </sup></div>
                                {{--  @if($country != "US")
                                    <div class="conversion_price">{{$products[2]['conversion']['display_amount']}}</div>
                                @endif  --}}
                            <ul class="text-center">
                                <li>
                                    - 1 Selfles Multi-Solution Gel
                                </li>
                                <li>
                                    - 1 Selfles Advanced Hydrating Cream
                                </li>
                            </ul>
                            <a class="btn-a" href="{{url('optional-promotional/2')}}"> SELECT BASIC
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="plan plan2">
                                <div class="header">Visionary Pack</div>
                                <div class="price"><span style="font-size: 18px;">$</span>{{$visionary_price[0]}}.<sup>{{$visionary_price[1]}} </sup></div>
                                {{--  @if($country != "US")
                                    <div class="conversion_price">{{$products[3]['conversion']['display_amount']}}</div>
                                @endif  --}}
                            <ul class="text-center">
                                <li>
                                    - 1 Selfles Multi-Solution Gel
                                </li>
                                <li>
                                    - 1 Selfles Advanced Hydrating Cream
                                </li>
                                <li>
                                    - Exclusive Emotional Intelligence and Personal Development seminars
                                </li>
                                <li>
                                    - Monthly Visionary Personal and Professional Development Zooms
                                </li>
                                <li>
                                    - Discount Admission for one Year to all Corporate events
                                </li>
                                <li>
                                    - Preferred Seating at corporate events
                                </li>
                                <li>
                                    - Exclusive Visionary Party at corporate events
                                </li>
                                <li>
                                    - Exclusive Visionary Pre-release product notification
                                </li>
                                <li>
                                    - Eligible to join a Visionary Expedition
                                </li>
                                <li>
                                    <strong>- Includes the FX Pack</strong>
                                </li>
                            </ul>
                            <a class="btn-a" href="{{url('optional-promotional/3')}}"> SELECT VISIONARY
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- select-country -->

    <footer id="footer">
        <div class="margin">
            <p>{!! __('lang.2019_ONYX_CORE_ALL_RIGHTS_RESERVED') !!}</p>
            <div class="footer-menu">
                <ul>
                    <li><a href="#">{!! __('lang.POLICIES_PROCEDURES') !!}</a></li>
                    <li><a href="#">{!! __('lang.PRIVACY_POLICY') !!}</a></li>
                    <li><a href="#">{!! __('lang.SUPPORT') !!}</a></li>
                </ul>
            </div>
        </div>
    </footer>
@endsection
@section('custom-js')
    <script src="{{asset('plugins/assets-optional-payment')}}/js/script.js"></script>
    <script>
        $(document).ready(function () {
            $('#checkoutStandbyOnly').click(function () {
                var _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '/payment/standby',
                    type: 'POST',
                    data: '_token=' + _token + '&coupon_code=' + $('#coupon-code').val(),
                    success: function (data) {
                        window.location.replace(data['url']);
                    }
                });
            });
        })
    </script>
@endsection
