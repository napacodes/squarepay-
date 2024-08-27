@php
    $offerContent = getContent('offer.content', true);
@endphp


<section class="offer-section section--bg2">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 text-lg-left text-center">
                <h2 class="title text-white">{{ __(@$offerContent->data_values->heading) }}</h2>
                <p class="text-white mt-2">{{ __(@$offerContent->data_values->sub_heading) }}</p>
            </div>
            <div class="col-lg-4 text-lg-right text-center mt-lg-0 mt-4">
                <a href="{{ __(@$offerContent->data_values->button_url) }}" class="btn--base">{{ __(@$offerContent->data_values->button_text) }}</a>
            </div>
        </div>
    </div>
</section>
