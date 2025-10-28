@extends($activeTemplate.'layouts.auth')

@section('auth')
<form action="{{ route('user.password.update') }}" class="account-form verify-gcaptcha" method="POST">
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-4">
        <h4 class="mb-2">@lang('Reset Password')</h4>
        <p>
            @lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone')
        </p> 
    </div>
    <div class="row gy-2 gap-2"> 
        <div class="col-12">
            <div class="form-group">
                <label>@lang('Password') <span class="text--danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control form--control h-45 @if(gs('secure_password')) secure-password @endif" name="password" required>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="form-group"> 
                <label>@lang('Confirm Password') <span class="text--danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control form--control h-45" required>   
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
        </div>
        <div class="col-12">
            <p class="text-center">
                @lang('Remember your password?') 
                <a href="{{ route('user.login') }}" class="fw-bold text--base">@lang('Login Here')</a>
            </p>
        </div>
    </div>
</form>
@endsection

@if(gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
