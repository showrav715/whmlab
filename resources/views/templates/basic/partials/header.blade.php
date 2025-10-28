<div class="header bg--dark">
    <div class="container">
        <div class="header-bottom">
            <div class="header-bottom-area align-items-center">
                <div class="logo">
                    <a href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="@lang('logo')">
                    </a>
                </div>
                <ul class="menu">
                    <li>
                        <a href="{{ route('home') }}">@lang('Home')</a>
                    </li>
                    <li>
                        <a href="#0">@lang('Store')</a>
                        <ul class="sub-menu">
                            @if (@$serviceCategories->first())
                                <li>
                                    <a href="{{ route('service.category', [@$serviceCategories->first()->slug, 'all=']) }}">@lang('Browse All')</a>
                                </li>
                            @endif
                            @foreach ($serviceCategories as $serviceCategory)
                                <li>
                                    <a href="{{ route('service.category', $serviceCategory->slug) }}">
                                        {{ __($serviceCategory->name) }}
                                    </a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ route('register.domain') }}">@lang('Register New Domain')</a>
                            </li>
                        </ul>
                    </li>

                    @php
                        $pages = App\Models\Page::where('tempname', $activeTemplate)
                            ->where('is_default', 0)
                            ->get();
                    @endphp

                    @foreach ($pages as $k => $data)
                        <li>
                            <a href="{{ route('pages', [$data->slug]) }}">{{ __($data->name) }}</a>
                        </li>
                    @endforeach

                    <li>
                        <a href="{{ route('blogs') }}">@lang('Announcements')</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}">@lang('Contact')</a>
                    </li>

                    @auth
                        <div class="header-buttons d-flex flex-wrap ms-xl-4 ms-0">
                            <li class="menu-btn">
                                <a href="{{ route('user.home') }}" class="text--white ps-2 d-inline-block"> <i class="las la-home"></i> @lang('Dashboard')</a>
                            </li>
                            <li class="menu-btn ms-xl-2">
                                <a href="{{ route('user.logout') }}" class="btn--base-outline me-xl-2 ms-xl-0 ms-2 ps-2 d-inline-block"> <i class="las la-sign-out-alt"></i> @lang('Logout')</a>
                            </li>
                        </div>
                    @else
                        <li class="menu-btn">
                            <a href="{{ route('user.login') }}" class="text--white ps-2 d-inline-block"> <i class="las la-sign-in-alt"></i> @lang('Login')</a>
                        </li>
                    @endauth

                </ul>
                <div class="d-flex align-items-center ms-xl-2 ms-auto me-xl-0 me-2">
                    @include($activeTemplate . 'partials.cart_widget')
                    <x-currency-switcher />
                    <x-language />
                </div>
                <div class="header-trigger-wrapper d-flex d-xl-none align-items-center">
                    <div class="header-trigger">
                        <div class="header-trigger__icon"> <i class="las la-bars"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
