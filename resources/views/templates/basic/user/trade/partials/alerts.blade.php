@if ($trade->status == Status::TRADE_ESCROW_FUNDED)
    @if ($authBuyer)
        <div class="mb-3">
            <div class="badge--danger rounded-1 text-center" role="alert">
                @lang('Please pay') {{ showAmount($trade->amount) }} {{ __($trade->fiat->code) }} @lang('using')
                <strong>{{ __($trade->fiatGateway->name) }} @lang('E-Wallet')</strong>
            </div>

            <p class="text-md mt-3">
                <i class="la la-info-circle" aria-hidden="true"></i>
                <span class="text--success"> {{ showAmount($trade->crypto_amount, 8) }}</span> {{ __($trade->advertisement->crypto->code) }} @lang('will be added to your wallet after seller confirmation about the payment.')
            </p>
        </div>
    @else
        <p class="text-md mb-3">@lang('Once the buyer has confirmed your payment then ') <span class="text--success">{{ showAmount($trade->crypto_amount, 8) }}</span> {{ __($trade->advertisement->crypto->code) }} @lang('will be available for release.')
        </p>
    @endif

    @if ($endTime > now())
        <div class="alert alert-warning mb-3" role="alert">
            @if ($authBuyer)
                <p class="text-md">@lang('The seller can cancel this trade after') <span id="cancel-min">{{ $remainingMinitues }}</span> @lang('minutes. Please make the payment within that time and mark the trade as paid.')</p>
            @else
                <p class="text-md">@lang('You can cancel this trade after') <span id="cancel-min">{{ $remainingMinitues }}</span> @lang('minutes.')</p>
            @endif
        </div>
    @else
        @if ($authBuyer)
            <div class="alert alert-warning mb-3" role="alert">
                <p class="text-md">@lang('The seller can cancel this trade anytime. Please make the payment and mark the trade as paid.')</p>
            </div>
        @endif
    @endif
@endif

@if ($trade->status == Status::TRADE_BUYER_SENT)
    <div class="mb-3">
        @if ($lastTime > now())
            <div class="alert alert-warning" role="alert">
                @if ($authBuyer)
                    <p class="text-md">@lang('You can dispute this trade after') <span id="dispute-min">{{ $remainingMin }}</span> @lang('minutes.')</p>
                @else
                    <p class="text-md">@lang('The buyer can dispute this trade after') <span id="dispute-min">{{ $remainingMin }}</span> @lang('minutes.')</p>
                @endif
            </div>
        @else
            @if (!$authBuyer)
                <div class="alert alert-warning" role="alert">
                    <p class="text-md">@lang('The buyer can dispute this trade anytime from now.')</p>
                </div>
            @endif
        @endif
    </div>
@endif

@if ($trade->status == Status::TRADE_DISPUTED)
    <div class="badge--danger rounded-1 text-center mb-3" role="alert">
        @lang('This trade is')
        <strong>{{ __($trade->details) }}.</strong>
        @lang('Please wait for the system response.')
    </div>
@endif
