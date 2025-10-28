@extends($activeTemplate.'layouts.master')

@section('content')
    <div class="pt-60 pb-60 bg--light section-full">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
            
                    <div class="row gy-4">  
                        <div class="col-lg-5">
                            <div class="card custom--card style-two h-100">
                                <div class="card-body">
                                    <div class="row">
                                        @if(!$product->server_group_id) 
                                            <div class="col-md-12">
                                                <div class="new-card">
                                                    <span class="fa-stack fa-lg">
                                                        <i class="fas fa-circle fa-stack-2x"></i>
                                                        @php 
                                                            $icon = 'hdd'; 
                    
                                                            if($product->product_type == 3){
                                                                $icon = 'server'; 
                                                            }
                                                            elseif($product->product_type == 4){
                                                                $icon = 'archive';
                                                            }
                                                        @endphp
                                                        <i class="fas fa-{{ $icon }} fa-stack-1x fa-inverse"></i>
                                                    </span>
                                                    <h3 class="text-center">{{ __(@$service->product->name) }}</h3>
                                                    <h4 class="text-center">{{ __(@$service->product->serviceCategory->name) }}</h4>
                                                    <span class="text-center d-block">
                                                        @php echo $service->showStatus; @endphp
                                                    </span>
                                                </div>
                    
                                                @if($status == 1)
                                                    <button class="btn btn--danger btn--sm w-100 mt-2 {{ $service->cancelRequest ? 'disabled' : 'cancenRequest' }}">  
                                                        @lang('Request Cancellation') 
                                                    </button> 
                                                @endif
                    
                                                @if($service->cancelRequest && $service->cancelRequest->status == 2)
                                                    <small class="text-center w-100 d-block mt-2 text--danger">
                                                        @lang('There is an outstanding cancellation request for this product/service')
                                                    </small>
                                                @endif
                                            </div> 
                                        @else 
                                            @if($status == 1)
                                                <div class="col-md-12">
                                                    <div class="new-card text-center">
                                                        <h4 class="mb-3">@lang('Package/Domain')</h4>
                                                        <div>
                                                            <em>{{ __($product->serviceCategory->name) }}</em>
                                                            <h4>{{ __($product->name) }}</h4>
                                                            @if($service->domain)
                                                                <a href="http://{{ $service->domain }}" target="_blank">www.{{ $service->domain }}</a>
                                                            @endif
                                                            <div class="d-block">
                                                                <a class="btn btn--success btn--xs mt-3" href="http://{{ $service->domain }}" target="_blank">
                                                                    @lang('Visit Website')
                                                                </a>
                                                                @if($hasAccount)
                                                                    <a 
                                                                        class="btn btn--primary btn--xs mt-3" 
                                                                        href="{{ route('user.login.hosting', $service->id) }}"
                                                                    >
                                                                        @lang('Login to '.@$serverGroup->getType ?? 'Control Panel')
                                                                    </a>
                                                                @endif
                                                                <a href="{{ session()->get('hostingLoginUrl') ?? '#' }}" class="hostingLogin" target="_blank"></a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if(@$serverGroup->type == 1 && $diskUsagePercent)
                                                        <div class="new-card mt-4">
                                                            <h4 class="text-center mb-3">@lang('Disk Usage')</h4>
                                                            <div class="row"> 
                                                                <div class="col-lg-12 form-group">
                                                                    <div class="progress custom--progress progress-bg">
                                                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                                                            style="width: {{ $diskUsagePercent }};" 
                                                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                                        </div>
                                                                        <div class="progress-text text-white">
                                                                            {{ $diskUsagePercent }}
                                                                        </div>
                                                                        </div>
                                                                    <small>
                                                                        {{ @$accountSummary['diskused'] }} / {{ @$accountSummary['disklimit'] }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                            @elseif($status == 2)
                                                <div class="col-md-12">
                                                    <div class="new-card bg--warning">
                                                        <h3 class="mb-3">@lang('Pending')</h3>
                                                        <small class="d-block">@lang('This hosting package is currently Pending')</small>
                                                        <small>@lang('You cannot begin using this hosting account until it is activated')</small>
                                                    </div>
                                                </div>
                                            @elseif($status == 3)
                                                <div class="col-md-12">
                                                    <div class="new-card bg--warning">
                                                        <h3 class="mb-3">@lang('Suspended')</h3>
                                                        <small class="d-block">@lang('This hosting package is currently Suspended')</small>
                                                        <small>@lang('You cannot continue to use or manage this package until it is reactivated')</small>
                                                    </div>
                                                </div>
                                            @elseif($status == 4)
                                                <div class="col-md-12">
                                                    <div class="new-card bg--warning">
                                                        <h3 class="mb-3">@lang('Terminated')</h3>
                                                        <small>@lang('This hosting package is currently Terminated')</small>
                                                    </div>
                                                </div>
                                            @elseif($status == 5)
                                                <div class="col-md-12">
                                                    <div class="new-card bg--warning">
                                                        <h3 class="mb-3">@lang('Cancelled')</h3>
                                                        <small>@lang('This hosting package is currently Cancelled')</small>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 text-center">
                            <div class="card custom--card style-two h-100">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush text-center">
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('Registration Date')
                                            <strong>{{ @$service->reg_date ? showDateTime(@$service->reg_date, 'd/m/Y') : 'N/A' }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('First Payment Amount')
                                            <strong>{{ showAmount($service->first_payment_amount) }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('First Payment Amount')
                                            <strong>{{ showAmount($service->first_payment_amount) }}</strong>
                                        </li>
                                        @if($service->billing != 1)
                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                @lang('Recurring Amount')
                                                <strong>{{ showAmount($service->recurring_amount) }}</strong>
                                            </li>
                                        @endif
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('Billing Cycle')
                                            <strong>{{ billingCycle(@$service->billing_cycle, true)['showText'] }}</strong>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            @lang('Next Due Date')
                                            <strong>
                                                @if($service->billing_cycle == 0)
                                                    @lang('N/A')
                                                @else 
                                                    {{ @$service->next_due_date ? showDateTime(@$service->next_due_date, 'd/m/Y') : 'N/A' }}
                                                @endif
                                            </strong>
                                        </li>
                                    </ul>
                                </div>  
                            </div>                              
                        </div> 
                    </div>
    
                    @if(count($product->getConfigs))
                        <h4 class="mt-4 text-center">@lang('Configurable Options')</h4>
                        <div class="card custom--card style-two w-100 mt-4">
                            <div class="card-body">    
                                <ul class="list-group list-group-flush text-center">
                                    @foreach($product->getConfigs as $config)
                                        @forelse($config->group->options as $option)  
                                            <li class="list-group-item d-flex justify-content-between">
                                                {{ __(@$option->name) }}
                                                <strong>
                                                    {{ @$service->hostingConfigs->where('configurable_group_option_id', $option->id)->first()->option->name ?? __('N/A') }}
                                                </strong>
                                            </li>
                                        @empty
                                            {{ __(@$emptyMessage) }}
                                        @endforelse
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if($product->product_type == 3)
                        <h3 class="mt-4 text-center">@lang('Server Information')</h3>
                        <div class="card custom--card style-two w-100 mt-4">
                            <div class="card-body">   
                                <ul class="list-group list-group-flush text-center">
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Hostname')
                                        <strong> 
                                            {{ $service->domain ?? 'N/A' }}  
                                        </strong> 
                                    </li> 
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Primary IP')
                                        <strong>
                                            {{ $service->dedicated_ip ?? 'N/A' }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Nameservers')
                                        <strong>
                                            {{ $service->ns1 ?? 'N/A' }}, {{ $service->ns2 ?? 'N/A' }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        @lang('Assigned IPs')
                                        <strong>
                                            @php echo nl2br($service->assigned_ips); @endphp 
                                        </strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @if(!$product->server_group_id && $status == 1)
        <div class="modal fade" id="cancenRequest" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Briefly Describe your reason for Cancellation')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('user.service.cancel.request') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $service->id }}">
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12 form-group">
                                    <label for="cancellation_type">@lang('Cancellation Type')</label>
                                    <select name="cancellation_type" class="form-control form--control h-45 form-select" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach($cancelRequestTypes as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="reason">@lang('Reason')</label>
                                    <textarea name="reason" id="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn--base btn--sm">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
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
        .progress-bg{
            background: #c5cace;
        }
        .custom--progress {
            position: relative;
        }
        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            line-height: 0;
            font-size: .75rem;
        }
    </style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            var hostingLoginUrl = @json(session()->get('hostingLoginUrl'));

            if(hostingLoginUrl){
                document.querySelector('.hostingLogin').click();
            }

            $('.cancenRequest').on('click', function(){
                var modal = $('#cancenRequest');
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush 