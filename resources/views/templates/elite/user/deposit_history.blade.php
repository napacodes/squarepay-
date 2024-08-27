@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    <div class="row gy-4">
        <div class="col-lg-12">
            <form  class="coin-search-form text-center" method="GET">
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <div class="flex-fill">
                        <select class="select form--control select2" name="crypto">
                            <option value="">@lang('All')</option>
                            @foreach ($cryptos as $crypto)
                                <option @selected(request()->crypto == $crypto->id) value="{{ $crypto->id }}">{{ __($crypto->code) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-fill">
                        <input class="form--control" name="search" placeholder="@lang('TRX No.')" type="text" value="{{ request()->search }}">
                    </div>
                    <div>
                        <button class="btn btn--base-two w-100" type="submit"><i class="la la-search"></i> @lang('Search')</button>
                    </div>
                </div>
            </form>
        </div>

        @if (blank($deposits))
            <x-no-data message="No deposit yet"></x-no-data>
        @else
            <div class="col-lg-12">
                <div class="ptable-wrapper">
                    <table class="table table--responsive--lg">
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
                                        <a class="btn btn--sm btn-outline--base-two approveBtn" data-after_charge="{{ showAmount($deposit->amount + $deposit->charge, 8) }} {{ __($deposit->crypto->code) }}" data-amount="{{ showAmount($deposit->amount, 8) }} {{ __($deposit->crypto->code) }}" data-charge="{{ showAmount($deposit->charge, 8) }} {{ __($deposit->crypto->code) }}" data-id="{{ $deposit->id }}" data-payable="{{ showAmount($deposit->final_amo, 8) }} {{ __($deposit->crypto->code) }}" href="javascript:void(0)">
                                            <i class="las la-desktop"></i> @lang('Details')
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($deposits->hasPages())
                    <div class="pagination-wrapper">
                        {{ $deposits->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- APPROVE MODAL --}}
    <div class="modal custom--modal fade" id="approveModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"><i class="fas fa-times"></i></button>
                <div class="modal-body">
                    <h4 class="notice-text">@lang('Details')</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                            <span class="caption">@lang('Amount')</span>
                            <span class="value withdraw-amount"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                            <span class="caption">@lang('Charge')</span>
                            <span class="value withdraw-charge "></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
                            <span class="caption">@lang('After Charge')</span>
                            <span class=" value withdraw-after_charge"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between flex-wrap gap-1 px-0">
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
