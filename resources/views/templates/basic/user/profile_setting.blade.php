@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="pt-60 pb-60 bg--light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom--card style-two">
                        <div class="card-header">
                            <h6 class="card-title text-center mb-0">{{ __($pageTitle) }}</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                @csrf
                                <div class="row gy-4">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('First Name') <span class="text--danger">*</span></label>
                                            <input type="text" class="form-control form--control h-45" name="firstname" value="{{ $user->firstname }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('Last Name') <span class="text--danger">*</span></label>
                                            <input type="text" class="form-control form--control h-45" name="lastname" value="{{ $user->lastname }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('E-mail Address') <span class="text--danger">*</span></label>
                                            <input class="form-control form--control h-45" value="{{ $user->email }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('Mobile Number') <span class="text--danger">*</span></label>
                                            <input class="form-control form--control h-45" value="{{ $user->mobile }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Address') <span class="text--danger">*</span></label>
                                            <input type="text" class="form-control form--control h-45" name="address" value="{{ @$user->address }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('State') <span class="text--danger">*</span></label>
                                            <input type="text" class="form-control form--control h-45" name="state" value="{{ @$user->state }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('Zip Code') <span class="text--danger">*</span></label>
                                            <input type="text" class="form-control form--control h-45" name="zip" value="{{ @$user->zip }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('City') <span class="text--danger">*</span></label>
                                            <input type="text" class="form-control form--control h-45" name="city" value="{{ @$user->city }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('Country') <span class="text--danger">*</span></label>
                                            <input class="form-control form--control h-45" value="{{ @$user->country_name }}" disabled>
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
