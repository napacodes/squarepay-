@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    @php
        $kycContent = getContent('kyc.content', true);
        $walletImage = fileManager()->crypto();
        $profileImage = fileManager()->userProfile();
    @endphp

    <div class="notice"></div>

    @php
        $kyc = getContent('kyc.content', true);
    @endphp

    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="alert alert--danger" role="alert">
            <div class="alert__icon"><i class="fas fa-file-signature"></i></div>
            <p class="alert__message">
                <span class="fw-bold">@lang('KYC Documents Rejected')</span><br>
                <small><i>{{ __(@$kyc->data_values->kyc_rejected) }}
                        <a href="javascript::void(0)" class="link-color" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Click here')</a> @lang('to show the reason').

                        <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click Here')</a> @lang('to Re-submit Documents').
                        <br>
                        <a href="{{ route('user.kyc.data') }}" class="link-color">@lang('See KYC Data')</a>
                    </i></small>
            </p>
        </div>
    @elseif ($user->kv == Status::KYC_UNVERIFIED)
        <div class="alert alert--info" role="alert">
            <div class="alert__icon"><i class="fas fa-file-signature"></i></div>
            <p class="alert__message">
                <span class="fw-bold">@lang('KYC Verification Required')</span><br>
                <small><i>{{ __(@$kyc->data_values->kyc_required) }} <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click here')</a> @lang('to submit KYC information').</i></small>
            </p>
        </div>
    @elseif($user->kv == Status::KYC_PENDING)
        <div class="alert alert--warning" role="alert">
            <div class="alert__icon"><i class="fas fa-user-check"></i></div>
            <p class="alert__message">
                <span class="fw-bold">@lang('KYC Verification Pending')</span><br>
                <small><i>{{ __(@$kyc->data_values->kyc_pending) }} <a href="{{ route('user.kyc.data') }}" class="link-color">@lang('Click here')</a> @lang('to see your submitted information')</i></small>
            </p>
        </div>
    @endif

    <div class="row gy-4">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <h5 class="title">@lang('Referral Link')</h5>
            <div class="input-group">
                <input class="form-control form--control bg-white" id="key" name="key" readonly="" type="text" value="{{ route('home') }}?reference={{ auth()->user()->username }}">
                <button class="input-group-text bg--base-two text-white border-0 copyBtn" id="copyBoard">
                    <i class="lar la-copy"></i>
                </button>
            </div>
        </div>

        @foreach ($wallets as $wallet)
            <div class="col-xl-4 col-md-6 d-widget-item">
                <a class="d-block" href="{{ route('user.transactions') }}?crypto={{ $wallet->cryptoId }}">
                    <div class="d-widget">
                        <div class="d-widget__icon">
                            <img src="{{ getImage($walletImage->path . '/' . $wallet->cryptoImage, $walletImage->size) }}">
                        </div>
                        <div class="d-widget__content">
                            <p class="d-widget__caption">{{ __($wallet->cryptoCode) }} </p>
                            <h2 class="d-widget__amount">{{ showAmount($wallet->balance, 8) }}</h2>
                            <h6 class="d-widget__usd text--base">
                                @lang('In USD') <i class="las la-arrow-right"></i>
                                {{ showAmount($wallet->balanceInUsd) }}
                            </h6>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <h4 class="mt-4">@lang('Latest Advertisements')</h4>
    @include($activeTemplate . 'partials.user_ads_table')

    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth()->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.copyBtn').on('click', function() {
                var copyText = document.getElementById("key");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");

                iziToast.success({
                    message: "Copied: " + copyText.value,
                    position: "topRight"
                });
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .d-widget__usd {
            font-size: 15px;
            margin-top: 5px;
        }
    </style>
@endpush
