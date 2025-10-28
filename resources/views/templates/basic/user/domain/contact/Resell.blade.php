@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light">
    <div class="container">
        <div class="row gy-4 justify-content-center"> 
            <div class="col-md-10">
                <div class="card w-100">
                    <form action="{{ route('user.domain.contact.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
                        <div class="card-header">
                            <h6>@lang('Contact Information')</h6>
                            @lang('It is important to keep your domain WHOIS contact information up-to-date at all times to avoid losing control of your domain')
                        </div> 
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-4" id="tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="register-tab" data-bs-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="true">
                                        @lang('Register')
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="tabContent">
                                <div class="tab-pane fade show active" id="register" role="tabpanel" aria-labelledby="register-tab">
                                    <div class="row gy-3">
                                        <div class="col-md-6 form-group">
                                            <label>@lang('Name')</label>
                                            <input class="form-control form--control h-45" type="text" name="name" value="{{@$contactInfo->name}}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>@lang('Address 1')</label>
                                            <input class="form-control form--control h-45" type="text" name="address1" value="{{@$contactInfo->address1}}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>@lang('Address 2')</label>
                                            <input class="form-control form--control h-45" type="text" name="address2" value="{{@$contactInfo->address2}}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>@lang('City')</label>
                                            <input class="form-control form--control h-45" type="text" name="city" value="{{@$contactInfo->city}}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>@lang('Zip')</label>
                                            <input class="form-control form--control h-45" type="text" name="zip" value="{{@$contactInfo->zip}}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>@lang('Telephone CC')</label>
                                            <input class="form-control form--control h-45" type="text" name="telephonecc" value="{{@$contactInfo->telnocc}}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>@lang('Telephone')</label>
                                            <input class="form-control form--control h-45" type="text" name="telephone" value="{{@$contactInfo->telno}}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>@lang('Email Address')</label>
                                            <input class="form-control form--control h-45" type="email" name="email" value="{{@$contactInfo->emailaddr}}">
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label>@lang('Country')</label> 
                                            <select name="country" class="form-control form--control h-45">
                                                @foreach($countries as $key => $country)
                                                    <option value="{{ $key }}" @if($key == @$contactInfo->country) selected @endif>{{ __($country->country) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--base w-100 mt-4 btn-block">@lang('Submit')</button>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .new-card {
        margin: 0;
        background-color: #efefef;
        border-radius: 10px;
        padding: 30px;
        line-height: 1em;
    }
    .fa-stack {
        display: inline-block;
        height: 2em;
        line-height: 2em;
        position: relative;
        vertical-align: middle;
        width: 2.5em;
        font-size: 50px;
        width: 100%;
        justify-content: center;
    }
</style>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            $('.nameserverModal').on('click', function() {
                var modal = $('#nameserverModal');
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
