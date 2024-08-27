@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Buyer')</th>
                                    <th>@lang('Seller')</th>
                                    <th>@lang('Unique ID')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Exchange Rate')</th>
                                    <th>@lang('Crypto Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($trades as $trade)
                                    <tr>
                                        <td>{{ $trades->firstItem() + $loop->index }}</td>

                                        <td>
                                            <span>{{ $trade->buyer->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $trade->buyer->id) }}"><span>@</span>{{ $trade->buyer->username }}</a>
                                            </span>
                                        </td>

                                        <td>
                                            <span>{{ $trade->seller->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', $trade->seller->id) }}"><span>@</span>{{ $trade->seller->username }}</a>
                                            </span>
                                        </td>

                                        <td><b>{{ $trade->uid }}</b></td>

                                        <td><b>{{ showAmount($trade->amount, 2) }}</b> {{ __($trade->fiat->code) }}</td>

                                        <td>
                                            <span class="text--cyan">{{ __($trade->fiatGateway->name) }}</span><br>
                                        </td>

                                        <td>
                                            <span>{{ showAmount($trade->exchange_rate) }}</span> <span class="small">{{ __($trade->fiat->code) }}</span>
                                            <br>
                                            /{{ __($trade->crypto->code) }}
                                        </td>
                                        <td>
                                            <span class="text--primary">
                                                {{ showAmount($trade->crypto_amount, 8) }} {{ __($trade->crypto->code) }}
                                            </span>
                                        </td>
                                        <td>@php echo $trade->statusBadge @endphp</td>
                                        <td>
                                            <a href="{{ route('admin.trade.details', $trade->id) }}" class="btn btn-sm btn-outline--primary ms-1"><i class="las la-desktop"></i> @lang('Details')</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($trades->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($trades) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
        <x-search-form placeholder="Unique ID / Seller / Buyer"></x-search-form>
    </div>
@endpush
