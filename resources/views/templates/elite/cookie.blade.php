@extends($activeTemplate.'layouts.frontend')
@section('content')
    <section class="pt-120 pb-120">
        <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 mb-4 ">
                <h3 class="mb-4">{{__($pageTitle)}}</h4>
                @php
                    echo __($cookie->data_values->description);
                @endphp
            </div>
        </div>
        </div>
    </section>
@endsection
