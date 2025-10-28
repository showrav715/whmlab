@extends($activeTemplate . 'layouts.auth')

@section('auth')
    <form action="{{ route('user.login') }}" class="account-form verify-gcaptcha" method="POST">
        @csrf

        @include($activeTemplate.'partials.social_login')

        <div class="mb-4 text-center">
            <h4 class="mb-2">@lang('Login to your account')</h4>
            <p>@lang('You can sign in to your account using email or username')</p>
        </div>
        <div class="row gy-2 gap-2">
            <div class="col-12">
                <div class="form-group">
                    <label>@lang('Username or Email') <span class="text--danger">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" required class="form-control form--control h-45">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>@lang('Password') <span class="text--danger">*</span></label>
                    <input type="password" name="password" class="form-control form--control h-45" required>
                </div>
            </div>
            <div class="col-12">
                <div class="d-flex flex-wrap gap-2 justify-content-between">
                    <div class="form-group custom--checkbox">
                        <input type="checkbox" id="remember" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">@lang('Remember Me')</label>
                    </div>
                    <a href="{{ route('user.password.request') }}" class="text--base fw-bold">@lang('Forgot Password?')</a>
                </div>
            </div>

            <x-captcha />

            <div class="col-12">
                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
            </div>
            <div class="col-12">
                <p class="text-center">
                    @lang('Don\'t have any account?')
                    <a href="{{ route('user.register') }}" class="fw-bold text--base">@lang('Registration Here')</a>
                </p>
            </div>
        </div>
    </form>
@endsection
