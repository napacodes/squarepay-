@php
    $socialIconElements = getContent('social_icon.element');
    $policyElements     = getContent('policy_pages.element');
    $subscribeContent   = getContent('subscribe.content', true);
    $cryptos = App\Models\CryptoCurrency::active()
        ->orderBy('name')
        ->take(7)
        ->get();
@endphp


<footer class="footer-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="footer-logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}"></a>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="row footer-item-wrapper">
                    <div class="col-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title">@lang('Quick Link')</h5>
                            <ul class="footer-menu">
                                <li class="footer-menu__item">
                                    @auth
                                        <a href="{{ route('user.home') }}" class="footer-menu__link">@lang('Dashboard')</a>
                                    @else
                                        <a href="{{ route('home') }}" class="footer-menu__link">@lang('Home')</a>
                                    @endauth
                                </li>
                                <li class="footer-menu__item">
                                    <a href="{{ route('pages', 'about') }}" class="footer-menu__link">
                                        @lang('About')
                                    </a>
                                </li>
                                <li class="footer-menu__item">
                                    <a href="{{ route('user.trade.request.running') }}" class="footer-menu__link">
                                        @lang('Trade')
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="footer-item two">
                            <h5 class="footer-item__title">@lang('Legal')</h5>
                            <ul class="footer-menu">
                                @foreach ($policyElements as $policy)
                                    <li class="footer-menu__item">
                                        <a class="footer-menu__link" href="{{ route('policy.pages', $policy->slug) }}">
                                            {{ __($policy->data_values->title) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="row footer-item-wrapper">
                    <div class="col-6">
                        <div class="footer-item">
                            <h5 class="footer-item__title">@lang('Buy Asset')</h5>
                            <ul class="footer-menu">
                                @foreach ($cryptos as $crypto)
                                    <li class="footer-menu__item">
                                        <a class="footer-menu__link" href="{{ route('advertisement.all', ['buy', $crypto->code, 'all']) }}">
                                            {{ $crypto->code }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="footer-item two">
                            <h5 class="footer-item__title">@lang('Sell Asset')</h5>
                            <ul class="footer-menu">
                                @foreach ($cryptos as $crypto)
                                    <li class="footer-menu__item">
                                        <a class="footer-menu__link" href="{{ route('advertisement.all', ['sell', $crypto->code, 'all']) }}">
                                            {{ $crypto->code }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-5 offset-xxl-1 col-xl-6 col-lg-7 col-md-9">
                <div class="footer-item newsletter">
                    <h5 class="footer-item__title">@lang('Subscribe')</h5>
                    <p class="footer-item__desc">
                        {{ __(@$subscribeContent->data_values->heading) }}
                    </p>
                    <form method="get" class="newsletter-form" id="subscribe-form">
                        <div class="form-group input-group gap-3">
                            <input type="email" name="email" class="form-control form--control" placeholder="@lang('Enter your email')">
                            <button type="submit" class="btn btn-outline--base">
                                {{ __(@$subscribeContent->data_values->button_text) }}
                           </button>
                        </div>
                    </form>
                    <p class="footer-item__desc sm">
                        {{ __(@$subscribeContent->data_values->subscribing_message) }}
                    </p>
                    <h5 class="footer-item__title mb-0">@lang('Our Community')</h5>
                    <ul class="social-list">
                        @foreach ($socialIconElements as $social)
                            <li class="social-list__item">
                                <a class="social-list__link flex-center" href="{{ @$social->data_values->url }}" target="_blank">
                                    @php echo @$social->data_values->social_icon @endphp
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="bottom-footer py-3 mt-60">
            <p class="bottom-footer__text">
                &copy; {{ date('Y') }}
                <a href="{{ route('home') }}" class="text--base">
                    {{ __(gs('site_name')) }}
                </a>
                @lang('All Rights Reserved')
            </p>
        </div>
    </div>
</footer>

@push('script')
    <script>
        "use strict";
        (function($) {
            $('#subscribe-form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData($(this)[0]);
                let $this = $(this);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: "{{ route('subscribe') }}",
                    method: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $this.find('button[type=submit]').html(`
                        <span class="right-sidebar__button-icon">
                            <i class="las la-spinner la-spin"></i> {{ __(@$subscribeContent->data_values->button_text) }}
                        </span>`).attr('disabled', true);
                    },
                    complete: function(e) {
                        setTimeout(() => {
                            $this.find('button[type=submit]').html(
                                `{{ __(@$subscribeContent->data_values->button_text) }}`
                                ).attr('disabled', false);
                        }, 500);
                    },
                    success: function(resp) {
                        setTimeout(() => {
                            if (resp.success) {
                                notify('success', resp.message);
                                $($this).trigger('reset');
                            } else {
                                notify('error', resp.message || resp.error);
                            }
                        }, 500);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
