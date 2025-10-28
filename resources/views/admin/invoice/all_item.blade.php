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
                                <th>@lang('Invoice')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Invoice Date')</th>
                                <th>@lang('Total') | @lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead> 
                            <tbody>
                            @forelse(@$invoicesItems as $invoicesItem)

                            @php $invoice = $invoicesItem->invoice; @endphp

                                <tr>
                                    <td>
                                        <span class="fw-bold">
                                            @permit('admin.invoices.details')
                                                <a href="{{ route('admin.invoices.details', @$invoicesItem->invoice_id) }}">
                                                    #{{@$invoice->id}}
                                                </a>
                                            @else 
                                                <a href="javascript:void(0)">
                                                    #{{@$invoice->id}}
                                                </a>
                                            @endpermit
                                        </span> 
                                    </td> 

                                    <td>
                                         <span class="fw-bold">{{@$invoicesItem->user->fullname}}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', @$invoicesItem->user->id) : 'javascript:void(0)' }}">
                                                <span>@</span>{{ @$invoicesItem->user->username }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>
                                        {{ showDateTime(@$invoice->created) }} <br> {{ diffForHumans(@$invoice->created) }}
                                    </td> 

                                    <td>
                                        <span class="fw-bold"> 
                                            {{ showAmount(@$invoice->amount) }}</a>
                                        </span>
                                        <br>
                                        @php echo $invoice->showStatus; @endphp
                                    </td> 
                                
                                    <td>
                                        @permit('admin.invoices.details')
                                            <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.invoices.details', @$invoicesItem->invoice_id) }}">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
                                        @else 
                                            <a class="btn btn-sm btn-outline--primary" href="javascript:void(0)">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
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
                @if ($invoicesItems->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($invoicesItems) }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('breadcrumb-plugins')
@if(@$hosting)
    @permit('admin.order.hosting.details')
        <a href="{{ route('admin.order.hosting.details', @$hosting->id) }}" class="btn btn-sm btn-outline--dark me-1">
            <i class="la la-undo"></i> @lang('Go to Service')
        </a>
    @endpermit
@endif
@if(@$domain)
    @permit('admin.order.domain.details')
        <a href="{{ route('admin.order.domain.details', @$domain->id) }}" class="btn btn-sm btn-outline--dark me-1">
            <i class="la la-undo"></i> @lang('Go to Domain')
        </a>
    @endpermit
@endif
@endpush