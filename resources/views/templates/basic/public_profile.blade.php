@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php $profileImage = fileManager()->userProfile(); @endphp
    <div class="section--bg pb-60">
        <div class="coin-search-area">
            <div class="container">
                <form class="coin-search-form text-center"  method="GET">
                    <div class="row align-items-end justify-content-center">
                        <div class="col-xxl-3 col-md-3 col-sm-6 form-group">
                            <label class="float-start">@lang('Crypto currency')</label>
                            <select class="select select2" name="crypto">
                                <option value="">@lang('All')</option>
                                @foreach ($cryptos as $cryptoData)
                                    <option value="{{ $cryptoData->id }}" @selected(request()->crypto == $cryptoData->id)>
                                        {{ __($cryptoData->code) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xxl-3 col-md-3 col-sm-6 form-group">
                            <label class="float-start">@lang('Payment Method')</label>
                            <select class="select select2" name="fiat_gateway">
                                <option value="" selected disabled>@lang('Select One')</option>
                                @foreach ($fiatGateways as $gateway)
                                    <option value="{{ $gateway->id }}" @selected(request()->fiat_gateway == $gateway->id)>
                                        {{ __($gateway->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xxl-3 col-md-3 col-sm-6 form-group">
                            <label class="float-start">@lang('Amount')</label>
                            <input type="number" step="any" name="amount" value="{{ request()->amount }}" class="form-control">
                        </div>
                        <div class="col-xxl-2 col-md-2 col-sm-6 form-group">
                            <button type="submit" class="btn--base w-100">@lang('Search')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="container pt-60">
            <div class="row justify-content-center">
                <div class="col-xl-3 col-lg-4">
                    <div class="profile-setting-sidebar">
                        <div class="fileinput fileinput-new " data-provides="fileinput">
                            <div class="fileinput-new thumbnail" data-trigger="fileinput">
                                <img src="{{ getImage($profileImage->path . '/' . @$user->image, null, true) }}" alt="Image">
                            </div>
                        </div>
                        <ul class="caption-list mt-4">
                            <li>
                                <span class="caption">@lang('Name')</span>
                                <span class="value">{{ $user->username }}</span>
                            </li>

                            <li>
                                <span class="caption">@lang('Feedbacks')</span>
                                <span class="value d-flex gap-3">
                                    <span><i class="fa fa-thumbs-up text--success"></i> ({{ $positiveFeedbacks }})</span>
                                    <span>
                                        <i class="fa fa-thumbs-down text--danger"></i> ({{ $negativeFeedBacks }})
                                    </span>
                                </span>
                            </li>

                            <li>
                                <span class="caption">@lang('Country')</span>
                                <span class="value">{{ $user->country_name ?? 'Unknown' }}</span>
                            </li>

                            <li>
                                <span class="caption">@lang('Joined')</span>
                                <span class="value">{{ @$user->created_at->diffForHumans() }}</span>
                            </li>

                            <li>
                                <span class="caption">@lang('Email')</span>
                                @if ($user->ev)
                                    <span class="value"><span class="badge badge--success">@lang('Verified')</span></span>
                                @else
                                    <span class="value"><span class="badge badge--danger">@lang('Unverified')</span></span>
                                @endif
                            </li>

                            <li>
                                <span class="caption">@lang('Phone')</span>
                                @if ($user->sv)
                                    <span class="value"><span class="badge badge--success">@lang('Verified')</span></span>
                                @else
                                    <span class="value"><span class="badge badge--danger">@lang('Unverified')</span></span>
                                @endif
                            </li>

                            <li>
                                <span class="caption">@lang('ID')</span>
                                @if ($user->kv == 1)
                                    <span class="value"><span class="badge badge--success">@lang('Verified')</span></span>
                                @else
                                    <span class="value"><span class="badge badge--danger">@lang('Unverified')</span></span>
                                @endif
                            </li>

                            <li>
                                <span class="caption">@lang('Trades')</span>
                                <span class="value">{{ $user->completed_trade }}</span>
                            </li>

                            <li>
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

                <div class="col-xl-9 col-lg-8 mt-lg-0 mt-4">
                    <h4 class="my-3">@lang('Latest Buy Ads')</h4>
                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md ">
                                <table class="table custom--table mb-0 bg-white">
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
                                                        <a href="{{ route('user.trade.request.new', $ad->id) }}" class="btn--base btn-sm">@lang('Buy')</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        @if (!$i)
                                            <td colspan="100%" class="text-center">@lang('No data found')</td>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h4 class="my-3">@lang('Latest Sell Ads')</h4>

                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md">
                                <table class="table custom--table mb-0">
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
                                                    <a href="{{ route('user.trade.request.new', $ad->id) }}" class="btn--base btn-sm">@lang('Sell') </a>
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
                </div>
            </div>
        </div>
    </div>
@endsection
