@extends('admin.layouts.app')

@push('topBar')
    <ul class="nav nav-tabs mb-4 topTap breadcrumb-nav" role="tablist">
        <button class="breadcrumb-nav-close"><i class="las la-times"></i></button>
        <li class="nav-item" role="presentation">
            <a href="javascript:void(0)" class="nav-link" id="information-tab" data-bs-toggle="pill" data-bs-target="#information" type="button"
                role="tab" aria-controls="information" aria-selected="true">@lang('Details')
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="javascript:void(0)" class="nav-link" id="edit-information-tab" data-bs-toggle="pill" data-bs-target="#edit-information"
                type="button" role="tab" aria-controls="edit-information" aria-selected="false">@lang('Information')
            </a>
        </li>
        <li class="nav-item">
            @permit('admin.report.transaction')
                <a class="nav-link" href="{{ route('admin.report.transaction') }}?search={{ $user->username }}">
                    @lang('Balance') ({{ showAmount($user->balance) }})
                </a>
            @else
                <a class="nav-link disabled" href="javascript:void(0)">
                    @lang('Balance') ({{ showAmount($user->balance) }})
                </a>
            @endpermit
        </li>
        <li class="nav-item">
            @permit('admin.deposit.list')
                <a class="nav-link" href="{{ route('admin.deposit.list') }}?search={{ $user->username }}">
                    @lang('Payments') ({{ showAmount($totalDeposit) }})
                </a>
            @else
                <a class="nav-link disabled" href="javascript:void(0)">
                    @lang('Payments') ({{ showAmount($totalDeposit) }})
                </a>
            @endpermit
        </li>
        <li class="nav-item">
            @permit('admin.report.transaction')
                <a class="nav-link" href="{{ route('admin.report.transaction') }}?search={{ $user->username }}">
                    @lang('Transactions') ({{ $totalTransaction }})
                </a>
            @else
                <a class="nav-link disabled" href="javascript:void(0)">
                    @lang('Transactions') ({{ $totalTransaction }})
                </a>
            @endpermit
        </li>
        <li class="nav-item">
            @permit('admin.users.orders')
                <a class="nav-link" href="{{ route('admin.users.orders', $user->id) }}">
            @else
                <a class="nav-link disabled" href="javascript:void(0)">
            @endpermit
                @lang('Orders') ({{ @$statistics['count_total_order'] }})
            </a>
        </li>
        <li class="nav-item">
            @permit('admin.users.invoices')
                <a class="nav-link" href="{{ route('admin.users.invoices', $user->id) }}">
            @else
                <a class="nav-link disabled" href="javascript:void(0)">
            @endpermit
                @lang('Invoices') ({{ @$statistics['count_total_invoice'] }})
            </a>
        </li>
        <li class="nav-item">
            @permit('admin.users.cancellations')
                <a class="nav-link" href="{{ route('admin.users.cancellations', $user->id) }}">
            @else
                <a class="nav-link disabled" href="javascript:void(0)">
            @endpermit
                @lang('Cancellations') ({{ @$statistics['count_total_cancellation'] }})
            </a>
        </li>
        <li class="nav-item">
            @permit('admin.users.services')
                <a class="nav-link" href="{{ route('admin.users.services', $user->id) }}">
            @else
                <a class="nav-link disabled" href="javascript:void(0)">
            @endpermit
                @lang('Services') ({{ @$statistics['count_total_service'] }})
            </a>
        </li>
        <li class="nav-item">
            @permit('admin.users.domains')
                <a class="nav-link" href="{{ route('admin.users.domains', $user->id) }}">
            @else
                <a class="nav-link disabled" href="javascript:void(0)">
            @endpermit
                @lang('Domains') ({{ @$statistics['count_total_domain'] }})
            </a>
        </li>
    </ul>
@endpush

