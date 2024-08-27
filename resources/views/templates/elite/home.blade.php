@extends($activeTemplate . 'layouts.frontend')
@section('content')

@php
    $content = getContent('banner.content', true);
@endphp

<section class="banner-section">
    <div class="banner-section__shape">
        <img alt="Banner Shape" src="{{ getImage($activeTemplateTrue . 'images/banner-shape.png') }}">
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="banner-content">
                    <h5 class="banner-content__subtitle">{{ __(@$content->data_values->heading) }}</h5>
                    <h2 class="banner-content__title" s-break="3" s-color="base-two" s-length="2">{{ __(@$content->data_values->subheading) }}</h2>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="banner-thumb">
                    <img alt="Banner Thumb" src="{{ frontendImage('banner', @$content->data_values->image, '515x295') }}">
                </div>
            </div>
        </div>
    </div>
</section>

    @if ($sections && $sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            }).change();

            $('#fiat-gateway').on('change', function() {
                var fiats = $(this).find('option:selected').data('fiat');
                var html = ``;

                if (fiats && fiats.length > 0) {
                    $.each(fiats, function(i, v) {
                        html += `<option value="${v.code}">${v.code}</option>`;
                    });
                } else {
                    html = `<option value="">@lang('Select Fiat Currency')</option>`;
                }

                $('.fiat-currency').html(html);
            }).change();

        })(jQuery)
    </script>
@endpush
