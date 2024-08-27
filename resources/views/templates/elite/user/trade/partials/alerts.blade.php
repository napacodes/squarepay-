<div class="alert-section">
    @if ($trade->status == Status::TRADE_ESCROW_FUNDED)
        @if ($authBuyer)
            <div class="mb-3 text-center">
                <p class="text--danger">
                    @lang('Please pay') {{ showAmount($trade->amount) }} {{ __($trade->fiat->code) }} @lang('using')
                    <strong>{{ __($trade->fiatGateway->name) }} @lang('E-Wallet')</strong>
                </p>

                <p class=" mt-3">
                    <i aria-hidden="true" class="la la-info-circle"></i>
                    <span class="text--success"> {{ showAmount($trade->crypto_amount, 8) }}</span> {{ __($trade->advertisement->crypto->code) }} @lang('will be added to your wallet after seller confirmation about the payment.')
                </p>
            </div>
        @else
            <p class="mb-3">@lang('Once the buyer has confirmed your payment then ') <span class="text--success">{{ showAmount($trade->crypto_amount, 8) }}</span> <strong>{{ __($trade->advertisement->crypto->code) }}</strong> @lang('will be available for release.')
            </p>
        @endif

        @if ($endTime > now())
            <div class="alert alert-warning mb-3" role="alert">
                @if ($authBuyer)
                    <p class="text--warning"><i>@lang('The seller can cancel this trade after') <span class="fw-bold" id="cancel-min">{{ $remainingMinitues }}</span> @lang('minutes. Please make the payment within that time and mark the trade as paid.')</i></p>
                @else
                    <p class="text--warning"><i>@lang('You can cancel this trade after') <span class="fw-bold" id="cancel-min">{{ $remainingMinitues }}</span> @lang('minutes.')</i></p>
                @endif
            </div>
        @else
            @if ($authBuyer)
                <div class="alert alert-warning mb-3" role="alert">
                    <p class="text--warning"><i>@lang('The seller can cancel this trade anytime. Please make the payment and mark the trade as paid.')</i></p>
                </div>
            @endif
        @endif
    @endif

    @if ($trade->status == Status::TRADE_BUYER_SENT)
        <div class="mb-3">
            @if ($lastTime > now())
                <div class="alert alert-warning" role="alert">
                    @if ($authBuyer)
                        <p class="text--warning"><i>@lang('You can dispute this trade after') <span class="fw-bold" id="dispute-min">{{ $remainingMin }}</span> @lang('minutes.')</i></p>
                    @else
                        <p class="text--warning"><i>@lang('The buyer can dispute this trade after') <span class="fw-bold" id="dispute-min">{{ $remainingMin }}</span> @lang('minutes.')</i></p>
                    @endif
                </div>
            @else
                @if (!$authBuyer)
                    <div class="alert alert-warning" role="alert">
                        <p class="text--warning"><i>@lang('The buyer can dispute this trade anytime from now.')</i></p>
                    </div>
                @endif
            @endif
        </div>
    @endif

    @if ($trade->status == Status::TRADE_DISPUTED)
        <div class="alert alert-danger rounded-1 text-center mb-3" role="alert">
            <p><i>@lang('This trade is') <strong>{{ __($trade->details) }}.</strong>@lang('Please wait for the system response.')</i></p>
        </div>
    @endif
</div>
