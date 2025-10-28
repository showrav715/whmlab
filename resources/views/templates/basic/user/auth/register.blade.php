@extends($activeTemplate . 'layouts.auth')

@php
    $policyPages = getContent('policy_pages.element', false, orderById: true);
@endphp

@section('auth')


    <form action="{{ route('user.register') }}"
        class="account-form verify-gcaptcha @if (!gs('registration')) form-disabled @endif" method="POST">
        @csrf

        @if (!gs('registration'))
            <p class="form-disabled-text">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="80"
                    height="80" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512"
                    xml:space="preserve">
                    <g>
                        <path
                            d="M255.999 0c-79.044 0-143.352 64.308-143.352 143.353v70.193c0 4.78 3.879 8.656 8.659 8.656h48.057a8.657 8.657 0 0 0 8.656-8.656v-70.193c0-42.998 34.981-77.98 77.979-77.98s77.979 34.982 77.979 77.98v70.193c0 4.78 3.88 8.656 8.661 8.656h48.057a8.657 8.657 0 0 0 8.656-8.656v-70.193C399.352 64.308 335.044 0 255.999 0zM382.04 204.89h-30.748v-61.537c0-52.544-42.748-95.292-95.291-95.292s-95.291 42.748-95.291 95.292v61.537h-30.748v-61.537c0-69.499 56.54-126.04 126.038-126.04 69.499 0 126.04 56.541 126.04 126.04v61.537z"
                            fill="hsl(var(--danger))" opacity="1" data-original="hsl(var(--danger))"></path>
                        <path
                            d="M410.63 204.89H101.371c-20.505 0-37.188 16.683-37.188 37.188v232.734c0 20.505 16.683 37.188 37.188 37.188H410.63c20.505 0 37.187-16.683 37.187-37.189V242.078c0-20.505-16.682-37.188-37.187-37.188zm19.875 269.921c0 10.96-8.916 19.876-19.875 19.876H101.371c-10.96 0-19.876-8.916-19.876-19.876V242.078c0-10.96 8.916-19.876 19.876-19.876H410.63c10.959 0 19.875 8.916 19.875 19.876v232.733z"
                            fill="hsl(var(--danger))" opacity="1" data-original="hsl(var(--danger))"></path>
                        <path
                            d="M285.11 369.781c10.113-8.521 15.998-20.978 15.998-34.365 0-24.873-20.236-45.109-45.109-45.109-24.874 0-45.11 20.236-45.11 45.109 0 13.387 5.885 25.844 16 34.367l-9.731 46.362a8.66 8.66 0 0 0 8.472 10.436h60.738a8.654 8.654 0 0 0 8.47-10.434l-9.728-46.366zm-14.259-10.961a8.658 8.658 0 0 0-3.824 9.081l8.68 41.366h-39.415l8.682-41.363a8.655 8.655 0 0 0-3.824-9.081c-8.108-5.16-12.948-13.911-12.948-23.406 0-15.327 12.469-27.796 27.797-27.796 15.327 0 27.796 12.469 27.796 27.796.002 9.497-4.838 18.246-12.944 23.403z"
                            fill="hsl(var(--danger))" opacity="1" data-original="hsl(var(--danger))"></path>
                    </g>
                </svg>

                <span>@lang('Registration is currently disabled')</span>
            </p>
        @endif

        @include($activeTemplate . 'partials.social_login')

        <div class="mb-4 text-center">
            <h4 class="mb-2">@lang('Create an Account')</h4>
            <p>@lang('You can create account using email or username and the registration is fully free')</p>
        </div>
        <div class="row gy-3">
            <div class="form-group col-sm-6">
                <label class="form-label">@lang('First Name')</label>
                <input type="text" class="form-control form--control h-45" name="firstname"
                    value="{{ old('firstname') }}" required>
            </div>
            <div class="form-group col-sm-6">
                <label class="form-label">@lang('Last Name')</label>
                <input type="text" class="form-control form--control h-45" name="lastname" value="{{ old('lastname') }}"
                    required>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">@lang('E-Mail Address')</label>
                    <input type="email" class="form-control form--control h-45 checkUser" name="email"
                        value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">@lang('Password')</label>
                    <input type="password"
                        class="form-control form--control h-45 @if (gs('secure_password')) secure-password @endif"
                        name="password" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">@lang('Confirm Password')</label>
                    <input type="password" class="form-control form--control h-45" name="password_confirmation" required>
                </div>
            </div>

            <x-captcha />

            @if (gs('agree'))
                <div class="col-12">
                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                        <div class="form-group custom--checkbox">
                            <input type="checkbox" id="agree" @checked(old('agree')) name="agree"
                                class="form-check-input" required>
                            <label for="agree">@lang('I agree with')</label>

                            @foreach ($policyPages as $policy)
                                <a href="{{ route('policy.pages', $policy->slug) }}" target="_blank">
                                    {{ __($policy->data_values->title) }}
                                </a>
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-12">
                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
            </div>
            <div class="col-12">
                <p class="text-center">
                    @lang('Have an account?') <a href="{{ route('user.login') }}" class="fw-bold text--base">@lang('Login Here')</a>
                </p>
            </div>
        </div>
    </form>

    <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <span class="text-center d-block">@lang('You already have an account please Login ')</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--base btn--sm">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .form-disabled {
            overflow: hidden;
            position: relative;
            user-select: none;
        }

        .form-disabled::after {
            content: "";
            position: absolute;
            height: 100%;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.2);
            top: 0;
            left: 0;
            backdrop-filter: blur(4px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            z-index: 99;
        }

        .form-disabled-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 991;
            font-size: 24px;
            height: auto;
            width: 100%;
            text-align: center;
            color: hsl(var(--dark-600));
            font-weight: 800;
            line-height: 1.2;
            user-select: auto;
        }

        @media(max-width: 767px){
            .form-disabled-text{
                font-size: 22px;
            }
        }

        .form-disabled-text span {
            display: block;
            margin-top: 12px;
            color: hsl(var(--danger));
        }

    </style>
@endpush


@if (gs('registration'))
    @if (gs('secure_password'))
        @push('script-lib')
            <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
        @endpush
    @endif

    @push('script')
        <script>
            "use strict";


            (function($) {
                $('.checkUser').on('focusout', function(e) {
                    var url = '{{ route('user.checkUser') }}';
                    var value = $(this).val();
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        if (response.data != false) {
                            $('#existModalCenter').modal('show');
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush
@endif
