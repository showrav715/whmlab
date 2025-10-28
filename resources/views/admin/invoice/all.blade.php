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
                                <th>@lang('Date')</th>
                                <th>@lang('Total') | @lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody> 
                            @forelse(@$invoices as $invoice)
                                <tr>
                                    <td>
                                        <span class="fw-bold">
                                            @permit('admin.invoices.details')
                                                <a href="{{ route('admin.invoices.details', @$invoice->id) }}">
                                                    {{@$invoice->getInvoiceNumber}}
                                                </a> 
                                            @else
                                                <a href="javascript:void(0)">
                                                    {{@$invoice->getInvoiceNumber}}
                                                </a> 
                                            @endpermit
                                        </span>
                                    </td> 

                                    <td>
                                         <span class="fw-bold">{{@$invoice->user->fullname}}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', @$invoice->user->id) : 'javascript:void(0)' }}">
                                                <span>@</span>{{ @$invoice->user->username }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>
                                        {{ showDateTime(@$invoice->created_at) }} <br> {{ diffForHumans(@$invoice->created_at) }}
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
                                            <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.invoices.details', @$invoice->id) }}">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
                                        @else 
                                            <button class="btn btn-sm btn-outline--primary" disabled>
                                                <i class="la la-desktop"></i> @lang('Details')
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
                @if ($invoices->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($invoices) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
@if(@$hosting)
    @permit('admin.order.hosting.details')
        <a href="{{ route('admin.order.hosting.details', @$hosting->id) }}" class="btn btn-sm btn-outline--primary me-1">
            <i class="la la-undo"></i> @lang('Go to Service')
        </a>
    @endpermit
@endif
@if(@$domain)
    @permit('admin.order.domain.details')
        <a href="{{ route('admin.order.domain.details', @$domain->id) }}" class="btn btn-sm btn-outline--primary me-1">
            <i class="la la-undo"></i> @lang('Go to Domain')
        </a>
    @endpermit
@endif

@if(!@$hosting && !@$domain && !@$user)
<x-search-form dateSearch='yes' placeholder='Username' />
@endif
@endpush
