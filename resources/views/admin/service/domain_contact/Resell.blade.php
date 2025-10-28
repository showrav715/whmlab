@extends('admin.layouts.app')
@section('panel')

<form action="{{ route('admin.domain.module.command') }}" method="POST">
    <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
    <input type="hidden" name="module_type" required value="4">
    @csrf
    <div class="row mb-none-30 mb-2 justify-content-center"> 
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="form-group ">
                                <label> @lang('Name')</label>
                                <input class="form-control" type="text" name="name" value="{{@$contactInfo->name}}">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="form-group ">
                                <label>@lang('Address 1')</label>
                                <input class="form-control" type="text" name="address1" value="{{@$contactInfo->address1}}">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="form-group ">
                                <label>@lang('Address 2')</label>
                                <input class="form-control" type="text" name="address2" value="{{@$contactInfo->address2}}">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="form-group ">
                                <label>@lang('City')</label>
                                <input class="form-control" type="text" name="city" value="{{@$contactInfo->city}}">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="form-group ">
                                <label>@lang('Zip')</label>
                                <input class="form-control" type="text" name="zip" value="{{@$contactInfo->zip}}">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="form-group ">
                                <label>@lang('Country')</label>
                                <select name="country" class="form-control">
                                    @foreach($countries as $key => $country)
                                        <option value="{{ $key }}" @if($key == @$contactInfo->country ) selected @endif>{{ __($country->country) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="form-group ">
                                <label>@lang('Telephone CC')</label>
                                <input class="form-control" type="text" name="telephonecc" value="{{@$contactInfo->telnocc}}">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="form-group ">
                                <label>@lang('Telephone')</label>
                                <input class="form-control" type="text" name="telephone" value="{{@$contactInfo->telno}}">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6">
                            <div class="form-group ">
                                <label>@lang('Email Address')</label>
                                <input class="form-control" type="text" name="email" value="{{@$contactInfo->emailaddr}}">
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
  