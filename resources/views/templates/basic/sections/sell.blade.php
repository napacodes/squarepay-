@php
    $sellContent = getContent('sell.content', true);
    $cryptos = App\Models\CryptoCurrency::active()->get();
@endphp

<section class="pt-120 pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-header text-center">
                    <h2 class="section-title">{{ __(@$sellContent->data_values->heading) }}</h2>
                    <p>{{ __(@$sellContent->data_values->sub_heading) }}</p>
                </div>
            </div>
        </div><!-- row end -->

        @php $type = 'sell';@endphp
        @include($activeTemplate . 'partials.ads_table', [$type])
    </div>
</section>

@include($activeTemplate . 'partials.login_required')
