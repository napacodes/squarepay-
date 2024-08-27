@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form  method="POST">
                        @csrf
                        <div class="row">
                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label> @lang('Fiat Currency Rate Api Key') </label>
                                    ( <small>@lang('For the api key please visit :')
                                        <a target="_blank" class="text--info" href="https://currencylayer.com/">@lang('Currency Layer')</a>
                                    </small> )
                                    <input class="form-control" type="text" name="fiat_api_key" value="{{ $general->fiat_api_key }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label> @lang('Cryptocurrency Rate Api Key') </label>
                                    ( <small>@lang('For the api key please visit :')
                                        <a target="_blank" class="text--info" href="https://coinmarketcap.com/">@lang('CoinMarketCap')</a>
                                    </small> )
                                    <input class="form-control" type="text" name="crypto_api_key" value="{{ $general->crypto_api_key }}">
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-4 mt-4">
                                <h4>@lang('CoinPayment Setting')</h4>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Public Key')</label>
                                    <input type="text" class="form-control" name="public_key" value="{{ $general->public_key }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Private Key')</label>
                                    <input type="text" class="form-control" name="private_key" value="{{ $general->private_key }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label> @lang('Merchant ID')</label>
                                    <input type="text" class="form-control" name="merchant_id" value="{{ $general->merchant_id }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 btn-lg h-45">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
