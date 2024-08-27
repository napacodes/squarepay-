<div class="buttons">
    @if ($trade->status == Status::TRADE_ESCROW_FUNDED)
        @if ($authBuyer || (!$authBuyer && $endTime <= now()))
            <button class="btn btn--danger confirmationBtn" data-action="{{ route('user.trade.request.cancel', $trade->id) }}" data-question="@lang('Are you sure to cancel this trade?')" type="button">
                <i class="las la-times-circle"></i> @lang('Cancel')
            </button>
        @endif

        @if ($authBuyer)
            <button class="btn btn--success confirmationBtn" data-action="{{ route('user.trade.request.paid', $trade->id) }}" data-question="@lang('Are you sure that you have paid the amount?')" type="button">
                <i class="las la-check-circle"></i> @lang('I Have Paid')
            </button>
        @endif
    @endif

    @if ($trade->status == Status::TRADE_BUYER_SENT)
        @if (!$authBuyer || ($authBuyer && $lastTime <= now()))
            <button class="btn btn--danger confirmationBtn" data-action="{{ route('user.trade.request.dispute', $trade->id) }}" data-question="@lang('Are you sure to dispute this trade?')" type="button">
                <i class="las la-times-circle"></i> @lang('Dispute')
            </button>
        @endif

        @if (!$authBuyer)
            <button class="btn btn--success confirmationBtn" data-action="{{ route('user.trade.request.release', $trade->id) }}" data-question="@lang('Are you sure to release this trade?')" type="submit">
                <i class="las la-check-circle"></i> @lang('Release')
            </button>
        @endif
    @endif
</div>

<x-user-confirmation-modal />
