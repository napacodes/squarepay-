@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $registrationContent = getContent('registration.content', true);
        $policyElements = getContent('policy_pages.element');
    @endphp

    <section class="account-section style--two">
        <div class="left">
            <div class="line-bg">
                <img alt="image" src="{{ asset($activeTemplateTrue . 'images/line-bg.png') }}">
            </div>
            <div class="account-form-area">
                <div class="text-center">
                    <a class="account-logo" href="{{ url('/') }}"><img alt="image" src="{{ siteLogo() }}"></a>
                </div>

                <div class="mt-4">
                    @include($activeTemplate . 'partials.social_login')
                </div>

                <form action="{{ route('user.register') }}" method="POST" onsubmit="return submitUserForm();">
                    @csrf

                    <div class="row">
                        @if (session()->get('reference') != null)
                            <div class="form-group col-sm-12">
                                <label>@lang('Referred By')</label>
                                <input class="form-control" id="referenceBy" name="referBy" readonly type="text"
                                    value="{{ session()->get('reference') }}">
                            </div>
                        @endif

                        <div class="form-group col-sm-6">
                            <label class="form-label">@lang('First Name')</label>
                            <input type="text" class="form-control" name="firstname" value="{{ old('firstname') }}"
                                required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label class="form-label">@lang('Last Name')</label>
                            <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}"
                                required>
                        </div>

                        <div class="form-group col-sm-12">
                            <label>@lang('Email Address')</label>
                            <input class="form-control checkUser" id="email" name="email" required type="email"
                                value="{{ old('email') }}">
                        </div>

                        <div class="form-group col-sm-6">
                            <label>@lang('Password')</label>
                            <input class="form-control  @if (gs('secure_password')) secure-password @endif"
                                name="password" required type="password">
                        </div>

                        <div class="form-group col-sm-6">
                            <label>@lang('Confirm Password')</label>
                            <input class="form-control" name="password_confirmation" required type="password">
                        </div>
                    </div>

                    @if (gs('agree'))
                        <div class="form-group custom-checkbox mt-2">
                            <input @checked(old('agree')) class="form-check-input" id="agree" name="agree"
                                type="checkbox">
                            <label class="form-check-label" for="agree">
                                @lang('I agree with')&nbsp;
                                @foreach ($policyElements as $policy)
                                    <a class="text--base" href="{{ route('policy.pages', $policy->slug) }}"
                                        target="_blank">{{ __($policy->data_values->title) }}</a>
                                    @if (!$loop->last)
                                        ,&nbsp;
                                    @endif
                                @endforeach
                            </label>
                        </div>
                    @endif

                    <x-captcha />

                    <div>
                        <button class="btn--base w-100" id="recaptcha" type="submit">@lang('Register Now')</button>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-lg-12">
                            <p>@lang('Already Have An Account')? <a class="mt-3 base--color"
                                    href="{{ route('user.login') }}">@lang('Login Now')</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="right bg_img"
            data-background="{{ frontendImage('registration', @$registrationContent->data_values->image, '1150x950') }}">
            <div class="content text-center">
                <h2 class="text-white mb-4">{{ __(@$registrationContent->data_values->heading) }}</h2>
                <p class="text-white">{{ __(@$registrationContent->data_values->sub_heading) }}</p>
            </div>
        </div>
    </section>

    <div aria-hidden="true" aria-labelledby="existModalCenterTitle" class="modal fade" id="existModalCenter" role="dialog"
        tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login')</h6>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-sm btn--base" href="{{ route('user.login') }}">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('script')
    <script>
        "use strict";
        (function($) {

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    email: value,
                    _token: token
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
