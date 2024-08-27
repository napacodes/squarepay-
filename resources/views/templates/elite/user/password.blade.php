@extends($activeTemplate . 'layouts.master_with_menu')

@section('content')
    <form  class="register" method="post">
        @csrf
        <div class="form-group">
            <label class="form--label">@lang('Current Password')</label>
            <input class="form--control" name="current_password" required type="password">
        </div>
        <div class="form-group">
            <label class="form--label">@lang('Password')</label>
            <input class="form--control @if (gs('secure_password')) secure-password @endif" name="password" required type="password">
        </div>
        <div class="form-group">
            <label class="form--label">@lang('Confirm Password')</label>
            <input class="form--control" name="password_confirmation" required type="password">
        </div>
        <div class="form-group">
            <input class="btn btn--base-two" type="submit" value="@lang('Change Password')">
        </div>
    </form>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
