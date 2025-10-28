@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('Order Status')</label> <div>@php echo @$order->showStatus; @endphp</div>
                    </li>
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('Order Date')</label> 
                        <div>{{ showDateTime(@$order->created_at, 'd/m/Y') }}</div> 
                    </li>
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('Order')</label>
                        <div>#{{ @$order->id }}</div>
                    </li>
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('User')</label>
                        @if($order->user)
                            <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', @$order->user->id) : 'javascript:void(0)' }}">
                                {{ @$order->user->fullname }}
                            </a>
                        @endif
                    </li>
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('IP Address')</label>
                        <a href="https://extreme-ip-lookup.com/{{ @$order->ip_address }}" target="_blank">{{ @$order->ip_address ?? 'N/A' }}</a>
                    </li>
                </ul>
            </div>
        </div> 
    </div>  
    <div class="col-xl-6 col-md-6 mb-30">
        <div class="card overflow-hidden box--shadow1">
            <div class="card-body">
                <ul class="list-group list-group-flush"> 
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('Amount')</label> <div>{{ showAmount(@$order->amount) }}</div>
                    </li>
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('Discount')</label> <div>{{ showAmount(@$order->discount) }}</div>
                    </li>
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('After Discount')</label> <div>{{ showAmount(@$order->after_discount) }}</div>
                    </li>
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('Invoice')</label> 
                        <div>
                            @permit('admin.invoices.details')
                                <a href="{{ route('admin.invoices.details', @$order->invoice->id) }}">{{ @$order->invoice->getInvoiceNumber }}</a>
                            @else 
                                <a href="javascript:void(0)">{{ @$order->invoice->getInvoiceNumber }}</a>
                            @endpermit
                        </div> 
                    </li>
                    <li class="list-group-item justify-content-between d-flex flex-wrap">
                        <label>@lang('Coupon Code')</label>
                        <div>{{ @$order->coupon->code ?? __('N/A') }}</div>
                    </li>
                </ul>
            </div>
        </div> 
    </div>  

    @php
        $hostings = $order->hostings;
        $domains = $order->domains;
        $invoice = $order->invoice;
    @endphp


@permit('admin.order.notes')
    <div class="col-md-12 text-end mb-4">
        <button class="btn btn-sm btn-outline--info noteBtn" type="button">
            <i class="las la-sticky-note"></i>@lang('Admin Notes')
        </button>
    </div>
    <div class="col-md-12 note mb-4"> 
        <div class="card">
            <div class="card-header">
                <h5>@lang('Additional Information or Notes')</h5>
            </div>
            <form action="{{ route('admin.order.notes') }}" method="post">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="card-body">
                    <textarea name="admin_notes" rows="6" class="form-control">@php echo $order->admin_notes; @endphp</textarea>
                    <button type="submit" class="btn btn--primary h-45 w-100 mt-4">@lang('Submit')</button>
                </div>
            </form> 
        </div>
    </div>
@endpermit

