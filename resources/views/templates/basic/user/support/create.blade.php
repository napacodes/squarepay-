@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="container pt-60 pb-60">
        <div class="row justify-content-center mt-4">
            <div class="col-md-12">
                <div class="text-end">
                    <a href="{{ route('ticket.index') }}" class="btn btn-sm btn--base mb-4"><i class="las la-ticket-alt"></i> @lang('All Tickets')</a>
                </div>
                <div class="card custom--card">
                    <div class="card-body">
                        <form action="{{ route('ticket.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Subject')</label>
                                        <input type="text" name="subject" value="{{ old('subject') }}" class="form-control " required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Priority')</label>
                                        <select name="priority" class="form-control select2" required>
                                            <option value="3">@lang('High')</option>
                                            <option value="2">@lang('Medium')</option>
                                            <option value="1">@lang('Low')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Message')</label>
                                        <textarea name="message" id="inputMessage" rows="6" class="form-control " required>{{ old('message') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <button type="button" class="btn btn--base btn-sm addAttachment my-2"> <i class="fas fa-plus"></i> @lang('Add Attachment') </button>
                                    <p class="mb-2"><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is '.convertToReadableSize(ini_get('upload_max_filesize')) .' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                                    <div class="row fileUploadsContainer">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn--base w-100 my-2" type="submit"><i class="las la-paper-plane"></i> @lang('Submit')
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <button class="btn btn--base w-100 mt-4" type="submit">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
        .select2-container .select2-selection--single {
            height: 50px;
            border: 1px solid #e5e5e5 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #363636 !important;
            line-height: 50px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: rgb(0 0 0 / 10%);
            color: black;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid rgba(0, 0, 0, 0.15);
            background: transparent;
            margin-bottom: 6px;
            color: black;
        }

        .select2-dropdown {
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.15);
        }

        .select2-results__option--selectable {
            cursor: pointer;
            color: #000;
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
                                <input type="file" name="attachments[]" class="form-control custom-input-field" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile btn--danger border--danger"><i class="fas fa-times"></i></button>
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

