@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30 justify-content-center">

    <div class="col-xl-4 col-md-4 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Date')
                        <span class="font-weight-bold">{{ showDateTime($withdrawal->created_at) }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Wallet Address')
                        <span class="font-weight-bold">{{ $withdrawal->wallet_address }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Trx Number')
                        <span class="font-weight-bold">{{ $withdrawal->trx }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Username')
                        <span class="font-weight-bold">
                            <a href="{{ route('admin.users.detail', $withdrawal->user_id) }}">{{ @$withdrawal->user->username }}</a>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Amount')
                        <span class="font-weight-bold">{{ showAmount($withdrawal->amount,8) }} {{ __($withdrawal->crypto->code) }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Charge')
                        <span class="font-weight-bold">{{ showAmount($withdrawal->charge,8) }} {{ __($withdrawal->crypto->code) }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payable')
                        <span class="font-weight-bold">{{ showAmount($withdrawal->payable,8 ) }} {{ __($withdrawal->crypto->code) }}</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Status')
                        @php echo $withdrawal->statusBadge; @endphp
                    </li>

                    @if ($withdrawal->status == Status::PAYMENT_PENDING)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Action')
                            <span>
                                <button class="btn btn-sm btn-outline--success ms-1 approveBtn" data-id="{{ $withdrawal->id }}" data-amount="{{ showAmount($withdrawal->payable,8) }} {{$withdrawal->crypto->code}}">
                                    <i class="fas la-check"></i> @lang('Approve')
                                </button>

                                <button class="btn btn-sm btn-outline--danger ms-1 rejectBtn" data-id="{{ $withdrawal->id }}">
                                    <i class="fas fa-ban"></i> @lang('Reject')
                                </button>
                            </span>
                        </li>
                    @endif

                    @if($withdrawal->admin_feedback)
                        <li class="list-group-item">
                            <strong>@lang('Admin Response')</strong>
                            <br>
                            <p>{{$withdrawal->admin_feedback}}</p>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>



{{-- APPROVE MODAL --}}
<div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Approve Withdrawal Confirmation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.withdraw.data.approve') }}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <p>@lang('Have you sent') <span class="fw-bold withdraw-amount text--success"></span>?</p>
                    <p class="withdraw-detail"></p>
                    <textarea name="details" class="form-control pt-3" value="{{ old('details') }}" rows="3" placeholder="@lang('Provide the details. eg: transaction number')" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- REJECT MODAL --}}
<div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Reject Withdrawal Confirmation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{route('admin.withdraw.data.reject')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Reason of Rejection')</label>
                        <textarea name="details" class="form-control pt-3" rows="3" value="{{ old('details') }}" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.approveBtn').on('click', function() {
                var modal = $('#approveModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.modal('show');
            });

            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });
        })(jQuery);

    </script>
@endpush
