@php
    $content = getContent('cta.content', true);
@endphp

<section class="cta-section pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-heading cta-content text-center">
                    <h2 class="section-heading__title" s-break="-1" s-color="highlight">{{ __(@$content->data_values->heading) }}</h2>
                    <p class="section-heading__desc">{{ __(@$content->data_values->sub_heading) }}</p>
                    <a class="btn btn--base-two sec-btn" href="{{ __(@$content->data_values->button_url) }}">{{ __(@$content->data_values->button_text) }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
