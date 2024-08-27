@php
    $profileImage = fileManager()->userProfile();
@endphp

@forelse ($reviews as $review)
    <div class="review-item mb-4">
        <div class="review-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div class="review-user d-flex flex-wrap gap-2 align-items-center">
                <img alt="image" class="mt-0 user-image" src="{{ getImage(getFilePath('userProfile') . '/' . @$review->user->image, null, true) }}">
                <div class="d-inline-block">
                    <span><b class="fw-medium pl-1">{{ __($review->user->username) }}</b></span>
                    <div class="review-status mt-1">
                        <div class="d-flex flex-column gap-2 justify-content-center align-items-center">
                            @if ($review->type == 1)
                                <small class="text--success"><i class="fas fa-thumbs-up"></i> @lang('Positive')</small>
                            @else
                                <small class="text--danger"><i class="fas fa-thumbs-down"></i> @lang('Negative')</small>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <div class="d-flex gap-2">
                <small><em>{{ showDateTime($review->created_at, 'd M, Y h:i A') }}</em></small>

                @if ($review->user->id == auth()->id())
                    <button class="text--base bg-transparent editBtn fs--14px" data-feedback="{{ $review->feedback }}" data-id="{{ $review->tradeRequest->uid }}" data-type="{{ $review->type }}"><i class="fas fa-pencil-alt fs--13px"></i> @lang('Edit')</button>
                @endif
            </div>
        </div>
        <p>{{ __($review->feedback) }}</p>
    </div>
@empty
    <x-no-data message="No feedback yet"></x-no-data>
@endforelse

@if ($reviews->hasPages())
    <div class="pagination-wrapper">
        {{ $reviews->links() }}
    </div>
@endif

<div aria-hidden="true" aria-labelledby="myModalLabel" class="modal fade" id="editModal" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"> @lang('Update Review')!</h5>
                <button aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                    <i class="las la-times"></i>
                </button>
            </div>

            <form class="contact-form give-review-form" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-lg-12 order-lg-1 order-2">
                            <div class="contact-form-wrapper">
                                <div class="row align-items-center">
                                    <div class="col-12 form-group">
                                        <div class="d-flex flex-wrap" style="gap:6px 10px">
                                            <div class="form-check review-input-group p-0">
                                                <input class="form-check-input review-input d-none positive-review" id="positive-review" name="type" type="radio" value="1">
                                                <label class="form-check-label review-label positive-label" for="positive-review">
                                                    <span class="icon"><i class="far fa-thumbs-up"></i></span>
                                                    @lang('Positive')
                                                </label>
                                            </div>
                                            <div class="form-check review-input-group">
                                                <input class="form-check-input review-input d-none negative-review" id="negative-review" name="type" type="radio" value="0">
                                                <label class="form-check-label review-label negative-label" for="negative-review">
                                                    <span class="icon"><i class="far fa-thumbs-down"></i></span>
                                                    @lang('Negative')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 form-group">
                                        <label>@lang('Your Review')</label>
                                        <textarea class="form--control check-length user-feedback" data-length="500" id="check-length" name="feedback" placeholder="@lang('Your feedback')" rows="4">{{ old('feedback') }}</textarea>
                                        <span class="remaining text-left mt-2"><i class="las la-info-circle"></i>
                                            @lang('500 characters remaining')</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button class="btn btn-sm btn--base w-100" type="submit"> @lang('Update')
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.editBtn').on('click', function() {
                var editModal = $('#editModal');
                var feedback = $(this).data('feedback');
                var type = $(this).data('type');
                var id = $(this).data('id');

                if (type == 1) {
                    editModal.find('#positive-review').prop('checked', true);
                    $('.positive-label .icon').html("<i class='fas fa-thumbs-up text--success'></i>")
                } else {
                    editModal.find('#negative-review').prop('checked', true);
                    $('.negative-label .icon').html("<i class='fas fa-thumbs-down text--danger'></i>")
                }

                editModal.find('.user-feedback').val(feedback);
                editModal.find('.give-review-form').attr('action',
                    `{{ route('user.review.store', '') }}/${id}`);

                var nameLength = editModal.find('.user-feedback').val().length;
                editModal.find('.user-feedback').parent('.form-group').find('.remaining').html(
                    `<i class="las la-info-circle"></i> ${500 - nameLength} @lang('characters remaining')`);
                editModal.modal('show');
            });


            $('.check-length').on('input', function() {
                let maxLength = $(this).data('length');
                let currentLength = $(this).val().length;
                let remain = maxLength - currentLength;
                let remainElement = $(this).parent('.form-group').find('.remaining');

                if (remain <= 4) {
                    remainElement.css('color', 'red');
                } else if (remain <= 20) {
                    remainElement.css('color', 'green');
                } else {
                    remainElement.css('color', '#6f6f6f');
                }

                remainElement.html(`<i class="las la-info-circle"></i> ${remain} @lang('characters remaining')`);
            });

            $('.check-length').on('keypress', function() {
                let maxLength = $(this).data('length');
                let currentLength = $(this).val().length;

                if (currentLength >= maxLength) {
                    return false;
                }
            });

            $('.check-length').on('paste', function(e) {
                let paste = false;
                let maxLength = parseInt($("#check-length").data('length'));
                let data = e.originalEvent.clipboardData.getData('text/plain');
                let currentLength = data.length + parseInt($("#check-length").val().length);

                if (currentLength < maxLength) {
                    paste = true;
                } else {
                    notify('error', `Max character allowed ${maxLength}`)
                }

                return paste;
            });

        })(jQuery)
    </script>
@endpush
