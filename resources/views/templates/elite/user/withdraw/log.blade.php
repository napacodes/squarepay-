@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    <div class="row gy-4">
        <div class="col-lg-12">
            <form  class="coin-search-form text-center" method="GET">
                <div class="d-flex flex-wrap gap-3">
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

        <div class="col-lg-12 ">
            @include($activeTemplate . 'user.withdraw.withdrawals_table')
        </div>
    </div>
@endsection
