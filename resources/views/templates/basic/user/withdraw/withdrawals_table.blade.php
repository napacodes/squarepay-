<div class="custom--card">
    <div class="card-body p-0">
        <div class="table-responsive--lg">
            <table class="table custom--table">
                <thead>
                    <tr>
                        <th>@lang('Cryptocurrency')</th>
                        <th>@lang('TRX No.')</th>
                        <th>@lang('Wallet Address')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Charge')</th>
                        <th>@lang('After Charge')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Time')</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($withdrawals as $withdrawal)
                        <tr>
                            <td><span class="text--base">{{ __($withdrawal->crypto->code) }}</span></td>
                            <td>{{ $withdrawal->trx }}</td>

                            <td>{{ $withdrawal->wallet_address }}</td>

                            <td>
                                <strong>{{ showAmount($withdrawal->amount, 8) }} {{ __($withdrawal->crypto->code) }}</strong>
                            </td>

                            <td class="text-danger">
                                {{ showAmount($withdrawal->charge, 8) }} {{ __($withdrawal->crypto->code) }}
                            </td>

                            <td>
                                {{ showAmount($withdrawal->payable, 8) }} {{ __($withdrawal->crypto->code) }}
                            </td>

                            <td>
                                @php
                                    echo $withdrawal->statusBadge;
                                @endphp

                                @if ($withdrawal->status == Status::PAYMENT_SUCCESS)
                                    <button class="bg--base btn-rounded badge text-white approveBtn" title="@lang('Feedback')" data-admin_feedback="{{ $withdrawal->admin_feedback }}"><i class="fa fa-info"></i></button>
                                @endif
                                @if ($withdrawal->status == Status::PAYMENT_REJECT)
                                    <button class="bg--base btn-rounded badge text-white approveBtn" title="@lang('Reason')" data-admin_feedback="{{ $withdrawal->admin_feedback }}"><i class="fa fa-info"></i></button>
                                @endif
                            </td>

                            <td>
                                {{ showDateTime($withdrawal->created_at) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (blank($withdrawals))
                <x-no-data message="No withdrawals yet"></x-no-data>
            @endif
        </div>
    </div>
</div>
@if ($withdrawals->hasPages())
    <div class="pagination-wrapper">
        {{ $withdrawals->links() }}
    </div>
@endif
