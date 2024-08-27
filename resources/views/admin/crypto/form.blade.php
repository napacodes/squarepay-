@extends('admin.layouts.app')

@section('panel')
    @php
        $cryptoImage = fileManager()->crypto();
    @endphp

    <form action="{{ route('admin.crypto.store', $crypto->id ?? 0) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xxl-3 col-lg-5 ">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader image="{{ @$crypto->image }}" class="w-100" type="crypto" :required=false />
                                </div>
                            </div>
                            <div class="col-xxl-9 col-lg-7 ">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>@lang('Name')</label>
                                        <input class="form-control" type="text" name="name" value="{{ old('name', @$crypto->name) }}" placeholder="@lang('e.g.') @lang('Bitcoin')" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>@lang('Code')</label>
                                        <input class="form-control" type="text" name="code" value="{{ old('code', @$crypto->code) }}" placeholder="@lang('e.g.') @lang('BTC')" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>@lang('Symbol')</label>
                                        <input class="form-control" type="text" name="symbol" value="{{ old('', @$crypto->symbol) }}" placeholder="@lang('e.g.') ₿" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>@lang('Rate')</label>
                                        <div class="input-group">
                                            <div class="input-group-text"><span>1&nbsp;</span> <span class="currency-symbol">{{ @$crypto->code }}</span> &nbsp; =</div>
                                            <input class="form-control" type="number" step="any" name="rate" value="{{ getAmount(@$crypto->rate) }}" required>
                                            <span class="input-group-text">@lang('USD')</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12 border-line-area mt-3">
                                        <h5 class="card-title border-bottom pb-2 text-center">@lang('Deposit Charges')</h5>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>@lang('Fixed Charge')</label>
                                        <div class="input-group">
                                            <input class="form-control" type="number" step="any" name="deposit_charge_fixed" value="{{ old('number', getAmount(@$crypto->deposit_charge_fixed)) }}" placeholder="0" required>
                                            <span class="input-group-text currency-symbol">{{ __(@$crypto->code) }}</span>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>@lang('Percentage Charge')</label>
                                        <div class="input-group">
                                            <input class="form-control" type="number" step="0.01" max="100" name="deposit_charge_percent" value="{{ old('deposit_charge_percent', getAmount(@$crypto->deposit_charge_percent)) }}" placeholder="0" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-12 border-line-area mt-3">
                                        <h5 class="card-title border-bottom pb-2 text-center">@lang('Withdrawal Charges')</h5>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>@lang('Fixed Charge')</label>
                                        <div class="input-group">
                                            <input class="form-control" type="number" step="any" name="withdraw_charge_fixed" value="{{ old('withdraw_charge_fixed', getAmount(@$crypto->withdraw_charge_fixed)) }}" placeholder="0" required>
                                            <span class="input-group-text currency-symbol">{{ __(@$crypto->code) }}</span>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>@lang('Percentage Charge')</label>
                                        <div class="input-group">
                                            <input class="form-control" type="number" step="0.01" max="100" name="withdraw_charge_percent" value="{{ old('withdraw_charge_percent', getAmount(@$crypto->withdraw_charge_percent)) }}" placeholder="0" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.crypto.index') }}"></x-back>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('[name=code]').on('input', function() {
                $('.currency-symbol').text($(this).val());
            });
        })(jQuery);
    </script>
@endpush
