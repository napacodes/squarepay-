@php
    $ad = @$advertisement;
@endphp
<div class="sell-card">
    <div class="sell-card__inner">
        <form action="{{ route('user.advertisement.store',@$ad->id) }}" class="sell-post-form row" method="POST">
            @csrf
            <input type="hidden" name="step" value="2">
            <input type="hidden" name="mode" value="{{ $mode }}">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form--label">@lang('Payment Method')</label>
                    <span class="icon" title="{{ __(@$advertisementContent->data_values->fiat_payment_method) }}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <select class="form--control select select2" name="fiat_gateway_id" required>
                        <option value="" selected disabled>@lang('Select One')</option>
                        @foreach ($fiatGateways as $gateway)
                            <option value="{{ $gateway->id }}" data-fiat="{{ @$gateway->fiat }}"
                                @selected(old('fiat_gateway_id', $ad->fiat_gateway_id) == $gateway->id)>
                                {{ __($gateway->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form--label">@lang('Payment Window')</label>
                    <span class="icon" title="{{ __(@$advertisementContent->data_values->payment_window) }}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <select class="form--control select select2" name="window" required>
                        <option value="">@lang('Select One')</option>
                        @foreach ($paymentWindows as $window)
                            <option value="{{ $window->minute }}" @selected(old('window', @$ad->window) == $window->minute)>
                                {{ __($window->minute) }} @lang('Minutes')
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form--label"> @lang('Price Type')</label>
                    <span class="icon" title="{{ __(@$advertisementContent->data_values->price_type) }}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <select class="form--control select select2" name="price_type" required data-minimum-results-for-search="-1">
                        <option value="1">@lang('Margin')</option>
                        <option value="2">@lang('Fixed')</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group price--input-wrapper">
                    <label class="form--label price--label"> @lang('Margin Rate')</label>
                    <span class="icon" title="{{ __(@$advertisementContent->data_values->margin) }}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <div class="input-group">
                        <input type="number" step="any" name="margin" class="form-control form--control" value="{{ old('margin',@$ad->margin > 0 ? getAmount($ad->margin) : 0 ) }}">
                        <span class="input-group-text price--symbol">%</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 price--result-wrapper d-none">
                <div class="border-line-area form-group">
                    <h6 class="border-line-title text--warning mb-0">
                        @lang('Price'): <span class="price--result"></span>
                    </h6>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form--label"> @lang('Minimum Limit') </label>
                    <span class="icon" title="{{ __(@$advertisementContent->data_values->minimum_limit) }}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <div class="input-group">
                        <input type="number" step="any" name="min" placeholder="@lang('Minimum amount')"
                            class="form--control form-control" required value="{{ old('min', @$ad->min > 0 ? getAmount($ad->min) : '' ) }}">
                        <span class="input-group-text">{{ __(@$ad->fiat->code) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form--label">@lang('Maximum Limit')</label>
                    <span class="icon" title="{{ __(@$advertisementContent->data_values->maximum_limit) }}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <div class="input-group">
                        <input type="number" step="any" name="max" placeholder="@lang('Maximum amount')"
                            class="form-control form--control" required value="{{ old('max', @$ad->max > 0 ?  getAmount($ad->max) : '') }}">
                        <span class="input-group-text">{{ __(@$ad->fiat->code) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label class="form--label"> @lang('Payment Details')</label>
                    <span class="icon" title="{{ __(@$advertisementContent->data_values->payment_details) }}">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <textarea name="details" class="form--control form-control" placeholder="@lang('Write about your convenient payment method')" required>{{ old('details', @$ad->details) }}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="text-end">
                    <a href="{{ appendQuery('step','one') }}" class="btn btn--light btn--xl"
                        type="submit">
                        <i class="las la-chevron-left"></i> @lang('Previous')
                    </a>
                    <button class="btn btn--warning btn--xl" type="submit">
                        @lang('Next') <i class="las la-chevron-right"></i>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@push('script')
    <script>
        "use strict";
        (function($) {

            let cryptoRate   = parseFloat("{{ @$ad->crypto->rate }}");
            let fiatRate     = parseFloat("{{ @$ad->fiat->rate }}");
            let type         = parseInt("{{ @$ad->type }}");
            let cryptoCode   = "{{ @$ad->crypto->code }}";
            let fiatCodeCode = "{{ @$ad->fiat->code }}";


            $('select[name=price_type]').on('change', function(e) {
                if (parseInt(this.value) == 1) {
                    $('.price--input-wrapper').find(`input`).attr('name', 'margin').val(0);
                    $('.price--input-wrapper').find(`input`).attr('placeholder', "@lang('Margin Rate')");
                    $('.price--input-wrapper').find(`.price--label`).text("@lang('Margin Rate')");
                    $('.price--input-wrapper').find(`.price--symbol`).text("%");
                } else {
                    $('.price--input-wrapper').find(`input`).attr('name', 'fixed_price').val(0);
                    $('.price--input-wrapper').find(`.price--label`).text("@lang('Fixed Price')");
                    $('.price--input-wrapper').find(`input`).attr('placeholder', "@lang('Fixed Price')");
                    $('.price--input-wrapper').find(`.price--symbol`).text(fiatCodeCode);
                }
            });


            $(`.sell-card__inner input[name=fixed_price], .sell-card__inner input[name=margin], select[name=price_type]`).on('input change',function(e){
                priceCalulation();
            });

            function priceCalulation () {
                let priceType=parseInt($(`select[name=price_type]`).val());

                if(priceType == 2){
                    var rate = parseFloat($(`.sell-card__inner input[name=fixed_price]`).val() || 0)
                }else{
                    let amount = parseFloat(cryptoRate * fiatRate);
                    let margin = parseFloat($(`.sell-card__inner input[name=margin]`).val() || 0)

                    if(type == 1){
                        var rate=parseFloat(amount - ((amount*margin)/100));
                    }else{
                        var rate=parseFloat(amount + ((amount*margin)/100));
                    }
                }

                if(rate && rate > 0){
                    $('.price--result-wrapper').removeClass('d-none');
                    $('.price--result').text(`${rate.toFixed(2)} ${fiatCodeCode}/${cryptoCode}`);
                }else{
                    $('.price--result-wrapper').addClass('d-none');
                }
            };

            priceCalulation();
        })(jQuery);
    </script>
@endpush

@push('style')
<style>
    .border-line-area {
        position: relative;
        text-align: center;
        z-index: 1;
    }

    .border-line-area::before {
        position: absolute;
        content: '';
        top: 50%;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: #e5e5e5;
        z-index: -1;
    }

    .border-line-title {
        display: inline-block;
        background-color: #fff;
        border: 1px solid #eee;
        padding: 14px 25px;
        border-radius: 5px;
    }
</style>
@endpush
