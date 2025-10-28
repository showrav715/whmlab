@php
    $footer = @getContent('footer.content', true);
    $policyPages = @getContent('policy_pages.element', orderById:true);
@endphp 

<!-- Footer Section -->
<footer class="py-4 footer bg-dark-two">
    <div class="container">
        <div class="footer-content text-center">
            <a href="{{ route('home') }}" class="logo mb-3">
                <img src="{{ siteLogo() }}" alt="@lang('logo')">
            </a>
            <p class="footer-text mx-auto">
                {{ __(@$footer->data_values->description) }}
            </p>
            <ul class="footer-links d-flex flex-wrap gap-3 justify-content-center mt-3 mb-3">
                <li><a href="{{ route('home') }}" class="anchor-decoration text--base">@lang('Home')</a></li>

                <li><a href="{{ route('blogs') }}" class="anchor-decoration text--base">@lang('Announcements')</a></li>
                @foreach($policyPages as $policyPage)
                    <li>
                        <a href="{{ route('policy.pages', ['slug'=>slug($policyPage->data_values->title)]) }}" 
                            class="anchor-decoration text--base"
                        >
                            {{ __(@$policyPage->data_values->title) }}
                        </a>
                    </li>
                @endforeach
                <li><a href="{{ route('contact') }}" class="anchor-decoration text--base">@lang('Contact')</a></li>

                <li><a href="{{ route('user.login') }}" class="anchor-decoration text--base">@lang('Login')</a></li>
                <li><a href="{{ route('user.register') }}" class="anchor-decoration text--base">@lang('Register')</a></li>
            </ul>
            <p>{{ gs('site_name') }} &copy; {{ date('Y') }}. @lang('All Rights Reserved')</p>
        </div>
    </div>
</footer>
