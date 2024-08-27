@extends($activeTemplate .'layouts.frontend')
@section('content')
<section class="pt-120 pb-120 mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 mb-4 text-center">
                <h3 class="mb-4 text-danger">@lang('YOU ARE BANNED')</h3>
                <p class="fw-bold mb-1">@lang('Reason'):</p>
                <p>{{ $user->ban_reason }}</p>
            </div>
        </div>
    </div>
</section>
@endsection

