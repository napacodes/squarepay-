@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    @if (blank($logs))
        <x-no-data message="No referral commission earned yet"></x-no-data>
    @else
        <div class="ptable-wrapper">
            <table class="table table--responsive--lg">
                <thead>
                    <tr>
                        <th>@lang('Date')</th>
                        <th>@lang('From')</th>
                        <th>@lang('Level')</th>
                        <th>@lang('Percent')</th>
                        <th>@lang('Amount')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $data)
                        <tr>
                            <td>{{ showDateTime($data->created_at, 'd M, Y') }}</td>
                            <td><strong>{{ @$data->bywho->username }}</strong></td>
                            <td>{{ __(ordinal($data->level)) }} @lang('Level')</td>
                            <td>{{ getAmount($data->percent) }} %</td>
                            <td>{{ showAmount($data->commission_amount, 8) }} {{ __($data->crypto->code) }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    @endif
    @if ($logs->hasPages())
        {{ $logs->links() }}
    @endif
@endsection

@if (request()->routeIs('user.referral.commissions.trade'))
    @push('breadcrumb-plugins')
        <a class="ptable-header-right__link" href="{{ route('user.referral.users') }}">
            <span class="icon"><i class="las la-users"></i></span>
            <span class="text">@lang('Referred Users')</span>
        </a>
    @endpush
@endif

@push('style')
    <style>
        .btn-disabled {
            opacity: 0.5;
            cursor: auto !important;
        }
    </style>
@endpush
