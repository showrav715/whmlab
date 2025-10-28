@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.users.add.new')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group ">
                                <label>@lang('First Name')</label>
                                <input class="form-control" type="text" name="firstname" required value="{{ old('firstname') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">@lang('Last Name')</label>
                                <input class="form-control" type="text" name="lastname" required value="{{ old('lastname') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>@lang('Username') </label>
                                <input class="form-control" type="text" name="username" value="{{ old('username') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>@lang('Email') </label>
                                <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="form-group ">
                                <label>@lang('Country')</label>
                                <select name="country" class="form-control" required>
                                    @foreach($countries as $key => $country)
                                        <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}">{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="form-group">
                                <label>@lang('Mobile Number') </label>
                                <div class="input-group ">
                                    <span class="input-group-text mobile-code"></span>
                                    <input type="number" name="mobile" value="{{ old('mobile') }}" id="mobile" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 ">
                            <div class="form-group ">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <label>@lang('Password')</label>
                                    <a href="javascript:void(0)" class="generatePassword">@lang('Generate Strong Password')</a>
                                </div>                            
                                <input class="form-control" type="text" name="password" value="{{@$hosting->password}}" id="password" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group ">
                                <label>@lang('Address')</label>
                                <input class="form-control" type="text" name="address" value="{{ old('address') }}">
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group">
                                <label>@lang('City')</label>
                                <input class="form-control" type="text" name="city" value="{{ old('city') }}">
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group ">
                                <label>@lang('State')</label>
                                <input class="form-control" type="text" name="state" value="{{ old('state') }}">
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="form-group ">
                                <label>@lang('Zip/Postal')</label>
                                <input class="form-control" type="text" name="zip" value="{{ old('zip') }}">
                            </div>
                        </div>
                        <div class="form-group  col-xl-3 col-md-6 col-12">
                            <label>@lang('Email Verification')</label>
                            <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="ev" @if(old('ev')) checked @endif>
                        </div>
                        <div class="form-group  col-xl-3 col-md-6 col-12">
                            <label>@lang('Mobile Verification')</label>
                            <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                    data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="sv" @if(old('sv')) checked @endif>
                        </div>
                        <div class="form-group col-xl-3 col-md- col-12">
                            <label>@lang('2FA Verification') </label>
                            <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="ts" @if(old('ts')) checked @endif>
                        </div> 
                        <div class="form-group col-xl-3 col-md- col-12">
                            <label>@lang('KYC') </label>
                            <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="kv" @if(old('kv')) checked @endif>
                        </div>
                        @permit('admin.users.add.new')
                            <div class="col-md-12 mt-2">
                                <div class="form-group">
                                    <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                                </div>
                            </div>
                        @endpermit
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($){
    "use strict"

        @if(old('country'))
            $('select[name=country]').val("{{ old('country') }}");
        @endif

        let mobileElement = $('.mobile-code');
        $('select[name=country]').change(function(){
            mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
        });

        let dialCode        = $('select[name=country] :selected').data('mobile_code');
        mobileElement.text(`+${dialCode}`);

        $('.generatePassword').on('click', function(){
            var password = generatePassword(15);
            $('#password').val(password);
        });

        function generatePassword(passwordLength) {
            var numberChars = "0123456789";
            var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            var lowerChars = "abcdefghijklmnopqrstuvwxyz";
            var specialChars = "!#$%&()*+,-./:;<=>?@[\]^_`{|}~";
            var allChars = numberChars + upperChars + lowerChars + specialChars;
            var randPasswordArray = Array(passwordLength);

            randPasswordArray[0] = numberChars;
            randPasswordArray[1] = upperChars;
            randPasswordArray[2] = lowerChars;
            randPasswordArray[3] = specialChars;
            randPasswordArray = randPasswordArray.fill(allChars, 4);

            return shuffleArray(randPasswordArray.map(function(x) { return x[Math.floor(Math.random() * x.length)] })).join('');
        }

        function shuffleArray(array) {
            for (var i = array.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
            return array;
        }

    })(jQuery);
</script>
@endpush
