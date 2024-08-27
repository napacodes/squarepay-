@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    @if ($user->refBy)
        <div class="d-flex flex-wrap justify-content-center">
            <h5><span class="mb-2">@lang('You are referred by')</span> <span><a class="text--base" href="{{ route('public.profile', $user->refBy->username) }}">{{ $user->refBy->username }}</a></span></h5>
        </div>
    @endif

    @if ($user->allReferrals->count() > 0 && $maxLevel > 0)
        <div class="treeview-container">
            <ul class="treeview">
                <li class="items-expanded"> {{ $user->username }}
                    @include($activeTemplate . 'partials.under_tree', ['user' => $user, 'layer' => 0, 'isFirst' => true])
                </li>
            </ul>
        </div>
    @else
        <x-no-data message="No user found"></x-no-data>
    @endif
@endsection

@if (request()->routeIs('user.referral.users'))
    @push('breadcrumb-plugins')
        <a class="ptable-header-right__link" href="{{ route('user.referral.commissions.trade') }}">
            <span class="icon"><i class="las la-wallet"></i></span>
            <span class="text">@lang('Commissions')</span>
        </a>
    @endpush
@endif

@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/treeView.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/treeView.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            $('.treeview').treeView();
        })(jQuery);
    </script>
@endpush
