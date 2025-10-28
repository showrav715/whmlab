@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light">
    <div class="invoice-container">

        @include('partials.invoice')

        <div class="container mt-3">
            <div class="row gy-4">
                @if($invoice->status == 2)
                    <form method="post" action="{{ route('user.invoice.payment') }}" class="paymentForm">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                        <input type="hidden" name="method_code">
                        <input type="hidden" name="currency">
                        <div class="row gy-4">
                            <div class="col-6 select2-parent">
                                <select name="payment" class="custom-select gateway form-group form-select form--control h-45 select2-basic">
                                    <option value="">@lang('Payment Methods')</option>
                                        @if(gs('deposit_module'))
                                            <option value="wallet">@lang('Wallet Balance') {{ showAmount($user->balance) }}</option>
                                        @endif
                                        @foreach ($gatewayCurrency as $data)
                                            <option value="{{ $data->method_code }}" data-gateway="{{ $data }}">{{ $data->name }}</option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn--base payBtn hide w-100"
                                    disabled>@lang('Pay Now')</button>
                            </div>
                        </div>

                    </form> 
                @endif
                <div class="col-6">
                    <a href="{{ route('user.invoice.download', ['id' => $invoice]) }}" class="btn btn--base w-100">
                        <i class="las la-download"></i> @lang('Download') 
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{ route('user.invoice.download', ['id' => $invoice, 'view' => 'preview']) }}" target="_blank"
                        class="btn btn--dark w-100"><i class="las la-eye"></i> @lang('Preview')
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
    <style>
        .select2-container--default .select2-search--dropdown .select2-search__field{
            color: hsl(var(--dark)) !important;
        }
        .card {
            border: none;
        }
        .card .table thead tr {
            border: 1px solid hsl(var(--dark));
        }
        .table thead tr {
            background: none;
        }
        .btn--dark:hover {
            background: hsl(var(--dark)) !important;
            color: hsl(var(--white)) !important;
        }
        :disabled {
            cursor: no-drop;
        }
        .invoice-container {
            margin: 15px auto;
            padding: 70px;
            max-width: 850px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .invoice-container td.total-row {
            background-color: #f8f8f8;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .invoice-container .invoice-status {
            margin: 20px 0 0 0;
            text-transform: uppercase;
            font-size: 24px;
            font-weight: 700;
        }
        @media (max-width: 767px) {
            .invoice-container {
            padding: 20px;
        }
        @media (max-width: 575px) {
            .invoice-container {
            padding: 10px;
        }
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.gateway').on('change', function() {
                var gateway = $(this).val();

                var resource = $('select[name=payment] option:selected').data('gateway');

                if (gateway == 'wallet') {
                    $('.payBtn').prop('disabled', false);
                } else if (gateway && gateway != 'wallet') {
                    $('input[name=currency]').val(resource.currency);
                    $('input[name=method_code]').val(resource.method_code);
                    $('.payBtn').prop('disabled', false);
                } else {
                    $('.payBtn').prop('disabled', true);
                }
            });
        })(jQuery);
    </script>
@endpush
