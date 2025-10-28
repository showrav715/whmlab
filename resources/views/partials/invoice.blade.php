<style>
    @page {
        size: 8.27in 11.7in;
        margin: .5in;
    }

    body {
        font-family: "Maven Pro", sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: #023047;
    }

    /* Typography */
    .strong {
        font-weight: 700;
    }

    .fw-md {
        font-weight: 500;
    }

    .text-base {
        color: #{{ gs('base_color') }};
    }

    .bg-base {
        background: #{{ gs('base_color') }};
    }

    h1,
    .h1 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 67px;
        line-height: 1.2;
        font-weight: 500;
    }

    h2,
    .h2 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 50px;
        line-height: 1.2;
        font-weight: 500;
    }

    h3,
    .h3 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 38px;
        line-height: 1.2;
        font-weight: 500;
    }

    h4,
    .h4 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 500;
    }

    h5,
    .h5 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 500;
    }

    h6,
    .h6 {
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 16px;
        line-height: 1.2;
        font-weight: 500;
    }

    .text-uppercase {
        text-transform: uppercase;
    }

    .text-end {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    /* List Style */
    ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    /* Utilities */
    .d-block {
        display: block;
    }

    .mt-0 {
        margin-top: 0;
    }

    .m-0 {
        margin: 0;
    }

    .mt-3 {
        margin-top: 16px;
    }

    .mt-4 {
        margin-top: 24px;
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    /* Title */
    .title {
        display: inline-block;
        letter-spacing: 0.05em;
    }

    /* Table Style */
    table {
        width: 7.27in;
        caption-side: bottom;
        border-collapse: collapse;
        border: 1px solid #eafbff;
        color: #023047;
        vertical-align: top;
    }

    table td {
        padding: 5px 15px;
    }

    table th {
        padding: 5px 15px;
    }

    table th:last-child {
        text-align: right !important;
    }

    .table> :not(caption)>*>* {
        padding: 12px 24px;
        background-color: #023047;
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px #023047;
    }

    .table>tbody {
        vertical-align: inherit;
        border: 1px solid #eafbff;
    }

    .table>thead {
        vertical-align: bottom;
        background: #219ebc;
        color: white;
    }

    .table>thead th {
        text-align: left;
        font-size: 16px;
        letter-spacing: 0.03em;
        font-weight: 500;
    }

    .table td:last-child {
        text-align: right;
    }

    .table th:last-child {
        text-align: right;
    }

    .table> :not(:first-child) {
        border-top: 0;
    }

    .table-sm> :not(caption)>*>* {
        padding: 5px;
    }

    .table-bordered> :not(caption)>* {
        border-width: 1px 0;
    }

    .table-bordered> :not(caption)>*>* {
        border-width: 0 1px;
    }

    .table-borderless> :not(caption)>*>* {
        border-bottom-width: 0;
    }

    .table-borderless> :not(:first-child) {
        border-top-width: 0;
    }

    .table-striped>tbody>tr:nth-of-type(even)>* {
        background: #eafbff;
    }

    .mt-30 {
        margin-top: 30px;
    }

    .text-danger {
        color: red;
    }

    .text-success {
        color: green;
    }

    /* Logo */

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .info {
        display: flex;
        justify-content: space-between;
        padding-top: 15px;
        padding-bottom: 15px;
        border-top: 1px solid #023047;
        border-bottom: 1px solid #023047;
    }

    .address {
        padding-top: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #023047;
    }

    header {
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .body {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    footer {
        padding-bottom: 15px;
    }

    .badge {
        display: inline-block;
        padding: 3px 15px;
        font-size: 10px;
        line-height: 1;
        border-radius: 15px;
    }

    .badge--success {
        color: white;
        background: #02c39a;
    }

    .badge--warning {
        color: white;
        background: #ffb703;
    }

    .align-items-center {
        align-items: center;
    }

    .footer-link {
        text-decoration: none;
        color: #219ebc;
    }

    .footer-link:hover {
        text-decoration: none;
        color: #219ebc;
    }

    .list--row {
        overflow: auto
    }

    .list--row::after {
        content: '';
        display: block;
        clear: both;
    }

    .float-left {
        float: left;
    }

    .float-right {
        float: right;
    }

    .d-block {
        display: block;
    }

    .d-inline-block {
        display: inline-block;
    }

    .table tbody tr td {
        font-family: ui-monospace;
    }

    /* //////////////////////////// */

    .table {
        border-color: #dee2e670;
    }

    .table>thead {
        vertical-align: bottom;
        background-color: hsl(var(--base)) !important;
        color: white;
    }

    .table> :not(caption)>*>* {
        background-color: transparent !important;
        box-shadow: none !important;
    }

    .table tbody tr td {
        border-width: 0;
        font-family: ui-monospace;
    }

    .table thead tr th {
        padding: 10px 15px;
    }

    .border--top {
        border-top: 1px solid #dee2e670;
    }

    .table tbody tr td {
        padding: 12px 15px;
    }

    .text-center {
        align-items: center !important;
    }

    .logo img {
        width: 165px;
        height: 35px;
    }

    @media (max-width: 575px) {
        .logo img {
            height: 30px;
        }
    }

    .badge--danger {
        background-color: rgba(234, 84, 85, 0.1);
        border: 1px solid #ea5455;
        color: #ea5455;
    }

    .badge--success {
        background-color: rgba(40, 199, 111, 0.1);
        border: 1px solid #28c76f;
        color: #28c76f;
    }

    .badge--warning {
        background-color: rgba(255, 159, 67, 0.1);
        border: 1px solid #ff9f43;
        color: #ff9f43;
    }

    .badge--dark {
        background-color: rgba(0, 0, 0, 0.1);
        border: 1px solid #000000;
        color: #000000;
    }

    .bg--dark {
        background-color: #081f30 !important;
        color: #fff;
        font-weight: 700;
    }

    tr.even {
        background-color: #{{ gs('base_color') }}08 !important;
    }

    .table tbody tr:nth-child(even) {
        background: unset;
    }
    td:nth-child(2) {
        max-width: 200px !important;
    }
    .body{
        overflow-x: auto;
    }
</style>

<header>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="list--row">
                    <div class="logo float-left">
                        <img src="{{ siteLogo('dark') }}" alt="image" class="logo-img" />
                    </div>
                    <div class="m-0 float-right text-end">
                        @php echo @$invoice->showStatus; @endphp
                        @if ($invoice->status == 2)
                            <div class="mt-2">
                                @lang('Due Date'): {{ showDateTime($invoice->due_date, 'd/m/Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="info list--row d-block">
                    <div class="info-left float-left">
                        <div class="list list--row">
                            <span class="strong">@lang('Date') :</span>
                            <span> {{ showDateTime($invoice->created, 'd/m/Y') }} </span>
                        </div>
                    </div>
                    <div class="info-right float-right">
                        <div class="list list--row">
                            <span class="strong">@lang('Invoice') :</span>
                            <span>{{ $invoice->getInvoiceNumber }}</span>
                        </div>
                    </div>
                </div>
                <div class="address list--row">
                    <div class="address-to float-left">
                        <span class="text-base d-block fw-md">@lang('Invoiced To')</span>
                        <h5 class="text-uppercase">{{ __(@$user->fullname) }}</h5>
                        <ul class="list" style="--gap: 0.3rem">
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span class="strong">@lang('Country') :</span>
                                    <span>{{ __(@$user->country_name) ?? __('N/A') }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span class="strong">@lang('State') :</span>
                                    <span>{{ __(@$user->state) ?? __('N/A') }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span class="strong">@lang('City') :</span>
                                    <span>{{ __(@$user->city) ?? __('N/A') }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span class="strong">@lang('Zip') :</span>
                                    <span>{{ __(@$user->zip) ?? __('N/A') }}</span>
                                </div>
                            </li>
                            <li>
                                <div class="list list--row" style="--gap: 0.5rem">
                                    <span class="strong">@lang('Mobile') :</span>
                                    <span>{{ __(@$user->mobile) ?? __('N/A') }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="address-form float-right">
                        <ul class="text-end">
                            <li>
                                <h5 class="primary-text d-block fw-md text-base">@lang('Pay To')</h5>
                            </li>
                            <li>
                                <span>{{ __(@$address->data_values->address) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="body">
                    <div class="text-center mt-4 mb-3">
                        <div class="title-inset">
                            <h5 class="title m-0 text-uppercase">@lang('Invoice Items')</h5>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr class="bg-base">
                                <th>@lang('SL')</th>
                                <th>@lang('Description')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sl = 0; @endphp
                            @foreach ($items as $groups)
                                @php $sl++; @endphp
                                @foreach ($groups as $allGroup)
                                    @foreach ($allGroup as $data)
                                        <tr data-group="id{{ $sl }}"
                                            class="@if ($sl % 2 == 0) even @endif border">
                                            <td>{{ $loop->first ? $sl : null }}</td>
                                            <td>
                                                @php echo nl2br($data->description); @endphp
                                            </td>
                                            <td>
                                                @if ($allGroup->count() > 1)
                                                    @if ($data->amount > 0)
                                                        {{ showAmount($data->amount) }}
                                                    @else
                                                        ({{ showAmount(abs($data->amount)) }})
                                                    @endif
                                                @endif
                                            </td>
                                            @if ($allGroup->count() <= 1)
                                                <td class="fw-bold">{{ showAmount($allGroup->sum('amount')) }}</td>
                                            @else
                                                <td></td>
                                            @endif
                                        </tr>
                                        @if ($loop->last && $allGroup->count() > 1)
                                            <tr class="@if ($sl % 2 == 0) even @endif border">
                                                <td colspan="4" class="fw-bold">
                                                    {{ showAmount($allGroup->sum('amount')) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endforeach
                            <tr class="border--top">
                                <td colspan="2" class="text-end">@lang('Total')</td>
                                <td colspan="2">
                                    <span class="h5 fw-bold">{{ showAmount($invoice->amount) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @php $payments = @$invoice->payments; @endphp

                    @if ($payments->count())
                        <table class="table table-striped mt-30">
                            <thead>
                                <tr class="bg-base">
                                    <td>@lang('Transaction Date')</td>
                                    <td>@lang('Gateway')</td>
                                    <td>@lang('Transaction ID')</td>
                                    <td>@lang('Charge')</td>
                                    <td>@lang('Amount')</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (@$payments as $payment)
                                    <tr>
                                        <td>{{ showDateTime(@$payment->created_at, 'd/m/Y') }}</td>
                                        <td>{{ __(@$payment->gateway->name) }}</td>
                                        <td>{{ @$payment->trx }}</td>
                                        <td>{{ showAmount(@$payment->charge) }}</td>
                                        <td>
                                            {{ showAmount(@$payment->amount + @$payment->charge) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>


