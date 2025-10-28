<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')

    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@1,400;1,500&family=Maven+Pro:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}" />

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" />

    @stack('style-lib')
    @stack('style')

    <link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/color.php') }}?color={{ gs('base_color') }}">
</head>

@php echo loadExtension('google-analytics') @endphp
<body>

    <!-- Overlay -->
    <div class="overlay"></div>

    <div class="preloader">
        <div class="spinner"></div>
    </div>

    @yield('app')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.validate.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')

    @php echo loadExtension('tawk-chat') @endphp
    
    @include('partials.notify')

    @if(gs('pn'))
        @include('partials.push_script')
    @endif

    @stack('script')

    <script>
        (function($) {
            "use strict";

            var currentUrl = '{{ url()->full() }}';

            $('.menu a[href="' + currentUrl + '"]').addClass('active');
            $('.menu .sub-menu a[href="' + currentUrl + '"]').closest('a').addClass('active');
            $('.menu .sub-menu a[href="' + currentUrl + '"]').parents('.has-sub-menu').find('a').eq(0).addClass('active')

            if ($('.navbar-nav .dropdown-menu a[href="' + currentUrl + '"]').length || "{{ @request()->routeIs('service.category') }}") {
                $('#navbarDropdown').addClass('active');
            }

            var inputElements = $('[type=text],select,textarea');
            $.each(inputElements, function (index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for',element.attr('name'));
                element.attr('id',element.attr('name'))
            });

            $.each($('input, select, textarea'), function (i, element) {
                var elementType = $(element);
                if(elementType.attr('type') != 'checkbox'){
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }

            });

            let disableSubmission = false;
            $('.disableSubmission').on('submit',function(e){
                if (disableSubmission) {
                e.preventDefault()
                }else{
                disableSubmission = true;
                }
            });
        })(jQuery);
    </script>

</body>

</html>
