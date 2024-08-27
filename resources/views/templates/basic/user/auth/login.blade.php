@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $loginContent = getContent('login.content', true);
    @endphp

    <section class="account-section">
        <div class="left">
            <div class="line-bg">
                <img src="{{ asset($activeTemplateTrue . 'images/line-bg.png') }}" alt="image">
            </div>
            <div class="account-form-area">
                <div class="text-center">
                    <a href="{{ url('/') }}" class="account-logo"><img src="{{ siteLogo() }}" alt="image"></a>
                </div>

                <div class="mt-4">
                    @include($activeTemplate . 'partials.social_login')
                </div>

                <form method="POST" action="{{ route('user.login') }}" onsubmit="return submitUserForm();">
                    @csrf
                    <div class="form-group">
                        <label>@lang('Username or Email')</label>
                        <input type="text" name="username" value="{{ old('username') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Password')</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <x-captcha />

                    <div class="form-group custom-checkbox mt-2">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            @lang('Remember Me')
                        </label>
                    </div>

                    <button type="submit" class="btn--base w-100">@lang('Login Now')</button>
                    <div class="row mt-3">
                        <div class="col-lg-6">
                            <p>@lang('Haven\'t an account')? <a href="{{ route('user.register') }}"
                                    class="base--color">@lang('Signup now')</a></p>
                        </div>
                        <div class="col-lg-6 text-lg-end">
                            <a href="{{ route('user.password.request') }}" class="mt-3 base--color">@lang('Forgot password')?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="right bg_img"
            data-background="{{ frontendImage('login', @$loginContent->data_values->image, '1150x950') }}">
            <div class="content text-center">
                <h2 class="text-white mb-4">{{ __(@$loginContent->data_values->heading) }}</h2>
                <p class="text-white">{{ __(@$loginContent->data_values->sub_heading) }}</p>
            </div>
        </div>
    </section>
@endsection
