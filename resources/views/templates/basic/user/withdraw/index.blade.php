@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-60 pb-60 section--bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="d-widget2">
                        <div class="icon">
                            <i class="las la-wallet"></i>
                        </div>
                        <div class="content">
                            <span class="d-widget2__caption">@lang('Current Balance')</span>
                            <h4 class="d-widget2__amount">{{ showAmount($userBalance->balance, 8) }} {{ $crypto->code }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mt-md-0 mt-4">
                    <div class="d-widget2">
                        <div class="icon">
                            <i class="las la-percent"></i>
                        </div>
                        <div class="content">
                            <span class="d-widget2__caption">@lang('Withdraw Charge')</span>
                            <h4 class="d-widget2__amount">
                                @if ($crypto->withdraw_charge_fixed > 0)
                                    {{ $crypto->withdraw_charge_fixed }} {{ $crypto->code }} +
                                @endif {{ $crypto->withdraw_charge_percent }}%
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mt-lg-0 mt-4">
                    <div class="d-widget2">
                        <div class="icon">
                            <i class="las la-coins"></i>
                        </div>
                        <div class="content">
                            @php
                                $charge = $crypto->withdraw_charge_fixed + ($userBalance->balance * $crypto->withdraw_charge_percent) / 100;
                                $maxWithdrawAmount = $userBalance->balance - $charge;
                            @endphp

                            <span class="d-widget2__caption">@lang('Withdrawal Limit')</span>
                            <h4 class="d-widget2__amount">
                                @if ($maxWithdrawAmount > 0)
                                    {{ showAmount($userBalance->balance - $charge, 8) }} {{ $crypto->code }}
                                @else
                                    0.00000000 {{ $crypto->code }}
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <form class="bitcoin-form mt-4" action="{{ route('user.withdraw.store') }}" method="POST">
                @csrf
                <div class="bitcoin-form-wrapper p-md-4 p-3 mb-5">
                    <h5 class="title text-white mb-3">@lang('Make Withdrawal')</h5>

                    <div class="row">
                        <input type="hidden" name="crypto" value="{{ $crypto->code }}">

                        <div class="col-lg-8">
                            <div class="form-group">
                                <input type="text" name="wallet" class="form-control" placeholder="@lang('Wallet Address')" required>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <input type="number" step="any" name="amount" class="form-control" placeholder="@lang('Withdraw Amount')" autocomplete="off" required>
                                <small class="text--base"><span>@lang('Charge :')</span> <span class="withdraw-charge">0.00 {{ $crypto->code }}</span></small>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <button type="submit" class="btn--base w-100">@lang('Request Withdraw')</button>
                        </div>
                    </div>
                </div>
            </form>

            <h4>@lang('Previeous') {{ $crypto->code }} @lang('Withdrawals')</h4>

            <div class="row mt-3">
                <div class="col-xl-12">
                    @include($activeTemplate . 'user.withdraw.withdrawals_table')
                </div>
            </div>
        </div>
    </section>

    {{-- Detail MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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
