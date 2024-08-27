@extends($activeTemplate . 'layouts.master_without_menu')
@section('content')
    <div class="row gy-4">
        <div class="col-xl-4 col-md-6 d-widget-item">
            <div class="d-widget">
                <div class="d-widget__icon">
                    <i class="las la-wallet success"></i>
                </div>
                <div class="d-widget__content">
                    <p class="d-widget__caption">@lang('Current Balance')</p>
                    <h2 class="d-widget__amount">{{ showAmount($userBalance->balance, 8) }} {{ $crypto->code }}</h2>
                    <h6 class="d-widget__usd text--base">
                        @lang('In USD') <i class="las la-arrow-right"></i> {{ showAmount($userBalance->balanceInUsd) }}
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 d-widget-item">
            <div class="d-widget">
                <div class="d-widget__icon">
                    <i class="las la-percent warning"></i>
                </div>
                <div class="d-widget__content">
                    <p class="d-widget__caption">@lang('Withdraw Charge')</p>
                    <h2 class="d-widget__amount">
                        @if ($crypto->withdraw_charge_fixed > 0)
                            {{ $crypto->withdraw_charge_fixed }} {{ $crypto->code }} +
                        @endif {{ $crypto->withdraw_charge_percent }}%
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 d-widget-item">
            @php
                $charge = $crypto->withdraw_charge_fixed + ($userBalance->balance * $crypto->withdraw_charge_percent) / 100;
                $maxWithdrawAmount = $userBalance->balance - $charge;
            @endphp

            <div class="d-widget">
                <div class="d-widget__icon">
                    <i class="las la-coins dark"></i>
                </div>
                <div class="d-widget__content">
                    <p class="d-widget__caption">@lang('Withdrawal Limit')</p>
                    <h2 class="d-widget__amount">
                        @if ($maxWithdrawAmount > 0)
                            {{ showAmount($userBalance->balance - $charge, 8) }} {{ $crypto->code }}
                        @else
                            0.00000000 {{ $crypto->code }}
                        @endif
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('user.withdraw.store') }}" class="bitcoin-form mt-4" method="POST">
        @csrf
        <div class="bitcoin-form-wrapper">
            <h5 class="title">@lang('Make Withdrawal')</h5>
            <div class="row">
                <input name="crypto" type="hidden" value="{{ $crypto->code }}">
                <div class="col-lg-8">
                    <div class="form-group">
                        <input class="form--control" name="wallet" placeholder="@lang('Wallet Address')" required type="text">
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <input autocomplete="off" class="form--control" name="amount" placeholder="@lang('Withdraw Amount')" required step="any" type="number">
                        <small class="text--base"><span>@lang('Charge :')</span> <span class="withdraw-charge">0.00 {{ $crypto->code }}</span></small>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="text-end">
                        <button class="btn btn--base-two" type="submit">@lang('Request Withdraw')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row mt-3">
        <h4>@lang('Previous') {{ $crypto->code }} @lang('Withdrawals')</h4>
        <div class="col-xl-12">
            @include($activeTemplate . 'user.withdraw.withdrawals_table')
        </div>
    </div>

    {{-- Detail MODAL --}}
    <div class="modal fade" id="detailModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                        <i class="la la-times"></i>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="withdraw-detail"></div>

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
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
                modal.modal('show');
            });

            $('input[name="amount"]').on('input', function() {
                var value = $(this).val();
                var fixedCharge = '{{ $crypto->withdraw_charge_fixed }}';
                var percentCharge = '{{ $crypto->withdraw_charge_percent }}';

                var charge = parseFloat(fixedCharge) + (parseFloat(value) * parseFloat(percentCharge) / 100);
                if (charge) {
                    $('.withdraw-charge').text(parseFloat(charge).toFixed(8) + ' {{ $crypto->code }}');
                } else {
                    $('.withdraw-charge').text('0.00 {{ $crypto->code }}');
                }
            });
        })(jQuery);
    </script>
@endpush
