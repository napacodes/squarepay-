@extends($activeTemplate . 'layouts.frontend')
@section('content')

    <section class="pt-60 pb-60 section--bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="border-bottom-0 text-center">
                        <ul class="btn-list justify-content-center mb-4">
                            <li><a class="btn btn-sm btn-outline--base @if (!request()->id) active @endif" href="{{ route('user.wallets') }}">@lang('All')</a></li>
                            @foreach ($wallets as $wallet)
                                <li>
                                    <a class="btn btn-sm btn-outline--base @if (request()->id == $wallet->crypto->id) active @endif" href="{{ route('user.wallets.single', [$wallet->crypto->id, $wallet->crypto->code]) }}"><span>{{ $wallet->crypto->code }}</span> ({{ $cryptoWallets->where('crypto_id', $wallet->crypto_id)->count() }}) {{ showAmount($wallet->balance, 8) }}</a>
                                </li>
                            @endforeach
                        </ul>

                        @foreach ($wallets as $wallet)
                            @if (Request::routeIs('user.wallets.single'))
                                @if ($crypto->id == $wallet->crypto->id)
                                    <div class="text-center mt-4">
                                        <h4>@lang('Deposit Charge is') @if ($wallet->crypto->deposit_charge_fixed > 0)
                                                {{ $wallet->crypto->deposit_charge_fixed }} {{ $wallet->crypto->code }} +
                                            @endif {{ $wallet->crypto->deposit_charge_percent }}%
                                        </h4>
                                    </div>

                                    <div class="mt-2 d-flex flex-wrap justify-content-center">
                                        <a class="link-btn m-2" href="{{ route('user.wallets.generate', $wallet->crypto->code) }}"><i class="las la-plus"></i> @lang('Generate New') {{ $wallet->crypto->code }} @lang('Address')</a>

                                        <a class="link-btn m-2" href="{{ route('user.withdraw', $wallet->crypto->code) }}"><i class="las la-credit-card"></i> @lang('Withdraw') {{ $wallet->crypto->code }}</a>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>

                    <div class="custom--card rounded-5">
                        <div class="card-body p-0">
                            <div class="table-responsive table-responsive--md">
                                <table class="table custom--table mb-0">
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
                                                    <a class="btn btn-outline--base copy-address" href="#"><i class="las la-copy"></i> @lang('Copy')</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if (blank($cryptoWallets))
                                    <x-no-data message="No wallet found"></x-no-data>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($cryptoWallets->hasPages())
                <div class="pagination-wrapper">
                    {{ $cryptoWallets->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

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
