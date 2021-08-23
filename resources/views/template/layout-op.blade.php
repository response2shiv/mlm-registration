<?php
$t = 1;
?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token()}}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/png" href="{{asset("images/favicon.png")}}"/>
        <title>@yield('title')</title>

        <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300,400,500,600,700,800" rel="stylesheet">

        <link href="{{asset('plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css"/>
        <link href="{{asset('plugins/assets-optional-payment')}}/css/style.css" rel="stylesheet">

        @yield('custom-css')
    </head>
    <body>
        <div id="burl" style="display:none">{{url('/')}}</div>
        @include('template.nav')

        @yield('content')
    </body>
    <script src="{{asset('plugins/assets-optional-payment')}}/js/jquery.min.js"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{asset('plugins/select2/dist/js/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js" integrity="sha384-B1miHxuCmAMGc0405Cm/zSXCc38EP5hNOy2bblqF6yiX01Svinm1mWMwJDdNDBGr" crossorigin="anonymous"></script>
    <script src="{{ asset('js/jquery.dd.js') }}" type="text/javascript"></script>
    @yield('custom-js')
    <script src="{{asset('/js/myjs.js?'.$t)}}" type="text/javascript"></script>
    <script>
jQuery(document).ready(function () {
    js_myjs.init();
});
    </script>
    {{--Global site tag (gtag.js) - Google Analytics--}}
    <script src="https://www.googletagmanager.com/gtag/js?id=UA-137362273-1"
            integrity="sha384-M0PmqEqDVvgMrJO8JgSu65AyeEmKI96/uqRiAKDwb9n/aZzO4f21F1E40xvWPTha"
    crossorigin="anonymous"></script>
    <script>
window.dataLayer = window.dataLayer || [];

function gtag() {
    dataLayer.push(arguments);
}

gtag('js', new Date());
gtag('config', 'UA-137362273-1');
    </script>
</html>
