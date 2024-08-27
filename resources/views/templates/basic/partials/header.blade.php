@php
    $cryptos = App\Models\CryptoCurrency::active()->orderBy('name')->get();
    $pages = App\Models\Page::where('tempname', $activeTemplate)
        ->where('is_default', Status::NO)
        ->get();
@endphp

<header class="header">
    <div class="header__bottom">
        <div class="container">
            <nav class="navbar navbar-expand-xl p-0 align-items-center">
                <a class="site-logo site-title" href="{{ route('home') }}"><img alt="@lang('logo')" src="{{ siteLogo() }}"></a>
                <button aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler ms-auto shadow-none" data-bs-target="#navbarSupportedContent" data-bs-toggle="collapse" type="button">
                    <span class="menu-toggle"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav main-menu m-auto">
                        <li> <a href="{{ route('home') }}">@lang('Home')</a></li>

                        <li class="menu_has_children"><a href="javascript:void(0)">@lang('Buy')</a>
                            <ul class="sub-menu">
                                @foreach ($cryptos as $crypto)
                                    <li><a href="{{ route('advertisement.all', ['buy', $crypto->code, 'all']) }}">{{ $crypto->code }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="menu_has_children"><a href="javascript:void(0)">@lang('Sell')</a>
                            <ul class="sub-menu">
                                @foreach ($cryptos as $crypto)
                                    <li><a href="{{ route('advertisement.all', ['sell', $crypto->code, 'all']) }}">{{ $crypto->code }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                        @auth
                            <li><a href="{{ route('user.advertisement.index') }}">@lang('Advertisements')</a></li>
                            <li class="menu_has_children"><a href="javascript:void(0)">@lang('Trades')</a>
                                <ul class="sub-menu">
                                    <li><a href="{{ route('user.trade.request.running') }}">@lang('Running')</a></li>
                                    <li><a href="{{ route('user.trade.request.completed') }}">@lang('Completed')</a></li>
                                </ul>
                            </li>

                            <li><a href="{{ route('user.wallets') }}">@lang('Wallets')</a></li>
                            <li><a href="{{ route('user.transactions') }}">@lang('Transactions')</a></li>
                        @endauth

                        @foreach ($pages as $k => $data)
                            <li><a class="nav-link" href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a></li>
                        @endforeach

                        <li><a href="{{ route('contact') }}">@lang('Contact')</a></li>

                        @auth
                            <li class="menu_has_children"><a href="javascript:void(0)">@lang('More')</a>
                                <ul class="sub-menu">
                                    <li><a href="{{ route('ticket.index') }}">@lang('Support')</a></li>
                                    <li><a href="{{ route('user.deposit.history') }}">@lang('Deposits')</a></li>
                                    <li><a href="{{ route('user.withdraw.history') }}">@lang('Withdrawals')</a></li>
                                    <li><a href="{{ route('user.referral.commissions.trade') }}">@lang('Referral')</a>
                                    </li>
                                    <li><a href="{{ route('user.change.password') }}">@lang('Password')</a></li>
                                    <li><a href="{{ route('user.profile.setting') }}">@lang('Profile Setting')</a></li>
                                    <li><a href="{{ route('user.twofactor') }}">@lang('2FA Security')</a></li>
                                    <li><a href="{{ route('user.logout') }}">@lang('Logout')</a></li>
                                </ul>
                            </li>
                        @endauth
                    </ul>

                    <div class="nav-right">

                        @if (gs('multi_language'))
                            @php
                                $language = App\Models\Language::all();
                                $currentLang = session('lang') ? $language->where('code', session('lang'))->first() : $language->where('is_default', Status::YES)->first();
                            @endphp
                            <div class="language dropdown">
                                <button class="language-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="language-content">
                                        <div class="language_flag">
                                            <img src="{{ getImage(getFilePath('language') . '/' . $currentLang->image, getFileSize('language')) }}" alt="@lang('image')">
                                        </div>
                                        <p class="language_text_select">{{ __($currentLang->name) }}</p>
                                    </div>
                                    <span class="collapse-icon"><i class="las la-angle-down"></i></span>
                                </button>
                                <div class="dropdown-menu langList_dropdow py-2" style="">
                                    <ul class="langList">
                                        @foreach ($language as $item)
                                            @if (session('lang') != $item->code)
                                                <li class="language-list languageList" data-code="{{ $item->code }}">
                                                    <div class="language_flag">
                                                        <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}" alt="@lang('image')">
                                                    </div>
                                                    <p class="language_text">{{ __($item->name) }}</p>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        @endif

                        <ul class="account-menu ms-3">
                            @auth
                                <li>
                                    <a class="btn btn--base btn-sm" href="{{ route('user.home') }}">@lang('Dashboard')</a>
                                </li>
                            @else
                                <li class="icon"><i class="las la-user"></i>
                                    <ul class="account-submenu">
                                        <li><a href="{{ route('user.login') }}">@lang('Login')</a></li>
                                        <li><a href="{{ route('user.register') }}">@lang('Registration')</a></li>
                                    </ul>
                                </li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div><!-- header__bottom end -->
</header>


@push('script')
    <script>
        (function($) {
            "use strict";

            const $mainlangList = $(".langList");
            const $langBtn = $(".language-content");
            const $langListItem = $mainlangList.children();

            $langListItem.each(function() {
                const $innerItem = $(this);
                const $languageText = $innerItem.find(".language_text");
                const $languageFlag = $innerItem.find(".language_flag");

                $innerItem.on("click", function(e) {
                    $langBtn.find(".language_text_select").text($languageText.text());
                    $langBtn.find(".language_flag").html($languageFlag.html());
                });
            });

        })(jQuery);
    </script>
@endpush
