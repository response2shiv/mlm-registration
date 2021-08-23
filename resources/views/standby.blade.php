@extends('layout')
@section('title')
    ncrease - Standby
@endsection
@section('back-link')
   {{ url('/vitals') }}
@endsection
@section('content')
    <section class="enrollment-content-wrapper">
        <div class="wrapper content-wrapper">
            <div class="row your-sponsor-info">
                <div class="col-12 your-sponsor-title">{!! __('lang.YOUR_SPONSOR') !!}</div>
                <div class="col-12 your-sponsor-name">{{ session('sponsor_name') }}</div>
                <div class="col-12">{{session('sponsor_city')}}, {{session('sponsor_state')}}</div>
                <div class="col-12">{{session('mobile_number')}}</div>
                <div class="col-12">{{session('sponsor_email')}}</div>
            </div>
            <div class="enrollment-title">{!! __('lang.STANDBY_IS_A_REQUIRED_PACKAGE_THAT') !!}</div>
            <div class="required-packages-wrap">
                <div class="required-package">
                    <div class="required-package-head">
                        <span>{!!__('lang.STANDBY')!!}</span>
                    </div>
                    <div class="required-package-body">
                        <div class="required-package-list">
                            <ul>
                                <li>
                                    <i class="fa fa-check icon-check" aria-hidden="true"></i>1 büümerang Customer Site
                                </li>
                                <li>
                                    <i class="fa fa-check icon-check" aria-hidden="true"></i>5 büümerangs to Send
                                </li>
                                <li>
                                    <i class="fa fa-check icon-check" aria-hidden="true"></i>25% Referral Commissions
                                </li>
                                <li>
                                    <i class="fa fa-check icon-check" aria-hidden="true"></i>iDecide Interactive Presentation
                                </li>
                                <li>
                                    <i class="fa fa-check icon-check" aria-hidden="true"></i>Access to your TSA Back Office
                                </li>
                            </ul>
                        </div>
                        <div class="required-package-footer">
                            <div class="package-price">$49<sub>.95<sub style="font-size: 20px;">*</sub></sub></div>
                            <div class="package-button-wrapper">
                                <a href="{{url('sponsor-information')}}" role="button" class="package-button">{!!__('lang.ADD_TO_CART')!!}</a>
                            </div>
                            <div class="package-hint">
                                *{!! __('lang.MONTHLY_SUBSCRIPTION_FEE_WILL_AUTOMATICALLY') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
