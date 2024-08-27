@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-wrapper p-4 overflow-visible">
                        <div class="card custom--card">
                            <div class="card-body">
                                <p class="mb-3">@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
                                <form action="{{ route('user.password.update') }}" class="w-100" method="POST">
                                    @csrf
                                    <input name="email" type="hidden" value="{{ $email }}">
                                    <input name="token" type="hidden" value="{{ $token }}">

                                    <div class="form-group">
                                        <label class="form--label">@lang('Password')</label>
                                        <input class="form--control @if (gs('secure_password')) secure-password @endif" name="password" required type="password">
                                    </div>

                                    <div class="form-group">
                                        <label class="form--label">@lang('Confirm Password')</label>
                                        <input class="form--control" name="password_confirmation" required type="password">
                                    </div>
                                    <div class="mt-4 d-felx flex-wrap align-items-center flex-between">
                                        <button class="btn btn--base" type="submit"> @lang('Reset Password')</button>
                                        <a class="text--base" href="{{ route('user.login') }}"> @lang('Login Here')?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
