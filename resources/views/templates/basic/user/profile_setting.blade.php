@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $profileImage = fileManager()->userProfile();
    @endphp

    <div class="section--bg pt-60 pb-60">
        <div class="container">
            <form class="register prevent-double-click"  method="post" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-lg-5">
                        <div class="profile-setting-sidebar">
                            <div class="profile-thumb-wrapper text-center">
                                <div class="profile-thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview" style="background-image: url({{ getImage($profileImage->path . '/' . @$user->image, null, true) }})"></div>
                                    </div>
                                    <div class="avatar-edit" title="@lang('jpg, jpeg, png image only')">
                                        <input type='file' class="profilePicUpload" id="profilePicUpload1" name="image" accept=".jpg, .jpeg, .png" />
                                        <label for="profilePicUpload1"><i class="las la-upload"></i> @lang('Update')</label>
                                    </div>
                                </div>
                            </div>
                            <ul class="caption-list mt-4">
                                <li>
                                    <span class="caption">@lang('Username')</span>
                                    <span class="value">{{ $user->username }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Email Address')</span>
                                    <span class="value">{{ $user->email }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Mobile Number')</span>
                                    <span class="value">+{{ $user->mobile }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('Country')</span>
                                    <span class="value">{{ @$user->country_name }}</span>
                                </li>
                                <li>
                                    <span class="caption">@lang('KYC')</span>
                                    @if ($user->kv == 1)
                                        <span class="value"><span class="badge badge--success">@lang('Verified')</span></span>
                                    @else
                                        <span class="value"><span class="badge badge--danger">@lang('Unverified')</span> <span><a href="{{ route('user.kyc.form') }}" class="text--base">@lang('Verify Now')</a></span></span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7 mt-lg-0 mt-4">
                        <div class="custom--card bg-white">
                            <div class="card-body">

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label for="InputFirstname">@lang('First Name')</label>
                                        <input type="text" class="form-control" id="InputFirstname" name="firstname" placeholder="@lang('First Name')" value="{{ $user->firstname }}" minlength="3">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label for="lastname">@lang('Last Name')</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="@lang('Last Name')" value="{{ $user->lastname }}" required>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="state">@lang('State')</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="@lang('state')" value="{{ $user->state }}" required="">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="zip">@lang('Zip Code')</label>
                                        <input type="text" class="form-control" id="zip" name="zip" placeholder="@lang('Zip Code')" value="{{ $user->zip }}" required="">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="city">@lang('City')</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="@lang('City')" value="{{ $user->city }}" required="">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label for="address">@lang('Address')</label>
                                        <input type="text" class="form-control" id="address" name="address" placeholder="@lang('Address')" value="{{ $user->address }}" required="">
                                    </div>
                                </div>
                                <div class="form-group  mb-0 mt-3">
                                    <button type="submit" class="btn--base w-100">@lang('Save Changes')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .profile-thumb .avatar-edit label {
            background-color: #fca120;
            color: #fff;
        }

        .profile-thumb {
            position: relative;
            width: 12.5rem;
            height: 12.5rem;
            border-radius: 15px;
            -webkit-border-radius: 15px;
            -moz-border-radius: 15px;
            -ms-border-radius: 15px;
            -o-border-radius: 15px;
            display: inline-flex;
        }

        .profile-thumb .profilePicPreview {
            width: 12.5rem;
            height: 12.5rem;
            border-radius: 15px;
            -webkit-border-radius: 15px;
            -moz-border-radius: 15px;
            -ms-border-radius: 15px;
            -o-border-radius: 15px;
            display: block;
            border: 2px solid #ffffff;
            box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.15);
            background-size: cover;
            background-position: top;
        }

        .profile-thumb .profilePicUpload {
            font-size: 0;
            opacity: 0;
        }

        .profile-thumb .avatar-edit {
            position: absolute;
            right: 0;
            bottom: -10px;
        }

        .profile-thumb .avatar-edit input {
            width: 0;
        }

        .profile-thumb .avatar-edit label {
            font-size: 0.75rem;
            padding: 0.0625rem 0.9375rem;
            border-radius: 999px;
            -webkit-border-radius: 999px;
            -moz-border-radius: 999px;
            -ms-border-radius: 999px;
            -o-border-radius: 999px;
            cursor: pointer;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            function proPicURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var preview = $(input).parents('.profile-thumb').find('.profilePicPreview');
                        $(preview).css('background-image', 'url(' + e.target.result + ')');
                        $(preview).addClass('has-image');
                        $(preview).hide();
                        $(preview).fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".profilePicUpload").on('change', function() {
                proPicURL(this);
            });

            $(".remove-image").on('click', function() {
                $(".profilePicPreview").css('background-image', 'none');
                $(".profilePicPreview").removeClass('has-image');
            });
        })(jQuery);
    </script>
@endpush
