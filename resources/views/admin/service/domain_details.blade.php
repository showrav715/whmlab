@extends('admin.layouts.app')

@section('panel')
<form action="{{ route('admin.order.domain.update') }}" method="POST">
<input type="hidden" name="id" value="{{ $domain->id }}">
@csrf
    <div class="row mb-none-30 mb-1">
   
    @if(@$domainRegisters->count()) 
        <div class="col-md-12 form-group ">
            <h6 class="text-center mb-3">@lang('Register Commands')</h6>
        </div>
        <div class="col-lg-12 col-md-12 mb-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-xsm-6 form-group">
                            <button class="btn btn-sm btn-outline--primary registerModal w-100" type="button">
                                <i class="las la-registered"></i>@lang('Register')
                            </button>
                        </div>
                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-xsm-6 form-group">
                            <button class="btn btn-sm btn-outline--primary moduleModal w-100" type="button" data-module="2">
                                <i class="las la-server"></i>@lang('Change Nameservers')
                            </button>
                        </div>
                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-xsm-6 form-group">
                            <button class="btn btn-sm btn-outline--primary moduleModal w-100" type="button" data-module="3">
                                <i class="las la-shopping-cart"></i>@lang('Renew')
                            </button>
                        </div> 
                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-xsm-6 form-group">
                            @php
                                $contactDetails = $domain->register ? route('admin.order.domain.contact', $domain->id) : '#';
                            @endphp
                            @permit('admin.order.domain.contact')
                                <a href="{{ $contactDetails }}" class="btn btn-sm btn-outline--primary w-100">
                                    <i class="las la-undo-alt"></i>@lang('Modify Contact Details')
                                </a>
                            @else
                                <button class="btn btn-sm btn-outline--primary w-100" type="button" disabled>
                                    <i class="las la-undo-alt"></i>@lang('Modify Contact Details')
                                </button>
                            @endpermit
                        </div> 

                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-xsm-6 form-group">
                            <button class="btn btn-sm btn-outline--primary moduleModal w-100" type="button" data-module="5" {{ $domain->id_protection ? 'disabled' : null }}>
                                <i class="las la-shopping-cart"></i>@lang('Enable ID Protection')
                            </button>
                        </div> 
                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-xsm-6 form-group">
                            <button class="btn btn-sm btn-outline--primary moduleModal w-100" type="button" data-module="6" 
                            {{ !$domain->id_protection ? 'disabled' : null }}>
                                <i class="las la-shopping-cart"></i>@lang('Disable ID Protection')
                            </button>
                        </div> 
                        
                    </div>
                </div>
            </div>
        </div> 
    @endif

        <div class="col-xl-12 col-md-12 mb-30">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Registration Date')</label>
                                <input type="text" class="datePicker form-control flex-grow-1" data-language='en' data-position='bottom left' 
                                value="{{ showDateTime($domain->reg_date, 'd-m-Y') }}" name="reg_date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Expiry Date') 
                                    <span class="text--primary" title="@lang('Expiry date and next due date should be same')"><i class="fas fa-info-circle"></i></span>
                                </label>
                                <input type="text" class="datePicker form-control flex-grow-1" data-language='en' data-position='bottom left' 
                                value="{{ showDateTime($domain->expiry_date, 'd-m-Y') }}" name="expiry_date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>
                                    @lang('Next Due Date')
                                    <span class="text--primary" title="@lang('Next due date and expiry date should be same')"><i class="fas fa-info-circle"></i></span>
                                </label>
                                <input type="text" class="datePicker form-control flex-grow-1" data-language='en' data-position='bottom left' 
                                value="{{ showDateTime($domain->next_due_date, 'd-m-Y') }}" name="next_due_date" autocomplete="off">
                            </div>
                        </div> 
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Next Invoice Date')</label>
                                <input type="text" class="datePicker form-control flex-grow-1" data-language='en' data-position='bottom left' 
                                value="{{ showDateTime($domain->next_invoice_date, 'd-m-Y') }}" name="next_invoice_date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Domain')</label>
                                <input class="form-control" type="text" name="domain" value="{{@$domain->domain}}">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Register')</label>
                                <select name="register_id" class="form-control"> 
                                    <option value="">@lang('Select One')</option>
                                    @foreach($domainRegisters as $register) 
                                        <option value="{{ $register->id }}" {{ $domain->domain_register_id == $register->id ? 'selected' : null}}>{{ $register->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Status')</label>
                                <select name="status" class="form-control"> 
                                    @foreach($domain::status() as $index => $data) 
                                        <option value="{{ $index }}" {{ $domain->status == $index ? 'selected' : null}}>{{ $data }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('First Payment Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="first_payment_amount" value="{{ getAmount(@$domain->first_payment_amount) }}" class="form-control">
                                    <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Recurring Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="recurring_amount" value="{{ getAmount(@$domain->recurring_amount) }}" class="form-control">
                                    <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Registration Period')</label>
                                <div class="input-group">
                                    <input type="text" name="reg_period" value="{{ @$domain->reg_period }}" class="form-control">
                                    <span class="input-group-text">@lang('Years')</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Nameserver 1')</label>
                                <input type="text" name="ns1" value="{{ @$domain->ns1 }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Nameserver 2')</label>
                                <input type="text" name="ns2" value="{{ @$domain->ns2 }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('Nameserver 3')</label>
                                <input type="text" name="ns3" value="{{ @$domain->ns3 }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6"> 
                            <div class="form-group ">
                                <label>@lang('Nameserver 4')</label>
                                <input type="text" name="ns4" value="{{ @$domain->ns4 }}" class="form-control">
                            </div>
                        </div> 
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group ">
                                <label>@lang('ID Protection') </label> 
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Enable')" data-off="@lang('Disabled')" name="id_protection" @if($domain->id_protection) checked @endif>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="form-group">
                                <label>@lang('Admin Notes')</label>
                                <textarea name="admin_notes" class="form-control h-45" rows="2">@php echo nl22br($domain->admin_notes); @endphp</textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div> 
        </div>  
    </div> 

    @permit('admin.order.domain.update')
        <div class="row mb-none-30">
            <div class="col-lg-12 col-md-12 mb-30">
                <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>  
            </div>
        </div>
    @endpermit
</form> 

{{-- Register Modal --}} 
<div id="registerModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="createModalLabel">@lang('Register Domain')</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.domain.module.command') }}">
                @csrf  
                <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
                <input type="hidden" name="module_type" required value="1">
                <div class="modal-body"> 
                    <div class="form-group">
                        <label>@lang('Domain Register')</label>
                        <input type="text" class="form-control" disabled value="{{ @$domain->register->name ?? 'N/A' }}">
                    </div>
                    <div class="form-group">
                        <label>@lang('Domain')</label>
                        <input type="text" class="form-control" disabled value="{{ $domain->domain }}">
                    </div> 
                    <div class="form-group"> 
                        <label>@lang('Registration Period')</label>
                        <div class="input-group">
                            <input type="text" class="form-control" disabled value="{{ $domain->reg_period }}">
                            <span class="input-group-text">@lang('Years')</span>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label for="ns1">@lang('Nameserver 1')</label> 
                        <input type="text" class="form-control" name="ns1" id="ns1" required value="{{ $domain->ns1 ?? @$domain->register->ns1 }}">
                    </div>
                    <div class="form-group">
                        <label for="ns2">@lang('Nameserver 2')</label>
                        <input type="text" class="form-control" name="ns2" id="ns2" required value="{{ $domain->ns2 ?? @$domain->register->ns2 }}">
                    </div>
                    <div class="form-group">
                        <label for="ns3">@lang('Nameserver 3')</label>
                        <input type="text" class="form-control" name="ns3" id="ns3" value="{{ $domain->ns3 }}">
                    </div>
                    <div class="form-group">
                        <label for="ns4">@lang('Nameserver 4')</label>
                        <input type="text" class="form-control" name="ns4" id="ns4" value="{{ $domain->ns4 }}">
                    </div>
                    <div>
                        <input type="checkbox" name="send_email" id="send_email"> 
                        <label for="send_email">@lang('Send Email')</label>
                    </div>
                </div> 
                @permit('admin.domain.module.command')
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div> 
                @endpermit
            </form> 
        </div>
    </div>
</div> 

{{-- Module Modal --}}
<div id="moduleModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="createModalLabel">@lang('Confirm Register Command')</h6>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.domain.module.command') }}">
                @csrf  
                <input type="hidden" name="domain_id" value="{{ $domain->id }}" required>
                <input type="hidden" name="module_type" required>
                <div class="modal-body"> 
                    <div class="form-group">
                        @lang('Are you sure to want run the ') <span class="moduleName text--danger"></span> @lang(' command request to the ')
                        <span class="registerName fw-bold"></span>?
                    </div>
                </div>
                @permit('admin.domain.module.command')
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div> 
                @endpermit
            </form> 
        </div>
    </div>
</div> 

@endsection

@push('breadcrumb-plugins')  
<div class="d-flex justify-content-end flex-wrap gap-2">
    @permit('admin.orders.details')
        <a href="{{ route('admin.orders.details', $domain->order_id) }}" class="btn btn-sm btn-outline--dark">
            <i class="la la-undo"></i> @lang('Go to Order')
        </a>
    @endpermit

    @permit('admin.invoices.domain.all')
        <a href="{{ route('admin.invoices.domain.all', $domain->id) }}" class="btn btn-sm btn-outline--primary me-1">
            <i class="las la-file-alt"></i> @lang('Invoices')
        </a>
    @endpermit
</div>
@endpush 

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
           
            // ========================================= daterangepicker =========================================
            // ========================================= daterangepicker =========================================
            $('.datePicker').daterangepicker({
                autoApply: true,
                autoUpdateInput: false,
                singleDatePicker: true,
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });

            $('.datePicker').on('apply.daterangepicker', (event, picker) => {
                $(event.target).val(picker.startDate.format('DD-MM-YYYY')); 
            });
            // ========================================= daterangepicker =========================================
            // ========================================= daterangepicker =========================================

            $('.moduleModal').on('click', function () {
                var modal = $('#moduleModal');

                var moduleName = $(this).text();
                modal.find('.registerName').text(getRegisterName());
                modal.find('.moduleName').text(moduleName);
                modal.find('input[name=module_type]').val($(this).data('module'));

                modal.modal('show');
            });

            $('.registerModal').on('click', function () {
                var modal = $('#registerModal');
                modal.modal('show');
            });

            function getRegisterName(){
                return $('select[name=register_id]').find(":selected").text();
            }

        })(jQuery);
    </script>
@endpush 
