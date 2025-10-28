@extends($activeTemplate.'layouts.master')
@section('content')
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card custom--card style-two">
                    <div class="card-header">
                        <h6 class="card-title text-center">{{ __($pageTitle) }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{$data->url}}" method="{{$data->method}}">
                            <ul class="list-group list-group-flush text-center">
                                <li class="list-group-item d-flex justify-content-between">
                                    @lang('You have to pay ')
                                    <strong>{{showAmount($deposit->final_amount,currencyFormat:false)}} {{__($deposit->method_currency)}}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    @lang('You will get ')
                                    <strong>{{showAmount($deposit->amount)}}</strong>
                                </li>
                            </ul>
                             <script src="{{$data->src}}"
                                class="stripe-button"
                                @foreach($data->val as $key=> $value)
                                data-{{$key}}="{{$value}}"
                                @endforeach
                            >
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-lib')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.stripe-button-el').addClass("btn btn--base w-100 mt-4").removeClass('stripe-button-el').text("Pay Now");
        })(jQuery);
    </script>
@endpush