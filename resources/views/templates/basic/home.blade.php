@extends($activeTemplate . 'layouts.frontend')
@section('content')

    @php
        $bannerContent = getContent('banner.content', true);
    @endphp

    <section class="hero bg_img"
        data-background="{{ frontendImage('banner' , @$bannerContent->data_values->image, '1920x1270') }}">
        <div class="container position-relative">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-5 text-xl-start text-center">
                    <h2 class="hero__title text-white mb-3">{{ __(@$bannerContent->data_values->heading) }}</h2>
                    <p class="hero__details text-white">{{ __(@$bannerContent->data_values->subheading) }}</p>
                </div>
                <div class="col-xl-6 mt-5">
                    <div class="bitcoin-form-wrapper">
                        <div class="form-image">
                            <img src="{{ frontendImage('banner' , @$bannerContent->data_values->form_bg, '700x465') }}">
                        </div>
                        <h5 class="title text-white">@lang(@$bannerContent->data_values->form_header)</h5>
                        <form class="bitcoin-form buy--sell-filter" method="GET">
                            <div class="row align-items-center">
                                <div class="col-md-6 form-group">
                                    <select class="select type select2" data-minimum-results-for-search="-1"  required>
                                        <option value="">@lang('Select Buy or Sell')</option>
                                        <option value="buy">@lang('Buy')</option>
                                        <option value="sell">@lang('Sell')</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <select class="select crypto select2" required>
                                        <option value="">@lang('Select Cryptocurrency')</option>
                                        @foreach ($cryptos as $cryptoData)
                                            <option value="{{ $cryptoData->code }}">
                                                {{ __($cryptoData->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <select class="select gateway select2" required>
                                        <option selected  disabled>@lang('Select Payment Method')</option>
                                        <option value="all">@lang('All')</option>
                                        @foreach ($fiatGateways as $gateway)
                                            <option value="{{ $gateway->slug }}" data-fiat="{{ @$gateway->fiat }}">
                                                {{ __($gateway->name) }} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <select class="select currency select2" ></select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <select class="select location select2">
                                        <option selected disabled>@lang('Select Location')</option>
                                        <option value="all">@lang('All')</option>
                                        @foreach ($countries as $key => $country)
                                            <option value="{{ $key }}" data-code="{{ $key }}">
                                                {{ __($country->country) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-6 form-group">
                                    <input type="number" step="any" name="" class="form-control amount"
                                        placeholder="@lang('Preferred Amount')">
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn--base w-100 mt-3">@lang(@$bannerContent->data_values->form_button_text)</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";

            $('.gateway').on('change', function() {
                var fiats = $(this).find('option:selected').data('fiat');
                var html = `<option value="all">@lang('All')</option>`;
                if (fiats && fiats.length > 0) {
                    $.each(fiats, function(i, v) {
                        html += `<option value="${v.code}">${v.code}</option>`;
                    });
                } else {
                    html = `<option value="">@lang('Select Fiat Currency')</option>`;
                }

                $('.currency').html(html);
            }).change();

            $('.buy--sell-filter').on('change input', function(e) {
                newUrl();
                return false;
            });

            function newUrl() {

                let amount     = $('.buy--sell-filter').find(`.amount`).val();
                let gateway    = $('.buy--sell-filter').find(`.gateway`).val();
                let cryptoCode = $('.buy--sell-filter').find(`.crypto`).val();
                let type       = $('.buy--sell-filter').find(`.type`).val();
                let currency   = $('.buy--sell-filter').find(`.currency`).val();
                let location   = $('.buy--sell-filter').find(`.location`).val();

                let moreBuySellUrl = "{{ route('advertisement.all', ['type' => ':type', 'crypto' => ':crypto', 'country' => ':country', 'gateway' => ':gateway', 'currency' => ':currency', 'amount' => ':amount']) }}";

                if (amount) {
                    moreBuySellUrl = moreBuySellUrl.replace(":amount", amount).replace(':currency', currency || 'all').replace(':gateway', gateway || 'all');
                } else {
                    moreBuySellUrl = moreBuySellUrl.replace("/:amount", '')
                }
                moreBuySellUrl = moreBuySellUrl.replace(":country", location || 'all' );

                if (currency) {
                    moreBuySellUrl = moreBuySellUrl.replace(':currency', currency || 'all').replace(':gateway', gateway || 'all');
                } else {
                    moreBuySellUrl = moreBuySellUrl.replace('/:currency', '')
                }
                if (gateway) {
                    moreBuySellUrl = moreBuySellUrl.replace(':gateway', gateway);
                } else {
                    moreBuySellUrl = moreBuySellUrl.replace('/:gateway', '');
                }

                moreBuySellUrl = moreBuySellUrl.replace(':type', type).replace(':crypto', cryptoCode);

                $('.buy--sell-filter').attr('action',moreBuySellUrl);

            };
        })(jQuery)
    </script>
@endpush
