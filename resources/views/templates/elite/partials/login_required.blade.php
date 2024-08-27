<div class="modal fade custom--modal" id="loginRequired" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="existModalLongTitle">@lang('Login Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6 class="text-center">@lang('Login required for this action')</h6>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn--sm btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                <a href="{{ route('user.login') }}" class="btn btn--sm btn--base">@lang('Login')</a>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            $(document).on('click', '.loginRequired',function () {
                $('#loginRequired').modal('show');
            });
        })(jQuery);
    </script>
@endpush

