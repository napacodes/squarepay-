@extends($activeTemplate . 'layouts.frontend')
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
    @endphp

    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row">

                <div class="col-lg-12 text-center mb-4">
                    <h3 class="mb-1">{{ $title }}</h3>
                    <h6 class="text--base">{{ $title2 }}</h6>
                </div>

                <div class="col-lg-6 pl-lg-5 mt-lg-0 mt-5">
                    @include($activeTemplate . 'user.trade.partials.chat_box')
                </div>

                <div class="col-lg-6 mt-lg-0 mt-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-sm text-muted">
                                <span>#{{ $trade->uid }}</span>
                            </span>
                            <span>
                                @php echo $trade->statusBadge @endphp
                            </span>
                        </div>
                        <div class="card-body">
                            @include($activeTemplate . 'user.trade.partials.alerts')
                            @include($activeTemplate . 'user.trade.partials.actions')
                            @include($activeTemplate . 'user.trade.partials.info')
                            @include($activeTemplate . 'user.trade.partials.instructions')
                        </div>
                    </div>
                </div>

                @include($activeTemplate . 'user.trade.partials.review')

                 @if ($trade->reviewed == 1 && $trade->advertisement->user_id != auth()->id())
                    <div class="mt-5 alert alert-warning">
                        @lang('You\'ve already given feedback on this advertisement.') <a href="{{ route('user.trade.request.new', $trade->advertisement->id) }}" class="text--base">@lang('View Reviews')</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
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
