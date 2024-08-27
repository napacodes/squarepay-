@php
    $content = getContent('payment_methods.content', true);
    $elements = getContent('payment_methods.element');
@endphp

<section class="payment-method-section py-120">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="payment-method-wrapper">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="payment-method-left">
                                <h2 class="title mb-0">{{ __(@$content->data_values->heading) }}</h2>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="payment-method-right">
                                @foreach ($elements as $item)
                                    <span class="brand"><img alt="" src="{{ frontendImage('payment_methods', @$item->data_values->image, '80x45') }}"></span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
