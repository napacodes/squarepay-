@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-60 pb-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="custom--card rounded-5">
                        <div class="card-body p-0">
                            <div class="table-responsive table-responsive--md">
                                <table class="table custom--table mb-0">
                                    <thead>
                                        <tr>
                                            <th>@lang('With')</th>
                                            <th>@lang('Type')</th>
                                            <th>@lang('Currency')</th>
                                            <th>@lang('Payment Method')</th>
                                            <th>@lang('Rate')</th>
                                            <th>@lang('Crypto Amount')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Requested On')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tradeRequests as $trade)
                                            <tr>
                                                <td>
                                                    @if ($trade->buyer_id == auth()->user()->id)
                                                        {{ __($trade->seller->username) }}
                                                    @endif

                                                    @if ($trade->seller_id == auth()->user()->id)
                                                        {{ __($trade->buyer->username) }}
                                                    @endif
                                                </td>

                                                <td> @php echo $trade->typeBadge; @endphp </td>

                                                <td>{{ __($trade->fiat->code) }}</td>

                                                <td>{{ __($trade->fiatGateway->name) }}</b></td>

                                                <td>{{ showAmount($trade->exchange_rate, 2) }} {{ __($trade->fiat->code) }}/ {{ __($trade->crypto->code) }}</td>

                                                <td>
                                                    <span class="text--base">
                                                        {{ showAmount($trade->crypto_amount, 8) }} {{ __($trade->crypto->code) }}
                                                    </span>
                                                </td>

                                                <td>@php echo $trade->statusBadge @endphp</td>

                                                <td>{{ $trade->created_at->diffForHumans() }}</td>

                                                <td>
                                                    <a href="{{ route('user.trade.request.details', $trade->uid) }}" class="btn btn-outline--base"><i class="las la-desktop"></i> @lang('Details')</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (blank($tradeRequests))
                                    <x-no-data message="No trade found"></x-no-data>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @if ($tradeRequests->hasPages())
                <div class="pagination-wrapper">
                    {{ $tradeRequests->links() }}
                </div>
            @endif

        </div>
    </section>
@endsection
