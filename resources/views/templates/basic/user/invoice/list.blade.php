@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
        <table class="table table--responsive--md">
            <thead>
                <tr>
                    <th>@lang('Invoice')</th>
                    <th>@lang('Invoice Date')</th>
                    <th>@lang('Total Amount')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr> 
            </thead>
            <tbody>
                @forelse($invoices as $invoice) 
                    <tr>
                        <td> 
                            <a href="{{ route('user.invoice.view', $invoice->id) }}" class="fw-bold">
                                {{ $invoice->getInvoiceNumber }}
                            </a>
                        </td>
                        <td>
                            {{ showDateTime(@$invoice->created, 'd/m/Y') }}
                        </td>
                        <td>
                            {{showAmount($invoice->amount)}}
                        </td>
                        <td>
                            @php echo $invoice->showStatus; @endphp
                        </td>
                        <td>
                            <a href="{{ route('user.invoice.view', $invoice->id) }}" class="badge badge--icon badge--fill-base" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('View')">
                                <i class="fas fa-desktop"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <x-empty-message table={{ true }} />
                @endforelse
            </tbody>
        </table>
        @if($invoices->hasPages())
            <div class="mt-5">
                {{ paginateLinks($invoices) }}
            </div>
        @endif
    </div>
</div>
@endsection


