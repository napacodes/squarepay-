@php
    $content = getContent('faq.content', true);
    $elements = getContent('faq.element');
@endphp

<section class="faq-section py-60">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="section-heading">
                    <h2 class="section-heading__title" s-break="-1" s-color="highlight">{{ __(@$content->data_values->heading) }}</h2>
                    <p class="section-heading__desc">{{ __(@$content->data_values->sub_heading) }}</p>
                </div>
            </div>
        </div>
        <div class="accordion custom--accordion accordion-flush" id="accordionFlushExample">
            <div class="row">
                <div class="col-lg-6">
                    @foreach ($elements as $item)
                        @if ($loop->odd)
                            <div class="accordion-item">
                                <h6 class="accordion-header" id="flush-heading-{{ $item->id }}">
                                    <button aria-controls="flush-collapse-{{ $item->id }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#flush-collapse-{{ $item->id }}" data-bs-toggle="collapse" type="button">
                                        {{ __($item->data_values->question) }}
                                    </button>
                                </h6>
                                <div aria-labelledby="flush-heading-{{ $item->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" id="flush-collapse-{{ $item->id }}">
                                    <div class="accordion-body">
                                        {{ __(@$item->data_values->answer) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>
                <div class="col-lg-6">
                    @foreach ($elements as $item)
                        @if ($loop->even)
                            <div class="accordion-item">
                                <h6 class="accordion-header" id="flush-heading-{{ $item->id }}">
                                    <button aria-controls="flush-collapse-{{ $item->id }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#flush-collapse-{{ $item->id }}" data-bs-toggle="collapse" type="button">
                                        {{ __($item->data_values->question) }}
                                    </button>
                                </h6>
                                <div aria-labelledby="flush-heading-{{ $item->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" id="flush-collapse-{{ $item->id }}">
                                    <div class="accordion-body">
                                        {{ __(@$item->data_values->answer) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
