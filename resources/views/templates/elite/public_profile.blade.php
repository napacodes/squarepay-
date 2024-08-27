@extends($activeTemplate . 'layouts.master_without_menu')
@section('content')
    @php $profileImage = fileManager()->userProfile(); @endphp
    
    <form  method="GET">
        <div class="d-flex flex-wrap gap-1">
            <div class="flex-fill">
                <div class="form-group">
                    <label class="form--label">@lang('Crypto currency')</label>
                    <select class="select form--control select2" name="crypto">
                        <option value="">@lang('All')</option>
                        @foreach ($cryptos as $cryptoData)
                            <option @selected(request()->crypto == $cryptoData->id) value="{{ $cryptoData->id }}">
                                {{ __($cryptoData->code) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex-fill">
                <div class="form-group">
                    <label class="form--label">@lang('Payment Method')</label>
                    <select class="select form--control select2" name="fiat_gateway">
                        <option disabled selected value="">@lang('Select One')</option>
                        @foreach ($fiatGateways as $gateway)
                            <option @selected(request()->fiat_gateway == $gateway->id) value="{{ $gateway->id }}">
                                {{ __($gateway->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex-fill">
                <div class="form-group">
                    <label class="form--label">@lang('Amount')</label>
                    <input class="form--control" name="amount" step="any" type="number" value="{{ request()->amount }}">
                </div>
            </div>
            <div class="d-flex align-items-end">
                <div class="form-group">
                    <button class="btn btn--base-two w-100" type="submit">@lang('Search')</button>
                </div>
            </div>
        </div>
    </form>

    <div class="pt-60">
        <div class="row gy-4 justify-content-center">
            <div class="col-xl-3 col-lg-4">
                <div class="profile-setting-sidebar">
                    <div class="fileinput fileinput-new " data-provides="fileinput">
                        <div class="fileinput-new thumbnail" data-trigger="fileinput">
                            <img alt="Image" src="{{ getImage($profileImage->path . '/' . @$user->image, null, true) }}">
                        </div>
                    </div>
                    <ul class="caption-list">
                        <li class="caption-list__item">
                            <span class="caption">@lang('Name')</span>
                            <span class="value">{{ $user->username }}</span>
                        </li>

                        <li class="caption-list__item">
                            <span class="caption">@lang('Feedbacks')</span>
                            <span class="value d-flex gap-3">
                                <span><i class="fa fa-thumbs-up text--success"></i> ({{ $positiveFeedbacks }})</span>
                                <span>
                                    <i class="fa fa-thumbs-down text--danger"></i> ({{ $negativeFeedBacks }})
                                </span>
                            </span>
                        </li>

                        <li class="caption-list__item">
                            <span class="caption">@lang('Country')</span>
                            <span class="value">{{ $user->address->country ?? 'Unknown' }}</span>
                        </li>

                        <li class="caption-list__item">
                            <span class="caption">@lang('Joined')</span>
                            <span class="value">{{ @$user->created_at->diffForHumans() }}</span>
                        </li>

                        <li class="caption-list__item">
                            <span class="caption">@lang('Email')</span>
                            @if ($user->ev)
                                <span class="value"><span class="badge badge--success">@lang('Verified')</span></span>
                            @else
                                <span class="value"><span class="badge badge--danger">@lang('Unverified')</span></span>
                            @endif
                        </li>

                        <li class="caption-list__item">
                            <span class="caption">@lang('Phone')</span>
                            @if ($user->sv)
                                <span class="value"><span class="badge badge--success">@lang('Verified')</span></span>
                            @else
                                <span class="value"><span class="badge badge--danger">@lang('Unverified')</span></span>
                            @endif
                        </li>

                        <li class="caption-list__item">
                            <span class="caption">@lang('ID')</span>
                            @if ($user->kv == 1)
                                <span class="value"><span class="badge badge--success">@lang('Verified')</span></span>
                            @else
                                <span class="value"><span class="badge badge--danger">@lang('Unverified')</span></span>
                            @endif
                        </li>

                        <li class="caption-list__item">
                            <span class="caption">@lang('Trades')</span>
                            <span class="value">{{ $user->completed_trade }}</span>
                        </li>

                        <li class="caption-list__item">
                            <span class="caption">@lang('Avg. Speed')</span>
                            <span class="value">
                                @if ($user->completed_trade)
                                    {{ round($user->total_min / $user->completed_trade) }} @lang('Minutes')
                                @else
                                    @lang('No trades yet')
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-xl-9 col-lg-8">
                <div class="row gy-4">
                    <h4 class="mb-0">@lang('Latest Buy Ads')</h4>
                    <div class="ptable-wrapper">
                        <table class="table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('Payment method')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Limits')</th>
                                    <th>@lang('Avg. Trade Speed')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $i = 0; @endphp
                                
                                @foreach ($latestBuyAds as $ad)
                                    @php $maxLimit = getMaxLimit($ad->user->wallets, $ad); @endphp
                                    
                                    @if ($maxLimit >= $ad->min)
                                        @php
                                            $i++;
                                            
                                        @endphp
                                        <tr>
                                            <td>{{ __($ad->fiatGateway->name) }}</td>

                                            <td>
                                                <b>{{ getRate($ad) }} {{ __($ad->fiat->code) }}/ {{ __($ad->crypto->code) }}</b>
                                            </td>
                                            
                                            <td>
                                                {{ showAmount($ad->min) }} - {{ showAmount($maxLimit) }} {{ __($ad->fiat->code) }}
                                            </td>

                                            <td>{{ avgTradeSpeed($ad) }}</td>

                                            <td>
                                                <a class="btn btn--base-two btn--sm" href="{{ route('user.trade.request.new', $ad->id) }}">@lang('Buy')</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                @if (!$i)
                                    <td class="text-center" colspan="100%">@lang('No data found')</td>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <h4 class="mb-0">@lang('Latest Sell Ads')</h4>

                    <div class="ptable-wrapper">
                        <table class="table table--responsive--lg">
                            <thead>
                                <tr>
                                    <th>@lang('Payment method')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Limits')</th>
                                    <th>@lang('Avg. Trade Speed')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestSellAds as $ad)
                                    <tr>
                                        <td>{{ __($ad->fiatGateway->name) }}</td>

                                        <td><b>{{ getRate($ad) }} {{ __($ad->fiat->code) }}/ {{ __($ad->crypto->code) }}</b></td>

                                        <td>{{ showAmount($ad->min) }} - {{ showAmount($ad->max) }} {{ __($ad->fiat->code) }}</td>

                                        <td>{{ avgTradeSpeed($ad) }}</td>

                                        <td>
                                            <a class="btn btn--base-two btn--sm" href="{{ route('user.trade.request.new', $ad->id) }}">@lang('Sell') </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="100%">@lang('No data found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
