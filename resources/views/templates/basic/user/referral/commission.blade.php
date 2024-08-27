@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row mb-none-30">
                <div class="col-xl-12 col-lg-12 col-sm-12">
                    <div class="btn-group justify-content-end">
                        @if (request()->routeIs('user.referral.commissions.trade'))
                            <a href="{{ route('user.referral.users') }}" class="btn--base btn-sm">@lang('Referred Users')</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-50">
                <div class="col-xl-12">
                    <div class="custom--card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md">
                                <table class="table custom--table">
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
                                @if (blank($logs))
                                    <x-no-data message="No referral commission earned yet"></x-no-data>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($logs->hasPages())
                        <div class="pagination-wrapper">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            </div>
    </section>
@endsection

@push('style')
    <style>
        .btn-disabled {
            opacity: 0.5;
            cursor: auto !important;
        }
    </style>
@endpush
