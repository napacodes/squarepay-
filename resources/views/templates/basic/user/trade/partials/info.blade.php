<div class="accordion cmn-accordion accordion-arrow mb-3" id="accordionExample">
    <div class="card">
        <div class="card-header" id="headingOne">
            <button class="btn btn-link btn-block text-left collapsed text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">

                <p class="text-dark"><i class="las la-info-circle"></i> @lang('Trade Information')</p>
            </button>
        </div>

        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
                <ul class="caption-list">
                    <li>
                        <span class="caption">@lang('Buyer Name')</span>
                        <span class="value">{{ __($trade->buyer->username) }}</span>
                    </li>
                    <li>
                        <span class="caption">@lang('Seller Name')</span>
                        <span class="value">{{ __($trade->seller->username) }}</span>
                    </li>
                    <li>
                        <span class="caption">@lang('Amount')</span>
                        <span class="value">{{ showAmount($trade->amount) }} {{ __($trade->fiat->code) }}</span>
                    </li>
                    <li>
                        <span class="caption">{{ __($trade->crypto->code) }}</span>
                        <span class="value">{{ showAmount($trade->crypto_amount, 8) }}</span>
                    </li>
                    <li>
                        <span class="caption">@lang('Payment Window')</span>
                        <span class="value">{{ $trade->window }} @lang('Minutes')</span>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- card end -->
</div>
