<div aria-hidden="true" class="custom--modal modal fade" id="confirmationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">@lang('Confirmation Alert!')</h5>
                <span aria-label="Close" class="close" data-bs-dismiss="modal" type="button">
                    <i class="las la-times"></i>
                </span>
            </div>
            <form  method="POST">
                @csrf
                <div class="modal-body">
                    <p class="question"></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--danger btn--sm" data-bs-dismiss="modal" type="button">@lang('No')</button>
                    <button class="btn btn--base btn--sm" type="submit">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.confirmationBtn', function() {
                var modal = $('#confirmationModal');
                let data = $(this).data();
                modal.find('.question').text(`${data.question}`);
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .custom--modal .modal-body .notice-text {
            margin-bottom: 15px;
        }
    </style>
@endpush
