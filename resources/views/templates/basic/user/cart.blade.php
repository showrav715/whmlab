@extends($activeTemplate . 'layouts.side_bar')

@section('data')
    <div class="col-lg-9">
        <div class="row g-3">
            <div class="col-lg-8">
                <h4>@lang('Cart')</h4>
                @forelse($carts as $cart)
                    <div class="card fs-12 cart_child m-1 mt-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-11 d-flex justify-content-between flex-wrap align-items-center">
                                    @if ($cart->product_id && !$cart->domain_setup_id && !$cart->domain_id)
                                        <div>
                                            <h6 class="d-inline">{{ __($cart->product->name) }}</h6>
                                            <a href="{{ route('shopping.cart.config.service', $cart->id) }}">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </a>
                                            <span class="d-block">{{ __($cart->product->serviceCategory->name) }}</span>
                                            <span class="d-block fw-bold">{{ @$cart->domain }}</span>
                                        </div>
                                        <div class="mt-1 mt-lg-0">
                                            <h6 class="d-inline">{{ showAmount(@$cart->price) }}</h6>
                                            <span class="d-block">{{ @billingCycle($cart->billing_cycle, true)['showText'] }}</span>
                                            <span class="d-block small">{{ gs('cur_sym') }}{{ showAmount(@$cart->setup_fee, 2, true, false) }} @lang('Setup Fee')</span>
                                            <span class="fst-italic fw-bold small">
                                                @lang('Total') {{ showAmount(@$cart->total) }}
                                            </span>
                                        </div>
                                    @else
                                        <div>
                                            @if ($cart->type == 4)
                                                <h6 class="d-inline">@lang('Domain Renew')</h6>
                                                <a href="{{ route('user.domain.details', $cart->domain_id) }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                            @else
                                                <h6 class="d-inline">@lang('Domain Registration')</h6>
                                                <a href="{{ route('shopping.cart.config.domain', $cart->id) }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                            @endif
                                            <span class="d-block fw-bold">
                                                {{ @$cart->domain }} - {{ @$cart->reg_period }} @lang('Year')
                                                {{ @$cart->id_protection ? __('with ID Protection') : null }}
                                            </span>
                                        </div>
                                        <div class="mt-1 mt-lg-0">
                                            <h6 class="d-block">{{ showAmount(@$cart->price) }}</h6>
                                            @if (@$cart->id_protection)
                                                <span class="d-block small">{{ showAmount(@$cart->setup_fee) }} @lang('ID Protection')</span>
                                            @endif
                                            <span class="fst-italic fw-bold small">
                                                @lang('Total') {{ showAmount(@$cart->total) }} 
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-1 form-group">
                                    <a class="remove_cart d-none" href="{{ route('shopping.cart.remove', $cart->id) }}">
                                        <i class="la la-trash">&nbsp;@lang('Remove')</i>
                                    </a>
                                    <a href="{{ route('shopping.cart.remove', $cart->id) }}" class="remove_icon">
                                        <i class="la la-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="row">
                        <div class="col-md-12 text-center mt-3">
                            <div class="card p-4">
                                <x-empty-message div={{ true }} message="Empty carts" />
                            </div>
                        </div>
                    </div>
                @endforelse

                @if (@$cart)
                    <div class="row">
                        <div class="col-lg-12 text-end mb-4">
                            <span class="bg--dark text-white p-2 fs-12 me-1 btn--xs btn">
                                <a href="{{ route('shopping.cart.empty') }}" class="text-white"><i class="la la-trash"></i> @lang('Empty Cart')</a>
                            </span>
                        </div>
                        <div class="col-lg-12">
                            <div class="card p-3">
                                @if (@$appliedCoupon)
                                    <form action="{{ route('shopping.cart.coupon.remove') }}" method="post">
                                        @csrf
                                        <p class="border p-2 text-center">
                                            {{ $appliedCoupon->coupon->code }} - {{ $appliedCoupon->coupon_type == 0 ? showAmount($appliedCoupon->coupon_discount, currencyFormat:false) . '%' : showAmount($carts->sum('discount')) }} @lang('Discount')
                                        </p>
                                        <div class="form-group mt-2">
                                            <button type="submit" class="btn btn-warning w-100 text-white">@lang('Remove Coupon Code')</button>
                                        </div>
                                    </form>
                                @else
                                    <form action="{{ route('shopping.cart.coupon') }}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text" class="form-control form--control h-45" name="coupon_code" placeholder="@lang('Enter coupon code if you have one')" required>
                                        </div>
                                        <div class="form-group mt-2">
                                            <button type="submit" class="btn btn--base btn--sm w-100">@lang('Validate Code')</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card p-3">
                    <span class="border-bottom p-2 bg-dark-two text-center fw-bold w-100">@lang('Order Summary')</span>
                    <div class="card-body pb-0 px-0">
                        <div>
                            <div class="d-flex justify-content-between mt-3">
                                <span>@lang('Subtotal')</span>
                                <span class="basicPrices">{{ showAmount($carts->sum('total')) }}</span>
                            </div>
                        </div>
                        @if ($appliedCoupon)
                            <div class="border-top mt-3">
                                <div class="d-flex justify-content-between small mt-1">
                                    <span class="discounts">
                                        @lang('Get') - {{ $appliedCoupon->coupon_type == 0 ? showAmount($appliedCoupon->coupon_discount, currencyFormat:false) . '%' : showAmount($cart->sum('discount')) }} @lang('Discount')
                                    </span>
                                    <span>
                                        <span class="discountAmounts">{{ showAmount($cart->sum('discount')) }}</span>
                                    </span>
                                </div>
                            </div>
                        @endif
                        <div class="d-flex flex-wrap justify-content-between border-top mt-2 pt-4">
                            <h5 class="text-center fw-bold">@lang('Total')</h5>
                            <h5 class="justify-content-end d-flex">
                                <span class="finalAmounts">{{ showAmount($carts->sum('after_discount')) }}</span>
                            </h5>
                        </div>
                    </div>

                    @if (count($carts))
                        <div class="text-center mt-3">
                            <form action="{{ route('user.invoice.create') }}" method="post">
                                @csrf
                                <button type="submit" class="btn bg--base btn-lg text-white w-100">
                                    @lang('Checkout') <i class="la la-arrow-circle-right"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .capitalize {
            text-transform: capitalize;
        }

        .cart_child:nth-child(odd) .card-body {
            background-color: #00000008;
        }

        @media only screen and (max-width: 767px) {
            .remove_cart {
                display: inline-block !important;
            }

            .remove_icon {
                display: none;
            }
        }
    </style>
@endpush
