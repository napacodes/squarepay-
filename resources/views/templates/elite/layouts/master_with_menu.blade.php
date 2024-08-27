@extends($activeTemplate . 'layouts.app')
@section('panel')
    @include($activeTemplate . 'partials.header')
    @include($activeTemplate . 'partials.user_banner')

    @include($activeTemplate . 'partials.breadcrumb')
    <section class="account-setting-body pt-60 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    @include($activeTemplate . 'partials.user_sidebar')
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="dashboard-body">
                        <button class="sidebar-toggle-btn d-lg-none d-inline-flex align-items-center justify-content-center" type="button"><i class="las la-sliders-h"></i></button>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (!Request::routeIs('user.register') && !Request::routeIs('user.login'))
        @include($activeTemplate . 'partials.footer')
    @endif
@endsection
