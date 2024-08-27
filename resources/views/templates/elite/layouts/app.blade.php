<!doctype html>
<html itemscope itemtype="http://schema.org/WebPage" lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>

    @include('partials.seo')

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">

    <link href="{{ asset($activeTemplateTrue . 'css/main.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" rel="stylesheet">

    @stack('style-lib')
    @stack('style')
    <link href="{{ asset($activeTemplateTrue . 'css/color.php?color=' . gs('base_color') . '&second_color=' . gs('secondary_color')) }}" rel="stylesheet">

</head>
@php echo loadExtension('google-analytics') @endphp

<body>
    <div class="preloader">
        <div class="loader-p"></div>
    </div>

    <div class="body-overlay"></div>

    <div class="sidebar-overlay"></div>

    <a class="scroll-top"><i class="fas fa-angle-double-up"></i></a>

    @yield('panel')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')
    @php echo loadExtension('tawk-chat') @endphp

    @include('partials.notify')

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')
</body>

<script>
    "use strict";

    function tableResponsive() {
        Array.from(document.querySelectorAll('table')).forEach(table => {
            let heading = table.querySelectorAll('thead tr th');
            Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                Array.from(row.querySelectorAll('td')).forEach((column, i) => {
                    if (heading[i]) {
                        (column.colSpan == 100) || column.setAttribute('data-label', heading[i].innerText)
                    }
                });
            });
        });
    }

    tableResponsive();

    $(function() {
        $(".langSel").on("change", function() {
            window.location.href = "{{ route('home') }}/change/" + $(this).val();
        });

        $.each($('input, select, textarea'), function(i, element) {
            if (element.hasAttribute('required')) {
                $(element).closest('.form-group').find('label').first().addClass('required');
            }
        });

        var inputElements = $('input:not([type=checkbox]):not([type=hidden]), select, textarea');
        $.each(inputElements, function(index, element) {
            element = $(element);
            element.closest('.form-group').find('label').attr('for', element.attr('name'));
            element.attr('id', element.attr('name'))
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });


        let elements = document.querySelectorAll('[s-break]');

        Array.from(elements).forEach(element => {
            let html = element.innerHTML;

            if (typeof html != 'string') {
                return false;
            }

            let position = parseInt(element.getAttribute('s-break'));
            let wordLength = parseInt(element.getAttribute('s-length'));


            html = html.split(" ");

            var firstPortion = [];
            var colorText = [];
            var lastPortion = [];

            if (position < 0) {
                colorText = html.slice(position);
                firstPortion = html.slice(0, position);
            } else {
                var lastWord = position + wordLength;
                colorText = html.slice(position, lastWord);
                firstPortion = html.slice(0, position);
                lastPortion = html.slice(lastWord, html.length);
            }

            var color = element.getAttribute('s-color') || "text--white";

            colorText = `<span class="${color}">${colorText.toString().replaceAll(',', ' ')}</span>`;

            firstPortion = firstPortion.toString().replaceAll(',', ' ');
            lastPortion = lastPortion.toString().replaceAll(',', ' ');

            element.innerHTML = `${firstPortion} ${colorText}  ${lastPortion}`;
        });

        $('.select2').select2();
    });
</script>

</html>
