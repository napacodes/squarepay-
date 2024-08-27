@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-120 pb-120">
        <div class="container">

            <div class="row mb-none-30">
                <div class="col-xl-12 col-lg-12 col-sm-12">
                    <div class="btn-group justify-content-end">
                        @if (request()->routeIs('user.referral.users'))
                            <a href="{{ route('user.referral.commissions.trade') }}" class="btn--base btn-sm">@lang('Commissions')</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-50">
                <div class="col-xl-12 create-trade-form">
                    @if ($user->refBy)
                        <div class="d-flex flex-wrap justify-content-center">
                            <h5><span class="mb-2">@lang('You are referred by')</span> <span><a href="{{ route('public.profile', $user->refBy->username) }}" class="text--base">{{ $user->refBy->username }}</a></span></h5>
                        </div>
                    @endif

                    <div class="treeview-container">
                        <ul class="treeview">
                            @if ($user->allReferrals->count() > 0 && $maxLevel > 0)
                                <li class="items-expanded"> {{ $user->username }}
                                    @include($activeTemplate . 'partials.under_tree', ['user' => $user, 'layer' => 0, 'isFirst' => true])
                                </li>
                            @else
                                <li class="items-expanded">@lang('No user found')</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/treeView.css') }}">
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
