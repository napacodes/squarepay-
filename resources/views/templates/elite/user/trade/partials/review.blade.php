@if ($trade->status == Status::TRADE_COMPLETED && $trade->advertisement->user_id != auth()->id())
    @php
        $reviewCheck = \App\Models\Review::where('trade_id', $trade->id)
            ->where('user_id', auth()->id())
            ->first();
    @endphp

    @if (!$reviewCheck && !$trade->reviewed)
        <div class="col-lg-12 pl-lg-5 mt-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>@lang('How was the trading experience?')</h4>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-lg-12 order-lg-1 order-2">
                            <div class="contact-form-wrapper">
                                <form class="contact-form give-review-form" action="{{ route('user.review.store', $trade->uid) }}" method="POST">
                                    @csrf
                                    <div class="row align-items-center">
                                        <div class="col-12 form-group">
                                            <div class="d-flex flex-wrap" style="gap:6px 10px">
                                                <div class="form-check review-input-group">
                                                    <input class="form-check-input review-input d-none positive-review" type="radio" name="type" id="positive-review" value="1">
                                                    <label class="form-check-label review-label positive-label" for="positive-review">
                                                        <span class="icon"><i class="far fa-thumbs-up"></i></span> @lang('Positive')
                                                    </label>
                                                </div>
                                                <div class="form-check review-input-group">
                                                    <input class="form-check-input review-input d-none negative-review" type="radio" name="type" id="negative-review" value="0">
                                                    <label class="form-check-label review-label negative-label" for="negative-review">
                                                        <span class="icon"><i class="far fa-thumbs-down"></i></span> @lang('Negative')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 form-group">
                                            <textarea name="feedback" placeholder="@lang('Your feedback')" class="form--control check-length" id="check-length" data-length="500">{{ old('feedback') }}</textarea>
                                            <span class="remaining text-left mt-2"><i class="las la-info-circle"></i> @lang('500 characters remaining')</span>
                                        </div>

                                        <div class="col-lg-12">
                                            <button type="submit" class="btn--base w-100">@lang('Submit Feedback')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
