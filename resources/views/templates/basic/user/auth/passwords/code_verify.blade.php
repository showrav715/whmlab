@extends($activeTemplate.'layouts.frontend')

@section('content')
<div class="pt-120 pb-120 bg--light">
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="verification-code-wrapper">
                <div class="verification-area">
                    <h5 class="pb-3 text-center border-bottom">@lang('Verify Email Address')</h5>
                    <form action="{{ route('user.password.verify.code') }}" method="POST" class="submit-form">
                        @csrf
                        <p class="verification-text text--dark">
                            @lang('A 6 digit verification code sent to your email address'):  {{ showEmailAddress($email) }}
                        </p>
                        
                        <input type="hidden" name="email" value="{{ $email }}">

                        @include($activeTemplate.'partials.verification_code')
    
                        <div class="mb-3">
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
    
                        <div class="mb-2">
                            <p>
                                @lang('Please check including your Junk/Spam Folder. if not found, you can'), 
                                <a href="{{ route('user.password.request') }}" class="text--base">@lang('Try to send again')</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
