@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-60 pb-60">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-sm-12">
                    @include($activeTemplate . 'partials.reviews')
                </div>
            </div>
        </div>
    </section>
@endsection
