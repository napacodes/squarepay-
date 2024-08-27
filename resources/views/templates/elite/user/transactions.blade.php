@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form  class="ptable-filter align-items-end" id="searchForm" method="GET">
                <div class="d-flex gap-3 flex-wrap align-items-end justify-content-center mb-3">
                    <div class="flex-fill">
                        <div class="">
                            <label class="form--label">@lang('TRX No.')</label>
                            <input class="form-control form--control" name="search" placeholder="@lang('Transaction No.')" type="text" value="{{ request()->search }}">
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="">
                            <label class="form--label">@lang('Type')</label>
                            <select class="select form--control select2" name="type" data-minimum-results-for-search="-1"\>
                                <option value="">@lang('All')</option>
                                <option @selected(request()->type == '+') value="+">@lang('Plus')</option>
                                <option @selected(request()->type == '-') value="-">@lang('Minus')</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="">
                            <label class="form--label">@lang('Crypto currency')</label>
                            <select class="select form--control select2" name="crypto">
                                <option value="">@lang('All')</option>
                                @foreach ($cryptos as $cryptoData)
                                    <option @selected(request()->crypto == $cryptoData->id) value="{{ $cryptoData->id }}">{{ __($cryptoData->code) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex-fill">
                        <div class="">
                            <label class="form--label">@lang('Remark')</label>
                            <select class="select form--control select2" name="remark" data-minimum-results-for-search="-1">
                                <option value="">@lang('Any')</option>
                                @foreach ($remarks as $remark)
                                    <option @selected(request()->remark == $remark->remark) value="{{ $remark->remark }}">{{ __(keyToTitle($remark->remark)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex-fill ">
                        <button class="btn btn--base" type="submit"><i class="las la-filter"></i> @lang('Filter')</button>
                        <a class="btn btn--light clearBtn" href="{{ route('user.transactions') }}"><i class="las la-undo-alt"></i> @lang('Reset')</a>
                    </div>
            </form>
        </div>
        @if (blank($transactions))
            <div class="col-12">
                <x-no-data message="No transaction found"></x-no-data>
            </div>
        @else
            <div class="col-lg-12">
                <div class="table-acordion-wrapper mt-4">
                    <div class="accordion table--acordion" id="transactionAccordion">
                        @foreach ($transactions as $transaction)
                            <div class="accordion-item transaction-item">
                                <h2 class="accordion-header" id="h-{{ $transaction->id }}">
                                    <button class="accordion-button collapsed" data-bs-target="#id-{{ $transaction->id }}" data-bs-toggle="collapse" type="button">
                                        <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                            <div class="left">
                                                <div class="icon tr-icon icon-success ">
                                                    @if ($transaction->trx_type == '+')
                                                    <i class="las la-long-arrow-alt-right"></i>
                                                    @else
                                                    <i class="las la-long-arrow-alt-left"></i>
                                                    @endif
                                                </div>
                                                <div class="content">
                                                    <h6 class="trans-title">{{ __($transaction->crypto->code) }}</h6>
                                                    <span class="time text-muted mt-2">{{ showDateTime($transaction->created_at, 'M d Y @g:i:sa') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper">
                                            <p class="trx-no text-muted"><b>#{{ $transaction->trx }}</b></p>
                                        </div>
                                        <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                            <p class="btc">
                                                <b>{{ showAmount($transaction->amount, 8) }} {{ __($transaction->crypto->code) }}</b><br>
                                                <small class="fw-bold text-muted">{{ showAmount($transaction->post_balance, 8) }} {{ __($transaction->crypto->code) }}</small>
                                            </p>

                                        </div>
                                    </button>
                                </h2>
                                <div aria-labelledby="h-{{ $transaction->id }}" class="accordion-collapse collapse" data-bs-parent="#transactionAccordion" id="id-{{ $transaction->id }}">
                                    <div class="accordion-body">
                                        <ul class="caption-list">
                                            <li>
                                                <span class="caption">@lang('Charge')</span>
                                                <span class="value">{{ showAmount($transaction->charge, 8) }} {{ __($transaction->crypto->code) }}</span>
                                            </li>
                                            <li>
                                                <span class="caption">@lang('Post Balance')</span>
                                                <span class="value">{{ showAmount($transaction->post_balance, 8) }} {{ __($transaction->crypto->code) }}</span>
                                            </li>
                                            <li>
                                                <span class="caption">@lang('Details')</span>
                                                <span class="value">{{ __($transaction->details) }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($transactions->hasPages())
                    {{ $transactions->links() }}
                @endif
            </div>
        @endif
    </div>
@endsection
