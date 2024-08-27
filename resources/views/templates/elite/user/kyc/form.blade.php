@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-header">
                    <p class="mb-0">@lang('Please complete your KYC verification by providing the below data').</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.kyc.submit') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <x-viser-form identifier="act" identifierValue="kyc" />
                        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        "use strict";
        (function ($) {
            $('.form-control').addClass('form--control');
        })(jQuery);

    </script>
@endpush
