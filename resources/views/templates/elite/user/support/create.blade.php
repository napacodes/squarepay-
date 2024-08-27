@extends($activeTemplate . 'layouts.master_without_menu')
@section('content')
        <form action="{{ route('ticket.store') }}" enctype="multipart/form-data" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Subject')</label>
                        <input class="form--control" name="subject" required type="text" value="{{ old('subject') }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Priority')</label>
                        <select class="form--control select2" name="priority" required data-minimum-results-for-search="-1">
                            <option value="3">@lang('High')</option>
                            <option value="2">@lang('Medium')</option>
                            <option value="1">@lang('Low')</option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label class="form--label">@lang('Message')</label>
                        <textarea class="form--control " id="inputMessage" name="message" required rows="6">{{ old('message') }}</textarea>
                    </div>
                </div>

                <div class="col-md-9">
                    <button type="button" class="btn btn--base btn--sm addAttachment my-2"> <i class="fas fa-plus"></i> @lang('Add Attachment') </button>
                    <p class="mb-2"><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is '.convertToReadableSize(ini_get('upload_max_filesize')) .' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                    <div class="row fileUploadsContainer">
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="btn btn--base w-100 my-2" type="submit"><i class="las la-paper-plane"></i> @lang('Submit')
                    </button>
                </div>

            </div>
        </form>
@endsection

@push('breadcrumb-plugins')
    <a class="ptable-header-right__link" href="{{ route('ticket.index') }}">
        <span class="icon"><i class="las la-list"></i></span>
        <span class="text">@lang('All Tickets')</span>
    </a>
@endpush

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click',function(){
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled',true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control custom-input-field border-end-0" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border--danger text-white"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click','.removeFile',function(){
                $('.addAttachment').removeAttr('disabled',true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
