<header class="header two" id="header">
    <div class="container">
        <div class="account-header-inner d-flex align-items-center justify-content-between">
            <a class="navbar-brand logo" href="{{ route('home') }}"><img alt="" src="{{ siteLogo() }}"></a>
            <div class="header-right ms-auto">
                @if (gs('multi_language'))
                    @php
                        $language = App\Models\Language::all();
                        $localLang = $language->where('code', config('app.locale'))->first();
                    @endphp
                    <div class="language-wrapper">
                        <div class="header-right__button language">
                            <img src="{{ getImage(getFilePath('language') . '/' . @$localLang->image, getFileSize('language')) }}">
                        </div>

                        <ul class="language-list">
                            @foreach ($language as $lang)
                                <li class="language-list__item">
                                    <a class="language-list__link" href="{{ route('lang', $lang->code) }}">
                                        <span class="thumb"><img alt="@lang('image')" src="{{ getImage(getFilePath('language') . '/' . @$lang->image, getFileSize('language')) }}"></span>
                                        <span class="text">{{ __($lang->name) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>