@section('panel')
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade" id="information" role="tabpanel" aria-labelledby="information-tab"
            tabindex="0">
            <div class="row mt-30 justify-content-center">
                <div class="col-xl-6">
                    <div class="card-body p-0">
                        <div class="d-flex p-3 bg--primary align-items-center">
                            <div class="avatar avatar--lg">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="ps-3">
                                <h6 class="text--white">{{__($user->fullname)}}</h6>
                            </div>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Name')
                                <span class="fw-bold">{{__($user->fullname)}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Username')
                                <span  class="fw-bold">{{ $user->username }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Email')
                                <span  class="fw-bold">{{$user->email}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Mobile')
                                <span  class="font-weight-bold">{{$user->mobile}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Address')
                                <span class="font-weight-bold">{{@$user->address}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('State')
                                <span class="font-weight-bold">{{@$user->state}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Zip')
                                <span class="font-weight-bold">{{@$user->zip}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Country')
                                <span class="font-weight-bold">{{@$user->country_name}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('City')
                                <span class="font-weight-bold">{{@$user->city}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Email Verification')
                                @if($user->ev)
                                    <span class="badge badge--success">@lang('Verified')</span>
                                @else
                                    <span class="badge badge--warning">@lang('Unverified')</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Mobile Verification')
                                @if($user->sv)
                                    <span class="badge badge--success">@lang('Verified')</span>
                                @else
                                    <span class="badge badge--warning">@lang('Unverified')</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('2FA Verification')
                                @if($user->ts)
                                    <span class="badge badge--success">@lang('Enabled')</span>
                                @else
                                    <span class="badge badge--warning">@lang('Disabled')</span>
                                @endif
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('KYC Status')
                                @if($user->kv == 0)
                                    <span class="badge badge--warning">@lang('Unverified')</span>
                                @elseif($user->kv == 1)
                                    <span class="badge badge--success">@lang('Verified')</span>
                                @else
                                    <span class="badge badge--danger">@lang('Pending')</span>
                                @endif
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="col-xl-6 mt-4 mt-xl-0">
                    <div class="d-flex flex-wrap gap-3">

                        @permit('admin.users.add.sub.balance')
                            <div class="flex-fill">
                                <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn--success btn--shadow w-100 btn-lg bal-btn" data-act="add">
                                    <i class="las la-plus-circle"></i> @lang('Balance')
                                </button>
                            </div>
                            <div class="flex-fill">
                                <button data-bs-toggle="modal" data-bs-target="#addSubModal" class="btn btn--danger btn--shadow w-100 btn-lg bal-btn" data-act="sub">
                                    <i class="las la-minus-circle"></i> @lang('Balance')
                                </button>
                            </div>
                        @endpermit

                        <div class="flex-fill">
                            @permit('admin.report.login.history')
                                <a href="{{route('admin.report.login.history')}}?search={{ $user->username }}" class="btn btn--primary btn--shadow w-100 btn-lg">
                                    <i class="las la-list-alt"></i>@lang('Logins')
                                </a>
                            @else
                                <a href="javascript:void(0)" class="btn btn--primary btn--shadow w-100 btn-lg">
                                    <i class="las la-list-alt"></i>@lang('Logins')
                                </a>
                            @endpermit
                        </div>

                        @permit('admin.users.notification.log')
                            <div class="flex-fill">
                                <a href="{{ route('admin.users.notification.log',$user->id) }}" class="btn btn--secondary btn--shadow w-100 btn-lg">
                                    <i class="las la-bell"></i>@lang('Notifications')
                                </a>
                            </div>
                        @endpermit

                        @permit('admin.users.kyc.details')
                            @if ($user->kyc_data)
                                <div class="flex-fill">
                                    <a href="{{ route('admin.users.kyc.details', $user->id) }}" target="_blank" class="btn btn--dark btn--shadow w-100 btn-lg">
                                        <i class="las la-user-check"></i>@lang('KYC Data')
                                    </a>
                                </div>
                            @endif
                        @endpermit

                        @permit('admin.users.status')
                            <div class="flex-fill">
                                @if ($user->status == 1)
                                <button type="button" class="btn btn--warning btn--gradi btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                                    <i class="las la-ban"></i>@lang('Ban User')
                                </button>
                                @else
                                <button type="button" class="btn btn--success btn--gradi btn--shadow w-100 btn-lg userStatus" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                                    <i class="las la-undo"></i>@lang('Unban User')
                                </button>
                                @endif
                            </div>
                        @endpermit
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="edit-information" role="tabpanel" aria-labelledby="edit-information-tab" tabindex="0">
            <div class="card mt-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Edit Information of') {{$user->fullname}}</h5>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.users.update',[$user->id])}}" method="POST" enctype="multipart/form-data" class="form">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('First Name')</label>
                                    <input class="form-control" type="text" name="firstname" required value="{{$user->firstname}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">@lang('Last Name')</label>
                                    <input class="form-control" type="text" name="lastname" required value="{{$user->lastname}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input class="form-control" type="email" name="email" value="{{$user->email}}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Mobile Number')</label>
                                    <div class="input-group ">
                                        <span class="input-group-text mobile-code">+{{ $user->dial_code }}</span>
                                        <input type="number" name="mobile" value="{{ $user->mobile }}" id="mobile" class="form-control checkUser" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Address')</label>
                                    <input class="form-control" type="text" name="address" value="{{@$user->address}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group">
                                    <label>@lang('City')</label>
                                    <input class="form-control" type="text" name="city" value="{{@$user->city}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('State')</label>
                                    <input class="form-control" type="text" name="state" value="{{@$user->state}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Zip/Postal')</label>
                                    <input class="form-control" type="text" name="zip" value="{{@$user->zip}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Country') <span class="text--danger">*</span></label>
                                    <select name="country" class="form-control select2">
                                        @foreach($countries as $key => $country)
                                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}" @selected($user->country_code == $key)>{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Email Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                           data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="ev"
                                           @if($user->ev) checked @endif>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="form-group">
                                    <label>@lang('Mobile Verification')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                           data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="sv"
                                           @if($user->sv) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-3 col-12">
                                <div class="form-group">
                                    <label>@lang('2FA Verification') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="ts" @if($user->ts) checked @endif>
                                </div>
                            </div>
                            <div class="col-xl-3 col-12">
                                <div class="form-group">
                                    <label>@lang('KYC') </label>
                                    <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="kv" @if($user->kv == Status::KYC_VERIFIED) checked @endif>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Sub Balance MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Balance')</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.add.sub.balance', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="act">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="amount" class="form-control"
                                    placeholder="@lang('Please provide positive amount')" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Remark')</label>
                            <textarea class="form-control" placeholder="@lang('Remark')" name="remark" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div id="userStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($user->status == 1)
                            <span>@lang('Ban User')</span>
                        @else
                            <span>@lang('Unban User')</span>
                        @endif
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if ($user->status == 1)
                            <h6 class="mb-2">@lang('If you ban this user he/she won\'t able to access his/her dashboard.')</h6>
                            <div class="form-group">
                                <label>@lang('Reason')</label>
                                <textarea class="form-control" name="reason" rows="4" required></textarea>
                            </div>
                        @else
                            <p><span>@lang('Ban reason was'):</span></p>
                            <p>{{ $user->ban_reason }}</p>
                            <h4 class="text-center mt-3">@lang('Are you sure to unban this user?')</h4>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($user->status == 1)
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        @else
                            <button type="button" class="btn btn--dark"
                                data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@permit('admin.users.login')
    @push('breadcrumb-plugins')
        <a href="{{route('admin.users.login',$user->id)}}" target="_blank" class="btn btn-sm btn-outline--primary">
            <i class="las la-sign-in-alt"></i>@lang('Login as Client')
        </a>
    @endpush
@endpermit

@push('script')
    <script>
        (function($) {
            "use strict"
            $('.bal-btn').click(function() {
                var act = $(this).data('act');
                $('#addSubModal').find('input[name=act]').val(act);
                if (act == 'add') {
                    $('.type').text('Add');
                } else {
                    $('.type').text('Subtract');
                }
            });
            let mobileElement = $('.mobile-code');
            $('select[name=country]').change(function() {
                mobileElement.text(`+${$('select[name=country] :selected').data('mobile_code')}`);
            });

            $('select[name=country]').val('{{ @$user->country_code }}');
            let dialCode = $('select[name=country] :selected').data('mobile_code');
            let mobileNumber = `{{ $user->mobile }}`;
            mobileNumber = mobileNumber.replace(dialCode, '');
            $('input[name=mobile]').val(mobileNumber);
            mobileElement.text(`+${dialCode}`);

            $('.form').on('submit', function(){
                localStorage.setItem('isFormSubmit', true);
            });

            if(localStorage.getItem('isFormSubmit')){
                $('#edit-information-tab').addClass('active');
                $('#edit-information').addClass('active show');

                localStorage.removeItem('isFormSubmit');
            }else{
                $('#information-tab').addClass('active');
                $('#information').addClass('active show');
            }
        })(jQuery);
    </script>
@endpush
