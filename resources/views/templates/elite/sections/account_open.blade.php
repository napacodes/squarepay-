@php
    $content = getContent('account_open.content', true);
    $elements = getContent('account_open.element', orderById: true);
@endphp
<div class="account-open-section pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-heading style-three">
                    <h2 class="section-heading__title" s-break="-3" s-color="highlight">{{ __(@$content->data_values->heading) }}</span></h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            @foreach ($elements as $element)
                <div class="col-lg-4 col-md-6">
                    <div class="account-open">
                        <div class="account-open__thumb">
                            <img alt="" src="{{ frontendImage('account_open', @$element->data_values->image, '150x150') }}">
                        </div>
                        <div class="account-open__content">
                            <h4 class="account-open__title">{{ __($element->data_values->title) }}</h4>
                            <p class="account-open__desc">{{ __($element->data_values->description) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
