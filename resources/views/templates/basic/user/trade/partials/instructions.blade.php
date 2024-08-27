<h6>@lang('Instructions to be followed')</h6>

<div class="accordion cmn-accordion accordion-arrow mt-3" id="accordionExample">
    <div class="card shadow-none">
        <div class="card-header" id="headingTwo">
            <button class="btn btn-link btn-block text-left collapsed text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                <p class="text-dark"><i class="las la-question-circle"></i> @lang('Terms of trade')</p>
            </button>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
            <div class="card-body border-top-0">
                <p>{{ __($trade->advertisement->terms) }}</p>
            </div>
        </div>
    </div><!-- card end -->

    <div class="card shadow-none">
        <div class="card-header" id="headingThree">
            <button class="btn btn-link btn-block text-left collapsed text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                <p class="text-dark"><i class="las la-question-circle"></i> @lang('Payment details')</p>
            </button>
        </div>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
            <div class="card-body border-top-0">
                <p>{{ __($trade->advertisement->details) }}</p>
            </div>
        </div>
    </div><!-- card end -->
</div>
