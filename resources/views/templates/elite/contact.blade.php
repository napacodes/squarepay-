@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $content = getContent('contact.content', true);
        $elements = getContent('contact.element', orderById: true);
    @endphp

    <section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <div class="contact-heading">
                        <h1 class="contact-heading__title" s-break="-3" s-color="base-two"> {{ __(@$content->data_values->heading) }} </h1>
                        <p class="contact-heading__desc">{{ __(@$content->data_values->subheading) }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-5 col-lg-6">
                    <form  class="contact-form verify-gcaptcha" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form--label">@lang('Name')</label>
                            <input @if ($user) readonly @endif class="form--control" name="name" required type="text" value="{{ old('name', @$user->fullname) }}">
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Email')</label>
                            <input @if ($user) readonly @endif class="form--control" name="email" required type="email" value="{{ old('email', @$user->email) }}">
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Subject')</label>
                            <input class="form--control" name="subject" required type="text" value="{{ old('subject') }}">
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Message')</label>
                            <textarea class="form--control" name="message" placeholder="@lang('Write your message').." wrap="off">{{ old('message') }}</textarea>
                        </div>

                        <x-captcha :path="$activeTemplate . 'partials.'" />

                        <div class="form-group">
                            <button class="btn btn--base-two" type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
                <div class="col-xl-6 offset-xl-1 col-lg-6">
                    <div class="contact-thumb">
                        <img alt="" src="{{ frontendImage('contact' , @$content->data_values->image, '615x430') }}">
                    </div>
                    <div class="contact-info-wrapper">
                        <div class="row">
                            @foreach ($elements as $item)
                                <div class="col-sm-6">
                                    <div class="contact-info">
                                        <span class="contact-info__icon"><img alt="" src="{{ frontendImage('contact', @$item->data_values->image, '44x44') }}"></span>
                                        <h5 class="contact-info__title">{{ __($item->data_values->name) }}</h5>
                                        <p class="contact-info__desc"> {{ __($item->data_values->details) }} </p>
                                        @if ($item->data_values->address_type == 'mail')
                                            <a class="contact-info__info" href="mailto:{{ $item->data_values->address }}">{{ $item->data_values->address }}</a>
                                        @else
                                            <a class="contact-info__info" href="tel:{{ $item->data_values->address }}">{{ $item->data_values->address }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include($activeTemplate . 'sections.' . $sec)
        @endforeach
    @endif

@endsection
