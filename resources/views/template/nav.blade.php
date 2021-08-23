<header>
    <div class="wrapper">
        <div class="back-btn-wrapper" style="visibility: hidden">
            <div class="back-btn">
                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
                <a href="@yield('back-link')">BACK</a>
            </div>
        </div>

        <div class="logo mb-4">
            <a href="/">
                <img src="{{asset('images/logo.png')}}" alt="" style="magin-top: 20px;">
            </a>
        </div>
        <div class="btn-group" id="navbar2">
            {{--<span class="dropdown-label">Select Language</span>--}}
            {{--<a class="btn dropdown-toggle" data-toggle="dropdown" href="#" style="margin-top: auto;">--}}
                {{--<i class="icon-user"></i>--}}
                {{--{!! __('lang.LANGUAGE') !!}--}}
                {{--<span class="caret"></span>--}}
            {{--</a>--}}
            {{--<ul class="dropdown-menu">--}}
                {{--<li><a class="dropdown-item" href="{{url('/lng/en')}}">English</a></li>--}}
                {{--<li><a class="dropdown-item" href="{{url('/lng/fr')}}">French</a></li>--}}
                {{--<li><a class="dropdown-item" href="{{url('/lng/de')}}">German</a></li>--}}
                {{--<li><a class="dropdown-item" href="{{url('/lng/it')}}">Italian</a></li>--}}
                {{--<li><a class="dropdown-item" href="{{url('/lng/ko')}}">Korean</a></li>--}}
                {{--<li><a class="dropdown-item" href="{{url('/lng/pt')}}">Portuguese</a></li>--}}
                {{--<li><a class="dropdown-item" href="{{url('/lng/es')}}">Spanish</a></li>--}}
                {{--<li><a class="dropdown-item" href="{{url('/lng/tr')}}">Turkish</a></li>--}}
            {{--</ul>--}}
        </div>
    </div>
</header>
