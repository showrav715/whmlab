@extends('admin.layouts.app')
@section('panel')
    <form action="{{ route('admin.invoice.update') }}" method="post">
        @csrf

        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

        <div class="row mb-none-30">
            <div class="col-xl-6 col-md-6 mb-30">
                <div class="card overflow-hidden box--shadow1">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item justify-content-between d-flex flex-wrap">
                                <label>@lang('Invoice Status')</label>
                                <div>@php echo $invoice->showStatus; @endphp</div>
                            </li>
                            <li class="list-group-item justify-content-between d-flex flex-wrap">
                                <label>@lang('Total Amount')</label>
                                <div><span class="totalAmount">{{ showAmount(@$invoice->amount) }}</span></div>
                            </li>
                            <li class="list-group-item justify-content-between d-flex flex-wrap">
                                <label>@lang('Go to View')</label>
                                <div>@php echo $invoice->viewDetails(); @endphp</div>
                            </li>
                            <li class="list-group-item justify-content-between d-flex flex-wrap">
                                <label>@lang('User')</label>
                                <div>
                                    <a
                                        href="{{ permit('admin.users.detail') ? route('admin.users.detail', @$invoice->user_id) : 'javascript:void(0)' }}">
                                        {{ @$invoice->user->fullname }}
                                    </a>
                                </div>
                            </li>
                            @if ($invoice->payments->count())
                                <li class="list-group-item justify-content-between d-flex flex-wrap">
                                    <label>@lang('Transactions')</label>
                                    <div>
                                        @permit('admin.invoices.payment.transactions')
                                            <a href="{{ route('admin.invoices.payment.transactions', @$invoice->id) }}">@lang('View All')
                                                ({{ $invoice->payments->count() }})</a>
                                        @else
                                            <a href="javascript:void(0)">@lang('View All')
                                                ({{ $invoice->payments->count() }})</a>
                                        @endpermit
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-md-6 mb-30">
                <div class="card overflow-hidden box--shadow1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Invoice Date')</label>
                                    <input 
                                        type="text" 
                                        class="datePicker form-control flex-grow-1" 
                                        value="{{ showDateTime($invoice->created, 'd-m-Y') }}"
                                        name="created" 
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Due Date')</label>
                                    <input 
                                        type="text"
                                        class="datePicker form-control"
                                        value="{{ showDateTime(@$invoice->due_date, 'd-m-Y') }}" 
                                        name="due_date" 
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Paid Date')</label>
                                    <input 
                                        type="text" 
                                        class="datePicker form-control"
                                        value="@if(@$invoice->paid_date) {{ showDateTime(@$invoice->paid_date, 'd-m-Y') }} @endif" 
                                        name="paid_date"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Stauts')</label>
                                    <select name="status" class="server_id form-control"
                                        {{ $invoice->status == 5 ? 'disabled' : null }}>
                                        @foreach ($invoice::status() as $index => $status)
                                            <option value="{{ $index }}"
                                                {{ $invoice->status == $index ? 'selected' : null }}
                                                {{ $index == 5 ? 'disabled' : null }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Admin Notes')</label>
                                    <textarea name="admin_notes" class="form-control" rows="3">@php echo nl22br($invoice->admin_notes); @endphp</textarea>
                                </div>
                            </div>
                            @permit('admin.invoice.update')
                                <div class="col-md-12 mt-3">
                                    <div class="form-group ">
                                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                                    </div>
                                </div>
                            @endpermit
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $items = @$invoice->items;
        @endphp


        <div class="row mt-4">

            @if ($invoice->status != 5)
                <div class="col-md-12 text-end mb-4">
                    <button class="btn btn-sm btn-outline--info addNewItem" type="button">
                        <i class="las la-plus"></i>@lang('Add Item')
                    </button>
                </div>
            @endif

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive--md  table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Description')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody class="invoiceTable">
                                    @forelse($items as $item)
                                        <tr>
                                            <td>
                                                <textarea name="items[{{ $item->id }}][description]" cols="80" class="form-control" required>@php echo nl22br($item->description); @endphp</textarea>
                                            </td>
                                            <td>
                                                <div class="row justify-content-center">
                                                    <div class="col-xl-10">
                                                        <div class="input-group">
                                                            <input type="number" step="any"
                                                                class="form-control invoiceItem"
                                                                name="items[{{ $item->id }}][amount]"
                                                                value="{{ getAmount(@$item->amount) }}" required>
                                                            <span class="input-group-text">{{ gs('cur_text') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @permit('admin.invoice.item.delete')
                                                    <button class="btn btn-sm btn-outline--danger delete"
                                                        data-id="{{ $item->id }}" type="button">
                                                        <i class="la la-trash"></i>@lang('Delete')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger" disabled type="button">
                                                        <i class="la la-trash"></i>@lang('Delete')
                                                    </button>
                                                @endpermit
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">@lang('No invoice item found')</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>

                            <div class="total-amount-wrapper">
                                <div class="row justify-content-end p-0 m-0">
                                    <div class="col-xl-5 col-lg-6 p-0 m-0">
                                        <div class="total-amount px-4 pb-3">
                                            <h5 class="total-amount__text">@lang('Total')</h5>
                                            <span class="total-amount__number fw-bold">
                                                <span>{{ gs('cur_sym') }}</span><span
                                                    class="totalAmount">{{ getAmount($invoice->amount, 2) }}</span>
                                                <span>{{ gs('cur_text') }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @permit('admin.invoice.update')
                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn--primary h-45 w-100"
                        {{ $invoice->status == 5 ? 'disabled' : null }}>@lang('Submit')</button>
                </div>
            @endpermit
        </div>
    </form>

    <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">@lang('Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="form-horizontal" method="post" action="{{ route('admin.invoice.item.delete') }}">
                    @csrf
                    <input type="hidden" name="id" required>
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <div class="modal-body">
                        <p class="question">@lang('Are you sure to delete this item')?</p>
                    </div>
                    @permit('admin.invoice.item.delete')
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        </div>
                    @endpermit
                </form>
            </div>
        </div>
    </div>

    <div id="refundModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">@lang('Are you sure to refund')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="form-horizontal" method="post" action="{{ route('admin.invoice.refund') }}">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="amount">@lang('Amount')</label>
                                <label><code>(@lang('Leave blank for full refund'))</code></label>
                                <input type="number" step="any" class="form-control" name="amount" id="amount"
                                    placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    @permit('admin.invoice.refund')
                        <div class="modal-footer">
                            <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        </div>
                    @endpermit
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="invoice-details-btns justify-content-end d-flex flex-wrap gap-2">
        @if ($invoice->status == 1)
            <button class="btn btn-sm btn-outline--warning refundModal buttonResponsive" type="button">
                <i class="la la-hand-holding-usd"></i> @lang('Refund')
            </button>
        @endif
        @permit('admin.invoice.download')
            <a href="{{ route('admin.invoice.download', ['id' => $invoice->id, 'view' => 'preview']) }}"
                class="btn btn-sm btn-outline--secondary buttonResponsive" target="_blank">
                <i class="la la-eye"></i>@lang('Preview')
            </a>
            <a href="{{ route('admin.invoice.download', $invoice->id) }}"
                class="btn btn-sm btn-outline--success buttonResponsive">
                <i class="la la-download"></i>@lang('Download')
            </a>
        @endpermit
        <a href="{{ @$invoice->viewDetails(true) }}" class="btn btn-sm btn-outline--primary buttonResponsive">
            <i class="las la-desktop"></i>@lang('Details')
        </a>
    </div>
@endpush

@push('style')
    <style>
        table textarea {
            min-height: auto !important;
        }

        @media(max-width:400px) {
            .buttonResponsive {
                padding: 5px 6px;
            }
        }

        @media (max-width: 991px) {
            .tr-class {
                display: none;
            }
        }

        .total-amount {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .total-amount__text {
            flex-basis: 38%;
            text-align: right;
        }

        @media (max-width: 991px) {
            .total-amount__text {
                text-align: left;
                flex-basis: 50%;
            }

            .total-amount {
                padding: 20px 16px !important;
            }
        }
    </style>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // ========================================= daterangepicker =========================================
            // ========================================= daterangepicker =========================================
            $('.datePicker').daterangepicker({
                autoApply: true,
                autoUpdateInput: false,
                singleDatePicker: true,
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });

            $('.datePicker').on('apply.daterangepicker', (event, picker) => {
                $(event.target).val(picker.startDate.format('DD-MM-YYYY')); 
            });
            // ========================================= daterangepicker =========================================
            // ========================================= daterangepicker =========================================
 
            $(document).on('input', '.invoiceItem', function() {
                totalAmount();
            });

            function totalAmount() {
                var items = $('.invoiceItem');
                var totalAmount = 0;

                $(items).each(function(index, data) {
                    totalAmount += parseFloat($(data).val());
                });

                $('.totalAmount').text(totalAmount.toFixed(2));
            }

            $('table textarea').each(function() {
                this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
            }).on('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            $('.addNewItem').on('click', function() {

                var getFakeId = fakeId(4);
                var html = `
                <tr> 
                    <td>
                        <textarea name="items[${getFakeId}][description]" cols="80" class="form-control" required></textarea>
                    </td>
                    <td>
                        <div class="row justify-content-center">
                            <div class="col-xl-10">
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control invoiceItem" name="items[${getFakeId}][amount]" required>
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline--danger removeItem" type="button">
                            <i class="la la-trash"></i> @lang('Delete')
                        </button>
                    </td>
                </tr>`;

                $('.invoiceTable').append(html);
            });

            $(document).on('click', '.removeItem', function() {
                $(this).closest('tr').remove();
                totalAmount();
            })

            $('.delete').on('click', function() {
                var modal = $('#deleteModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.refundModal').on('click', function() {
                var modal = $('#refundModal');
                modal.modal('show');
            });

            function fakeId(length) {
                var result = '';
                var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                var charactersLength = characters.length;
                for (var i = 0; i < length; i++) {
                    result += characters.charAt(Math.floor(Math.random() *
                        charactersLength));
                }

                var date = new Date();
                var seconds = date.getSeconds();
                return result + seconds;
            }

        })(jQuery);
    </script>
@endpush
