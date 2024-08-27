<div class="row gy-3 mb-3 justify-content-center">
    @if ($trade->status == Status::TRADE_ESCROW_FUNDED)
        @if ($authBuyer || (!$authBuyer && $endTime <= now()))
            <div class="col-md-6">
                <button type="button" class="btn btn-lg btn--danger w-100 confirmationBtn" data-action="{{ route('user.trade.request.cancel', $trade->id) }}" data-question="@lang('Are you sure to cancel this trade?')">
                    <i class="las la-times-circle"></i> @lang('Cancel')
                </button>
            </div>
        @endif

        @if ($authBuyer)
            <div class="col-md-6">
                <button type="button" class="btn btn-lg btn--success w-100 confirmationBtn" data-action="{{ route('user.trade.request.paid', $trade->id) }}" data-question="@lang('Are you sure that you have paid the amount?')">
                    <i class="las la-check-circle"></i> @lang('I Have Paid')
                </button>
            </div>
        @endif
    @endif

    @if ($trade->status == Status::TRADE_BUYER_SENT)
        @if (!$authBuyer || ($authBuyer && $lastTime <= now()))
            <div class="col-md-6">
                <button type="button" class="btn btn-lg btn--danger w-100 confirmationBtn" data-action="{{ route('user.trade.request.dispute', $trade->id) }}" data-question="@lang('Are you sure to dispute this trade?')">
                    <i class="las la-times-circle"></i> @lang('Dispute')
                </button>
            </div>
        @endif

        @if (!$authBuyer)
            <div class="col-md-6">
                <button type="submit" class="btn btn-lg btn--success w-100 confirmationBtn" data-action="{{ route('user.trade.request.release', $trade->id) }}" data-question="@lang('Are you sure to release this trade?')">
                    <i class="las la-check-circle"></i> @lang('Release')
                </button>
            </div>
        @endif
    @endif
</div>

<x-confirmation-modal />
