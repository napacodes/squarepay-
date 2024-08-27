@php
    $cryptos = App\Models\CryptoCurrency::active()->get();
@endphp
<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-tabs custom--style-two justify-content-center bg-transparent">
            @foreach ($cryptos as $cryptoData)
                <li class="nav-item goto-more-{{ $type }}">
                    <a class="nav-link crypto-currency-{{ $type }} @if ($loop->first) active @endif" data-bs-toggle="tab" data-code="{{ $cryptoData->code }}" href="#{{ $cryptoData->code }}-{{ $type }}" id="{{ $cryptoData->code }}-{{ $type }}-tab" role="tab">{{ __($cryptoData->code) }}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content mt-4">
            @foreach ($cryptos as $cryptoData)
                <div aria-labelledby="{{ $cryptoData->code }}-{{ $type }}-tab" class="tab-pane bg-transparent fade content-load-{{ $type }} @if ($loop->first) show active @endif" id="{{ $cryptoData->code }}-{{ $type }}" role="tabpanel">
                    <div class="d-flex justify-content-center align-items-center currency-loading">
                        <h4><i class="fa fa-spinner fa-spin text-muted"></i></h4>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="btn-group mt-4 justify-content-center">
            <a  class="btn--base btn-sm" id="{{ $type }}-more">@lang('More')</a>
        </div>
    </div>
</div>

@push('script')
    <script>
        'use strict';

        (function($) {
            let cryptoCount = '{{ $cryptos->count() }}';
            let type        = `{{ $type }}`
            if (parseInt(cryptoCount) > 0) {
                getAds(`{{ $cryptos->first()->code ?? 0 }}`);
            }

            $(`.crypto-currency-${type}`).on('click', function() {
                $(`.content-load-${type}`).html(
                    `<div class="d-flex justify-content-center align-items-center currency-loading">
                        <h4><i class="fa fa-spinner fa-spin text-muted"></i></h4>
                    </div>`
                );
                getAds($(this).data('code'));
            });

            function getAds(code) {
                let url=`{{ route('advertisement.all', ['type' => ':type','crypto' => ':crypto']) }}`;
                url=url.replace(":type",type).replace(':crypto',code)
                $("#{{ $type }}-more").attr('href',url)
                $.get(url,
                    function(data, status) {
                        if (status == 'success') {
                            $(`.content-load-${type}`).html(data.html);
                            tableResponsive();
                        }
                    }
                );
            }
            $(`#${type}-more`).on('click', function() {
                if ($(`.crypto-currency-${type}`).hasClass('active')) {
                    var cryptoCode = $(`.goto-more-${type}`).find('.active').data('code');
                    $(`.${type}-submit`).find('input[name="crypto_code"]').val(cryptoCode);
                }
            });
        })(jQuery);
    </script>
@endpush


@push('style')
    <style>
        .currency-loading {
            height: 400px;
            background-color: #fca12038
        }
    </style>
@endpush
