@php
    $registrationDisabled = getContent('register_disable.content', true);
@endphp

@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="register-disable">
        <div class="container">
            <div class="register-disable-image">
                <img class="fit-image" src="{{ frontendImage('register_disable', @$registrationDisabled->data_values->image, '280x280') }}" alt="@lang('image')">
            </div>

            <h5 class="register-disable-title">{{ __(@$registrationDisabled->data_values->heading) }}</h5>
            <p class="register-disable-desc">
                {{ __(@$registrationDisabled->data_values->subheading) }}
            </p>
            <div class="text-center">
                <a href="{{ @$registrationDisabled->data_values->button_url }}" class="register-disable-footer-link">{{ __(@$registrationDisabled->data_values->button_name) }}</a>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .register-disable {
            height: 100vh;
            width: 100%;
            background-color: #fff;
            color: black;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-disable-image {
            max-width: 300px;
            width: 100%;
            margin: 0 auto 32px;
        }

        .register-disable-title {
            color: rgb(0 0 0 / 80%);
            font-size: 42px;
            margin-bottom: 18px;
            text-align: center
        }

        .register-disable-icon {
            font-size: 16px;
            background: rgb(255, 15, 15, .07);
            color: rgb(255, 15, 15, .8);
            border-radius: 3px;
            padding: 6px;
            margin-right: 4px;
        }

        .register-disable-desc {
            color: rgb(0 0 0 / 50%);
            font-size: 18px;
            max-width: 565px;
            width: 100%;
            margin: 0 auto 32px;
            text-align: center;
        }

        .register-disable-footer-link {
            color: #fff;
            background-color: #5B28FF;
            padding: 13px 24px;
            border-radius: 6px;
            text-decoration: none
        }

        .register-disable-footer-link:hover {
            background-color: #440ef4;
            color: #fff;
        }

        .btn--base {
            background: hsl(var(--base));
            color: #fff;
        }

        .btn--base:hover {
            color: #fff;
            background: hsl(var(--base) / 0.9) !important;
        }
    </style>
@endpush
