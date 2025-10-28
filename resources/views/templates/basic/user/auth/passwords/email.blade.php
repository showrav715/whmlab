@extends($activeTemplate.'layouts.auth')

@section('auth')
<form method="POST" action="{{ route('user.password.email') }}" class="account-form verify-gcaptcha">
    @csrf
    <div class="mb-4">
        <h4 class="mb-2">@lang('Account Recovery')</h4>
        <p>@lang('Enter your email and we’ll help you create a new password')</p>
    </div> 

    <div class="form-group mb-3">
        <label class="form-label">@lang('Email or Username') <span class="text--danger">*</span></label>
        <input type="text" class="form-control form--control h-45" name="value" value="{{ old('value') }}" required autofocus="off">
    </div>

    <x-captcha />

    <div class="form-group mt-3">
        <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
    </div>

    <div class="col-12 mt-3"> 
        <p class="text-center">
            @lang('Remember your password?') 
            <a href="{{ route('user.login') }}" class="fw-bold text--base">@lang('Login Here')</a>
        </p>
    </div>
</form>  
@endsection
