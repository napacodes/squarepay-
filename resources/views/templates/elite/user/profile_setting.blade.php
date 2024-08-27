@extends($activeTemplate . 'layouts.master_with_menu')
@section('content')
    <div class="ac-setting-content">
        <div class="author">
            <div class="author__thumb">
                <img alt="" class="user_image" src="{{ getImage(getFilePath('userProfile') . '/' . @$user->image, getFileSize('userProfile')) }}">
            </div>
            <label class="author__change-thumb" for="image"><i class="las la-plus-circle"></i> @lang('Change Your Profile Picture')</label>
        </div>
        <div class="ac-setting-form-wrapper">
            <form  class="ac-setting-form row" enctype="multipart/form-data" method="POST">
                @csrf
                <input accept=".jpg, .jpeg, .png" class="profilePicUpload" hidden id="image" name="image" type="file">
                <div class="col-12">
                    <div class="heading mt-0">
                        <h5 class="heading__title">@lang('Personal Information')</h5>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('First Name')</label>
                        <input class="form-control form--control" name="firstname" type="text" value="{{ $user->firstname }}">
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Last Name')</label>
                        <input class="form-control form--control" name="lastname" type="text" value="{{ $user->lastname }}">
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Phone Number')</label>
                        <input class="form-control form--control" disabled type="text" value="+{{ $user->mobile }}">
                    </div>
                </div>
                <div class="col-12">
                    <div class="heading">
                        <h5 class="heading__title">@lang('Address')</h5>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Address')</label>
                        <input class="form-control form--control" name="address" required type="text" value="{{ $user->address }}">
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Zip Code')</label>
                        <input class="form-control form--control" name="zip" required type="text" value="{{ $user->zip }}">
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('City')</label>
                        <input class="form-control form--control" name="city" required type="text" value="{{ $user->city }}">
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('State')</label>
                        <input class="form-control form--control" name="state" required type="text" value="{{ $user->state }}">
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="form-group">
                        <label class="form--label">@lang('Country')</label>
                        <input class="form-control form--control" disabled required type="text" value="{{ $user->country_name }}">
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn--base-two" type="submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            function proPicURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('.user_image').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $(".profilePicUpload").on('change', function() {
                proPicURL(this);
            });
        })(jQuery);
    </script>
@endpush
