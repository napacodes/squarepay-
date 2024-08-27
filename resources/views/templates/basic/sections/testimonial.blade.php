@php
    $testimonialContent = getContent('testimonial.content', true);
    $testimonialElements = getContent('testimonial.element');
@endphp

<section class="pt-120 pb-120 overlay--one bg_img" data-background="{{ frontendImage('testimonial', @$testimonialContent->data_values->image, '1920x1080') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-header text-center">
                    <h2 class="section-title text-white">{{ __(@$testimonialContent->data_values->heading) }}</h2>
                    <p class="text-white">{{ __(@$testimonialContent->data_values->sub_heading) }}</p>
                </div>
            </div>
        </div><!-- row end -->
        <div class="testimonial-slider">

            @foreach ($testimonialElements as $testimonial)
                <div class="single-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-card__content text-center">
                            <i class="fas fa-quote-left"></i>
                            <p>{{ __(@$testimonial->data_values->quote) }}</p>
                        </div>
                        <div class="testimonial-card__client text-center mt-4">
                            <div class="thumb"><img src="{{ frontendImage('testimonial', @$testimonial->data_values->image, '450x475') }}" alt="image"></div>
                            <h6 class="name mt-2">{{ __(@$testimonial->data_values->author_name) }}</h6>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
</section>
