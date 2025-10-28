@extends('admin.layouts.app')
@section('panel')

    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-4 mb-30">

            <div class="card b-radius--5 overflow-hidden">
                <div class="card-body p-0">
                    <div class="d-flex p-3 bg--primary align-items-center">
                        <div class="avatar avatar--lg">
                            <img src="{{ getImage(getFilePath('adminProfile').'/'. $admin->image,getFileSize('adminProfile'))}}" alt="Image">
                        </div>
                        <div class="ps-3">
                            <h4 class="text--white">{{__($admin->name)}}</h4>
                        </div>
                    </div>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Name')
                            <span class="fw-bold">{{__($admin->name)}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span  class="fw-bold">{{__($admin->username)}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Email')
                            <span  class="fw-bold">{{$admin->email}}</span>
                        </li>

                        @if(isSuperAdmin())
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Mobile')
                                <span  class="font-weight-bold">{{$admin->mobile}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Address')
                                <span class="font-weight-bold">{{@$admin->address->address}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('State')
                                <span class="font-weight-bold">{{@$admin->address->state}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Zip')
                                <span class="font-weight-bold">{{@$admin->address->zip}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Country')
                                <span class="font-weight-bold">{{@$admin->address->country}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('City')
                                <span class="font-weight-bold">{{@$admin->address->city}}</span>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-8 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4 border-bottom pb-2">@lang('Profile Information')</h5>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xxl-4 col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader image="{{ $admin->image }}" class="w-100" type="adminProfile" :required=false />
                                </div>
                            </div>
                            <div class="col-xxl-8 col-lg-6">
                                <div class="form-group ">
                                    <label>@lang('Name')</label>
                                    <input class="form-control" type="text" name="name" value="{{ $admin->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" type="email" name="email" value="{{ $admin->email }}" required>
                                </div>

                                @if(isSuperAdmin())
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Country')</label>
                                        <select name="country" class="form-control select2" required>
                                            @foreach($countries as $key => $country)
                                                <option value="{{ $key }}" @if($key == $admin->address->country ) selected @endif>{{ __($country->country) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">
                                            @lang('Mobile') <span class="text--primary">(@lang('Phone number in the format +NNN.NNNNNNNNNN'))</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">+</span>
                                            <input class="form-control" type="text" name="mobile" 
                                                value="{{ substr($admin->mobile, 1) }}" required pattern="\d{3}\.\d+" placeholder="+NNN.NNNNNNNNNN"
                                            >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Address')</label>
                                        <input class="form-control" type="text" name="address" value="{{ @$admin->address->address }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('State')</label>
                                        <input class="form-control" type="text" name="state" value="{{ @$admin->address->state }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('Zip')</label>
                                        <input class="form-control" type="text" name="zip" value="{{ @$admin->address->zip }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label  font-weight-bold">@lang('City')</label>
                                        <input class="form-control" type="text" name="city" value="{{ @$admin->address->city }}">
                                    </div>
                                @endif

                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{route('admin.password')}}" class="btn btn-sm btn-outline--primary"><i class="las la-key"></i>@lang('Password Setting')</a>
@endpush
@push('style')
    <style>
        .list-group-item:first-child{
            border-top-left-radius:unset;
            border-top-right-radius:unset;
        }
    </style>
@endpush
