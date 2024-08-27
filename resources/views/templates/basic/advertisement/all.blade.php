@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pb-120">
        <div class="coin-search-area">
            <div class="container">
                <form class="coin-search-form buy--sell-filter"  method="GET">
                    <div class="row align-items-end gy-3">
                        <div class="col-xxl-1 col-md-4 col-sm-6">
                            <label>@lang('Buy or Sell')</label>
                            <select class="select type select2" required data-minimum-results-for-search="-1">
                                <option value="buy"  @selected(@$request->type == 'buy')>@lang('Buy')</option>
                                <option value="sell" @selected(@$request->type == 'sell')>@lang('Sell')</option>
                            </select>
                        </div>
                        <div class="col-xxl-2 col-md-4 col-sm-6">
                            <label>@lang('Cryptocurrency')</label>
                            <select class="select crypto select2" required>
                                @foreach ($cryptos as $cryptoData)
                                    <option value="{{ $cryptoData->code }}"  @selected(@$request->crypto == $cryptoData->code) >{{ __($cryptoData->code) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xxl-2 col-md-4 col-sm-6">
                            <label>@lang('Payment Method')</label>
                            <select class="select gateway select2">
                                <option value="" selected disabled>@lang('Select One')</option>
                                <option value="all" @selected(@$request->gateway == 'all')>@lang('All')</option>
                                @foreach ($fiatGateways as $gateway)
                                    <option value="{{ $gateway->slug }}" data-fiat="{{ @$gateway->fiat }}" @selected(@$request->gateway == $gateway->slug)>
                                        {{ __($gateway->name) }}
                                     </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xxl-2 col-md-4 col-sm-6">
                            <label>@lang('Fiat currency')</label>
                            <select class="select currency select2"></select>
                        </div>
                        <div class="col-xxl-2 col-md-4 col-sm-6">
                            <label>@lang('Offer location')</label>
                            <select class="select country select2">
                                <option value="all">@lang('All')</option>
                                @foreach ($countries as $key => $country)
                                    <option value="{{ $key }}" @selected(@$request->country == $key)>
                                        {{ __($country->country) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xxl-2 col-md-4 col-sm-6">
                            <label>@lang('Limit')</label>
                            <input type="number" step="any" value="{{ @$request->amount  ?? '' }}" placeholder="@lang('Enter Amount')" class="form-control amount">
                        </div>
                        <div class="col-xxl-1">
                            <button type="submit" class="btn--base w-100 px-xxl-2"> <i class="la la-search" aria-hidden="true"></i> @lang('Search')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="container pt-120">
            <div class="row">
                <div class="col-lg-12">
                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md">
                                <table class="table custom--table mb-0">

                                    <thead>
                                        <tr>
                                            @if ($type == 'buy')
                                                <th>@lang('Seller')</th>
                                            @else
                                                <th>@lang('Buyer')</th>
                                            @endif
                                            <th>@lang('Payment method')</th>
                                            <th>@lang('Rate')</th>
                                            <th>@lang('Payment Window')</th>
                                            <th>@lang('Limit')</th>
                                            <th>@lang('Avg. Trade Speed')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($advertisements as $ad)
                                            <tr class="@if (auth()->id() == $ad->user_id) own-trade-color @endif">
                                                <td>
                                                    <a href="{{ route('public.profile', $ad->username) }}" class="text--base">{{ __($ad->username) }}</a>
                                                </td>

                                                <td>{{ __($ad->gateway_name) }}</td>

                                                <td class="fw-bold">{{ showAmount($ad->rate_value) }} {{ __($ad->fiat_code) }}/ {{ __($ad->crypto_code) }}</td>

                                                <td>{{ $ad->window }} @lang('Minutes')</td>

                                                <td>
                                                    {{ showAmount($ad->min) }} {{ __($ad->fiat_code) }} - {{ showAmount($ad->max) }} {{ __($ad->fiat_code) }}
                                                </td>

                                                <td>{{ avgTradeSpeed($ad) }}</td>

                                                <td>
                                                    @auth
                                                        <a href="{{ route('user.trade.request.new', $ad->id) }}" class="btn--base btn-sm">{{ __(ucfirst($type)) }}</a>
                                                    @else
                                                        <button class="btn--base btn-sm loginRequired">{{ __(ucfirst($type)) }}</button>
                                                    @endauth
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="text-center">
                                                    <x-no-data message="No advertisement found"></x-no-data>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @if ($advertisements->hasPages())
                        <div class="pagination-wrapper">
                            {{ $advertisements->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @include($activeTemplate . 'partials.login_required')
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.gateway').on('change', function() {
                var fiats = $(this).find('option:selected').data('fiat');
                var html = `<option value="all">@lang('All')</option>`;
                let oldCurrency="{{ @$request->currency }}"
                if (fiats && fiats.length > 0) {
                    $.each(fiats, function(i, v) {
                        html += `<option value="${v.code}" ${v.code == oldCurrency ? 'selected' : ''}>${v.code}</option>`;
                    });
                } else {
                    html = `<option value="">@lang('Select Fiat Currency')</option>`;
                }

                $('.currency').html(html);
            }).change();


            $('.buy--sell-filter').on('change input', function(e) {
                newUrl();
                return false;
            }).change();

            function newUrl() {

                let amount     = $('.buy--sell-filter').find(`.amount`).val();
                let gateway    = $('.buy--sell-filter').find(`.gateway`).val();
                let cryptoCode = $('.buy--sell-filter').find(`.crypto`).val();
                let type       = $('.buy--sell-filter').find(`.type`).val();
                let currency   = $('.buy--sell-filter').find(`.currency`).val();
                let location   = $('.buy--sell-filter').find(`.country`).val();

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
