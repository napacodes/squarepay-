@extends($activeTemplate . 'layouts.master_without_menu')
@section('content')
    @php
        $profileImage = fileManager()->userProfile();
        $user = auth()->user();
        $topImage = $trade->buyer_id == $user->id ? $trade->seller->image : $trade->buyer->image;
        $authBuyer = $user->id == $trade->buyer_id;

        $lastTime = Carbon\Carbon::parse($trade->paid_at)->addMinutes($trade->window);
        $remainingMin = $lastTime->diffInMinutes(now());

        $endTime = $trade->created_at->addMinutes($trade->window);
        $remainingMinitues = $endTime->diffInMinutes(now());

        if ($trade->buyer_id == $user->id) {
            $trader = $trade->seller;
        } elseif ($trade->seller_id == $user->id) {
            $trader = $trade->buyer;
        }
    @endphp

    <div class="row ">
        <div class="col-lg-12">
            <div class="buy-details two">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="buy-details__left">
                            <div class="buy-details__header">
                                <div class="buy-details__header-top">
                                    <div class="customer flex-align">
                                        <div class="customer__thumb">
                                            <img alt="" class="fit-image" src="{{ getImage($profileImage->path . '/' . @$topImage, null, true) }}">
                                        </div>
                                        <div class="customer__content">
                                            <h6 class="customer__name">{{ __($trader->fullname) }}</h6>
                                            <span class="customer__info">{{ $trader->username }}</span>
                                        </div>
                                    </div>
                                    @if(gs('kv'))
                                    @if ($trader->kv == Status::KYC_VERIFIED)
                                        <span class="kyc"><i class="fas fa-check-circle"></i> @lang('KYC Verified')</span>
                                    @elseif($trader->kv == Status::KYC_PENDING)
                                        <span class="kyc"><i class="fas fa-spinner text--warning"></i> @lang('KYC Pending')</span>
                                    @else
                                        <span class="kyc"><i class="fas fa-times text--danger"></i> @lang('KYC Unverified')</span>
                                    @endif
                                    @endif
                                    <span class="location"><img alt="" src="{{ getImage('assets/images/globe.png') }}"> {{ __(@$trader->address->country) }}</span>
                                </div>
                            </div>
                            <div class="buy-details__chatbox-heading">
                                <h5 class="title mb-0">@lang('Messages')</h5>
                                <a class="text--base" href="" title="@lang('Click here to load new chat and trade current status')"><i class="las la-undo-alt"></i> @lang('Refresh')</a>
                            </div>
                           @include($activeTemplate.'user.trade.partials.chat_box')
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="buy-details__right">
                            <div class="buy-details__right-top">
                                <div class="trade">
                                    <p class="trade__desc">@lang('Trade Code'): <span class="trade__code">#{{ $trade->uid }}</span>
                                    </p>
                                    @php echo $trade->statusBadge @endphp
                                </div>

                                @include($activeTemplate . 'user.trade.partials.alerts')

                                <div class="instructions">
                                    <h6 class="heading">@lang('Instructions to be followed')</h6>
                                    <div class="instruction_list">

                                        <h6 class="title"> @lang('Terms of trade')</h6>
                                        <p>{{ __($trade->advertisement->terms) }}</p>
                                    </div>
                                    <div class="instruction_list">
                                        <h6 class="title">@lang('Payment details')</h6>
                                        <p>{{ __($trade->advertisement->details) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="buy-details__right-middle">
                                <h6 class="title">@lang('Trade Information'):</h6>
                                <ul class="list">
                                    <li class="list__item"><span class="title">@lang('Buyer Name'):</span> <span class="info">{{ __($trade->buyer->username) }}</span></li>
                                    <li class="list__item"><span class="title">@lang('Seller Name'):</span> <span class="info">{{ __($trade->seller->username) }}</span></li>
                                    <li class="list__item"><span class="title">@lang('Amount'):</span> <span class="info">{{ showAmount($trade->amount) }} {{ __($trade->fiat->code) }}</span></li>
                                    <li class="list__item"><span class="title">{{ __($trade->crypto->code) }}:</span> <span class="info">{{ showAmount($trade->crypto_amount, 8) }}</span></li>
                                    <li class="list__item"><span class="title">@lang('Payment Window'):</span> <span class="info">{{ $trade->window }} @lang('Minutes')</span></li>
                                </ul>
                            </div>

                            @include($activeTemplate . 'user.trade.partials.actions')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            function startTimer(duration, display) {
                let timer = duration;
                let minutes;
                let seconds;
                if (display) {
                    setInterval(function() {
                        minutes = parseInt(timer / 60, 10);
                        seconds = parseInt(timer % 60, 10);

                        minutes = minutes < 10 ? "0" + minutes : minutes;
                        seconds = seconds < 10 ? "0" + seconds : seconds;
                        display.textContent = minutes + ":" + seconds;

                        if (--timer < 0) {
                            timer = duration;
                        }
                    }, 1000);
                }

            }

            @if ($trade->status == Status::TRADE_ESCROW_FUNDED)
                window.onload = function() {
                    let cancelMinutes = 60 * '{{ $remainingMinitues }}';
                    let display = document.querySelector('#cancel-min');
                    startTimer(cancelMinutes, display);
                };
            @endif

            @if ($trade->status == Status::TRADE_BUYER_SENT)
                window.onload = function() {
                    var disputeMinutes = 60 * '{{ $remainingMin }}';
                    let display = document.querySelector('#dispute-min');
                    startTimer(disputeMinutes, display);
                };
            @endif
        })(jQuery);
    </script>
@endpush
