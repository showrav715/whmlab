@extends($activeTemplate . 'layouts.side_bar')

@section('data')
    <div class="col-lg-9">
        <div class="row gy-4">

            @if ($product->stock_control && $product->stock_quantity <= 0)
                <div class="col-md-12 text-center">
                    <div class="alert alert-warning" role="alert">
                        <span class="text-muted">@lang('Sorry, Out of Stock')</span>
                    </div>
                </div>
            @else
                @if ($product->domain_register && !@$cart)
                    <div class="col-md-10 domainArea">

                        <h3 class="mb-3">@lang('Choose a Domain')...</h3>
 
                        <div class="card">
                            <div class="card-header bg-dark-two">
                                <input type="radio" id="register_domain" class="domain-option" data-form="register_domain_form" checked>
                                <label for="register_domain" role="button">@lang('Register new domain')</label>
                            </div>
                            <div class="card-body">
                                <form action="" class="register_domain_form p-2 form">
                                    <div class="row g-2">
                                        <div class="col-lg-8 col-md-12">
                                            <div class="input-group">
                                                <span class="input-group-text">@lang('WWW.')</span>
                                                <input type="text" name="domain_name" required class="form-control form--control h-45 h-45 domain_name" required placeholder="@lang('example')">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-6">
                                            <select class="form-control form--control h-45 form-select h-45 extension" name="extension" required>
                                                @foreach ($domains as $singleDomain)
                                                    <option value="{{ $singleDomain->extension }}">{{ $singleDomain->extension }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-6">
                                            <button type="submit" class="btn btn--base text-white w-100 exclude">@lang('Check')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-header bg-dark-two">
                                <input type="radio" id="existing_domain" class="domain-option" data-form="domain_form">
                                <label for="existing_domain" class="d-inline" role="button">@lang('I will use my existing domain and update my nameservers')</label>
                            </div>
                            <div class="card-body d-none">
                                <form action="" class="domain_form p-2 form">
                                    <div class="row g-2">
                                        <div class="col-lg-8 col-md-12">
                                            <div class="input-group">
                                                <span class="input-group-text">@lang('WWW.')</span>
                                                <input type="text" name="domain_name" required class="form-control form--control h-45 h-45 domain_name" required placeholder="@lang('example')">
                                                <span class="input-group-text">@lang('.')</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-6">
                                            <input type="text" class="form-control form--control h-45 extension h-45" placeholder="@lang('com')" required name="extension">
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-6">
                                            <button type="submit" class="btn btn--base text-white w-100 exclude">@lang('Use')</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="text-center mt-4 availability"></div>
                        <div class="showAvailability"></div>

                    </div>
                @endif

                <div class="col-md-12 {{ $product->domain_register ? 'd-none hideElement' : null }}">
                    @if (!@$isUpdate)
                        <form action="{{ route('shopping.cart.add.service') }}" method="post">
                        @else
                            <form action="{{ route('shopping.cart.config.service.update') }}" method="post">
                                <input type="hidden" name="cart_id" value="{{ $cart->id }}">
                    @endif
                    @csrf
                    <input type="hidden" name="domain" class="domain">
                    <input type="hidden" name="domain_id" value="0" class="domain_id" required>
                    <input type="hidden" name="product_id" value="{{ $product->id }}" required>

                    <div class="row g-3">
                        <div class="col-lg-8">
                            <div class="col-md-12 form-group">
                                <h3>@lang('Product Configure')</h3>
                                <p class="mt-1">@lang('Configure your desired options and continue to checkout')</p>
                            </div>
                            <div class="row gy-3 mt-2">

                                @php $price = $product->price; @endphp
                                <div class="col-md-12 form-group">
                                    <div class="card">
                                        <div class="card-header bg-dark-two">
                                            <h5 class="text--white">{{ __($product->name) }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                <div class="fs-12">@php echo nl2br($product->description); @endphp</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 {{ $product->payment_type == 1 ? 'd-none' : '' }}">
                                    <div class="form-group">
                                        <label>@lang('Choose Billing Type')</label>
                                        <select name="billing_cycle" class="form-control form--control h-45 form-select">
                                            @php echo pricing($product->payment_type, $price); @endphp
                                        </select>
                                    </div>
                                </div>

                                @php $configs = $product->getConfigs; @endphp

                                @foreach ($configs as $config)
                                    @php
                                        $group = $config->activeGroup;
                                        $options = $group->activeOptions;
                                    @endphp

                                    @foreach ($options->sortBy('order') as $option)
                                        @php $subOptions = $option->activeSubOptions; @endphp

                                        @if (count($subOptions))
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ __($option->name) }}</label>
                                                    <select name="config_options[{{ $option->id }}]" class="form-control form--control h-45 options form-select" data-type='' data-name="{{ __($option->name) }}">
                                                        @foreach ($subOptions->sortBy('order') as $subOption)
                                                            <option value="{{ $subOption->id }}" data-price='{{ $subOption->getOnlyPrice }}' data-text='{{ __($subOption->name) }}'>
                                                                {{ __($subOption->name) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endforeach

                                @if ($product->product_type == 3)
                                    <div class="col-md-12 mt-5">
                                        <h5 class="text-center mb-3">@lang('Configure Server')</h5>
                                        <div class="row gy-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('Hostname')</label>
                                                    <input type="text" name="hostname" class="form-control form--control h-45 hostname" placeholder="servername.example.com" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('Root Password')</label>
                                                    <input type="password" name="password" class="form-control form--control h-45 root_password" placeholder="*******" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('NS1 Prefix')</label>
                                                    <input type="text" name="ns1" class="form-control form--control h-45 ns1_prefix" placeholder="ns1" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>@lang('NS2 Prefix')</label>
                                                    <input type="text" name="ns2" class="form-control form--control h-45 ns2_prefix" placeholder="ns2" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card p-3">
                                <span class="card-header bg-dark-two text-center fw-bold rounded-0">@lang('Order Summary')</span>
                                <div class="card-body pb-0 px-0">
                                    <div>
                                        <b>{{ __($product->name) }}</b>
                                        <span class="d-block fst-italic">{{ $product->serviceCategory->name }}</span>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <span>{{ __($product->name) }}</span>
                                            <span>
                                                {{ gs('cur_sym') }}<span class="basicPrice">{{ pricing($product->payment_type, $price, $type = 'price') }}</span>
                                                {{ __(gs('cur_text')) }}
                                            </span>
                                        </div>
                                        <div class="configurablePrice"></div>
                                    </div>
                                    <div class="calculatePrice border-top mt-3">
                                        <div class="d-flex justify-content-between">
                                            <span>@lang('Setup Fees'):</span>
                                            <span>
                                                {{ gs('cur_sym') }}<span class="setupFee">{{ pricing($product->payment_type, $price, $type = 'setupFee') }}</span>
                                                {{ __(gs('cur_text')) }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="billingType">{{ pricing($product->payment_type, $price, $type = 'price', $showText = true) }}:</span>
                                            <span>
                                                {{ gs('cur_sym') }}<span class="billingPrice">{{ pricing($product->payment_type, $price, $type = 'price') }}</span>
                                                {{ __(gs('cur_text')) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-between border-top mt-2 pt-4">
                                        <h5 class="text-center fw-bold">@lang('Total')</h5>
                                        <h5 class="justify-content-end d-flex">
                                            {{ gs('cur_sym') }} <span class="finalAmount">
                                                {{ pricing($product->payment_type, $price, $type = 'price') + pricing($product->payment_type, $price, $type = 'setupFee') }}
                                            </span>
                                            {{ __(gs('cur_text')) }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <button type="submit" class="btn bg--base btn-lg text-white w-100">
                                        @lang('Continue') <i class="la la-arrow-circle-right"></i>
                                    </button>
                                </div>
                            </div>

                        </div>

                    </div>
                    </form>
                </div>

            @endif
        </div>
    </div>
@endsection

@push('style')
<style>
    .showAvailability {
        display: flex;
        flex-direction: column;
    }
    .domain-row {
        order: 2;
    }
    .domain-row.domain-match {
        order: 1;
    }
</style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var general = @json(gs(['cur_sym', 'cur_text']));
            var userCurrency = {
                symbol: '{{ gs("cur_sym") }}',
                code: '{{ gs("cur_text") }}',
                rate: 1
            };
            var product = @json($product);

            var productPrice = @json($product->price);
            var allOptions = $('.options');

            var globalSetup = "{{ pricing($product->payment_type, @$price, $type = 'setupFee') }}";
            var addingSetupFee = 0;

            var globalPrice = "{{ pricing($product->payment_type, @$price, $type = 'price') }}";
            var addingPrice = 0;

            var basicPrice = $('.basicPrice');

            var billingType = $('.billingType');
            var setupFee = $('.setupFee');
            var billingPrice = $('.billingPrice');

            var finalAmount = $('.finalAmount');

            var info = '';

            $('.domain-option').on('click', function() {

                var form = $(this).data('form');

                if (form == 'register_domain_form') {
                    $('input[data-form=domain_form]').prop('checked', false);
                    $('.register_domain_form').parent().removeClass('d-none');
                    return $('.domain_form').parent().addClass('d-none');
                }

                $('input[data-form=register_domain_form]').prop('checked', false);
                $('.register_domain_form').parent().addClass('d-none');
                return $('.domain_form').parent().removeClass('d-none');

            });

            if (product.domain_register) {

                var domains = @json($domains);
                var hideElement = $('.hideElement');
                var domainArea = $('.domainArea');

                $('.register_domain_form').on('submit', function(e) {
                    e.preventDefault();

                    var sld = $(this).find('.domain_name').val();
                    var tld = $(this).find('.extension :selected').val();

                    if(!sld){
                        return false;
                    }

                    $('.showAvailability').empty();
                    $('.availability').empty();

                    var domain = sld + tld;
                    checkDomain(domain);
                });

                $('.domain_form').on('submit', function(e) {
                    e.preventDefault();

                    var domainName = $(this).find('.domain_name').val();
                    var extension = $(this).find('.extension').val();
                    var domain = domainName + '.' + extension;

                    if (domain) {
                        $('.domain').val(domain);
                        $('.domain_id').val(0);
                        hideElement.removeClass('d-none');
                        domainArea.addClass('d-none');
                    }
                });

                function checkDomain(domain) {

                    $.ajax({
                        url: "{{ route('search.domain') }}",
                        data: {
                            domain: domain
                        },

                        beforeSend: function(){
                            $('.availability').html(`<h5>@lang('Loading')...<h5>`); 
                        },
                        
                        success: function(getResponse) {
                 
                            if (!getResponse['success']) {
                                $('.availability').html(``);

                                var errors = getResponse['message'];
                                if(typeof(errors) != 'object'){
                                    errors = [errors];
                                }

                                $.each(errors, function(index, message) {
                                    return $('.availability').append(`<h5 class='text--danger'>${message}<h5>`);
                                });
                                return false;
                            }

                            var response = getResponse.result;
                            var available = false;

                            $.each(response.data.sort(function(a, b) {
                                return b.match - a.match;
                            }), function(key, data) {

                                var domain = data.domain;
                                var setup = data.setup;
                                var match = data.match;
                                var button = `<span class="text--info fw-bold">@lang('Unavailable')</span>`;

                                if(response.domain == domain && data.available){ 
                                    available = true;
                                }

                                if(data.available){  
                                    button = `
                                        <span class='fw-bold'>${general.cur_sym}${parseFloat(setup.pricing.firstPrice['price'] ?? 0).toFixed(2)}</span>
                                        <button 
                                            class="btn btn--sm btn--base${ !match ? '-outline' : '' } 
                                            registerDomainBtn ms-2" 
                                            data-domain="${domain}" 
                                            data-id="${setup.id}"
                                        >
                                            <i class="la la-cart-plus"></i> @lang('Add')
                                        </button>`
                                    ;
                                }

                                var html = `<div class="domain-row ${match}">
                                    <span>${domain}</span>
                                    <div class='text-end'>
                                        ${button}
                                    </div>
                                </div>`;

                                $('.showAvailability').append(html);
                            });

                            if(available){
                                $('.availability').html(`
                                    <h3 class='mb-4'>@lang('Congratulations')! <span class='text--base'>${response.domain}</span> is @lang('available')!<h3>
                                `);
                            }else{
                                $('.availability').html(`<h3 class='mb-4'><span class='text--danger'>${response.domain}</span> is @lang('unavailable')<h3>`);
                            }
                        },
                        error: function(error) {
                            $('.availability').html(`<h5 class='text--danger'>${error.responseJSON.messages}<h5>`);
                        }
                    });

                }

                $(document).on('click', '.registerDomainBtn', function() {
                    $('.domain').val($(this).data('domain'));
                    $('.domain_id').val($(this).data('id'));
                    hideElement.removeClass('d-none');
                    domainArea.addClass('d-none');
                });
            }

            $('select[name=billing_cycle]').on('change', function() {
                var value = $(this).val();

                var price = pricing(productPrice, 'price', value);
                var setup = pricing(productPrice, 'setupFee', value);
                var type = pricing(0, null, value);

                var totalPriceForSelectedItem = pricing(productPrice, null, value);

                billingType.text(type);
                basicPrice.text(getAmount(price));
                billingPrice.text(getAmount(price));
                setupFee.text(getAmount(setup));

                finalAmount.text(getAmount(totalPriceForSelectedItem));
                allOptions.attr('data-type', value);

                globalSetup = setup;
                globalPrice = price;

                showSelect(value);

            }).change();

            allOptions.on('change', function() {

                var column = $(this).attr('data-type');
                var getPrice = $(this).find(":selected").data('price');
                var setup = getPrice[column + '_setup_fee'];

                showSelect(column, false);
            });

            function pricing(price, type, column) {
                try {

                    if (!price) {
                        column = column.replaceAll('_', ' ');

                        if (product.payment_type == 1) {
                            column = 'One Time:';
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

            // Convert amount to user currency
            function convertToUserCurrency(amount) {
                return amount * userCurrency.rate;
            }

            // Format amount with user currency symbol
            function formatUserCurrency(amount) {
                return userCurrency.symbol + parseFloat(amount).toFixed(2);
            }

            function showSelect(value, showDropdown = true) {

                try {

                    addingSetupFee = 0;
                    addingPrice = 0;

                    var getColumn = value;
                    var getFeeColumn = value + '_setup_fee';

                    allOptions.each(function(index, data) {
                        var options = $(data).find('option');
                        var finalText = null;

                        options.each(function(iteration, dropdown) {
                            var dropdown = $(dropdown);
                            var dropdownOptions = null;
                            var optionSetupFee = '';

                            if (dropdown.data('price')) {
                                var priceForThisItem = dropdown.data('price');
                                var mainText = dropdown.data('text');
                                var display = product.payment_type == 1 ? 'One Time' : pricing(0, null, getColumn);

                                if (product.payment_type == 1) {
                                    getColumn = 'monthly'
                                }

                                if (priceForThisItem[getFeeColumn] > 0) {
                                    optionSetupFee = ` + ${userCurrency.symbol}${getAmount(priceForThisItem[getFeeColumn])} ${userCurrency.code} Setup Fee`
                                }

                                dropdownOptions = `${userCurrency.symbol}${getAmount(priceForThisItem[getColumn])} ${userCurrency.code} ${display} ${optionSetupFee}`;

                                finalText = mainText + ' ' + dropdownOptions;

                                if (showDropdown) {
                                    dropdown.text(finalText);
                                }

                            }

                            if (dropdown.filter(':selected').attr('data-price')) {

                                var configurableOption = $('.configurablePrice')
                                configurableOption.empty();

                                info += `<div class='d-flex justify-content-between fs-12 mt-2 flex-wrap'>
                                        <span><i class='fa fa-angle-double-right'></i> ${$(data).data('name')}:</span>
                                        <span>${finalText}</span>
                                    </div>`

                                configurableOption.append(info);

                                addingSetupFee = sum(addingSetupFee, priceForThisItem[getFeeColumn]);
                                addingPrice = sum(addingPrice, priceForThisItem[getColumn]);

                                setupFee.text(sum(addingSetupFee, globalSetup));
                                billingPrice.text(sum(addingPrice, globalPrice));

                                finalAmount.text(sum(sum(addingSetupFee, globalSetup), sum(addingPrice, globalPrice)));
                            }

                        });

                    });

                    info = '';

                } catch (message) {
                    console.log(message);
                }

            }

            function sum(param1, param2) {
                var amount = parseFloat(param1) + parseFloat(param2);
                return getAmount(amount);
            }

            //For update operation
            @if (@$cart)

                var cart = @json(@$cart);
                var billingCycle = '{{ $billingCycle }}';

                var column = billingCycle;
                $(`select[name=billing_cycle] option[value=${column}]`).prop('selected', true).change();
                $('select[name=billing_cycle]').parent().hide();

                $('.hideElement').removeClass('d-none');
                $('.domainArea').addClass('d-none');

                $('.domain').val(cart.domain);
                $('.hostname').val(cart.hostname);
                $('.root_password').val(cart.password);
                $('.ns1_prefix').val(cart.ns1);
                $('.ns2_prefix').val(cart.ns2);

                var length = Object.keys(cart.config_options).length;

                for (var i = 0; i <= length; i++) {
                    var selectName = Object.keys(cart.config_options)[i];
                    var selectOption = Object.values(cart.config_options)[i];

                    $(`select[name='config_options[${selectName}]'] option[value=${selectOption}]`).prop('selected', true);

                    var price = pricing(productPrice, 'price', column);
                    var setup = pricing(productPrice, 'setupFee', column);
                    var type = pricing(0, null, column);

                    var totalPriceForSelectedItem = pricing(productPrice, null, column);

                    billingType.text(type);
                    basicPrice.text(price);
                    billingPrice.text(price);
                    setupFee.text(setup);

                    finalAmount.text(totalPriceForSelectedItem);
                    allOptions.attr('data-type', column);

                    globalSetup = setup;
                    globalPrice = price;

                    showSelect(column, false);
                }
            @endif

        })(jQuery);
    </script>
@endpush
