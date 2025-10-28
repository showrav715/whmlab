@extends('admin.layouts.app')

@section('panel')
    <form action="{{ route('admin.order.hosting.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ @$hosting->id }}">
        <div class="row mb-none-30 mb-1">

            @if (session()->has('response'))
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            @php echo @session()->get('response')->metadata->output->raw; @endphp
                        </div>
                    </div>
                </div>
            @endif

            @if ($product->server_group_id)
                <div class="col-lg-12">
                    <div class="row mb-none-30 mb-3">
                        <div class="col-md-12 form-group">
                            <h6 class="text-center mb-3">@lang('Module Commands')</h6>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="1" data-type="1" type="button">
                                                <i class="las la-plus"></i>@lang('Create')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="2" data-type="2" type="button">
                                                <i class="las la-ban"></i>@lang('Suspend')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="3" data-type="3" type="button">
                                                <i class="las la-undo"></i>@lang('Unsuspend')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 col-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="4" data-type="4" type="button">
                                                <i class="las la-trash"></i>@lang('Terminate')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="5" data-type="5" type="button">
                                                <i class="las la-exchange-alt"></i>@lang('Change Package')
                                            </button>
                                        </div>
                                        <div class="col-lg-4 col-xxl-2 col-md-4 col-sm-6 form-group">
                                            <button class="btn btn-sm btn-outline--primary moduleModal w-100"
                                                data-module="6" data-type="6" type="button">
                                                <i class="las la-key"></i>@lang('Change Password')
                                            </button>
                                        </div>
                                        @if ($hosting->suspend_reason)
                                            <div class="col-md-12 mt-3">
                                                <div class="alert alert-warning p-3 d-block" role="alert">
                                                    <div class="border-bottom pb-2 text-center">
                                                        <h6 class="alert-heading d-xl-inline">@lang('Account Suspended')</h6>
                                                        <small>({{ showDateTime($hosting->suspend_date) }})</small>
                                                    </div>
                                                    <p class="pt-2">{{ $hosting->suspend_reason }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div
                class="col-xl-{{ $product->server_group_id ? '8' : '12' }} col-md-{{ $product->server_group_id ? '8' : '12' }} mb-30">
                <div class="card overflow-hidden box--shadow1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Registration Date')</label>
                                    <input type="text" class="datePicker form-control reg_time flex-grow-1"
                                        data-language='en' data-position='bottom left'
                                        value="{{ showDateTime($hosting->reg_date, 'd-m-Y') }}" name="reg_date"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Next Due Date')</label>
                                    <input type="text" class="datePicker form-control" data-language='en'
                                        data-position='bottom left'
                                        value="{{ showDateTime(@$hosting->next_due_date, 'd-m-Y') }}" name="next_due_date"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Next Invoice Date')</label>
                                    <input type="text" class="datePicker form-control" data-language='en'
                                        data-position='bottom left'
                                        value="{{ showDateTime(@$hosting->next_invoice_date, 'd-m-Y') }}"
                                        name="next_invoice_date" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Termination Date')</label>
                                    <input type="text" class="datePicker form-control" data-language='en'
                                        data-position='bottom left'
                                        value="{{ showDateTime(@$hosting->termination_date, 'd-m-Y') }}"
                                        name="termination_date" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('First Payment Amount')</label>
                                    <div class="input-group">
                                        <input type="text" name="first_payment_amount"
                                            value="{{ getAmount(@$hosting->first_payment_amount) }}"
                                            class="form-control">
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Recurring Amount')</label>
                                    <div class="input-group">
                                        <input type="text" name="recurring_amount"
                                            value="{{ getAmount(@$hosting->recurring_amount) }}" class="form-control">
                                        <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Product/Service')</label>
                                    <select name="change_product_id" class="change_product_id form-control">
                                        @php echo $productDropdown; @endphp
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Server')</label>
                                    <select name="server_id" class="server_id form-control">
                                        @if (@$product->serverGroup)
                                            <option value="">@lang('Select One')</option>
                                            @foreach (@$product->serverGroup->servers as $index => $server)
                                                <option value="{{ $server->id }}"
                                                    {{ $server->id == $hosting->server_id ? 'selected' : null }}>
                                                    {{ $server->hostname }} - {{ $server->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="">@lang('N/A')</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>
                                        @if ($product->product_type == 3)
                                            @lang('Hostname')
                                        @else
                                            @lang('Domain')
                                        @endif
                                    </label>
                                    <input class="form-control" type="text" name="domain"
                                        value="{{ @$hosting->domain }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label> @lang('Dedicated IP')</label>
                                    <input class="form-control" type="text" name="dedicated_ip"
                                        value="{{ @$hosting->dedicated_ip }}">
                                </div>
                            </div>


                            @if ($product->product_type == 3)
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="form-group ">
                                        <label> @lang('Assigned IPs')</label>
                                        <textarea name="assigned_ips" class="form-control" rows="2">{{ @$hosting->assigned_ips }}</textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('Nameserver 1') </label>
                                        <input class="form-control" type="text" name="ns1"
                                            value="{{ @$hosting->ns1 }}">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <div class="form-group ">
                                        <label>@lang('Nameserver 2') </label>
                                        <input class="form-control" type="text" name="ns2"
                                            value="{{ @$hosting->ns2 }}">
                                    </div>
                                </div>
                            @endif

                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Username') </label>
                                    <input class="form-control" type="text" name="username"
                                        value="{{ @$hosting->username }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <div class="justify-content-between d-flex flex-wrap">
                                        <label>@lang('Password')</label>
                                        <a href="javascript:void(0)" class="generatePassword">@lang('Generate Strong Password')</a>
                                    </div>
                                    <input class="form-control" type="text" name="password"
                                        value="{{ @$hosting->password }}" id="password" required>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Status') </label>
                                    <select name="status" class="form-control">
                                        @foreach (@$hosting::status() as $index => $data)
                                            <option value="{{ $index }}"
                                                {{ @$hosting->status == $index ? 'selected' : null }}>{{ $data }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="form-group ">
                                    <label>@lang('Billing Cycle') </label>
                                    <select name="billing_cycle" class="form-control">
                                        @foreach (billingCycle() as $index => $data)
                                            <option value="{{ $index }}"
                                                {{ $hosting->billing_cycle == $index ? 'selected' : null }}
                                                data-data='{{ $data['billing_cycle'] }}'>
                                                {{ __($data['showText']) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @foreach ($product->getConfigs as $index => $config)
                                @foreach ($config->group->activeOptions as $option)
                                    <div class="col-xl-4 col-lg-6 col-md-6">
                                        <div class="form-group ">
                                            <label>{{ __($option->name) }}</label>
                                            <select name="config_options[{{ $option->id }}]"
                                                class="form-control options">
                                                <option value="">@lang('Select One')</option>
                                                @forelse($option->activeSubOptions as $subOption)
                                                    <option value="{{ $subOption->id }}"
                                                        data-price='{{ $subOption->getOnlyPrice }}'
                                                        data-text='{{ $subOption->name }}'>
                                                        {{ __($subOption->name) }}
                                                    </option>
                                                @empty
                                                    <option value="">@lang('N/A')</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach

                            <div class="col-xl-4 col-lg-12 col-md-12">
                                <div class="form-group ">
                                    <label>@lang('Admin Notes') </label>
                                    <textarea name="admin_notes" class="form-control h-45" rows="2">@php echo nl22br($hosting->admin_notes); @endphp</textarea>
                                </div>
                            </div>

                            @php $cancelRequest = $hosting->cancelRequest; @endphp

                            @if ($cancelRequest)
                                <div class="col-xl-12 col-lg-12 col-md-12 border-top pt-3 ps-3 mt-3">
                                    <div class="form-group">
                                        <div class="justify-content-between d-flex flex-wrap">
                                            <label>
                                                <a
                                                    href="
                                                    {{ permit('admin.cancel.requests') ? route('admin.cancel.requests', ['id' => $cancelRequest->id]) : 'javascript:void(0)' }}">
                                                    @lang('Reason for Cancellation Request')</a>
                                                <small>({{ @$cancelRequest::type()[$cancelRequest->type] }})</small>
                                            </label>
                                            <div>
                                                <input type="checkbox" id="delete_cancel_request"
                                                    name="delete_cancel_request">
                                                <label>@lang('Delete Cancellation Request') </label>
                                            </div>
                                        </div>
                                        <p class="text--danger">@php echo nl22br($cancelRequest->reason); @endphp</p>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            @if ($product->server_group_id)
                <div class="col-xl-4 col-md-4 mb-30">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive--md  table-responsive metric-table">
                                <table class="table table--light style--two">
                                    <thead>
                                        <tr>
                                            <th>@lang('Metric')</th>
                                            <th>@lang('Info')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($accountSummary ?? [] as $key => $value)
                                            <tr>
                                                <td>
                                                    {{ $key }}
                                                </td>
                                                <td>
                                                    <span class="{{ $value ? 'fw-bold' : null }}">
                                                        {{ $value ?? 'N/A' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @permit('admin.order.hosting.update')
            <div class="row mb-none-30">
                <div class="col-lg-12 col-md-12 mb-30">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </div>
        @endpermit
    </form>

    {{-- Module Modal --}}
    <div id="moduleModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="createModalLabel">@lang('Confirm Module Command')</h6>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="form-horizontal" method="post" action="{{ route('admin.module.command') }}">
                    @csrf
                    <input type="hidden" name="hosting_id" value="{{ $hosting->id }}" required>
                    <input type="hidden" name="module_type" required>
                    <div class="modal-body">
                        <div class="form-group">
                            @lang('Are you sure to want run the') <span class="moduleName text--danger"></span> @lang('function')?

                            <div class="form-group mt-4 suspendArea">
                                <label class="form-control-label fw-bold">@lang('Reason')*</label>
                                <input type="text" class="form-control" name="suspend_reason" autocomplete="off"
                                    placeholder="@lang('Reason')">
                            </div>
                            <div class="form-group suspendArea">
                                <input type="checkbox" name="suspend_email" id="suspend"> <label
                                    for="suspend">@lang('Send Suspension Email')</label>
                            </div>

                            <div class="form-group mt-4 unSuspendArea">
                                <input type="checkbox" name="unSuspend_email" id="unSuspend"> <label
                                    for="unSuspend">@lang('Send Unsuspension Email')</label>
                            </div>

                        </div>
                    </div>
                    @permit('admin.module.command')
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
    <div class="d-flex justify-content-end flex-wrap gap-2 breadcrumb-button">
        @permit('admin.orders.details')
            <a href="{{ route('admin.orders.details', @$hosting->order_id) }}"
                class="btn btn-sm btn-outline--dark me-1 breadcrumb-button__one">
                <i class="la la-undo"></i> @lang('Go to Order')
            </a>
        @endpermit

        @permit('admin.invoices.hosting.all')
            <a href="{{ route('admin.invoices.hosting.all', $hosting->id) }}"
                class="btn btn-sm btn-outline--primary me-1 breadcrumb-button__two">
                <i class="las la-file-alt"></i> @lang('Invoices')
            </a>
        @endpermit

        @permit('admin.module.login.hosting')
            @if ($product->server_group_id)
                <form class="d-init breadcrumb-button__form" action="{{ route('admin.module.login.hosting') }}"
                    method="post">
                    @csrf
                    <input type="hidden" name="hosting_id" value="{{ $hosting->id }}" required>
                    <button 
                        type="submit" 
                        class="btn btn-sm btn-outline--info breadcrumb-button__three"
                        @disabled(!$hasAccount)
                    >
                        <i class="las la-sign-in-alt"></i>@lang('Login to ' . @$serverGroup->getType ?? 'Control Panel')
                    </button>
                </form>
                <a href="{{ session()->get('hostingLoginUrl') ?? '#' }}" class="hostingLogin" target="_blank"></a>
            @endif
        @endpermit
    </div>
@endpush

@push('style')
    <style>
        .metric-table {
            max-height: 580px;
        }

        .metric-table::-webkit-scrollbar {
            width: 8px;
        }

        .metric-table::-webkit-scrollbar-thumb {
            background-color: #8b8b8b;
            border-radius: 12px;
        }

        .metric-table table thead {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .metric-table table :is(td, th) {
            white-space: wrap !important;
        }

        .d-init {
            display: initial;
        }

        @media (max-width: 991px) {
            .table-responsive--md tbody tr:nth-child(odd) {
                background-color: #1208080d;
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

            $('.moduleModal').on('click', function() {
                var modal = $('#moduleModal');

                var moduleName = $(this).text();
                var moduleType = $(this).data('type');

                if (moduleType == 2) {
                    $('.suspendArea').removeClass('d-none');
                } else {
                    $('.suspendArea').addClass('d-none');
                }

                if (moduleType == 3) {
                    $('.unSuspendArea').removeClass('d-none');
                } else {
                    $('.unSuspendArea').addClass('d-none');
                }

                modal.find('.moduleName').text(moduleName);
                modal.find('input[name=module_type]').val(moduleType);

                modal.modal('show');
            });

            $('.generatePassword').on('click', function() {
                var password = generatePassword(15);
                $('#password').val(password);
            });

            function generatePassword(passwordLength) {
                var numberChars = "0123456789";
                var upperChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                var lowerChars = "abcdefghijklmnopqrstuvwxyz";
                var specialChars = "!$%";
                var allChars = numberChars + upperChars + lowerChars + specialChars;
                var randPasswordArray = Array(passwordLength);

                randPasswordArray[0] = numberChars;
                randPasswordArray[1] = upperChars;
                randPasswordArray[2] = lowerChars;
                randPasswordArray[3] = specialChars;
                randPasswordArray = randPasswordArray.fill(allChars, 4);

            return shuffleArray(randPasswordArray.map(function(x) {
                return x[Math.floor(Math.random() * x.length)]
            })).join('');
        }

        function shuffleArray(array) {
            for (var i = array.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
            return array;
        }

        $('.change_product_id').on('change', function() {
            var productId = $(this).val();
            var hostingId = @json($hosting->id);

            if (!productId) {
                return false;
            }

            @permit('admin.change.order.hosting.product')
            window.location.href = '{{ route('admin.change.order.hosting.product', ['', '']) }}/' +
                hostingId + '/' + productId;
            @endpermit
        });

        $('.change_product_id option[value=@json($product->id)]').prop('selected', true);

        var product = @json($product);
        var hosting = @json($hosting);

        $('select[name=billing_cycle]').on('change', function() {
            var value = $('select[name=billing_cycle] option:selected').data('data');

            if ($(this).val() == 0) {
                value = 'monthly';
            }

            showSelect(value, product, $(this).val());
        }).change();

        function showSelect(value, product, cycle = null) {
            try {

                var getColumn = value;
                var getFeeColumn = value + '_setup_fee';

                $('.options').each(function(index, data) {
                    var options = $(data).find('option');
                    var general = @json(gs());
                    var finalText = null;

                    options.each(function(iteration, dropdown) {
                        var dropdown = $(dropdown);
                        var dropdownOptions = null;
                        var optionSetupFee = '';

                        if (dropdown.data('price')) {
                            var priceForThisItem = dropdown.data('price');
                            var mainText = dropdown.data('text');

                            var display = cycle == 0 ? 'One Time' : pricing(0, null, getColumn,
                                cycle);

                            if (cycle == 0) {
                                getColumn = 'monthly'
                            }

                            if (priceForThisItem[getFeeColumn] > 0) {
                                optionSetupFee =
                                    ` + ${general.cur_sym}${getAmount(priceForThisItem[getFeeColumn])} ${general.cur_text} Setup Fee`
                            }

                            dropdownOptions =
                                `${general.cur_sym}${getAmount(priceForThisItem[getColumn])} ${general.cur_text} ${display} ${optionSetupFee}`;

                            finalText = mainText + ' ' + dropdownOptions;
                            dropdown.text(finalText);
                        }

                    });
                });

            } catch (message) {
                console.log(message);
            }
        }

        function pricing(price, type, column, cycle = null) {
            try {

                if (!price) {
                    column = column.replaceAll('_', ' ');

                    if (cycle == 0) {
                        column = 'One Time';
                    }

                    return column.replaceAll(/(?:^|\s)\S/g, function(word) {
                        return word.toUpperCase();
                    });
                }

                if (!type) {
                    var price = productPrice[column];
                    var fee = productPrice[column + '_setup_fee'];
                    var sum = (parseFloat(fee) + parseFloat(price));

                    return getAmount(sum);
                }

                var amount = 0;

                if (type == 'price') {
                    amount = productPrice[column];
                } else {
                    column = column + '_setup_fee';
                    amount = productPrice[column];
                }

                return getAmount(amount);

            } catch (message) {
                console.log(message);
            }
        }

        function getAmount(getAmount, length = 2) {
            var amount = parseFloat(getAmount).toFixed(length);
            return amount;
        }

        var hostingConfigs = @json(@$hosting->hostingConfigs);

        for (var i = 0; i < hostingConfigs.length; i++) {

            var selectName = hostingConfigs[i]['configurable_group_option_id'];
            var selectOption = hostingConfigs[i]['configurable_group_sub_option_id'];

            $(`select[name='config_options[${selectName}]'] option[value=${selectOption}]`).prop('selected', true);
            }

            var hostingLoginUrl = @json(session()->get('hostingLoginUrl'));

            if (hostingLoginUrl) {
                document.querySelector('.hostingLogin').click();
            }

        })(jQuery);
    </script>
@endpush
