@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pb-60">
        <div class="coin-search-area">
            <div class="container">
                <form class="coin-search-form text-center"  method="GET">
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <div class="flex-fill">
                            <select class="select select2" name="crypto_currency_id">
                                <option value="">@lang('All')</option>
                                @foreach ($cryptos as $crypto)
                                    <option value="{{ $crypto->id }}" @selected(request()->crypto_currency_id == $crypto->id)>{{ __($crypto->code) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-fill">
                            <input type="text" name="search" value="{{ request()->search }}" class="form-control" placeholder="@lang('TRX No.')">
                        </div>
                        <div class="flex-fill">
                            <button type="submit" class="btn--base w-100"><i class="la la-search"></i> @lang('Search')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="container pt-60">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md">
                                <table class="table custom--table">
                                    <thead>
                                        <tr>
                                            <th>@lang('Cryptocurrency')</th>
                                            <th>@lang('TRX No.')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Time')</th>
                                            <th> @lang('More')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deposits as $deposit)
                                            <tr>
                                                <td><span class="text--base">{{ __($deposit->crypto->code) }}</span></td>
                                                <td>{{ $deposit->trx }}</td>
                                                <td>
                                                    <strong>{{ showAmount($deposit->amount, 8) }} {{ __($deposit->crypto->code) }}</strong>
                                                </td>
                                                <td>
                                                    @if ($deposit->status == Status::PAYMENT_SUCCESS)
                                                        <span class="badge badge--success">@lang('Completed')</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ showDateTime($deposit->created_at) }}
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)" class="btn btn-outline--base approveBtn" data-id="{{ $deposit->id }}" data-amount="{{ showAmount($deposit->amount, 8) }} {{ __($deposit->crypto->code) }}" data-charge="{{ showAmount($deposit->charge, 8) }} {{ __($deposit->crypto->code) }}" data-after_charge="{{ showAmount($deposit->amount + $deposit->charge, 8) }} {{ __($deposit->crypto->code) }}" data-payable="{{ showAmount($deposit->final_amo, 8) }} {{ __($deposit->crypto->code) }}">
                                                        <i class="las la-desktop"></i> @lang('Details')
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if (blank($deposits))
                                <x-no-data message="No deposit yet"></x-no-data>
                            @endif
                        </div>
                    </div>

                    @if ($deposits->hasPages())
                        <div class="pagination-wrapper">
                            {{ $deposits->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- APPROVE MODAL --}}
    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="caption-list">
                        <li>
                            <span class="caption">@lang('Amount')</span>
                            <span class="value withdraw-amount"></span>
                        </li>
                        <li>
                            <span class="caption">@lang('Charge')</span>
                            <span class="value withdraw-charge "></span>
                        </li>
                        <li>
                            <span class="caption">@lang('After Charge')</span>
                            <span class=" value withdraw-after_charge"></span>
                        </li>
                        <li>
                            <span class="caption">@lang('Payable Amount')</span>
                            <span class="value withdraw-payable"></span>
                        </li>
                    </ul>
                    <ul class="caption-list withdraw-detail mt-2">
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.approveBtn').on('click', function() {

                var modal = $('#approveModal');
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-charge').text($(this).data('charge'));
                modal.find('.withdraw-after_charge').text($(this).data('after_charge'));
                modal.find('.withdraw-payable').text($(this).data('payable'));

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
