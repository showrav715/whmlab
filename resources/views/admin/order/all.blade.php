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
                                <th>@lang('Date')</th>
                                <th>@lang('Username')</th>
                                <th>@lang('Total')</th>
                                <th>@lang('After Discount')</th>
                                <th>@lang('Payment Status')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>  
                            </tr>
                            </thead>
                            <tbody> 
                            @forelse(@$orders as $order)
                                <tr>
                                    <td>
                                        @permit('admin.invoices.details')
                                            <a href="{{ route('admin.invoices.details', @$order->invoice->id ?? 0) }}">{{@$order->invoice->getInvoiceNumber}}</a>
                                        @else 
                                            <a href="javascript:void(0)">{{@$order->invoice->getInvoiceNumber}}</a>
                                        @endpermit
                                    </td>

                                    <td>
                                        {{ showDateTime(@$order->created_at) }} <br> {{ diffForHumans($order->created_at) }}
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{@$order->user->fullname}}</span>
                                        <br>
                                        @if(@$order->user->id)
                                            <span class="small">
                                                <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', @$order->user->id) : 'javascript:void(0)' }}">
                                                    <span>@</span>{{ @$order->user->username }}
                                                </a>
                                            </span>
                                        @endif
                                    </td>  
                              
                                    <td>
                                        {{ showAmount(@$order->amount) }}
                                    </td>

                                    <td>
                                        <span class="fw-bold">{{ showAmount(@$order->after_discount) }}</span>
                                    </td>
                                        
                                    <td>
                                        @php echo @$order->invoice->showStatus; @endphp
                                    </td>

                                    <td>
                                        @php echo @$order->showStatus; @endphp
                                      </td>

                                      <td>
                                        @permit('admin.orders.details')
                                            <a href="{{ route('admin.orders.details', @$order->id) }}" class="btn btn-sm btn-outline--primary">
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
                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@if(!@$user)
    @push('breadcrumb-plugins')
        <x-search-form dateSearch='yes' placeholder='Username' />
    @endpush
@endif
