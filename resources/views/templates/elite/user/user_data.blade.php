@extends($activeTemplate . 'layouts.master_without_menu')

@section('content')
    <section class="py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-8 col-xl-8">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="alert alert-warning" role="alert">
                                <strong> <i class="la la-info-circle text--warning"></i> @lang('You need to complete your profile to get access to your dashboard')</strong>
                            </div>

                            <form method="POST" action="{{ route('user.data.submit') }}" class="mt-3">
                                @csrf
                                <div class="row">
                                    <div class="form-group">
                                        <label class="form--label">@lang('Username')</label>
                                        <input class="form--control checkUser" id="username" name="username" required type="text" value="{{ old('username') }}">
                                        <small class="text-danger usernameExist"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form--label">@lang('Country')</label>
                                        <select class="select form--control select2" id="country" name="country" required>
                                            @foreach ($countries as $key => $country)
                                                <option data-code="{{ $key }}" data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}">{{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
            
                                    <div class="form-group">
                                        <label class="form--label">@lang('Mobile')</label>
                                        <div class="input-group">
                                            <span class="input-group-text mobile-code">

                                            </span>
                                            <input name="mobile_code" type="hidden">
                                            <input name="country_code" type="hidden">
            
                                            <input class="form-control form--control checkUser" name="mobile" placeholder="@lang('Your Phone Number')" required type="number" value="{{ old('mobile') }}">
                                        </div>
                                        <small class="text-danger mobileExist"></small>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Address')</label>
                                        <input type="text" class="form--control" name="address" value="{{ old('address') }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('State')</label>
                                        <input type="text" class="form--control" name="state" value="{{ old('state') }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Zip Code')</label>
                                        <input type="text" class="form--control" name="zip" value="{{ old('zip') }}">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('City')</label>
                                        <input type="text" class="form--control form--control" name="city" value="{{ old('city') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base-two w-100">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {

            $('select[name=country]').on('change',function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value,name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));


            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name')
                checkUser(value,name);
            });

            function checkUser(value,name){
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code:$('.mobile-code').text().substr(1),
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                     if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.field} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            }
        })(jQuery);
    </script>
@endpush
