<div class="custom--card">
    <div class="card-body p-0">
        <div class="table-responsive--md">
            <table class="table custom--table mb-0">
                <thead>
                    <tr>
                        <th>@lang('Seller')</th>
                        <th>@lang('Payment method')</th>
                        <th>@lang('Rate')</th>
                        <th>@lang('Limits')</th>
                        <th>@lang('Avg. Trade Speed')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($ads as $ad)
                        <tr class="@if (auth()->id() == $ad->user_id) own-trade-color @endif">
                            <td>
                                <a href="{{ route('public.profile', $ad->username) }}" class="text--base">{{ __($ad->username) }}</a>
                            </td>

                            <td> {{ __($ad->gateway_name) }}</td>

                            <td><b>{{ showAmount($ad->rate_value) }} {{ __($ad->fiat_code) }}/ {{ __($ad->crypto_code) }}</b></td>

                            <td>{{ showAmount($ad->min) }} - {{ showAmount($ad->max_limit) }} {{ __($ad->fiat_code) }}</td>

                            <td>{{ avgTradeSpeed($ad) }}</td>

                            <td>
                                @auth
                                    <a href="{{ route('user.trade.request.new', $ad->id) }}" class="btn--base btn-sm">@lang('Buy')</a>
                                @else
                                    <button class="btn--base btn-sm loginRequired">@lang('Buy')</button>
                                @endauth
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text-center">@lang('No data found')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
