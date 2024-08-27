<div class="ptable-wrapper">
    <table class="table table--responsive--lg">
        <thead>
            <tr>
                <th>@lang('Type')</th>
                <th>@lang('Currency')</th>
                <th>@lang('Payment Method')</th>
                <th>@lang('Margin / Fixed')</th>
                <th>@lang('Rate')</th>
                <th>@lang('Payment Window')</th>
                <th>@lang('Published')</th>
                <th>@lang('Status')</th>
                @if (!request()->routeIs('user.home'))
                    <th>@lang('Action')</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($advertisements as $ad)
                <tr>
                    @php $maxLimit = getMaxLimit($ad->user->wallets, $ad); @endphp
                    <td>
                        @php echo $ad->typeBadge @endphp
                    </td>
                    <td>{{ __(@$ad->fiat->code ?? "N/A") }}</td>
                    <td>{{ __(@$ad->fiatGateway->name ?? "N/A") }}</b></td>
                    <td>
                        @php echo @$ad->marginValue; @endphp
                    </td>
                    <td>
                        @if ($ad->status != Status::ADVERTISEMENT_DRAFT)
                            {{ getRate($ad) }} {{ __(@$ad->fiat->code) }}/ {{ __(@$ad->crypto->code) }}
                        @else
                            @lang('N/A')
                        @endif
                    </td>
                    <td>
                        {{ $ad->window ?  $ad->window . ' '. __("Minute") : "N/A" }}
                    </td>
                    <td>
                        @if ($ad->status != Status::ADVERTISEMENT_DRAFT)
                            @php
                                $isPublished = getPublishStatus($ad, $maxLimit);
                                @endphp

                            @if ($isPublished)
                                <span class="badge badge--success">@lang('Yes')</span>
                            @else
                            <button class="badge badge--danger" data-bs-target="#reasonModal" data-bs-toggle="modal" data-reasons='@json(getAdUnpublishReason($ad, $maxLimit))'><i aria-hidden="true" class="fa fa-question-circle"></i> @lang('No')</button>
                            @endif
                        @else
                            @lang('N/A')
                        @endif
                    </td>
                    <td>
                        @php echo $ad->statusBadge @endphp
                    </td>
                    @if (!request()->routeIs('user.home'))
                        <td>
                            <div class="btn--group">
                                @if ($ad->status != Status::ADVERTISEMENT_DRAFT)
                                    <a class="btn btn--sm btn-outline--warning" href="{{ route('user.advertisement.reviews', $ad->id) }}"><i class="lar la-thumbs-up"></i> @lang('Feedbacks')</a>
                                    @endif
                                    <a class="btn btn--sm btn-outline--info" href="{{ route('user.advertisement.edit', $ad->id) }}"><i class="la la-pencil"></i> @lang('Edit')</a>
                                    @if ($ad->status == Status::ADVERTISEMENT_DRAFT)
                                        <button class="btn btn--sm btn-outline--danger confirmationBtn" data-action="{{ route('user.advertisement.delete', $ad->id) }}" data-question="@lang('Are you sure to delete this ad?')">
                                            <i class="la la-trash"></i> @lang('Delete')
                                        </button>
                                    @else

                                    @if ($ad->status)
                                        <button class="btn btn--sm btn-outline--danger confirmationBtn" data-action="{{ route('user.advertisement.status', $ad->id) }}" data-question="@lang('Are you sure to disable this ad?')"><i class="la la-eye-slash"></i> @lang('Disable')</button>
                                    @else
                                        <button class="btn btn--sm btn-outline--success confirmationBtn" data-action="{{ route('user.advertisement.status', $ad->id) }}" data-question="@lang('Are you sure to enable this ad?')"><i class="las la-eye"></i> @lang('Enable')</button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>

<!-- Modal -->
<div aria-hidden="true" aria-labelledby="modelTitleId" class="modal custom--modal fade" id="reasonModal" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="notice-text modal-title">@lang('Unpublished Reason')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="reason"></div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#reasonModal').on('show.bs.modal', function(e) {
                let content = `<ul class="list-group list-group-flush">`;
                let reasons = $(e.relatedTarget).data('reasons');
                let i = 1;
                $.each(reasons, function(index, element) {
                    content += `<li class="list-group-item text--danger fw-bold"> ${i}. ${element} </li>`;
                    i++;
                });
                content += `</ul>`;
                $('#reasonModal').find('.reason').html(content);
            });

        })(jQuery)
    </script>
@endpush
