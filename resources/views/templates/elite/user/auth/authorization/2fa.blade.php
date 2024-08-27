@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="d-flex justify-content-center">
                        <div class="verification-code-wrapper">
                            <div class="verification-area">
                                <form action="{{ route('user.go2fa.verify') }}" method="POST" class="submit-form">
                                    @csrf
                                    <p>@lang('You need to verify yourself by google authenticator code to get access to your dashboard.')</p>

                                    @include($activeTemplate . 'partials.verification_code')

                                    <div class="form--group">
                                        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
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
