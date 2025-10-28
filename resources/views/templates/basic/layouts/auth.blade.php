@extends($activeTemplate . 'layouts.app')

@section('app')
    <section class="account-section position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="@if (request()->routeIs('user.register')) col-xl-6 col-lg-7 col-md-10 @else col-xxl-5 col-lg-6 col-md-8 @endif">
                    <a href="{{ route('home') }}" class="text-center d-block my-3 mb-sm-4 logo">
                        <img src="{{ siteLogo('dark') }}" alt="@lang('logo')">
                    </a>
                    @yield('auth')
                </div>
            </div>
        </div>
    </section>
@endsection
