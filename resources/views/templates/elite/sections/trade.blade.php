@php
    $content = getContent('trade.content', true);
    $elements = App\Models\Frontend::where('tempname', activeTemplate())->where('data_keys', 'trade.element');
    $buyElements = (clone $elements)->where('data_values->trade_type', 'buy')->get();
    $sellElements = (clone $elements)->where('data_values->trade_type', 'sell')->get();
@endphp

<div class="p2p-trade-section py-60">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="section-heading style-left">
                    <h2 class="section-heading__title" s-break="2" s-color="highlight" s-length="2">{{ __(@$content->data_values->heading) }}</h2>
                </div>
            </div>
            <div class="col-lg-6">
                <ul class="nav nav-tabs custom--tab" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button aria-controls="first" aria-selected="true" class="nav-link active" data-bs-target="#first" data-bs-toggle="tab" id="first-tab" role="tab" type="button">@lang('Buy Crypto')</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button aria-controls="second" aria-selected="false" class="nav-link" data-bs-target="#second" data-bs-toggle="tab" id="second-tab" role="tab" tabindex="-1" type="button">@lang('Sell Crypto')</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            <div aria-labelledby="first-tab" class="tab-pane fade show active" id="first" role="tabpanel">
                <div class="row justify-content-center">
                    @foreach ($buyElements as $buyElement)
                        <div class="col-lg-4 col-md-6">
                            <div class="p2p-trade">
                                <div class="p2p-trade__thumb">
                                    <img alt="" src="{{ frontendImage('trade', @$buyElement->data_values->image, '220x180') }}">
                                </div>
                                <div class="p2p-trade__content">
                                    <h4 class="p2p-trade__title">{{ __($buyElement->data_values->title) }}</h4>
                                    <p class="p2p-trade__desc">{{ __($buyElement->data_values->description) }} </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div aria-labelledby="second-tab" class="tab-pane fade" id="second" role="tabpanel">
                <div class="row justify-content-center">

                    @foreach ($sellElements as $sellElement)
                        <div class="col-lg-4 col-md-6">
                            <div class="p2p-trade">
                                <div class="p2p-trade__thumb">
                                    <img alt="" src="{{ frontendImage('trade', @$sellElement->data_values->image, '220x180') }}">
                                </div>
                                <div class="p2p-trade__content">
                                    <h4 class="p2p-trade__title">{{ __($sellElement->data_values->title) }}</h4>
                                    <p class="p2p-trade__desc">{{ __($sellElement->data_values->description) }} </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
