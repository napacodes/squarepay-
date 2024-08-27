@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    <div class="row justify-content-center gy-4">
        @if (Auth::user()->ts)
            <div class="col-md-6">
                <div class="card custom--card">
                    <div class="card-header bg-dark text-center">
                        <h5 class="card-title m-0 text-white">@lang('Disable 2FA Security')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <h6 class="text-center">@lang('Your 2FA Verification is Enabled.')</h6>
                        </div>
                        <form action="{{ route('user.twofactor.disable') }}" class="transparent-form" method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <input class="form--control" name="code" placeholder="@lang('Enter Google Authenticator Code')" type="text">
                                </div>
                                <button class="btn btn--base w-100" type="submit">
                                    @lang('Submit')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h5 class="card-title m-0 text-white text-center">@lang('Google Authenticator')</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group mx-auto text-center">
                            <b class="d-block">@lang('Use the QR code or setup key on your Google Authenticator app to add your account.')</b>
                            <img class="mx-auto mt-2" src="{{ $qrCodeUrl }}">
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Setup Key')</label>
                            <div class="input-group">
                                <input class="form-control form--control referralURL" name="key" readonly type="text" value="{{ $secret }}">
                                <button class="input-group-text text-white copytext btn--base-two" id="copyBoard"> <i class="fa fa-copy"></i> </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label> <i class="fas fa-info-circle "></i> @lang('Help')</label>
                            <p>@lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device'). <a class="text--base" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank">@lang('App Link')</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card custom--card">
                    <div class="card-header bg-dark">
                        <h5 class="card-title m-0 text-white text-center">@lang('Enable 2FA Security')</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.twofactor.enable') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label class="form--label">@lang('Google Authenticatior Code')</label>
                                <input name="key" type="hidden" value="{{ $secret }}">
                                <input class="form--control" name="code" required type="text">
                            </div>
                            <button class="btn btn--base-two w-100" type="submit">@lang('Submit')</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('style')
    <style>
        .copied::after {
            background-color: #{{ gs('base_color') }};
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#copyBoard').click(function() {
                var copyText = document.querySelector(".referralURL");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                /*For mobile devices*/
                document.execCommand("copy");
                copyText.blur();
                this.classList.add('copied');
                setTimeout(() => this.classList.remove('copied'), 1500);
            });
        })(jQuery);
    </script>
@endpush
