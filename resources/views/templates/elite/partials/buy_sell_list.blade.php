@php
    $request        = request();
    $cryptos        = @$cryptos ?? App\Models\CryptoCurrency::active()->get();
    $cryptoFirst    = @$request->crypto ? @$cryptos->where('code',@$request->crypto)->first() : @$cryptos->first();
    $fiatGateways   = @$fiatGateways ?? App\Models\FiatGateway::getGateways();
    $countries      = @$countries ?? json_decode(file_get_contents(resource_path('views/partials/country.json')));
    $advertisements = @$advertisements ?? adsQuery(@$cryptoFirst->id, $type == 'buy' ? 2 : 1)->with('user')->orderBy('advertisements.id', 'desc')->paginate(getPaginate(6));
@endphp

@if (!request()->routeIs('home'))
@php
    $content = getContent('buy_sell.content', true);
@endphp
<section class="banner-section">
    <div class="banner-section__shape">
        <img alt="Banner Shape" src="{{ getImage($activeTemplateTrue . 'images/banner-shape.png') }}">
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="banner-content">
                    <h5 class="banner-content__subtitle">{{ __(@$content->data_values->heading) }}</h5>
                    <h2 class="banner-content__title" s-break="3" s-color="base-two" s-length="2">{{ __(@$content->data_values->subheading) }}</h2>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="banner-thumb">
                    <img alt="Banner Thumb" src="{{ frontendImage('buy_sell', @$content->data_values->image, '520x300') }}">
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<section class="ptable-header-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="ptable-header-left">
                    <div class="buy-sell-tab {{  $type }}">
                        <button type="button" class="buy-sell-tab__link buy btn  buy--sell-btn @if($type == 'buy')  active @endif" data-type="buy">
                            @lang('Buy')
                        </button>
                        <button type="button" class="buy-sell-tab__link sell btn buy--sell-btn @if($type == 'sell')  active @endif" data-type="sell">
                            @lang('Sell')
                        </button>
                    </div>
                    <div class="tag-list">
                        @foreach ($cryptos as $crypto)
                            <span class="tag-list__item @if (@$request->crypto == $crypto->code ) active @elseif(!@$request->crypto && $loop->first) active @endif crypto--symbol cursor-pointer"
                                data-id="{{ $crypto->id }}" data-code="{{ $crypto->code }}" data-name="{{ $crypto->name }}">
                                {{ __($crypto->code) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ptable-section pb-120">
    <div class="container">
        <div class="row ">
            <div class="col-12">
                <form action="#" method="get" class="ptable-filter row buy--sell-filter">
                    <div class="col-xl-3 col-sm-4">
                        <div class="form-group">
                            <input type="number" step="any" name="amount" placeholder="@lang('Enter amount')" class="form-control form--control currency" value="{{@$request->amount &&  @$request->amount > 0 ? getAMount(@$request->amount) : '' }}">
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-4 fiat--payment-gateway-wrapper">
                        <div class="form-group">
                            <select class="select form--control select2" name="fiat_gateway">
                                <option selected disabled>@lang('Select Payment Gateway')</option>
                                <option value="all" @selected(@$request->gateway == 'all')>@lang('All')</option>
                                @foreach ($fiatGateways as $gateway)
                                    <option data-fiat='@json(@$gateway->fiat)' value="{{ $gateway->slug }}"  @selected(@$request->gateway == @$gateway->slug)>
                                        {{ __($gateway->name) }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-sm-4">
                        <div class="form-group">
                            <select class="select form--control select2" name="fiat_currency">
                                <option selected disabled>@lang('Select Currency')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2  col-sm-4">
                        <div class="form-group">
                            <select class="select form--control select2" name="location">
                                <option selected disabled>@lang('Select Location')</option>
                                <option data-code="all" value="all">@lang('All')</option>
                                @foreach ($countries as $key => $country)
                                    <option value="{{$key}}" @selected(@$request->country == $key) >
                                        {{ __($country->country) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2  col-sm-6 filter--btn-wrapper">
                        <div class="form-group f-flex flex-align gap-3">
                            <button type="submit" class="btn btn--base filter--btn flex-fill" disabled>
                                <i class="las la-filter"></i>@lang('Filter')
                            </button>
                            <button type="button" class="btn btn--light flex-fill reset--btn d-none">
                                <i class="las la-redo-alt"></i> @lang('Reset')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex-fill">
                <div class="advertisements--list">
                    <div class="ptable-wrapper">
                        <table class="table table--responsive--lg" id="buy--sell-table">
                            <thead>
                                <tr>
                                    <th class="advertiser-user-heading">@lang('Seller')</th>
                                    <th>@lang('Payment method')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Limits')</th>
                                    <th>@lang('Avg. Trade Speed')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @include($activeTemplate . "advertisement.$type", [
                                    'ads' => $advertisements,
                                ])
                            </tbody>
                        </table>
                    </div>
                    <div class=" justify-content-center align-items-center currency-loading d-none advertisements--table-loader ">
                        <div class="spinner-border text--base" role="status"></div>
                    </div>
                </div>
                <div class="text-center mt-3 @if($advertisements->total() <= $take)  d-none @endif">
                    <a href="javascript:void(0)"  class="btn btn-outline--base btn--sm load--more-btn">
                        <i class="las la-bars"></i> @lang('Load More')
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@include($activeTemplate . 'partials.login_required')



@push('script')
    <script>
        "use strict";
        (function($) {

            let type                = "{{ $type }}";
            let id                  = "{{ @$cryptoFirst->id }}";
            let cryptoCode          = "{{ @$cryptoFirst->code }}";
            let cryptoName          = "{{ @$cryptoFirst->name }}";
            let take                = parseInt("{{ $take }}");
            let skip                = take;
            let isLoadMore          = true;
            let advertisementRoueIs = "{{ request()->routeIs('advertisement.all') }}";
            let siteName            = "{{ gs('site_name') }}";
            let amount, fiat_gateway, fiat_currency, location,action;

            function advertisementList() {
                formData();
                newUrl();
                $.ajax({
                    url: action,
                    method: "GET",
                    data: {
                        skip,
                        take
                    },
                    beforeSend: function() {
                        if(isLoadMore){
                            $('.load--more-btn').attr('disabled', true);
                            $('.load--more-btn').html(`
                              <div class="spinner-border text--base spinner-load-more" role="status"></div>
                            `);
                        }else{
                            $('#buy--sell-table').addClass('d-none');
                            $('.load--more-btn').addClass('d-none');
                            $('.advertisements--table-loader').addClass('d-flex').removeClass('d-none')
                        }
                    },
                    complete: function() {
                        if(isLoadMore){
                            $('.load--more-btn').attr('disabled', false);
                            $('.load--more-btn').html(`
                                <i class="las la-bars"></i> @lang('Load More')
                            `);
                        }else{
                            $('.load--more-btn').removeClass('d-none');
                            $('#buy--sell-table').removeClass('d-none');
                            $('.advertisements--table-loader').removeClass('d-flex').addClass('d-none')
                        }
                    },
                    success: function(resp) {
                        $('.empty--message').closest('tr').remove();
                        if (resp.success) {
                            if (isLoadMore) {
                                $('#buy--sell-table tbody').append(resp.html);
                            } else {
                                $('#buy--sell-table tbody').html(resp.html);
                            }
                            skip += take;
                            if (parseInt(resp.total) <= skip) {
                                $('.filter--btn').attr('disabled',true);
                                $('.load--more-btn').parent().addClass('d-none');
                                if(isLoadMore){
                                    $('body .empty--message').text("@lang('No more data found')");
                                }
                            } else {
                                $('.filter--btn').attr('disabled',false);
                                $('.load--more-btn').parent().removeClass('d-none');
                            }
                            tableResponsive();
                        }
                    },
                    error: function(e) {
                        notify("@lang('Something went to wrong')")
                    }
                });
            }

            $('.crypto--symbol').on('click', function(e) {
                $('.crypto--symbol').removeClass('active');
                $(this).addClass('active');
                id         = $(this).data('id');
                cryptoCode = $(this).data('code');
                cryptoName = $(this).data('name');
                resetVariable();
                advertisementList();
            });

            $('.buy--sell-btn').on('click', function(e) {
                $('.buy--sell-btn').removeClass('active');
                $(this).addClass('active');
                type = $(this).data('type');
                $('.advertiser-user-heading').text(type == 'buy' ? 'Seller' : "Buyer")
                resetVariable();
                advertisementList();
            });

            $('select[name=fiat_gateway]').on('change', function() {
                let fiats = $(this).find('option:selected').data('fiat');
                let html = `<option value="all" @selected(@$request->currency == 'all')>@lang('All')</option>`;
                let oldCurrency="{{ @$request->currency }}"
                $.each(fiats || [], function(i, v) {
                    html += `<option value="${v.code}" ${v.code == oldCurrency ? 'selected' : ''}>${v.code}</option>`;
                });
                $('.select[name=fiat_currency]').html(html);
            }).change();

            $('.buy--sell-filter').on('submit', function(e) {
                e.preventDefault();
                advertisementList();
            });

            $('.buy--sell-filter').on('change input', function(e) {

                $('.reset--btn').removeClass('d-none');
                $('.fiat--payment-gateway-wrapper').addClass('col-xl-2')
                $('.fiat--payment-gateway-wrapper').removeClass('col-xl-3');

                $('.filter--btn-wrapper').addClass('col-xl-3')
                $('.filter--btn-wrapper').removeClass('col-xl-2');

                $('.filter--btn').attr('disabled',false);
                resetVariable();
                formData();

            });

            $('.reset--btn').on('click', function(e) {
                $('.buy--sell-filter').trigger('reset');

                $('.fiat--payment-gateway-wrapper').addClass('col-xl-3')
                $('.fiat--payment-gateway-wrapper').removeClass('col-xl-2');

                $('.filter--btn-wrapper').addClass('col-xl-2')
                $('.filter--btn-wrapper').removeClass('col-xl-3');
                $(this).addClass('d-none');
                $('.filter--btn').attr('disabled',false)
                resetVariable();
                advertisementList();
            });

            function newUrl() {

                 action ="{{ route('advertisement.all', ['type' => ':type', 'crypto' => ':crypto', 'country' => ':country', 'gateway' => ':gateway', 'currency' => ':currency', 'amount' => ':amount']) }}";

                if (amount) {
                    action = action.replace(":amount", amount).replace(':currency', fiat_currency || 'all').replace(':gateway',fiat_gateway || 'all')
                } else {
                    action = action.replace(":amount", '')
                }

                action = action.replace(":country", location || 'all');

                if (fiat_currency) {
                    action = action.replace(':currency', fiat_currency || 'all').replace(':gateway', fiat_gateway || 'all');
                } else {
                    action = action.replace(':currency/', '')
                }

                if (fiat_gateway) {
                    action = action.replace(':gateway', fiat_gateway);
                } else {
                    action = action.replace(':gateway/', '');
                }

                action = action.replace(':type', type).replace(':crypto', cryptoCode);

                if(advertisementRoueIs){
                    document.title=`${siteName} - ${type.replace("b","B").replace('s',"S")} ${cryptoName}`;
                    window.history.pushState({}, '', action);
                    $('.load--more-btn').attr('href', "javascript:void(0)");
                }else{
                    $('.load--more-btn').attr('href', action);
                }
            };

            $('.load--more-btn').on('click', function(e) {
                isLoadMore = true;
                advertisementList();
            });

            function resetVariable(){
                isLoadMore = false;
                take       = "{{ $take }}";
                skip       = 0;
            }
            function formData () {
                amount        = $('.buy--sell-filter').find(`input[name=amount]`).val();
                fiat_gateway  = $('.buy--sell-filter').find(`select[name=fiat_gateway]`).val();
                fiat_currency = $('.buy--sell-filter').find(`select[name=fiat_currency]`).val();
                location      = $('.buy--sell-filter').find(`select[name=location]`).val();
            }

        })(jQuery);
    </script>
@endpush
