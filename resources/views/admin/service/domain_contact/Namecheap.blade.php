@extends('admin.layouts.app')
@section('panel')

@php
    $register = @$contactInfo['CommandResponse']['DomainContactsResult']['Registrant'];
    $tech = @$contactInfo['CommandResponse']['DomainContactsResult']['Tech'];
    $admin = @$contactInfo['CommandResponse']['DomainContactsResult']['Admin'];
    $auxBilling = @$contactInfo['CommandResponse']['DomainContactsResult']['AuxBilling'];
@endphp

<form action="{{ route('admin.domain.module.command') }}" method="POST">
    <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
    <input type="hidden" name="module_type" required value="4">
    @csrf
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <nav class="mb-4">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="true">
                                @lang('Register')
                            </button>
                            <button class="nav-link" id="tech-tab" data-bs-toggle="tab" data-bs-target="#tech" type="button" role="tab" aria-controls="tech" aria-selected="false">
                                @lang('Tech')
                            </button>
                            <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab" aria-controls="admin" aria-selected="false">
                                @lang('Admin')
                            </button>
                            <button class="nav-link" id="aux-tab" data-bs-toggle="tab" data-bs-target="#aux" type="button" role="tab" aria-controls="aux" aria-selected="false">
                                @lang('AuxBilling')
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="register" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="row">
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('First Name')</label>
                                        <input class="form-control" type="text" name="RegisterFirstName" value="{{@$register['FirstName']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Last Name')</label>
                                        <input class="form-control" type="text" name="RegisterLastName" value="{{@$register['LastName']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Address')</label>
                                        <input class="form-control" type="text" name="RegisterAddress1" value="{{@$register['Address1']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('City')</label>
                                        <input class="form-control" type="text" name="RegisterCity" value="{{@$register['City']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('State')</label>
                                        <input class="form-control" type="text" name="RegisterStateProvince" value="{{@$register['StateProvince']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Postal Code')</label>
                                        <input class="form-control" type="text" name="RegisterPostalCode" value="{{@$register['PostalCode']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Country')</label>
                                        <select name="RegisterCountry" class="form-control">
                                            @foreach($countries as $key => $country)
                                                <option value="{{ $key }}" @if($key == @$register['Country'] ) selected @endif>{{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label> @lang('Email Address')</label>
                                        <input class="form-control" type="email" name="RegisterEmailAddress" value="{{@$register['EmailAddress']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group ">
                                        <div class="justify-content-between d-flex flex-wrap">
                                            <label> @lang('Phone')</label>
                                            <label class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</label>
                                        </div>
                                        <input class="form-control" type="text" name="RegisterPhone" value="{{@$register['Phone']}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tech" role="tabpanel" aria-labelledby="teach-tab">
                            <div class="row">
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('First Name')</label>
                                        <input class="form-control" type="text" name="TechFirstName" value="{{@$tech['FirstName']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Last Name')</label>
                                        <input class="form-control" type="text" name="TechLastName" value="{{@$tech['LastName']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Address')</label>
                                        <input class="form-control" type="text" name="TechAddress1" value="{{@$tech['Address1']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('City')</label>
                                        <input class="form-control" type="text" name="TechCity" value="{{@$tech['City']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('State')</label>
                                        <input class="form-control" type="text" name="TechStateProvince" value="{{@$tech['StateProvince']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Postal Code')</label>
                                        <input class="form-control" type="text" name="TechPostalCode" value="{{@$tech['PostalCode']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Country')</label>
                                        <select name="TechCountry" class="form-control">
                                            @foreach($countries as $key => $country)
                                                <option value="{{ $key }}" @if($key == @$tech['Country'] ) selected @endif>{{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label> @lang('Email Address')</label>
                                        <input class="form-control" type="email" name="TechEmailAddress" value="{{@$tech['EmailAddress']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group ">
                                        <div class="justify-content-between d-flex flex-wrap">
                                            <label> @lang('Phone')</label>
                                            <label class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</label>
                                        </div>
                                        <input class="form-control" type="text" name="TechPhone" value="{{@$tech['Phone']}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                            <div class="row">
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('First Name')</label>
                                        <input class="form-control" type="text" name="AdminFirstName" value="{{@$admin['FirstName']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Last Name')</label>
                                        <input class="form-control" type="text" name="AdminLastName" value="{{@$admin['LastName']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Address')</label>
                                        <input class="form-control" type="text" name="AdminAddress1" value="{{@$admin['Address1']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('City')</label>
                                        <input class="form-control" type="text" name="AdminCity" value="{{@$admin['City']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('State')</label>
                                        <input class="form-control" type="text" name="AdminStateProvince" value="{{@$admin['StateProvince']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Postal Code')</label>
                                        <input class="form-control" type="text" name="AdminPostalCode" value="{{@$admin['PostalCode']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Country')</label>
                                        <select name="AdminCountry" class="form-control">
                                            @foreach($countries as $key => $country)
                                                <option value="{{ $key }}" @if($key == @$admin['Country'] ) selected @endif>{{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label> @lang('Email Address')</label>
                                        <input class="form-control" type="email" name="AdminEmailAddress" value="{{@$admin['EmailAddress']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group ">
                                        <div class="justify-content-between d-flex flex-wrap">
                                            <label> @lang('Phone')</label>
                                            <label class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</label>
                                        </div>
                                        <input class="form-control" type="text" name="AdminPhone" value="{{@$admin['Phone']}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="aux" role="tabpanel" aria-labelledby="aux-tab">
                            <div class="row">
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('First Name')</label>
                                        <input class="form-control" type="text" name="AuxBillingFirstName" value="{{@$auxBilling['FirstName']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Last Name')</label>
                                        <input class="form-control" type="text" name="AuxBillingLastName" value="{{@$auxBilling['LastName']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Address')</label>
                                        <input class="form-control" type="text" name="AuxBillingAddress1" value="{{@$auxBilling['Address1']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('City')</label>
                                        <input class="form-control" type="text" name="AuxBillingCity" value="{{@$auxBilling['City']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('State')</label>
                                        <input class="form-control" type="text" name="AuxBillingStateProvince" value="{{@$auxBilling['StateProvince']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Postal Code')</label>
                                        <input class="form-control" type="text" name="AuxBillingPostalCode" value="{{@$auxBilling['PostalCode']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group ">
                                        <label> @lang('Country')</label>
                                        <select name="AuxBillingCountry" class="form-control">
                                            @foreach($countries as $key => $country)
                                                <option value="{{ $key }}" @if($key == @$auxBilling['Country'] ) selected @endif>{{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-6 col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label> @lang('Email Address')</label>
                                        <input class="form-control" type="email" name="AuxBillingEmailAddress" value="{{@$auxBilling['EmailAddress']}}">
                                    </div>
                                </div>
                                <div class="col-xxl-4 col-lg-12 col-md-12 col-sm-12">
                                    <div class="form-group ">
                                        <div class="justify-content-between d-flex flex-wrap">
                                            <label> @lang('Phone')</label>
                                            <label class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</label>
                                        </div>
                                        <input class="form-control" type="text" name="AuxBillingPhone" value="{{@$auxBilling['Phone']}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @permit('admin.domain.module.command')
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>
                    @endpermit
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@permit('admin.order.domain.details')
    @push('breadcrumb-plugins') 
        <a href="{{ route('admin.order.domain.details', $domain->id) }}" class="btn btn-sm btn-outline--primary">
            <i class="la la-undo"></i> @lang('Go to Details')
        </a>
    @endpush 
@endpermit
 
@push('script')
    <script>
        (function($){
            "use strict";  

            var tab = sessionStorage.getItem('tab');

            if(tab){
                $('.nav-link').removeClass('active');
                $('.tab-pane').removeClass('show active');
                $('#'+tab).addClass('active');
                $('#'+tab.split('-')[0]).addClass('show active');

                sessionStorage.removeItem('tab');
            }

            $('.nav-link').on('click', function(){
                var id = $(this).attr('id');
                sessionStorage.setItem('tab', id);
            });

        })(jQuery);
    </script>
@endpush