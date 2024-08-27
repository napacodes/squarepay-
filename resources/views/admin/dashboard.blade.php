@extends('admin.layouts.app')

@section('panel')
    <div class="row gy-4">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.all') }}" icon="las la-users" title="Total Users" value="{{ $widget['total_users'] }}" bg="primary" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.active') }}" icon="las la-user-check" title="Active Users" value="{{ $widget['verified_users'] }}" bg="success" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.email.unverified') }}" icon="lar la-envelope" title="Email Unverified Users" value="{{ $widget['email_unverified_users'] }}" bg="danger" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="6" link="{{ route('admin.users.mobile.unverified') }}" icon="las la-comment-slash" title="Mobile Unverified Users" value="{{ $widget['mobile_unverified_users'] }}" bg="warning" />
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->


    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.withdraw.data.all') }}" title="Approved Withdrawal" icon="far fa-credit-card" value="{{ __($widget['totalWithdrawApproved']) }}" bg="primary" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.withdraw.data.pending') }}" title="Pending Withdrawal" icon="fas fa-sync" value="{{ __($widget['totalWithdrawPending']) }}" bg="warning" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.withdraw.data.rejected') }}" title="Rejected Withdrawal" icon="la la-bank" value="{{ __($widget['totalWithdrawRejected']) }}" bg="red" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.withdraw.data.all') }}" title="Total Withdrawal" icon="far fa-user" value="{{ __($widget['totalWithdraw']) }}" bg="19" />
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->


    @if ($deposits->count())
        <div class="row gy-4 mt-2">
            <div class="col-12">
                <h4>@lang('Deposit Summary')</h4>
            </div>
            @foreach ($deposits as $deposit)
                <div class="col-xxl-3 col-sm-6">
                    <div class="widget-two box--shadow2 b-radius--5 bg--white">
                        <div class="widget-two__icon b-radius--5 text--success">
                            <img alt="image" src="{{ getImage(getFilePath('crypto') . '/' . $deposit->image, getFileSize('crypto')) }}">
                        </div>
                        <div class="widget-two__content">
                            <h3>{{ showAmount($deposit->deposits_sum_amount, 8) }} {{ __($deposit->code) }}</h3>
                            <span>@lang('Charge')</span>
                            <i class="fas fa-arrow-right text--danger"></i>
                            <span class="text--danger">{{ showAmount($deposit->deposits_sum_charge, 8) }} {{ __($deposit->code) }}</span>
                        </div>
                        <a class="widget-two__btn border border--success btn-outline--success" href="{{ route('admin.deposit.list') }}">@lang('View All')</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif



    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.ad.index') }}" title="Total Advertisements" icon="fab fa-adversal" value="{{ __($widget['totalAd']) }}" bg="19" type="2" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.trade.index') }}" title="Total Trades" icon="fas fa-exchange-alt" value="{{ __($widget['totalTrade']) }}" bg="primary" type="2" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.crypto.index') }}" title="Total Cryptocurrency" icon="fab fa-bitcoin" value="{{ __($widget['totalCrypto']) }}" bg="1" type="2" />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget style="7" link="{{ route('admin.fiat.currency.index') }}" title="Total Fiat Currency" icon="fas fa-coins" value="{{ __($widget['totalFiat']) }}" bg="success" type="2" />
        </div><!-- dashboard-w1 end -->
    </div>

    @if ($withdrawals->count())
        <div class="row gy-4 mt-2">
            <div class="col-12">
                <h4>@lang('Withdrawal Summary')</h4>
            </div>
            @foreach ($withdrawals as $withdrawal)
                <div class="col-xxl-3 col-sm-6">
                    <div class="widget-two box--shadow2 b-radius--5 bg--white">
                        <div class="widget-two__icon b-radius--5 text--success">
                            <img alt="image" src="{{ getImage(getFilePath('crypto') . '/' . $withdrawal->image, getFileSize('crypto')) }}">
                        </div>
                        <div class="widget-two__content">
                            <h3>{{ showAmount($withdrawal->withdrawals_sum_amount, 8) }} {{ __($withdrawal->code) }}</h3>
                            <span>@lang('Charge')</span>
                            <i class="fas fa-arrow-right text--danger"></i>
                            <span class="text--danger">{{ showAmount($withdrawal->withdrawals_sum_charge, 8) }} {{ __($withdrawal->code) }}</span>
                        </div>
                        <a class="widget-two__btn border border--success btn-outline--success" href="{{ route('admin.withdraw.data.all') }}">@lang('View All')</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="row mb-none-30 mt-5">
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card overflow-hidden">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Browser') (@lang('Last 30 days'))</h5>
                    <canvas id="userBrowserChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By OS') (@lang('Last 30 days'))</h5>
                    <canvas id="userOsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Login By Country') (@lang('Last 30 days'))</h5>
                    <canvas id="userCountryChart"></canvas>
                </div>
            </div>
        </div>
    </div>



    @include('admin.partials.cron_modal')
@endsection
@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm" data-bs-toggle="modal" data-bs-target="#cronModal">
        <i class="las la-server"></i>@lang('Cron Setup')
    </button>
@endpush


@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        "use strict";

        piChart(
            document.getElementById('userBrowserChart'),
            @json(@$chart['user_browser_counter']->keys()),
            @json(@$chart['user_browser_counter']->flatten())
        );

        piChart(
            document.getElementById('userOsChart'),
            @json(@$chart['user_os_counter']->keys()),
            @json(@$chart['user_os_counter']->flatten())
        );

        piChart(
            document.getElementById('userCountryChart'),
            @json(@$chart['user_country_counter']->keys()),
            @json(@$chart['user_country_counter']->flatten())
        );
    </script>
@endpush
@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }
    </style>
@endpush
