@extends($activeTemplate . 'layouts.app')
@section('panel')
    @include($activeTemplate . 'partials.header')
    @include($activeTemplate . 'partials.breadcrumb')

    <section class="py-60">
        <div class="container">
            @yield('content')
        </div>
    </section>

    @if (!Request::routeIs('user.register') && !Request::routeIs('user.login'))
        @include($activeTemplate . 'partials.footer')
    @endif
@endsection
