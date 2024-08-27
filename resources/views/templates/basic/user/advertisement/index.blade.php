@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-60 pb-60 section--bg">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-sm-12">
                    <div class="text-end">
                        <a href="{{ route('user.advertisement.new') }}" class="btn btn-sm btn--base"><i class="las la-plus-circle"></i> @lang('New Ad')</a>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                @include($activeTemplate . 'partials.user_ads_table')
            </div>
            @if ($advertisements->hasPages())
                <div class="pagination-wrapper">
                    {{ $advertisements->links() }}
                </div>
            @endif
        </div>
    </section>

    <x-confirmation-modal />
@endsection
