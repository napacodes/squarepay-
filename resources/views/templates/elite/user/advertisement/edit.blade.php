@extends($activeTemplate . 'layouts.master_without_menu')
@section('content')
    @php
        $advertisementContent = getContent('advertisement.content', true);
    @endphp
    <section class="ptable-section pb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="sell-category d-flex align-items-center justify-content-between">
                        <li class="sell-category__item active">
                            <p class="text">@lang('Currency')</p>
                        </li>
                        <li class="sell-category__item">
                            <p class="text">@lang('Payment')</p>
                        </li>
                        <li class="sell-category__item">
                            <p class="text">@lang('Terms')</p>
                        </li>
                    </ul>
                    <ul class="sell-category-number d-flex align-items-center justify-content-between">
                        <li class="item active">
                            <span class="number">@lang('1')</span>
                        </li>
                        <li class="item @if ($step == 'two' || $step == 'three') active @endif">
                            <span class="number">@lang('2')</span>
                        </li>
                        <li class="item @if ($step == 'three') active @endif">
                            <span class="number">@lang('3')</span>
                        </li>
                    </ul>
                </div>
                <div class="col-12">
                    @include($activeTemplate . 'user.advertisement.step_form.' . $step, [
                        'advertisementContent' => $advertisementContent,
                        'advertisement'        => $advertisement,
                        'mode'                 => 'edit',
                    ])
                </div>
            </div>
        </div>
    </section>
@endsection


@push('breadcrumb-plugins')
    <a class="ptable-header-right__link" href="{{ route('user.advertisement.index') }}">
        <span class="icon"><i class="lab la-adversal"></i></span>
        <span class="text">@lang('My Ads')</span>
    </a>
@endpush


