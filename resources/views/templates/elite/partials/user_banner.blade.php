@php
    $content = getContent('banner.content', true);
    $user = auth()
        ->user()
        ->load('loginLogs');
    $lastLogin = \App\Models\UserLogin::where('user_id', $user->id)
        ->latest()
        ->first();
@endphp

<section class="account-setting-banner">
    <div class="banner-section__shape">
        <img alt="" src="{{ getImage($activeTemplateTrue . 'images/banner-shape.png') }}">
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="author">
                    <div class="author__thumb">
                        <img alt="" src="{{ getImage(getFilePath('userProfile') . '/' . @$user->image, getFileSize('userProfile')) }}">
                    </div>
                    <h3 class="author__hello">{{ __($user->fullname) }}</h3>
                    <h2 class="author__email">{{ __($user->email) }}</h2>
                </div>
                <div class="author-details">
                    <div class="author-details__item">
                        <span class="author-details__title">@lang('Username')</span>
                        <p class="author-details__info">{{ $user->username }}</p>
                    </div>
                    @if (gs('kv')==Status::ENABLE)
                        <div class="author-details__item">
                            <span class="author-details__title">@lang('KYC Verification')
                                @if ($user->kv == Status::NO)
                                    ( <a href="{{ route('user.kyc.form') }}">@lang('Verify Now')</a>)
                                @endif
                            </span>
                            <p class="author-details__info @if ($user->kv == Status::KYC_VERIFIED) verified @elseif($user->kv == Status::KYC_UNVERIFIED) secure @endif">
                                @if ($user->kv == Status::KYC_VERIFIED)
                                    <i class="fas fa-check"></i> @lang('Verified')
                                @elseif($user->kv == Status::KYC_PENDING)
                                    <i class="fas fa-spinner"></i> @lang('Pending')
                                @else
                                    <i class="fas fa-times"></i> @lang('Unverified')
                                @endif
                            </p>
                        </div>
                    @endif
                    <div class="author-details__item">
                        <span class="author-details__title">@lang('Security')
                            @if ($user->ts == Status::DISABLE)
                                ( <a href="{{ route('user.twofactor') }}">@lang('Secure Now')</a>)
                            @endif
                        </span>
                        <p class="author-details__info @if ($user->ts == Status::ENABLE) verified @else secure @endif">
                            <i class="fas fa-shield-alt"></i>
                            @if ($user->ts == Status::ENABLE)
                                @lang('Highly Secure')
                            @else
                                @lang('Less Secure')
                            @endif
                        </p>
                    </div>
                    <div class="author-details__item">
                        <span class="author-details__title">@lang('Last Login')</span>
                        <p class="author-details__info">{{ showDateTime(@$lastLogin->created_at, 'F j, Y, g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
