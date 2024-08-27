@php
    $cryptos = App\Models\CryptoCurrency::active()->get();
@endphp
<div class="row mt-5">
    <div class="col-lg-12">
        <ul class="custom--tab nav nav-tabs justify-content-center bg-transparent">
            @foreach ($cryptos as $cryptoData)
                <li class="nav-item goto-more-{{ $type }}">
                    <a class="nav-link crypto-currency-{{ $type }} @if ($loop->first) active @endif" data-bs-toggle="tab" data-code="{{ $cryptoData->code }}" data-id="{{ $cryptoData->id }}" href="#{{ $cryptoData->code }}-{{ $type }}" id="{{ $cryptoData->code }}-{{ $type }}-tab" role="tab">{{ __($cryptoData->code) }}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content mt-4">
            @foreach ($cryptos as $cryptoData)
                <div aria-labelledby="{{ $cryptoData->code }}-{{ $type }}-tab" class="tab-pane bg-transparent fade content-load-{{$type}} @if ($loop->first) show active @endif" id="{{ $cryptoData->code }}-{{ $type }}" role="tabpanel">
                    <div class="d-flex justify-content-center align-items-center currency-loading">
                        <h4><i class="fa fa-spinner fa-spin text-muted"></i></h4>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <form action="{{ route('advertisement.all') }}" class="{{ $type }}-submit" method="GET">
                <input name="type" type="hidden" value="{{ $type }}">
                <input name="crypto_code" type="hidden">
                <input name="country" type="hidden" value="all">
                <input name="country_code" type="hidden" value="all">
                <button class="btn btn--base btn--sm" id="{{ $type }}-more">@lang('More')</button>
            </form>
        </div>
    </div>
</div>




@push('script')
    <script>
        'use strict';

        (function($) {
            let cryptoCount = '{{ $cryptos->count() }}';
            let type = `{{ $type }}`

            if (parseInt(cryptoCount) > 0) {
                getAds(`{{ $cryptos->first()->id ?? 0 }}`);
            }

            $(`.crypto-currency-${type}`).on('click', function() {
                $(`.content-load-${type}`).html(
                    `<div class="d-flex justify-content-center align-items-center currency-loading">
                        <h4><i class="fa fa-spinner fa-spin text-muted"></i></h4>
                    </div>`
                );
                getAds($(this).data('id'));
            });

            function getAds(id) {
                let url=`{{ route('advertisement.all', ':type',':crypto') }}`;

                $.get(url.replace(":type",type).replace(':crypto',cryptoCode)
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
