@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
        <table class="table table--responsive--md">
            <thead>
                <tr>
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
                            {{ __(@$service->product->serviceCategory->name) }}
                        </td>
                        <td>
                            {{ showAmount($service->recurring_amount) }}
                            {{ billingCycle(@$service->billing_cycle, true)['showText'] }}
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
                            <a href="{{ route('user.service.details', $service->id) }}" class="badge badge--icon badge--fill-base" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('View')">
                                <i class="fas fa-desktop"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <x-empty-message table={{ true }} />
                @endforelse
            </tbody>
        </table>
        @if($services->hasPages())
            <div class="mt-5">
                {{ paginateLinks($services) }}
            </div>
        @endif
    </div>
</div>
@endsection


