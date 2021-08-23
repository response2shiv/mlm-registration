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
            <div class="col-sm-7 col-7">
                <div class="order-pack-name" style="text-transform: uppercase"> {{$standby->productname}}</div>
                <div
                    class="order-pack-notes">{!! __('lang.REQUIRED_ENROLLMENT_PACK') !!}</div>
            </div>
            <div class="col-sm-5 col-5 order-pack-price">
                ${{number_format($standby->price,2)}}
                @if($country_conversion != "USD")
                    / {{$products_conversion[1]['conversion']['display_amount']}}
                @endif
            </div>
        </div>
    @endif
    @if($product)
        <div class="row order-pack-non-color-wrap">
            <div class="col-sm-7 col-7">
                <div class="order-pack-name" style="text-transform: uppercase">{{$product->productname}}</div>
                <div class="order-pack-notes">{!! __('lang.OPTIONAL_PROMOTIONAL_ENROLLMENT_PACK') !!}</div>
            </div>
            <div class="col-sm-5 col-5 order-pack-price">
                ${{number_format($product->price,2)}}
                @if($country_conversion != "USD")
                    / {{$products_conversion[$product->id]['conversion']['display_amount']}}
                @endif
            </div>
            <div class="change-pack-btn">
                <a href="{{ url('optional-promotional') }}">{!! __('lang.CHANGE_PACK') !!}</a>
            </div>
        </div>
    @endif
    @if(!empty($ticket_product))
        <div class="row order-pack-non-color-wrap">
            <div class="col-sm-7 col-7">
                <div class="order-pack-name" style="text-transform: uppercase">{{$ticket_product->productname}}</div>
                <div class="order-pack-notes">{!! __('lang.SPECIAL_ENROLLMENT_PRICE') !!}</div>
            </div>
            <div class="col-sm-5 col-5 order-pack-price">
                ${{number_format($ticket_product->price,2)}}
                @if($country_conversion != "USD")
                    / {{$ticket_display}}
                @endif
            </div>
        </div>
    @endif
</div>
<hr>
<div class="row mt-3">
    <div class="col-sm-9 col-6" style="font-size: 25px">
        <div>{!! __('lang.TOTAL') !!}</div>
    </div>
    <div class="col-sm-3 col-6 order-pack-price" id="" style="font-size: 25px">
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
