@extends('layout')
@section('title')
    ncrease - {!! __('lang.THANK_YOU_TITLE') !!}
@endsection

@section('content')
    <section class="enrollment-content-wrapper">
        <div class="wrapper content-wrapper">
            <div class="row thank-you-page-wrap">
                <hr>
                <div class="col-12 thank-you-text">
                    <div class="mb-4">{!! __('lang.WELCOME_TO_THE_IBUUMERANG_FAMILY') !!}</div>
                    {{--TODO: Add correct link--}}
                </div>
                <hr>
                <div class="col-12 account-info-title">
                    {!! __('lang.IMPORTANT_INFORMATION_FOR_YOUR_ACCOUNT') !!}:
                </div>

                @php
                @endphp
                <div class="col-12">
                    <div class="row account-info-row">
                        <div class="col-sm-6 col-auto account-info-left">{!! __('lang.USER_ID') !!}:</div>
                        <div class="col-sm-6 col-auto account-info-right">{{$tsa}}</div>
                    </div>
                    <div class="row account-info-row">
                        <div class="col-sm-6 col-auto account-info-left">{!! __('lang.USERNAME') !!}:</div>
                        <div class="col-sm-6 col-auto account-info-right">{{$username}}</div>
                    </div>
                    <div class="row account-info-row">
                        <div class="col-sm-6 col-auto account-info-left">{!! __('lang.WEBSITE') !!}:</div>
                        <div class="col-sm-6 col-auto account-info-right">{{$username}}.ncrease.com</div>
                    </div>
                    <div class="row account-info-row">
                        <div class="col-sm-6 col-auto account-info-left">{!! __('lang.ORDER_STATUS') !!}:</div>
                        <div id="order-status-value" class="col-sm-6 col-auto account-info-right">{{ $orderStatus }}</div>
                    </div>
                </div>
                {{--<div class="col-12 mt-3 mb-4 pl-0 pr-0">--}}
                {{--<div class="video-wrap">--}}
                {{--<video width="100%" controls>--}}
                {{--<source src="https://s3.amazonaws.com/optomiz/14617180-hd.mp4" type="video/mp4">--}}
                {{--{!! __('lang.YOUR_BROWSER_DOES_NOT_SUPPORT_HTML5_VIDEO') !!}--}}
                {{--</video>--}}
                {{--</div>--}}
                {{--</div>--}}
                @php
                    $isCancelled = in_array($orderStatus, $cancelledStatuses);
                    $isProcessed = !in_array($orderStatus, $pendingStatuses) && !in_array($orderStatus, $cancelledStatuses)
                @endphp
                <div class="col-12 col-md-12 mt-3">
                    <div id="order-cancelled" style="@if(!$isCancelled) display:none; @endif">
                        <p>You order has been cancelled.</p>
                    </div>
                    <div id='order-under-process' style="
                    @if($isProcessed || $isCancelled) display:none @endif">
                        <p>Your order is processing. Your status will be updated in:</p>
                        <div class='pending-timer-container my-2'>
                            <span id='status-refresh-countdown'></span>
                        </div>

                        <p>Don't want to wait?</p>
                    </div>
                    <div id='order-completed' style='@if(!$isProcessed || $isCancelled) display:none @endif'>
                        <p> {!! __('lang.ORDER_COMPLETED_GREETINGS') !!} </p>
                    </div>
                </div>
                @if(!$isCancelled)
                {{-- <div class="col-12 col-md-12 form-field text-center mt-3">
                    <div class="form-field-button">
                        <a href="https://www.ncrease.com" id="payment-button" class="input-btn">
                            {!! __('lang.GO_TO_MY_BACK_OFFICE') !!}
                            <i id="loading" class="fa fa-spinner fa-spin" style="display: none"></i>
                        </a>
                    </div>
                </div> --}}
                @endif
            </div>
        </div>
    </section>
@endsection
@section('custom-js')
<script>
    let seconds;
    let initialSeconds;
    let intervalId;
    let pendingStatuses = [
        'UNPAID',
        'PARTIAL',
        'PROCESSING',
        'PENDING'
    ];
    let cancelledStatuses = [
        'ERROR',
        'CANCELLED',
        'EXPIRED',
        'DECLINED'
    ];
    let timer = function (seconds, tick, completed) {
        initialSeconds = seconds;
        intervalId = setInterval(function () {
            seconds -= 1;
            tick(seconds);
            if (seconds <= 0) {
                completed(initialSeconds).then((data) => {
                    if(data.cancelled)
                        clearInterval(intervalId);
                    if(!data.isProcessed){
                        seconds = data.initialSeconds;
                    }
                    else{
                        clearInterval(intervalId);
                    }
                });
            }
        }, 1000)
    }
timer(30, (seconds) => {
    seconds = "00:" + (seconds <= 0? "00" : seconds < 10 ? "0" + seconds : seconds);
    document.getElementById('status-refresh-countdown').innerHTML = seconds;
}, (initialSeconds) => {
    {{--TODO: Add Ajax API call--}}
    return new Promise((resolve, reject) => {
        const orderHash = '{{ $orderHash }}';
        //Mock
        // resolve({ isProcessed: true, cancelled: false, initialSeconds});
        location.reload();

        $.ajax({
            url: "/payment/status?orderhash=" + orderHash,
            type: "GET",
            cache: false,
            processData: false,
            success: function (response) {
                const status = response.status;
                $('#order-status-value').html(status);
                if (pendingStatuses.indexOf(status) !== -1) {
                    // seconds = initialSeconds;
                    $('#order-under-process').show();
                    $('#order-completed').hide();
                    $('#order-cancelled').hide();
                    resolve({cancelled: false, isProcessed: false, initialSeconds});
                } else if (cancelledStatuses.indexOf(status) !== -1) {
                    clearInterval(intervalId);
                    $('#order-under-process').hide();
                    $('#order-completed').hide();
                    $('#order-cancelled').show();
                    resolve({cancelled: true, isProcessed: true, initialSeconds});
                } else if (status == 'PAID') {
                    clearInterval(intervalId);
                    $('#order-under-process').hide();
                    $('#order-completed').show();
                    $('#order-cancelled').hide();
                    resolve({cancelled: false, isProcessed: true, initialSeconds});
                }
         },
        error: function(err){
            resolve({cancelled: true, isProcessed: false, initialSeconds});
        }
        });
    })
});
</script>
@endsection

<style>
    #status-refresh-countdown{
        font-size: 28px;
        color: #1c93e8;
        font-style: italic;
    }
</style>
