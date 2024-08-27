@php
    $ad             = @$advertisement;
    $selectedCrypto = $cryptos->firstWhere('id', @$ad->crypto_currency_id ?? old('crypto_id')) ?? $cryptos->first();
    $selectedFiat   = $fiatCurrencies->firstWhere('id', @$ad->fiat_currency_id ?? old('fiat_id')) ?? $fiatCurrencies->first();
    $type           = $ad->type ?? 1;
@endphp

<div class="sell-card">
    <div class="sell-card__buttons d-flex gap-2">
        <button class="buy--sell-tab @if($type == 2 ) active @endif" data-type="2">@lang('I Want To Sell')</button>
        <button class="buy--sell-tab @if($type == 1 ) active @endif" data-type="1">@lang('I Want To Buy')</button>
    </div>
    <div class="sell-card__inner">
        @if (gs('trade_charge') > 0)
        <div class="mt-2 sell--charge @if(!@$ad && @$ad->type != 2) opacity-0 @endif text--warning">
            @lang('For selling') {{ getAmount(gs('trade_charge')) }}% @lang('will be charged for each completed trade.')
        </div>
        @endif
        <div class="row justify-content-center ">
            <div class="col-xl-6 col-lg-7 col-md-9 col-sm-11">
                <form action="{{ route('user.advertisement.store', @$ad->id) }}" class="sell-post-form row buy--sell-form pt-4"
                    method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="step" value="1">
                    <input type="hidden" name="crypto_id" value="{{ @$selectedCrypto->id }}">
                    <input type="hidden" name="fiat_id" value="{{ @$selectedFiat->id }}">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form--label">
                                @lang('Asset')
                                <span class="icon"
                                    title="{{ __(@$advertisementContent->data_values->crypto_currency) }}">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </label>
                            <div class="currency_switcher form--control">
                                <div class="currency_switcher__caption">
                                    <span class="text">
                                        {{ __(@$selectedCrypto->code) }}
                                    </span>
                                </div>
                                <div class="currency_switcher__list">
                                    @foreach ($cryptos as $crypto)
                                        <div class="currency_switcher__item crypto--currency-item"
                                            data-id="{{ $crypto->id }}" data-ad="{{ @$advertisement->id }}">
                                            <span class="text">
                                                {{ __($crypto->code) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form--label">
                                @lang('With Fiat')
                                <span class="icon"
                                    title="{{ __(@$advertisementContent->data_values->currency) }}">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                            </label>
                            <div class="currency_switcher form--control">
                                <div class="currency_switcher__caption">
                                    <span class="text">
                                        {{ __(@$selectedFiat->code) }}
                                    </span>
                                </div>
                                <div class="currency_switcher__list">
                                    @foreach ($fiatCurrencies as $fiatCurrency)
                                        <div class="currency_switcher__item fiat--currency-item"
                                            data-id="{{ $fiatCurrency->id }}">
                                            <span class="text">{{ __($fiatCurrency->code) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group text-center">
                            <button class="btn btn--base btn--xl" type="submit">
                                @lang('Next') <i class="las la-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.crypto--currency-item').on('click', function(e) {
                $('.buy--sell-form').find(`input[name=crypto_id]`).val($(this).data('id'))
            });

            $('.fiat--currency-item').on('click', function(e) {
                $('.buy--sell-form').find(`input[name=fiat_id]`).val($(this).data('id'))
            });

            $('.buy--sell-tab').on('click', function(e) {
                $(this).addClass('active');
                $('.buy--sell-tab').not(this).removeClass('active');
                $('.buy--sell-form').find(`input[name=type]`).val($(this).data('type'))
                if(parseInt($(this).data('type')) == 1){
                    $('.sell--charge').addClass('opacity-0')
                }else{
                    $('.sell--charge').removeClass('opacity-0')
                }
            });
        })(jQuery);
    </script>
@endpush
