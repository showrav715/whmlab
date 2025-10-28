<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>

    <link rel="shortcut icon" href="{{ siteFavicon() }}" type="image/x-icon">
</head> 

<body>
    @include('partials.invoice')
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <span class="d-block text-center">
                        @lang('Copyright') &copy; {{ date('Y') }} @lang('All Right Reserved By')
                        <span class=" text-base">{{ __(gs('site_name')) }}</span>
                    </span>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
