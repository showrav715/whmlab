<div class="py-2 breadcrumb-bg">
    <div class="container px-3 justify-content-between d-flex flex-wrap align-items-center">
        <div>
            @if (request()->routeIs('home'))
                {{ __($pageTitle) }}
            @else
                <a href="{{ route('home') }}" class="anchor-decoration text--base">@lang('Home')</a> / {{ __($pageTitle) }}
            @endif
        </div>

    </div>
</div>
