@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-120 pb-120">
        <div class="container">
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
                                <form action="{{ route('user.twofactor.disable') }}" method="POST" class="transparent-form">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                                        </div>
                                        <button type="submit" class="btn btn--base w-100">
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
                                    <label class="form-label">@lang('Setup Key')</label>
                                    <div class="input-group">
                                        <input type="text" name="key" value="{{ $secret }}" class="form-control referralURL" readonly>
                                        <button class="input-group-text border--base bg--base text-white copytext" id="copyBoard"> <i class="fa fa-copy"></i> </button>
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
                                        <label class="form-label">@lang('Google Authenticatior Code')</label>
                                        <input type="hidden" name="key" value="{{ $secret }}">
                                        <input type="text" class="form-control" name="code" required>
                                    </div>
                                    <button type="submit" class="btn btn--base w-100">
                                        @lang('Submit')
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
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
