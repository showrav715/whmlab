@extends('admin.layouts.app')

@section('panel')
<form class="form-horizontal" method="post">
    @csrf 
    <div class="row">
        <div class="col-xl-6 col-md-12 form-group">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="d-flex flex-wrap">
                                    <label for="create_default_invoice_days">@lang('Invoice Generation')</label>
                                    <a href="javascript:void(0)" class="text--primary advanceBtn ms-1">
                                        (@lang('Advanced Settings')) 
                                    </a>
                                </div>
                                <div class="input-group">
                                    <input type="number" name="create_default_invoice_days" class="form-control" required id="create_default_invoice_days" 
                                    value="{{ $setting->create_default_invoice_days }}"> 
                                    <span class="input-group-text">@lang('Days')</span>
                                </div>
                                <p class="text-muted">
                                    <small>
                                        <i class="las la-info-circle"></i>
                                        <span class="fst-italic">@lang('Enter the default number of days before the due payment date to generate invoices')</span>
                                    </small>
                                </p>
                            </div>   
                        </div> 
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="invoice_send_reminder_days">@lang('Invoice Unpaid Reminder')</label>
                                <div class="input-group">
                                    <input type="number" name="invoice_send_reminder_days" class="form-control" required id="invoice_send_reminder_days"
                                    value="{{ $setting->invoice_send_reminder_days }}">
                                    <span class="input-group-text">@lang('Days')</span>
                                </div>
                                <p>
                                    <small>
                                        <i class="las la-info-circle"></i>
                                        <span class="fst-italic">
                                            @lang('Enter the number of days before the invoice due date you would like to send a reminder (0 to disable)')
                                        </span>
                                    </small>
                                </p>
                            </div>   
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                    <label for="invoice_first_over_due_reminder">@lang('First Overdue Reminder')</label>
                                <div class="input-group">
                                    <input type="number" name="invoice_first_over_due_reminder" class="form-control" required id="invoice_first_over_due_reminder"
                                    value="{{ $setting->invoice_first_over_due_reminder }}">
                                    <span class="input-group-text">@lang('Days')</span>
                                </div>
                                <p>
                                    <small>
                                        <i class="las la-info-circle"></i>
                                        <span class="fst-italic">
                                            @lang('Enter the number of days after the invoice due date you would like to send the first overdue notice (0 to disable)')
                                        </span>
                                    </small>
                                </p>
                            </div>   
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                    <label for="invoice_second_over_due_reminder">@lang('Second Overdue Reminder')</label>
                                <div class="input-group">
                                    <input type="number" name="invoice_second_over_due_reminder" class="form-control" required id="invoice_second_over_due_reminder"
                                    value="{{ $setting->invoice_second_over_due_reminder }}">
                                    <span class="input-group-text">@lang('Days')</span>
                                </div>
                                <p>
                                    <small>
                                        <i class="las la-info-circle"></i>
                                        <span class="fst-italic">
                                            @lang('Enter the number of days after the invoice due date you would like to send the second overdue notice (0 to disable)')
                                        </span>
                                    </small>
                                </p>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12 form-group">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                    <label for="invoice_third_over_due_reminder">@lang('Third Overdue Reminder')</label>
                                <div class="input-group">
                                    <input type="number" name="invoice_third_over_due_reminder" class="form-control" required id="invoice_third_over_due_reminder"
                                    value="{{ $setting->invoice_third_over_due_reminder }}">
                                    <span class="input-group-text">@lang('Days')</span>
                                </div>
                                <p>
                                    <small>
                                        <i class="las la-info-circle"></i>
                                        <span class="fst-italic">
                                            @lang('Enter the number of days after the invoice due date you would like to send the third (final) overdue notice (0 to disable)')
                                        </span>
                                    </small>
                                </p>
                            </div>   
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                    <label for="late_fee_days">@lang('Add Late Fee Days')</label>
                                <div class="input-group">
                                    <input type="number" name="late_fee_days" class="form-control" required id="late_fee_days"
                                    value="{{ $setting->late_fee_days }}">
                                    <span class="input-group-text">@lang('Days')</span>
                                </div>
                                <p>
                                    <small>
                                        <i class="las la-info-circle"></i>
                                        <span class="fst-italic">
                                            @lang('Enter the number of days after the due payment date you want to add the late fee (0 to disable)')
                                        </span>
                                    </small>
                                </p>
                            </div>   
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="invoice_late_fee_amount">@lang('Late Fee Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="invoice_late_fee_amount" class="form-control" required id="invoice_late_fee_amount"
                                    value="{{ getAmount($setting->invoice_late_fee_amount) }}">
                                    <span class="input-group-append">
                                        <select name="invoice_late_fee_type" class="form-control">
                                            <option value="1" {{ $setting->invoice_late_fee_type == 1 ? 'selected' : null }}>{{ __(gs('cur_text')) }}</option>
                                            <option value="2" {{ $setting->invoice_late_fee_type == 2 ? 'selected' : null }}>%</option>
                                        </select>
                                    </span>
                                </div>
                                <p>
                                    <small>
                                        <i class="las la-info-circle"></i>
                                        <span class="fst-italic">
                                            @lang('Enter the amount (percentage or monetary value) to apply to late invoices (set to 0 to disable)')
                                        </span>
                                    </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-12"> 
                            <div class="form-group mb-0">
                                <label>@lang('Payment Reminder Emails')</label>
                            </div>
                            <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Enable')" data-off="@lang('Disable')" name="invoice_send_reminder" @if($setting->invoice_send_reminder) checked @endif>
                            <p>
                                <small>
                                    <i class="las la-info-circle"></i>
                                    <span class="fst-italic">
                                        @lang('Check to activate invoice payment reminder emails')
                                    </span>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @permit('admin.billing.setting.update')
        <div class="row justify-content-center">
            <div class="col-lg-12 mt-3">
                <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
            </div>
        </div>
    @endpermit
</form>

<div class="modal fade" id="advaceModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Advanced Settings')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.billing.setting.advanced') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center fw-bold mb-2">@lang('Per Billing Cycle Settings')</h6>
                                    <span class="text-center d-block mb-3">
                                        @lang('This allows you to specify for certain cycles to generate further or less in advance of the due date than the default specified above')
                                    </span>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-lg-4 col-xl-2">
                                        <div class="form-group">
                                            <label for="monthly">@lang('Monthly')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" placeholder="0" name="monthly" required id="monthly">
                                                <span class="input-group-text">@lang('Days')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-xl-2">
                                        <div class="form-group">
                                            <label for="quarterly">@lang('Quarterly')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" placeholder="0" name="quarterly" required id="quarterly">
                                                <span class="input-group-text">@lang('Days')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-xl-2">
                                        <div class="form-group">
                                            <label for="semi_annually">@lang('Semi-Annually')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" placeholder="0" name="semi_annually" required id="semi_annually">
                                                <span class="input-group-text">@lang('Days')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-xl-2">
                                        <div class="form-group">
                                            <label for="annually">@lang('Annually')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" placeholder="0" name="annually" required id="annually">
                                                <span class="input-group-text">@lang('Days')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-xl-2">
                                        <div class="form-group">
                                            <label for="biennially">@lang('Biennially')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" placeholder="0" name="biennially" required id="biennially">
                                                <span class="input-group-text">@lang('Days')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-xl-2">
                                        <div class="form-group">
                                            <label for="triennially">@lang('Triennially')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" placeholder="0" name="triennially" required id="triennially">
                                                <span class="input-group-text">@lang('Days')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                       @lang('(Leave blank to use default setting for a cycle)')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center fw-bold mb-2">@lang('Domain Settings')</h6>
                                    <span class="text-center d-block mb-3">
                                        @lang('Enter the number of days before the renewal date to generate invoices for domain renewals below')
                                    </span>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-12 col-lg-4 col-xl-4">
                                        <div class="form-group">
                                            <label for="create_domain_invoice_days">@lang('Days For Domain')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" placeholder="0" name="create_domain_invoice_days" required id="create_domain_invoice_days" value="{{ $setting->create_domain_invoice_days }}">
                                                <span class="input-group-text">@lang('Days')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                       @lang('(Leave blank to use default setting)')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @permit('admin.billing.setting.advanced')
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                @endpermit
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .custom-pricing {
        background: #fafafa;
        padding: 30px 15px;
        border-radius: 5px;
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
    }
    .custom-pricing .border-line-title {
        margin-top: 0;
    }
    .custom-pricing .form-control {
        background: white;
    }
</style>
@endpush

@push('script')
    <script>
        (function($){
            "use strict"; 

            $('.advanceBtn').on('click', function () {
                var modal = $('#advaceModal');
                var data = @json($setting->create_invoice);
      
                for(var [field, value] of Object.entries(data)) {
                    modal.find(`input[name=${field}]`).val(value);
                }

                modal.modal('show');
            });

        })(jQuery);    
    </script> 
@endpush
