@extends($activeTemplate . 'layouts.frontend')
@section('content')
@php
$advertisementContent = getContent('advertisement.content', true);
@endphp

<section class="pt-60 pb-60">
    <div class="container">
        <div class="row justify-content-center gy-3">
            <div class="col-lg-10">
                <div class="text-end">
                    <a href="{{ route('user.advertisement.index') }}" class="btn btn-sm btn--base">
                        <i class="lab la-adversal"></i> @lang('My Ads')
                    </a>
                </div>
            </div>
            <div class="col-lg-10">
                <ul class="sell-category d-flex align-items-center justify-content-between">
                    <li class="sell-category__item active">
                        <p class="text">@lang('Asset & Fiat')</p>
                    </li>
                    <li class="sell-category__item">
                        <p class="text">@lang('Payment Information')</p>
                    </li>
                    <li class="sell-category__item">
                        <p class="text">@lang('Trade Rules')</p>
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
            <div class="col-lg-10">
                @include($activeTemplate . 'user.advertisement.step_form.' . $step, [
                'advertisementContent' => $advertisementContent,
                'advertisement' => $advertisement,
                'mode' => 'create',
                ])
            </div>
        </div>
    </div>
</section>
@endsection
