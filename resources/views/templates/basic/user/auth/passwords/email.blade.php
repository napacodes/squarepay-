@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="pt-60 pb-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-wrapper p-4">
                        <form action="{{ route('user.password.email') }}" class="w-100" class="verify-gcaptcha" method="POST">
                            @csrf
                            <div class="card custom--card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>@lang('Email or Username')</label>
                                        <input autofocus="off" class="form-control" name="value" required type="text" value="{{ old('value') }}">
                                    </div>

                                    <x-captcha />

                                    <div class="mt-4">
                                        <button class="btn--base w-100" type="submit"> @lang('Send Password Code')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
