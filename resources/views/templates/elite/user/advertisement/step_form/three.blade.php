@php
   $ad = @$advertisement;
@endphp

<div class="sell-card">
    <div class="sell-card__inner">
        <form action="{{ route('user.advertisement.store',@$ad->id) }}" class="sell-post-form row" method="POST">
            @csrf
            <input type="hidden" name="mode" value="{{ $mode }}">
            <input type="hidden" name="step" value="3">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form--label">@lang('Terms of Trade')</label>
                    <span class="icon" title="{{ __(@$advertisementContent->data_values->terms_of_trades) }}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <textarea name="terms" class="form-control form--control" placeholder="@lang('If you have any condition write here')" required>{{ old('terms',@$ad->terms) }}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group text-end">
                    <a href="{{ appendQuery('step','two') }}" class="btn btn--light btn--xl"
                        type="submit">
                        <i class="las la-chevron-left"></i> @lang('Previous')
                    </a>
                    <button class="btn btn--warning btn--xl" type="submit">
                        @lang('Finish') <i class="las la-check-circle"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('style')
    <style>
        .sell-card {
            min-height: unset;
        }
    </style>
@endpush
