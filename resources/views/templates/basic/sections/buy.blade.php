@php
    $buyContent = getContent('buy.content', true);
@endphp


<section class="pt-120 pb-120 bg_img" data-background="{{ frontendImage('buy' , @$buyContent->data_values->image, '1920x1340') }}">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-header text-center">
                    <h2 class="section-title">{{ __(@$buyContent->data_values->heading) }}</h2>
                    <p>{{ __(@$buyContent->data_values->sub_heading) }}</p>
                </div>
            </div>
        </div><!-- row end -->
        @php $type = 'buy';@endphp
        @include($activeTemplate . 'partials.ads_table', [$type])
    </div>
</section>

@include($activeTemplate . 'partials.login_required')
