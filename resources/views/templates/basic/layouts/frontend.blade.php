<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>

    @include('partials.seo')

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php?color1=' . gs('base_color')) }}">

    @stack('style-lib')
    @stack('style')

</head>
@php echo loadExtension('google-analytics') @endphp

<body>
    <div class="preloader">
        <div class="preloader-container">
            <span class="animated-preloader"></span>
        </div>
    </div>

    <div class="page-wrapper">
        @if (!Request::routeIs('user.register') && !Request::routeIs('user.login') && !Request::routeIs('maintenance'))
            @include($activeTemplate . 'partials.header')
        @endif

        @if (!Request::routeIs('home') && !Request::routeIs('user.register') && !Request::routeIs('user.login') && !Request::routeIs('maintenance'))
            @include($activeTemplate . 'partials.breadcrumb')
        @endif

        @yield('content')

        @if (!Request::routeIs('user.register') && !Request::routeIs('user.login') && !Request::routeIs('maintenance'))
            @include($activeTemplate . 'partials.footer')
        @endif
    </div>

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp

    @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="mt-4 cookies-card__content">{{ $cookie->data_values->short_desc }} <a href="{{ route('cookie.policy') }}" class="text--base" target="_blank">@lang('learn more')</a></p>
            <div class="cookies-card__btn mt-4">
                <a href="javascript:void(0)" class="btn btn--base w-100 policy">@lang('Allow')</a>
            </div>
        </div>
    @endif

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/slick.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')

    @php echo loadExtension('tawk-chat') @endphp
    
    @include('partials.notify')

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

    <script>
        (function($) {
            "use strict";
            $(".languageList").on("click", function() {
                window.location.href = "{{ url('/') }}/change/" + $(this).data('code');
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            $.each($('input:not([type=checkbox]):not([type=hidden]), select, textarea'), function (i, element) {
                if (element.hasAttribute('required')) {
                    $(element).closest('.form-group').find('label').first().addClass('required');
                }
            });

            $('.select2').select2();

        })(jQuery);
    </script>

</body>

</html>
