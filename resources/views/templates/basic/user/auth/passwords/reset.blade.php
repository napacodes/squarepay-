@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-60 pb-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-wrapper p-4 overflow-visible">
                        <p class="mb-4">@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>

                        <form action="{{ route('user.password.update') }}" class="w-100" method="POST">
                            @csrf
                            <div class="card custom--card">
                                <div class="card-body">
                                    <input name="email" type="hidden" value="{{ $email }}">
                                    <input name="token" type="hidden" value="{{ $token }}">

                                    <div class="form-group">
                                        <label>@lang('Password')</label>
                                        <input class="form-control @if (gs('secure_password')) secure-password @endif" name="password" required type="password">
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Confirm Password')</label>
                                        <input class="form-control" name="password_confirmation" required type="password">
                                    </div>
                                    <div class="mt-4">
                                        <button class="btn btn--base" type="submit"> @lang('Reset Password')</button>
                                    </div>

                                    <p class="mt-2"><a class="text--base" href="{{ route('user.login') }}"> @lang('Login Here')?</a></p>
                                </div>
                            </div>
                        </form>
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
