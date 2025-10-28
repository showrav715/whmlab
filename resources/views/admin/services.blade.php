@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('User')</th>
                            <th>@lang('Service/Product')</th>
                            <th>@lang('Pricing')</th> 
                            <th>@lang('Next Due Date')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr> 
                        </thead> 
                        <tbody>  
                            @forelse($services as $service)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{$service->user->fullname}}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', $service->user_id) : 'javascript:void(0)' }}">
                                                <span>@</span>{{ $service->user->username }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ __(@$service->product->name) }}</span>
                                        <br>
                                        <span class="small">
                                            {{ __(@$service->product->serviceCategory->name) }}
                                        </span>
                                    </td>  
                                    <td>
                                        <span class="fw-bold">
                                            {{ gs('cur_sym') }}{{ getAmount($service->recurring_amount) }} {{ __(gs('cur_text')) }}
                                            {{ @billingCycle(@$service->billing_cycle, true)['showText'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($service->billing_cycle != 0)
                                            {{ showDateTime($service->next_due_date, 'd/m/Y') }}
                                        @else 
                                            @lang('N/A')
                                        @endif 
                                    </td>
                                    <td>
                                        @php echo $service->showStatus; @endphp
                                    </td> 
                                    <td>
                                        @permit('admin.order.hosting.details')
                                            <a href="{{ route('admin.order.hosting.details', $service->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop text--shadow"></i> @lang('Details')
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline--primary" disabled>
                                                <i class="las la-desktop text--shadow"></i> @lang('Details')
                                            </button>
                                        @endpermit
                                    </td>
                                </tr>
                            @empty  
                                <tr> 
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($services->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($services) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@if(request()->routeIs('admin.services'))
    @push('breadcrumb-plugins')
        <x-search-form placeholder="Username / Email" />
    @endpush
@endif