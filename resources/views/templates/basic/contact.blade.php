@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $contactContent = getContent('contact.content', true);
        $contactElements = getContent('contact.element');
    @endphp
    <div class="container  pt-120 pb-120">
        <div class="row justify-content-center gy-4">
            <div class="col-lg-6 order-lg-1 order-2">
                <div class="contact-form-wrapper">
                    <form class="contact-form verify-gcaptcha"  method="POST">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input name="name" type="text" class="form-control" value="{{ old('name',@$user->fullname) }}" @if($user) readonly @endif required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input name="email" type="email" class="form-control" value="{{  old('email',@$user->email) }}" @if($user) readonly @endif required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Subject')</label>
                                    <input name="subject" type="text" placeholder="@lang('Write your subject')" class="form-control" value="{{ old('subject') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Message')</label>
                                    <textarea name="message" wrap="off" placeholder="@lang('Write your message')" class="form-control">{{ old('message') }}</textarea>
                                </div>
                            </div>

                            <x-captcha />

                            <div class="col-lg-12">
                                <button type="submit" class="btn--base w-100">@lang('Submit')</button>
                            </div>
                        </div>
                    </form>
                </div><!-- contact-form-wrapper end -->
            </div>
            <div class="col-lg-6 pl-lg-5 order-lg-2 order-1 mb-lg-0">
                <div class="d-flex flex-wrap gap-3">
                    @foreach ($contactElements as $contact)
                        <div class="contact-item w-100">
                            <div class="icon">
                                @php echo @$contact->data_values->icon @endphp
                            </div>
                            <div class="content">
                                <h4 class="title">{{ __(@$contact->data_values->heading) }}</h4>
                                <p>{{ __(@$contact->data_values->details) }}</p>
                            </div>
                        </div><!-- contact-item end -->
                    @endforeach
                </div>
            </div>

        </div>
    </div>
    <div class="map-area">
        <iframe src="https://maps.google.com/maps?q={{ @$contactContent->data_values->latitude }},{{ @$contactContent->data_values->longitude }}&hl=es;z=14&amp;output=embed"></iframe>
    </div>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
