@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
        <div class="row gy-4 justify-content-center">
            <div class="col-lg-7">
                <div class="card custom--card style-two w-100">
                    <form action="{{ route('user.domain.nameserver.update') }}" method="post">
                        @csrf
                        <div class="card-body"> 

                            <div class="card-header text-center">
                                @if($domain->status != 1)
                                    <h6 class="text--danger mb-4">
                                        @lang('This domain is not currently active. Domains cannot be managed unless active')
                                    </h6>
                                @endif
                                @if($domain->status == 1)
                                    <h6 class="mb-3">@lang('What would you like to do today')?</h6>
                                    <span class="d-block mb-2 text-bold"> 
                                        <a href="javascript:void(0)" class="nameserverModal text--primary">@lang('Change the nameservers your domain points to')</a>
                                    </span>
                                    <span class="d-block mb-2 text-bold"> 
                                        <a class="text--primary" href="{{ $domain->register ? route('user.domain.contact', $domain->id) : 'javascript:void(0)' }}">
                                            @lang('Update the WHOIS contact information for your domain')
                                        </a>
                                    </span>
                                    <span class="d-block text-bold"> 
                                        <a href="javascript:void(0)" class="renewModal text--primary">@lang('Renew Your Domain')</a>
                                    </span>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <ul class="list-group list-group-flush text-center">
                                        <li class="list-group-item d-flex justify-content-between">
                                            @lang('Domain')
                                            <strong><a href="http://{{ $domain->domain }}" target="_blank">{{ $domain->domain }}</a></strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            @lang('Registration Date')
                                            <strong>{{ $domain->reg_date ? showDateTime($domain->reg_date, 'd/m/Y') : 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            @lang('Next Due Date')
                                            <strong>{{ $domain->next_due_date ? showDateTime($domain->next_due_date, 'd/m/Y') : 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            @lang('Status')
                                            <strong>@php echo $domain->showStatus; @endphp</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            @lang('First Payment Amount')
                                            <strong>{{ showAmount($domain->first_payment_amount) }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between">
                                            @lang('Recurring Amount')
                                            <strong>
                                            {{ showAmount($domain->recurring_amount) }} {{ $domain->reg_period }} @lang('Year/s') 
                                            @if($domain->id_protection)
                                                @lang('with ID Protection')
                                            @endif
                                            </strong>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div> 
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="nameserverModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Change Nameservers')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.domain.nameserver.update') }}" method="post">
                @csrf
                <input type="hidden" name="domain_id" required value="{{ $domain->id }}">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-12"> 
                            <div class="">
                                @lang('You can change where your domain points to here. Please be aware changes can take up to 24 hours to propagate')
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns1">@lang('Nameserver 1')</label>
                            <input type="text" class="form-control form--control h-45" name="ns1" id="ns1" required placeholder="@lang('ns1.example.com')" value="{{ $domain->ns1 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns2">@lang('Nameserver 2')</label>
                            <input type="text" class="form-control form--control h-45" name="ns2" id="ns2" required placeholder="@lang('ns2.example.com')" value="{{ $domain->ns2 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns3">@lang('Nameserver 3')</label>
                            <input type="text" class="form-control form--control h-45" name="ns3" id="ns3" placeholder="@lang('ns3.example.com')" value="{{ $domain->ns3 }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="ns4">@lang('Nameserver 4')</label>
                            <input type="text" class="form-control form--control h-45" name="ns4" id="ns4" placeholder="@lang('ns4.example.com')" value="{{ $domain->ns4 }}">
                        </div>
                    </div>
                </div> 
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="renewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Domain Renewal')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('shopping.cart.domain.renew') }}" method="post">
                @csrf
                <input type="hidden" name="domain_id" required value="{{ $domain->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3"> 
                            <h5>{{ $domain->domain }}</h5>
                            @lang('Expiry Date'): {{ showDateTime($domain->expiry_date, 'd M Y') }} ({{ diffForHumans($domain->expiry_date) }})
                        </div> 
                        <div class="col-md-12 form-group"> 
                            <label for="ns1">@lang('Available Renewal Periods')</label>
                            <select name="renew_year" class="form-control form--control h-45 form-select">
                                @forelse(@$renewPricing ? @$renewPricing->renewPrice() : [] as $year => $data)
                                    <option value="{{ $year }}">
                                        {{ $year }} @lang('Year/s') @ {{ showAmount($data['renew']) }}
                                    </option>
                                @empty 
                                    <option>@lang('N/A')</option>
                                @endforelse 
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
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
            $('.renewModal').on('click', function() {
                var modal = $('#renewModal');
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
