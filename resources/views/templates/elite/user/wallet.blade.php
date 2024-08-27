@extends($activeTemplate . 'layouts.master_without_menu')
@section('content')
    @php
        $wallet = $wallets->where('crypto_currency_id', @$crypto->id)->first();
    @endphp

    @if (blank($cryptoWallets))
        <x-no-data message="No wallet found"></x-no-data>
    @else
        <div class="row">
            <div class="col-lg-12">
                <div class="ptable-wrapper">
                    <table class="table table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('Currency')</th>
                                <th>@lang('Generated at')</th>
                                <th>@lang('Wallet Address')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cryptoWallets as $cryptoWallet)
                                <tr>
                                    <td>{{ $cryptoWallet->crypto->code }}</td>
                                    <td>{{ showDateTime($cryptoWallet->created_at) }}</b></td>
                                    <td class="copy-text">{{ $cryptoWallet->wallet_address }}</td>
                                    <td>
                                        <button class="btn btn-outline--base-two btn--sm copy-address" type="button"><i class="las la-copy"></i> @lang('Copy')</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($cryptoWallets->hasPages())
            <div class="pagination-wrapper">
                {{ $cryptoWallets->links() }}
            </div>
        @endif
    @endif
@endsection

@if (Request::routeIs('user.wallets.single') && $wallet != null)
    @push('breadcrumb-plugins')
        <a class="ptable-header-right__link" href="{{ route('user.wallets.generate', $wallet->crypto->code) }}">
            <span class="icon"><i class="las la-plus"></i></span>
            <span class="text"> @lang('Generate New') {{ $wallet->crypto->code }} @lang('Address')</span>
        </a>
        <a class="ptable-header-right__link" href="{{ route('user.withdraw', $wallet->crypto->code) }}">
            <span class="icon"><i class="las la-credit-card"></i></span>
            <span class="text"> @lang('Withdraw') {{ $wallet->crypto->code }}</span>
        </a>
    @endpush

    @push('subtitle')
        <small class="text--danger">@lang('Deposit Charge is') @if ($wallet->crypto->deposit_charge_fixed > 0)
                {{ $wallet->crypto->deposit_charge_fixed }} {{ $wallet->crypto->code }} +
            @endif {{ $wallet->crypto->deposit_charge_percent }}%
        </small>
    @endpush
@endif

@push('script')
    <script>
        "use strict";
        $(document).ready(function() {
            $('.copy-address').on('click', function(event) {
                event.preventDefault();
                let textToCopy = $(this).closest('tr').find('.copy-text').text();

                let tempTextArea = $('<textarea>');
                tempTextArea.val(textToCopy);
                $('body').append(tempTextArea);

                tempTextArea.select();
                document.execCommand('copy');
                tempTextArea.remove();
                notify('success', 'Copied')

            });
        });
    </script>
@endpush
