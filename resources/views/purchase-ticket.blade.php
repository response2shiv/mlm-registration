@extends('layout')
@section('title')
    ncrease - {!! __('lang.PURCHASE_YOUR_TICKETS') !!}
@endsection

@section('content')
    <section class="enrollment-content-wrapper">
        <div class="wrapper content-wrapper">
            <div class="row thank-you-page-wrap upsell-page">
                <img class="logo-background-img" src="<?php echo e(asset('images/logo-big.png')); ?>">
                <hr>
                <div style="color: #00b6eb" class="col-12 thank-you-text">
                    <div>{!! __('lang.PURCHASE_YOUR_TICKETS_FOR_VISION_2020') !!} </a>
                    </div>
                </div>
                <hr>
                <div class="col-12 account-info-title xce">
                    <img class="mx-auto d-block" src="<?php echo e(asset('images/vision2020.png')); ?>">

                </div>
                <div class="col-12">
                    <div class="row account-info-row">
                        <div class="col-sm-12 "><strong>Ticket Price:&nbsp;&nbsp; <span
                                    class="ot-offer">${{$product->price}}</span></strong>
                        </div>
                    </div>
                    <div class="row account-info-row">
                        <div class="col-sm-12" style="font-size: 10px; color: #666667"><span>{!! __('lang.ALL_TICKET_SALES_NON_REFUNDABLE') !!}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-12 form-field text-center mt-3">
                    <div class="form-field-button">
                        @if(session()->get('ticket_purchase') == false )
                            <form method="post" action="{{url('payment/tickets')}}">
                                @csrf
                                <input type="hidden" name="ticket_purchase" value="yes">
                                <button type="submit" class="input-btn">
                                    {!! __('lang.YES_SIGN_ME_UP') !!}
                                    <i id="loading" class="fa fa-spinner fa-spin" style="display: none"></i>
                                </button>
                            </form>
                        @elseif(session()->get('ticket_purchase')==true)
                            <a href="{{url('/payment')}}" class="input-btn">
                                {!! __('lang.YES_SIGN_ME_UP') !!}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-12 mt-3 mb-4 pl-0 pr-0">
                    <div class="video-wrap">
                        <iframe src="https://player.vimeo.com/video/396507870" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-12 col-md-12 form-field text-center mt-3">
                    <div class="form-field-button">
                        <a href="#" id="purchaseButton"
                           style="color: #00b6eb" class="">
                            No thanks, I'll purchase my ticket later
                            <i id="loading" class="fa fa-spinner fa-spin" style="display: none"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#purchaseButton').click(function () {
                var button = $(this);
                var _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '/payment/tickets',
                    type: 'POST',
                    data: '_token=' + _token + '&ticket_purchase=false',
                    success: function (data) {
                         location.replace(data.url)
                    }
                });
            });
        })
    </script>
@endsection
