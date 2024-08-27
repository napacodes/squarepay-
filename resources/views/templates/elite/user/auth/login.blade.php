@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $content = getContent('login.content', true);
        $policyElements = getContent('policy_pages.element');
    @endphp

    <section class="account">
        <div class="account-inner">
            <div class="account-left">
                <div class="account-left__thumb">
                    <img alt="Thumb" src="{{ frontendImage('login', @$content->data_values->image, '735x605') }}">
                </div>
            </div>
            <div class="account-right">
                <div class="account-form-wrapper">
                    <h1 class="title">{{ __($pageTitle) }}</h1>
                    <h5 class="sub-title">{{ __(@$content->data_values->heading) }}</h5>
                    <p class="desc">{{ __(@$content->data_values->subheading) }}</p>
                    <div class="mt-3">
                    </div>
                    <form action="{{ route('user.login') }}" class="account-form" method="post"
                        onsubmit="return submitUserForm();">
                        @include($activeTemplate . 'partials.social_login')
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Username or Email')</label>
                            <input class="form--control" name="username" required type="text"
                                value="{{ old('username') }}">
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Password')</label>
                            <div class="position-relative">
                                <input class="form--control" id="password" name="password" required type="password">
                                <div class="password-show-hide fas fa-eye toggle-password fa-eye-slash" id="#password">
                                </div>
                            </div>
                        </div>
                        <x-captcha :path="$activeTemplate . 'partials.'" />
                        <div class="form-group">
                            <button class="btn btn--base-two w-100" type="submit">@lang('Login Now')</button>
                        </div>
                        <div class="form-group">
                            <p class="switch">@lang('Don\'t have an account?') <a class="link"
                                    href="{{ route('user.register') }}">@lang('Create Now')</a></p>
                            <a class="forgot" href="{{ route('user.password.request') }}">@lang('Forgot your password?')</a>
                        </div>
                    </form>
                </div>
                <div class="account-footer login--page">
                    <p class="account-footer__text">
                        &copy; {{ date('Y') }}
                        <a href="{{ route('home') }}" class="text--base">
                            {{ __(gs('site_name')) }}
                        </a>
                        @lang('All Rights Reserved')
                    </p>
                    <div class="account-footer__right">
                        @foreach ($policyElements as $policy)
                            <a class="account-footer__right-link"
                                href="{{ route('policy.pages', $policy->slug) }}">{{ __($policy->data_values->title) }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
