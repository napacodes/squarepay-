@forelse ($ads as $ad)
    <tr class="@if (auth()->id() == $ad->user_id) own-trade-color @endif">
        <td>

            <div class="customer flex-align">
                <div class="customer__thumb">
                    <img src="{{ getImage(getFilePath('userProfile') . '/' . @$ad->user->image, null, true) }}"
                        class="fit-image">
                </div>
                <div class="customer__content">
                    <h6 class="customer__name">
                        <a class="text--base" href="{{ route('public.profile', $ad->username) }}">{{ __(@$ad->user->fullname) }}</a>
                    </h6>
                    <span class="info">
                        @lang('Trades'): {{ getAmount($ad->total_trade) }} | 
                        <span title="@lang('Trade Completion Rate')">
                            {{ getAmount( $ad->total_trade > 0 ? (($ad->trade_requests_count/$ad->total_trade)*100) : 0) }}%
                        </span>
                    </span>
                </div>
            </div>
        </td>
        <td>{{ __($ad->gateway_name) }}</td>
        <td><b>{{ (float) $ad->rate_value }} {{ __($ad->fiat_code) }}/ {{ __($ad->crypto_code) }}</b></td>
        <td>{{ showAmount($ad->min) }} - {{ showAmount($ad->max) }} {{ __($ad->fiat_code) }}</td>
        <td>
            {{ avgTradeSpeed($ad) }}
        </td>
        <td>
            @auth
                <a class="btn btn--danger btn--sm" href="{{ route('user.trade.request.new', $ad->id) }}">
                    @lang('Sell') {{ __($ad->crypto_code) }}
                </a>
            @else
                <button class="btn btn--danger btn--sm loginRequired">
                    @lang('Sell') {{ __($ad->crypto_code) }}
                </button>
            @endauth
        </td>
    </tr>
@empty
    <tr>
        <td class="text-center" colspan="100%">
            <div class="p-5 text-center">
                <img src="{{ getImage($activeTemplateTrue . '/images/icon/empty.png') }}">
                <span class="text-muted fs-13 d-block empty--message">@lang('No data found')</span>
            </div>
        </td>
    </tr>
@endforelse
