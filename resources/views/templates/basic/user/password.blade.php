@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="container pt-60 pb-60">
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">

                <div class="card custom--card">
                    <div class="card-body p-4">
                        <form  method="post" class="register">
                            @csrf
                            <div class="form-group">
                                <label for="password">@lang('Current Password')</label>
                                <div class="icon-input-field">
                                    <input type="password" class="form-control" name="current_password" required autocomplete="current-password">
                                    <i class="las la-lock"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password">@lang('Password')</label>
                                <div class="icon-input-field">
                                    <input type="password" class="form-control @if(gs('secure_password')) secure-password @endif" name="password" required autocomplete="current-password">
                                    <i class="las la-key"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">@lang('Confirm Password')</label>
                                <div class="icon-input-field">
                                    <input type="password" class="form-control" name="password_confirmation" required autocomplete="current-password">
                                    <i class="las la-key"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="mt-4 btn btn--base w-100" value="@lang('Change Password')">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if(gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
