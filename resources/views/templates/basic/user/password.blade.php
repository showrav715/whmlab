@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light">
    <div class="container"> 
        <div class="row justify-content-center"> 
            <div class="col-lg-8">
                <div class="card custom--card style-two">
                    <div class="card-header">
                        <h6 class="card-title text-center">{{ __($pageTitle) }}</h6>
                    </div>
                    <div class="card-body"> 
                        <form method="POST" action="">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-sm-12"> 
                                    <div class="form-group"> 
                                        <label>@lang('Current Password') <span class="text--danger">*</span></label>
                                        <input type="password" class="form-control form--control h-45" name="current_password" required autocomplete="current-password">
                                    </div>
                                </div>
                                <div class="col-sm-12"> 
                                    <div class="form-group"> 
                                        <label>@lang('Current Password') <span class="text--danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control form--control h-45 @if(gs('secure_password')) secure-password @endif" name="password" required autocomplete="current-password">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12"> 
                                    <div class="form-group"> 
                                        <label>@lang('Confirm Password') <span class="text--danger">*</span></label>
                                        <input type="password" class="form-control form--control h-45" name="password_confirmation" required autocomplete="current-password">
                                    </div>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                            </div>
                        </form> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if(gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif