<div class="header">
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
                        <a href="{{ route('user.home') }}">@lang('Dashboard')</a>
                    </li>
                    <li>
                        <a href="#0">@lang('Services')</a>
                        <ul class="sub-menu">
                            <li>
                                <a href="{{ route('user.service.list') }}">@lang('My Services')</a>
                            </li>
                            <li>
                                <a href="{{ route('service.category') }}?all">@lang('Order New Service')</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#0">@lang('Domains')</a>
                        <ul class="sub-menu">
                            <li>
                                <a href="{{ route('user.domain.list') }}">@lang('My Domains')</a>
                            </li>
                            <li>
                                <a href="{{ route('register.domain') }}">@lang('Register New Domain')</a>
                            </li>
                            <li>
                                <a href="{{ route('register.domain') }}">@lang('Domain Search')</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('user.invoice.list') }}">@lang('Invoices')</a>
                    </li>
                    <li>
                        <a href="#0">@lang('Support Ticket')</a>
                        <ul class="sub-menu">
                            <li>
                                <a href="{{ route('ticket.open') }}">@lang('Create New')</a>
                            </li>
                            <li>
                                <a href="{{ route('ticket.index') }}">@lang('My Ticket')</a>
                            </li>
                        </ul>
                    </li>

                    @if (gs('deposit_module'))
                        <li>
                            <a href="#0">@lang('Deposit')</a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="{{ route('user.deposit.index') }}">@lang('Deposit Money')</a>
                                </li>
                                <li>
                                    <a href="{{ route('user.deposit.history') }}">@lang('Deposit Log')</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <li>
                        <a href="#0">@lang('Account')</a>
                        <ul class="sub-menu">

                            <li>
                                <a href="{{ route('user.transactions') }}">@lang('Transactions')</a>
                            </li>

                            <li>
                                <a href="{{ route('user.email.history') }}">@lang('Email Log')</a>
                            </li>

                            <li>
                                <a href="{{ route('user.profile.setting') }}">@lang('Profile Setting')</a>
                            </li>

                            <li>
                                <a href="{{ route('user.change.password') }}">@lang('Change Password')</a>
                            </li>

                            <li>
                                <a href="{{ route('user.twofactor') }}">@lang('2FA Security')</a>
                            </li>

                            <li>
                                <a href="{{ route('user.logout') }}">@lang('Logout')</a>
                            </li>
                        </ul>
                    </li>

                </ul>
                <div class="d-flex align-items-lg-center ms-xl-2 ms-auto me-xl-0 me-1 me-md-2">
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
