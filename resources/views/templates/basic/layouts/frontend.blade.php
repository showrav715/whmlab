@extends($activeTemplate.'layouts.app')

@section('app')

    @stack('fbComment')
    
    @include($activeTemplate.'partials.header')

    @include($activeTemplate.'partials.breadcrumb')

    @yield('content')

    @include($activeTemplate.'partials.footer')

    @include($activeTemplate.'partials.subscribe')
    
    <x-cookie-policy />
@endsection 
 