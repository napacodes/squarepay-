@extends($activeTemplate . 'layouts.master_without_menu')
@section('content')

    @if (blank($advertisements))
        <x-no-data message="No advertisement added yet" />
        <div class="text-center">
            <a href="{{ route('user.advertisement.new') }}" class="btn btn--base"><i class="las la-plus"></i> @lang('Create Ad')</a>
        </div>
    @else
        @include($activeTemplate . 'partials.user_ads_table')

        <div class="container">
            @if ($advertisements->hasPages())
                {{ $advertisements->links() }}
            @endif
        </div>

    @endif
    <x-user-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <a class="ptable-header-right__link" href="{{ route('user.advertisement.new') }}">
        <span class="icon"><i class="las la-plus"></i></span>
        <span class="text">@lang('New Ad')</span>
    </a>
@endpush