<form action="{{ route('admin.order.accept') }}" method="post" class="form w-100">
@csrf
<input type="hidden" name="order_id" value="{{ $order->id }}">

    <div class="col-xl-12 col-md-12 mb-30">
        <div class="card ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('Service')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('Billing Cycle')</th> 
                            <th>@lang('Amount')</th>
                            <th>@lang('Payment Status')</th>
                            <th>@lang('Status')</th>
                        </tr> 
                        </thead> 
                        <tbody>

                        @foreach($hostings as $hosting)
                            @php $product = $hosting->product; @endphp
                            <tr>
                                <td>
                                    <span class="fw-bold">
                                        @permit('admin.order.hosting.details')
                                            <a href="{{ route('admin.order.hosting.details', $hosting->id) }}">{{ __(@$product->item) }}</a>
                                        @else
                                            <a href="javascript:void(0)">{{ __(@$product->item) }}</a>
                                        @endpermit
                                    </span>
                                </td>
                                <td>
                                    {{ __(@$product->serviceCategory->name) }} - 
                                    @permit('admin.product.update.page')
                                        <a href="{{ route('admin.product.update.page', $product->id) }}" target="_blank">{{ __(@$product->name) }}</a>
                                    @else 
                                        <a href="javascript:void(0)">{{ __(@$product->name) }}</a>
                                    @endpermit
                                </td>
                                <td>
                                    {{ @billingCycle(@$hosting->billing_cycle, true)['showText'] }}
                                </td>
                                <td>
                                    {{ showAmount(@$hosting->recurring_amount) }}
                                </td>
                                <td>
                                    @php echo $invoice->showStatus; @endphp
                                </td>
                                <td>
                                    @php echo $hosting->showStatus; @endphp
                                </td>
                            </tr>
                            @if(@$hosting->status == 2 && $order->status == 2 && @$product->process() != 'manual') {{-- For hosting/service automation --}}
                                <tr class="bg-light">
                                    <td class="text-muted fullwidth-td" colspan="100%">
                                        <div class="row align-items-center">
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <span class="fw-bold">@lang('Username')</span>
                                                    <input type="text" class="form-control" name="hostings[{{ $hosting->id }}][username]" value="{{ $hosting->username }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <span class="fw-bold">@lang('Password')</span>
                                                    <input type="text" class="form-control" name="hostings[{{ $hosting->id }}][password]" value="{{ $hosting->password }}">
                                                </div>
                                            </div> 
                                            <div class="col-sm-12 col-md-4">
                                                <div class="form-group">
                                                    <span class="fw-bold">@lang('Server')</span>
                                                    <select name="hostings[{{ $hosting->id }}][server_id]" class="form-control">
                                                        <option value="">@lang('None')</option>
                                                        @foreach(@$product->serverGroup->servers ?? [] as $index => $server) 
                                                            <option value="{{ $server->id }}" {{ $server->id == $hosting->server_id ? 'selected' : null }}>
                                                                {{ $server->hostname }} - {{ $server->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="col-sm-6 col-md-4">
                                                <div class="form-group">
                                                    <input type="checkbox" name="hostings[{{ $hosting->id }}][run_create_module]" checked id="run_create_module{{ $hosting->id }}"> 
                                                    <label for="run_create_module{{ $hosting->id }}"><span class="fw-bold">@lang('Run Module Create')</span></label>
                                                </div>
                                            </div>
                                            @if($product->welcome_email != 0)
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-group">
                                                        <input type="checkbox" name="hostings[{{ $hosting->id }}][send_email]" checked id="send_email{{ $hosting->id }}">
                                                        <label for="send_email{{ $hosting->id }}"><span class="fw-bold">@lang('Send Welcome Email')</span></label>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        
                        @foreach($domains as $domain)
                            <tr> 
                                <td>
                                    <span class="fw-bold">
                                        @permit('admin.order.domain.details')
                                            <a href="{{ route('admin.order.domain.details', $domain->id) }}">@lang('Domain')</a>
                                        @else
                                            <a href="javascript:void(0)">@lang('Domain')</a>
                                        @endpermit
                                    </span>
                                </td>
                                <td>
                                    @lang('Registration') - {{ $domain->domain }}
                                    @if($domain->id_protection) 
                                    <br>
                                       + @lang('ID Protection')
                                    @endif
                                </td>
                                <td>
                                    {{ __($domain->reg_period) }} @lang('Year/s')
                                </td>
                                <td>
                                    {{ showAmount(@$domain->recurring_amount) }}
                                </td>
                                <td>
                                    @php echo $invoice->showStatus; @endphp
                                </td>
                                <td>
                                    @php echo $domain->showStatus; @endphp
                                </td>
                            </tr>
                            @if(@$domain->status == 2 && $order->status == 2 && $domainRegisters) {{-- For domain automation --}}
                                <tr class="bg-light">
                                    <td class="text-muted fullwidth-td" colspan="100%">
                                        <div class="row align-items-center">
                                            <div class="col-sm-12 col-md-4">
                                                <div class="form-group">
                                                    <span class="fw-bold">@lang('Register')</span>
                                                    <select name="domains[{{ $domain->id }}][register]" class="form-control">
                                                        @foreach($domainRegisters as $register) 
                                                            <option value="{{ $register->id }}" {{ $domain->domain_register_id == $register->id ? 'selected' : null}}>
                                                                {{ $register->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> 
                                            <div class="col-sm-6 col-md-2">
                                                <div class="form-group">
                                                    <input type="checkbox" name="domains[{{ $domain->id }}][domain_register]" checked id="domain_register{{ $domain->id }}">
                                                    <label for="domain_register{{ $domain->id }}"><span class="fw-bold">@lang('Send to Register')</span></label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-2">
                                                <div class="form-group">
                                                    <input type="checkbox" name="domains[{{ $domain->id }}][send_email]" checked id="send_domain_email{{ $domain->id }}">
                                                    <label for="send_domain_email{{ $domain->id }}"><span class="fw-bold">@lang('Send Email')</span></label>
                                                </div>
                                            </div> 
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        
                        {{-- For domain renew --}}
                        @foreach($invoice->items()->where('item_type', 1)->where('next_due_date', '!=', null)->groupBy('domain_id')->cursor() as $renewDomain)
                            @php $domain = $renewDomain->domain; @endphp
                            <tr>  
                                <td>
                                    <span class="fw-bold">
                                        @permit('admin.order.domain.details')
                                            <a href="{{ route('admin.order.domain.details', $domain->id) }}">@lang('Domain')</a>
                                        @else 
                                            <a href="javascript:void(0)">@lang('Domain')</a>
                                        @endpermit
                                    </span>
                                </td>
                                <td>
                                    @lang('Renew') - {{ $domain->domain }}
                                </td>
                                <td>
                                    {{ __($renewDomain->reg_period) }} @lang('Year/s')
                                </td>
                                <td>
                                    {{ showAmount(@$renewDomain->recurring_amount) }}
                                </td>
                                <td>
                                    @php echo $invoice->showStatus; @endphp
                                </td>
                                <td>
                                    @php echo $domain->showStatus; @endphp
                                </td>
                            </tr>
                        @endforeach 

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
        </div>
    </div>
    @if($order->status == 2)
        <div class="col-lg-12 mb-30 justify-content-end d-flex">
            @permit('admin.order.cancel')
                <button class="btn btn-sm btn-outline--danger cancelBtn me-2" type="button">
                    <i class="la la-times"></i> @lang('Cancel Order')
                </button>
            @endpermit
            @permit('admin.order.accept')
                <button class="btn btn-sm btn-outline--primary acceptBtn" type="button">
                    <i class="la la-check"></i> @lang('Accept Order')
                </button>
            @endpermit
        </div>
    @endif 
</form>

</div>
 
{{-- Accept Modal --}}
<div id="acceptModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <div class="modal-body">
                <p class="question">
                    @lang('Are you sure to want to accept this order')?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                <button type="button" class="btn btn--primary submitBtn">@lang('Yes')</button>
            </div> 
        </div>
    </div>
</div>

{{-- Cancel Modal --}}
<div id="cancelModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.order.cancel') }}">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <p class="question">
                        @lang('Are you sure to want to cancel this order')?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Mark as Modal --}}
<div id="markPendingModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.order.mark.pending') }}">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <p class="question">
                        @lang('Are you sure to want to set this order back to Pending')?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('breadcrumb-plugins') 
<div class="d-flex justify-content-end flex-wrap gap-2">
    @if($order->status != 2)
        @permit('admin.order.mark.pending')
            <button class="btn btn-sm btn-outline--warning pendingBtn">
                <i class="la la-spinner"></i> @lang('Mark as Pending')
            </button>
        @endpermit
    @endif
    @permit('admin.invoices.details')
        <a href="{{ route('admin.invoices.details', @$order->invoice->id) }}" class="btn btn-sm btn-outline--primary">
            <i class="las la-file-alt"></i> @lang('Invoice')
        </a>
    @endpermit
</div>
@endpush

@push('style')
<style>
    @media (max-width: 991px) {
        .table-responsive--md tr .fullwidth-td {
            display: block;
            padding-left: 15px !important;
            text-align: left !important;
        }
    }
</style> 
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            
            $('.note').toggle();

            $('.acceptBtn').on('click', function () {
                var modal = $('#acceptModal');
                modal.modal('show');
            });
            
            $('.cancelBtn').on('click', function () {
                var modal = $('#cancelModal');
                modal.modal('show');
            });
            
            $('.pendingBtn').on('click', function () {
                var modal = $('#markPendingModal');
                modal.modal('show');
            });
            
            $('.submitBtn').on('click', function () {
                $('.form').submit();
            });
            
            $('.noteBtn').on('click', function () {
                $('.note').slideToggle(500);
            });

        })(jQuery);
    </script>
@endpush
