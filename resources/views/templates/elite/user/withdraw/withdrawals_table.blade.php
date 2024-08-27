@if (blank($withdrawals))
    <x-no-data message="No withdrawals yet"></x-no-data>
@else
    <div class="table-acordion-wrapper">
        <div class="accordion table--acordion" id="transactionAccordion">
            @foreach ($withdrawals as $withdrawal)
                <div class="accordion-item transaction-item">
                    <h2 class="accordion-header" id="h-{{ $withdrawal->id }}">
                        <button class="accordion-button collapsed" data-bs-target="#id-{{ $withdrawal->id }}" data-bs-toggle="collapse" type="button">
                            <div class="col-lg-4 col-sm-5 col-8 order-1 icon-wrapper">
                                <div class="left">
                                    <div class="icon tr-icon icon-success ">
                                        <i class="las la-long-arrow-alt-right"></i>
                                    </div>
                                    <div class="content">
                                        <h6 class="trans-title">{{ __($withdrawal->crypto->code) }}</h6>
                                        <span class="time text-muted mt-2">{{ showDateTime($withdrawal->created_at, 'M d Y @g:i:sa') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-12 order-sm-2 order-3 content-wrapper">
                                <p class="trx-no text-muted"><b>#{{ $withdrawal->trx }}</b></p>
                            </div>
                            <div class="col-lg-4 col-sm-3 col-4 order-sm-3 order-2 text-end amount-wrapper">
                                <p class="btc">
                                    <b>{{ showAmount($withdrawal->amount, 8) }} {{ __($withdrawal->crypto->code) }}</b><br>
                                    <small class="fw-bold text--danger">{{ showAmount($withdrawal->charge, 8) }} {{ __($withdrawal->crypto->code) }} </small>
                                </p>

                            </div>
                        </button>
                    </h2>
                    <div aria-labelledby="h-{{ $withdrawal->id }}" class="accordion-collapse collapse" data-bs-parent="#transactionAccordion" id="id-{{ $withdrawal->id }}">
                        <div class="accordion-body">
                            <ul class="caption-list">
                                <li>
                                    <span class="caption">@lang('Charge')</span>
                                    <span class="value">{{ showAmount($withdrawal->charge, 8) }} {{ __($withdrawal->crypto->code) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('After Charge')</span>
                                    <span class="value">{{ showAmount($withdrawal->payable, 8) }} {{ __($withdrawal->crypto->code) }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Wallet Address')</span>
                                    <span class="value">{{ $withdrawal->wallet_address }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Status')</span>
                                    <span class="value">
                                        @php echo $withdrawal->statusBadge; @endphp
                                    </span>
                                </li>
                                @if ($withdrawal->status == Status::PAYMENT_SUCCESS || $withdrawal->status == Status::PAYMENT_REJECT)
                                    <li>
                                        <span class="caption">@lang('Reason')</span>
                                        <span class="value">{{ $withdrawal->admin_feedback }}</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @if ($withdrawals->hasPages())
        {{ $withdrawals->links() }}
    @endif
@endif
