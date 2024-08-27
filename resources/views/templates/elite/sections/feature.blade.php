@php
    $content = getContent('feature.content', true);
    $elements = getContent('feature.element', orderById: true);
@endphp
<div class="feature-section pt-120 pb-120">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 col-lg-7">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title" s-break="-3" s-color="highlight">{{ @$content->data_values->heading }}</h2>
                </div>
            </div>
        </div>
        <div class="row align-items-end">
            <div class="col-lg-6">
                <ul class="feature-content">
                    @foreach ($elements as $element)
                        <li class="feature-content__item">
                            <div class="feature-content__thumb">
                                <img alt="feature Icon" src="{{ frontendImage('feature' , @$element->data_values->image, '75x75') }}">
                            </div>
                            <h4 class="feature-content__title">{{ __($element->data_values->title) }}</h4>
                            <p class="feature-content__desc">{{ __($element->data_values->description) }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-6 col-xxl-5 offset-xxl-1">
                <div class="feature-thumb">
                    <img alt="" src="{{ frontendImage('feature', @$content->data_values->image, '550x500') }}">
                </div>
            </div>
        </div>
    </div>
</div>
