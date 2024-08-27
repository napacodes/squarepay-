@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pb-60">
        <div class="coin-search-area">
            <div class="container">
                <form class="coin-search-form text-center"  method="GET">
                    <div class="d-flex gap-3 flex-wrap align-items-end justify-content-center">
                        <div class="flex-fill">
                            <label class="float-start">@lang('TRX No.')</label>
                            <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="@lang('Trannsaction No.')">
                        </div>
                        <div class="flex-fill">
                            <label class="float-start">@lang('Type')</label>
                            <select class="select select2" name="type" data-minimum-results-for-search="-1">
                                <option value="">@lang('All')</option>
                                <option value="+" @selected(request()->type == '+')>@lang('Plus')</option>
                                <option value="-" @selected(request()->type == '-')>@lang('Minus')</option>
                            </select>
                        </div>
                        <div class="flex-fill">
                            <label class="float-start">@lang('Crypto currency')</label>
                            <select class="select select2" name="crypto">
                                <option value="">@lang('All')</option>
                                @foreach ($cryptos as $cryptoData)
                                    <option value="{{ $cryptoData->id }}" @selected(request()->crypto == $cryptoData->id)>{{ __($cryptoData->code) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-fill">
                            <label class="float-start">@lang('Remark')</label>
                            <select class="select select2" name="remark" data-minimum-results-for-search="-1">
                                <option value="">@lang('Any')</option>
                                @foreach ($remarks as $remark)
                                    <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-fill">
                            <button type="submit" class="btn--base w-100"> <i class="la la-search"></i> @lang('Search')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="container pt-sm-5 pt-4">
            <div class="row">
                <div class="col-lg-12">

                    <div class="custom--card rounded-5">
                        <div class="card-body p-0">
                            <div class="table-responsive table-responsive--md">
                                <table class="table custom--table mb-0">
                                    <thead>
                                        <tr>
                                            <th>@lang('Cryptocurrency')</th>
                                            <th>@lang('TRX No.')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Charge')</th>
                                            <th>@lang('Post balance')</th>
                                            <th>@lang('Details')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $transaction)
                                            <tr>
                                                <td><span class="text--base">{{ __($transaction->crypto->code) }}</span></td>
                                                <td>{{ $transaction->trx }}</td>
                                                <td>
                                                    <span class="fw-bold @if ($transaction->trx_type == '+') text--success @else text-danger @endif">
                                                        {{ showAmount($transaction->amount, 8) }}
                                                    </span>
                                                </td>
                                                <td>{{ showAmount($transaction->charge, 8) }}</td>
                                                <td>{{ showAmount($transaction->post_balance, 8) }}</td>
                                                <td>{{ __($transaction->details) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @if (blank($transactions))
                                    <x-no-data message="No transaction found"></x-no-data>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($transactions->hasPages())
                <div class="pagination-wrapper">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
