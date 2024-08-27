@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Payment Method')</th>
                                    <th>@lang('Payment Window') | @lang('Margin')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Published')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($advertisements as $ad)
                                    <tr>
                                        <td>{{ $advertisements->firstItem() + $loop->index }}</td>

                                        <td>
                                            <span class="fw-bold d-block">{{ $ad->user->fullname }}</span>
                                            <a class="text--small" href="{{ route('admin.users.detail', $ad->user->id) }}"><span>@</span>{{ $ad->user->username }}</a>
                                        </td>

                                        <td>
                                            @php echo $ad->typeBadge @endphp
                                        </td>

                                        <td>
                                            <span class="d-block fw-bold">{{ $ad->fiatGateway->name }}</span>

                                            <span class="text--small">{{ $ad->fiat->code }}</span>
                                        </td>

                                        <td>
                                            <span>{{ $ad->window }} @lang('Minutes')</span>
                                            <br>
                                            @php echo $ad->marginValue @endphp
                                        </td>

                                        <td>
                                            <span>
                                                {{ showAmount(getRate($ad)) }} {{ $ad->fiat->code }}
                                            </span>
                                            <span class="small d-block"> / {{ $ad->crypto->code }}</span>
                                        </td>

                                        <td>
                                            @php
                                                $maxLimit = getMaxLimit($ad->user->wallets, $ad);
                                                $isPublished = getPublishStatus($ad, $maxLimit);
                                            @endphp

                                            @if ($isPublished)
                                                <span class="badge badge--success">@lang('Yes')</span>
                                            @else
                                                <button class="badge badge--danger" data-bs-toggle="modal" data-bs-target="#reasonModal" data-reasons='@json(getAdUnpublishReason($ad, $maxLimit, true))'><i class="fa fa-question-circle" aria-hidden="true"></i> @lang('No')</button>
                                            @endif
                                        </td>

                                        <td>
                                            @php echo $ad->statusBadge @endphp
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary viewBtn" data-terms_of_trades="{{ $ad->terms }}" data-payment_details="{{ $ad->details }}" data-status='@php echo $ad->statusBadge @endphp' data-rate="{{ showAmount(getRate($ad)) }} {{ __($ad->fiat->code) }} / {{ __($ad->crypto->code) }}" data-maximum_limit="{{ showAmount($ad->max) }}" data-minimum_limit="{{ showAmount($ad->min) }}" data-margin='@php echo $ad->marginValue @endphp' data-payment_window="{{ $ad->window }} @lang('Minutes')" data-payment_methods="{{ __($ad->fiatGateway->name) }}" data-fiat_currency="{{ __($ad->fiat->name) }}" data-crypto_currency="{{ __($ad->crypto->name) }}" data-type='@php echo $ad->typeBadge @endphp'>
                                                    <i class="las la-desktop"></i>@lang('Details')
                                                </button>

                                                @if ($ad->status)
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.ad.status', $ad->id) }}" data-question="@lang('Are you sure to disable this ad?')">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.ad.status', $ad->id) }}" data-question="@lang('Are you sure to enable this ad?')">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($advertisements->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($advertisements) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- View Modal --}}
    <div id="viewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Advertisement Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <ul class="list-group list-group-flush advertisementDetails"></ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Modal --}}
    <x-confirmation-modal />

    <!-- Modal -->
    <div class="modal fade" id="reasonModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Unpublished Reason')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>

            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
        <x-search-form placeholder="Username"></x-search-form>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.viewBtn').on('click', function() {
                var modal = $('#viewModal');
                let data = $(this).data();
                let content = ``;
                $.each(data, function(index, value) {
                    content += `
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <span >@lang('${titleCase(index)}')</span>
                            <span class="type">${value}</span>
                        </li>
                    `;
                });
                $('.advertisementDetails').html(content);
                modal.modal('show');
            });

            $('#reasonModal').on('show.bs.modal', function(e) {
                let content = `<ul class="list-group list-group-flush">`;
                let reasons = $(e.relatedTarget).data('reasons');
                let i = 1;
                $.each(reasons, function(index, element) {
                    content += `<li class="list-group-item text--danger fw-bold"> ${i}. ${element} </li>`;
                    i++;
                });

                content += `</ul>`;
                $('#reasonModal').find('.modal-body').html(content);
            });
        })(jQuery);
    </script>
@endpush
