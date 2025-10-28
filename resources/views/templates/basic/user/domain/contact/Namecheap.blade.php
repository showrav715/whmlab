@extends($activeTemplate.'layouts.master')
@section('content')

@php
    $register = @$contactInfo['CommandResponse']['DomainContactsResult']['Registrant'];
    $tech = @$contactInfo['CommandResponse']['DomainContactsResult']['Tech'];
    $admin = @$contactInfo['CommandResponse']['DomainContactsResult']['Admin'];
    $auxBilling = @$contactInfo['CommandResponse']['DomainContactsResult']['AuxBilling'];
@endphp

<div class="pt-60 pb-60 bg--light">
    <div class="container">
        <div class="row gy-4 justify-content-center">
            <div class="col-md-10">
                <div class="card w-100">
                    <form action="{{ route('user.domain.contact.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
                        <div class="card-header">
                            <h5>@lang('Contact Information')</h5>
                            @lang('It is important to keep your domain WHOIS contact information up-to-date at all times to avoid losing control of your domain')
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-4" id="tab" role="tablist">
                                <li class="nav-item">
                                <a class="nav-link active" id="register-tab" data-bs-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="true">
                                    @lang('Register')
                                </a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" id="tech-tab" data-bs-toggle="tab" href="#tech" role="tab" aria-controls="tech" aria-selected="false">
                                    @lang('Teach')
                                </a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" id="admin-tab" data-bs-toggle="tab" href="#admin" role="tab" aria-controls="admin" aria-selected="false">
                                    @lang('Admin')
                                </a>
                                </li>
                                <li class="nav-item">
                                <a class="nav-link" id="aux-tab" data-bs-toggle="tab" href="#aux" role="tab" aria-controls="aux" aria-selected="false">
                                    @lang('AuxBilling')
                                </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="tabContent">
                                    <div class="tab-pane fade show active" id="register" role="tabpanel" aria-labelledby="register-tab">
                                        <div class="row gy-3">
                                            <div class="col-md-6 form-group">
                                                <label>@lang('First Name')</label>
                                                <input class="form-control" type="text" name="RegisterFirstName" value="{{@$register['FirstName']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Last Name')</label>
                                                <input class="form-control" type="text" name="RegisterLastName" value="{{@$register['LastName']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Address')</label>
                                                <input class="form-control" type="text" name="RegisterAddress1" value="{{@$register['Address1']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('City')</label>
                                                <input class="form-control" type="text" name="RegisterCity" value="{{@$register['City']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('State')</label>
                                                <input class="form-control" type="text" name="RegisterStateProvince" value="{{@$register['StateProvince']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Postal Code')</label>
                                                <input class="form-control" type="text" name="RegisterPostalCode" value="{{@$register['PostalCode']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Phone')</label>
                                                <div class="w-100">
                                                    <input class="form-control" type="text" name="RegisterPhone" value="{{@$register['Phone']}}">
                                                    <span class="text-primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Email Address')</label>
                                                <input class="form-control" type="text" name="RegisterEmailAddress" value="{{@$register['EmailAddress']}}">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label>@lang('Country')</label> 
                                                <select name="RegisterCountry" class="form-control">
                                                    @foreach($countries as $key => $country)
                                                        <option value="{{ $key }}" @if($key == @$register['Country']) selected @endif>{{ __($country->country) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tech" role="tabpanel" aria-labelledby="tech-tab">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>@lang('First Name')</label>
                                                <input class="form-control" type="text" name="TechFirstName" value="{{@$tech['FirstName']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Last Name')</label>
                                                <input class="form-control" type="text" name="TechLastName" value="{{@$tech['LastName']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Address')</label>
                                                <input class="form-control" type="text" name="TechAddress1" value="{{@$tech['Address1']}}">
                                            </div> 
                                            <div class="col-md-6 form-group">
                                                <label>@lang('City')</label>
                                                <input class="form-control" type="text" name="TechCity" value="{{@$tech['City']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('State')</label>
                                                <input class="form-control" type="text" name="TechStateProvince" value="{{@$tech['StateProvince']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Postal Code')</label>
                                                <input class="form-control" type="text" name="TechPostalCode" value="{{@$tech['PostalCode']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Phone')</label>
                                                <div class="w-100">
                                                    <input class="form-control" type="text" name="TechPhone" value="{{@$tech['Phone']}}">
                                                    <span class="text-primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Email Address')</label>
                                                <input class="form-control" type="text" name="TechEmailAddress" value="{{@$tech['EmailAddress']}}">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label>@lang('Country')</label> 
                                                <select name="TechCountry" class="form-control">
                                                    @foreach($countries as $key => $country)
                                                        <option value="{{ $key }}" @if($key == @$tech['Country']) selected @endif>{{ __($country->country) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>@lang('First Name')</label>
                                                <input class="form-control" type="text" name="AdminFirstName" value="{{@$admin['FirstName']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Last Name')</label>
                                                <input class="form-control" type="text" name="AdminLastName" value="{{@$admin['LastName']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Address')</label>
                                                <input class="form-control" type="text" name="AdminAddress1" value="{{@$admin['Address1']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('City')</label>
                                                <input class="form-control" type="text" name="AdminCity" value="{{@$admin['City']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('State')</label>
                                                <input class="form-control" type="text" name="AdminStateProvince" value="{{@$admin['StateProvince']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Postal Code')</label>
                                                <input class="form-control" type="text" name="AdminPostalCode" value="{{@$admin['PostalCode']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Phone')</label>
                                                <div class="w-100">
                                                    <input class="form-control" type="text" name="AdminPhone" value="{{@$admin['Phone']}}">
                                                    <span class="text-primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Email Address')</label>
                                                <input class="form-control" type="text" name="AdminEmailAddress" value="{{@$admin['EmailAddress']}}">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label>@lang('Country')</label> 
                                                <select name="AdminCountry" class="form-control">
                                                    @foreach($countries as $key => $country)
                                                        <option value="{{ $key }}" @if($key == @$admin['Country']) selected @endif>{{ __($country->country) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="aux" role="tabpanel" aria-labelledby="aux-tab">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>@lang('First Name')</label>
                                                <input class="form-control" type="text" name="AuxBillingFirstName" value="{{@$auxBilling['FirstName']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Last Name')</label>
                                                <input class="form-control" type="text" name="AuxBillingLastName" value="{{@$auxBilling['LastName']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Address')</label>
                                                <input class="form-control" type="text" name="AuxBillingAddress1" value="{{@$auxBilling['Address1']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('City')</label>
                                                <input class="form-control" type="text" name="AuxBillingCity" value="{{@$auxBilling['City']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('State')</label>
                                                <input class="form-control" type="text" name="AuxBillingStateProvince" value="{{@$auxBilling['StateProvince']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Postal Code')</label>
                                                <input class="form-control" type="text" name="AuxBillingPostalCode" value="{{@$auxBilling['PostalCode']}}">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Phone')</label>
                                                <div class="w-100">
                                                    <input class="form-control" type="text" name="AuxBillingPhone" value="{{@$auxBilling['Phone']}}">
                                                    <span class="text-primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>@lang('Email Address')</label>
                                                <input class="form-control" type="text" name="AuxBillingEmailAddress" value="{{@$auxBilling['EmailAddress']}}">
                                            </div>
                                            <div class="col-md-12 form-group">
                                                <label>@lang('Country')</label> 
                                                <select name="AuxBillingCountry" class="form-control">
                                                    @foreach($countries as $key => $country)
                                                        <option value="{{ $key }}" @if($key == @$auxBilling['Country']) selected @endif>{{ __($country->country) }}</option>
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

<div id="nameserverModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Change Nameservers')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.domain.nameserver.update') }}" method="post">
                @csrf
                <input type="hidden" name="domain_id" required value="{{ $domain->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                @lang('You can change where your domain points to here. Please be aware changes can take up to 24 hours to propagate')
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns1">@lang('Nameserver 1')</label>
                            <input type="text" class="form-control" name="ns1" id="ns1" required placeholder="@lang('ns1.example.com')" value="{{ $domain->ns1 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns2">@lang('Nameserver 2')</label>
                            <input type="text" class="form-control" name="ns2" id="ns2" required placeholder="@lang('ns2.example.com')" value="{{ $domain->ns2 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns3">@lang('Nameserver 3')</label>
                            <input type="text" class="form-control" name="ns3" id="ns3" placeholder="@lang('ns3.example.com')" value="{{ $domain->ns3 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns4">@lang('Nameserver 4')</label>
                            <input type="text" class="form-control" name="ns4" id="ns4" placeholder="@lang('ns4.example.com')" value="{{ $domain->ns4 }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">@lang('Submit')</button>
                </div>
            </form>
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
